<?php

namespace DrupalCodeGenerator\Command;

use DrupalCodeGenerator\Asset;

/**
 * Implements plugin-manager command.
 */
final class PluginManager extends ModuleGenerator {

  protected $name = 'plugin-manager';
  protected $description = 'Generates plugin manager';

  /**
   * {@inheritdoc}
   */
  protected function generate(): void {
    $vars = &$this->collectDefault();

    // self::validateMachineName() does not allow dots, but they can appear
    // in some plugin types (field.widget, views.argument, etc).
    $plugin_type_validator = function (string $value): string {
      $value = self::validateRequired($value);
      if (!preg_match('/^[a-z][a-z0-9_\.]*[a-z0-9]$/', $value)) {
        throw new \UnexpectedValueException('The value is not correct machine name.');
      }
      return $value;
    };
    $vars['plugin_type'] = $this->ask('Plugin type', '{machine_name}', $plugin_type_validator);

    $discovery_types = [
      'annotation' => 'Annotation',
      'yaml' => 'YAML',
      'hook' => 'Hook',
    ];
    $vars['discovery'] = $this->choice('Discovery type', $discovery_types, 'Annotation');
    $vars['class_prefix'] = '{plugin_type|camelize}';

    // Common files.
    $this->addServicesFile()->template('{discovery}/model.services.yml');
    $this->addFile('src/{class_prefix}Interface.php', '{discovery}/src/ExampleInterface.php');
    $this->addFile('src/{class_prefix}PluginManager.php', '{discovery}/src/ExamplePluginManager.php');

    switch ($vars['discovery']) {
      case 'annotation':
        $this->addFile('src/Annotation/{class_prefix}.php', 'annotation/src/Annotation/Example.php');
        $this->addFile('src/{class_prefix}PluginBase.php', 'annotation/src/ExamplePluginBase.php');
        $this->addFile('src/Plugin/{class_prefix}/Foo.php', 'annotation/src/Plugin/Example/Foo.php');
        break;

      case 'yaml':
        $this->addFile('{machine_name}.{plugin_type|pluralize}.yml', 'yaml/model.examples.yml');
        $this->addFile('src/{class_prefix}Default.php', 'yaml/src/ExampleDefault.php');
        break;

      case 'hook':
        $this->addFile('{machine_name}.module', 'hook/model.module')
          ->action(Asset::APPEND)
          ->headerTemplate('_lib/file-docs/module')
          ->headerSize(7);
        $this->addFile('src/{class_prefix}Default.php', 'hook/src/ExampleDefault.php');
        break;
    }

  }

}

<?php

namespace DrupalCodeGenerator\Tests\Generator;

/**
 * Test for plugin-manager command (YAML discovery).
 */
class PluginManagerYamlTest extends BaseGeneratorTest {

  protected $class = 'PluginManager';

  protected $interaction = [
    'Module name [%default_name%]:' => 'Foo',
    'Module machine name [foo]:' => 'foo',
    'Plugin type [foo]:' => 'bar',
    "Discovery type [Annotation]:\n  [1] Annotation\n  [2] YAML\n  [3] Hook" => 'YAML',
  ];

  protected $fixtures = [
    'foo.bars.yml' => '/_plugin_manager_yaml/foo.bars.yml',
    'foo.services.yml' => '/_plugin_manager_yaml/foo.services.yml',
    'src/BarDefault.php' => '/_plugin_manager_yaml/src/BarDefault.php',
    'src/BarInterface.php' => '/_plugin_manager_yaml/src/BarInterface.php',
    'src/BarPluginManager.php' => '/_plugin_manager_yaml/src/BarPluginManager.php',
  ];

}

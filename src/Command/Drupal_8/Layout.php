<?php

namespace DrupalCodeGenerator\Command\Drupal_8;

use DrupalCodeGenerator\Command\BaseGenerator;
use DrupalCodeGenerator\Utils;

/**
 * Implements d8:layout command.
 */
class Layout extends BaseGenerator {

  protected $name = 'd8:layout';
  protected $description = 'Generates a layout';
  protected $alias = 'layout';
  protected $nameQuestion = NULL;

  /**
   * {@inheritdoc}
   */
  protected function generate() :void {

    $vars = &$this->collectDefault();

    $vars['layout_name'] = $this->ask('Layout name', 'Example');
    $vars['layout_machine_name'] = $this->ask('Layout machine name', Utils::human2machine($vars['layout_name']));
    $vars['category'] = $this->ask('Category', 'My layouts');

    $vars['js'] = $this->confirm('Would you like to create JavaScript file for this layout?', FALSE);
    $vars['css'] = $this->confirm('Would you like to create CSS file for this layout?', FALSE);

    $this->addFile()
      ->path('{machine_name}.layouts.yml')
      ->template('d8/_layout/layouts.twig')
      ->action('append');

    if ($vars['js'] || $vars['css']) {
      $this->addFile()
        ->path('{machine_name}.libraries.yml')
        ->template('d8/_layout/libraries.twig')
        ->action('append');
    }

    $vars['layout_asset_name'] = '{layout_machine_name|u2h}';

    $this->addFile('layouts/{layout_machine_name}/{layout_asset_name}.html.twig', 'd8/_layout/template');
    if ($vars['js']) {
      $this->addFile('layouts/{layout_machine_name}/{layout_asset_name}.js', 'd8/_layout/javascript');
    }
    if ($vars['css']) {
      $this->addFile('layouts/{layout_machine_name}/{layout_asset_name}.css', 'd8/_layout/styles');
    }

  }

}

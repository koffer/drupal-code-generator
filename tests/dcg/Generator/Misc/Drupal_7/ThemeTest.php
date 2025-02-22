<?php

namespace DrupalCodeGenerator\Tests\Generator\Misc\Drupal_7;

use DrupalCodeGenerator\Tests\Generator\BaseGeneratorTest;

/**
 * Test for misc:d7:theme command.
 */
class ThemeTest extends BaseGeneratorTest {

  protected $class = 'Misc\Drupal_7\Theme';

  protected $interaction = [
    'Theme name [%default_name%]:' => 'Example',
    'Theme machine name [example]:' => 'example',
    'Theme description [A simple Drupal 7 theme.]:' => 'A theme for test.',
    'Base theme:' => 'garland',
  ];

  protected $fixtures = [
    'example/example.info' => '/_theme/example.info',
    'example/images' => [],
    'example/template.php' => '/_theme/template.php',
    'example/templates' => [],
    'example/css/example.css' => '/_theme/css/example.css',
    'example/js/example.js' => '/_theme/js/example.js',
  ];

}

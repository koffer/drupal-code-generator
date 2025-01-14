<?php

namespace DrupalCodeGenerator\Tests\Generator\Yml;

use DrupalCodeGenerator\Tests\Generator\BaseGeneratorTest;

/**
 * Test for yml:routing command.
 */
class RoutingTest extends BaseGeneratorTest {

  protected $class = 'Yml\Routing';

  protected $interaction = [
    'Module name [%default_name%]:' => 'Example',
    'Module machine name [example]:' => 'example',
  ];

  protected $fixtures = [
    'example.routing.yml' => '/_routing.yml',
  ];

}

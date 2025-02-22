<?php

namespace DrupalCodeGenerator\Tests\Generator\Test;

use DrupalCodeGenerator\Tests\Generator\BaseGeneratorTest;

/**
 * Test for test:nightwatch command.
 */
class NightwatchTest extends BaseGeneratorTest {

  protected $class = 'Test\Nightwatch';

  protected $interaction = [
    'Module name [%default_name%]:' => 'Foo',
    'Module machine name [foo]:' => 'foo',
    'Test name [example]:' => 'example',
  ];

  protected $fixtures = [
    'tests/src/Nightwatch/exampleTest.js' => '/_nightwatch.js',
  ];

}

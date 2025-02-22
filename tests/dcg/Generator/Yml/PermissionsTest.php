<?php

namespace DrupalCodeGenerator\Tests\Generator\Yml;

use DrupalCodeGenerator\Tests\Generator\BaseGeneratorTest;

/**
 * Test for yml:permissions command.
 */
class PermissionsTest extends BaseGeneratorTest {

  protected $class = 'Yml\Permissions';

  protected $interaction = [
    'Module machine name [%default_machine_name%]:' => 'example',
  ];

  protected $fixtures = [
    'example.permissions.yml' => '/_permissions.yml',
  ];

}

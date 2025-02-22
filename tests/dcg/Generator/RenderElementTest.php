<?php

namespace DrupalCodeGenerator\Tests\Generator;

/**
 * Test for render-element command.
 */
class RenderElementTest extends BaseGeneratorTest {

  protected $class = 'RenderElement';

  protected $interaction = [
    'Module machine name [%default_machine_name%]:' => 'foo',
  ];

  protected $fixtures = [
    'src/Element/Entity.php' => '/_render_element.php',
  ];

}

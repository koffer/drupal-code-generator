<?php

namespace DrupalCodeGenerator\Tests\Generator\Other;

use DrupalCodeGenerator\Tests\Generator\BaseGeneratorTest;

/**
 * Test for Misc:html-page command.
 */
class HtmlPageTest extends BaseGeneratorTest {

  protected $class = 'Misc\HtmlPage';

  protected $answers = [
    'example.html',
  ];

  protected $interaction = [
    'File name [index.html]:' => 'example.html',
  ];

  protected $fixtures = [
    'example.html' => '/_html_page.html',
    'css/main.css' => '/_html_page.css',
    'js/main.js' => '/_html_page.js',
  ];

}

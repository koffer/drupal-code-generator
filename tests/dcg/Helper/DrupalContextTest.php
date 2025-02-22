<?php

namespace DrupalCodeGenerator\Tests\Helper;

use DrupalCodeGenerator\Helper\DrupalContext;
use PHPUnit\Framework\TestCase;

/**
 * A test for Drupal context helper.
 */
class DrupalContextTest extends TestCase {

  /**
   * Mocked container.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface
   */
  private $container;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->container = $this->getMockBuilder('\Symfony\Component\DependencyInjection\ContainerInterface')
      ->setMethods(['get'])
      ->getMock();

    $handler_map = [
      ['module_handler', $this->getModuleHandler()],
      ['theme_handler', $this->getThemeHandler()],
    ];
    $this->container->expects($this->any())
      ->method('get')
      ->will($this->returnValueMap($handler_map));

    if (!defined('DRUPAL_ROOT')) {
      define('DRUPAL_ROOT', '/tmp/drupal');
    }
  }

  /**
   * Test callback.
   */
  public function testGetExtensionList(): void {
    $drupal_context = new DrupalContext($this->container);

    $modules = [
      'foo' => 'Foo',
      'bar' => 'Bar',
      'qux' => 'Qux',
    ];
    self::assertEquals($modules, $drupal_context->getExtensionList('module'));

    $themes = [
      'alpha' => 'Alpha',
      'beta' => 'Beta',
      'gamma' => 'Gamma',
    ];
    self::assertEquals($themes, $drupal_context->getExtensionList('theme'));

    self::expectException(\UnexpectedValueException::class);
    self::expectExceptionMessage('Unsupported extension type "wrong"');
    $drupal_context->getExtensionList('wrong');
  }

  /**
   * Test callback.
   */
  public function testGetDestination(): void {
    $drupal_context = new DrupalContext($this->container, '/path');

    // New module.
    self::assertEquals('/path/modules', $drupal_context->getDestination('module', TRUE, 'does not matter'));
    // Existing module.
    self::assertEquals('/path/modules/custom/bar', $drupal_context->getDestination('module', FALSE, 'bar'));
    // Existing module which is not really exists.
    self::assertEquals('/path/modules/fake', $drupal_context->getDestination('module', FALSE, 'fake'));
    // New theme.
    self::assertEquals('/path/themes', $drupal_context->getDestination('theme', TRUE, 'does not matter'));
    // Existing theme.
    self::assertEquals('/path/themes/gamma', $drupal_context->getDestination('theme', FALSE, 'gamma'));

    self::expectException(\UnexpectedValueException::class);
    self::expectExceptionMessage('Unsupported extension type "wrong"');
    $drupal_context->getDestination('wrong', TRUE, NULL);
  }

  /**
   * Mocks module handler.
   */
  private function getModuleHandler() {
    $module_handler = $this->getMockBuilder('\Drupal\Core\Extension\ModuleHandlerInterface')
      ->setMethods(['getModuleList', 'getModule', 'getName'])
      ->getMock();

    $module_names = [
      'foo' => 'Foo',
      'bar' => 'Bar',
      'qux' => 'Qux',
    ];

    foreach ($module_names as $machine_name => $name) {
      $module = $this->getMockBuilder('\Drupal\Core\Extension\Extension')
        ->setMethods(['getPath'])
        ->getMock();

      $module->expects($this->any())
        ->method('getPath')
        ->willReturn('modules/custom/' . $machine_name);

      $modules[$machine_name] = $module;
      $module_map[] = [$machine_name, $module];
      $module_name_map[] = [$machine_name, $name];
    }

    $module_handler->expects($this->any())
      ->method('getModule')
      ->will($this->returnValueMap($module_map));

    $module_handler->expects($this->any())
      ->method('getName')
      ->will($this->returnValueMap($module_name_map));

    $module_handler->expects($this->any())
      ->method('getModuleList')
      ->willReturn($modules);

    return $module_handler;
  }

  /**
   * Mocks theme handler.
   */
  private function getThemeHandler() {

    // -- Theme handler.
    $theme_handler = $this->getMockBuilder('\Drupal\Core\Extension\ThemeHandlerInterface')
      ->setMethods(['listInfo', 'getTheme', 'getName'])
      ->getMock();

    $theme_names = [
      'alpha' => 'Alpha',
      'beta' => 'Beta',
      'gamma' => 'Gamma',
    ];

    foreach ($theme_names as $machine_name => $name) {
      $theme = $this->getMockBuilder('\Drupal\Core\Extension\Extension')
        ->setMethods(['getPath'])
        ->getMock();

      $theme->expects($this->any())
        ->method('getPath')
        ->willReturn('themes/' . $machine_name);

      $theme->info['name'] = $name;

      $themes[$machine_name] = $theme;
      $theme_map[] = [$machine_name, $theme];
    }

    $theme_handler->expects($this->any())
      ->method('getTheme')
      ->will($this->returnValueMap($theme_map));

    $theme_handler->expects($this->any())
      ->method('listInfo')
      ->willReturn($themes);

    return $theme_handler;
  }

}

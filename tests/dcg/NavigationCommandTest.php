<?php

namespace DrupalCodeGenerator\Tests;

use DrupalCodeGenerator\Application;
use DrupalCodeGenerator\Command\Navigation;
use DrupalCodeGenerator\GeneratorFactory;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * A test for navigation command.
 */
class NavigationCommandTest extends BaseTestCase {

  /**
   * Test callback.
   */
  public function testNavigation(): void {

    $factory = new GeneratorFactory(new Filesystem());
    $generators = $factory->getGenerators([Application::ROOT . '/src/Command']);

    $application = Application::create();
    $helper_set = $application->getHelperSet();
    $helper_set->set(new QuestionHelper());

    $application->addCommands($generators);

    $navigation = new Navigation();
    $application->add($navigation);

    $command_tester = new CommandTester($navigation);

    $fixture = file_get_contents(__DIR__ . '/_navigation_fixture.txt');

    // The return symbol is used to identify user input in fixture.
    preg_match_all('/\s([^\s]+)⏎/', $fixture, $matches);
    $command_tester->setInputs($matches[1]);

    $input = ['--directory' => $this->directory];
    $command_tester->execute($input);

    $expected_output = rtrim(preg_replace('/[^\s]+⏎/', '', $fixture));
    $output = rtrim($command_tester->getDisplay());
    self::assertEquals($expected_output, $output);

    /** @var \Symfony\Component\Console\Command\HelpCommand $help */
    $help = $application->find('help');
    $help->setCommand($navigation);
    $command_tester = new CommandTester($help);
    $command_tester->execute([]);
    $display = $command_tester->getDisplay();
    self::assertContains('Command line code generator', $display);
    self::assertContains('dcg [options] <generator>', $display);
    self::assertContains('Display navigation', $display);
    self::assertContains('List all available generators', $display);
  }

}

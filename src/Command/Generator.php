<?php

namespace DrupalCodeGenerator\Command;

use DrupalCodeGenerator\Application;
use DrupalCodeGenerator\Asset;
use DrupalCodeGenerator\IOAwareInterface;
use DrupalCodeGenerator\IOAwareTrait;
use DrupalCodeGenerator\Style\GeneratorStyle;
use DrupalCodeGenerator\Utils;
use DrupalCodeGenerator\ValidatorTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * Base class for code generators.
 */
abstract class Generator extends Command implements GeneratorInterface, IOAwareInterface, LoggerAwareInterface {

  use IOAwareTrait;
  use LoggerAwareTrait;
  use ValidatorTrait;

  /**
   * The command name.
   *
   * @var string
   */
  protected $name;

  /**
   * The command description.
   *
   * @var string
   */
  protected $description;

  /**
   * The command alias.
   *
   * @var string
   */
  protected $alias;

  /**
   * Command label.
   *
   * @var string
   */
  protected $label;

  /**
   * A path where templates are stored.
   *
   * @var string
   */
  protected $templatePath;

  /**
   * The working directory.
   *
   * This is used to supply generators with some context. For instance, the
   * directory name can be used to set default extension name.
   *
   * @var string
   */
  protected $directory;

  /**
   * Assets to create.
   *
   * @var \DrupalCodeGenerator\Asset[]
   */
  protected $assets = [];

  /**
   * Twig template variables.
   *
   * @var array
   */
  protected $vars = [];

  /**
   * {@inheritdoc}
   */
  protected function configure(): void {
    $this
      ->setName($this->name)
      ->setDescription($this->description)
      ->addOption('directory', '-d', InputOption::VALUE_OPTIONAL, 'Working directory')
      ->addOption('answer', '-a', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'Answer to generator question')
      ->addOption('dry-run', NULL, InputOption::VALUE_NONE, 'Output the generated code but not save it to file system')
      ->setAliases($this->alias ? [$this->alias] : []);
  }

  /**
   * {@inheritdoc}
   */
  protected function initialize(InputInterface $input, OutputInterface $output): void {

    $this->io = new GeneratorStyle($input, $output, $this->getHelper('question'));
    foreach ($this->getHelperSet() as $helper) {
      if ($helper instanceof IOAwareInterface) {
        $helper->io($this->io);
      }
    }

    $this->getHelperSet()->setCommand($this);

    if ($this->templatePath) {
      $this->getHelper('renderer')->addPath($this->templatePath);
    }
    else {
      // This is specific to DCG core generators. Third-party generators should
      // always define template path.
      $template_path = Application::TEMPLATE_PATH . str_replace(':', '/', $this->getName());
      if (file_exists($template_path) && is_dir($template_path)) {
        $this->getHelper('renderer')->addPath($template_path);
        // Also add default template path as some generators may share their
        // templates.
        $this->getHelper('renderer')->addPath(Application::TEMPLATE_PATH);
      }
      else {
        throw new \LogicException('Template path is not specified.');
      }
    }

    $this->setLogger($this->getHelper('logger_factory')->getLogger());

    $this->directory = $input->getOption('directory') ?: getcwd();

    $this->logger->debug('Working directory: {directory}', ['directory' => $this->directory]);
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output): int {

    $this->logger->debug('Command: {command}', ['command' => get_class($this)]);

    $this->io->title(sprintf('Welcome to %s generator!', $this->getAliases()[0] ?? $this->getName()));

    $this->generate();

    $this->render();

    $dumped_assets = $this->dump($input->getOption('dry-run'));

    $this->printSummary($dumped_assets);

    $this->logger->debug('Memory usage: {memory}', ['memory' => Helper::formatMemory(memory_get_peak_usage())]);

    return 0;
  }

  /**
   * Generates assets.
   */
  abstract protected function generate(): void;

  /**
   * Render assets.
   */
  protected function render(): void {
    $renderer = $this->getHelper('renderer');

    $this->processVars();

    $collected_vars = preg_replace('/^Array/', '', print_r($this->vars, TRUE));
    $this->logger->debug('Collected variables: {vars}', ['vars' => $collected_vars]);

    foreach ($this->assets as $asset) {
      // Supply the asset with all collected variables if it has no local ones.
      if (!$asset->getVars()) {
        $asset->vars($this->vars);
      }
      $renderer->renderAsset($asset);
      $this->logger->debug('Rendered template: {template}', ['template' => $asset->getTemplate()]);
    }
  }

  /**
   * Dumps assets.
   */
  protected function dump(bool $dry_run): array {
    $destination = $this->getDestination();
    $this->logger->debug('Destination directory: {directory}', ['directory' => $destination]);
    return $this->getHelper('dumper')->dump($this->assets, $destination, $dry_run);
  }

  /**
   * Prints summary.
   */
  protected function printSummary(array $dumped_assets): void {
    $this->getHelper('result_printer')->printResult($dumped_assets);
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel(): ?string {
    return $this->label;
  }

  /**
   * Asks a question.
   */
  protected function ask(string $question, $default = NULL, $validator = NULL) {
    $question = Utils::stripSlashes(Utils::replaceTokens($question, $this->vars));
    $default = Utils::stripSlashes(Utils::replaceTokens($default, $this->vars));

    // Allow the validators to be referenced in a short form like
    // '::validateMachineName'.
    if (is_string($validator) && substr($validator, 0, 2) == '::') {
      $validator = [get_class($this), substr($validator, 2)];
    }
    return $this->io->ask($question, $default, $validator);
  }

  /**
   * Asks for confirmation.
   */
  protected function confirm(string $question, bool $default = TRUE): bool {
    $question = Utils::stripSlashes(Utils::replaceTokens($question, $this->vars));
    return $this->io->confirm($question, $default);
  }

  /**
   * Asks a choice question.
   */
  protected function choice(string $question, array $choices, $default = NULL) {
    $question = Utils::stripSlashes(Utils::replaceTokens($question, $this->vars));

    // The choices can be an associative array.
    $choice_labels = array_values($choices);
    // Start choices list form '1'.
    array_unshift($choice_labels, NULL);
    unset($choice_labels[0]);

    // Do not use IO choice here as it prints choice key as default value.
    // @see \Symfony\Component\Console\Style\SymfonyStyle::choice().
    $answer = $this->io->askQuestion(new ChoiceQuestion($question, $choice_labels, $default));
    return array_search($answer, $choices);
  }

  /**
   * Creates an asset.
   *
   * @param string $type
   *   Asset type.
   *
   * @return \DrupalCodeGenerator\Asset
   *   The asset.
   */
  protected function addAsset(string $type): Asset {
    $asset = (new Asset())->type($type);
    $this->assets[] = $asset;
    return $asset;
  }

  /**
   * Creates file asset.
   *
   * @param string $path
   *   (Optional) File path.
   * @param string $template
   *   (Optional) Template.
   *
   * @return \DrupalCodeGenerator\Asset
   *   The asset.
   */
  protected function addFile(string $path = NULL, string $template = NULL): Asset {
    return $this->addAsset('file')
      ->path($path)
      ->template($template);
  }

  /**
   * Creates directory asset.
   *
   * @param string $path
   *   (Optional) Directory path.
   *
   * @return \DrupalCodeGenerator\Asset
   *   The asset.
   */
  protected function addDirectory(string $path = NULL): Asset {
    return $this->addAsset('directory')->path($path);
  }

  /**
   * Processes collected variables.
   */
  protected function processVars(): void {
    $process_vars = function (&$var, string $key, array $vars): void {
      if (is_string($var)) {
        $var = Utils::stripSlashes(Utils::replaceTokens($var, $vars));
      }
    };
    array_walk_recursive($this->vars, $process_vars, $this->vars);
  }

  /**
   * Returns destination for generated files.
   */
  protected function getDestination(): ?string {
    return $this->directory;
  }

}

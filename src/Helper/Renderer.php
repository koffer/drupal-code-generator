<?php

namespace DrupalCodeGenerator\Helper;

use DrupalCodeGenerator\Asset;
use Symfony\Component\Console\Helper\Helper;
use Twig\Environment;

/**
 * Output dumper form generators.
 */
class Renderer extends Helper {

  /**
   * The twig environment.
   *
   * @var \Twig\Environment
   */
  protected $twig;

  /**
   * Constructs the Renderer object.
   *
   * @param \Twig\Environment $twig
   *   The twig environment.
   */
  public function __construct(Environment $twig) {
    $this->twig = $twig;
  }

  /**
   * {@inheritdoc}
   */
  public function getName(): string {
    return 'renderer';
  }

  /**
   * Renders a template.
   *
   * @param string $template
   *   Twig template.
   * @param array $vars
   *   Template variables.
   *
   * @return string
   *   A string representing the rendered output.
   */
  public function render(string $template, array $vars): string {
    return $this->twig->render($template, $vars);
  }

  /**
   * Renders a Twig string directly.
   *
   * @param string $inline_template
   *   The template string to render.
   * @param array $vars
   *   (Optional) Template variables.
   *
   * @return string
   *   A string representing the rendered output.
   */
  public function renderInline(string $inline_template, array $vars): string {
    return $this->twig->createTemplate($inline_template)->render($vars);
  }

  /**
   * Renders an asset.
   *
   * @param \DrupalCodeGenerator\Asset $asset
   *   Asset to render.
   */
  public function renderAsset(Asset $asset): void {
    if ($asset->isDirectory()) {
      return;
    }

    $template = $asset->getTemplate();
    $inline_template = $asset->getInlineTemplate();
    // A generator may set content directly.
    if (!$template && !$inline_template) {
      return;
    }

    $content = '';
    if ($header_template = $asset->getHeaderTemplate()) {
      $content .= $this->render($header_template, $asset->getVars()) . "\n";
    }

    if ($template) {
      $content .= $this->render($template, $asset->getVars());
    }
    elseif ($inline_template) {
      $content .= $this->renderInline($inline_template, $asset->getVars());
    }

    $asset->content($content);
  }

  /**
   * Adds a path where templates are stored.
   *
   * @param string $path
   *   A path where to look for templates.
   */
  public function addPath(string $path): void {
    /** @var \Twig\Loader\FilesystemLoader $loader */
    $loader = $this->twig->getLoader();
    $loader->addPath($path);
  }

}

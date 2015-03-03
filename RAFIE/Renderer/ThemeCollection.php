<?php

namespace RAFIE\Renderer;

use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Parser;

/**
 * Class ThemeCollection
 * @package RAFIE\Renderer
 */
class ThemeCollection extends Collection
{

  /**
   * @var array List of lookup directories
   */
  protected $themesPaths;

  /**
   * @param array $themesPaths
   * @throws Exception
   * @throws \Exception
   */
  public function __construct($themesPaths)
  {
    parent::__construct();
    $this->themesPaths = $themesPaths;

    $this->loadThemes();
  }

  /**
   * Load the list of available themes
   *
   * @throws Exception
   * @throws \Exception
   */
  protected function loadThemes()
  {
    $finder = Finder::create()->directories()->in($this->themesPaths);

    foreach ($finder->getIterator() as $theme) {
      if ($theme->getRelativePath()) { // only get top level directories
        continue;
      }

      $themeFile = $theme->getRealPath() . '/theme.yml';

      if (!file_exists($themeFile)) {
        throw new \Exception(sprintf('%s doesn\'t exist.', $themeFile));
      }

      $config = (new Parser())->parse(file_get_contents($themeFile));

      if (!isset($config['name'])) {
        throw new \Exception(sprintf('%s must contain a name value.', $themeFile));
      }

      $this->put($config['name'], new Theme($config, $theme->getRealPath()));
    }

  }

}
 
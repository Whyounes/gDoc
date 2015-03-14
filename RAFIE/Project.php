<?php

namespace RAFIE;

use Illuminate\Container\Container;
use RAFIE\Renderer\ThemeCollection;
use RAFIE\Version\GitVersionCollection;
use Symfony\Component\Yaml\Parser;


/**
 * Class Project
 * @package RAFIE
 */
class Project extends Container
{
  /**
   * @var \RAFIE\Configuration
   */
  protected $configuration;

  /**
   * @var \Illuminate\Filesystem\Filesystem
   */
  protected $fileSystem;

  /**
   * @var \RAFIE\Contracts\ParserContracts
   */
  protected $parser;

  /**
   * @var \RAFIE\Renderer\ThemeCollection
   */
  protected $themeCollection;

  /**
   * @var \RAFIE\Renderer\Theme
   */
  protected $theme;

  /**
   * @param Configuration $configuration
   */
  public function __construct(Configuration $configuration)
  {

    $this->configuration = $configuration;// build_path should exist
    $this->fileSystem = $this->make('Illuminate\Filesystem\Filesystem');
    $this->themeCollection = new ThemeCollection($this->configuration->getOptions()['themesPaths']);//test isset

    $this->_registerBindings();// conflict with parent class method
    $this->loadTheme();

    // must after registerBindings
    $this->parser = $this->make('parser');
  }

  /**
   * See if the theme exists and set it's value
   * @throws \Exception
   */
  public function loadTheme()
  {
    $themeName = $this->configuration->getOptions()['theme'] ?: 'default';

    if (!$this->themeCollection->has($themeName)) {
      throw new \Exception(sprintf('%s Cannot be found in the themes directories.', $themeName));
    }

    $this->theme = $this->themeCollection->get($themeName);

    // change the twig filesystem lookup dir
    $twig = $this['twig'];
    $twig->setLoader(new \Twig_Loader_Filesystem($this->theme->getPath()));
    $this['twig'] = $twig;
  }

  /**
   * Create the working stage (built directory)
   */
  protected function initDirectory($path)
  {
    $this->fileSystem->deleteDirectory($path);
    $this->fileSystem->makeDirectory($path);
  }

  /**
   * Register Ioc bindings
   */
  public function _registerBindings()
  {
    $pro = $this;
    $options = $this->configuration->getOptions();

    if (isset($options['parser'])) {
      if (!class_exists($options['parser'])) {
        throw new \InvalidArgumentException(sprintf("The `%s class doesn't exist.", $options['parser']));

      }
      if (!method_exists($options['parser'], 'parse')) {
        // we may change this test, and only require it to be an instance of Contracts\ParserContract
        throw new \InvalidArgumentException(sprintf("The `%s class must have a `parse` method.", $options['parser']));
      }

      $this->singleton('parser', function ($app) use ($options) {
        return $app->make($options['parser']);
      });
    } else {
      $this->singleton('parser', function ($app) {
        return $app->make('\\RAFIE\\Parser\\MarkdownParser');
      });
    }


    $this->singleton('twig', function () use ($pro) {
      $twig = new \Twig_Environment(new \Twig_Loader_Filesystem('/'), [
          'strict_variables' => true,
          'debug'            => true,
          'auto_reload'      => true,
          'cache'            => false,
          'autoescape'       => true
      ]);
      $twig->addExtension(new \Twig_Extension_Debug());

      return $twig;
    });
  }

  /**
   * Process files
   */
  public function process()
  {
    $buildPath = $this->configuration->getOptions()['build_path'];

    if (isset($this->configuration->getOptions()['versions'])) {
      if (strpos($buildPath, '%version%') === false) {
        throw new \InvalidArgumentException('buildPath must contain %version% when using a Git repository.');
      }

      $this->processVersioned($buildPath);
    } else {
      $this->processSingleVersion($buildPath);
    }
  }

  /**
   * Process a normal folder doc
   */
  protected function processSingleVersion($buildPath, $version = null)
  {
    $this->initDirectory($buildPath);
    $this->copyAssets($buildPath);

    $iterator = $this->configuration->getFinder()->getIterator();
    $navigation = $this->loadNavigation($this->configuration->getFinder());

    foreach ($iterator as $file) {
      $content = $this->parser->parse($file->getContents());
      $this->writeFile($file, $buildPath, $content, $navigation, $version);
    }
  }

  /**
   * Process a Git documentation
   */
  protected function processVersioned($buildPath)
  {
    $versions = $this->configuration->getOptions()['versions'];

    foreach ($versions as $version) {
      $versionPath = str_replace('%version%', $version->getName(), $buildPath);

      $this->processSingleVersion($versionPath, $version->getName());
    }
  }

  /**
   * Write file to the disk
   */
  public function writeFile($file, $path, $content, $navigation, $version)
  {
    // create the theme -> load theme configuration -> pass the content to the output file creator
    // create structure from theme.yml file, or from the directory structure
    $fileName = basename($file->getBasename(), $file->getExtension()) . 'html';
    //$navigation = parse yaml file 'doc.yml' and get navigation

    $viewContent = $this['twig']->render('page.twig', [
        'rootPath'   => $version,
        'filename'   => $fileName,
        'assets'     => $this->theme->getValue()['assets'] ?: [],
        'content'    => $content,
        'navigation' => $navigation,
    ]);

    $this->fileSystem->put($path . '/' . $fileName, $viewContent);
  }

  /**
   * @param string $buildPath
   */
  protected function copyAssets($buildPath)
  {
    $assets = $this->theme->getValue()['assets'] ?: [];

    foreach ($assets as $k => $asset) {
      $assetSrcPath = $this->theme->getPath() . '/' . $asset;
      $assetDestPath = $buildPath . '/' . $asset;

      if (!$this->fileSystem->isDirectory(dirname($assetDestPath))) {
        $this->fileSystem->makeDirectory(dirname($assetDestPath), 0777, true, true);
      }

      $this->fileSystem->copy($assetSrcPath, $assetDestPath);
    }

  }

  /**
   * @param \Symfony\Component\Finder\Finder $finder
   * @return array
   */
  protected function loadNavigation($finder)
  {
    // if doc.yml doesn't exist
    $doc = (new Parser())->parse(file_get_contents($this->configuration->getDocConfFile()));

    if (!isset($doc['navigation'])) {
      $navigation = [];
      $iterator = $finder->getIterator();
      foreach ($iterator as $item) {
        $navigation[$item->getRelativePathname()] = $item->getFilename();
      }

      return $navigation;
    }

    return $doc['navigation'];
  }

}
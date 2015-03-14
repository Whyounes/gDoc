<?php

namespace RAFIE;

use Symfony\Component\Finder\Finder;

/**
 * Class Configuration
 * @package RAFIE
 */
class Configuration
{

  /**
   * @var Symfony\Component\Finder\Finder
   */
  protected $finder;

  /**
   * @var array
   */
  protected $options;

  /**
   * @var string Path to the documentation configuration file.
   */
  protected $docConfFile;

  /**
   * @param Finder $finder
   * @param Finder|string $rootDir
   * @param array $options
   */
  public function __construct(Finder $finder, $docConfFile, $options)
  {
    $this->finder = $finder;
    $this->options = $options;
    $this->docConfFile = $docConfFile;
  }

  /**
   * @return Finder
   */
  public function getFinder()
  {
    return $this->finder;
  }

  /**
   * @return array
   */
  public function getOptions()
  {
    return $this->options;
  }

  /**
   * @return string
   */
  public function getDocConfFile()
  {
    return $this->docConfFile;
  }
}

 
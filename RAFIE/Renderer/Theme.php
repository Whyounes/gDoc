<?php

namespace RAFIE\Renderer;

/**
 * Class Theme
 * @package RAFIE\Renderer
 */
class Theme
{

  protected $name;

  /**
   * @var array Config values
   */
  protected $value;

  protected $path;

  /**
   * @param string $name
   * * @param string $path
   */
  public function __construct($value, $path)
  {
    $this->value = $value;
    $this->name = $value['name'];
    $this->path = $path;
  }

  /**
   * @return array
   */
  public function getValue()
  {
    return $this->value;
  }

  public function getName(){
    return $this->name;
  }

  public function getPath(){
    return $this->path;
  }
}
 
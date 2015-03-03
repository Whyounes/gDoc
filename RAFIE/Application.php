<?php

namespace RAFIE;

use RAFIE\Console\Commands\Generate;

class Application extends \Symfony\Component\Console\Application
{
  public function __construct()
  {
    parent::__construct('rafie');
    $this->add(new Generate());
  }
}
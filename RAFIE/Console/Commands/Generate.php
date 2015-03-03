<?php

namespace RAFIE\Console\Commands;

use RAFIE\Configuration;
use RAFIE\Project;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Generate extends Command
{

  protected function configure()
  {
    $description = "Generate HTML documentation from Markdown, also supports GFM 'Github Flavored Markdown'";
    $this->setName('generate')
        ->setDescription($description)
        ->setHelp($description)
        ->addArgument('config', InputArgument::REQUIRED, 'Configuration file');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {

    $configFile = $input->getArgument('config');

    if (!file_exists($configFile)) {
      throw new \InvalidArgumentException(sprintf('File "%s" does not exist.', $configFile));
    }

    $configuration = require_once $configFile;// config file must return Finder

    if(!($configuration instanceof Configuration)){
      return new \InvalidArgumentException("%s must return an instance of RAFIE\\Configuration", $configFile);
    }

    $project = new Project($configuration);
    $project->process();
  }


}

 
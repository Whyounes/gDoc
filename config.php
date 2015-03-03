<?php

use RAFIE\Configuration;
use Symfony\Component\Finder\Finder;

$dir = __DIR__ . '/../doc';

$finder = Finder::create()->files()->in($dir);

$versions = Sami\Version\GitVersionCollection::create($dir)
    ->add('master', 'Master')
    ->add('4.2', '4.2');

$options = [
    'theme'       => 'laravel',
    'build_path'  => __DIR__ . '/build',
    'versions'    => $versions,
    'themesPaths' => [__DIR__ . '/../gDocThemes/themes/']
];

$docConf = $dir . '/doc.yml';

return new Configuration($finder, $docConf, $options);
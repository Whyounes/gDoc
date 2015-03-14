##Documentation Generator
API documentation is important, but an official documentation for your application project is also important. I tried to adapt the [Sami documentation generator](https://github.com/FriendsOfPHP/Sami) to help generate official documentation.

##Installation
You can require [rafie/gdoc](https://packagist.org/packages/rafie/gdoc) in your `composer.json`and do a `composer update`.

##Usage
You can run the documentation generator from the command line.

```bash
$ php path/to/rafie.php generate your_config.php
```

Where the config file must return an instance of `RAFIE\Configuration`, you may use the config file inside the root of the package, this is an example.

```php
use RAFIE\Configuration;
use Symfony\Component\Finder\Finder;

$dir = __DIR__ . '/doc';

$finder = Finder::create()->files()->in($dir);

$docConf = $dir.'/doc.yml';

$options = [
    'theme'       => 'laravel',
    'build_path'  => __DIR__ . '/build',
    'themesPaths' => [__DIR__ . '/src/themes/']
];

return new Configuration($finder, $docConf, $options);
```

You can use the [Finder](http://symfony.com/doc/current/components/finder.html) to lookup your markdown documentation. The `$docConf` can be used to describe your documentation navigation structure and it will parsed and passed to your theme file, for example, if you wanted to create the [Laravel documentation](http://laravel.com/docs/5.0) structure.

```yaml
navigation:
  Prologue:
    Releases Notes: releases.html
    Upgrade Guide: upgrade.html
    Contribution Guide: contributions.html
  Setup:
    Installation: installation.html
    Configuration: configuration.html
    ...
```

The options parameter specify three attributes, your theme name, themes paths (where you stored your themes), and the build path where you output the result.

##Using Git
You can use versioned documentation by specifying a fourth option.
 
```php
$dir = __DIR__ . '/doc';

$finder = Finder::create()->files()->in($dir);

$versions = RAFIE\Version\GitVersionCollection::create($dir)
    ->add('master', 'Master')
    ->add('4.2', '4.2');

$options = [
    'theme'       => 'laravel',
    'build_path'  => __DIR__ . '/build/%version%',
    'versions'    => $versions,
    'themesPaths' => [__DIR__ . '/src/themes/']
];

$docConf = $dir . '/doc.yml';

return new Configuration($finder, $docConf, $options);
```

The `GitVersionCollection` lets you specify which versions you want to use for the generation, and the result is passed as an option. Note that the build path contains a `%version%` which indicates the sub directory structure used for the output.

##Creating Themes
You can this Github [repository](https://github.com/Whyounes/gDocThemes) to learn more about theme.

##Demo
The [gDocDemo](https://github.com/Whyounes/gDocDemo) repository contain a demo for generating a documentation for Laravel framework, I'm using their CSS file and some of their HTML.
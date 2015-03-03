##Documentation Generator
API documentation is important, but an official documentation for your application functionnality is also important. I tried to adapt the [Sami documentation generator](https://github.com/FriendsOfPHP/Sami) to help generate official documentation.

##Installation
You can require [rafie/gdoc]() in your `composer.json`and do an update.

##Usage
You can run the documentation generator from the command line.

```bash
$ php path/to/rafie.php generate your_config.php
```

Where the config file must return an instance of `RAFIE\Configuration`, this is an example.

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
To create a theme for your documentation, you need to create a folder that contains a `theme.yml' file.
 
```yml
name: laravel

assets:
  - 'assets/css/laravel.css'
  - 'assets/js/laravel.js'
  - 'assets/img/laravel-logo.png'
```

The only required attribute is the `name` which will be passed as an option like we described previously. The `assets` define a list of assets needed by the documentation static files, those files are copied to the documentation build path using the same structure.
Your theme must also contain a `page.twig` file which will be called with the following parameters.

- 'rootPath': If you're using versioned docs, this attributes will hold the current one.
- 'content': HTML parsed content.
- 'assets': List of assets as specified in your theme file.
- 'filename': The current parsed file name.
- 'navigation': The list of navigation specified on your `doc.yml`. 

The laravel theme inside the repository is an example theme that can be used as a reference.

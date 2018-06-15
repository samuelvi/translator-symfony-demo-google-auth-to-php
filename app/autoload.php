<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

spl_autoload_register( function () {

    $directory = new RecursiveDirectoryIterator(realpath(__DIR__ . '/../vendor/atico/spreadsheet-translator/src') . '/');
    $recIterator = new RecursiveIteratorIterator($directory);
    $regex = new RegexIterator($recIterator, '/.*.php$/i');

    foreach($regex as $item) {
        require_once $item->getPathname();

    }
});

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

return $loader;
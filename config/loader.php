<?php

use Phalcon\Loader;

$loader = new Loader();

$loader->registerDirs(
    [
        $config->application->modelsDir,
        $config->application->controllersDir,
        $config->application->validatorsDir,
        $config->application->libraryDir
    ]
);

//register loader
$loader->register();
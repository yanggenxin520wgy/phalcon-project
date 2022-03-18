<?php

use Phalcon\Loader;

$loader = new Loader();
$config = $di->getConfig();

/**
 * Register Namespaces
 */
$loader->registerNamespaces([
    '@@namespace@@\Library' => $config->application->libraryDir,
    '@@namespace@@\Traits' => $config->application->traitsDir,
    '@@namespace@@\Plugins' => $config->application->pluginsDir,
    '@@namespace@@\Utils' => $config->application->utilsDir,
    '@@namespace@@\Validators' => $config->application->validatorsDir,
]);

$loader->registerDirs(
    [
        $config->application->utilsDir,
        $config->application->pluginsDir,
        $config->application->validatorsDir,
        $config->application->libraryDir,
        $config->application->enumsDir,
        $config->application->traitsDir,
    ]
);

$loader->registerFiles(
    [
        $config->application->libraryDir . '/functions.php',
        $config->application->coreDir . '/PhalBaseLogger.php',
    ]
);

$loader->register();

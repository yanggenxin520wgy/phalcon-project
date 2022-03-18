<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerNamespaces(
    [
        '@@namespace@@\Library' => $config->application->libraryDir,
        '@@namespace@@\Controllers' => $config->application->controllersDir,
        '@@namespace@@\Models' => $config->application->modelsDir,
        '@@namespace@@\Services' => $config->application->servicesDir,
        '@@namespace@@\Core' => $config->application->coreDir,
        '@@namespace@@\FlashInterfaces' => $config->application->interfacesDir,
        '@@namespace@@\Validators' => $config->application->validatorsDir,
        '@@namespace@@\Enums' => $config->application->enumsDir,
        '@@namespace@@\Traits' => $config->application->traitsDir,
    ]
)
    ->registerDirs(
    [
        $config->application->tasksDir,
        $config->application->utilsDir,
        $config->application->pluginsDir,
        $config->application->validatorsDir,
        $config->application->libraryDir,
        $config->application->enumsDir,
        $config->application->traitsDir,
    ]
)
    ->registerFiles(
        [
            $config->application->libraryDir . '/functions.php',
            $config->application->coreDir . '/PhalBaseLogger.php',
        ]
    )
    ->register();

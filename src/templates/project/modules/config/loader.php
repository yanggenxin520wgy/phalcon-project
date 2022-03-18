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
    '@@namespace@@\Modules\Cli\Tasks' => $config->application->tasksDir,
    '@@namespace@@\Modules\Cli\Migrations' => $config->application->migrationsDir,
    '@@namespace@@\Modules\Rpc\Controllers' => $config->application->modulesDir . 'Rpc/controllers/',
    '@@namespace@@\Modules\Rpc\Models' => $config->application->modulesDir . 'Rpc/models/',
    '@@namespace@@\Modules\Rpc\Services' => $config->application->modulesDir . 'Rpc/services/',
]);

$loader->registerDirs(
    [
        $config->application->tasksDir,
        $config->application->utilsDir,
        $config->application->pluginsDir,
        $config->application->validatorsDir,
        $config->application->libraryDir,
        $config->application->enumsDir,
        $config->application->traitsDir,
    ]
);

$loader->registerClasses(
    [
        '@@namespace@@\Modules\Rpc\ControllerBase' => APP_PATH . '/modules/Rpc/ControllerBase.php',
    ]
);

$loader->registerFiles(
    [
        $config->application->libraryDir . '/functions.php',
        $config->application->coreDir . '/PhalBaseLogger.php',
    ]
);

$loader->register();

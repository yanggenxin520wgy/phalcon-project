#!/usr/bin/env php
<?php

use Phalcon\Di\FactoryDefault\Cli as FactoryDefault;
use Phalcon\Cli\Console as ConsoleApp;

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

require BASE_PATH . '/vendor/autoload.php';

/**
 * The FactoryDefault Dependency Injector automatically registers the services that
 * provide a full stack framework. These default services can be overidden with custom ones.
 */
$di = new FactoryDefault();

$dotenv = new Dotenv\Dotenv(BASE_PATH);
$dotenv->load();

defined('RUNTIME') or define('RUNTIME', strtolower(env('runtime', 'pro')));
defined('COUNTRY_CODE') or define('COUNTRY_CODE', strtolower(env('country_code', 'th')));
defined('TIMEZONE') or define('TIMEZONE', strtolower(env('timezone', '+07:00')));

/**
 * Include cli environment specific services
 */
include APP_PATH . '/config/services.php';
include APP_PATH . '/config/services_cli.php';

/**
 * Include Autoloader
 */
include APP_PATH . '/config/loader.php';

/**
 * Get config service for use in inline setup below
 */
$config = $di->getConfig();

/**
 * Create a console application
 */
$console = new ConsoleApp($di);

$modules = include APP_PATH . '/config/modules.php';
/**
 * Register console modules
 */
$console->registerModules($modules);

/**
 * Setup the arguments to use the 'cli' module
 */
$arguments = [
    'module' => 'cli',
    'config' => $config,
];

if (count($argv) > 1) {
    /**
     * Process the console arguments
     */
    foreach ($argv as $k => $arg) {
        if ($k == 1) {
            $arguments['task'] = $arg;
        } elseif ($k == 2) {
            $arguments['action'] = $arg;
        } elseif ($k >= 3) {
            $arguments['params'][] = $arg;
        }
    }
} else {
    $arguments["task"] = 'main';
    $arguments["action"] = 'main';
}

try {

    /**
     * Handle
     */
    $console->handle($arguments);

    /**
     * If configs is set to true, then we print a new line at the end of each execution
     *
     * If we dont print a new line,
     * then the next command prompt will be placed directly on the left of the output
     * and it is less readable.
     *
     * You can disable this behaviour if the output of your application needs to don't have a new line at end
     */
    if (isset($config["printNewLine"]) && $config["printNewLine"]) {
        echo PHP_EOL;
    }

} catch (Exception $e) {
    if (in_array(RUNTIME, ['tra', 'pro'])) {
        $errStr = $e->getMessage() . '<pre>' . $e->getTraceAsString() . '</pre>';
        error_log($errStr);
    }

    if (in_array(RUNTIME, ['dev', 'tra', 'host'])) {
        echo $e->getMessage() . '<br>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    } else {
        echo "<pre> Something went wrong, if the error continue please contact us </pre>";
    }

    $log = array(
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'code' => $e->getCode(),
        'msg' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    );
    $date = date('Ymd');
    $path = $config->application->runtimeDir . 'exception_log/';
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
    $logger = new \@@namespace@@\Core\PhalBaseLogger($path . 'log_' . $date . '.log');
    $logger->write_log($log, 'error');
    exit(255);
}

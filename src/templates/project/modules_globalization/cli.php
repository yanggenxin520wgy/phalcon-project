#!/usr/bin/env php
<?php


use Phalcon\Cli\Console;

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

//为了引入env函数
require BASE_PATH . '/vendor/autoload.php';

/**
 * 加载环境变量
 */
$dotenv = new Dotenv\Dotenv(BASE_PATH);
$dotenv->load();

defined('RUNTIME') || define('RUNTIME', strtolower(env('runtime', 'pro')));
defined('COUNTRY_CODE') || define('COUNTRY_CODE', strtolower(env('country_code', 'th')));
defined('TIMEZONE') || define('TIMEZONE', env('timezone', '+07:00'));

$di = new \Phalcon\Di\FactoryDefault\Cli();

/**
 * Include Services
 */
include APP_PATH . '/config/services.php';
include APP_PATH . '/config/services_cli.php';

/**
 * Get config service for use in inline setup below
 */
$config = $di->get("config");

/**
 * Include Autoloader
 */
include APP_PATH . '/config/loader.php';

try {
    /**
     * Create a console application
     */

    $console = new Console();
    $console->setDI($di);
    /**
     * 处理console应用参数
     */
    $arguments = [];

    if (file_exists($config->application->modulesDir . COUNTRY_CODE . '/cli/Module.php')) {
        $console->registerModules([
            COUNTRY_CODE => [
                'className' => '@@namespace@@\Modules\\' . ucfirst(COUNTRY_CODE) . '\Cli\Module',
                'path' => $config->application->modulesDir . COUNTRY_CODE . '/cli/Module.php',
            ]
        ]);
    } else {
        $console->registerModules([
            COUNTRY_CODE => [
                'className' => '@@namespace@@\Modules\Base\Cli\Module',
                'path' => $config->application->modulesDir . 'base/cli/Module.php',
            ]
        ]);
    }

    $arguments['module'] = 'cli';
    $arguments['config'] = $config;
    foreach ($argv as $k => $arg) {
        if ($k === 1) {
            $arguments["task"] = $arg;
        } elseif ($k === 2) {
            $arguments["action"] = $arg;
        } elseif ($k >= 3) {
            $arguments["params"][] = $arg;
        }
    }
    /**
     * 默认现实command列表
     */
    if (count($argv) == 1) {
        $arguments["task"] = 'main';
        $arguments["action"] = 'main';
    }

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
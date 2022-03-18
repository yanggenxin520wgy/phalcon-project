<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Application;

error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

try {

    /**
     * Use composer autoloader to load vendor classes
     */
    if (file_exists(BASE_PATH . "/vendor/autoload.php")) {
        include BASE_PATH . "/vendor/autoload.php";
    }

    //加载环境变量
    $dotenv = new Dotenv\Dotenv(BASE_PATH);
    $dotenv->load();

    define('RUNTIME', strtolower(env('runtime', 'dev')));
    define('TIMEZONE', env('timezone', '+07:00'));
    define('COUNTRY_CODE', strtolower(env('country_code', 'th')));

    /**
     * The FactoryDefault Dependency Injector automatically registers the services that
     * provide a full stack framework. These default services can be overidden with custom ones.
     */
    $di = new FactoryDefault();

    /**
     * Include general services
     */
    require APP_PATH . '/config/services.php';
    require APP_PATH . '/config/services_web.php';

    /**
     * Get config service for use in inline setup below
     */
    $config = $di->getConfig();

    /**
     * Include Autoloader
     */
    include APP_PATH . '/config/loader.php';


    /**
     * Handle the request
     */
    $application = new Application($di);

    $modules = require APP_PATH . '/config/modules.php';

    /**
     * Register application modules
     */
    $application->registerModules($modules);

    /**
     * Include router
     */
    require APP_PATH . '/config/router.php';

    echo str_replace(["\n", "\r", "\t"], '', $application->handle()->getContent());

} catch (\Exception $e) {
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
}

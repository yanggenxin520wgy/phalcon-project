<?php

use Phalcon\Di\FactoryDefault;

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

    define('TIMEZONE', env('timezone', '+07:00'));
    define('COUNTRY_CODE', strtolower(env('country_code', 'th')));
    define('RUNTIME', strtolower(env('runtime', 'dev')));


    /**
     * The FactoryDefault Dependency Injector automatically registers
     * the services that provide a full stack framework.
     */
    $di = new FactoryDefault();
    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);

    /**
     * Read services
     */
    include APP_PATH . '/config/services.php';
    include APP_PATH . '/config/services_web.php';

    /**
     * Get config service for use in inline setup below
     */
    $config = $di->getConfig();

    /**
     * Include Autoloader
     */
    include APP_PATH . '/config/loader.php';

    /**
     * 注册本国家模块
     */
    if (file_exists($config->application->modulesDir . COUNTRY_CODE . '/Module.php')) {
        $application->registerModules([
            COUNTRY_CODE => [
                'className' => '@@namespace@@\Modules\\' . ucfirst(COUNTRY_CODE) . '\Module',
                'path' => $config->application->modulesDir . COUNTRY_CODE . '/Module.php',
            ]
        ]);
    }

    /**
     * Handle router
     */
    include APP_PATH . '/config/router.php';

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

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

    defined('RUNTIME') or define('RUNTIME', strtolower(env('runtime', 'pro')));
    defined('COUNTRY_CODE') or define('COUNTRY_CODE', strtolower(env('country_code', 'th')));

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

    /**
     * 多模块
     */
    $url = explode('/',trim($_SERVER['REQUEST_URI'], '/'));
    if (!empty($url)) {
        $moduleName = $url[0];
        $module = ucfirst(strtolower($moduleName));
        $className = 'App\Modules\Base\\' . $module . '\Module';
        $path = APP_PATH . '/modules/Base/' . $module . '/Module.php';

        if (file_exists(APP_PATH . '/modules/' . COUNTRY_CODE .'/' . $module .'/Module.php')) {
            $className = 'App\Modules\\' . COUNTRY_CODE . '\\' . $module . '\Module';
            $path = APP_PATH . '/modules/' . COUNTRY_CODE .'/' . $module .'/Module.php';
        }

        if (file_exists($path)) {
            $application->registerModules([
                $moduleName => [
                    'className' => $className,
                    'path' => $path,
                ]
            ]);
        } else {
            header('Location:' . '/Common/error/notFound');
            exit;
        }
    }

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

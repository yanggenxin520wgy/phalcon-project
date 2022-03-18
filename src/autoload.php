<?php

use Phalcon\Loader;
use Dotenv\Dotenv;

if (!extension_loaded('phalcon')) {
    throw new Exception('未安装phalcon框架扩展');
}

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

defined('PROJECT_PATH') || define('PROJECT_PATH', dirname(__FILE__));

defined('CORE_PATH') || define('CORE_PATH', PROJECT_PATH . DS . 'core' . DS);

defined('TEMPLATE_PATH') || define('TEMPLATE_PATH', PROJECT_PATH . DS . 'templates');

$load = new Loader;
$load->registerDirs([
    PROJECT_PATH . DS . 'core' . DS,
])
    ->registerNamespaces([
        'Project\Core\Builder' => CORE_PATH . 'Builder' . DS,
        'Project\Core\Version' => CORE_PATH . 'Version' . DS,
    ])
    ->register();

$vendorPath = realpath(dirname(PROJECT_PATH ) . '/../../');
if (false !== stripos($vendorPath, '/vendor')) {
    $dotenv = new Dotenv(realpath('.'));
    $dotenv->load();
    if (file_exists($vendorPath . DS . 'autoload.php')) {
        require_once $vendorPath . DS . 'autoload.php';
    }

    if (file_exists($vendorPath . DS . 'phalcon' . DS . 'devtools' . DS . 'bootstrap' . DS . 'autoload.php')) {
        require_once $vendorPath . DS . 'phalcon' . DS . 'devtools' . DS . 'bootstrap' . DS . 'autoload.php';
    }
} else {
    if (file_exists(dirname(PROJECT_PATH) . DS . 'vendor' . DS . 'autoload.php')) {
        require_once dirname(PROJECT_PATH) . DS . 'vendor' . DS . 'autoload.php';
    }

    if (file_exists(dirname(PROJECT_PATH) . DS . 'vendor' . DS . 'phalcon' . DS . 'devtools' . DS . 'bootstrap' . DS . 'autoload.php')) {
        require_once dirname(PROJECT_PATH) . DS . 'vendor' . DS . 'phalcon' . DS . 'devtools' . DS . 'bootstrap' . DS . 'autoload.php';
    }
}


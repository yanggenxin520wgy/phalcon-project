<?php

use Phalcon\Loader;

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

if (file_exists(dirname(PROJECT_PATH) . DS .'vendor' . DS . 'autoload.php')) {
    require_once dirname(PROJECT_PATH) . DS .'vendor' . DS . 'autoload.php';
}

if (file_exists(dirname(PROJECT_PATH) . DS .'vendor' . DS . 'phalcon' .DS . 'devtools' . DS . 'bootstrap' . DS . 'autoload.php')) {
    require_once dirname(PROJECT_PATH) . DS .'vendor' . DS . 'phalcon' .DS . 'devtools' . DS . 'bootstrap' . DS . 'autoload.php';
}


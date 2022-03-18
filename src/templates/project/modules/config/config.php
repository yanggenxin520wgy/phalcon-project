<?php
/*
 * Modified: prepend directory path of current file, because of this file own different ENV under between Apache and command line.
 * NOTE: please remove this comment.
 */
defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

return new \Phalcon\Config([
    'database' => [
        'database_adapter'   => env('database_adapter', 'Mysql'),
        'database_host'      => env('database_host', 'localhost'),
        'database_username'  => env('database_username', 'root'),
        'database_password'  => env('database_password', '123456'),
        'database_dbname'    => env('database_dbname', 'dbname'),
        'database_charset'   => env('database_charset', 'utf8'),
    ],
    'database_r' => [
        'database_adapter'   => env('database_adapter_r', 'Mysql'),
        'database_host'      => env('database_host_r', 'localhost'),
        'database_username'  => env('database_username_r', 'root'),
        'database_password'  => env('database_password_r', '123456'),
        'database_dbname'    => env('database_dbname_r', 'dbname'),
        'database_charset'   => env('database_charset_r', 'utf8'),
    ],
    'redis' => [
        'host'      => env('redis_host', '127.0.0.1'),
        'port'      => env('redis_port', '6379'),
        'auth'      => env('redis_auth', ''),
        'lifetime'  => env('redis_lifetime', 3600 * 24),
        'prefix'    => env('redis_prefix', ''),
        'db'        => env('redis_db', 0)
    ],
    'application' => [
        'appDir'            => APP_PATH . '/',
        'configDir'         => APP_PATH . '/config/',
        'viewsDir'          => APP_PATH . '/views/',
        'modulesDir'        => APP_PATH . '/modules/',
        'migrationsDir'     => APP_PATH . '/modules/Cli/migrations/',
        'pluginsDir'        => APP_PATH . '/plugins/',
        'utilsDir'          => APP_PATH . '/utils/',
        'libraryDir'        => APP_PATH . '/library/',
        'cacheDir'          => BASE_PATH . '/cache/',
        'coreDir'           => APP_PATH . '/core/',
        'interfacesDir'     => APP_PATH . '/interfaces/',
        'traitsDir'         => APP_PATH . '/traits/',
        'enumsDir'          => APP_PATH . '/enums/',
        'validatorsDir'     => APP_PATH . '/validators/',
        'runtimeDir'        => APP_PATH . '/runtime/',
        'tasksDir'          => APP_PATH . '/modules/Cli/tasks/',
        'logLevel'          => env('log_level', \Phalcon\Logger::INFO),
        'baseUri'           => '/',
        'aliMailAddress'    => env('aliMailAddress', ''),//邮箱
        'aliMailPass'       => env('aliMailPass', ''),//邮箱密码
    ]
]);

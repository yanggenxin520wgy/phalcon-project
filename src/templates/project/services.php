<?php

use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Cache\Frontend\Data;

/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return @@configLoader@@;
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () {
    $config = $this->getConfig();

    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

/**
 * 注册dotenv
 */
$di->setShared('dotenv', function () {
    $dotenv = new Dotenv\Dotenv(BASE_PATH);
    $dotenv->load();
    return $dotenv;
});

/**
 * 注册sql分析服务
 */
$di->set('profiler', function(){
    return new \Phalcon\Db\Profiler();
}, true);


/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () use($di) {
    $config = $this->getConfig();

    $eventsManager = new \Phalcon\Events\Manager();
    if (RUNTIME != 'pro') {
        //从di中获取共享的profiler实例 分析底层sql性能，并记录日志
        $profiler = $this->getProfiler();
        $eventsManager->attach('db', function ($event, $connection) use ($profiler) {
            if ($event->getType() == 'beforeQuery') {
                //在sql发送到数据库前启动分析
                $profiler->startProfile($connection->getSQLStatement());
            }
            if ($event->getType() == 'afterQuery') {
                if ($event->getType() == 'afterQuery') {
                    //在sql执行完毕后停止分析
                    $profiler->stopProfile();
                    //获取分析结果
                    $profile    = $profiler->getLastProfile();
                    $sql        = $profile->getSQLStatement();
                    $params     = $connection->getSqlVariables();
                    $connectionId = $connection->getConnectionId();
                    (is_array($params) && count($params)) && $params = json_encode($params);
                    $executeTime = $profile->getTotalElapsedSeconds();
                    //记录sql日志
                    $logStr = [
                        'connection_id' => $connectionId,
                        'sql'           => $sql,
                        'params'        => $params,
                        'execute_time'  => $executeTime
                    ];
                    $logPath = APP_PATH . '/runtime/sql_log/';
                    if (!is_dir($logPath)) {
                        mkdir($logPath, 0755, true);
                    }
                    $loggerFile = $logPath . '/' . date('Ymd') . '.log';
                    $logger = new \@@namespace@@\Core\PhalBaseLogger($loggerFile);
                    $logger->write_log($logStr, 'info');
                }
            }
        });
    }
    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->database_adapter;
    $params = [
        'host'     => $config->database->database_host,
        'username' => $config->database->database_username,
        'password' => $config->database->database_password,
        'dbname'   => $config->database->database_dbname,
        'charset'  => $config->database->database_charset
    ];

    if ($config->database->database_adapter == 'Postgresql') {
        unset($params['charset']);
    }

    $connection = new $class($params);
    if (RUNTIME != 'pro') {
        $connection->setEventsManager($eventsManager);
    }

    return $connection;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db_r', function () {
    $config = $this->getConfig();

    $eventsManager = new \Phalcon\Events\Manager();
    if (RUNTIME != 'pro') {
        //从di中获取共享的profiler实例 分析底层sql性能，并记录日志
        $profiler = $this->getProfiler();
        $eventsManager->attach('db', function ($event, $connection) use ($profiler) {
            if ($event->getType() == 'beforeQuery') {
                //在sql发送到数据库前启动分析
                $profiler->startProfile($connection->getSQLStatement());
            }
            if ($event->getType() == 'afterQuery') {
                if ($event->getType() == 'afterQuery') {
                    //在sql执行完毕后停止分析
                    $profiler->stopProfile();
                    //获取分析结果
                    $profile    = $profiler->getLastProfile();
                    $sql        = $profile->getSQLStatement();
                    $params     = $connection->getSqlVariables();
                    $connectionId = $connection->getConnectionId();
                    (is_array($params) && count($params)) && $params = json_encode($params);
                    $executeTime = $profile->getTotalElapsedSeconds();
                    //记录sql日志
                    $logStr = [
                        'connection_id' => $connectionId,
                        'sql'           => $sql,
                        'params'        => $params,
                        'execute_time'  => $executeTime
                    ];
                    $logPath = APP_PATH . '/runtime/sql_log/';
                    if (!is_dir($logPath)) {
                        mkdir($logPath, 0755, true);
                    }
                    $loggerFile = $logPath . '/' . date('Ymd') . '.log';
                    $logger = new \App\Core\PhalBaseLogger($loggerFile);
                    $logger->write_log($logStr, 'info');
                }
            }
        });
    }
    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database_r->database_adapter;
    $params = [
        'host'     => $config->database_r->database_host,
        'username' => $config->database_r->database_username,
        'password' => $config->database_r->database_password,
        'dbname'   => $config->database_r->database_dbname,
        'charset'  => $config->database_r->database_charset
    ];

    if ($config->database_r->database_adapter == 'Postgresql') {
        unset($params['charset']);
    }

    $connection = new $class($params);
    if (RUNTIME != 'pro') {
        $connection->setEventsManager($eventsManager);
    }

    return $connection;
});


/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * DI注册Redis服务
 */
$di->setShared('redis', function () {
    $config = $this->getConfig();

    $frontCache = new Data(["lifetime" => $config->redis->lifetime,]);
    $cache = new \Phalcon\Cache\Backend\Redis(
        $frontCache,
        [
            'prefix' => $config->redis->prefix,
            'host' => $config->redis->host,
            'port' => $config->redis->port,
            'auth' => $config->redis->auth,
            'persistent' => false,
            "index"      => $config->redis->db,
        ]
    );
    return $cache;
});

/**
 *
 */
$di->setShared('redisLib', function(){
    $config = $this->getConfig();
    $redis = new \Redis();
    $redis->pconnect($config->redis->host, $config->redis->port);
    $redis->auth($config->redis->auth);
    $redis->setOption(\Redis::OPT_PREFIX, $config->redis->prefix);
    $redis->select($config->redis->db);
    return $redis;
});

/**
 * DI注册svc日志服务
 */
$di->setShared('svcLogger', function () use ($di) {
    $config = $this->getConfig(); //使用DI里注册的config服务
    $date = date('Ymd');
    $logPath = $config->application->runtimeDir . 'svclog/';
    if (!is_dir($logPath)) {
        mkdir($logPath,0755,true);
    }
    $file_full_path = $logPath.'log_' . $date . '.log';
    $logger = new \@@namespace@@\Core\PhalBaseLogger($file_full_path);
    $logger->setLogLevel($config->application->logLevel);
    return $logger;
});
<?php

use Phalcon\Cli\Dispatcher;


/**
* Set the default namespace for dispatcher
*/
$di->setShared('dispatcher', function() {
    $dispatcher = new Dispatcher();
    $dispatcher->setDefaultNamespace('@@namespace@@\Modules\Cli\Tasks');
    return $dispatcher;
});

/**
 * DI注册日志服务
 */
$di->setShared('logger', function () use ($di) {
    $config = $this->getConfig(); //使用DI里注册的config服务
    $date = date('Ymd');
    $logPath = $config->application->runtimeDir . 'task_log/';
    if (!is_dir($logPath)) {
        mkdir($logPath,0755,true);
    }
    $file_full_path = $logPath.'log_' . $date . '.log';
    $logger = new \@@namespace@@\Core\PhalBaseLogger($file_full_path);
    $logger->setLogLevel($config->application->logLevel);
    return $logger;
});


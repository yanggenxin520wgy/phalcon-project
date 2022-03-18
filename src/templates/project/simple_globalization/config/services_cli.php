<?php

use Phalcon\Events\Manager;
use Phalcon\Events\Event;
use Phalcon\Cli\Dispatcher;

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

//注册调度器
$di->setShared('dispatcher', function () use ($di) {
    //创建一个事件管理
    $eventManager = new Manager();
    $dispatcher = new Dispatcher();
    $dispatcher->setNamespaceName('@@namespace@@\Modules\\' . ucfirst(COUNTRY_CODE) . '\Tasks');

    //添加一个监听器
    $eventManager->attach('dispatch:beforeException',
        function (Event $event, Dispatcher $dispatcher, \Exception $exception) use ($di) {
            if ($exception instanceof \Phalcon\Cli\Dispatcher\Exception) {
                $key = 'has_dispatcher_' . $dispatcher->getTaskName() . '_' . $dispatcher->getActionName();
                switch ($exception->getCode()) {
                    case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                    case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                        if ($dispatcher->hasOption($key)) {
                            echo $exception->getMessage(), PHP_EOL;
                            return false;
                        } else {
                            $dispatcher->setNamespaceName('');
                            $dispatcher->setTaskName($dispatcher->getTaskName());
                            $dispatcher->setActionName($dispatcher->getActionName());
                            $dispatcher->setParams($dispatcher->getParams());
                        }
                        $dispatcher->setOptions([$key => true]);
                        $dispatcher->dispatch();
                }
                return false;
            }
        }
    );

    $dispatcher->setEventsManager($eventManager);

    return $dispatcher;
});
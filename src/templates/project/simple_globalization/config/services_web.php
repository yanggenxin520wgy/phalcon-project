<?php

use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Events\Manager;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Events\Event;

//注册调度器
$di->setShared('dispatcher', function () use ($di) {
    //创建一个事件管理
    $eventManager = new Manager();

    //添加一个监听器
    $eventManager->attach('dispatch:beforeException',
        function (Event $event, Dispatcher $dispatcher, \Exception $exception) use ($di) {
            if ($exception instanceof \Phalcon\Mvc\Dispatcher\Exception) {
                switch ($exception->getCode()) {
                    case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                    case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                        $dispatcher->forward([
                            'namespace' => '@@namespace@@\Controllers',
                            'controller' => $dispatcher->getControllerName(),
                            'action' => $dispatcher->getActionName(),
                        ]);
                        $di['response']->setStatusCode(200);
                }
                return false;
            }
        }
    );

    $dispatcher = new Dispatcher();
    $dispatcher->setEventsManager($eventManager);

    return $dispatcher;
});

/**
 * Setting up the view component
 */
$di->setShared('view', function () {
    $config = $this->getConfig();

    $view = new View();
    $view->setDI($this);
    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines([
        '.volt' => function ($view) {
            $config = $this->getConfig();

            $volt = new VoltEngine($view, $this);

            $volt->setOptions([
                'compiledPath'      => $config->application->cacheDir,
                'compiledSeparator' => '_'
            ]);

            return $volt;
        },
        '.phtml' => PhpEngine::class

    ]);

    return $view;
});


/**
 *
 */
$di->setShared('request', function (){
    return new @@namespace@@\Library\Request();
});

/**
 *
 */
$di->setShared('response', function (){
    return new @@namespace@@\Library\Response();
});


/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});


/**
 * DI注册日志服务
 */
$di->setShared('logger', function () use ($di) {
    $config = $this->getConfig(); //使用DI里注册的config服务
    $date = date('Ymd');
    $logPath = $config->application->runtimeDir . 'log/';
    if (!is_dir($logPath)) {
        mkdir($logPath,0755,true);
    }
    $file_full_path = $logPath.'log_' . $date . '.log';
    $logger = new \@@namespace@@\Core\PhalBaseLogger($file_full_path);
    $logger->setLogLevel($config->application->logLevel);
    return $logger;
});
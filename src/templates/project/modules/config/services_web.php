<?php

use Phalcon\Mvc\View;
use Phalcon\Events\Manager;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
@@iniConfigImport@@


/**
 * Setting up dispatcher
 */
$di->setShared('dispatcher', function () use($di) {
    $eventsManager = new Manager();

    $eventsManager->attach('dispatch', new \@@namespace@@\Plugins\DispatchPlugin());
    $eventsManager->attach('dispatch', new \@@namespace@@\Plugins\CORSPlugin());
    $eventsManager->attach('dispatch', new \@@namespace@@\Plugins\ExceptionPlugin());

    $dispatcher = new Dispatcher();
    $dispatcher->setEventsManager($eventsManager);

    return $dispatcher;
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



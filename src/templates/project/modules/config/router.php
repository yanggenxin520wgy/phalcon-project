<?php

$router = $di->getRouter();

foreach ($application->getModules() as $key => $module) {
    $namespace = preg_replace('/Module$/', 'Controllers', $module["className"]);
    $router->add('/'.$key.'/:params', [
        'namespace' => $namespace,
        'module' => $key,
        'controller' => 'index',
        'action' => 'index',
        'params' => 1
    ])->setName($key);
    $router->add('/'.$key.'/:controller/:params', [
        'namespace' => $namespace,
        'module' => $key,
        'controller' => 1,
        'action' => 'index',
        'params' => 2
    ]);
    $router->add('/'.$key.'/:controller/:action/:params', [
        'namespace' => $namespace,
        'module' => $key,
        'controller' => 1,
        'action' => 2,
        'params' => 3
    ]);
}

//Rpc模块下面的接口，除svc外均不可以直接访问
$router->add('/rpc/((?!svc).)*/:action/:params', [
    'namespace' => '@@namespace@@\Modules\Controllers',
    'module' => 'common',
    'controller' => 'error',
    'action' => 'notFound'
]);

$router->add('/healthy/index', [
    'namespace' => '@@namespace@@\Modules\Controllers',
    'module' => 'common',
    'controller' => 'healthy',
    'action' => 'index'
]);

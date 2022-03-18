<?php

$router = $di->getRouter();
$config = $di->getConfig();

$default = [
    'namespace'     => '@@namespace@@\Modules\\' . ucfirst(COUNTRY_CODE) . '\Controllers',
    'controller'    => 1,
    'action'        => 2,
    'params'        => 3,
];

if (file_exists($config->application->modulesDir . COUNTRY_CODE . '/Module.php')) {
    $default['module'] = COUNTRY_CODE;
}

$router->add( "/:controller/:action/:params", $default);

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

$router->handle();
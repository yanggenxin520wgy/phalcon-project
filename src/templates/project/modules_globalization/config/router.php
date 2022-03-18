<?php

$router = $di->getRouter();

$uri = $router->getRewriteUri();
$default = [
    'namespace'  => '@@namespace@@\Modules\Base\{$1}\Controllers',
    'module'     => 1,
    'controller' => 2,
    'action'     => 3,
    'params'     => 4,
];

$module = explode('/', trim($uri, '/'));
if (!empty($module)) {
    $module[0] = ucfirst(strtolower($module[0]));
    if (file_exists(APP_PATH . '/modules/' . COUNTRY_CODE . '/' . $module[0] . '/Module.php')) {
        $default['namespace'] = '@@namespace@@\Modules\\' . COUNTRY_CODE . '\\' . $module[0] . '\Controllers';
    } else {
        $default['namespace'] = '@@namespace@@\Modules\\' . COUNTRY_CODE . '\Base\Controllers';
    }
}

//多模块路由设置
$router->add("/:module/:controller/:action/:params", $default);
//Rpc模块下面的接口，除svc外均不可以直接访问
$router->add("/rpc/((?!svc).)*/:action/:params", [
    'namespace'  => '@@namespace@@\Modules\Base\Common\Controllers',
    'module' => 'common',
    'controller' => 'error',
    'action' => 'notFound'
]);

$router->add('/healthy/index/', [
    'namespace'  => '@@namespace@@\Modules\Base\Common\Controllers',
    'module' => 'common',
    'controller' => 'healthy',
    'action' => 'index'
]);

$router->handle();

return $router;
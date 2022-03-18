<?php

$router = $di->getRouter();

// Define your routes here
$router->add('/:controller/:action/:params', [
    'namespace'     => '@@namespace@@\Controllers',
    'controller'    => 1,
    'action'        => 2,
    'params'        => 3,
]);

$router->handle();

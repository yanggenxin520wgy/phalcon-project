<?php

return [
    'common' => [
        'className' => \@@namespace@@\Modules\Common\Module::class,
        'path' => APP_PATH . '/modules/Common/Module.php',
        'metadata'  => [
            'controllersNamespace' => '@@namespace@@\Modules\Common\Controllers',
        ],
    ],
    'cli' => [
        'className' => \@@namespace@@\Modules\Cli\Module::class,
        'path' => APP_PATH . '/modules/Cli/Module.php',
    ],
    'rpc' => [
        'className' => \@@namespace@@\Modules\Rpc\Module::class,
        'path' => APP_PATH . '/modules/Rpc/Module.php',
        'metadata'  => [
            'controllersNamespace' => '@@namespace@@\Modules\Rpc\Controllers',
        ],
    ],
];
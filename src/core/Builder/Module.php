<?php


namespace Project\Core\Builder;

use Project\Traits\functionsTraits;

/**
 * Class Module
 * @package Project\Core\Builder
 */
class Module extends \Phalcon\Builder\Module
{
    use functionsTraits;

    protected $moduleDirectories = [
        'config',
        'controllers',
        'models',
        'services',
        'enums',
        'tasks',
    ];
}
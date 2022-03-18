<?php

namespace Project\Core\Commands\Builtin;


use Project\Core\Commands\CommandsInterface;

/**
 * Class Controller
 * @package Project\Core\Commands\Builtin
 */
class Controller extends \Phalcon\Commands\Builtin\Controller implements CommandsInterface
{
    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getPossibleParams()
    {
        return [
            'name=s'        => '控制器名',
            'namespace=s'   => '控制器命名空间 [可选]',
            'directory=s'   => '项目根目录 [可选]',
            'output=s'      => '控制器路径 [可选]',
            'base-class=s'  => '父类 [可选]',
            'force'         => '覆盖现有控制器 [可选]',
            'help'          => '展示help信息 [可选]',
        ];
    }

    public function getDesc(): string
    {
        return '创建controller文件';
    }
}

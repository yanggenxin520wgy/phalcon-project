<?php

namespace Project\Core\Commands\Builtin;


use Project\Core\Commands\CommandsInterface;

/**
 * Class Model
 * @package Project\Core\Commands\Builtin
 */
class Model extends \Phalcon\Commands\Builtin\Model implements CommandsInterface
{

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getPossibleParams()
    {
        return [
            'name=s'          => '表名',
            'schema=s'        => '库名 [可选]',
            'config=s'        => '配置文件 [可选]',
            'namespace=s'     => "命名空间 [可选]",
            'get-set'         => '属性设置成protected，并通过setter/getter来设置和获取 [可选]',
            'extends=s'       => '父类 [可选]',
            'excludefields=l' => '逗号分隔列举不需要定义的字段 [可选]',
            'doc'             => '类注释 [可选]',
            'directory=s'     => '项目的根路径 [可选]',
            'output=s'        => 'model路径 [可选]',
            'force'           => 'model存在时覆盖原model [可选]',
            'camelize'        => '驼峰命名属性 [可选]',
            'trace'           => '异常时显示异常信息 [可选]',
            'mapcolumn'       => '字段列表 [可选]',
            'abstract'        => '抽象类model [可选]',
            'annotate'        => '注解属性 [可选]',
            'help'            => '展示help信息 [可选]',
        ];
    }

    public function getDesc(): string
    {
        return '创建model文件';
    }
}

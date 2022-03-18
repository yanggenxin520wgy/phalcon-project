<?php

namespace Project\Core\Commands\Builtin;

use Project\Core\Commands\CommandsInterface;

/**
 * Class AllModels
 * @package Project\Core\Commands\Builtin
 */
class AllModels extends \Phalcon\Commands\Builtin\AllModels implements CommandsInterface
{
    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getPossibleParams()
    {
        return [
            'config=s'    => '配置文件 [可选]',
            'schema=s'    => '库名. [可选]',
            'namespace=s' => '命名空间 [可选]',
            'extends=s'   => '父类 [可选]',
            'force'       => 'model存在时覆盖原model [可选]',
            'camelize'    => '驼峰命名属性 [可选]',
            'get-set'     => '属性设置成protected，并通过setter/getter来设置和获取 [可选]',
            'doc'         => '类注释 [可选]',
            'relations'   => '根据约束定义可能的关系 [可选]',
            'fk'          => '定义虚拟外键 [可选]',
            'directory=s' => '项目根目录 [可选]',
            'output=s'    => 'model文件路径 [可选]',
            'mapcolumn'   => '字段列表 [可选]',
            'abstract'    => '抽象model类 [可选]',
            'annotate'    => '注释属性 [可选]',
            'help'        => '展示help信息 [可选]',
        ];
    }

    public function getDesc(): string
    {
        return '根据数据库表创建所有的model文件';
    }
}

<?php

namespace Project\Core\Commands\Builtin;

use Project\Core\Commands\CommandsInterface;

/**
 * Class Migration
 * @package Project\Core\Commands\Builtin
 */
class Migration extends \Phalcon\Commands\Builtin\Migration implements CommandsInterface
{

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getPossibleParams()
    {
        return [
            'action=s'          => '生成迁移脚本 [generate|run]',
            'config=s'          => '配置文件',
            'migrations=s'      => '迁移脚本路径',
            'directory=s'       => '项目目录',
            'table=s'           => '要迁移的表. 表名或者表前缀*. 多个表名\',\'分割 默认: 全部',
            'version=s'         => '本次迁移版本号',
            'descr=s'           => '迁移描述（用于基于时间戳的迁移）',
            'data=s'            => '导出数据 [always|oncreate]（运行迁移时导入数据）',
            'force'             => '强制覆盖现有迁移',
            'ts-based'          => '基于时间戳的迁移版本',
            'log-in-db'         => '将迁移日志保存在数据库表中而不是文件中',
            'dry'               => '失败时输出错误信息而不是尝试创建文件 (仅用于生成迁移脚本)',
            'verbose'           => '运行期间输出调试信息 (仅用于运行迁移脚本)',
            'no-auto-increment' => '禁用自增 (仅用于生成迁移脚本)',
            'help'              => '展示help信息 [可选]',
        ];
    }

    public function getDesc(): string
    {
        return '创建数据库迁移文件';
    }
}

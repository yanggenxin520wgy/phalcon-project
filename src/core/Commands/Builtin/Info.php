<?php


namespace Project\Core\Commands\Builtin;

use Project\Core\Commands\CommandsInterface;
use Project\Core\Utils\SystemInfo;
use Phalcon\Script\Color;

class Info extends \Phalcon\Commands\Builtin\Info implements CommandsInterface
{
    /**
     * {@inheritdoc}
     *
     * @param array $parameters
     * @return mixed
     */
    public function run(array $parameters)
    {
        $info = new SystemInfo();

        printf("%s\n", Color::head('Environment:'));
        foreach ($info->getEnvironment() as $k => $v) {
            printf("  %s: %s\n", $k, $v);
        }

        printf("%s\n", Color::head('Versions:'));
        foreach ($info->getVersions() as $k => $v) {
            printf("  %s: %s\n", $k, $v);
        }

        print PHP_EOL;

        return 0;
    }

    public function getDesc(): string
    {
        return '显示环境信息';
    }
}
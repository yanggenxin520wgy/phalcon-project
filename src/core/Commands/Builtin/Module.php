<?php

namespace Project\Core\Commands\Builtin;

use Project\Core\Commands\CommandsInterface;
use Project\Core\Builder\Module as ModuleBuilder;

/**
 * Class Module
 * @package Project\Core\Commands\Builtin
 */
class Module extends \Phalcon\Commands\Builtin\Module implements CommandsInterface
{
    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getPossibleParams()
    {
        return [
            'name'            => '模块名称',
            'namespace=s'     => "模块命名空间 [可选]",
            'output=s'     => '模块路径 [可选]',
            'template-path=s' => '自定义模板路径 [可选]',
            'help'            => '展示help信息 [可选]',

        ];
    }


    /**
     * {@inheritdoc}
     *
     * @param array $parameters
     * @return mixed
     */
    public function run(array $parameters)
    {
        $moduleName   = $this->getOption(['name', 1]);
        $namespace    = $this->getOption('namespace', null, 'App\Modules');
        $modulesDir   = $this->getOption('output');
        $templatePath = $this->getOption('template-path', null, TEMPLATE_PATH . DS . 'module');

        $builder = new ModuleBuilder([
            'name'         => $moduleName,
            'namespace'    => $namespace,
            'config-type'  => 'php',
            'templatePath' => $templatePath,
            'modulesDir'   => $modulesDir
        ]);

        return $builder->build();
    }

    public function getDesc(): string
    {
        return '创建一个新模块';
    }


}

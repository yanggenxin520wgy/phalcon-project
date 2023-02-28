<?php

namespace Project\Core\Commands\Builtin;


use Project\Core\Commands\CommandsInterface;
use Phalcon\Script\Color;
use Project\core\Builder\Project as ProjectBuilder;

/**
 * Class Project
 * @package Project\Core\Commands\Builtin
 */
class Project extends \Phalcon\Commands\Builtin\Project implements CommandsInterface
{
    public function getDesc(): string
    {
        return '创建新项目';
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getPossibleParams()
    {
        return [
            'name=s'            => '项目名',
            'directory=s'       => '项目根目录 [可选], 默认当前目录',
            'namespace=s'       => '项目命名空间, 默认App [可选]',
            'type=s'            => '项目结构 [simple:单模块, modules：多模块, simple_globalization：单模块->国际化, modules_globalization: 多模块->国际化]',
            'template-path=s'   => '自定义模板文件路径 [可选]',
            'template-engine=s' => '定义试图文件引擎 默认phtml(phtml, volt) [可选]',
            'trace'             => '异常时显示异常跟踪信息 [可选]',
            'help'              => '展示help信息 [可选]',
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
        $projectName    = $this->getOption(['name', 1], null, 'default');
        $projectType    = $this->getOption(['type', 2], null, 'simple');
        $projectPath    = $this->getOption(['directory', 3]);
        $namespace      = $this->getOption(['namespace'], null, "App");
        $templatePath   = $this->getOption(['template-path'], null, TEMPLATE_PATH);
        $templateEngine = $this->getOption(['template-engine'], null, "phtml");

        $builder = new ProjectBuilder([
            'name'           => $projectName,
            'type'           => $projectType,
            'directory'      => $projectPath,
            'enableWebTools' => false,
            'templatePath'   => $templatePath,
            'templateEngine' => $templateEngine,
            'useConfigIni'   => false,
            'namespace'      => $namespace,
        ]);

        return $builder->build();
    }


    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function getHelp()
    {
        print Color::head('Help:') . PHP_EOL;
        print Color::colorize('  Creates a project') . PHP_EOL . PHP_EOL;

        print Color::head('Usage:') . PHP_EOL;
        print Color::colorize('  project [name] [type] [directory]', Color::FG_GREEN)
            . PHP_EOL . PHP_EOL;

        print Color::head('Arguments:') . PHP_EOL;
        print Color::colorize('  help', Color::FG_GREEN);
        print Color::colorize("\tShows this help text") . PHP_EOL . PHP_EOL;

        print Color::head('Example') . PHP_EOL;
        print Color::colorize('  phalcon project store simple', Color::FG_GREEN) . PHP_EOL . PHP_EOL;

        $this->printParameters($this->getPossibleParams());
    }
}

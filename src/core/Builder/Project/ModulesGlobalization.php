<?php


namespace Project\Core\Builder\Project;


use Project\core\Builder\ProjectBuilder;
use Phalcon\Builder\Options;
use Project\Core\Builder\Controller as ControllerBuilder;


class ModulesGlobalization extends ProjectBuilder
{
    protected static $module_key = 'modules_globalization';

    /**
     * Project directories
     * @var array
     */
    protected $projectDirectories = [
        'public',
        'app/language/th',
        'app/modules/base/common/controllers',
        'app/modules/base/common/services',
        'app/modules/base/common/models',
        'app/modules/base/cli/tasks',
        'app/modules/base/cli/migrations',
        'app/modules/th/common/controllers',
        'app/modules/th/common/services',
        'app/modules/th/common/models',
        'app/modules/th/cli/tasks',
        'app/modules/th/cli/migrations',
    ];

    protected $projectConfig;

    public function __construct(Options $options)
    {
        parent::__construct($options);
    }

    public function build()
    {
        $this
            ->buildDirectories()
            ->getVariableValues()
            ->createConfig()
            ->createBootstrapFiles()
            ->createHtaccessFiles()
            ->createCoreFile()
            ->createLibraryFiles()
            ->createValidateFiles()
            ->createEnvFiles()
            ->createEnumsFiles()
            ->createComposerJsonFile()
            ->createTraitsFiles()
            ->createControllerBase()
            ->createBaseTask()
            ->createHtrouterFile()
            ->createErrorController()
            ->createPluginsFiles()
            ->createHealthyController()
            ->createDefaultLanguageFile()
            ->createModuleFiles()
            ->createControllerFile();

        return true;
    }


    protected function createControllerBase()
    {
        $getFile = $this->options->get('templatePath') . '/project/' . self::$module_key . '/ControllerBase.php';
        $putFile = $this->options->get('projectPath') . 'app/modules/base/common/controllers/ControllerBase.php';
        $this->generateFile($getFile, $putFile, 'Common', $this->options->get('namespace'));

        return $this;
    }


    private function createBaseTask()
    {
        $getFile = $this->options->get('templatePath') . '/project/' . self::$module_key . '/MainTask.php';
        $putFile = $this->options->get('projectPath') . 'app/modules/base/cli/tasks/MainTask.php';
        $this->generateFile($getFile, $putFile, 'Base', $this->options->get('namespace'));

        return $this;
    }

    private function createErrorController()
    {
        $getFile = $this->options->get('templatePath') . '/project/' . self::$module_key . '/ErrorController.php';
        $putFile = $this->options->get('projectPath') . '/app/modules/base/common/controllers/ErrorController.php';
        $this->generateFile($getFile, $putFile, 'Common', $this->options->get('namespace', 'App'));

        return $this;
    }

    private function createHealthyController()
    {
        $getFile = $this->options->get('templatePath') . '/project/' . self::$module_key . '/HealthyController.php';
        $putFile = $this->options->get('projectPath') . '/app/modules/base/common/controllers/HealthyController.php';
        $this->generateFile($getFile, $putFile, $this->options->get('name'), $this->options->get('namespace', 'App'));

        return $this;
    }


    /**
     * 创建默认控制器文件
     * @return $this
     * @throws \Phalcon\Builder\BuilderException
     */
    private function createControllerFile()
    {
        $builder = new ControllerBuilder([
            'name'              => 'index',
            'directory'         => $this->options->get('projectPath'),
            'controllersDir'    => $this->options->get('projectPath')  . 'app/modules/base/common/controllers',
            'baseClass'         => 'ControllerBase',
            'namespace'         => $this->options->get('namespace') . '\Modules\Base\Common\Controllers'
        ]);

        $builder->build();

        $builder = new ControllerBuilder([
            'name'              => 'index',
            'directory'         => $this->options->get('projectPath'),
            'controllersDir'    => $this->options->get('projectPath')  . 'app/modules/th/common/controllers',
            'baseClass'         => '\\' . $this->options->get('namespace') . '\Modules\Base\Common\Controllers\IndexController',
            'namespace'         => $this->options->get('namespace') . '\Modules\Th\Common\Controllers'
        ]);

        $builder->build();

        return $this;
    }


    /**
     * 创建默认模块module.php文件
     * @return self
     */
    protected function createModuleFiles()
    {
        $getFile = $this->options->get('templatePath') . '/project/' . self::$module_key . '/module_web.php';
        $putFile = $this->options->get('projectPath') . '/app/modules/base/common/module.php';
        $this->generateFile($getFile, $putFile, 'Base', $this->options->get('namespace', 'App'));

        $getFile = $this->options->get('templatePath') . '/project/' . self::$module_key . '/module_cli.php';
        $putFile = $this->options->get('projectPath') . '/app/modules/base/cli/module.php';
        $this->generateFile($getFile, $putFile, 'Base', $this->options->get('namespace', 'App'));

        $getFile = $this->options->get('templatePath') . '/project/' . self::$module_key . '/module_web.php';
        $putFile = $this->options->get('projectPath') . '/app/modules/th/common/module.php';
        $this->generateFile($getFile, $putFile, 'Th', $this->options->get('namespace', 'App'));

        $getFile = $this->options->get('templatePath') . '/project/' . self::$module_key . '/module_cli.php';
        $putFile = $this->options->get('projectPath') . '/app/modules/th/cli/module.php';
        $this->generateFile($getFile, $putFile, 'Th', $this->options->get('namespace', 'App'));

        return $this;
    }

}
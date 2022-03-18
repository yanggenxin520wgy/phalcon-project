<?php


namespace Project\Core\Builder\Project;

use Phalcon\Builder\Options;
use Project\Core\Builder\Controller as ControllerBuilder;

/**
 * Class SimpleGlobalization
 * @package Project\Core\Builder\Project
 */
class SimpleGlobalization extends \Project\core\Builder\ProjectBuilder
{
    protected static $module_key = 'simple_globalization';

    /**
     * Project directories
     * @var array
     */
    protected $projectDirectories = [
        'public',
        'app/config',
        'app/language/th',
        'app/modules/th/config',
        'app/modules/th/controllers',
        'app/modules/th/models',
        'app/modules/th/services',
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
            ->createPluginsFiles()
            ->createHealthyController()
            ->createDefaultLanguageFile()
            ->createThModuleConfig()
            ->createModules()
            ->createControllerFile();

        return true;
    }


    /**
     * 创建控制器基类文件
     *
     * @return $this
     */
    protected function createControllerBase()
    {
        $getFile = $this->options->get('templatePath') . '/project/' . self::$module_key . '/ControllerBase.php';
        $putFile = $this->options->get('projectPath') . 'app/controllers/ControllerBase.php';
        $this->generateFile($getFile, $putFile, $this->options->get('name'), $this->options->get('namespace'));

        return $this;
    }

    private function createBaseTask()
    {
        $getFile = $this->options->get('templatePath') . '/project/' . self::$module_key . '/BaseTask.php';
        $putFile = $this->options->get('projectPath') . '/app/tasks/BaseTask.php';
        $this->generateFile($getFile, $putFile, $this->options->get('name'), $this->options->get('namespace'));

        return $this;
    }

    /**
     * 创建健康检查文件
     */
    protected function createHealthyController()
    {
        $getFile = $this->options->get('templatePath') . '/project/' . self::$module_key . '/HealthyController.php';
        $putFile = $this->options->get('projectPath') . '/app/controllers/HealthyController.php';
        $this->generateFile($getFile, $putFile, $this->options->get('name'), $this->options->get('namespace', 'App'));

        return $this;
    }


    /**
     * 创建默认控制器
     */
    protected function createControllerFile()
    {
        $builder = new ControllerBuilder([
            'name'              => 'index',
            'directory'         => $this->options->get('projectPath'),
            'controllersDir'    => $this->options->get('projectPath')  . 'app/controllers',
            'baseClass'         => 'ControllerBase',
            'namespace'         => $this->options->get('namespace') . '\Controllers'
        ]);

        $builder->build();

        $builder = new ControllerBuilder([
            'name'              => 'index',
            'directory'         => $this->options->get('projectPath'),
            'controllersDir'    => $this->options->get('projectPath')  . 'app/modules/th/controllers',
            'baseClass'         => '\\' . $this->options->get('namespace') . '\Controllers\IndexController',
            'namespace'         => $this->options->get('namespace') . '\Modules\Th\Controllers'
        ]);

        $builder->build();

        return $this;
    }


    private function createThModuleConfig()
    {
        $content = <<< CONTENT
<?php

return [
    'application' => [
        'controllersDir'    => __DIR__ . '/../controllers/',
        'servicesDir'       => __DIR__ . '/../services/',
        'modelsDir'         => __DIR__ . '/../models/',
        'tasksDir'          => __DIR__ . '/../tasks/',
        'enumsDir'          => __DIR__ . '/../enums/',
    ]
];
CONTENT;

        file_put_contents($this->options->get('projectPath') . '/app/modules/th/config/config.php', $content);
        return $this;
    }

    /**
     * Create Module
     *
     * @return $this
     */
    private function createModules()
    {
        $getFile = $this->options->get('templatePath') . '/project/' . self::$module_key . '/Module_web.php';
        $putFile = $this->options->get('projectPath') . 'app/modules/th/Module.php';
        $this->generateFile($getFile, $putFile, 'Th', $this->options->get('namespace'));

        return $this;
    }

}
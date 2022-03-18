<?php


namespace Project\Core\Builder\Project;

use Project\core\Builder\ProjectBuilder;
use Phalcon\Builder\Options;

/**
 * Class Modules
 * @package Project\Core\Builder\Project
 */
class Modules extends ProjectBuilder
{
    protected static $module_key = 'modules';

    /**
     * Project directories
     * @var array
     */
    protected $projectDirectories = [
        'app/config',
        'public',
        'app/modules/rpc/controllers',
        'app/modules/common/controllers',
        'app/language/th',
    ];

    protected $projectConfig;


    public function __construct(Options $options)
    {
        parent::__construct($options);
    }


    /**
     * Create Default Tasks
     *
     * @return $this
     */
    private function createDefaultTasks()
    {
        $getFile = $this->options->get('templatePath') . '/project/' . self::$module_key . '/MainTask.php';
        $putFile = $this->options->get('projectPath') . 'app/modules/cli/tasks/MainTask.php';
        $this->generateFile($getFile, $putFile, $this->options->get('name'), $this->options->get('namespace'));

        return $this;
    }

    private function createSvcController() {
        $getFile = $this->options->get('templatePath') . '/project/' . self::$module_key . '/SvcController.php';
        $putFile = $this->options->get('projectPath') . 'app/modules/rpc/controllers/SvcController.php';
        $this->generateFile($getFile, $putFile, $this->options->get('name'), $this->options->get('namespace'));

        return $this;
    }


    /**
     * Create ControllerBase
     *
     * @return $this
     */
    private function createControllerBase()
    {
        $getFile = $this->options->get('templatePath') . '/project/' . self::$module_key . '/ControllerBase.php';
        $putFile = $this->options->get('projectPath') . 'app/modules/rpc/ControllerBase.php';
        $this->generateFile($getFile, $putFile, $this->options->get('name'), $this->options->get('namespace'));

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
        $putFile = $this->options->get('projectPath') . 'app/modules/common/Module.php';
        $this->generateFile($getFile, $putFile, 'Common', $this->options->get('namespace'));

        $putFile = $this->options->get('projectPath') . 'app/' . self::$module_key . '/rpc/Module.php';
        $this->generateFile($getFile, $putFile, 'Rpc', $this->options->get('namespace'));

        $getFile = $this->options->get('templatePath') . '/project/' . self::$module_key . '/Module_cli.php';
        $putFile = $this->options->get('projectPath') . 'app/modules/cli/Module.php';
        $this->generateFile($getFile, $putFile, $this->options->get('name'), $this->options->get('namespace'));

        return $this;
    }


    /**
     * 创建健康检查文件
     */
    protected function createHealthyController()
    {
        $getFile = $this->options->get('templatePath') . '/project/' . self::$module_key . '/HealthyController.php';
        $putFile = $this->options->get('projectPath') . '/app/modules/common/controllers/HealthyController.php';
        $this->generateFile($getFile, $putFile, $this->options->get('name'), $this->options->get('namespace', 'App'));

        return $this;
    }

    /**
     * 创建错误返回控制器文件
     */
    protected function createErrorController()
    {
        $getFile = $this->options->get('templatePath') . '/project/' . self::$module_key . '/ErrorController.php';
        $putFile = $this->options->get('projectPath') . '/app/modules/common/controllers/ErrorController.php';
        $this->generateFile($getFile, $putFile, $this->options->get('name'), $this->options->get('namespace', 'App'));

        return $this;
    }


    private function createCommonModuleConfig()
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

        file_put_contents($this->options->get('projectPath') . '/app/modules/common/config/config.php', $content);
        return $this;
    }


    /**
     * Build project
     *
     * @return bool
     */
    public function build()
    {
        $this
            ->buildDirectories()
            ->getVariableValues()
            ->createConfig()
            ->createBootstrapFiles()
            ->createHtaccessFiles()
            ->createControllerBase()
            ->createDefaultTasks()
            ->createModules()
            ->createCoreFile()
            ->createLibraryFiles()
            ->createValidateFiles()
            ->createEnvFiles()
            ->createEnumsFiles()
            ->createComposerJsonFile()
            ->createTraitsFiles()
            ->createSvcController()
            ->createHealthyController()
            ->createErrorController()
            ->createPluginsFiles()
            ->createDefaultLanguageFile()
            ->createCommonModuleConfig()
            ->createHtrouterFile();

        return true;
    }
}

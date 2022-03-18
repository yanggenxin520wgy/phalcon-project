<?php


namespace Project\Core\Builder\Project;


use Project\Core\Builder\Controller as ControllerBuilder;
use Phalcon\Builder\Options;
use Project\core\Builder\ProjectBuilder;

/**
 * Class Simple
 * @package Project\Core\Builder\Project
 */
class Simple extends ProjectBuilder
{
    protected static $module_key = 'simple';

    /**
     * Project directories
     * @var array
     */
    protected $projectDirectories = [
        'public',
        'app/config',
        'app/language/th',
    ];

    protected $projectConfig;

    public function __construct(Options $options)
    {
        parent::__construct($options);
    }


    /**
     * Create indexController file
     *
     * @return $this
     */
    private function createControllerFile()
    {
        $builder = new ControllerBuilder([
            'name'              => 'index',
            'directory'         => $this->options->get('projectPath'),
            'controllersDir'    => $this->options->get('projectPath')  . 'app/controllers',
            'baseClass'         => 'ControllerBase',
            'namespace'         => $this->options->get('namespace') . '\Controllers'
        ]);

        $builder->build();

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


    private function createErrorController()
    {
        $getFile = $this->options->get('templatePath') . '/project/' . self::$module_key . '/ErrorController.php';
        $putFile = $this->options->get('projectPath') . '/app/controllers/ErrorController.php';
        $this->generateFile($getFile, $putFile, $this->options->get('name'), $this->options->get('namespace', 'App'));

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
            ->createControllerFile();

        return true;
    }
}

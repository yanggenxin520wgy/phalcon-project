<?php


namespace Project\core\Builder;


use Phalcon\Builder\Options;
use Project\Core\Utils\Color;

/**
 * Class ProjectBuilder
 * @package Project\core\Builder
 */
abstract class ProjectBuilder extends \Phalcon\Builder\Project\ProjectBuilder
{
    protected static $module_key;

    protected $projectDirectories;

    protected $projectConfig;

    public function __construct(Options $options)
    {
        parent::__construct($options);
        $this->options->offsetSet('useConfigIni', false);
        if (isset(static::$module_key)) {
            if (!file_exists($this->options->get('templatePath') . '/project/' . static::$module_key .'/config/config.php')) {
                $this->options->offsetSet('templatePath', TEMPLATE_PATH);
                echo Color::warning('指定的模板文件不存在，已使用工具模板！'), PHP_EOL;
            }
            $this->projectConfig = include $this->options->get('templatePath') . '/project/' . static::$module_key .'/config/config.php';
            foreach ($this->projectConfig->application as $dir => $path) {
                if (strrpos($dir, 'Dir', -3) !== false) {
                    $this->projectDirectories[] = trim(str_replace(rtrim($this->options->get('templatePath'), '/') . '/project/', '', $path), '/');
                }
            }

            $this->projectDirectories = array_unique($this->projectDirectories);
            sort($this->projectDirectories);
        }
    }

    /**
     * Generate file $putFile from $getFile, replacing @@variableValues@@
     *
     * @param string $getFile From file
     * @param string $putFile To file
     * @param string $name
     * @param string $namespace
     *
     * @return $this
     */
    protected function generateFile($getFile, $putFile, $name = '', $namespace = '')
    {
        if (false == file_exists($putFile)) {
            touch($putFile);
            $fh = fopen($putFile, "w+");

            $str = file_get_contents($getFile);
            if ($name) {
                $nameSpace = ucfirst($name);
                if (strtolower(trim($name)) == 'default') {
                    $nameSpace = 'MyDefault';
                }

                $str = preg_replace('/@@name@@/', $name, $str);
            }
            if ($namespace) {
                $nameSpace = ucfirst($namespace);
            }
            isset($nameSpace) && $str = preg_replace('/@@namespace@@/', $nameSpace, $str);

            if (sizeof($this->variableValues) > 0) {
                foreach ($this->variableValues as $variableValueKey => $variableValue) {
                    $variableValueKeyRegEx = '/@@'.preg_quote($variableValueKey, '/').'@@/';
                    $str = preg_replace($variableValueKeyRegEx, $variableValue, $str);
                }
            }

            fwrite($fh, $str);
            fclose($fh);
        }

        return $this;
    }

    /**
     * Create .htaccess files by default of application
     *
     * @return $this
     */
    protected function createHtaccessFiles()
    {
        if (file_exists($this->options->get('projectPath') . '.htaccess') == false) {
            $code = '<IfModule mod_rewrite.c>' . PHP_EOL .
                "\t" . 'RewriteEngine on' . PHP_EOL .
                "\t" . 'RewriteRule  ^$ public/    [L]' . PHP_EOL .
                "\t" . 'RewriteRule  (.*) public/$1 [L]' . PHP_EOL .
                '</IfModule>';
            file_put_contents($this->options->get('projectPath') . '.htaccess', $code);
        }

        if (file_exists($this->options->get('projectPath') . 'public/.htaccess') == false) {
            file_put_contents(
                $this->options->get('projectPath') . 'public/.htaccess',
                file_get_contents($this->options->get('templatePath') . '/project/htaccess')
            );
        }

        if (file_exists($this->options->get('projectPath') . 'index.html') == false) {
            $code = '<html><body><h1>Mod-Rewrite is not enabled</h1>' .
                '<p>Please enable rewrite module on your web server to continue</body></html>';
            file_put_contents($this->options->get('projectPath') . 'index.html', $code);
        }

        return $this;
    }


    /**
     * 创建入口文件
     *
     * @return $this
     */
    protected function createBootstrapFiles()
    {
        $getFile = $this->options->get('templatePath') . '/project/' . static::$module_key . '/index.php';
        $putFile = $this->options->get('projectPath') . 'public/index.php';
        $this->generateFile($getFile, $putFile, $this->options->get('name'), $this->options->get('namespace'));

        $getFile = $this->options->get('templatePath') . '/project/' . static::$module_key . '/cli.php';
        $putFile = $this->options->get('projectPath') . 'app/cli.php';
        $this->generateFile($getFile, $putFile, $this->options->get('name'), $this->options->get('namespace'));
        chmod($putFile, 0755);

        return $this;
    }


    /**
     * 创建env文件
     * @return $this
     */
    protected function createEnvFiles()
    {

        $getFile = $this->options->get('templatePath') . '/project/.env.example';
        $putFile = $this->options->get('projectPath') . '/.env.example';
        $this->generateFile($getFile, $putFile);

        return $this;
    }


    /**
     * 创建composer.json文件
     * @return $this
     */
    protected function createComposerJsonFile()
    {
        $getFile = $this->options->get('templatePath') . '/project/composer.json';
        $putFile = $this->options->get('projectPath') . '/composer.json';
        $this->generateFile($getFile, $putFile);

        return $this;
    }


    /**
     * 创建基类文件
     * @return $this
     */
    protected function createCoreFile()
    {
        $path = $this->options->get('templatePath') . '/project/core/';
        return $this->createFromDir($path, 'app/core');
    }


    /**
     * 创建库函数/类文件
     * @return $this
     */
    protected function createLibraryFiles()
    {
        $path = $this->options->get('templatePath') . '/project/library/';
        return $this->createFromDir($path, 'app/library');
    }


    /**
     * 创建trait文件
     * @return $this
     */
    protected function createTraitsFiles()
    {
        $path = $this->options->get('templatePath') . '/project/traits/';
        return $this->createFromDir($path, 'app/traits');
    }


    /**
     * 创建验证器文件
     * @return $this
     */
    protected function createValidateFiles()
    {
        $path = $this->options->get('templatePath') . '/project/validates/';
        return $this->createFromDir($path, 'app/validators');
    }


    /**
     * 创建枚举文件
     * @return $this
     */
    protected function createEnumsFiles()
    {

        $getFile = $this->options->get('templatePath') . '/project/ErrCode.php';
        $putFile = $this->options->get('projectPath') . '/app/enums/ErrCode.php';
        $this->generateFile($getFile, $putFile, $this->options->get('name'), $this->options->get('namespace', 'App'));

        $getFile = $this->options->get('templatePath') . '/project/Enums.php';
        $putFile = $this->options->get('projectPath') . '/app/enums/Enums.php';
        $this->generateFile($getFile, $putFile, $this->options->get('name'), $this->options->get('namespace', 'App'));

        return $this;
    }

    /**
     * 创建.htrouter文件
     *
     * @return $this
     */
    protected function createHtrouterFile()
    {
        $getFile = rtrim($this->options->get('templatePath'), '\\/') . DS . '.htrouter.php';
        $putFile   = $this->options->get('projectPath')  . '.htrouter.php';
        $this->generateFile($getFile, $putFile);

        return $this;
    }


    protected function createDefaultLanguageFile() {
        $content = <<< LANGUAGE
<?php

return [];

LANGUAGE;
        file_put_contents($this->options->get('projectPath') . '/app/language/th/en.php', $content);

        return $this;
    }

    /**
     * 创建配置文件
     *
     * @return $this
     */
    protected function createConfig()
    {
        $getFile = $this->options->get('templatePath') . '/project/services.php';
        $putFile = $this->options->get('projectPath') . 'app/config/services.php';
        $this->generateFile($getFile, $putFile, $this->options->get('name'), $this->options->get('namespace'));

        $path = $this->options->get('templatePath') . '/project/' . static::$module_key . '/config/';

        return $this->createFromDir($path, 'app/config');
    }

    /**
     * @return $this
     */
    protected function createPluginsFiles()
    {
        $path = $this->options->get('templatePath') . '/project/' . static::$module_key . '/plugins/';

        return $this->createFromDir($path, 'app/plugins');
    }


    private function createFromDir(string $fromPath, string $toPath)
    {
        if (is_dir($fromPath)) {
            $dirIterator = new \DirectoryIterator($fromPath);
            foreach ($dirIterator as $file) {
                if ($file->isDot() || !$file->isFile() || $file->getExtension() != 'php') {
                    continue;
                }

                $getFile = $fromPath . DS . $file->getFilename();
                $putFile = $this->options->get('projectPath') . DS . $toPath . DS . $file->getFilename();
                $this->generateFile($getFile, $putFile, $this->options->get('name'),
                    $this->options->get('namespace', 'App'));
            }
        }

        return $this;
    }
}
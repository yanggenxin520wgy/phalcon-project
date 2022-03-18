<?php
namespace @@namespace@@\Modules\@@name@@\Cli;

use Phalcon\DiInterface;
use Phalcon\Loader;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Config;

class Module implements ModuleDefinitionInterface
{
    /**
     * Registers an autoloader related to the module
     *
     * @param DiInterface $di
     */
    public function registerAutoloaders(DiInterface $di = null)
    {
        $loader = new Loader();

        $loader->registerNamespaces([
            '@@namespace@@\Modules\@@name@@\Cli\Task' => __DIR__ . '/tasks/',
            '@@namespace@@\Modules\@@name@@\Cli\Migrations' => __DIR__ . '/migrations/',
        ]);

        $loader->register();
    }

    /**
     * Registers services related to the module
     *
     * @param DiInterface $di
     */
    public function registerServices(DiInterface $di)
    {
        /**
         * Try to load local configuration
         */
        if (file_exists(__DIR__ . '/config/config.php')) {

            $config = $di['config'];

            $override = new Config(include __DIR__ . '/config/config.php');
            if ($config instanceof Config) {
                $config->merge($override);
            } else {
                $config = $override;
            }
        }
    }
}

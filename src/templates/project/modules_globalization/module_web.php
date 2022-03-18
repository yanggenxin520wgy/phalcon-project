<?php
namespace @@namespace@@\Modules\@@name@@\Common;

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
            '@@namespace@@\Modules\@@name@@\Common\Controllers' => __DIR__ . '/controllers/',
            '@@namespace@@\Modules\@@name@@\Common\Models' => __DIR__ . '/models/',
            '@@namespace@@\Modules\@@name@@\Common\Services' => __DIR__ . '/services/',
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

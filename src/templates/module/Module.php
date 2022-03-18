<?php

namespace @@FQMN@@;

use Phalcon\DiInterface;
use Phalcon\Loader;
use Phalcon\Mvc\ModuleDefinitionInterface;
@@iniConfigImport@@
@@useConfig@@

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
            '@@FQMN@@\Controllers'  => __DIR__ . '/controllers/',
            '@@FQMN@@\Models'       => __DIR__ . '/models/',
            '@@FQMN@@\Services'     => __DIR__ . '/services/',
            '@@FQMN@@\Tasks'        => __DIR__ . '/tasks/',
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
        if (file_exists(@@configName@@)) {
            
            $config = $di['config'];
            
            $override = @@configLoader@@;

            if ($config instanceof Config) {
                $config->merge($override);
            } else {
                $config = $override;
            }
        }
    }
}

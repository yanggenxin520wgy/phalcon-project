<?php


namespace @@namespace@@\Plugins;


use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\User\Plugin;

class DispatchPlugin extends Plugin
{
    /**
     * @param Event $event
     * @param Dispatcher $dispatcher
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {
        $modules = include APP_PATH . "/config/modules.php";
        $module = $dispatcher->getModuleName() ?: 'common';
        $metadata = $modules[$module]['metadata'];
        $dispatcher->setNamespaceName($metadata['controllersNamespace']);
    }

    /**
     * @param Event $event
     * @param Dispatcher $dispatcher
     * @param array $forward
     */
    public function beforeForward(Event $event, Dispatcher $dispatcher, array $forward)
    {
        $modules = include APP_PATH . "/config/modules.php";
        $metadata = $modules[$forward['module']]['metadata'];
        $dispatcher->setModuleName($forward['module']);
        $dispatcher->setNamespaceName($metadata['controllersNamespace']);
    }

}
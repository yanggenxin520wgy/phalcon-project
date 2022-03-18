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
        $module = $dispatcher->getModuleName() ?: 'common';
        $namespace = '\@@namespace@@\Modules\Base\\' . ucfirst($module) . '\Controllers';
        $dispatcher->setDefaultNamespace($namespace);
    }

    /**
     * @param Event $event
     * @param Dispatcher $dispatcher
     * @param array $forward
     */
    public function beforeForward(Event $event, Dispatcher $dispatcher, array $forward)
    {
        $namespace = '\@@namespace@@\Modules\Base\\' . ucfirst($forward['module']) . '\Controllers';
        $dispatcher->setModuleName($forward['module']);
        $dispatcher->setDefaultNamespace($namespace);
    }

}
<?php

namespace @@namespace@@\Plugins;

use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Dispatcher\Exception as DispatchException;
use Phalcon\Mvc\User\Plugin;

/**
 * NotFoundPlugin
 * Handles not-found controllers/actions
 */
class ExceptionPlugin extends Plugin
{
    /**
     * This action is executed before perform any action in the application
     *
     * @param Event $event
     * @param Dispatcher $dispatcher
     * @param \Exception $exception
     * @return bool
     */
    public function beforeException(Event $event, Dispatcher $dispatcher, $exception)
    {
        $data = null;
        $forward = [
            'namespace' => '@@namespace@@\Modules\Base\Common\Controllers',
            'module' => 'common',
            'controller' => 'error',
        ];

        if ($exception instanceof DispatchException) {
            $forward['namespace'] = '@@namespace@@\Modules\Base\\' . ucfirst($dispatcher->getModuleName()) . '\Controllers';
            $forward['module'] = $dispatcher->getModuleName();
            $forward['controller'] = $dispatcher->getControllerName();
            $forward['action'] = $dispatcher->getActionName();
            $this->getDI()['response']->setStatusCode(200);
        } else {
            $forward['action'] = 'exception';
            $forward['params'] = ['code' => $exception->getCode(), 'msg' => $exception->getMessage()];
            $this->getDI()->get('logger')->write_log($exception->getMessage());
        }

        $dispatcher->forward($forward);
        return false;
    }
}
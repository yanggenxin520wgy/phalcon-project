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
            'module' => 'common',
            'controller' => 'error',
        ];

        if ($exception instanceof DispatchException) {
            $forward['action'] = 'notFound';
        } else {
            $forward['action'] = 'exception';
            $forward['params'] = ['code' => $exception->getCode(), 'msg' => $exception->getMessage()];
            $this->getDI()->get('logger')->write_log($exception->getMessage());
        }

        $dispatcher->forward($forward);
        return false;
    }
}
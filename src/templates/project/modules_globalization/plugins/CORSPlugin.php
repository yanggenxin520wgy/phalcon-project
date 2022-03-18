<?php


namespace @@namespace@@\Plugins;


use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\User\Plugin;

class CORSPlugin extends Plugin
{
    /**
     * @param Event $event
     * @param Dispatcher $dispatcher
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {
        $this->setCorsPolicy();
        //跨域处理
        if ($this->request->isOptions()) {
            $this->response->send();
            exit();
        }
    }

    /**
     *
     */
    private function setCorsPolicy()
    {
        if ($this->request->getHeader('ORIGIN')) {
            $origin = $this->request->getHeader('ORIGIN');
        } else {
            $origin = '*';
        }
        $headers = [
            'Origin',
            'X-Requested-With',
            'Content-Range',
            'Content-Disposition',
            'Content-Type',
            'Authorization',
            'Accept-Encoding',
            'Accept-Language',
            'Cache-Control',
        ];
        $this
            ->response
            ->setHeader('Access-Control-Allow-Origin', $origin)
            ->setHeader('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', implode(',', $headers))
            ->setHeader('Access-Control-Allow-Credentials', 'true');
    }
}
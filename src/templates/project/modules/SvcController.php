<?php


namespace @@namespace@@\Modules\Rpc\Controllers;

use JsonRPC\Server;
use @@namespace@@\Modules\Rpc\ControllerBase;

class SvcController extends ControllerBase
{
    /**
     * @var Server
     */
    protected $_server;

    /**
     * @var string
     */
    protected $response;

    protected $params;

    public function onConstruct()
    {
        $this->view->disable(false);
        parent::onConstruct();
        $this->_server = new Server();
        $this->params = file_get_contents('php://input');
        $params = json_decode($this->params, true);
        static::$rpc_version = $params['jsonrpc'] ?? 2.0;
        static::$rpc_id = $params['id'] ?? '';
    }

    public function callAction()
    {
        try {
            $pathInfo = pathinfo(__FILE__);
            $files = scandir(__DIR__);
            $files = array_filter($files, function ($value, $index) use ($pathInfo) {
                return !in_array($value, ['.', '..', $pathInfo['basename']]);
            }, ARRAY_FILTER_USE_BOTH);

            $namespace = __NAMESPACE__;
            $callbacks = [];
            foreach ($files as $file) {
                $file = pathinfo($file);
                $class = $namespace . '\\' . $file['filename'];
                $object = new $class();
                $re_class = new \ReflectionClass($object);
                $class_name = strtolower(str_replace('Controller', '', $file['filename']));
                //遍历绑定
                foreach ($re_class->getMethods() as $method) {
                    if ($method->isPublic() && $method->isFinal() && $method->name != '__construct') {//判断方法是否是公开，只绑定公开final方法
                        $callbacks[$class_name . '.' . $method->name] = [new $class(), $method->name];
                    }
                }
            }

            $this->_server->getProcedureHandler()->withClassAndMethodArray($callbacks);

            $this->response =  $this->_server->execute();

            echo $this->response;
        } catch (\Throwable $t) {
            $this->response = $t;
            echo null;
        }
    }


    public function __destruct()
    {
        $level = 'info';
        $response = $this->response ?? static::$svc_response;

        $log_info = [
            'request_info' => json_decode($this->params, true),
            'response_info' => json_decode($response, true)
        ];

        if ($response instanceof \Throwable) {
            $log_info['response_info'] = $response->getMessage() . PHP_EOL . $response->getTraceAsString();
        }
        $this->getDI()->get('svcLogger')->write_log($log_info, $level);
    }
}
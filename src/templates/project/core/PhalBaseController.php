<?php


namespace @@namespace@@\Core;

use Phalcon\Translate\Adapter\NativeArray;
use @@namespace@@\Library\Response;

/**
 * Class PhalBaseController
 * @package App\Core
 * @property array $locale
 * @property string $lang
 * @property NativeArray $_dict
 */
class PhalBaseController extends \Phalcon\Mvc\Controller
{
    protected static $instance;
    /** @var PhalBaseLogger */
    protected $logger;
    protected static $svc_response;
    protected static $rpc_version = 2.0;
    protected static $rpc_id = null;

    public function initialize()
    {
        //项目设置零时区
        date_default_timezone_set('UTC');
    }

    public function onConstruct() {
        $this->logger = $this->getDI()->get('logger');
        $this->getLanguage();
        [$this->lang, $this->_dict] = PhalBaseService::setLanguage($this->locale);
    }

    public function getLanguage()
    {
        if (substr(php_sapi_name(), 0, 3) == 'cli.php') {
            $this->locale = ['locale' => 'zh'];
        } else {
            $params = json_decode(file_get_contents('php://input'), true)['params'] ?? [];
            if (empty($params)) {
                $this->locale = ['locale' => $this->request->getBestLanguage() ?: 'en'];
            } else {
                $this->locale = $params[0] ?? [];
            }
        }
    }


    /**
     * 返回请求json串
     * @param $code
     * @param $message
     * @param array $data
     * @param int $statusCode
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    protected function returnJson($code, $message, $data = [], $statusCode = Response::CODE_OK)
    {
        $result = [
            'code'      => $code,
            'message'   => $message,
            'data'      => $data,
        ];
        $this->response->setStatusCode($statusCode);
        $this->response->setJsonContent($result);

        return $this->response;
    }


    /**
     * 获取唯一实例
     * @return static
     */
    public static function getInstance() {
        if (!static::$instance instanceof static) {
            static::$instance = new static();
        }

        return static::$instance;
    }
}
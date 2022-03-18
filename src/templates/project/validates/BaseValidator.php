<?php


namespace @@namespace@@\Validators;


use @@namespace@@\Core\PhalBaseController;
use @@namespace@@\Enums\ErrCode;


class BaseValidator extends PhalBaseController
{

    const page = [
        'pageSize' => 'IntGeLe:1,1000',//每页条数
        'pageNum' => 'IntGeLe:1,100000000',
    ];

    const id = [
        'id' => 'Required|IntGe:1',
    ];

    const ids = [
        'ids'       => 'Required|ArrLenGe:1',
        'ids[*]'    => 'IntGe:1',
    ];

    protected static $params = [];

    /**
     ********请注意此处**********
     * [
     *  base => ['公共验证规则']，
     *  th => ['泰国差异验证规则'],
     *  ph => ['菲律宾差异验证规则'],
     *  ...
     * ]
     * @var array
     */
    protected static $validator = [];

    /**
     * 验证参数
     * @param array $input
     * @return array
     */
    public function validate(?array $input = null) {
        $input = empty($input) ? self::$params : $input;
        $validator = static::getValidator();
        try {
            MyValidate::setLangCode($this->lang);
            MyValidate::validate($input, $validator);
            return ['code' => ErrCode::$SUCCESS, 'message' => 'Success'];
        } catch (\Throwable $t) {
            if (substr(php_sapi_name(), 0, 3) == 'cli.php') {//任务调用
                return ['code' => ErrCode::$VALIDATE_ERROR, 'message' => $t->getMessage()];
            } else {//接口调用
                header('Content-type:application/json;charset=utf-8');
                if($this->dispatcher->getModuleName() == 'rpc') {//svc接口
                    $ret = $this->svcReturn(ErrCode::$VALIDATE_ERROR, $t->getMessage());
                    static::$svc_response = $ret;
                } else {
                    $ret = $this->returnJson(ErrCode::$VALIDATE_ERROR, $t->getMessage());
                }
                exit($ret);
            }
        }
    }

    /**
     * 返回svc结构数据
     * @param int $code
     * @param string $message
     * @param array $data
     * @return string
     */
    public function svcReturn($code = 1, $message = 'Success', $data = []) {
        $result = [
            'jsonrpc'   => static::$rpc_version,
            'result'    => [
                'code'      => $code,
                'message'   => $message,
                'data'      => $data,
            ],
            'id' => static::$rpc_id,
        ];
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }


    /**
     * 设置验证规则
     * @param array $validator
     * @return self
     */
    public static function setValidator(array $validator)
    {
        self::$validator = $validator;
        return self::getInstance();
    }


    /**
     * 获取验证规则
     * @return array
     */
    public static function getValidator():array
    {
        $validator  = static::$validator;
        $base       = $validator['base'] ?? [];
        $country    = $validator[COUNTRY_CODE] ?? [];

        $validator_country = array_merge($base, $country);

        return $validator_country ?: $validator;
    }

    public static function id() {
        self::$validator = [
            'base' => self::id,
        ];

        return static::getInstance();
    }

    public static function ids() {
        self::$validator = [
            'base' => self::ids,
        ];

        return static::getInstance();
    }

    public static function page() {
        self::$validator = [
            'base' => self::page,
        ];

        return static::getInstance();
    }

    public static function id_page() {
        self::$validator = [
            'base' => array_merge(self::id, self::page),
        ];

        return static::getInstance();
    }
}
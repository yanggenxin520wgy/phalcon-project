<?php


namespace @@namespace@@\Library;

use Stringy\StaticStringy;

class Request extends \Phalcon\Http\Request
{
    protected $uid;

    /**
     * 获取JWT
     * @return string
     */
    public function getToken()
    {
        return str_replace('Bearer ', '', $this->getHeader('Authorization'));
    }

    /**
     * 判断JWT是否为空
     * @return bool
     */
    public function isEmptyToken()
    {
        return true === empty($this->getToken());
    }

    /**
     * Checks whether request body is json
     *
     * @return bool
     */
    public function isJsonBody()
    {
        return StaticStringy::startsWith(strtolower($this->getContentType()) ,'application/json');
    }

    /**
     * 获取请求参数，同时支持 form 和 json
     * @param null $name
     * @param null $filters
     * @param null $defaultValue
     * @param bool $notAllowEmpty
     * @param bool $noRecursive
     * @return array|bool|mixed|\stdClass|null
     */
    public function get($name = null, $filters = null, $defaultValue = null, $notAllowEmpty = false, $noRecursive = false)
    {
        if ($this->isJsonBody()) {
            $data = $this->getJsonRawBody(true);
            return $this->sanitize((array)$data, $name , $filters, $defaultValue, $noRecursive);
        }

        return parent::get($name, $filters, $defaultValue, $notAllowEmpty, $noRecursive);
    }

    /**
     * @param array $data
     * @param string|null $name
     * @param null $filters
     * @param null $defaultValue
     * @param bool $noRecursive
     * @return array|mixed|null
     */
    final protected function sanitize(array $data, string $name = null, $filters = null, $defaultValue = null, $noRecursive = false)
    {
        $filter = $this->getDI()->get('filter');
        if (isset($name) && $name !== '') {
            if (isset($data[$name]) && $data[$name] !== '') {
                $data = $data[$name];
            } else {
                $data = $defaultValue;
            }
        }
        if (isset($filters)) {
            if(is_array($data)){
                foreach ($data as $k => $v) {
                    $data[$k] = is_array($v) ? $this->sanitize($data[$k], null, $filters, $defaultValue, $noRecursive) : $filter->sanitize($data[$k], $filters);
                }
            }else{
                $data = $filter->sanitize($data, $filters);
            }
        }
        return $data;
    }

    /**
     * @param $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }
}
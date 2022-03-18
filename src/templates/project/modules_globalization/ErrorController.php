<?php


namespace @@namespace@@\Modules\Base\@@name@@\Controllers;


use @@namespace@@\Core\PhalBaseController;
use @@namespace@@\Library\Response;

class ErrorController extends PhalBaseController
{

    /**
     * 404
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function notFoundAction()
    {
        return $this->returnJson(0, 'NOT_FOUND', [], Response::CODE_NOT_FOUND);
    }

    /**
     * 401
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function unauthorizedAction()
    {
        return $this->returnJson(0, 'UNAUTHORIZED', [], Response::CODE_UNAUTHORIZED);
    }

    /**
     * 403
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function forbiddenAction()
    {
        return $this->returnJson(0, 'FORBIDDEN', [], Response::CODE_FORBIDDEN);
    }

    /**
     * 400
     * @param \Throwable $e
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function exceptionAction($e)
    {
        return $this->returnJson(0, $e->getMessage(), [], Response::CODE_BAD_REQUEST);
    }

}
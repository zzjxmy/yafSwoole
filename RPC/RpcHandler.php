<?php
/**
 * Created by PhpStorm.
 * User: zhangzhijian
 * Date: 2018/6/29
 * Time: 下午2:05
 */

namespace RPC;

require_once __DIR__.'/../gen-php/RPCThrift/RPCService.php';

use RPCThrift\RPCServiceIf;

class RpcHandler implements RPCServiceIf {
    public static $_instance;
    public function __construct()
    {
        if(!self::$_instance){
            $path     = APPLICATION_PATH . "/conf/application.ini";
            self::$_instance = new \Yaf\Application($path);
        }

        return self::$_instance;
    }

    /**
     * @param string $controller
     * @param string $action
     * @param \RPCThrift\Params $params
     * @return string
     */
    public function call($controller,$action,\RPCThrift\Params $params)
    {
        try{
            \Yaf\Dispatcher::getInstance()->autoRender(false);
            $request     = new \Yaf\Request\Simple("CLI", 'Index', $controller, $action);
            ob_start();
            self::$_instance->bootstrap()->getDispatcher()->dispatch($request);
            $response = ob_get_contents();
            return $response;
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }
}
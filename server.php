<?php
/**
 * Created by PhpStorm.
 * User: zhangzhijian
 * Date: 2018/6/28
 * Time: 下午2:17
 */
require_once './vendor/autoload.php';
define('APPLICATION_PATH', dirname(__FILE__));
\RPC\RpcServer::getInstance()->start(function ($controller, $action, $_instance){
    ob_start();
    \Yaf\Dispatcher::getInstance()->autoRender(false);
    $request     = new \Yaf\Request\Simple("CLI", 'Index', $controller, $action);
    $_instance->bootstrap()->getDispatcher()->dispatch($request);
    $response = ob_get_contents();
    ob_end_clean();
    return $response;
});



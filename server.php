<?php
/**
 * Created by PhpStorm.
 * User: zhangzhijian
 * Date: 2018/6/28
 * Time: 下午2:17
 */
require_once './vendor/autoload.php';
define('APPLICATION_PATH', dirname(__FILE__));
\RPC\RpcServer::getInstance()->start();



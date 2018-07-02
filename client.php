<?php
/**
 * Created by PhpStorm.
 * User: zhangzhijian
 * Date: 2018/6/28
 * Time: 下午2:17
 */

error_reporting(E_ALL);
require_once './vendor/autoload.php';

define('APPLICATION_PATH', dirname(__FILE__));

use Thrift\Exception\TException;
try{
    $RpcClient = \RPC\RpcClientSocket::getInstance()->getClient('http://www.open-api.com');
    $result = $RpcClient->setController('index')->setAction('index')->call();
    var_dump($result);
}catch (TException $TException){
    var_dump('TException:'.$TException->getMessage().PHP_EOL);
}

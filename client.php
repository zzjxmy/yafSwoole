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

    $RpcClient = \RPC\RpcClient::getInstance()->getClient('http://www.test.com');
    $result = $RpcClient->setController('index')
        ->setAction('index')
        ->setGetParams(['name' => 'zhangzhijian'])
        ->setHeaderParams(['REQUEST_METHOD' => 'GET'])
        ->call();
//    $result2 = $RpcClient->setController('index')->setAction('indexTwo')->call();
    var_dump($result);

}catch (TException $TException){
    echo json_encode(['code' => 0 , 'msg' => '服务器连接超时']) . PHP_EOL;
}

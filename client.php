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
    $client = \RPC\RpcClient::getInstance()->getClient('http://www.open-api.com');
    $callIndexParams = new \RPCThrift\Params([
        'GET' => ['hello' => 'YES']
    ]);
    $callIndexTwoParams = new \RPCThrift\Params();
    var_dump($client->call('index','index',$callIndexParams));
    var_dump($client->call('index','indexTwo',$callIndexTwoParams));
}catch (TException $TException){
    var_dump('TException:'.$TException->getMessage().PHP_EOL);
}

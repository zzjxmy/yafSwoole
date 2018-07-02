<?php
/**
 * Created by PhpStorm.
 * User: zhangzhijian
 * Date: 2018/6/28
 * Time: 下午2:17
 */

error_reporting(E_ALL);

require_once './Thrift/ClassLoader/ThriftClassLoader.php';


use RPCThrift\RPCServiceClient;
use Thrift\ClassLoader\ThriftClassLoader;


$GEN_DIR = realpath(dirname(__FILE__)) . '/gen-php';

$loader = new ThriftClassLoader();
$loader->registerNamespace('Thrift', __DIR__);
$loader->registerNamespace('RPCThrift', $GEN_DIR);
$loader->registerDefinition('RPCThrift', $GEN_DIR);
$loader->register();


use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\THttpClient;
use Thrift\Transport\TSocket;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TFramedTransport;
use Thrift\Exception\TException;


try{
    $socket = new TSocket('127.0.0.1', 1101);
    $transport = new TFramedTransport($socket);
    $protocol = new TBinaryProtocol($transport);

    $transport->open();
    $callIndexParams = new \RPCThrift\Params([
        'GET' => ['hello' => 'YES']
    ]);
    $callIndexTwoParams = new \RPCThrift\Params();
    $client = new RPCServiceClient($protocol);
    var_dump($client->call('index','index',$callIndexParams));
    var_dump($client->call('index','indexTwo',$callIndexTwoParams));

    $transport->close();
}catch (TException $TException){
    var_dump('TException:'.$TException->getMessage().PHP_EOL);
}

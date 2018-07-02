<?php
/**
 * Created by PhpStorm.
 * User: zhangzhijian
 * Date: 2018/6/29
 * Time: 下午2:25
 */

namespace RPC;

use RPCThrift\RPCServiceClient;
use Thrift\ClassLoader\ThriftClassLoader;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TFramedTransport;
use Thrift\Transport\TSocket;
use Yaf\Exception;

class RpcClient
{
    private static $map = [];
    private static $_instance;
    public static $application;
    private static $socketMap;

    public static function getInstance()
    {
        if (!self::$_instance) {
            $GEN_DIR = APPLICATION_PATH . '/gen-php';
            $loader = new ThriftClassLoader();
            $loader->registerNamespace('Thrift', __DIR__);
            $loader->registerNamespace('RPCThrift', $GEN_DIR);
            $loader->registerDefinition('RPCThrift', $GEN_DIR);
            $loader->register();
            self::init();
            self::$_instance = new static();
        }

        return self::$_instance;
    }

    private static function init(){
        $path     = APPLICATION_PATH . "/conf/application.ini";
        self::$application = new \Yaf\Application($path);
        $otherServerHosts = self::$application->getConfig()->get('rpc.server.other');

        foreach ($otherServerHosts as $key => $value){
            list($serverName,$address) = explode('|',$value['addressMap']);
            list($host, $port) = explode(':', $address);
            self::$map[self::$application->getConfig()->get($serverName)] = ['host' => $host, 'port' => $port];
        }
    }

    public function getClient($url){
        $config = $this->getConfigMap($url);
        return $this->getSocket($config['host'], $config['port']);
    }

    public function getConfigMap($url){
        if(isset(self::$map[$url])){
            return self::$map[$url];
        }

        throw new Exception($url . ' server map is not exists');
    }

    private function getSocket($host, $port){
        $key = $host . ':' . $port;
        if(isset(self::$socketMap[$key])){
            return self::$socketMap[$key]['client'];
        }
        return $this->createSocket($host, $port);
    }

    private function createSocket($host, $port){
        $socket = new TSocket($host, $port);
        $transport = new TFramedTransport($socket);
        $protocol = new TBinaryProtocol($transport);
        $client = new RPCServiceClient($protocol);
        $transport->open();
        self::$socketMap[$host . ':' . $port] = ['client' => $client, 'transport' => $transport];
        return $client;
    }

    public function setGetParams(){
        return $this;
    }

    public function setPostParams(){
        return $this;
    }

    public function setHeader(){
        return $this;
    }

    public function __destruct()
    {
        foreach (self::$socketMap as $key => $value){
            try{
                $value['transport']->close();
            }catch (\Exception $exception){}
        }
    }
}
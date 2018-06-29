<?php
/**
 * Created by PhpStorm.
 * User: zhangzhijian
 * Date: 2018/6/28
 * Time: 下午2:17
 */

namespace RPC;

use RPCThrift\RPCServiceProcessor;
use RPC\RpcHandler;
use RPC\RpcSwooleServer;
use Thrift\ClassLoader\ThriftClassLoader;
use Thrift\Factory\TBinaryProtocolFactory;
use Thrift\Factory\TFramedTransportFactory;
use Thrift\Server\TServerSocket;
use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TFramedTransport;

class RpcServer {
    protected static $_instance;
    protected static $server;
    protected static $handler;
    public function __construct(){
        if(!self::$server instanceof RpcSwooleServer){
            $GEN_DIR = realpath(dirname(__DIR__)) . '/gen-php';
            $loader = new ThriftClassLoader();
            $loader->registerNamespace('Thrift', dirname(__DIR__). '/Thrift');
            $loader->registerNamespace('RPCThrift', $GEN_DIR);
            $loader->registerNamespace('RPC', __DIR__);
            $loader->registerDefinition('RPCThrift', $GEN_DIR);
            $loader->register();
            self::$handler = $handler = new RpcHandler();
            $processor = new RPCServiceProcessor($handler);
            $socketTransport = new TServerSocket(
                $handler::$_instance->getConfig()->get('rpc.server.host'),
                $handler::$_instance->getConfig()->get('rpc.server.port')
            );
            $outFactory = $inFactory = new TFramedTransportFactory();
            $outProtocol = $inProtocol = new TBinaryProtocolFactory();
            self::$server = new RpcSwooleServer($processor, $socketTransport, $inFactory, $outFactory, $inProtocol, $outProtocol);
        }

        self::$_instance = $this;
    }

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new static();
        }

        return self::$_instance;
    }

    public function start(){
        $handler = self::$handler;
        self::$server->start($handler::$_instance->getConfig()->get('rpc.server'));
    }
}



<?php
namespace RPC;
use RPCThrift\RPCServiceProcessor;
use Thrift;
use Thrift\Server\TNonblockingServer;

class RpcSwooleServer extends TNonblockingServer
{
    protected $processor = null;
    protected $serviceName = 'RPCService';

    public function onStart()
    {
        echo "RpcServer Start\n";
    }

    public function notice($log)
    {
        echo $log."\n";
    }

    public function onConnect($server, $fd, $reactorId)
    {
        echo $fd."\n";
    }

    /**
     * @param $server
     * @param $fd
     * @param $fromId
     * @param $data
     */
    public function onReceive($server, $fd, $fromId, $data)
    {
        var_dump(1);
        $handler = new \RPC\RpcHandler();
        $this->processor = new \RPCThrift\RPCServiceProcessor($handler);

        $socket = new Socket();
        $socket->setHandle($fd);
        $socket->buffer = $data;
        $socket->server = $server;
        $protocol = new Thrift\Protocol\TBinaryProtocol($socket, false, false);

        try {
            $protocol->fname = $this->serviceName;
            $this->processor->process($protocol, $protocol);
        } catch (\Exception $e) {
            $this->notice('CODE:' . $e->getCode() . ' MESSAGE:' . $e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }

    /**
     * @param $config
     */
    public function start($config)
    {
        $server = new \swoole_server($config['host'],$config['port']);
        $server->on('workerStart', [$this, 'onStart']);
        $server->on('receive', [$this, 'onReceive']);
        $server->on('connect', [$this, 'onConnect']);
        $swooleConfig = [];
        array_walk($config['config'], function($key, $value) use(&$swooleConfig){
            if($value == "\000*\000_config"){
                $swooleConfig = $key;
            }
        });
        $server->set($swooleConfig);
        $server->start();
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: zhangzhijian
 * Date: 2018/7/2
 * Time: 下午5:12
 */

namespace RPC;


use RPCThrift\RPCServiceClient;
use RPCThrift\Params;

class RpcClient{
    public $controller = 'index';
    public $action = 'index';
    private $getParams = [];
    private $postParams = [];
    private $headerParams = [];
    private $serviceClients;
    public function __construct(RPCServiceClient $serviceClients)
    {
        $this->serviceClients = $serviceClients;
    }

    public function call(){
        $requestParams = new Params(
            [
                'RPC_GET' => $this->getParams,
                'RPC_POST' => $this->postParams,
                'RPC_HEADER' => $this->headerParams,
            ]
        );
        return $this->serviceClients->call($this->controller, $this->action, $requestParams);
    }

    public function setGetParams($params = []){
        $this->getParams = $params;
        return $this;
    }

    public function setPostParams($params = []){
        $this->postParams = $params;
        return $this;
    }

    public function setHeaderParams($params = []){
        $this->headerParams = $params;
        return $this;
    }

    public function setController($controller){
        $this->controller = $controller;
        return $this;
    }

    public function setAction($action){
        $this->action = $action;
        return $this;
    }
}
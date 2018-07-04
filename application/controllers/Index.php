<?php

class IndexController extends Yaf\Controller_Abstract {

	public function indexAction() {
//        var_dump($_GET);
//        var_dump($_POST);
	    $this->getResponse()->setBody('i am index');
	}

    public function indexTwoAction() {
	    echo 111;
//        $this->getResponse()->setBody('i am indextwo');
    }

    public function indexThreeAction(){
        $RpcClient = \RPC\RpcTestClient::getInstance()->getClient('http://www.open-api.com');
        $result = $RpcClient->setController('index')
            ->setAction('index')
            ->setHeaderParams(['REQUEST_METHOD' => 'GET'])
            ->call();
        var_dump($result);
    }
}

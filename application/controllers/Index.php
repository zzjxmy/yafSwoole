<?php

class IndexController extends Yaf\Controller_Abstract {

	public function indexAction() {
	    var_dump($_GET);
	    var_dump($_POST);
	    $this->getResponse()->setBody('i am index');
	}

    public function indexTwoAction() {
        $this->getResponse()->setBody('i am indextwo');
    }
}

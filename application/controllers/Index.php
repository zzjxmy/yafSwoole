<?php

class IndexController extends Yaf\Controller_Abstract {

	public function indexAction() {
	    $this->getResponse()->setBody('i am index');
	}

    public function indexTwoAction() {
        $this->getResponse()->setBody('i am indextwo');
    }
}

<?php

require_once APPLICATION_PATH . '/models/Helper.php';

class ServicecenterController extends Zend_Controller_Action {
	
	public function init() {

		
	}
	public function indexAction() {
		// action body
		
	}
	public function useraccountAction() {
		$layout = $this->_helper->layout ();
		$layout->setLayout ( 'clear' );
	}
	
}
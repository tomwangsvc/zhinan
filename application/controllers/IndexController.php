<?php
class IndexController extends Zend_Controller_Action {
	public function init() {
		$this->view->current = "index";
		Zend_Session::start();
		/* Initialize action controller here */
		if (isset ( $_SESSION ['LAST_ACTIVITY'] ) && (time () - $_SESSION ['LAST_ACTIVITY'] > 1800)) {
			// last request was more than 30 minutes ago
			session_unset (); // unset $_SESSION variable for the run-time
			session_destroy (); // destroy session data in storage
		}
		$_SESSION ['LAST_ACTIVITY'] = time ();
	}
	public function indexAction() {
	}
	public function registersucessAction() {
		// action body
	}
}
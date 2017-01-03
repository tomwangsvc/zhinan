<?php
include_once ('Zend/Captcha/image.php');
include_once ('Zend/loader.php');
require_once APPLICATION_PATH . '/models/Helper.php';
class MypostController extends Zend_Controller_Action {
	public function init() {
		Zend_Session::start ();
		$layout = $this->_helper->layout ();
		$layout->setLayout ( 'mypost' );
		
		if (! isset ( $_SESSION ['user'] )) {
			$this->_redirect ( '/index/index' );
			return;
		}
		if (isset ( $_SESSION ['LAST_ACTIVITY'] ) && (time () - $_SESSION ['LAST_ACTIVITY'] > 1800)) {
			// last request was more than 30 minutes ago
			session_unset (); // unset $_SESSION variable for the run-time
			session_destroy (); // destroy session data in storage
		}
		$_SESSION ['LAST_ACTIVITY'] = time ();
	}
	public function indexAction() {
		
		$this->view->hover = 'guid';
	}
	public function displayAction() {
		$this->view->hover = 'display';
		$user_id = $_SESSION ['user'] ['id'];
		$data = array();
		$status='3';
		$jobs = new Application_Model_DbTable_Jobs ();
		$data = $jobs->getMyjobPost($data,$user_id,$status);
		$rentals = new Application_Model_DbTable_Rentals();
		$data = $rentals->getMyrentalPost($data,$user_id,$status);
		$this->view->mypost = $data;
	}
	public function showdeleteAction() {
		$this->view->hover = 'delete';
		$user_id = $_SESSION ['user'] ['id'];
		$jobs = new Application_Model_DbTable_Jobs ();
		$db = $jobs->getAdapter ();
		$status='0';
		$data = array();
		$jobs = new Application_Model_DbTable_Jobs ();
		$data = $jobs->getMyjobPost($data,$user_id,$status);
		$rentals = new Application_Model_DbTable_Rentals();
		$data = $rentals->getMyrentalPost($data,$user_id,$status);
		
		
		$this->view->mydelete = $data;
	}
	public function updateAction() {
		$this->_helper->layout->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ();
		
		$class = $this->getRequest ()->getParam ( 'classid' );
		$id = $this->getRequest ()->getParam ( 'itemid' );
		if ($class == 1) { // from job table
			$table = new Application_Model_DbTable_Jobs ();
		}		
		if ($class == 2) { // from job table
			$table = new Application_Model_DbTable_Rentals();
		}	
		if ($class == 3) { // from * table
			//
		}
		
		$db = $table->getAdapter ();
		$where = $db->quoteInto ( 'id=?', $id );
		$res = $table->fetchRow ( $where )->toArray ();
		if (date ( 'Y-m-d', $res ['updatedate'] ) == date ( 'Y-m-d', time () )) { // check if already updated today
			echo false;
			return;
		}
		$data = array (
				'updatedate' => time (),
		);
		if ($table->update ( $data, $where )) {
			$res = $table->fetchRow ( $where )->toArray ();
			echo date ( 'Y-m-d', ($res ['updatedate'] + $res ['expiredate'] * 24 * 60 * 60) ));
		}
	}
	public function deletepostAction() {
		$this->_helper->layout->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ();
		
		$class = $this->getRequest ()->getParam ( 'classid' );
		$id = $this->getRequest ()->getParam ( 'itemid' );
		if ($class == 1) { // from job table
			$table = new Application_Model_DbTable_Jobs ();
		}
		if ($class == 2) { // from job table
			$table = new Application_Model_DbTable_Rentals();
		}
				
		$db = $table->getAdapter ();
		$where = $db->quoteInto ( 'id=?', $id );
		$data = array (
				'status' => '0' ,
				'updatedate' => time()
		);
		if ($table->update ( $data, $where )) {
			echo true;
		}
	}
	public function repostAction() {
		$this->_helper->layout->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ();
		
		$class = $this->getRequest ()->getParam ( 'classid' );
		$id = $this->getRequest ()->getParam ( 'itemid' );
		if ($class == 1) { // from job table
			$table = new Application_Model_DbTable_Jobs ();
		}	
		if ($class == 2) { // from job table
			$table = new Application_Model_DbTable_Rentals();
		}
		
		$db = $table->getAdapter ();
		$where = $db->quoteInto ( 'id=?', $id );
		$res = $table->fetchRow ( $where )->toArray ();
		
		$data = array (
				'updatedate' => time (),
				'status' => '1' 
		);
		if ($table->update ( $data, $where )) {
			echo true;
		} else {
			echo false;
		}
	}
	
	public function verifyingAction() {
		$this->view->hover = 'verify';
		$user_id = $_SESSION ['user'] ['id'];
		$data = array();
		$status='1';
		$jobs = new Application_Model_DbTable_Jobs ();
		$data = $jobs->getMyjobPost($data,$user_id,$status);
		$rentals = new Application_Model_DbTable_Rentals();
		$data = $rentals->getMyrentalPost($data,$user_id,$status);
		$this->view->mypost = $data;
		
	}
	public function verifyfailAction() {
		$this->view->hover = 'verifyfail';
		$user_id = $_SESSION ['user'] ['id'];
		$data = array();
		$status='2';
		$jobs = new Application_Model_DbTable_Jobs ();
		$data = $jobs->getMyjobPost($data,$user_id,$status);
		$rentals = new Application_Model_DbTable_Rentals();
		$data = $rentals->getMyrentalPost($data,$user_id,$status);
		$this->view->mypost = $data;
	}
}
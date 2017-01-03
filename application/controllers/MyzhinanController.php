<?php
include_once ('Zend/Captcha/image.php');
include_once ('Zend/loader.php');
require_once APPLICATION_PATH . '/models/Helper.php';
class MyzhinanController extends Zend_Controller_Action {
	public function init() {
		Zend_Session::start ();
		$layout = $this->_helper->layout ();
		$layout->setLayout ( 'myzhinan' );
		$this->view->current = "index";
		
		if (isset ( $_SESSION ['LAST_ACTIVITY'] ) && (time () - $_SESSION ['LAST_ACTIVITY'] > 1800)) {
			// last request was more than 30 minutes ago
			session_unset (); // unset $_SESSION variable for the run-time
			session_destroy (); // destroy session data in storage
		}
		$_SESSION ['LAST_ACTIVITY'] = time ();
	}
	public function indexAction() {
		if (! isset ( $_SESSION ['user'] )) {
			$this->_redirect ( '/index/index' );
		}
		
	}
	public function userdataAction() {
		
		$users = new Application_Model_DbTable_Users();
		$user = $users->setAuthenSession();//set user's authen sessions
		
		$regions = new Application_Model_DbTable_Regions ();
		$regions = $regions->fetchAll ()->toArray ();
		$districts = new Application_Model_DbTable_Districts ();
		$districts = $districts->fetchAll ()->toArray ();
		$towns = new Application_Model_DbTable_Towns ();
		$towns = $towns->fetchAll ()->toArray ();
		
		$dis = array ();
		foreach ( $districts as $d ) {
			$dis [$d ['id']] = $d ['name'];
		}
		$tow = array ();
		foreach ( $towns as $t ) {
			$tow [$t ['id']] = $t ['name'];
		}
		
		// print "<pre>";
		// print_r($towns);
		// print "</pre>";
		
		// exit;
		$users = new Application_Model_DbTable_Users ();
		$db = $users->getAdapter ();
		$where = $db->quoteInto ( "id=?", $_SESSION ['user'] ['id'] );
		if ($this->getRequest ()->isPost ()) {
			$params = $this->getRequest ()->getParams ();
			unset ( $params ['controller'] );
			unset ( $params ['action'] );
			unset ( $params ['module'] );
			// 
			
			$users->update ( $params, $where );
			
			// $this->_helper->layout->disableLayout();
			// $this->_helper->viewRenderer->setNoRender();
		}
		$res = $users->fetchRow ( $where )->toArray ();
		$this->view->data = $res;
		$this->view->regions = $regions;
		$this->view->districts = $dis;
		$this->view->towns = $tow;
	}
	public function getdistrictsAction() {
		$this->_helper->layout->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ();
		$region_id = $this->getRequest ()->getParam ( 'region_id' );
		$districts = new Application_Model_DbTable_Districts ();
		$db = $districts->getAdapter ();
		$where = $db->quoteInto ( "region_id=?", $region_id );
		$res = $districts->fetchAll ( $where )->toArray ();
		
		$data = array ();
		foreach ( $res as $r ) {
			$data [$r ['id']] = $r ['name'];
		}
		
		echo json_encode ( $data );
	}
	public function gettownsAction() {
		$this->_helper->layout->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ();
		$district_id = $this->getRequest ()->getParam ( 'district_id' );
		$towns = new Application_Model_DbTable_Towns ();
		$db = $towns->getAdapter ();
		$where = $db->quoteInto ( "district_id=?", $district_id );
		$res = $towns->fetchAll ( $where )->toArray ();
		
		$data = array ();
		foreach ( $res as $r ) {
			$data [$r ['id']] = $r ['name'];
		}
		
		echo json_encode ( $data );
		
		/*
		 * $stmt = $db->query("SELECT id,name where district_id=?",array($district_id)); $res = $stmt->fetchAll(); echo json_encode($res);
		 */
	}
	// file_put_contents("e:/mylog.log",$key."-".$value."\r\n",FILE_APPEND);
	public function captchaAction() {
		// disable layout and view
		$this->_helper->layout->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ();
		
		// load Zend_Captcha_Image
		$captcha = new Zend_Captcha_Image ();
		$captcha->setExpiration ( '300' )->setWordLen ( '5' )->setDotNoiseLevel ( '5' )->setLineNoiseLevel ( '5' )->setHeight ( '35' )->setWidth ( '150' )->setFont ( APPLICATION_PATH . '/../public/fonts/ARIAL.TTF' )->setFontSize ( '20' )->setImgUrl ( APPLICATION_PATH . '/../public/images/captcha/' )->setImgDir ( APPLICATION_PATH . '/../public/images/captcha/' );
		
		$captcha->generate ();
		$captcha_session = new Zend_Session_Namespace ( 'Zend_Form_Captcha_' . $captcha->getId () );
		$captcha_iterator = $captcha_session->getIterator ();
		$captcha_word = $captcha_iterator ['word'];
		
		// keep random word in session
		//$this->_sess->captcha_confirm = $captcha_word;
		$_SESSION ['captcha_confirm'] = $captcha_word;
		//file_put_contents("e:/mylog.log",$captcha->getId ()."\r\n",FILE_APPEND);
		// response
		echo $captcha->getId ();
		
	}
}
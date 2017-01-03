<?php
define ( 'START_BALANCE', 0 );
// use application\models\AccountHelper;
require_once APPLICATION_PATH . '/models/AccountHelper.php';
include_once ('Zend/Captcha/image.php');
//include_once ('Zend/loader.php');
class AccountController extends Zend_Controller_Action {
	public function init() {
		$layout = $this->_helper->layout ();
		$layout->setLayout ( 'account_layout' );
		/* Initialize action controller here */
		Zend_Session::start ();
		
		if (isset ( $_SESSION ['LAST_ACTIVITY'] ) && (time () - $_SESSION ['LAST_ACTIVITY'] > 1800)) {
			// last request was more than 30 minutes ago
			session_unset (); // unset $_SESSION variable for the run-time
			session_destroy (); //destroy session data in storage
		}
		$_SESSION ['LAST_ACTIVITY'] = time ();
	}
	public function indexAction() {
		// action body
	}
	public function registerAction() {
		
		
		if ($this->getRequest ()->isPost ()) {
			$this->_helper->layout->disableLayout ();
			$this->_helper->viewRenderer->setNoRender ();
			
			$username = $this->getRequest ()->getParam ( 'username' );
			$email = $this->getRequest ()->getParam ( 'email' );
			$password = $this->getRequest ()->getParam ( 'password' );
			if (empty ( $username ) || empty ( $email ) || empty ( $password )) {
				echo false;
			}
			$users = new Application_Model_DbTable_Users ();
			$db = $users->getAdapter ();
			$where = $db->quoteInto ( "username =?", $username ) . $db->quoteInto ( " OR email =?", $email );
			// dauble check if there is already exit this username or email in users table
			// before create a new user account
			if ($users->fetchAll ( $where )->toArray ()) {
				echo false;
			}
			$accounthelper = new AccountHelper ();
			$password .= $accounthelper->getSalt ();
			$password = md5 ( $password );
			$data = array (
					'username' => $username,
					'email' => $email,
					'password' => $password,
					'balance' => START_BALANCE,
					'createtime'=>time(),
					'status'=>'1'
			);
			
			if ($id = $users->insert ( $data )) {
				echo true;
			} else {
				echo false;
			}
		}
	}
	public function loginAction() {
		if ($this->getRequest ()->isPost ()) {
			$this->_helper->layout->disableLayout ();
			$username = $this->getRequest ()->getParam ( 'username' );
			$password = $this->getRequest ()->getParam ( 'password' );
			$checkcode = $this->getRequest ()->getParam ( 'chekcode' );
			$autologin = $this->getRequest ()->getParam ( 'autologin' );
			if ($checkcode) {
				if ($checkcode != $_SESSION ['captcha_confirm']) {
					echo "3";
					exit ();
				}
			}
			
			if (empty ( $username ) || empty ( $password )) {
				return;
			}
			if ($autologin) { // set cookie for user's login details
				setcookie ( 'zhinuser', $username, time () + 3600 * 24 * 7, '/' );
				setcookie ( 'zhinpass', $password, time () + 3600 * 24 * 7, '/' );
			} else { // otherwise set cookie to be expired
				setcookie ( 'zhinuser', $username, time () - 10, '/' );
				setcookie ( 'zhinpass', $password, time () - 10, '/' );
			}
			$accounthelper = new AccountHelper ();
			$password .= $accounthelper->getSalt ();
			$password = md5 ( $password );
			
			$users = new Application_Model_DbTable_Users ();
			$db = $users->getAdapter ();
			$result = array ();
			$where =$db->quoteInto ( 'status>?', '0' ) ;
			if (strstr ( $username, '@' )) {
				$where .= $db->quoteInto ( ' AND email=?', $username ) . $db->quoteInto ( ' AND password=?', $password );
				$result = $users->fetchAll ( $where )->toArray ();
			} else {
				$where .= $db->quoteInto ( ' AND username=?', $username ) . $db->quoteInto ( ' AND password=?', $password );
				$result = $users->fetchAll ( $where )->toArray ();
			}
			$data = "";
			if ($result [0]) {
				$_SESSION ['user'] = array (
						'id' => $result [0] ['id'],
						'name' => $result [0] ['username'],
						'email' => $result [0] ['email'],
						'role' => $result [0] ['role']
				);
				$data = "2"; // for actived account
					
				$arr = array (
						'resetpasswordcode' => '0',
						'lastlogin' => time()
				);
				$where = $db->quoteInto ( 'id=?', $result [0] ['id'] );
				$users->update($arr, $where);
			} else {
				
				$data = "0"; // account is not exit
			}
			
			echo $data;
			exit ();
		}
	}
	public function activeAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		$activecode = $this->getRequest ()->getParam ( 'status' );
		$username = $this->getRequest ()->getParam ( 'username' );
		
		$users = new Application_Model_DbTable_Users ();
		$db = $users->getAdapter ();
		$where = $db->quoteInto ( "id =?", $id ) . $db->quoteInto ( " AND activecode=?", $activecode );
		$data = array (
				'status' => 1,
				'activecode' => '0',
				'lastlogin'=>time()
		);
		$result = $users->update ( $data, $where );
		$accounthelper = new AccountHelper ();
		$user = $accounthelper->getuserdetials ( array (
				'email' => '',
				'id' => $id 
		) );
		if ($result) {
			$_SESSION ['user'] = array (
					'id' => $id,
					'name' => $username,
					'email' => $user ['email'] 
			);
		}
		$this->_redirect ( '/account/activesuccess' );
	}
	public function checkifloginAction() {
		$this->_helper->layout->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ();
		if (isset ( $_SESSION ['user'] )) {
			echo true;
		} else {
			echo false;
		}
	}
	public function activesuccessAction() {
	}
	
	public function getpasswordAction() {
		if ($this->getRequest ()->isPost ()) {
			$this->_helper->layout->disableLayout ();
			$this->_helper->viewRenderer->setNoRender ();
			
			$email = $this->getRequest ()->getParam ( 'email' );
			$checkcode = $this->getRequest ()->getParam ( 'chekcode' );
			//file_put_contents("e:/mylog.log",$checkcode."\r\n",FILE_APPEND);
			if ($checkcode != $_SESSION ['captcha_confirm']) {
				echo "3";
				exit ();
			}
			
			$users = new Application_Model_DbTable_Users ();
			$db = $users->getAdapter ();
			$where = $db->quoteInto ( 'email=?', $email );
			$res = $users->fetchRow ( $where )->toArray ();
			$resetpasswordcode = md5(rand ( 100000, 999999 ));
			$data = array (
					'resetpasswordcode' => $resetpasswordcode 
			);
			$users->update ( $data, $where );
			
			$subject = "指南网用户密码重置通知";
			$sender = "指南网帐户管理中心";
			$html = "<div style='font-size:17px;'>" . "<p><b>尊敬的用户，</b></p>" 
					. "<p>您好！</p><p> 请点击下面的链接重置一个新的密码" 
					. "<p><a href='http://" 
					. $_SERVER ['HTTP_HOST'] . "/account/resetpassword?email=" . $email 
					. "&resetcode=" . $resetpasswordcode . "'>" . "<b>点击这里进入重置密码页面</b></a></p><br>" 
					. "指南网帐户管理中心</div>";
			$accounthelper = new AccountHelper ();
			$accounthelper->SendRregisterSuccessEmail ( $res ['username'], $res ['email'], $sender, $subject, $html );
			
			echo true;
		}
	}
	public function resetpasswordAction() {
		if ($this->getRequest ()->isPost ()) {
			
			$this->_helper->layout->disableLayout ();
			$this->_helper->viewRenderer->setNoRender ();
			
			$email = $this->getRequest ()->getParam ( 'email' );
			$resetpasswordcode = $this->getRequest ()->getParam ( 'resetcode' );
			$password = $this->getRequest ()->getParam ( 'password' );
			
			$this->_helper->layout->disableLayout ();
			$this->_helper->viewRenderer->setNoRender ();
			
			$checkcode = $this->getRequest ()->getParam ( 'checkcode' );
			// 
			if ($checkcode != $_SESSION ['captcha_confirm']) {
				echo "3";
				exit ();
			}
			$users = new Application_Model_DbTable_Users ();
			$db = $users->getAdapter ();
			
			$accounthelper = new AccountHelper ();
			$password .= $accounthelper->getSalt ();
			$password = md5 ( $password );
			
			$data = array (
					'password' => $password,
					'resetpasswordcode' => '0' 
			);
			$where = $db->quoteInto ( 'resetpasswordcode=?', $resetpasswordcode ) . $db->quoteInto ( ' AND email=?', $email );
			
			// check if resetcode is expired firstly
			if (! $users->fetchRow ( $where )) {
				echo '2';
				exit ();
			}	
			//check if update success and return true, otherwise return false
			if ($users->update ( $data, $where )) {
				$user = $accounthelper->getuserdetials ( array (
						'email' => $email,
						'id' => ""
				) );
				
				$_SESSION ['user'] = array (
						'id' => $user ['id'],
						'name' => $user ['username'],
						'email' => $user ['email'],
						'role' => $user ['role']
				);
				echo '1';
			} else {
				echo '0';
			}
		} else {
			$email = $this->getRequest ()->getParam ( 'email' );
			$resetcode = $this->getRequest ()->getParam ( 'resetcode' );
			$data = array (
					'email' => $email,
					'resetcode' => $resetcode 
			);
			$this->view->data = $data;
		}
	}
	public function changepasswordAction() {
		if (! isset ( $_SESSION ['user'] )) {
			return;
		}
		if ($this->getRequest ()->isPost ()) {
			$password = $this->getRequest ()->getParam ( 'password' );
			$accounthelper = new AccountHelper ();
			$password .= $accounthelper->getSalt ();
			$password = md5 ( $password );
			
			$user = new Application_Model_DbTable_Users ();
			$db = $user->getAdapter ();
			$where = $db->quoteInto ( 'id=?', $_SESSION ['user'] ['id'] );
			$data = array (
					'password' => $password 
			);
			$res = $user->update ( $data, $where );
			$data = "";
			if ($res) {
				$data = true;
			} else {
				$data = false;
			}
			$this->_helper->layout->disableLayout ();
			echo $data;
			exit ();
		}
	}
	
	/**
	 * create a new user account
	 */
	public function createAction() {
	}
	public function logoutAction() {
		Zend_Session::destroy ();
		$this->_redirect ( "/index/index" );
	}
	public function checkusernameAction() {
		$result = "";
		if ($this->getRequest ()->isPost ()) {
			$username = $this->getRequest ()->getParam ( 'username' );
			$users = new Application_Model_DbTable_Users ();
			$db = $users->getAdapter ();
			$where = $db->quoteInto ( "username=?", $username );
			// file_put_contents("e:/mylog.log",$username."\r\n",FILE_APPEND);
			$result = $users->fetchRow ( $where );
		}
		$this->_helper->layout->disableLayout ();
		$data = "";
		if ($result) {
			$data = "1";
		} else {
			$data = "0";
		}
		echo $data;
		exit ();
	}
	public function checkemailAction() {
		$result = "";
		if ($this->getRequest ()->isPost ()) {
			$email = $this->getRequest ()->getParam ( 'email' );
			$users = new Application_Model_DbTable_Users ();
			$db = $users->getAdapter ();
			$where = $db->quoteInto ( "email=?", $email );
			$result = $users->fetchRow ( $where );
		}
		$this->_helper->layout->disableLayout ();
		$data = "";
		if ($result) {
			$data = "1";
		} else {
			$data = "0";
		}
		echo $data;
		exit ();
	}
	public function checkpasswordAction() {
		if ($this->getRequest ()->isPost ()) {
			$password = $this->getRequest ()->getParam ( 'password' );
			$users = new Application_Model_DbTable_Users ();
			$db = $users->getAdapter ();
			$where = $db->quoteInto ( "id=?", $_SESSION ['user'] ['id'] );
			$result = $users->fetchRow ( $where )->toArray ();
			
			$accounthelper = new AccountHelper ();
			$password .= $accounthelper->getSalt ();
			$password = md5 ( $password );
			$data = "";
			if ($result ['password'] == $password) {
				$data = true;
			} else {
				$data = false;
			}
			$this->_helper->layout->disableLayout ();
			echo $data;
			exit ();
		}
		exit ();
	}
}
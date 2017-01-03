<?php
require_once APPLICATION_PATH . '/models/AccountHelper.php';
class AuthenticationController extends Zend_Controller_Action {
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
		$users = new Application_Model_DbTable_Users();
		$user = $users->setAuthenSession();//set user's authen sessions
	}
	public function realnameAction() {

	}
	/**
	 * get email and authencod from request
	 * and update user's db according to email and authencode
	 */
	public function emailauthenAction(){
		//$layout = $this->_helper->layout ();
		//$layout->setLayout ( 'myzhinan' );
		$email = $this->getRequest()->getParam('email');
		$authencode = $this->getRequest()->getParam('authencode');
		$user = new Application_Model_DbTable_Users();
		$db = $user->getAdapter();
		$where = $db->quoteInto('email=?', $email).$db->quoteInto(' AND authencode=?', $authencode);
		$data = array('emailauthen'=>'1');
		if($res = $user->update($data, $where)){
			$this->_redirect('authentication/emailauthensuccess');
		}else{
			$this->_redirect('authentication/emailauthenfail');
		}
	}
	
	
	public function emailauthensuccessAction(){
		$layout = $this->_helper->layout ();
		$layout->setLayout ( 'clear' );
	}
	public function emailauthenfailAction(){
		$layout = $this->_helper->layout ();
		$layout->setLayout ( 'clear' );
	}
	/**
	 * set an authen code to logined user's db and
	 * send this code to user via email
	 */
	public function emailauthenrequestAction(){
		
		$this->_helper->layout->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ();
		$id=$_SESSION['user']['id'];
		$email=$_SESSION['user']['email'];
		$name = $_SESSION['user']['name'];
		$authencode=md5(time());
		$users = new Application_Model_DbTable_Users();
		$db=$users->getAdapter();
		$data =array('authencode'=>$authencode);
		$where=$db->quoteInto('id=?', $id);
		if($res = $users->update($data, $where)){
			
			$subject = "指南网邮箱认证通知";
			$sender = "指南网帐户管理";
			$html = "<div style='font-size:17px;'>" . "<p><b>尊敬的用户，</b></p>"
					. "<div style='margin-left:30px;'><p>您好！</p>"
					."<p>请点击下面的链接继续认证:"
					. "<p><a href='http://"
					. $_SERVER ['HTTP_HOST'] . "/authentication/emailauthen?email=" . $email
					. "&authencode=" . $authencode . "'>" 
					. "<b>点击这里进入邮箱认证页面</b></a></p></div><br>"
					. "指南网帐户管理中心</div>";
			$accounthelper = new AccountHelper ();
			if($accounthelper->SendRregisterSuccessEmail ( $name, $email, $sender, $subject, $html )){
				echo true;
			}else{

				echo false;
			}	
		}else{
			echo false;
		}
		
	}
}
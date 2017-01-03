<?php
class AccountHelper {
	
	/**
	 * send email
	 * 
	 * @param unknown $receivername        	
	 * @param unknown $receiveremail        	
	 * @param unknown $sender        	
	 * @param unknown $subject        	
	 * @param unknown $html        	
	 */
	public function SendRregisterSuccessEmail($receivername, $receiveremail, $sender, $subject, $html) {
	
		$configs = Zend_Controller_Front::getInstance ()->getParam ( 'bootstrap' );
		$con = $configs->getOption ( 'mail' );
		$config = array (
				'auth' => 'login',
				'username' => $con ['transport'] ['username'],
				'password' => $con ['transport'] ['password'] 
		);
		// file_put_contents("e:/mylog.log",$config['username']."-".$password."\r\n",FILE_APPEND);
		$transport = new Zend_Mail_Transport_Smtp ( $con ['transport'] ['host'], $config );
		$mail = new Zend_Mail ( "utf-8" );
		//file_put_contents("e:/mylog.log",$receiveremail."\r\n",FILE_APPEND);
		$mail->setFrom ( $config ['username'], $sender );
		$mail->addTo ( $receiveremail, $receivername );
		$mail->setSubject ( $subject );
		$mail->setBodyHtml ( $html );
		//file_put_contents("e:/mylog.log",$receiveremail."\r\n",FILE_APPEND);
		$mail->send ( $transport );
		return true;
	}
	
	public function getuserdetials($arr) {
		$users = new Application_Model_DbTable_Users ();
		$db = $users->getAdapter ();
		
		$where = "";
		if ($arr ['id']) {
			$where = $db->quoteInto ( 'id=?', $arr ['id'] );
		}
		if ($arr ['email']) {
			$where = $db->quoteInto ( 'email=?', $arr ['email'] );
		}
		$res = $users->fetchRow ( $where )->toArray ();
		return $res;
	}
	public function getusername($arr) {
		$users = new Application_Model_DbTable_Users ();
		$db = $users->getAdapter ();
		$where = "";
		if ($arr ['id']) {
			$where = $db->quoteInto ( 'id=?', $arr ['id'] );
		}
		if ($arr ['email']) {
			$where = $db->quoteInto ( 'email=?', $arr ['email'] );
		}
		$res = $users->fetchRow ( $where )->toArray ();
		return $res ['username'];
	}
	public function getSalt() {
		$configs = Zend_Controller_Front::getInstance ()->getParam ( 'bootstrap' );
		return $configs->getOption ( 'account' )['password']['salt'];
	}
	public function generatecaptcha() {
		// disable layout and view
		// load Zend_Captcha_Image
		$captcha = new Zend_Captcha_Image ();
		$captcha->setExpiration ( '300' )->setWordLen ( '5' )->setDotNoiseLevel ( '10' )->setLineNoiseLevel ( '4' )->setHeight ( '35' )->setWidth ( '120' )->setFont ( APPLICATION_PATH . '/../public/fonts/ARIAL.TTF' )->setFontSize ( '20' )->setImgUrl ( APPLICATION_PATH . '/../public/images/captcha/' )->setImgDir ( APPLICATION_PATH . '/../public/images/captcha/' );
		
		$captcha->generate ();
		$captcha_session = new Zend_Session_Namespace ( 'Zend_Form_Captcha_' . $captcha->getId () );
		$captcha_iterator = $captcha_session->getIterator ();
		$captcha_word = $captcha_iterator ['word'];
		// file_put_contents("e:/mylog.log",$captcha_word."\r\n",FILE_APPEND);
		// keep random word in session
		$this->_sess->captcha_confirm = $captcha_word;
		// response
		return $captcha->getId ();
	}
}
<?php
class Application_Model_DbTable_Users extends Zend_Db_Table_Abstract {
	protected $_name = 'customers';
	
	public function setAuthenSession(){
		$db=$this->getAdapter();
		$where = $db->quoteInto('id=?', $_SESSION['user']['id']);
		if($res = $this->fetchRow($where)){
			$user = $res->toArray();
			$_SESSION['authen']=array(
				'email'=>$user['emailauthen'],
				'name'=>$user['nameauthen']
			);
		}
		
	}
}
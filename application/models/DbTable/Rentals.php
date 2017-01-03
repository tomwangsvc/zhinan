<?php
class Application_Model_DbTable_Rentals extends Zend_Db_Table_Abstract {
	protected $_name = 'rentals';
	
	
	
	public function getMyrentalPost($data,$user_id,$sataus){
	
		$db = $this->getAdapter ();
		$res = $db->query ( 'select id, class, title, expiredate, updatedate from rentals where status =? and poster_id=?
			    			order by class ASC, (updatedate+expiredate*24*60*60) ASC', array ($sataus,$user_id) )->fetchAll ();
		$helper = new Helper ();
		if($res){
			foreach ( $res as $temp ) {
				$temp ['classname'] = $helper->classConvertor ( $temp ['class'] );
				$temp ['time'] = date ( 'Y-m-d', ($temp ['updatedate'] + $temp ['expiredate'] * 24 * 60 * 60) );
				$data [] = $temp;
			}
		}
		return $data;
	
	}
}
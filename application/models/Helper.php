<?php
class Helper {
	public function getDistrictByid($id) {
		$district = new Application_Model_DbTable_Districts ();
		$db = $district->getAdapter ();
		$where = $db->quoteInto ( 'id=?', $id );
		$res = $district->fetchRow ( $where );
		return $res ['name'];
	}
	public function getRegionByid($id) {
		$regions = new Application_Model_DbTable_Regions ();
		$db = $regions->getAdapter ();
		$where = $db->quoteInto ( 'id=?', $id );
		$res = $regions->fetchRow ( $where );
		return $res ['name'];
	}
	public function getUsernameByid($id) {
		$users = new Application_Model_DbTable_Users ();
		$db = $users->getAdapter ();
		$where = $db->quoteInto ( 'id=?', $id );
		$res = $users->fetchRow ( $where )->toArray ();
		return $res ['username'];
	}
	public function getCategoryByid($id) {
		$category = new Application_Model_DbTable_Jobcategories ();
		$db = $category->getAdapter ();
		$where = $db->quoteInto ( 'category_id=?', $id );
		$res = $category->fetchRow ( $where )->toArray ();
		return $res ['name'];
	}
	public function getsubCategoryByid($id) {
		$subcategory = new Application_Model_DbTable_Jobsubcategories ();
		$db = $subcategory->getAdapter ();
		$where = $db->quoteInto ( 'subcategory_id=?', $id );
		$res = $subcategory->fetchRow ( $where )->toArray ();
		return $res ['name'];
	}
	public function convertJobtoShow($res) {
		if ($res ['pay_type'] == '1') {
			$res ['pay'] = "$" . number_format ( $res ['minimum_pay'] ) . "-" . number_format ( $res ['maximum_pay'] ) . "K/年";
		}
		if ($res ['pay_type'] == '2') {
			
			$res ['pay'] = "$" . $res ['minimum_pay'] . "-" . $res ['maximum_pay'] . "/小时";
		}
		if ($res ['job_age'] == 0) {
			$res ['job_age'] = "不限";
		}
		if ($res ['job_age'] == 1) {
			$res ['job_age'] = "毕业生/实习生";
		}
		if ($res ['job_age'] == 2) {
			$res ['job_age'] = "1到2年";
		}
		if ($res ['job_age'] == 3) {
			$res ['job_age'] = "2到4年";
		}
		if ($res ['job_age'] == 4) {
			$res ['job_age'] = "4年以上";
		}
		$res ['regron'] = $this->getRegionByid ( $res ['regron'] );
		$res ['district'] = $this->getDistrictByid ( $res ['district'] );
		$res ['category'] = $this->getCategoryByid ( $res ['category_id'] );
		
		if ($res ['qualification'] == 0) {
			$res ['qualification'] = '不限';
		}
		if ($res ['qualification'] == 1) {
			$res ['qualification'] = '高中/中专';
		}
		if ($res ['qualification'] == 2) {
			$res ['qualification'] = '大学/本科';
		}
		if ($res ['qualification'] == 3) {
			$res ['qualification'] = '研究生以上';
		}
		if ($res ['gender'] == 0) {
			$res ['gender'] = "不限";
		}
		if ($res ['gender'] == 1) {
			$res ['gender'] = "男";
		}
		if ($res ['gender'] == 2) {
			$res ['gender'] = "女";
		}
		$res ['postdate'] = date ( 'Y-m-d', $res ['postdate'] );
		
		return $res;
	}
	/**
	 * get jobs's details for job display
	 * 
	 * @param unknown $arr        	
	 * @return multitype:multitype:unknown Ambigous <string, unknown> string NULL
	 */
	public function getJobDetails($arr) {
		$data = array ();
		foreach ( $arr as $r ) {
			$temp = array ();
			$temp ['id'] = $r ['id'];
			$temp ['numofpeople'] = $r ['numofpeople'];
			$temp ['job_type'] = ($r ['job_type'] == 1) ? '全职' : (($r ['job_type'] == 2) ? '兼职' : "合同工");
			$temp ['title'] = $r ['title'];
			$temp ['comname'] = $r ['comname'] ? $r ['comname'] : $this->getUsernameByid ( $r ['poster_id'] ) . "<span class='personal'>（个人）</span>";
			$temp ['region'] = $this->getDistrictByid ( $r ['district'] );
			$temp ['time'] = (date ( 'Ymd', $r ['updatedate'] ) == date ( 'Ymd' )) ? "今天" : ((date ( 'Ymd', $r ['updatedate'] ) == (date ( 'Ymd' ) - 1)) ? "昨天" : date ( 'm月d日', $r ['updatedate'] ));
			$temp ['domain'] = ($r ['domain']) ? $r ['domain'] : "#";
			$temp ['category_id'] = $r ['category_id'];
			$temp ['subcategory_id'] = $r ['subcategory_id'];
			$temp ['collected'] = false;
			if (isset ( $_SESSION ['user'] )) {
				if ($this->checkIfJobcollected ( $r ['id'], $_SESSION ['user'] ['id'] )) {
					$temp ['collected'] = true;
				}
			}
			$data [] = $temp;
		}
		return $data;
	}
	
	/**
	 * check if the logined user collect this job
	 * 
	 * @param unknown $jobid        	
	 * @param unknown $userid        	
	 * @return boolean
	 */
	public function checkIfJobcollected($jobid, $userid) {
		$jobscollection = new Application_Model_DbTable_Jobscollection ();
		$db = $jobscollection->getAdapter ();
		$where = $db->quoteInto ( 'job_id=?', $jobid ) . $db->quoteInto ( ' AND user_id=?', $userid );
		if ($jobscollection->fetchRow ( $where )) {
			return true;
		} else {
			return false;
		}
	}
	public function classConvertor($index) {
		if ($index == 1) {
			return '招聘';
		}
		if ($index == 2) {
			return '出租';
		}
		if ($index == 3) {
			return '住宿';
		}
		if ($index == 4) {
			return '房产';
		}
		if ($index == 5) {
			return '生意';
		}
		if ($index == 6) {
			return '拼车';
		}
	}
}
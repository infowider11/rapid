<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Api_model extends CI_Model {

	public function __construct() {
		parent::__construct();
		$this->db->query("SET sql_mode = '';");
	}

	public function insertData($table,$insertdata)
	{
		$this->db->insert($table,$insertdata);
		return $this->db->insert_id(); 
	}
	
	public function security_filter($data) {
		$arrays=array();
		foreach($data as $key=>$values) {
			
			$_POST[$key] = $values;
			$arrays[$key]=$this->input->post($key);
		}
		return $arrays;
	}
	
	public function highlight_word( $content, $word) {
			$replace = '<span class="highlight_show">' . $word . '</span>'; 
			$content = str_replace( $word, $replace, $content ); 
			return $content; 
	}
		
	public function converToTz($time="",$user_id){  
		
		$fromTz='Asia/Dubai';
		
		$this->db->select('time_zone');
		$this->db->where('id', $user_id);
    $obj = $this->db->get('users');
		$row = $obj->row_array();
		
		$toTz = $row['time_zone'];
		// timezone by php friendly values
		$date = new DateTime($time, new DateTimeZone($fromTz));
		$date->setTimezone(new DateTimeZone($toTz));
		$time= $date->format('Y-m-d H:i:s');
		return $time;
	}
	
	public function check_manage_group_admin($user_id,$group_id) {
		
		$this->db->select('id');
		$this->db->where("FIND_IN_SET($user_id,group_admins) != 0 and id='".$group_id."'");
    $obj = $this->db->get('groups_tbl');
		$row = $obj->num_rows();
		
		if($row > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public function update_global_status($data) {
		if(empty($data['user_id']) || empty($data['feild'])) {
			$output['success'] = "0";
			$output['msg'] = "Please check parameter";
			return $output;
		} else {
			$feilds=$data['feild'];
			$user_id=$data['user_id'];
			$status=0;
			if(isset($data['status'])) {
				$status=$data['status'];
			}
			
			$this->db->select('id');
			$this->db->where('user_id', $user_id);
			$obj = $this->db->get('user_global_status');
			
			$row = $obj->num_rows();
			
			if($row==0){
				$this->db->insert('user_global_status',array('user_id'=>$user_id,$feilds=>$status));
			} else {
				$this->db->where('user_id',$user_id);
				$this->db->update('user_global_status',array($feilds=>$status));
			}
			
			$output['success'] = "1";
			$output['msg'] = "Global Status Updated!";
			return $output;
		}
	}
}
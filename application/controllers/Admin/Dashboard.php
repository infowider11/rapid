<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 */
class Dashboard extends CI_Controller
{
	
	public function __construct() {
		parent::__construct();
		$this->check_login();
		
	}

	public function check_login(){
		if(!$this->session->userdata('admin_id')){
			redirect('admin');
		}
	}			

	public function index(){
		$data["userData"] = $this->common_model->GetAllData('users');
		$data["userBlocked"] = $this->common_model->GetAllData('users', array('status' => 0));
		$data["userUnblocked"] = $this->common_model->GetAllData('users',array('status' => 1));
		$this->load->view('admin/dashboard',$data);
	}
	
	
	
}

?>
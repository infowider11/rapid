<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);
class Languages extends CI_Controller
{
	public function __construct() {
		parent::__construct();
		$this->check_login();
		
	}
	public function check_login(){
		if(!$this->session->userdata('admin_id')){
			redirect('Admin/login');
		}
	}
	public function index(){

		echo 'Here..';die;
		$data["userData"] = $this->common_model->GetAllData('users','','id','desc');
		$this->load->view('admin/language-list', $data);
	}
	

}
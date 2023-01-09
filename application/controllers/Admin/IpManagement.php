<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
/*
*/
/*
*/
error_reporting(0);
class IpManagement extends CI_Controller
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
		$data["data"] = $this->common_model->GetAllData('whitelisted_ip','','id','desc');
		$this->load->view('admin/white-list',$data);
	}

	public function black_list($value='')
	{
		$data["data"] = $this->common_model->GetAllData('blacklisted_ip','','id','desc');
		$this->load->view('admin/black-list',$data);
	}


	public function DeleteIp()
	{
		$id = $_GET["id"];
		$page = $_GET["page"];

		$run = $this->common_model->DeleteData('whitelisted_ip', array('id'=>$id));

		if ($run) {
			$this->session->set_flashdata('msgs','<div class="alert alert-success">Success! Deleted IP.</div>');
		} else {
			$this->session->set_flashdata('msgs','<div class="alert alert-danger">Something went Wrong.</div>');
		}

		redirect('admin/'.$page.'');
	}

	public function AddIP()
	{
		$this->form_validation->set_rules('ip','ip','trim|required');
		//$this->form_validation->set_rules('message','message','trim|required');
		if($this->form_validation->run()){
			$insert["ip"] = $ip = $this->input->post('ip');
			$insert["message"] = $this->input->post('message');

			$run = $this->common_model->GetSingleData('whitelisted_ip', array('ip'=>$ip));

			if ($run) {
					
				$run1 = $this->common_model->UpdateData('whitelisted_ip', array('id'=>$run["id"]), $insert);

			} else {
				$run1 = $this->common_model->InsertData('whitelisted_ip',$insert);
			}

							if ($run1) {
								$output['status'] = 1;
								$this->session->set_flashdata('msgs','<div class="alert alert-success">IP has been added successfully.</div>');
							} else {
								$output['status'] = 0;
								$output['message'] = '<div class="alert alert-danger">Something went Wrong!</div>';	

							}
				}//end if form validation
		else {
			$output['status'] = 0;
			$output['message'] = '<div class="alert alert-danger">'.validation_errors().'</div>';
		}//end else form validation
		echo json_encode($output);
	}

	
	public function AddBlackIP($value='')
	{
		$this->form_validation->set_rules('ip','ip','trim|required');
		//$this->form_validation->set_rules('message','message','trim|required');
		if($this->form_validation->run()){
			$insert["ip"] = $ip = $this->input->post('ip');
			$insert["message"] = $this->input->post('message');

			$run = $this->common_model->GetSingleData('blacklisted_ip', array('ip'=>$ip));

			if ($run) {
					
				$run1 = $this->common_model->UpdateData('blacklisted_ip', array('id'=>$run["id"]), $insert);

			} else {
				$run1 = $this->common_model->InsertData('blacklisted_ip',$insert);
			}

							if ($run1) {
								$output['status'] = 1;
								$this->session->set_flashdata('msgs','<div class="alert alert-success">IP has been added successfully.</div>');
							} else {
								$output['status'] = 0;
								$output['message'] = '<div class="alert alert-danger">Something went Wrong!</div>';	

							}
				}//end if form validation
		else {
			$output['status'] = 0;
			$output['message'] = '<div class="alert alert-danger">'.validation_errors().'</div>';
		}//end else form validation
		echo json_encode($output);
	}


}?>
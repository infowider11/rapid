<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Auth extends CI_Controller {
  public function __construct() {
    parent::__construct();
    $this->db->query("set sql_mode = ''");
		$this->check_login();
  }
	public function check_login(){
		if($this->session->userdata('agent_id')){
			redirect('home');
		}
	}
	public function login(){
		$this->load->view('agent/index');
	}

	public function forget_password(){
		$this->load->view('agent/forgot');
	}
	
	public function forget_password_form() {
		$this->form_validation->set_rules("email", "Email address", "required|valid_email");
		if ($this->form_validation->run()==false) {
			$json['status'] = 0;
			$json['msg'] = '<div class="alert alert-danger">'.validation_errors().'</div>';
		} else {
			$u_email = $this->input->post("email");
			$data = $this->common_model->GetSingleData('users',array('email'=>$u_email));
			
			if($data) {
				if($data['status']==1){
					$json['status'] = 1;
					
					$u_name = $data['nickname'];

					$subject = Project.": Forget Password!"; 
					$contant= 'Hello, '.$u_name.' <br><br>';
					$contant .='<p class="text-center">This is an automated message . If you did not recently initiate the Forgot Password process, please disregard this email.<p>';

					$contant .='<p class="text-center">You indicated that you forgot your login password. We can generate a temporary password for you to log in with, then once logged in you can change your password to anything you like.</p>';
	 
					$contant .='<p class="text-center">Password: <b>'.$data['password'].'</b></p>';
						
					$run = $this->common_model->SendMail($data['email'],$subject,$contant);
					
					$json['status'] = ($run) ? 1 : 0;
					
					$json['msg'] = '<div class="alert alert-danger">Something went wrong!</div>';
				} else {
					$json['status'] = 3;
				}
			} else {
				$json['status'] = 2;
			}
		}
		echo json_encode($json);
	}	
	
	
	public function do_login(){

		$this->form_validation->set_rules('email','Email','required|valid_email');
		$this->form_validation->set_rules('password','Password','required');

		if($this->form_validation->run()==true){

			$email = $this->input->post('email');
			$password = $this->input->post('password');

			$data = $this->common_model->GetSingleData('users',"password = '".$password."' and (email = '".$email."' or phone = '".$email."')");




			if($data){

				if($data['user_type']==2){
			$run = $this->common_model->GetSingleData('users',"user_type = 2 and password = '".$password."' and (email = '".$email."' or phone = '".$email."')");
			}else{
			$run = $this->common_model->GetSingleData('users',"user_type = 3 and password = '".$password."' and (email = '".$email."' or phone = '".$email."')");
			}


				 if($data['status'] == 1 && $data['isAgentBlock'] == 1) {
					$this->session->set_userdata('agent_id',$data['id']);
					$this->session->set_userdata('uniqueID',$data['uniqueID']);
					$this->session->set_userdata('email',$data['email']);
				   $this->session->set_userdata('nickname',$data['nickname']);
					 $this->session->set_userdata('user_type',$data['user_type']);
				   redirect('agent/dashboard');
				} else 
				{
					$this->session->set_flashdata('message','<div class="alert alert-danger">Your account has been blocked temporary! For more info please contact to our support.</div>');
					redirect('agent/login');
				}
				
				
			} else {
				$this->session->set_flashdata('message','<div class="alert alert-danger">Incorrect email or password.</div>');
				redirect('agent/login');	
			}
		} else {
			$this->session->set_flashdata('message','<div class="alert alert-danger">'.validation_errors().'</div>');
			redirect('agent/login');
		}
	}



}
	
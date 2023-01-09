<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Login extends CI_Controller {
  public function __construct() {
    parent::__construct();
		$this->check_login();
    $this->db->query("set sql_mode = ''");
  }
	
	public function check_login(){
		if($this->session->userdata('admin_id')){
			redirect('admin/dashboard');
		}
	}
	
	public function index(){
		$this->load->view('admin/login');
	}

	public function do_login(){
	  
		$this->form_validation->set_rules('email','Email','required');
		$this->form_validation->set_rules('password','Password','required');

		if($this->form_validation->run()==true){



			$email = $this->input->post('email');
			$password = $this->input->post('password');
          // echo "hffffffg";die;
          
			$run = $this->common_model->GetSingleData('admin',array('email'=>$email,'password'=>$password));
		
		
			if($run){

				$this->session->set_userdata('admin_id',$run['id']);

				$this->session->set_flashdata('msg','<div class="alert alert-success">Welcome '.$run['name'].'</div>');
                 //echo $this->session->userdata('admin_id');
				redirect('Admin/dashboard');
			} else {
				$this->session->set_flashdata('msg','<div class="alert alert-danger">Email or Password is incorrect.</div>');
				redirect('Admin/login');
			}

			
		} else {
			$this->session->set_flashdata('msg','<div class="alert alert-danger">'.validation_errors().'</div>');
			redirect('Admin/login');
		}


	}

	public function forget_password(){
		//https://www.bluediamondresearch.com/WEB01/MMXX/Api/forget_password?email=reshmi.webwiders@gmail.com
		
		$this->form_validation->set_rules('email','email','required');
		
		if($this->form_validation->run()){
			
			$email = $this->input->post('email');
			
			
		    $run = $this->common_model->GetColumnName('adminuser',array('admin_email' =>$email),array('admin_pass','admin_email','admin_name','id'));
			
			if($run){
				
				$email = $run['admin_email'];
				$name = $run['admin_name'];
				$id = $run['id'];
				$subject = "Forget password";
				
				$html = '<p>Hello, '.$run['admin_name'].'</p>';
				$html .= '<p>This is an automated message . If you did not recently initiate the Forgot Password process, please disregard this email.</p>';
				$html .= '<p>You indicated that you forgot your login password. We can generate a temporary password for you to log in with, then once logged in you can change your password to anything you like.</p>';
				$html .= '<p>Password: <b>'.$run['admin_pass'].'</b></p>';
				
				$this->common_model->SendMail($run['admin_email'],$subject,$html);
				
				$output['status'] = 1;
				$output['message'] = 'Please check your mail , We have sent your password in your registered mail id.';
				
			} else {
				$output['status'] = 0;
				$output['message'] = 'Email address that you have entered is not registered with us.';
			}
			
 		} else {
			$output['status'] = 0;
			$output['message'] = validation_errors();
		}
		echo json_encode($output);
	}
	
	

}
	
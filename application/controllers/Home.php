<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Home extends CI_Controller {
  public function __construct() {
    parent::__construct();
    $this->db->query("set sql_mode = ''");

    //redirect('Admin');
  }
	
	
	public function index(){
		$data['title'] = 'Home';
		$this->load->view('site/index',$data);
	}
	
	public function test(){
		$this->load->view('site/email');
	}
	public function logout(){ 
		session_destroy();
		redirect('');
	}

	
	private function generateRandomString($length = 10) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwzyz9876543210';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return strtoupper($randomString);
	}

	

	public function generateRandomString11($length = 9) {
			    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ9876543210';
			    $charactersLength = strlen($characters);
			    $randomString = '';
			    for ($i = 0; $i < $length; $i++) {
			        $randomString .= $characters[rand(0, $charactersLength - 1)];
			    }
			    return $randomString;
	}

	public function verify_otp()
	{
		$this->load->view('site/verify-otp');
	}

	public function sentCodeEmail()
	{
			$this->form_validation->set_rules('Email','Email','trim|required|is_unique[users.email]',
        array('is_unique' => 'Email already exist'));
			if ($this->form_validation->run()) {
		$insert["user_type"] = 2;
		$insert["invited_by"] = $invited_by = $this->input->post('invited_by');
		$token= $insert['token'] = rand(100000,999999);
		$Email = $insert['email'] = $this->input->post('Email');
		$insert['referal_code'] = $this->generateRandomString11($length = 9);
		$insert["created_at"]= date('Y-m-d H:i:s');
		$uniqueID = $this->GetUniqueUserID();
		$insert['uniqueID'] = $uniqueID;
		$run = $this->common_model->InsertData('users', $insert);
			if ($run) {
				$user = $this->common_model->GetSingleData('users', array('id' => $run));
				$this->session->set_userdata('User_id',$run);
				$this->session->set_userdata('User_email',$user["email"]);

				$output['status'] = 1;

				$output['message'] = '<div class="alert alert-danger">OTP has been sent to your email address</div>';
				/*$message = $token." is your one time password.\r\n Please enter the OTP to proceed.\r\n Thank you,\r\n Team Rapid";
				$this->common_model->SendMail($Email,'Verification Mail',$message);*/
				$email_template = $this->common_model->GetDataById('email_template',3);
						$email_body = $email_template['text'];				
						$CODE = $token;
				$email_body = str_replace("[CODE]",$CODE,$email_body);
				$email_body1 = str_replace("assets/admin/EmailImg/",site_url()."assets/admin/EmailImg/",$email_body);
				$this->common_model->SendMailCustom($Email,'Verification Mail',$email_body1);
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

	public function sentAgainCode()
	{
		$email = $this->session->userdata('User_email');

		$run = $this->common_model->GetSingleData('users', array('email' => $email ));

		if ($run) {
			$output['status'] = 1;

				$output['message'] = '<div class="alert alert-success">Otp send again to your email id.</div>';
			/*$message = $run["token"]." is your one time password.\r\n Please enter the OTP to proceed.\r\n Thank you,\r\n Team Rapid";
				$this->common_model->SendMail($email,'Verification Mail',$message);*/

				$email_template = $this->common_model->GetDataById('email_template',2);
						$email_body = $email_template['text'];				
						$CODE = $run["token"];
				$email_body = str_replace("[CODE]",$CODE,$email_body);
				$email_body1 = str_replace("assets/admin/EmailImg/",site_url()."assets/admin/EmailImg/",$email_body);
				$this->common_model->SendMailCustom($email,'Verification Mail',$email_body1);

		}
		else {
			$output['status'] = 0;

				$output['message'] = '<div class="alert alert-danger">Something went wrong!!</div>';
		}
		echo json_encode($output);

	}

	public function sign_up_form()
	{

		$this->load->view('site/sign-up-form');
	}

	public function sign_up()
	{
		$refferalCode = $this->uri->segment(3);
		$data["User"]= $this->common_model->GetSingleData('users', array('referal_code' => $refferalCode));
		$this->load->view('site/sign-up', $data);
	}

	public function sign_user_action()
	{
		$this->form_validation->set_rules('nickname','your name','trim|required');
		//$this->form_validation->set_rules('email','email','trim|required|is_unique[users.email]');
		$this->form_validation->set_rules('country','country ','trim|required');

		$this->form_validation->set_rules('phone','phone','trim|required|is_unique[users.phone]');
		$this->form_validation->set_rules('password','password ','trim|required|matches[cpassword]');
		$this->form_validation->set_rules('cpassword','cpassword ','trim|required');

		if($this->form_validation->run()){
			$UserId = $this->input->post('UserId');
			$insert["nickname"] = $this->input->post('nickname');
			$insert["country"] = $contryId = $this->input->post('country');
			$insert["phone"] = $phone = $this->input->post('phone');
			$insert["password"] = $this->input->post('password');

			$countryCode = $this->common_model->GetSingleData('country',array('id'=> $contryId));
			$insert['phone_with_code'] = $countryCode["phonecode"]."".$phone;
			
			$run = $this->common_model->UpdateData('users', array('id' => $UserId), $insert);

			if ($run) {
				$output['status'] = 1;
				$this->session->set_flashdata('msgs','<div class="alert alert-success">Success, Signup. Enter your email id & password to login</div>');

				//$message = '<p>Success, Signup</p>';
				//$message = $token." is your one time password.\r\n Please enter the OTP to proceed.";
				//$this->common_model->SendMail($email,'Success, Signup',$message);
			}

		}
		else {
			$output['status'] = 0;
			$output['message'] = '<div class="alert alert-danger">'.validation_errors().'</div>';
		}//end else form validation
		echo json_encode($output);
	}
	public function sign_up_hostess()
	{
		$refferalCode = $this->uri->segment(3);
		$data["User"]= $this->common_model->GetSingleData('users', array('referal_code' => $refferalCode));
		$this->load->view('site/signup-hostess', $data);
	}

	public function sentCodeEmailHostess()
	{
			$this->form_validation->set_rules('Email','Email','trim|required|is_unique[users.email]',
        array('is_unique' => 'Email already exist'));
			if ($this->form_validation->run()) {
		$insert["user_type"] = 1;
		$insert["invited_by"] = $invited_by = $this->input->post('invited_by');
		$token= $insert['token'] = rand(100000,999999);
		$Email = $insert['email'] = $this->input->post('Email');
		$insert['referal_code'] = $this->generateRandomString11($length = 9);
		$insert["created_at"]= date('Y-m-d H:i:s');
		$uniqueID = $this->GetUniqueUserID();
		$insert['uniqueID'] = $uniqueID;
		$run = $this->common_model->InsertData('users', $insert);
			if ($run) {
				$user = $this->common_model->GetSingleData('users', array('id' => $run));
				$this->session->set_userdata('User_id',$run);
				$this->session->set_userdata('User_email',$user["email"]);

				$output['status'] = 1;

				$output['message'] = '<div class="alert alert-danger">OTP has been sent to your email address.</div>';
				/*$message = $token." is your one time password.\r\n Please enter the OTP to proceed.\r\n Thank you,\r\n Team Rapid";
				$this->common_model->SendMail($Email,'Verification Mail',$message);*/

				$email_template = $this->common_model->GetDataById('email_template',3);
						$email_body = $email_template['text'];				
						$CODE = $token;
				$email_body = str_replace("[CODE]",$CODE,$email_body);
				$email_body1 = str_replace("assets/admin/EmailImg/",site_url()."assets/admin/EmailImg/",$email_body);
				$this->common_model->SendMailCustom($Email,'Verification Mail',$email_body1);

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

	public function sentAgainCodeHostess()
	{
		$email = $this->session->userdata('User_email');

		$run = $this->common_model->GetSingleData('users', array('email' => $email ));

		if ($run) {
			$output['status'] = 1;

				$output['message'] = '<div class="alert alert-success">Otp send again to your email id.</div>';
			/*$message = $run["token"]." is your one time password.\r\n Please enter the OTP to proceed.\r\n Thank you,\r\n Team Rapid";
				$this->common_model->SendMail($email,'Verification Mail',$message);*/
				$email_template = $this->common_model->GetDataById('email_template',2);
						$email_body = $email_template['text'];				
						$CODE = $run["token"];
				$email_body = str_replace("[CODE]",$CODE,$email_body);
				$email_body1 = str_replace("assets/admin/EmailImg/",site_url()."assets/admin/EmailImg/",$email_body);
				$this->common_model->SendMailCustom($email,'Verification Mail',$email_body1);
		}
		else {
			$output['status'] = 0;

				$output['message'] = '<div class="alert alert-danger">Something went wrong!!</div>';
		}
		echo json_encode($output);

	}
	public function hostess_success_signup()
	{
		$this->load->view('site/success-signup');
	}
	public function privacy_policy()
	{
		$this->load->view('site/privacy-policy');
	}

	private function GetUniqueUserID(){
		$uniqueID = rand(100000,999999);
		$check = $this->common_model->GetColumnName('users',array('uniqueID'=>$uniqueID),array('id'));
		if($check){
			$this->GetUniqueUserID();
		} else {
			return $uniqueID;
		}
	}

	public function sign_up_action()
	{
		$this->form_validation->set_rules('email','email','trim|required');
		$this->form_validation->set_rules('token','token','trim|required');

		if($this->form_validation->run()){

			$Email = $this->input->post('email');
			$token = $this->input->post('token');

			$run = $this->common_model->GetSingleData('users', array('email'=>$Email , 'token' => $token));

					if ($run ) {
						$output['status'] = 1;
						$output['message'] = '<div class="alert alert-danger">Success!!</div>';
					}
					else 
					{
						$output['status'] = 0;
						$output['message'] = '<div class="alert alert-danger">Wrong OTP.</div>';
					}
			}
		else {
			$output['status'] = 0;
			$output['message'] = '<div class="alert alert-danger">'.validation_errors().'</div>';
		}//end else form validation
		echo json_encode($output);
	}
	
}
	
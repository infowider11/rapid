<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
/*
*/
/*
*/
error_reporting(0);
class Agent extends CI_Controller
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
		$data["data"] = $this->common_model->GetAllData('users', array('user_type' => 2, 'invited_by = '=>0), 'id','desc');
		$data["content"] = "Agent";
		$data["countryList"]= $this->common_model->GetAllData('country','', 'nicename', 'ASC');
		$this->load->view('admin/agent-list', $data);
	}

	public function SubAgent(){
		$data["data"] = $this->common_model->GetAllData('users', array('user_type' => 2,'invited_by != '=>0), 'id','desc');
		$data["content"] = "Sub-agent";
		$data["countryList"]= $this->common_model->GetAllData('country','', 'nicename', 'ASC');
		$this->load->view('admin/agent-list', $data);
	}

	public function AddAgent()
	{
		$this->form_validation->set_rules('nickname','nick name','trim|required');
		$this->form_validation->set_rules('email','email','trim|required|is_unique[users.email]');
		$this->form_validation->set_rules('phone','phone','trim|required|is_unique[users.phone]');
		$this->form_validation->set_rules('dob','date of birth','trim|required');
		$this->form_validation->set_rules('gender','gender','trim|required');
		$this->form_validation->set_rules('country','country','trim|required');
		$this->form_validation->set_rules('city','city','trim|required');
		$this->form_validation->set_rules('cpass','Confirm password','trim|required|matches[password]');
		$this->form_validation->set_rules('password','password','trim|required');

		if($this->form_validation->run()){

			$insert['user_type'] = 2;
			$insert['nickname'] = $nickname =  $this->input->post('nickname');
			$insert['email'] = $email = $this->input->post('email');
			$insert['phone'] = $phone = $this->input->post('phone');
			$insert['dob'] = $this->input->post('dob');
			$insert['gender'] = $this->input->post('gender');
			$insert['country'] = $contryId = $this->input->post('country');
			$insert['city'] = $this->input->post('city');
			$countryCode = $this->common_model->GetSingleData('country',array('id'=> $contryId));
			$insert['phone_with_code'] = $countryCode["phonecode"]."".$phone;
			
			$insert['created_at'] = date('Y-m-d H:i:s');
			$insert['updated_at'] = date('Y-m-d H:i:s');

			$insert['status'] = 1;
			$insert['is_verified'] = 1;
			$insert['referal_code'] = $this->generateRandomString();
			$insert['password'] = $password = $this->input->post('password');
			
			$uniqueID = $this->GetUniqueUserID();
			$insert['uniqueID'] = $uniqueID;

			$run = $this->common_model->InsertData('users',$insert);
							if($run){
							
							$output['status'] = 1;
							$this->session->set_flashdata('msgs','<div class="alert alert-success">Agent has been added successfully.</div>');

							//$u_name = $nickname;

					$subject = "Account Created as agent"; 
					
					/*$contant= 'Hello, '.$nickname.' <br><br>';
					$contant .='<p class="text-center">This is an automated message. Donot reply this mail !!<p>';

					$contant .='<p class="text-center">Your account has been created as agent !! Kindly <a href="'.site_url().'agent/" target="_blank">click here</a> to login.</p>';
					$contant .='<p class="text-center">Your registered email id is: '.$email.' and password is : '.$password.'. Kindly change your password after first login.</p>';
	 
					//$contant .='<p class="text-center">Password: <b>'.$data['password'].'</b></p>';
						
					$run = $this->common_model->SendMail($email,$subject,$contant);*/
					$message = $this->common_model->GetSingleData('email_template', array('id'=>4));
					$email_body = str_replace("[username]",$nickname,$message["text"]);
					$email_body1 = str_replace("[email]",$email,$email_body);
					$email_body2 = str_replace("[password]",$password,$email_body1);

					$link = "<a class='btn btn-primary' href='".site_url('agent')."' target='_blank'>click here</a>";
					
					$email_body3 = str_replace("[login_link]",$link,$email_body2);
					$email_body4 = str_replace("assets/admin/EmailImg/",site_url()."assets/admin/EmailImg/",$email_body3);
					$contant ='<p class="text-center">'.$email_body4.'</p>';
	 
					//$contant .='<p class="text-center">Password: <b>'.$data['password'].'</b></p>';
						
					$run = $this->common_model->SendMailCustom($email,$subject,$contant);

							}
							else {
						$output['status'] = 0;
						$output['message'] = $this->session->set_flashdata('msgs','<div class="alert alert-danger">Something went Wrong!</div>');	
							}



		}
		else {
			$output['status'] = 0;
			$output['message'] = '<div class="alert alert-danger">'.validation_errors().'</div>';
		}//end else form validation
		echo json_encode($output);
	}

	public function EditAgent()
	{
		$this->form_validation->set_rules('nickname','nick name','trim|required');
		$this->form_validation->set_rules('email','email','trim|required');
		$this->form_validation->set_rules('phone','phone','trim|required');
		$this->form_validation->set_rules('dob','date of birth','trim|required');
		$this->form_validation->set_rules('gender','gender','trim|required');
		$this->form_validation->set_rules('country','country','trim|required');
		$this->form_validation->set_rules('city','city','trim|required');

		if($this->form_validation->run()){

			//$insert['user_type'] = 2;
			$Id = $this->input->post('UserId');

			$insert['nickname'] = $nickname =  $this->input->post('nickname');
			$insert['email'] = $email = $this->input->post('email');
			$insert['phone'] = $phone = $this->input->post('phone');
			$insert['dob'] = $this->input->post('dob');
			$insert['gender'] = $this->input->post('gender');
			$insert['country'] = $contryId = $this->input->post('country');
			$insert['city'] = $this->input->post('city');
			$countryCode = $this->common_model->GetSingleData('country',array('id'=> $contryId));
			$insert['phone_with_code'] = $countryCode["phonecode"]."".$phone;
			
			//$insert['created_at'] = date('Y-m-d H:i:s');
			$insert['updated_at'] = date('Y-m-d H:i:s');

			//$insert['status'] = 1;
			//$insert['is_verified'] = 1;
			//$insert['referal_code'] = rand(10,100).time();
			$alreadyEmail= $this->common_model->GetSingleData('users', array('email' => $email, 'id !=' => $Id));

			$alreadyPhone= $this->common_model->GetSingleData('users', array('phone' => $phone, 'id !=' => $Id));

							if($alreadyEmail){
							$output['status'] = 0;
							$output['message'] = '<div class="alert alert-danger">Email already exist.</div>';				
					

							} elseif ($alreadyPhone) {
							$output['status'] = 0;
							$output['message'] = '<div class="alert alert-danger">Phone already exist.</div>';
							} elseif ($this->common_model->UpdateData('users',array('id' => $Id), $insert)) {
							$output['status'] = 1;
							$this->session->set_flashdata('msgs','<div class="alert alert-success">Agent has been updated successfully.</div>');
							}
							else {
						$output['status'] = 0;
						$output['message'] = $this->session->set_flashdata('msgs','<div class="alert alert-danger">Something went Wrong!</div>');	
							}



		}
		else {
			$output['status'] = 0;
			$output['message'] = '<div class="alert alert-danger">'.validation_errors().'</div>';
		}//end else form validation
		echo json_encode($output);
	}

	public function delete_Users()
	{
		$Id = $_GET["UserId"];

		$run = $this->common_model->DeleteData('users',array('id'=>$Id));
            //echo $this->db->last_query();
			if($run){

				$this->session->set_flashdata('msgs','<div class="alert alert-success">Success! Agent has been deleted successfully.</div>');
				
			} else {
			    $this->session->set_flashdata('msgs','<div class="alert alert-danger">Something is Worng.</div>');
			}

			redirect('admin/agent');
	}

	public function generateRandomString($length = 9) {
			    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ9876543210';
			    $charactersLength = strlen($characters);
			    $randomString = '';
			    for ($i = 0; $i < $length; $i++) {
			        $randomString .= $characters[rand(0, $charactersLength - 1)];
			    }
			    return $randomString;
	}

	public function agent_comission()
	{
		$data["data"] = $this->common_model->GetAllData('agent_comission','','id','desc');
		$this->load->view('admin/agent-comission', $data);
	}

	public function AddAgentComission()
	{
		$this->form_validation->set_rules('amount','amount','trim|required');
		$this->form_validation->set_rules('percent','percent','trim|required');
		if($this->form_validation->run()){
		$insert["percent"]= $this->input->post('percent');
		$insert["amount"]= $this->input->post('amount');

		$run = $this->common_model->InsertData('agent_comission', $insert);
				if ($run) {
					$output['status'] = 1;
				$this->session->set_flashdata('msgs','<div class="alert alert-success">Success! Agent comission has been added successfully.</div>');
					
				} else {
					$output['status'] = 0;
					$output['message'] = '<div class="alert alert-danger">Something went Wrong!</div>';

				}

		}
		else {
			$output['status'] = 0;
			$output['message'] = '<div class="alert alert-danger">'.validation_errors().'</div>';
		}//end else form validation
		echo json_encode($output);
	}
	public function EditAgentComission()
	{
		$this->form_validation->set_rules('amount','amount','trim|required');
		$this->form_validation->set_rules('percent','percent','trim|required');
		if($this->form_validation->run()){
		$Id= $this->input->post('Id');	
		$insert["percent"]= $this->input->post('percent');
		$insert["amount"]= $this->input->post('amount');

		$run = $this->common_model->UpdateData('agent_comission', array('id' => $Id), $insert);
				if ($run) {
					$output['status'] = 1;
				$this->session->set_flashdata('msgs','<div class="alert alert-success">Success! Agent comission has been updated successfully.</div>');
					
				} else {
					$output['status'] = 0;
					$output['message'] = '<div class="alert alert-danger">Something went Wrong!</div>';

				}

		}
		else {
			$output['status'] = 0;
			$output['message'] = '<div class="alert alert-danger">'.validation_errors().'</div>';
		}//end else form validation
		echo json_encode($output);
	}

	public function delete_agent_Com()
	{
		$Id = $_GET["Id"];

		$run = $this->common_model->DeleteData('agent_comission',array('id'=>$Id));
            //echo $this->db->last_query();
			if($run){

				$this->session->set_flashdata('msgs','<div class="alert alert-success">Success! Agent comission has been deleted successfully.</div>');
				
			} else {
			    $this->session->set_flashdata('msgs','<div class="alert alert-danger">Something is Worng.</div>');
			}

			redirect('admin/agent-comission');
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

	public function BlockUser(){
		//print_r($this->input->post()); die;
		$this->form_validation->set_rules('reason','reason','trim|required');

		if($this->form_validation->run()){ 
		$uniqueID = $this->input->post('uniqueID');
		$reason = $this->input->post('reason');
		$update['isAgentBlock'] =0;
        $run = $this->common_model->UpdateData('users', array('uniqueID'=>$uniqueID),$update);
		        if($run)
		        {
		        	$chlid = $this->common_model->GetAllData('users', array('invited_by'=>$uniqueID));	
				foreach ($chlid as $key => $value) {
					$subject = "Agent Blocked"; 
					/*$contant= 'Hello, '.$value["nickname"].' <br><br>';
					$contant .='<p class="text-center">This is an automated message. Donot reply this mail !!<p>';

					$contant .='<p class="text-center">Your agent has been blocked because '.$reason.'</p>';			

					//$contant .='<p class="text-center">Password: <b>'.$data['password'].'</b></p>';
						
					$run = $this->common_model->SendMail($value["email"],$subject,$contant);*/

					$message = $this->common_model->GetSingleData('email_template', array('id'=>6));
			$email_body = str_replace("[username]",$value["nickname"],$message["text"]);
			$email_body1 = str_replace("[messageByAdmin]",$reason,$email_body);
			$email_body2 = str_replace("assets/admin/EmailImg/",site_url()."assets/admin/EmailImg/",$email_body1);
			$contant ='<p class="text-center">'.$email_body2.'</p>';
				
			$this->common_model->SendMailCustom($value["email"],$subject,$contant);
				}
				
		        	$update1["invited_by"] = 0;

		        	$this->common_model->UpdateData('users', array('invited_by'=>$uniqueID),$update1);

		        	if($status==1)
		        	{
		               $active = "activated";
		        	}
		        	else
		        	{
		               $active = "blocked";
		        	}
					$this->session->set_flashdata('msgs','<div class="alert alert-success">Success! User has been '.$active.' successfully.</div>');
				
				} else
				{
				    // $this->session->set_flashdata('msgs','<div class="alert alert-danger">Something is Worng.</div>');
				    $this->session->set_flashdata('msgs','Something went worng.</div>');
				}
		}//end if form validation
		else {
			$this->session->set_flashdata('msgs','<div class="alert alert-danger">'.validation_errors().'</div>');
		}//end else form validation
		
		redirect('admin/agent');


	}

	public function export_csv(){ 
		/* file name */
		$filename = 'agent_'.date('Ymd').'.csv'; 
		header("Content-Description: File Transfer"); 
		header("Content-Disposition: attachment; filename=$filename"); 
		header("Content-Type: application/csv; ");
	   /* get data */
	   	$sql = 'SELECT id, uniqueID, nickname, email, phone, dob, gender, country, city, status, is_verified FROM users WHERE user_type=2 ORDER BY id DESC';
	      $result = $this->db->query($sql);
	       $result->num_rows();
	   	$usersData = $result->result_array(); 

		//print_r($usersData); die;
		/* file creation */
		$file = fopen('php://output', 'w');
		$header = array("ID","uniqueID","User Name","Email", "Phone","Date of birth","Gender","country", "city","status", "verified"); 
		fputcsv($file, $header);
		foreach ($usersData as $key=>$line){
		$country = $this->common_model->GetSingleData('country',array('id'=>$line['country'])); 
		$line["country"] = $country["nicename"];
			fputcsv($file,$line); 
		}
		fclose($file); 
		exit; 
	}

}

?>
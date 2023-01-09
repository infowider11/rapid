<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User extends CI_Controller {
  public function __construct() {
    parent::__construct();
    $this->db->query("set sql_mode = ''");
		$this->check_login();
  }
	
	public function check_login(){
		if(!$this->session->userdata('agent_id')){
			redirect('agent/login');
		}
	}
	public function logout(){
		session_destroy();
		redirect('agent/login');
	}

	public function dashboard()
	{
		//$data=array();
		$admin_id = $this->session->userdata('agent_id');
		$where = "invited_by='".$admin_id."' AND user_type='1' " ;
		$data["hostess"] = $hostess = $this->common_model->GetAllData('users',$where);
		$result = array();
		foreach($hostess as $value){
			$result[] = $value["id"];
		}
		$hostess1 = implode(",", $result);
		$where1 = "invited_by='".$admin_id."' AND user_type='3' " ;
		$data["subAgents"] = $subAgents = $this->common_model->GetAllData('users',$where1);
		$result1 = array();
		foreach($subAgents as $value1){
			$result1[] = $value1["id"];
		}
		$subAgents1 = implode(",", $result1);
		
		if(!empty($subAgents1))
		{
		$sql = 'SELECT SUM(`total_call_sec`) as total FROM call_records WHERE status="1" AND call_to IN ('.$subAgents1.')';
		//echo $sql; die;
    $result2 = $this->db->query($sql);
    $result2->num_rows();
    $data["subAgentsCall"] = $result2->result_array();

    $sql2 = 'SELECT SUM(`amount`) as total FROM agent_transaction WHERE source="Call" AND user_id IN ('.$subAgents1.')';
		//echo $sql2; die;
    $result3 = $this->db->query($sql2);
    $result3->num_rows();
    $data["SubagentCallEarn"] = $result3->result_array();

    $sql4 = 'SELECT SUM(`amount`) as total FROM agent_transaction WHERE source="Gift" AND user_id IN ('.$subAgents1.')';
		//echo $sql2; die;
    $result5 = $this->db->query($sql4);
    $result5->num_rows();
    $data["SubagentGiftEarn"] = $result5->result_array();

  	} else {
  		$data["subAgentsCall"]= array();
  		$data["SubagentCallEarn"] = array();
  		$data["SubagentGiftEarn"] = array();
  	}

    if (!empty($hostess1)) {
    $sql1 = 'SELECT SUM(`total_call_sec`) as total FROM call_records WHERE status="1" AND call_to IN ('.$hostess1.')';
    //echo $sql1 ; die;
    $result3 = $this->db->query($sql1);
    $result3->num_rows();
    $data["hostessCall"] = $result3->result_array(); 

    $sql3 = 'SELECT SUM(`amount`) as total FROM coin_transaction WHERE source="Call" AND user_id IN ('.$hostess1.')';
		//echo $sql3; die;
    $result4 = $this->db->query($sql3);
    $result4->num_rows();
    $data["HostessCallEarn"] = $result4->result_array();	

    $sql5 = 'SELECT SUM(`amount`) as total FROM coin_transaction WHERE source="Gift" AND user_id IN ('.$hostess1.')';
		//echo $sql2; die;
    $result6 = $this->db->query($sql5);
    $result6->num_rows();
    $data["HostessGiftEarn"] = $result6->result_array();	
    } else {
    	$data["hostessCall"] = array();
    	$data["HostessCallEarn"] = array();
    	$data["HostessGiftEarn"] = array();
    }


    $data["user"] = $this->common_model->GetSingleData('users', array('id'=>$admin_id));
		$this->load->view('agent/dashboard', $data);
	}
	public function edit_profile()
	{
		$userId= $this->session->userdata('agent_id');
		$data["data"] = $this->common_model->GetSingleData('users', array('id' => $userId));
		$data["countryList"]= $this->common_model->GetAllData('country','', 'nicename', 'ASC');
		$this->load->view('agent/profile', $data);
	}

	public function EditAgent(){
		$this->form_validation->set_rules('nickname','nick name','trim|required');
		//$this->form_validation->set_rules('phone','phone','trim|required');
		$this->form_validation->set_rules('dob','date of birth','trim|required');
		$this->form_validation->set_rules('gender','gender','trim|required');
		$this->form_validation->set_rules('country','country','trim|required');
		$this->form_validation->set_rules('city','city','trim|required');

		if($this->form_validation->run()){

			//$insert['user_type'] = 2;
			$Id = $this->session->userdata('agent_id');

			$insert['nickname'] = $nickname =  $this->input->post('nickname');
			$phone = $this->input->post('phone');
			$insert['dob'] = $this->input->post('dob');
			$insert['gender'] = $this->input->post('gender');
			$insert['country'] = $contryId = $this->input->post('country');
			$insert['city'] = $this->input->post('city');
			$insert['introduction'] = $this->input->post('introduction');
			$countryCode = $this->common_model->GetSingleData('country',array('id'=> $contryId));
			$insert['phone_with_code'] = $countryCode["phonecode"]."".$phone;
			
			//$insert['created_at'] = date('Y-m-d H:i:s');
			$insert['updated_at'] = date('Y-m-d H:i:s');
			$check = true;
			if($_FILES['image']['name']){

                    $config['upload_path']="assets/admin/agentImg";
                    $config['allowed_types'] = 'jpeg|gif|jpg|png';
                    $config['encrypt_name']=true;
                    $this->load->library("upload",$config);
                    if ($this->upload->do_upload('image')) {
                    $u_profile=$this->upload->data("file_name");
                    $insert['image'] = $u_profile;


                    } else {
                    $check = false;
                $output['status'] = 0;
                $output['message'] = '<div class="alert alert-danger">'.$this->upload->display_errors().'</div>';   
                    }
                    }

						if ($check) {
							$run = $this->common_model->UpdateData('users',array('id' => $Id), $insert);
							if ($run) {
								$output['status'] = 1;
							$this->session->set_flashdata('msg','<div class="alert alert-success">Your profile has been updated successfully.</div>');
							} 
							else{

							$output['status'] = 0;
							$output['message'] = '<div class="alert alert-danger">Something went Wrong!</div>';	

							}
							
							}						

		}
		else {
			$output['status'] = 0;
			$output['message'] = '<div class="alert alert-danger">'.validation_errors().'</div>';
		}//end else form validation
		echo json_encode($output);
	}
	
	public function change_password(){

 		$admin_id = $this->session->userdata('agent_id');
		$admindata= $this->common_model->GetSingleData('users',array('id'=>$admin_id));

 		$this->form_validation->set_rules('admin_password','Current password','required');
 		$this->form_validation->set_rules('New_Password','New password','required|min_length[6]');
 		$this->form_validation->set_rules('Confirm_Password','Confirm password','required|matches[New_Password]');

 		if($this->form_validation->run()==true){

			$admin_pass = $this->input->post('admin_password');
			$New_Password = $this->input->post('New_Password');
			$Confirm_Password = $this->input->post('Confirm_Password');

 			if($admindata['password'] == $admin_pass){

 				$run = $this->common_model->UpdateData('users',array('id' =>$admin_id),array('password' =>$New_Password));

 				if($run){

					$this->session->set_flashdata('msg','<div class="alert alert-success">Success! Your password has been updated successfully.</div>');

				}else {
					$this->session->set_flashdata('msg','<div class="alert alert-danger">Something went wrong.</div>');

				}
 			}
 			elseif($New_Password != $Confirm_Password) {
					$this->session->set_flashdata('msg','<div class="alert alert-danger">New password & confirm password not matched.</div>');

				}

 			else{
 				$this->session->set_flashdata('msg','<div class="alert alert-danger">Current password does not match.</div>');

 			}
 		}
 		else{
 			$this->session->set_flashdata('msg','<div class="alert alert-danger">'.validation_errors().'</div>');
 		}
 		redirect('agent/edit-profile'); 
 	}



	public function star_list(){
		$this->load->view('agent/star-list');
	}

	public function hostess_ist()
	{
		$uniqueID = $this->session->userdata('uniqueID');
		$where = "invited_by='".$uniqueID."' AND user_type='1' " ;

		if(isset($_REQUEST['start_date']) && !empty($_REQUEST['start_date'])){
			$where .=" and DATE(created_at) >= DATE('".$_REQUEST['start_date']."')";	
		}
		
		if(isset($_REQUEST['end_date']) && !empty($_REQUEST['end_date'])){
			$where .=" and DATE(created_at) <= DATE('".$_REQUEST['end_date']."')";	
		}

		$data["data"] = $this->common_model->GetAllData('users',$where, 'id','desc');
		$data["countryList"]= $this->common_model->GetAllData('country','', 'nicename', 'ASC');
		$data["language"]= $this->common_model->GetAllData('language','','name', 'ASC');
		$this->load->view('agent/hostess-list',$data);
	}


	public function sub_agent(){
		$uniqueID = $this->session->userdata('uniqueID');

		$where = "invited_by='".$uniqueID."' AND user_type='3' " ;

		if(isset($_REQUEST['start_date']) && !empty($_REQUEST['start_date'])){
			$where .=" and DATE(created_at) >= DATE('".$_REQUEST['start_date']."')";	
		}
		
		if(isset($_REQUEST['end_date']) && !empty($_REQUEST['end_date'])){
			$where .=" and DATE(created_at) <= DATE('".$_REQUEST['end_date']."')";	
		}

		$data["data"] = $this->common_model->GetAllData('users', $where, 'id','desc');
		$data["countryList"]= $this->common_model->GetAllData('country','', 'nicename', 'ASC');
		$this->load->view('agent/sub-agent',$data);
	}


	public function AddSubAgent()
	{
		$admin_id = $this->session->userdata('agent_id');

		$this->form_validation->set_rules('nickname','nick name','trim|required');
		$this->form_validation->set_rules('email','email','trim|required|is_unique[users.email]');
		$this->form_validation->set_rules('phone','phone','trim|required|is_unique[users.phone]');
		$this->form_validation->set_rules('dob','date of birth','trim|required');
		$this->form_validation->set_rules('gender','gender','trim|required');
		$this->form_validation->set_rules('country','country','trim|required');
		$this->form_validation->set_rules('city','city','trim|required');

		if($this->form_validation->run()){

			$insert['user_type'] = 3;
			$insert['nickname'] = $nickname =  $this->input->post('nickname');
			$insert['email'] = $email = $this->input->post('email');
			$insert['phone'] = $phone = $this->input->post('phone');
			$insert['dob'] = $this->input->post('dob');
			$insert['invited_by'] = $admin_id;
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
			$insert['password'] = $password = time();

			$run = $this->common_model->InsertData('users',$insert);
							if($run){
							
							$output['status'] = 1;
							$this->session->set_flashdata('msgs','<div class="alert alert-success">Sub Agent has been added successfully.</div>');

							//$u_name = $nickname;

					$subject = "Account Created as sub agent"; 
					$contant= 'Hello, '.$nickname.' <br><br>';
					$contant .='<p class="text-center">This is an automated message. Donot reply this mail !!<p>';

					$contant .='<p class="text-center">Your account has been created as sub agent !! Kindly <a href="'.site_url().'agent/" target="_blank">click here</a> to login.</p>';
					$contant .='<p class="text-center">Your registered email id is: '.$email.' and password is : '.$password.'. Kindly change your password after first login.</p>';
	 
					//$contant .='<p class="text-center">Password: <b>'.$data['password'].'</b></p>';
						
					$run = $this->common_model->SendMail($email,$subject,$contant);

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

	public function EditSubAgent()
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
							$this->session->set_flashdata('msgs','<div class="alert alert-success">Sub Agent has been updated successfully.</div>');
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

				$this->session->set_flashdata('msgs','<div class="alert alert-success">Success! Sub Agent has been deleted successfully.</div>');
				
			} else {
			    $this->session->set_flashdata('msgs','<div class="alert alert-danger">Something is Worng.</div>');
			}

			redirect('agent/sub-agent');
	}

	public function delete_hostess()
	{
		$Id = $_GET["UserId"];

		$run = $this->common_model->DeleteData('users',array('id'=>$Id));
            //echo $this->db->last_query();
			if($run){

				$this->session->set_flashdata('msgs','<div class="alert alert-success">Success! Hostess has been deleted successfully.</div>');
				
			} else {
			    $this->session->set_flashdata('msgs','<div class="alert alert-danger">Something is Worng.</div>');
			}

			redirect('agent/hostess-list');
	}

	public function AddHostess()
	{
		$admin_id = $this->session->userdata('agent_id');

		$this->form_validation->set_rules('nickname','nick name','trim|required');
		$this->form_validation->set_rules('email','email','trim|required|is_unique[users.email]');
		$this->form_validation->set_rules('phone','phone','trim|required|is_unique[users.phone]');
		$this->form_validation->set_rules('dob','date of birth','trim|required');
		$this->form_validation->set_rules('gender','gender','trim|required');
		$this->form_validation->set_rules('country','country','trim|required');
		$this->form_validation->set_rules('city','city','trim|required');

		if($this->form_validation->run()){

			$insert['user_type'] = 1;
			$insert['nickname'] = $nickname =  $this->input->post('nickname');
			$insert['email'] = $email = $this->input->post('email');
			$insert['phone'] = $phone = $this->input->post('phone');
			$insert['dob'] = $this->input->post('dob');
			$insert['invited_by'] = $admin_id;
			$insert['gender'] = $this->input->post('gender');
			$insert['country'] = $contryId = $this->input->post('country');
			$insert['city'] = $this->input->post('city');
			$countryCode = $this->common_model->GetSingleData('country',array('id'=> $contryId));
			$insert['phone_with_code'] = $countryCode["phonecode"]."".$phone;

			$insert['lng1'] = $this->input->post('lng1');
			$insert['lng2'] = $this->input->post('lng2');
			
			$insert['created_at'] = date('Y-m-d H:i:s');
			$insert['updated_at'] = date('Y-m-d H:i:s');

			$insert['status'] = 1;
			$insert['is_verified'] = 1;
			$insert['referal_code'] = $this->generateRandomString();
			$insert['password'] = $password = time();

			$run = $this->common_model->InsertData('users',$insert);
							if($run){
							
							$output['status'] = 1;
							$this->session->set_flashdata('msgs','<div class="alert alert-success">Hostess has been added successfully.</div>');

							//$u_name = $nickname;

					$subject = "Account Created as hostess"; 
					$contant= 'Hello, '.$nickname.' <br><br>';
					$contant .='<p class="text-center">This is an automated message. Donot reply this mail !!<p>';

					$contant .='<p class="text-center">Your account has been created as hostess !!</p>';
					//$contant .='<p class="text-center">Your registered email id is: '.$email.' and password is : '.$password.'. Kindly change your password after first login.</p>';
	 
					//$contant .='<p class="text-center">Password: <b>'.$data['password'].'</b></p>';
						
					$run = $this->common_model->SendMail($email,$subject,$contant);

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

	public function EditHostess()
	{
		//$admin_id = $this->session->userdata('agent_id');

		$this->form_validation->set_rules('nickname','nick name','trim|required');
		$this->form_validation->set_rules('email','email','trim|required');
		$this->form_validation->set_rules('phone','phone','trim|required');
		$this->form_validation->set_rules('dob','date of birth','trim|required');
		$this->form_validation->set_rules('gender','gender','trim|required');
		$this->form_validation->set_rules('country','country','trim|required');
		$this->form_validation->set_rules('city','city','trim|required');

		if($this->form_validation->run()){
			$UserId = $this->input->post('UserId');
			$insert['nickname'] = $nickname =  $this->input->post('nickname');
			$insert['email'] = $email = $this->input->post('email');
			$insert['phone'] = $phone = $this->input->post('phone');
			$insert['dob'] = $this->input->post('dob');
			//$insert['invited_by'] = $admin_id;
			$insert['gender'] = $this->input->post('gender');
			$insert['country'] = $contryId = $this->input->post('country');
			$insert['city'] = $this->input->post('city');
			$countryCode = $this->common_model->GetSingleData('country',array('id'=> $contryId));
			$insert['phone_with_code'] = $countryCode["phonecode"]."".$phone;

			$insert['lng1'] = $this->input->post('lng1');
			$insert['lng2'] = $this->input->post('lng2');
			
			//$insert['created_at'] = date('Y-m-d H:i:s');
			$insert['updated_at'] = date('Y-m-d H:i:s');

			
			$alreadyEmail= $this->common_model->GetSingleData('users', array('email' => $email, 'id !=' => $UserId));

			$alreadyPhone= $this->common_model->GetSingleData('users', array('phone' => $phone, 'id !=' => $UserId));

							if($alreadyEmail){
							$output['status'] = 0;
							$output['message'] = '<div class="alert alert-danger">Email already exist.</div>';				
					

							} elseif ($alreadyPhone) {
							$output['status'] = 0;
							$output['message'] = '<div class="alert alert-danger">Phone already exist.</div>';
							} elseif($this->common_model->UpdateData('users',array('id'=>$UserId),$insert)){
							
							$output['status'] = 1;
							$this->session->set_flashdata('msgs','<div class="alert alert-success">Hostess has been updated successfully.</div>');

							//$u_name = $nickname;

					//$subject = "Account Created as hostess"; 
					//$contant= 'Hello, '.$nickname.' <br><br>';
					//$contant .='<p class="text-center">This is an automated message. Donot reply this mail !!<p>';

					//$contant .='<p class="text-center">Your account has been created as hostess !!</p>';
					//$contant .='<p class="text-center">Your registered email id is: '.$email.' and password is : '.$password.'. Kindly change your password after first login.</p>';
	 
					//$contant .='<p class="text-center">Password: <b>'.$data['password'].'</b></p>';
						
					//$run = $this->common_model->SendMail($email,$subject,$contant);

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

	public function generateRandomString($length = 9) {
			    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ9876543210';
			    $charactersLength = strlen($characters);
			    $randomString = '';
			    for ($i = 0; $i < $length; $i++) {
			        $randomString .= $characters[rand(0, $charactersLength - 1)];
			    }
			    return $randomString;
	}

	public function HostessSettlement()
	{
		$uniqueID = $this->session->userdata('uniqueID');

	$sql = 'SELECT * FROM settlement WHERE (SELECT count(id) from users WHERE invited_by='.$uniqueID.' and user_type=1 and settlement.user_id = users.id) > 0';

      $result = $this->db->query($sql);
       $result->num_rows();
    $data["data"] = $result->result_array();        

		$this->load->view('agent/hostess-settlement',$data);
	}

	public function SubagentSettlement()
	{
		$uniqueID = $this->session->userdata('uniqueID');

	$sql = 'SELECT * FROM settlement WHERE (SELECT count(id) from users WHERE invited_by='.$uniqueID.' and user_type=2 and settlement.user_id = users.id) > 0';

      $result = $this->db->query($sql);
       $result->num_rows();
    $data["data"] = $result->result_array();        

		$this->load->view('agent/Subagent-settlement',$data);
	}
	
	
} ?>
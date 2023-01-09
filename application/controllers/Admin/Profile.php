<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
/*
*/
/*
*/
error_reporting(0);
class Profile extends CI_Controller
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
 	
 	public function edit(){

		$this->form_validation->set_rules('name','Name','required');
 		$this->form_validation->set_rules('email','Email','required|valid_email');

 		if($this->form_validation->run()==true){

			$name = $this->input->post('name');
			$email = $this->input->post('email');

			$admin_id = $this->session->userdata('admin_id');


			$run = $this->common_model->UpdateData('admin',array('id' =>$admin_id),array('email' =>$email ,'name'=>$name));
			if($run){

				$this->session->set_flashdata('msg','<div class="alert alert-success">Success! Your profile has been update successfully.</div>');

				$json['status']=1;
			} else {
				$this->session->set_flashdata('msg','<div class="alert alert-danger">Something is Worng.</div>');
				$json['status']=0;
			}

			
		} else {
			$this->session->set_flashdata('msg','<div class="alert alert-danger">'.validation_errors().'</div>');
			$json['status']=0;
		}

		echo json_encode($json);

 	}
 	public function change_password(){

 		$admin_id = $this->session->userdata('admin_id');
		$admindata= $this->common_model->GetSingleData('admin',array('id'=>$admin_id));

 		$this->form_validation->set_rules('admin_password','Current password','required');
 		$this->form_validation->set_rules('New_Password','New password','required|min_length[6]');
 		$this->form_validation->set_rules('Confirm_Password','Confirm password','required|matches[New_Password]');

 		if($this->form_validation->run()==true){

			$admin_pass = $this->input->post('admin_password');
			$New_Password = $this->input->post('New_Password');
			$Confirm_Password = $this->input->post('Confirm_Password');

 			if($admindata['password'] == $admin_pass){

 				$run = $this->common_model->UpdateData('admin',array('id' =>$admin_id),array('password' =>$New_Password));

 				if($run){

					$this->session->set_flashdata('msg','<div class="alert alert-success">Success! Your password has been updated successfully.</div>');
					$json['status']=1;

				}else {
					$json['message']='<div class="alert alert-danger">Error! Something went wrong.</div>';
					$json['status']=0;
				}
 			}
 			else{
 				$json['message']='<div class="alert alert-danger">Current password does not match.</div>';
				$json['status']=0;
 			}
 		}
 		else{
 			$json['message']='<div class="alert alert-danger">'.validation_errors().'</div>';
			$json['status']=0;
 		}

 		echo json_encode($json);
 	}
 	public function index(){
		$this->load->view('admin/profile');
	} 


   public function logout(){
		session_destroy();
		redirect('admin');
	}
	public function point_management(){
		$data["data_point"]=$this->common_model->GetSingleData('setting', array('id'=>1));
		$this->load->view('admin/point-management',$data);
	}
	public function point_managementAction(){
		$this->form_validation->set_rules('sharing_point','sharing point','trim|required');
		$this->form_validation->set_rules('fav_point','favorite point','trim|required');
		$this->form_validation->set_rules('registration_point','registration point','trim|required');
		$this->form_validation->set_rules('ratting_point','ratting point','trim|required');
		$this->form_validation->set_rules('winning_point','winning point','trim|required');
		if($this->form_validation->run()){

			$update['sharing_point'] = $this->input->post('sharing_point');
			$update['fav_point'] = $this->input->post('fav_point');
			$update['registration_point'] = $this->input->post('registration_point');
			$update['ratting_point'] = $this->input->post('ratting_point');
			$update['winning_point'] = $this->input->post('winning_point');
			$run = $this->common_model->UpdateData('setting', array('id'=>1),$update);

						if($run){
							
							$output['status'] = 1;
							$this->session->set_flashdata('msg','<div class="alert alert-success">Points has been Updated successfully.</div>');	
							}
							else {
						$output['status'] = 0;
						$output['message'] = $this->session->set_flashdata('msgsErr','<div class="alert alert-danger">Something went Wrong!</div>');	
							}
		}
		else {
			$output['status'] = 0;
			$output['message'] = '<div class="alert alert-danger">'.validation_errors().'</div>';
		}//end else form validation
		echo json_encode($output);

	}

}


 ?>
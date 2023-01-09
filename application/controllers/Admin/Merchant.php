<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
/*
*/
/*
*/
error_reporting(0);
class Merchant extends CI_Controller
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
		$data["data"]=$this->common_model->GetAllData('merchant','','id','desc');	
		$this->load->view('admin/merchant-list', $data);	
	}

	public function AddMerchant()
	{
		$this->form_validation->set_rules('name','name','trim|required');
		$this->form_validation->set_rules('email','email','trim|required|is_unique[merchant.email]');
		$this->form_validation->set_rules('phone','phone','trim|required|is_unique[merchant.phone]');
		$this->form_validation->set_rules('description','description','trim|required');
		if($this->form_validation->run()){

				$insert["name"]=$name = $this->input->post('name');
				$insert["email"]=$email = $this->input->post('email');
				$insert["phone"]=$this->input->post('phone');
				$insert["description"]= $description = $this->input->post('description');
				$insert["unique_id"] = $unique_id = $this->GetUniqueUserID();
				$insert["authorization_key"] = $authorization_key = md5($unique_id.time());
				$insert["created_at"] = date('Y-m-d H:i:s');

				$run = $this->common_model->InsertData('merchant', $insert);
				if($run){
					$subject = "Merchant Account Created"; 
					$contant= 'Hello, '.$name.' <br><br>';
					$contant .='<p class="text-center">This is an automated message. Donot reply this mail !!<p>';

					$contant .='<p class="text-center">Your account as been created as merchant</p>';

					$contant .='<p class="text-center">Your unique id is <b>'.$unique_id.'</b> and Merchant Authorization Key <b>'.$authorization_key.'</b>.</p>';
					$contant .='<p class="text-center">Your description : '.$description.'!!</p>';
						
					$this->common_model->SendMail($email,$subject,$contant);

					$output['status'] = 1;
					$this->session->set_flashdata('msgs','<div class="alert alert-success">Merchant has been added successfully.</div>');	
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


	public function EditMerchant($value='')
	{
		$this->form_validation->set_rules('name','name','trim|required');
		$this->form_validation->set_rules('email','email','trim|required');
		$this->form_validation->set_rules('phone','phone','trim|required');
		$this->form_validation->set_rules('description','description','trim|required');
		//$this->form_validation->set_rules('unique_id','unique id','trim|required');
		if($this->form_validation->run()){

				$id = $this->input->post('id');
				$insert["name"]=$this->input->post('name');
				$insert["email"]= $email = $this->input->post('email');
				$insert["phone"]= $phone = $this->input->post('phone');
				$insert["description"]=$this->input->post('description');
				//$insert["unique_id"] = $unique_id = $this->input->post('unique_id');

				$alreadyEmail = $this->common_model->GetSingleData('merchant', array('id != '=>$id, 'email'=>$email));
				$alreadyPhone = $this->common_model->GetSingleData('merchant', array('id != '=>$id, 'phone'=>$phone));
				if ($alreadyEmail) {
					$output['status'] = 0;
					$output['message'] = '<div class="alert alert-danger">Email must be unique!</div>';
				} else if ($alreadyPhone) {
					$output['status'] = 0;
					$output['message'] = '<div class="alert alert-danger">Phone must be unique!</div>';
				}  else if($this->common_model->UpdateData('merchant',array('id'=>$id) ,$insert)){

					$output['status'] = 1;
					$this->session->set_flashdata('msgs','<div class="alert alert-success">Merchant has been updated successfully.</div>');	
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

	private function GetUniqueUserID(){
		$uniqueID = rand(100000,999999);
		$check = $this->common_model->GetColumnName('merchant',array('unique_id'=>$uniqueID),array('id'));
		
		if($check){
			$this->GetUniqueUserID();
		} else {
			return $uniqueID;
		}
	}
	public function deleteMerchant()
	{
		$id =$_GET["id"];
			
			$run = $this->common_model->DeleteData('merchant',array('id'=>$id));
            //echo $this->db->last_query();
			if($run){

				$this->session->set_flashdata('msgs','<div class="alert alert-success">Success! Merchant has been deleted successfully.</div>');

				
			} else {
			    $this->session->set_flashdata('msgs','<div class="alert alert-danger">Something is Worng.</div>');

			}			    redirect('admin/merchant-list');
	}


	public function AddDiamond($value='')
	{
		$this->form_validation->set_rules('diamond','name','trim|required');
		if($this->form_validation->run()){

				$insert["diamond"]= $diamond = $this->input->post('diamond');
				$insert["merchant_id"]= $id = $this->input->post('merchant_id');
				$insert["type"]=1;
				$insert["created_at"] = date('Y-m-d H:i:s');

				$run = $this->common_model->InsertData('merchant_diamond', $insert);
				if($run){
					$Merchant = $this->common_model->GetSingleData('merchant',array('id'=>$id));
					$update["diamond"] = $diamond+$Merchant["diamond"];
					$this->common_model->UpdateData('merchant',array('id'=>$id),$update);
					$output['status'] = 1;
					$this->session->set_flashdata('msgs','<div class="alert alert-success">Diamond has been added successfully.</div>');	
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

	public function removeDiamond($value='')
	{
		$this->form_validation->set_rules('diamond','name','trim|required');
		if($this->form_validation->run()){

				$insert["diamond"]= $diamond = $this->input->post('diamond');
				$insert["merchant_id"]= $id = $this->input->post('merchant_id');
				$insert["type"]=3;
				$insert["created_at"] = date('Y-m-d H:i:s');

				$run = $this->common_model->InsertData('merchant_diamond', $insert);
				if($run){
					$Merchant = $this->common_model->GetSingleData('merchant',array('id'=>$id));
					$update["diamond"] = $Merchant["diamond"]-$diamond;
					$this->common_model->UpdateData('merchant',array('id'=>$id),$update);
					$output['status'] = 1;
					$this->session->set_flashdata('msgs','<div class="alert alert-success">Diamond has been removed successfully.</div>');	
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

	public function revoke()
	{
		$id = $this->input->post('id');
		//echo $id; die;
		$unique_id = $this->GetUniqueUserID();
		$insert["authorization_key"] = $authorization_key = md5($unique_id.time());
		$run = $this->common_model->UpdateData('merchant', array('id'=>$id), $insert);
		//echo $this->db->last_query(); die;
		if ($run) {
				$output['status'] = 1;
				$this->session->set_flashdata('msgs','<div class="alert alert-success">Merchant Authorization Key has been updated successfully.</div>');	
		} else {
				$output['status'] = 0;
				$output['message'] = '<div class="alert alert-danger">Something went Wrong!</div>';
		}
		echo json_encode($output);
		/*$subject = "Merchant Account Created"; 
					$contant= 'Hello, '.$name.' <br><br>';
					$contant .='<p class="text-center">This is an automated message. Donot reply this mail !!<p>';

					$contant .='<p class="text-center">Your account as been created as merchant</p>';

					$contant .='<p class="text-center">Your unique id is <b>'.$unique_id.'</b> and Merchant Authorization Key <b>'.$authorization_key.'</b>.</p>';
					$contant .='<p class="text-center">Your description : '.$description.'!!</p>';
						
					$this->common_model->SendMail($email,$subject,$contant);*/
	}

}?>
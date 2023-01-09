<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
/*
*/
/*
*/
error_reporting(0);
class FormBuilder extends CI_Controller
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
		$data["data"] = $this->common_model->GetAllData('payment_form','','id','desc');
		$this->load->view('admin/form-builder',$data);
	}
	public function AddForm()
	{
		//print_r($this->input->post()); die;
		$this->form_validation->set_rules('name1','form name','trim|required');
		$this->form_validation->set_rules('note','form note','trim|required');
		$this->form_validation->set_rules('description','form description','trim|required');
		if($this->form_validation->run()){
			$insert["name"]= $this->input->post('name1');
			$insert["note"]= $this->input->post('note');
			$insert["description"]= $this->input->post('description');
			$country = $this->input->post('country');
			$insert["country"] = implode(',', $country);

			$is_required = $this->input->post('is_required');
			$field_type = $this->input->post('field_type');
			$label = $this->input->post('label');
			$placeholder = $this->input->post('placeholder');
			$name = $this->input->post('name');

			$run = $this->common_model->InsertData('payment_form', $insert);

					if($run){
									
					$output['status'] = 1;
					$this->session->set_flashdata('msgs','<div class="alert alert-success">Form created successfully.</div>');	
					foreach($is_required as $key => $value){
						$insert1["payment_form_id"]= $run;
						$insert1["is_required"]= $value;
						$insert1["field_type"]= $field_type[$key];
						$insert1["label"]= $label[$key];
						$insert1["placeholder"]= $placeholder[$key];
						$insert1["name"]= $name[$key];
						$run1 = $this->common_model->InsertData('payment_form_field', $insert1);
					}

					}
					else {
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

	public function RemoveFromFields()
	{
		$Id= $this->input->post('Id');

		$run = $this->common_model->DeleteData('payment_form_field', array('id'=>$Id));

		if ($run) {
			echo "1";
		} else {
			echo "0";
		}
	}

	public function EditFrom()
	{
		//print_r($this->input->post()); die;


		$this->form_validation->set_rules('name1','form name','trim|required');
		$this->form_validation->set_rules('note','form note','trim|required');
		$this->form_validation->set_rules('description','form description','trim|required');
		if($this->form_validation->run()){
			$formId= $this->input->post('formId');
			$insert["name"]= $this->input->post('name1');
			$insert["note"]= $this->input->post('note');
			$insert["description"]= $this->input->post('description');
			$country = $this->input->post('country');
			$insert["country"] = implode(',', $country);

			$is_required = $this->input->post('is_required');
			$field_type = $this->input->post('field_type');
			$label = $this->input->post('label');
			$placeholder = $this->input->post('placeholder');
			$name = $this->input->post('name');

			$formContentId = $this->input->post('formContentId');

			$run = $this->common_model->UpdateData('payment_form',array('id'=>$formId), $insert);

					if($run){
									
					$output['status'] = 1;
					$this->session->set_flashdata('msgs','<div class="alert alert-success">Form updated successfully.</div>');	
					foreach($is_required as $key => $value){
						$insert1["is_required"]= $value;
						$insert1["field_type"]= $field_type[$key];
						$insert1["label"]= $label[$key];
						$insert1["placeholder"]= $placeholder[$key];
						$insert1["name"]= $name[$key];
						if (!empty($formContentId[$key])) {
						$this->common_model->UpdateData('payment_form_field', array('id'=>$formContentId[$key]),$insert1);	
						}
						else {
						$insert1["payment_form_id"]= $formId;	
						$this->common_model->InsertData('payment_form_field', $insert1);	
						}
						
						
					}

					}
					else {
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

	public function Form_edit()
	{
		$id = $this->uri->segment(3);

		$data["form"]=$this->common_model->GetSingleData('payment_form', array('id'=>$id));
		$this->load->view('admin/form-edit', $data);	
	}

	public function delete()
	{
		$Id =$_GET["Id"];
			
			$run = $this->common_model->DeleteData('payment_form',array('id'=>$Id));
            //echo $this->db->last_query();
			if($run){

				$this->common_model->DeleteData('payment_form_field',array('payment_form_id'=>$Id));
				$this->session->set_flashdata('msgs','<div class="alert alert-success">Success! Form has been deleted successfully.</div>');
				
			} else {
			    $this->session->set_flashdata('msgs','<div class="alert alert-danger">Something is Worng.</div>');

			}			    

			redirect('admin/form-builder');
	}

	public function withdrawal()
	{
		$where = ' status=0';	
		if ($_REQUEST['start_date']) {
		$where .= " and DATE(create_date) = DATE('".$_REQUEST['start_date']."')";
		}
		if ($_REQUEST['uniqueID']) {
		$where .= " and ( (select count(id) from users where users.id = withdrawal.user_id and users.uniqueID='".$_REQUEST['uniqueID']."') > 0 )";
		}
		$data["content"]="Pending";
		$data["data"] = $this->common_model->GetAllData('withdrawal',$where,'id','asc');
		$this->load->view('admin/withdrawal-request', $data);
	}

	public function withdrawalApproved($value='')
	{
		$where = ' status=1';	
		if ($_REQUEST['start_date']) {
		$where .= " and DATE(create_date) = DATE('".$_REQUEST['start_date']."')";
		}
		if ($_REQUEST['uniqueID']) {
		$where .= " and ( (select count(id) from users where users.id = withdrawal.user_id and users.uniqueID='".$_REQUEST['uniqueID']."') > 0 )";
		}
		$data["content"]="Approved";
		$data["data"] = $this->common_model->GetAllData('withdrawal',$where,'id','desc');
		$this->load->view('admin/withdrawal-request', $data);
	}

	public function withdrawalRejected($value='')
	{
		$where = ' status=2';	
		if ($_REQUEST['start_date']) {
		$where .= " and DATE(create_date) = DATE('".$_REQUEST['start_date']."')";
		}
		if ($_REQUEST['uniqueID']) {
		$where .= " and ( (select count(id) from users where users.id = withdrawal.user_id and users.uniqueID='".$_REQUEST['uniqueID']."') > 0 )";
		}
		$data["content"]="Rejected";
		$data["data"] = $this->common_model->GetAllData('withdrawal',$where,'id','desc');
		$this->load->view('admin/withdrawal-request', $data);
	}

	public function withdrawalAccept()
	{
		$id = $_GET["id"];

		$update["status"] = "1";

		$run = $this->common_model->UpdateData('withdrawal', array('id'=>$id), $update);

		if ($run) {
			$data = $this->common_model->GetSingleData('withdrawal', array('id'=>$id));
			$user = $this->common_model->GetSingleData('users', array('id'=>$data["user_id"]));
			$update1["wallet"] = $user["wallet"]-$data["amount"];
			$this->common_model->UpdateData('users', array('id'=>$data["user_id"]), $update1);

			$this->session->set_flashdata('msgs','<div class="alert alert-success">Success! Accepted withdrawal request.</div>');
		} else {
			$this->session->set_flashdata('msgs','<div class="alert alert-danger">Something went Wrong.</div>');
		}

		redirect('admin/withdrawal-request');
	}

	public function withdrawalReject()
	{
		
		$this->form_validation->set_rules('reason','reason','trim|required');
		if($this->form_validation->run()){
		$id = $this->input->post('id');
		$update["reason"] = $this->input->post('reason');	
		$update["status"] = "2";	
		$run = $this->common_model->UpdateData('withdrawal', array('id'=>$id), $update);
				if($run){
							
				$output['status'] = 1;
				$this->session->set_flashdata('msgs','<div class="alert alert-success">Request has been rejected successfully.</div>');	
				}
				else {
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



}?>
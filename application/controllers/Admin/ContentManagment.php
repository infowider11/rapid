<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 */
class ContentManagment extends CI_Controller
{
	
	public function __construct() {
		parent::__construct();
		$this->check_login();
		
	}

	public function check_login(){
		if(!$this->session->userdata('admin_id')){
			redirect('admin');
		}
	}
	public function privacy_policy()
	{
		$data['privacy_policy'] = $this->common_model->GetSingleData('content_management',array('id'=>2));
		$this->load->view('admin/privacy-policy',$data);
	}
	public function terms_condition()
	{
		$data['terms_condition'] = $this->common_model->GetSingleData('content_management',array('id'=>1));
		$this->load->view('admin/terms-condition',$data);
	}
	public function about_us()
	{
		$data['terms_condition'] = $this->common_model->GetSingleData('content_management',array('id'=>3));
		$this->load->view('admin/about-us',$data);
	}
	public function rate_us()
	{
		$data['terms_condition'] = $this->common_model->GetSingleData('content_management',array('id'=>4));
		$this->load->view('admin/rate-us',$data);
	}

	public function privacy_policyEdit()
		{
			$this->form_validation->set_rules('text','text','trim|required');
			$this->form_validation->set_rules('title','title','trim|required');
			if($this->form_validation->run()){
				$update['text'] = $this->input->post('text');
				$update['title'] = $this->input->post('title');
				$run = $this->common_model->UpdateData('content_management',array('id'=>2),$update);

						if($run){
							$output['status'] = 1;
							$this->session->set_flashdata('msg','<div class="alert alert-success">Privacy & Policy has been Updated successfully.</div>');	
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
	public function terms_conditionEdit()
		{
			$this->form_validation->set_rules('text','text','trim|required');
			$this->form_validation->set_rules('title','title','trim|required');
			if($this->form_validation->run()){
				$update['text'] = $this->input->post('text');
				$update['title'] = $this->input->post('title');
				$run = $this->common_model->UpdateData('content_management',array('id'=>1),$update);

						if($run){
							$output['status'] = 1;
							$this->session->set_flashdata('msg','<div class="alert alert-success">Terms & Condition has been Updated successfully.</div>');	
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
	public function aboutusEdit()
		{
			$this->form_validation->set_rules('text','text','trim|required');
			$this->form_validation->set_rules('title','title','trim|required');
			if($this->form_validation->run()){
				$update['text'] = $this->input->post('text');
				$update['title'] = $this->input->post('title');
				$run = $this->common_model->UpdateData('content_management',array('id'=>3),$update);

						if($run){
							$output['status'] = 1;
							$this->session->set_flashdata('msg','<div class="alert alert-success">About us has been Updated successfully.</div>');	
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
	
	public function rateUsEdit()
		{
			$this->form_validation->set_rules('text','text','trim|required');
			$this->form_validation->set_rules('title','title','trim|required');
			if($this->form_validation->run()){
				$update['text'] = $this->input->post('text');
				$update['title'] = $this->input->post('title');
				$run = $this->common_model->UpdateData('content_management',array('id'=>4),$update);

						if($run){
							$output['status'] = 1;
							$this->session->set_flashdata('msg','<div class="alert alert-success">Rate Us page data has been Updated successfully.</div>');	
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
	


}	
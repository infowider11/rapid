<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 */
class SpinWheel extends CI_Controller
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
	public function index()
	{
		$data['records'] = $this->common_model->GetAllData('spin_wheel_option');
		$this->load->view('admin/spin-wheel',$data);
	}
	

	public function editWheel()
		{
			$this->form_validation->set_rules('text','text','trim|required');
			$this->form_validation->set_rules('id','title','trim|required');
			if($this->form_validation->run()){
				$update['text'] = $this->input->post('text');
				$id = $this->input->post('id');
				$is_empty = $this->input->post('is_empty');
				
				if($is_empty==1){
					$update['diamond'] = 0;
				} else {
					$update['diamond'] = $this->input->post('diamond');
				}
				
				$run = $this->common_model->UpdateData('spin_wheel_option',array('id'=>$id),$update);

						if($run){
							$output['status'] = 1;
							$this->session->set_flashdata('msg','<div class="alert alert-success">Data has been Updated successfully.</div>');	
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
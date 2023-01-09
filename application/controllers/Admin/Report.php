<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
/*
*/
/*
*/
error_reporting(0);
class Report extends CI_Controller
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
		$data["data"]=$this->common_model->GetAllData('reports','','id','desc');	
		$this->load->view('admin/report-list', $data);	
	}
	public function post(){
		$data["data"]=$this->common_model->GetAllData('post_reports','','id','desc');	
		$this->load->view('admin/report-post', $data);	
	}
	public function post_management(){
		$data["data"]=$this->common_model->GetAllData('post','','id','desc');	
		$this->load->view('admin/post-management', $data);	
	}

	public function category(){
		$data["data"]=$this->common_model->GetAllData('reporting_category','','id','desc');	
		$this->load->view('admin/report-category', $data);	
	}

	public function AddCat($value='')
		{
			$this->form_validation->set_rules('name','name','trim|required|is_unique[reporting_category.name]');
			if($this->form_validation->run()){

				$insert["name"] = $this->input->post('name');

				$run = $this->common_model->InsertData('reporting_category', $insert);

				if ($run) {
								$output['status'] = 1;
								$this->session->set_flashdata('msgs','<div class="alert alert-success">Category has been added successfully.</div>');
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

		public function EditCat($value='')
		{
			$this->form_validation->set_rules('name','name','trim|required');
			if($this->form_validation->run()){

				$insert["name"] = $name = $this->input->post('name');
				$id = $this->input->post('id');
				$already = $this->common_model->GetSingleData('reporting_category', array('id != '=>$id, 'name'=>$name));
				
				if ($already) {
					$output['status'] = 0;
					$output['message'] = '<div class="alert alert-danger">The category name must be unique.</div>';		
				}
				else if ($this->common_model->UpdateData('reporting_category', array('id'=>$id), $insert)) {
								$output['status'] = 1;
								$this->session->set_flashdata('msgs','<div class="alert alert-success">Category has been updated successfully.</div>');
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
		
		public function DeleteCat()
		{
			$id = $_GET["id"];
			$page = $_GET["page"];

			$run = $this->common_model->DeleteData('reporting_category', array('id'=>$id));

			if ($run) {
				$this->session->set_flashdata('msgs','<div class="alert alert-success">Success! Category has been deleted successfully.</div>');
			} else {
				$this->session->set_flashdata('msgs','<div class="alert alert-danger">Something went Wrong.</div>');
			}

			redirect('admin/'.$page.'');
		}

		public function BlockAction($value='')
		{
			//print_r($_REQUEST); die; 
			$this->form_validation->set_rules('block_to','block_to','trim|required');
			$this->form_validation->set_rules('blocked_for','blocked_for','trim|required');
			$this->form_validation->set_rules('till_date','till date','trim|required');
			if($this->form_validation->run()){
				$id = $this->input->post('block_to');
				$insert["blocked_for"] = $blocked_for = $this->input->post('blocked_for');
				$insert["blocked_till_date"] = $till_date = $this->input->post('till_date');
				$insert["report_id"] = $report_id = $this->input->post('report_id');
				$insert["temporary_block"] = 1;


				$run = $this->common_model->UpdateData('users', 'id = '.$id.'', $insert);
				//echo $this->db->last_query(); die;
					if ($run) {
						$update["status"] = 1;
						$this->common_model->UpdateData('reports', array('id'=>$report_id), $update);

					//mail to block user	
					$userData = $this->common_model->GetSingleData('users',array('id'=>$id));
					$subject = "Messsage from Admin"; 
					$message = $this->common_model->GetSingleData('email_template', array('id'=>8));	
					$email_body = str_replace("[username]",$userData["nickname"],$message["text"]);

					$email_body1 = str_replace("[blocked_for]",$blocked_for,$email_body);

					$email_body2 = str_replace("[till_date]",$till_date,$email_body1);
					$email_body3 = str_replace("assets/admin/EmailImg/",site_url()."assets/admin/EmailImg/",$email_body2);
					$contant ='<p class="text-center">'.$email_body3.'</p>';						
					$run1 = $this->common_model->SendMailCustom($userData['email'],$subject,$contant);
					
					//mail to blocked by
					$blocked_by = $this->common_model->GetSingleData('reports', array('id'=>$report_id, 'report_by != '=>$id));
					$message1 = $this->common_model->GetSingleData('email_template', array('id'=>9));
					if ($blocked_by) {
					$userData2 = $this->common_model->GetSingleData('users', array('id'=>$blocked_by["report_by"]));
					$email_body2 = str_replace("[username]",$userData2["nickname"],$message1["text"]);
					$email_body22 = str_replace("[reported_user]",$userData["nickname"],$email_body2);
					$email_body222 = str_replace("assets/admin/EmailImg/",site_url()."assets/admin/EmailImg/",$email_body22);
					$contant ='<p class="text-center">'.$email_body222.'</p>';						
					$run11 = $this->common_model->SendMailCustom($userData2['email'],$subject,$contant);
					}

					//mail to user who report for above user
					$existingReports = $this->common_model->GetAllData('reports', array('report_to' =>$id, 'id != '=>$report_id));

					if ($existingReports) {
						foreach ($existingReports as $key => $value) {
							$userData1 = $this->common_model->GetSingleData('users', array('id'=>$value["report_by"]));
					//$message1 = $this->common_model->GetSingleData('email_template', array('id'=>9));	
					$email_body11 = str_replace("[username]",$userData1["nickname"],$message1["text"]);
					$email_body111 = str_replace("[reported_user]",$userData["nickname"],$email_body11);
					$email_body1111 = str_replace("assets/admin/EmailImg/",site_url()."assets/admin/EmailImg/",$email_body111);
					$contant ='<p class="text-center">'.$email_body1111.'</p>';						
					$run11 = $this->common_model->SendMailCustom($userData1['email'],$subject,$contant);
						//close existing reports
					$update1["status"] = 1;
					$this->common_model->UpdateData('reports', array('id'=>$value["id"]), $update1);
						}
					}


								$output['status'] = 1;
								$this->session->set_flashdata('msgs','<div class="alert alert-success">User has been blocked successfully.</div>');
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

}?>
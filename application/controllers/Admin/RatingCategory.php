<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);
class RatingCategory extends CI_Controller
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
		$data["content"]= "Boys";
		$data["data"] = $this->common_model->GetAllData('rating_category',array('gender'=>'male'),'id','desc');
		$this->load->view('admin/rating-category', $data);
	}
	public function RatingGirl($value='')
	{
		$data["content"]= "Girls";
		$data["data"] = $this->common_model->GetAllData('rating_category',array('gender'=>'female'),'id','desc');
		$this->load->view('admin/rating-category', $data);
	}
	public function AddCategory()
	{
		$this->form_validation->set_rules('name','name','trim|required|is_unique[rating_category.name]');
		$this->form_validation->set_rules('rating','rating','trim|required');
		if($this->form_validation->run()){

			$insert["rating"]= $this->input->post('rating');
			$insert["name"]= $this->input->post('name');
			$type = $this->input->post('gender');
			if ($type == 'Boys') {
			$insert["gender"]= 'male';
			} else {
			$insert["gender"]= 'female';
			}
				$run = $this->common_model->InsertData('rating_category',$insert);

						if($run){
							$output['status'] = 1;
							$this->session->set_flashdata('msgs','<div class="alert alert-success">Success!, Category has been added successfully.</div>');	
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


	public function EditCategory()
	{
		$this->form_validation->set_rules('name','name','trim|required');
		$this->form_validation->set_rules('rating','rating','trim|required');
		if($this->form_validation->run()){

			$insert["rating"]= $this->input->post('rating');
			$insert["name"]= $name = $this->input->post('name');
			$id = $this->input->post('id');
				$already = $this->common_model->GetSingleData('rating_category',array('id != '=>$id, 'name'=>$name));
						if ($already) {
						$output['status'] = 0;
						$output['message'] = '<div class="alert alert-danger">The name field must be unique !</div>';
						}
						else if($this->common_model->UpdateData('rating_category',array('id'=>$id),$insert)){
							$output['status'] = 1;
							$this->session->set_flashdata('msgs','<div class="alert alert-success">Success!, Category has been Updated successfully.</div>');	
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

	public function deleteCat($value='')
	{
		$id = $_GET['id']; 
		$page = $_GET['pageName']; 


		$run = $this->common_model->DeleteData('rating_category',array('id'=>$id));
            //echo $this->db->last_query();
			if($run){

				$this->session->set_flashdata('msgs','<div class="alert alert-success">Success! Category has been deleted successfully.</div>');
				
			} else {
			    $this->session->set_flashdata('msgs','<div class="alert alert-danger">Something is Worng.</div>');

			}
			    redirect('admin/'.$page.'');
	}

	public function Rating_list()
	{
		$data["data"] = $this->common_model->GetAllData('ratings','','id','desc');
		$this->load->view('admin/rating-list', $data);
	}
	
	public function verification_limit()
	{
		$data["data"] = $this->common_model->GetSingleData('verification_limit','','id','desc');
		$this->load->view('admin/verification-limit', $data);
	}


	public function edit_coin($value='')
	{
		$this->form_validation->set_rules('min_daimonds_boy','minimum daimonds boy','trim|required');
		$this->form_validation->set_rules('min_coins_girl','minimum coins girl','trim|required');

		$this->form_validation->set_rules('min_level_boy','minimum level boy','trim|required');
		$this->form_validation->set_rules('min_level_girl','minimum level girl','trim|required');
		if($this->form_validation->run()){

			$insert["min_daimonds_boy"]= $this->input->post('min_daimonds_boy');
			$insert["min_coins_girl"]= $this->input->post('min_coins_girl');

			$insert["min_level_boy"]= $this->input->post('min_level_boy');
			$insert["min_level_girl"]= $this->input->post('min_level_girl');

			$run = $this->common_model->UpdateData('verification_limit', array('id'=>1), $insert);
							if($run){
							$output['status'] = 1;
							$this->session->set_flashdata('msgs','<div class="alert alert-success">Success!, Data has been Updated successfully.</div>');	
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

	public function verification_request($value='')
	{
		$data["data"] = $this->common_model->GetAllData('verification_users ','','id','desc');
		$this->load->view('admin/verification-request', $data);
	}

	public function VerificationAccept()
	{
		$id = $_GET["id"];
		$userId = $_GET["userId"];
		$update["status"] = 1;

		$run = $this->common_model->UpdateData('verification_users', array('id'=>$id), $update);

		if($run){
				$update["verification_icon"] = 2;
				$this->common_model->UpdateData('users', array('id'=>$userId), $update);
				$this->session->set_flashdata('msgs','<div class="alert alert-success">Success! User verification has been accepted successfully.</div>');
				
			} else {
			    $this->session->set_flashdata('msgs','<div class="alert alert-danger">Something is Worng.</div>');

			}
			if (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) {
				 redirect('admin/users');
			} else {
			    redirect('admin/verification-request');
			}
	}

	public function VerificationReject()
	{
		$id = $_GET["id"];
		$userId = $_GET["userId"];
		$update["status"] = 2;

		$run = $this->common_model->UpdateData('verification_users', array('id'=>$id), $update);

		if($run){
				$update["verification_icon"] = 3;
				$this->common_model->UpdateData('users', array('id'=>$userId), $update);
				$this->session->set_flashdata('msgs','<div class="alert alert-success">Success! User verification has been rejected successfully.</div>');
				
			} else {
			    $this->session->set_flashdata('msgs','<div class="alert alert-danger">Something is Worng.</div>');

			}
			    if (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) {
				 redirect('admin/users');
			} else {
			    redirect('admin/verification-request');
			}
	}

	public function official_badges()
	{
		$data["data"] = $this->common_model->GetAllData('users', array('official_badges' =>1),'id','desc');
		$data["data1"] = $this->common_model->GetAllData('users', array('official_badges' =>0,'status'=>1,'is_verified'=>1),'id','desc');

		$this->load->view('admin/official-badges', $data);
	}
	public function add_official_badges($value='')
	{
		$userId = $this->input->post('UserId');
		$update["official_badges"] = 1;

		$run = $this->common_model->UpdateData('users', array('id'=>$userId), $update);

		if ($run) {
			 $output['status'] = 1;
			 $this->session->set_flashdata('msgs','<div class="alert alert-success">Official badges added to user successfully.</div>');
		} else {
			$output['status'] = 0;
			$output['message'] = '<div class="alert alert-danger">Something went Wrong!</div>';
		}

		echo json_encode($output);
	}
}?>
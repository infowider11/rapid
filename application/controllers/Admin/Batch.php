<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
/*
*/
/*
*/
error_reporting(0);
class Batch extends CI_Controller
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
	$data["data"] = $this->common_model->GetAllData('badges','','id', 'desc');	
	$this->load->view('admin/batches-management', $data);
	}

	public function AddBatch()
	{
		$this->form_validation->set_rules('title','title','trim|required|is_unique[badges.title]');
		if($this->form_validation->run()){

			$insert['title'] = $this->input->post('title');
			$insert['created_at'] = date('Y-m-d H:i:s');
			$check = true;
                    if($_FILES['image']['name']){

                    $config['upload_path']="assets/admin/batchesImg";
                    $config['allowed_types'] = 'jpeg|gif|jpg|png';
                    $config['encrypt_name']=true;
                    $this->load->library("upload",$config);
                    if ($this->upload->do_upload('image')) {
                    $u_profile=$this->upload->data("file_name");
                    $insert['image'] = "assets/admin/batchesImg/".$u_profile;


                    } else {
                    $check = false;
                $output['status'] = 0;
                $output['message'] = '<div class="alert alert-danger">'.$this->upload->display_errors().'</div>';   
                    }
                    }

                    if($check){
                    	$run = $this->common_model->InsertData('badges', $insert);
                    	if ($run) {
                    		$output['status'] = 1;
							$this->session->set_flashdata('msgs','<div class="alert alert-success">Badges has been added successfully.</div>');
                    	} else {
						$output['status'] = 0;
						$output['message'] = '<div class="alert alert-danger">Something went Wrong!</div>';	
							}
															
							}
							

			}//end if form validation
		else {
			$output['status'] = 0;
			$output['message'] = '<div class="alert alert-danger">'.validation_errors().'</div>';
		}//end else form validation
		echo json_encode($output);
	}

	public function EditBatch($value='')
	{
		$this->form_validation->set_rules('title','title','trim|required');
		if($this->form_validation->run()){

			$insert['title'] = $title = $this->input->post('title');
			$id = $this->input->post('id');
			$check = true;
                    if($_FILES['image']['name']){

                    $config['upload_path']="assets/admin/batchesImg";
                    $config['allowed_types'] = 'jpeg|gif|jpg|png';
                    $config['encrypt_name']=true;
                    $this->load->library("upload",$config);
                    if ($this->upload->do_upload('image')) {
                    $u_profile=$this->upload->data("file_name");
                    $insert['image'] = "assets/admin/batchesImg/".$u_profile;


                    } else {
                    $check = false;
                $output['status'] = 0;
                $output['message'] = '<div class="alert alert-danger">'.$this->upload->display_errors().'</div>';   
                    }
                    }

                    if($check){
                    	$already = $this->common_model->GetSingleData('badges', array('title'=>$title, 'id != '=>$id));
                    	//echo $this->db->last_query(); die;
                    	if ($already) {
                    	$output['status'] = 0;
						$output['message'] = '<div class="alert alert-danger">Title must be unique!</div>';
                    	}
                    	else if ($this->common_model->UpdateData('badges', array('id'=>$id), $insert)) {
                    		$output['status'] = 1;
							$this->session->set_flashdata('msgs','<div class="alert alert-success">Badges has been updated successfully.</div>');
                    	} else {
						$output['status'] = 0;
						$output['message'] = '<div class="alert alert-danger">Something went Wrong!</div>';	
							}
															
							}
							

			}//end if form validation
		else {
			$output['status'] = 0;
			$output['message'] = '<div class="alert alert-danger">'.validation_errors().'</div>';
		}//end else form validation
		echo json_encode($output);
	}

	public function deleteBatch($value='')
	{
		$Id =$_GET["Id"];
			
			$run = $this->common_model->DeleteData('badges',array('id'=>$Id));
            //echo $this->db->last_query();
			if($run){

				$this->session->set_flashdata('msgs','<div class="alert alert-success">Success! Badges has been deleted successfully.</div>');
				
			} else {
			    $this->session->set_flashdata('msgs','<div class="alert alert-danger">Something is Worng.</div>');

			}
			    redirect('admin/batches-management');			
	}

	public function AddBatchUser()
	{
		//$insert['title'] = $title = $this->input->post('title');
		//print_r($_REQUEST); die;
			$UserId = $this->input->post('UserId');
			$update['badges']= implode(",", $this->input->post('batches'));

			$run = $this->common_model->UpdateData('users', array('id'=>$UserId), $update);
			//echo $this->db->last_query(); die;
					if ($run) {
		    		$output['status'] = 1;
					$this->session->set_flashdata('msgs','<div class="alert alert-success">Badges has been updated successfully.</div>');
			    	} else {
					$output['status'] = 0;
					$output['message'] = '<div class="alert alert-danger">Something went Wrong!</div>';	
						}
		echo json_encode($output);				
	}

	

}

?>
<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
/*
*/
/*
*/
error_reporting(0);
class Level extends CI_Controller
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

	public function Level_boys(){
		$data["data"] = $this->common_model->GetAllData('level',array('gender'=>'male'));
		$data["level_p"] = $this->common_model->GetAllData('level_privilege');
		$data["gender"] = 'male';
		$this->load->view('admin/level-management', $data);
	}
	public function Level_girls(){
		$data["data"] = $this->common_model->GetAllData('level',array('gender'=>'female'));
		$data["level_p"] = $this->common_model->GetAllData('level_privilege');
		$data["gender"] = 'female';
		$this->load->view('admin/level-management', $data);
	}

	public function EditLevel(){
		$this->form_validation->set_rules('level','level','trim|required');
		$this->form_validation->set_rules('daimond','daimond ','trim|required');
		if($this->form_validation->run()){
			$update['level'] = $this->input->post('level');
			$update['daimond'] = $this->input->post('daimond');
			$update['updated_at'] = date('Y-m-d H:i:s');
			$Id = $this->input->post('leveId');
			$update['privilege'] = implode(',',$this->input->post('privilege'));
			$check = true;
                    if($_FILES['image']['name']){

                    $config['upload_path']="assets/admin/levelIcone";
                    $config['allowed_types'] = 'jpeg|gif|jpg|png';
                    $config['encrypt_name']=true;
                    $this->load->library("upload",$config);
                    if ($this->upload->do_upload('image')) {
                    $u_profile=$this->upload->data("file_name");
                    $update['icon'] = $u_profile;


                    } else {
                    $check = false;
                $output['status'] = 0;
                $output['message'] = '<div class="alert alert-danger">'.$this->upload->display_errors().'</div>';   
                    }
                    } 

			
			if($check){
						if ($this->common_model->UpdateData('level',array('id'=>$Id),$update)) {
							$output['status'] = 1;
							$this->session->set_flashdata('msgs','<div class="alert alert-success">Level has been Updated successfully.</div>');
							} else {
						$output['status'] = 0;
						$output['message'] = $this->session->set_flashdata('msgs','<div class="alert alert-danger">Something went Wrong!</div>');	
							}	
								
			}
							
		}
		else {
			$output['status'] = 0;
			$output['message'] = '<div class="alert alert-danger">'.validation_errors().'</div>';
		}//end else form validation
		echo json_encode($output);
	}

	public function category()
	{
		$data["category"]= $this->common_model->GetAllData('gift_category','',$ob='id',$obc='DESC');
		$this->load->view('admin/category',$data);
	}

	public function AddCategory(){
		$this->form_validation->set_rules('name','category name','trim|required');
		if($this->form_validation->run()){

		$insert['name'] = $this->input->post('name');
		 $alreadyCat = $this->common_model->GetSingleData('gift_category',array('name'=>$this->input->post('name')));

						if ($alreadyCat) {
						$output['status'] = 0;
						$output['message'] = '<div class="alert alert-danger">Category already exist !</div>';
						}
						elseif($this->common_model->InsertData('gift_category', $insert)){
							
							$output['status'] = 1;
							$this->session->set_flashdata('msgs','<div class="alert alert-success">category has been added successfully.</div>');	
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

	public function deleteCategory(){
		$id = $_GET["Id"];
		$run = $this->common_model->DeleteData('gift_category',array('id'=>$id));
            //echo $this->db->last_query();
			if($run){

				$this->session->set_flashdata('msgs','<div class="alert alert-success">Success! category has been deleted successfully.</div>');
				
			} else {
			    $this->session->set_flashdata('msgs','<div class="alert alert-danger">Something is Worng.</div>');
			}			   
			redirect('admin/category');

	}
	public function EditCategory() {
		$this->form_validation->set_rules('name','category name','trim|required');
		if($this->form_validation->run()){
		$id = $this->input->post('categoryId');
		$update['name'] = $name = $this->input->post('name');

		$alreadyCat = $this->common_model->GetSingleData('gift_category',array('name'=>$name, 'id != ' => $id));

						if ($alreadyCat) {
						$output['status'] = 0;
						$output['message'] = '<div class="alert alert-danger">Category already exist !</div>';
						}
						elseif($this->common_model->UpdateData('gift_category',array('id'=>$id),$update)){
							
							$output['status'] = 1;
							$this->session->set_flashdata('msgs','<div class="alert alert-success">category has been updated successfully.</div>');	
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

	public function ring()
	{
		$data["ring"] = $this->common_model->GetSingleData('ringTone', array('id' => 1));
		$this->load->view('admin/ring', $data);
	} 

	public function editRing()
	{	
		//print_r($this->input->post()); die;
		$id = $this->input->post('id');

		//$allowedExts = array("mp3");
		$name_array = explode('.',$_FILES['file']['name']);
        $extension = end($name_array);

		//$file= $_FILES['file']['name'];
		$fileNewName=time().rand().$extension;

		$update["file"]= $fileNewName;
		if ($extension != "mp3") {
			$this->session->set_flashdata('msgs','<div class="alert alert-danger">Unvalid file.</div>');
		}
		elseif ($this->common_model->UpdateData('ringTone', array('id'=>$id),$update) && move_uploaded_file($_FILES["file"]["tmp_name"], "upload/rings/". $fileNewName)) {
	        
	        $this->session->set_flashdata('msgs','<div class="alert alert-success">Ringtone updated successfully.</div>');
	                 ;
	                 		
	                 	} else {
	                 		$this->session->set_flashdata('msgs','<div class="alert alert-danger">Something went wrong.</div>');
	                    	
	                    }   
               redirect('admin/ring');  	
	}

	public function AddLevel()
	{
		$this->form_validation->set_rules('level','level','trim|required');
		$this->form_validation->set_rules('daimond','daimond ','trim|required');
		if($this->form_validation->run()){
			$insert['level'] = $this->input->post('level');
			$insert['daimond'] = $this->input->post('daimond');
			$insert['created_at'] = date('Y-m-d H:i:s');
			$insert['gender'] = $this->input->post('page');

			//$Id = $this->input->post('leveId');

			$run = $this->common_model->InsertData('level',$insert);

			if($run){
							
							$output['status'] = 1;
							$this->session->set_flashdata('msgs','<div class="alert alert-success">Level has been added successfully.</div>');	
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

	public function deleteLevel()
	{
		$id = $_GET["Levelid"];
		$gender = $_GET["gender"];
		$run = $this->common_model->DeleteData('level',array('id'=>$id));
            //echo $this->db->last_query();
			if($run){

				$this->session->set_flashdata('msgs','<div class="alert alert-success">Success! Level has been deleted successfully.</div>');
				
			} else {
			    $this->session->set_flashdata('msgs','<div class="alert alert-danger">Something is Worng.</div>');
			}	
		if ($gender == "male") {
		redirect('admin/level-boys');
		}	else {
		redirect('admin/level-girls');	
		}
	}

}

?>
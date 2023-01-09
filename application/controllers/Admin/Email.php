<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
/*
*/
/*
*/
error_reporting(0);
class Email extends CI_Controller
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
	$this->load->view('admin/email');
	}
	public function GetUserData()
	{
		$user_type = $this->input->post('user_type');
		if ($user_type == "1") {
		$where = "user_type='1' and (gender='male' or gender='female')";
		}
		if ($user_type == "2") {
		$where = "user_type='1' and gender='male'";
		}
		if ($user_type == "3") {
		$where = "user_type='1' and gender='female'";
		}
		if ($user_type == "4") {
		$where = "user_type='2' and (gender='male' or gender='female')";
		}
		if ($user_type == "5") {
		$where = "user_type='2' and gender='male'";
		}
		if ($user_type == "6") {
		$where = "user_type='2' and gender='female'";
		}




		$data = $this->common_model->GetAllData('users',$where,$ob=null,$obc=null,$limit=null,$offset=null,array('id', 'nickname','gender'));
		
		foreach ($data as $key => $value) {
		echo "<option value='".$value["id"]."'>".$value["nickname"]."</option>";
		}
	}

	public function SendMail()
	{	
		$user_id = $this->input->post('user_id');
		$message = $this->input->post('message');
		foreach($user_id as $user){

			$userData = $this->common_model->GetSingleData('users', array('id'=>$user));			

			$subject = "Messsage from Admin"; 
					//$contant= 'Hello, '.$userData["nickname"].' <br><br>';					
					
					$email_body = str_replace("[username]",$userData["nickname"],$message);
					$email_body1 = str_replace("[email]",$userData['email'],$email_body);
					$email_body3 = str_replace("assets/admin/EmailImg/",site_url()."assets/admin/EmailImg/",$email_body1);
					$contant ='<p class="text-center">'.$email_body3.'</p>';
	 
					//$contant .='<p class="text-center">Password: <b>'.$data['password'].'</b></p>';
						
					$run = $this->common_model->SendMailCustom($userData['email'],$subject,$contant);

		}
		$output['status'] = 1;
		$this->session->set_flashdata('msgs','<div class="alert alert-success">Messsage has been send successfully.</div>');	
		echo json_encode($output);
	}

	public function email_builder_forget()
	{
		$data["data"] = $this->common_model->GetSingleData('email_template', array('id'=>1));
		$this->load->view('admin/email-builder-forget', $data);
	}

	public function email_builder_otp()
	{
		$data["data"] = $this->common_model->GetSingleData('email_template', array('id'=>2));
		$this->load->view('admin/email-builder-otp', $data);
	}

	public function email_builder_signup()
	{
		$data["data"] = $this->common_model->GetSingleData('email_template', array('id'=>3));
		$this->load->view('admin/email-builder-signup', $data);
	}

	public function email_block_agent()
	{
		$data["data"] = $this->common_model->GetSingleData('email_template', array('id'=>6));
		$this->load->view('admin/email-block-agent', $data);
	}

	public function email_edit_agent()
	{
		$data["data"] = $this->common_model->GetSingleData('email_template', array('id'=>7));
		$this->load->view('admin/email-edit-agent', $data);
	}

	public function EmailForgetUpdate()
	{
		
		$this->form_validation->set_rules('text','content','trim|required');
		if($this->form_validation->run()){
			$update["text"]= $this->input->post('text');

			$run = $this->common_model->UpdateData('email_template', array('id'=>1), $update);

				if ($run) {
				$this->session->set_flashdata('msgs','<div class="alert alert-success">Template has been updated successfully.</div>');
				} else {
				$this->session->set_flashdata('msgs','<div class="alert alert-danger">Something went wrong.</div>');
				}
			}//end if form validation
		else {
			$this->session->set_flashdata('msgs','<div class="alert alert-danger">'.validation_errors().'</div>');
		}
		redirect('admin/email-builder-forget');
	}

	public function EmailOTPUpdate()
	{
		
		$this->form_validation->set_rules('text','content','trim|required');
		if($this->form_validation->run()){
			$update["text"]= $this->input->post('text');

			$run = $this->common_model->UpdateData('email_template', array('id'=>2), $update);

				if ($run) {
				$this->session->set_flashdata('msgs','<div class="alert alert-success">Template has been updated successfully.</div>');
				} else {
				$this->session->set_flashdata('msgs','<div class="alert alert-danger">Something went wrong.</div>');
				}
			}//end if form validation
		else {
			$this->session->set_flashdata('msgs','<div class="alert alert-danger">'.validation_errors().'</div>');
		}
		redirect('admin/email-builder-otp');
	}

	public function EmailSignUpUpdate()
	{
		
		$this->form_validation->set_rules('text','content','trim|required');
		if($this->form_validation->run()){
			$update["text"]= $this->input->post('text');

			$run = $this->common_model->UpdateData('email_template', array('id'=>3), $update);

				if ($run) {
				$this->session->set_flashdata('msgs','<div class="alert alert-success">Template has been updated successfully.</div>');
				} else {
				$this->session->set_flashdata('msgs','<div class="alert alert-danger">Something went wrong.</div>');
				}
			}//end if form validation
		else {
			$this->session->set_flashdata('msgs','<div class="alert alert-danger">'.validation_errors().'</div>');
		}
		redirect('admin/email-builder-signup');
	}

	public function email_builder_agent($value='')
		{
			$data["data"] = $this->common_model->GetSingleData('email_template', array('id'=>4));
			$this->load->view('admin/email-builder-agent', $data);
		}
	public function email_builder_hostess($value='')
		{
			$data["data"] = $this->common_model->GetSingleData('email_template', array('id'=>5));
			$this->load->view('admin/email-builder-hostess', $data);
		}
			
	public function email_builder_agent_Update()
		{
			$this->form_validation->set_rules('text','content','trim|required');
		if($this->form_validation->run()){
			$update["text"]= $this->input->post('text');

			$run = $this->common_model->UpdateData('email_template', array('id'=>4), $update);

				if ($run) {
				$this->session->set_flashdata('msgs','<div class="alert alert-success">Template has been updated successfully.</div>');
				} else {
				$this->session->set_flashdata('msgs','<div class="alert alert-danger">Something went wrong.</div>');
				}
			}//end if form validation
		else {
			$this->session->set_flashdata('msgs','<div class="alert alert-danger">'.validation_errors().'</div>');
		}
		redirect('admin/email-builder-agent');
		}			


		public function email_builder_hostess_Update()
		{
			$this->form_validation->set_rules('text','content','trim|required');
		if($this->form_validation->run()){
			$update["text"]= $this->input->post('text');

			$run = $this->common_model->UpdateData('email_template', array('id'=>5), $update);

				if ($run) {
				$this->session->set_flashdata('msgs','<div class="alert alert-success">Template has been updated successfully.</div>');
				} else {
				$this->session->set_flashdata('msgs','<div class="alert alert-danger">Something went wrong.</div>');
				}
			}//end if form validation
		else {
			$this->session->set_flashdata('msgs','<div class="alert alert-danger">'.validation_errors().'</div>');
		}
		redirect('admin/email-builder-hostess');
		}


		function addeditorimage(){
		$url = array(
			site_url()
		);
		reset($_FILES);
		$temp = current($_FILES);
		if (is_uploaded_file($temp['tmp_name'])) {
			if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
        header("HTTP/1.1 400 Invalid file name,Bad request");
        return;
			}
			// Validating File extensions
			if (! in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif","jpg", "png"))) {
        header("HTTP/1.1 400 Not an Image");
        return;
			}
			$filesname = site_url()."assets/admin/EmailImg/" . $temp['name'];
			$fileName = "assets/admin/EmailImg/" . $temp['name'];
			move_uploaded_file($temp['tmp_name'], $fileName);
			// Return JSON response with the uploaded file path.
			echo json_encode(array(
        'file_path' => $filesname
			));
		}
	}


	public function email_builder_agent_Block()
		{
			$this->form_validation->set_rules('text','content','trim|required');
		if($this->form_validation->run()){
			$update["text"]= $this->input->post('text');

			$run = $this->common_model->UpdateData('email_template', array('id'=>6), $update);

				if ($run) {
				$this->session->set_flashdata('msgs','<div class="alert alert-success">Template has been updated successfully.</div>');
				} else {
				$this->session->set_flashdata('msgs','<div class="alert alert-danger">Something went wrong.</div>');
				}
			}//end if form validation
		else {
			$this->session->set_flashdata('msgs','<div class="alert alert-danger">'.validation_errors().'</div>');
		}
		redirect('admin/email-block-agent');
		}


		public function emailBuilderChangeAgent()
		{
			$this->form_validation->set_rules('text','content','trim|required');
		if($this->form_validation->run()){
			$update["text"]= $this->input->post('text');

			$run = $this->common_model->UpdateData('email_template', array('id'=>7), $update);

				if ($run) {
				$this->session->set_flashdata('msgs','<div class="alert alert-success">Template has been updated successfully.</div>');
				} else {
				$this->session->set_flashdata('msgs','<div class="alert alert-danger">Something went wrong.</div>');
				}
			}//end if form validation
		else {
			$this->session->set_flashdata('msgs','<div class="alert alert-danger">'.validation_errors().'</div>');
		}
		redirect('admin/email-edit-agent');
		}

		public function email_builder_block_by()
		{
		$data["data"] = $this->common_model->GetSingleData('email_template', array('id'=>8));
		$this->load->view('admin/email-builder-block-by', $data);
		}

		public function email_builder_User_Block()
		{
			$this->form_validation->set_rules('text','content','trim|required');
				if($this->form_validation->run()){
					$update["text"]= $this->input->post('text');

					$run = $this->common_model->UpdateData('email_template', array('id'=>8), $update);

						if ($run) {
						$this->session->set_flashdata('msgs','<div class="alert alert-success">Template has been updated successfully.</div>');
						} else {
						$this->session->set_flashdata('msgs','<div class="alert alert-danger">Something went wrong.</div>');
						}
					}//end if form validation
				else {
					$this->session->set_flashdata('msgs','<div class="alert alert-danger">'.validation_errors().'</div>');
				}
				redirect('admin/email-builder-block-by');
		}

		public function email_builder_report_other($value='')
		{
			$data["data"] = $this->common_model->GetSingleData('email_template', array('id'=>9));
			$this->load->view('admin/email-builder-report-other', $data);	
		}

		public function email_builder_Other_user($value='')
		{
			$this->form_validation->set_rules('text','content','trim|required');
				if($this->form_validation->run()){
					$update["text"]= $this->input->post('text');

					$run = $this->common_model->UpdateData('email_template', array('id'=>9), $update);

						if ($run) {
						$this->session->set_flashdata('msgs','<div class="alert alert-success">Template has been updated successfully.</div>');
						} else {
						$this->session->set_flashdata('msgs','<div class="alert alert-danger">Something went wrong.</div>');
						}
					}//end if form validation
				else {
					$this->session->set_flashdata('msgs','<div class="alert alert-danger">'.validation_errors().'</div>');
				}
				redirect('admin/email-builder-report-other');
		}

}

?>
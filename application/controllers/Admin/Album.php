<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
/*
*/
/*
*/
error_reporting(0);
class Album extends CI_Controller
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
		$where = ' status=0';	
		if ($_REQUEST['start_date']) {
		$where .= " and DATE(create_date) = DATE('".$_REQUEST['start_date']."')";
		}
		if ($_REQUEST['uniqueID']) {
		$where .= " and ( (select count(id) from users where users.id = album.user_id and users.uniqueID='".$_REQUEST['uniqueID']."') > 0 )";
		}
		$data["content"]='Pending';
		//$where .=" and (status = '0' or status ='2')";
		$data["data"] = $this->common_model->GetAllData('album',$where,'id','asc');
		$this->load->view('admin/album-list',$data);
	}

	public function album_approved($value='')
	{
		$where = '1=1';	
		if ($_REQUEST['start_date']) {
		$where .= " and DATE(create_date) = DATE('".$_REQUEST['start_date']."')";
		}
		if ($_REQUEST['uniqueID']) {
		$where .= " and ( (select count(id) from users where users.id = album.user_id and users.uniqueID='".$_REQUEST['uniqueID']."') > 0 )";
		}
		$where .=" and status='1'";
		$data["content"]='Approved';
		$data["data"] = $this->common_model->GetAllData('album',$where,'id','desc');
		$this->load->view('admin/album-list',$data);
	}

	public function album_rejected($value='')
	{
		$where = ' status=2';	
		if ($_REQUEST['start_date']) {
		$where .= " and DATE(create_date) = DATE('".$_REQUEST['start_date']."')";
		}
		if ($_REQUEST['uniqueID']) {
		$where .= " and ( (select count(id) from users where users.id = album.user_id and users.uniqueID='".$_REQUEST['uniqueID']."') > 0 )";
		}
		//$where .=" and status='1'";
		$data["content"]='Rejected';
		$data["data"] = $this->common_model->GetAllData('album',$where,'id','desc');
		$this->load->view('admin/album-list',$data);
	}
	public function Accept()
	{
		$id = $_GET["id"];
		$status = $_GET["status"];

		$update["status"] = $status;

		$run = $this->common_model->UpdateData('album', array('id'=>$id), $update);

		if ($run) {
			if ($status == 1) {
				$msg1 = "Accepted";
			} else {
				$msg1 = "Rejected";	
			}
			$this->session->set_flashdata('msgs','<div class="alert alert-success">Success! '.$msg1.' album.</div>');
		} else {
			$this->session->set_flashdata('msgs','<div class="alert alert-danger">Something went Wrong.</div>');
		}

		redirect('admin/album-list');
	}



	public function DeleteAlbum()
	{
		$id = $_GET["id"];
		$page = $_GET["page"];

		$run = $this->common_model->DeleteData('album', array('id'=>$id));

		if ($run) {
			$this->session->set_flashdata('msgs','<div class="alert alert-success">Success! Deleted album.</div>');
		} else {
			$this->session->set_flashdata('msgs','<div class="alert alert-danger">Something went Wrong.</div>');
		}

		redirect('admin/'.$page.'');
	}

	



}?>
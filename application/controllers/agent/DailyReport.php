<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class DailyReport extends CI_Controller {
  public function __construct() {
    parent::__construct();
    $this->db->query("set sql_mode = ''");
		$this->check_login();
  }
	
	public function check_login(){
		if(!$this->session->userdata('agent_id')){
			redirect('agent/login');
		}
	}
	public function logout(){
		session_destroy();
		redirect('agent/login');
	}

	public function index()
	{
		$this->load->view('agent/daily-report');
	}

	public function DailyReportSub()
	{
		$this->load->view('agent/daily-report-sub');
	}
	
} ?>
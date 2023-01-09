<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Coinbase extends CI_Controller {
  public function __construct() {
    parent::__construct();
    $this->db->query("set sql_mode = ''");

    //redirect('Admin');
  }
	
	
	public function success(){
		$myfile = fopen("coinbase.txt", "w") or die("Unable to open file!");
		$txt = json_encode($_REQUEST);
		fwrite($myfile, $txt);
		fclose($myfile);
		$this->load->view('site/coinbase_success');
	}
	
	public function cancel(){
		$this->load->view('site/coinbase_cancel');
	}
	
}
	
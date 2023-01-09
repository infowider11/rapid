<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 */
error_reporting(E_ALL);
class MY_Controller extends CI_Controller
{
	
	public function __construct() {
		parent::__construct();
		
		if($this->session->userdata('lang')=='eng'){
			$this->LANG = language_array('eng');
		} else {
			$this->LANG = language_array('swed');
		}
		
	}

}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cron extends CI_Controller {
  public function __construct() {
    parent::__construct();
    $this->db->query("set sql_mode = ''");
  }
	
	
	
	private function generateRandomString($length = 10) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwzyz9876543210';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return strtoupper($randomString);
	}
	
	
	public function SendPassphraseKey(){ 
	
		$today = date('Y-m-d');
		
		$data = $this->common_model->GetColumnName('recover_key',"status = 0 and DATE(end_date) < DATE('".$today."')",array('id','backup_id','email'),true);
		
		if(!empty($data)){
			foreach($data as $key => $value){
				
				$this->common_model->UpdateData('recover_key',array('id'=>$value['id']),array('token'=>'','status'=>'1'));
				
				$backup_keys = $this->common_model->GetColumnName('backup_keys',"id = '".$value['backup_id']."'",array('temp_data'));
				
				$email_template = $this->common_model->GetDataById('email_template',7);
				$email_body = $email_template['text'];
				$code = base64_decode($backup_keys['temp_data']);
				
				$buttonCode = '<p style="line-height: 23px;margin: 0px; font-size:20px; margin-top:15px;"><span style="color: #93f;font-size: 26px;font-weight: bold;background: #f1f1f1;padding: 7px;border: 2px dotted #93f;border-radius: 10px;">'.$code.'</span></p>';
				$email_body = str_replace("[CODE]",$buttonCode,$email_body);
				$subject = "KeyCoiner Passphrase Key";
				$this->common_model->SendMailCustom($value['email'],$subject,$email_body);
				
			}
		}
		
		echo json_encode(array('status'=>1));
		 
	}

		


	

	
	
	
} ?>
	
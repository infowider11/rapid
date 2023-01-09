<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
/*
*/
/*
*/
error_reporting(0);
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class Trade extends CI_Controller
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
	$data["trade"] = $this->common_model->GetAllData('user_trade_transfer','','id', 'desc');	
	$this->load->view('admin/trade', $data);
	}

	public function GetUser()
	{
		$UserId = $this->input->post('UserId');
		$UserData = $this->common_model->GetSingleData('users', array('uniqueID'=>$UserId));
		if ($UserData) {
		$output['status'] = 1;
		$output['message'] = '<div class="alert alert-success">'.$UserData["nickname"].'<div>';	
		} else {
		$output['status'] = 0;
		$output['message'] = "<div class='alert alert-danger'>No user found<div>";	
		}
		
		echo json_encode($output);
	}

	public function AddTrade()
	{
		//print_r($this->input->post()); die;
		$this->form_validation->set_rules('user_id','user id','trim|required');
		$this->form_validation->set_rules('trade_amount','trade amount','trim|required');
		if($this->form_validation->run()){

			$UserId = $this->input->post('user_id');
			$UserData = $this->common_model->GetSingleData('users', array('uniqueID'=>$UserId));

			$insert["send_by"]= 0;
			$insert["send_to"]= $UserData["id"];
			$insert["type"] = $type = $this->input->post('type');
			$insert["status"] = 1;
			$insert["diamonds"]= $trade_amount = $this->input->post('trade_amount');
			$insert['create_date'] = date('Y-m-d H:i:s');
			$insert['update_date'] = date('Y-m-d H:i:s');

		$run = $this->common_model->InsertData('user_trade_transfer', $insert);	

		if ($run) {
			$output['status'] = 1;
			$this->session->set_flashdata('msgs','<div class="alert alert-success">Trade amount has been added successfully.</div>');

			$data = $this->common_model->GetDataById('user_trade_transfer',$run);
			
			if($data && $data['send_by']==0 && $data['status']==1){
			
			
				
				$send_by = $data['send_by'];
				$send_to = $data['send_to'];
				$type = $data['type'];
				$diamond = $data['diamonds'];
				
				$amount = 0;
				
				/*$by_data = $this->common_model->GetColumnName('users',array('id'=>$send_by),array('id','trade_balance','free_call','gender','nickname'));*/
				
				$to_data = $this->common_model->GetColumnName('users',array('id'=>$send_to),array('id','diamond','free_call','gender','trade_balance','device_id'));
									
					if($type=='balance'){
					
						$update['diamond'] = $to_data['diamond']+$diamond;
						
						if($to_data['gender']=='male'){
							$total_diamond = $this->common_model->GetColumnName('transaction',array('user_id'=>$send_by,'type'=>1),array('SUM(diamond) as total'));
							$total_diamond = ($total_diamond['total']) ? $total_diamond['total'] : 0;
							$total_diamond = $total_diamond+$diamond;
							
							$get_level = $this->common_model->GetColumnName('level',array('gender'=>'male','daimond <= '=>$total_diamond),array('level'),false,'daimond','desc');
							
							$get_level = ($get_level['level']) ? $get_level['level'] : 0;
							
							$update['level'] = $get_level;
						}
					} else {
						$update['is_trade_account'] = 1;
						$update['trade_balance'] = $to_data['trade_balance']+$diamond;
					}
					
					
					$this->common_model->UpdateData('users',array('id'=>$send_to),$update);
					
					
					$insert1['user_id'] = $send_by;
					$insert1['diamond'] = $diamond;
					$insert1['transaction_id'] = $transaction_id = time();
					$insert1['payment_method'] = 'Transfer';
					$insert1['free_call'] = 0;
					$insert1['amount'] = 0;
					$insert1['type'] = 1;
					$insert1['date'] = date('Y-m-d H:i:s');
					$run1 = $this->common_model->InsertData('transaction',$insert1);

						
						$notArr['user_id'] = $send_to;
						$notArr['message'] = "Admin has sent you ".$diamond." diamond in your trade account.";
						$notArr['behalf_of'] = $send_by;
						$notArr['device_id'] = $to_data['device_id'];
						
						$notArr['other'] = array('screen'=>'trade_send','send_by'=>$send_by,'send_to'=>$send_to,'transaction_id'=>$transaction_id,'diamonds'=>$diamond,'sender_name'=>'Admin', 'type'=>'trade');
						$this->common_model->send_and_insert_notifi($notArr);								

						}

			} else {
				$output['status'] = 0;
				$output['message'] = "Something went wrong try again later.";
			}
		}	//end if form validation
		else {
			$output['status'] = 0;
			$output['message'] = validation_errors();
		}//end else form validation
		echo json_encode($output);
	}
	

	public function RemoveTrade()
	{
		$this->form_validation->set_rules('user_id','user id','trim|required');
		$this->form_validation->set_rules('trade_amount','trade amount','trim|required');
		if($this->form_validation->run()){
			$UserId = $this->input->post('user_id');
			$UserData = $this->common_model->GetSingleData('users', array('uniqueID'=>$UserId));
			$insert["user_id"]= $UserData["id"];
			$insert["trade_amount"] = $trade_amount = $this->input->post('trade_amount');
			$insert["type"] = 0;
			$insert["created_at"] = date('Y-m-d H:i:s');

		$UserData = $this->common_model->GetSingleData('users', array('uniqueID'=>$UserId));
		$NewTradeAmt = $UserData["trade_balance"]-$trade_amount;

			if($NewTradeAmt > 0){
			  $update["trade_balance"] = $NewTradeAmt;
			}
			elseif($NewTradeAmt < 0){
			  $update["trade_balance"] = 0;
			}
		$run = $this->common_model->InsertData('trade_history', $insert);	

		if ($run) {
			
			$this->common_model->UpdateData('users', array('uniqueID'=>$UserId), $update);
			$output['status'] = 1;
			$this->session->set_flashdata('msgs','<div class="alert alert-success">Trade amount has been removed successfully.</div>');
		}
		else {
			$output['status'] = 0;
			$output['message'] = '<div class="alert alert-danger">Something went Wrong!</div>';	
			}

			}//end if form validation
		else {
			$output['status'] = 0;
			$output['message'] = validation_errors();
		}//end else form validation
		echo json_encode($output);
	}

	public function guardian_list(){
		$data["data"] = $this->common_model->GetAllData('guardian','','id','desc');
		$this->load->view('admin/guardian-list', $data);
	}

	public function smtp_details(){
		$data["data"]= $this->common_model->GetSingleData('smtp',array('id'=>1));
		$this->load->view('admin/smtp-details', $data);

	}
	public function smtp_edit($value='')
	{
		$this->form_validation->set_rules('host','host','trim|required');
		$this->form_validation->set_rules('password','password','trim|required');
		$this->form_validation->set_rules('ssl','ssl','trim|required');
		$this->form_validation->set_rules('user_name','user name','trim|required');
		$this->form_validation->set_rules('port','port','trim|required');

		if($this->form_validation->run()){

			$update["host"] = $this->input->post('host');
			$update["password"] = $this->input->post('password');
			$update["ssl"] = $this->input->post('ssl');
			$update["user_name"] = $this->input->post('user_name');
			$update["port"] = $this->input->post('port');

			$run = $this->common_model->UpdateData('smtp', array('id'=>1), $update);

			if ($run) {

				$toz = "ameen.webwiders@gmail.com";
				$sub = "SMTP Test";
				$body = "This is test mail";
				$run1 = $this->SmtpTest($toz,$sub,$body);
							
			} else
			{ 
				$this->session->set_flashdata('msgs','<div class="alert alert-success">Something went Wrong.</div>');
			}

		}else {
			$this->session->set_flashdata('msgs','<div class="alert alert-danger">'.validation_errors().'</div>');
		}
	redirect('admin/smtp-details');	
	}

	public function SmtpTest($toz,$sub,$body) {
		
    $this->load->library('phpmailer_lib');
		
		$mail = $this->phpmailer_lib->load();
		
		$smtp = $this->common_model->GetDataById('smtp',1);
		
		$mail->isSMTP();
		$mail->Host     = $smtp['host'];
		$mail->SMTPAuth = true;
		$mail->Username = $smtp['user_name'];
		$mail->Password = $smtp['password'];
		$mail->SMTPSecure = $smtp['ssl'];
		$mail->Port     = $smtp['port'];
		$mail->CharSet = 'UTF-8';

		try {
		$mail->setFrom('info@webwiders.in', Project);
		
		$mail->addAddress($toz);
		
		$mail->Subject = $sub;
		
		$mail->isHTML(true);
		
    $mail->Body = $body;
    /*$run  = $mail->send();
    
	    if($run) {
	      return $mail->send();
	    } else {
	      return $mail->print_debugger();
	    }*/
	    
	    	$mail->send();
			  return $this->session->set_flashdata('msgs','<div class="alert alert-success">Details has been updated and SMTP tested successfully.</div>');
			} catch(Exception $e) {
			  return $this->session->set_flashdata('msgs','<div class="alert alert-danger">'.$e->errorMessage().'</div>');
			}

	  }

}

?>
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('application/libraries/opentok/vendor/autoload.php');
use OpenTok\OpenTok;
use OpenTok\MediaMode;
use OpenTok\ArchiveMode;
use OpenTok\Session;
use OpenTok\Role;


class MerchantApi extends CI_Controller {

  public function __construct() {

    parent::__construct();

		header("Access-Control-Allow-Origin:*");
		header('Content-type:application/json; charset=utf-8');
		$this->db->query("set sql_mode = ''");
		$this->load->model('Send_sms');
		$this->load->model('Api_model','API');
		$this->load->model('Common_model','common');
		date_default_timezone_set('Asia/Kolkata');

		$this->opentokApi='47222674';
		$this->opentokApiSecret='d2588cc21f78e3138e461f5bb58d64fe5693af83';
		$this->checkAuth();
		//https://www.webwiders.in/WEB01/rapid/MerchantApi
  }

	private function checkAuth(){
		$header = apache_request_headers();
		//print_r($header);
		if(isset($header['Auth-Api-Key'])){
			$merchant_id = $_REQUEST["merchant_id"];
			$keys = $this->common_model->GetColumnName('merchant',array('unique_id'=>$merchant_id),array('authorization_key'));
			if($keys['authorization_key']===$header['Auth-Api-Key']){
			} else {
				$response['status'] = 0;
				$response['message'] = 'You are not authorized';
				echo json_encode($response);
			   exit();
			}
		} else {
			$response['status'] = 0;
			$response['message'] = 'You are not authorized';
			echo json_encode($response);
			exit();
		}
	}
	public function test(){
	
		//$message = "123 is your one time password.\r\n Please enter the OTP to proceed.";
		//$this->common_model->SmtpTest('anil.webwiders@gmail.com','Verification Mail',$message);
	}
	
	public function index(){
		$output['status'] = 0;
		$output['message'] = 'Web service not found';
		echo json_encode($output);
	}

	public function GetMerchantDetail()
	{
		//https://www.webwiders.in/WEB01/rapid/MerchantApi/GetMerchantDetail?merchant_id=173606
		if(isset($_REQUEST['merchant_id']) && isset($_REQUEST['merchant_id'])){

			$id = $_REQUEST['merchant_id'];

				$Merchant = $this->common_model->GetSingleData('merchant', array('unique_id'=>$id));
				if ($Merchant) {
					
						$value1["id"]= $Merchant["id"];
						$value1["name"]= $Merchant["name"];
						$value1["email"]= $Merchant["email"];
						$value1["phone"]= $Merchant["phone"];
						$value1["unique_id"]= $Merchant["unique_id"];
						$value1["description"]= $Merchant["description"];
						$value1["diamond"]= $Merchant["diamond"];
						$value1["created_at"]= $Merchant["created_at"];

					$output['data'] = $value1;
					$output['status'] = 1;
					$output['message'] = 'Success';
				} else {
					$output['data'] = $result;
					$output['status'] = 0;
					$output['message'] = 'No record.';
					}

			} else {
			$output['status'] = 0;
			$output['message'] = 'Check Parameter';
		}
		
		
		echo json_encode($output);
	}

	public function CheckUserDetailById()
	{
		//https://www.webwiders.in/WEB01/rapid/MerchantApi/CheckUserDetailById?user_id=42
		 if(isset($_REQUEST['user_id']) && isset($_REQUEST['user_id'])){

			$id = $_REQUEST['user_id'];

				$user = $this->common_model->GetSingleData('users', array('uniqueID'=>$id));
				if ($user) {					
					$output['status'] = 1;
					$output['message'] = 'User Exist';
					$output['data'] = $this->very_short_profile($user["id"]);
					} else {
					$output['status'] = 0;
					$output['message'] = 'User Not Exist';
					}

			} else {
			$output['status'] = 0;
			$output['message'] = 'Check Parameter';
		}
		
		
		echo json_encode($output);
	}

	public function PurchaseDiamond()
	{
		 //https://www.webwiders.in/WEB01/rapid/MerchantApi/PurchaseDiamond?merchant_id=173606&user_id=42&diamond=2
		 if(isset($_REQUEST['user_id']) && isset($_REQUEST['user_id']) 
		 	&& isset($_REQUEST['merchant_id']) && isset($_REQUEST['merchant_id']) 
		 	&& isset($_REQUEST['diamond']) && isset($_REQUEST['diamond'])
		 	&& isset($_REQUEST['status']) && isset($_REQUEST['status']) ) {

			$user_id = $_REQUEST['user_id'];
			$merchant_id = $_REQUEST['merchant_id'];
			$diamond = $_REQUEST['diamond'];
			$status = $_REQUEST['status'];
			$merchant = $this->common_model->GetSingleData('merchant', array('unique_id'=>$merchant_id));
			if ($merchant) {
				 	$id = $merchant['id'];
				 	$insert["merchant_id"] = $id;
				 	$insert["type"] = 2;
				 	$insert["diamond"] = $diamond;
				 	$insert["status"] = $status;
				 	$insert["created_at"] = date('Y-m-d H:i:s'); 

				 	if(isset($_FILES['payment_receipt'])){
					$config['upload_path']="assets/payment_receipt/";
					$config['allowed_types'] = '*';
					$config['encrypt_name']=true;
					$this->load->library("upload",$config);
					if($this->upload->do_upload('payment_receipt')) {
					$u_profile=$this->upload->data("file_name");
					$insert['payment_receipt'] = "assets/payment_receipt/".$u_profile;
					} 
					}
					$run = $this->common_model->InsertData('merchant_diamond', $insert);


						 	if ($run) {
						 			
						 			if ($status == 1) {
						 			$update["diamond"] = $merchant["diamond"] - $diamond;
						 			$this->common_model->UpdateData('merchant', array('id'=>$id), $update);
						 			
						 			$userdata = $this->common_model->GetColumnName('users',array('id'=>$user_id),array('id','diamond','free_call','device_id','gender'));
										
										
										$update1['diamond'] = $userdata['diamond']+$diamond;
										//$update['free_call'] = $userdata['free_call']+$free_call;
										
										if($userdata['gender']=='male'){
											$total_diamond = $this->common_model->GetColumnName('transaction',array('user_id'=>$user_id,'type'=>1,'status'=>1),array('SUM(diamond) as total'));
											$total_diamond = ($total_diamond['total']) ? $total_diamond['total'] : 0;
											$total_diamond = $total_diamond+$diamond;
											
											$get_level = $this->common_model->GetColumnName('level',array('gender'=>'male','daimond <= '=>$total_diamond),array('level'),false,'daimond','desc');
											
											$get_level = ($get_level['level']) ? $get_level['level'] : 0;
											
											$update1['level'] = $get_level;
										}
										
										
									$this->common_model->UpdateData('users',array('id'=>$user_id),$update1);
										$insert1['user_id'] = $user_id;
										$insert1['diamond'] = $diamond;
										$insert1['transaction_id'] = $transaction_id = time();
										$insert1['payment_method'] = 'Purchased';
										//$insert['free_call'] = $free_call;
										//$insert['amount'] = $amount;
										$insert1['type'] = 1;
										$insert1['status'] = 1;
										$insert1['date'] = date('Y-m-d H:i:s');
										$insert1['message'] = 'Added by purchasing';
									$this->common_model->InsertData('transaction',$insert1);

										$notArr['user_id'] = $user_id;
										$notArr['message'] = "Admin added ".$diamond." diamonds in your balance";
										$notArr['behalf_of'] = 0;
										$notArr['device_id'] = $userdata['device_id'];
										
										$notArr['other'] = array('screen'=>'diamond_added','send_by'=>0,'send_to'=>$user_id, 'diamonds'=>$diamond,'sender_name'=>'', 'admin'=>'1');
									$this->common_model->send_and_insert_notifi($notArr);

									$update2['user_id'] = $user_id;
									$this->common_model->UpdateData('merchant_diamond',array('id'=>$run), $update2);

									$output['status'] = 1;
									$output['message'] = 'Success, Purchased';	
									} else {
									$output['status'] = 0;
									$output['message'] = 'Failed';
									}
						 		 	
						 	} else {
						 			$output['status'] = 0;
									$output['message'] = 'Something went wrong';	
						 	}
				} else {
					$output['status'] = 0;
					$output['message'] = 'Merchant not found';	
				}
			} else {
			$output['status'] = 0;
			$output['message'] = 'Check Parameter';
		}
		
		
		echo json_encode($output);
	}

	private function very_short_profile($id=null){
		
		$data = $this->common_model->GetDataById('users',$id);
		if ($data) {

			$output['id'] = $data['id'];
			$output['uniqueID'] = $data['uniqueID'];
			$output['nickname'] = $data['nickname'];
			$output['introduction'] = $data['introduction'];
			$output['dob'] = isset($data['dob']) ? date('Y-m-d',strtotime($data['dob'])) : '';
			$output['gender'] = $data['gender'];
			$output['coin'] = $data['coin'];
			$output['diamond'] = $data['diamond'];
			$output['avg_rate'] = $data['avg_rate'];
			$output['created_at'] = $data['created_at'];

			return $output;
		 } 
	}
}
	?>
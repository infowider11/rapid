<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('application/libraries/opentok/vendor/autoload.php');
use OpenTok\OpenTok;
use OpenTok\MediaMode;
use OpenTok\ArchiveMode;
use OpenTok\Session;
use OpenTok\Role;


class Api extends CI_Controller {

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

		//https://www.webwiders.in/WEB01/rapid/api
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
	
	public function GetUserRatingList(){
		
		if(isset($_REQUEST['user_id']) && isset($_REQUEST['sessionId'])){
			
			$user_id = $_REQUEST['user_id'];
			$sessionId = $_REQUEST['sessionId'];
			
			$avg_rate = $this->common_model->GetColumnName('ratings',array('rate_to'=>$user_id),array('AVG(rating) as total'));
			
			$avg_rate = ($avg_rate && $avg_rate['total']) ? $avg_rate['total'] : 0;
			$avg_rate = round($avg_rate,1);
			
			$ratings = $this->common_model->GetColumnName('ratings',array('rate_to'=>$user_id),Null,true,'id','desc','category');
			
			$result = array();
			$i=0;
			if(!empty($ratings)){
				foreach($ratings as $key => $value){
					
					$categoryName = $this->common_model->GetDataById('rating_category',$value['category']);
					if($categoryName){
						
						$categoryCount = $this->common_model->GetColumnName('ratings',array('rate_to'=>$user_id,'category'=>$value['category']),array('count(rating) as total'));
						
						$result[$i]['category'] = $categoryName['name'];
						$result[$i]['count'] = $categoryCount['total'];
						$i++;
					}
					
					
				}
			}
			
			$output['status'] = 1;
			$output['message'] = 'Success';
			$output['data'] = $result;
			$output['avg_rate'] = $avg_rate;
			
		} else {
			$output['status'] = 0;
			$output['message'] = 'Check Parameter';
		}
		
		
		echo json_encode($output);
		
	}
	
	public function MakeRating(){
		
		if(isset($_REQUEST['rate_by']) && isset($_REQUEST['rate_to']) && isset($_REQUEST['rating']) && isset($_REQUEST['category']) && isset($_REQUEST['call_id'])){
			
			$insert['rate_by'] = $_REQUEST['rate_by'];
			$insert['rate_to'] = $_REQUEST['rate_to'];
			$insert['rating'] = $_REQUEST['rating'];
			$insert['category'] = $_REQUEST['category'];
			$insert['call_id'] = $_REQUEST['call_id'];
			$insert['comment'] = isset($_REQUEST['comment']) ? $_REQUEST['comment'] : '';
			$insert['create_date'] = date('Y-m-d H:i:s');
			
			$run = $this->common_model->InsertData('ratings',$insert);
			
			$avg_rate = $this->common_model->GetColumnName('ratings',array('rate_to'=>$_REQUEST['rate_to']),array('AVG(rating) as total'));
			
			$avg_rate = ($avg_rate && $avg_rate['total']) ? $avg_rate['total'] : 0;
			$avg_rate = round($avg_rate,1);
			$this->common_model->UpdateData('users',array('id'=>$_REQUEST['rate_to']),array('avg_rate'=>$avg_rate));
			
			$output['status'] = 1;
			$output['message'] = 'Thankyou for rating.';
			
		} else {
			$output['status'] = 0;
			$output['message'] = 'Check Parameter';
		}
		
		
		echo json_encode($output);
		
	}
	
	public function getRatingCategory(){
		$where = "1=1";
		if(isset($_REQUEST['gender']) && !empty($_REQUEST['gender'])){
			$where .= " and gender = '".$_REQUEST['gender']."'";
		}
		
		$data = $this->common_model->GetAllData('rating_category',$where,'rating','asc');
		$output['data'] = $data;
		$output['status'] = 1;
		$output['message'] = 'Success';
		
		echo json_encode($output);
		
	}
	
	private function auto_transalate($from,$to,$text){
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://translation.googleapis.com/language/translate/v2?key='.GOOGLE_API,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => array(
				'q' => $text,
				//'source' => $from,
				'target' => $to,
				'format' => 'text'
				),
		));

		$response = curl_exec($curl);

		if(curl_errno($curl)){
			$err = 'Curl error: ' . curl_error($curl);
			$res['status'] = 0;
			$res['error'] = $err;
		} else{
			$res = json_decode($response);
		}
		curl_close ($curl);
		return $res;
	}
	
	public function SendWithdrawalRequest(){
		//https://www.webwiders.in/WEB01/rapid/api/SendWithdrawalRequest?user_id=3
		
		if(isset($_REQUEST['user_id']) && isset($_REQUEST['country']) && isset($_REQUEST['amount']) && isset($_REQUEST['payment_id']) && isset($_REQUEST['payment_data'])){
			
		$user_id = $_REQUEST['user_id'];
		$country = $_REQUEST['country'];
		$amount = $_REQUEST['amount'];
		$payment_id = $_REQUEST['payment_id'];
		$payment_data = $_REQUEST['payment_data'];
		
		
		$userdata = $this->common_model->GetColumnName('users',"id = $user_id",array('wallet'));

		
		if($userdata){
			$check_last_request = $this->common_model->GetColumnName('withdrawal',"user_id = $user_id and status = 0",array('id'));
			if(!$check_last_request){
				if($userdata['wallet'] >= $amount){
					
					$insert['user_id'] = $user_id;
					$insert['amount'] = $amount;
					$insert['country'] = $country;
					$insert['payment_id'] = $payment_id;
					$insert['payment_data'] = serialize($payment_data);
					$insert['status'] = 0;
					$insert['create_date'] = date('Y-m-d H:i:s');
					$insert['update_date'] = date('Y-m-d H:i:s');
					
					
					$this->common_model->InsertData('withdrawal',$insert);
					$output['status'] = 1;
					$output['message'] = 'Your withdrawal request as been submitted successfully.';
					
				} else {
					$output['status'] = 0;
					$output['message'] = 'Insufficient amount in your wallet.';
				}
			
			} else {
				$output['status'] = 0;
				$output['message'] = 'Your previews request already in pending.';
			}
		
		} else {
			$output['status'] = 0;
			$output['message'] = 'Something went wrong.';
		}
		
	
		
		
		} else {
			$output['status'] = 0;
			$output['message'] = 'Check Parameter	';
		}
		
		echo json_encode($output);
	}
	
	public function GetPaymentSetting(){
		//https://www.webwiders.in/WEB01/rapid/api/GetPaymentSetting
		

		$data = $this->common_model->GetColumnName('admin',"id = 1",array('min_withdrawal','max_withdrawal','coin_rate','album_limit'));

		$output['data'] = $data;
		$output['status'] = 1;
		$output['message'] = 'Success';
		
	
		
		echo json_encode($output);
	}
		
	public function MyWithdrawalHistory(){
		//https://www.webwiders.in/WEB01/rapid/api/MyWithdrawalHistory?user_id=3
		
		if(isset($_REQUEST['user_id'])){
			
		$user_id = $_REQUEST['user_id'];
		
		
		$data = $this->common_model->GetAllData('withdrawal',"user_id=$user_id",'id','desc');
		$result = array();
		
		if(!empty($data)){
			foreach($data as $key => $value){
				$result[$key] = $value;
				
				
				$payment_data = unserialize($value['payment_data']);
				$result2 = array();
				foreach($payment_data as $key1 => $value1){
					$result2[$key1]['payment_data'] = $value1;
					$result2[$key1]['payment_form_field'] = $this->common_model->GetDataById('payment_form_field',$value1['id']);
					
					
				}
				$result[$key]['payment_data'] = $result2;
			}
		}
		
	
		$output['data'] = $result;
		$output['status'] = 1;
		$output['message'] = 'Success!';
		
		} else {
			$output['status'] = 0;
			$output['message'] = 'Check parameter';
		}
		
		echo json_encode($output);
	}
	
	public function GetWithdrawalForm(){
		//https://www.webwiders.in/WEB01/rapid/api/GetLevelList?user_id=3
		
		if(isset($_REQUEST['user_id']) && isset($_REQUEST['country'])){
			
		$user_id = $_REQUEST['user_id'];
		$country = $_REQUEST['country'];
		
		
		$data = $this->common_model->GetAllData('payment_form',"FIND_IN_SET($country,payment_form.country)!=0",'id','desc');
		$result = array();
		
		if(!empty($data)){
			foreach($data as $key => $value){
				$result[$key] = $value;
				
				$payment_form_field = $this->common_model->GetAllData('payment_form_field',"payment_form_id = ".$value['id']."");
				
				$result[$key]['payment_form_field'] = $payment_form_field;
				
			}
		}
		
	
		$output['data'] = $result;
		$output['status'] = 1;
		$output['message'] = 'Success!';
		
		} else {
			$output['status'] = 0;
			$output['message'] = 'Check parameter';
		}
		
		echo json_encode($output);
	}
	
	public function GetLevelList(){
		//https://www.webwiders.in/WEB01/rapid/api/GetLevelList?user_id=3
		
		if(isset($_REQUEST['user_id']) && isset($_REQUEST['gender'])){
			
		$user_id = $_REQUEST['user_id'];
		$gender = $_REQUEST['gender'];
		
		$data = $this->common_model->GetAllData('level',"gender = '".$gender."'",'id','asc');
		$result = array();
		
		if(!empty($data)){
			foreach($data as $key => $value){
				$result[$key] = $value;
				
				if($value['icon']){
					$result[$key]['icon'] = site_url().'assets/admin/levelIcone/'.$value['icon'];
				} else {
					$result[$key]['icon'] = site_url().'upload/default_Image.png';
				}
				
				if($value['privilege']){
				
					$level_privilege = $this->common_model->GetAllData('level_privilege',"id in (".$value['privilege'].")");
					$result[$key]['level_privilege'] = $level_privilege;
				} else {
					$result[$key]['level_privilege'] = array();
				}
				
			}
		}
		
		if($_REQUEST['gender']=='male'){
			$total_diamond = $this->common_model->GetColumnName('transaction',array('user_id'=>$user_id,'type'=>1),array('SUM(diamond) as total'));
			$total_diamond = ($total_diamond['total']) ? $total_diamond['total'] : 0;
		} else {
			$total_coin = $this->common_model->GetColumnName('coin_transaction',array('user_id'=>$user_id),array('SUM(coin) as total'));
			$total_coin = ($total_coin['total']) ? $total_coin['total'] : 0;
			$total_diamond = $total_coin;
		}
		
		
		
		
		$All_privilege = $this->common_model->GetAllData('level_privilege');
		
		$output['total_diamond'] = $total_diamond;
		$output['All_privilege'] = $All_privilege;
		$output['data'] = $result;
		$output['status'] = 1;
		$output['message'] = 'Success!';
		
		} else {
			$output['status'] = 0;
			$output['message'] = 'Check parameter';
		}
		
		echo json_encode($output);
	}
	
	public function MySettlementList(){
		//https://www.webwiders.in/WEB01/rapid/api/MySettlementList?user_id=3
		
		if(isset($_REQUEST['user_id'])){
			
			$user_id = $_REQUEST['user_id'];
		
			$data = $this->common_model->GetAllData('settlement',"user_id = '".$user_id."'",'id','asc');
			
			$output['data'] = $data;
			$output['status'] = 1;
			$output['message'] = 'Success!';
		
		} else {
			$output['status'] = 0;
			$output['message'] = 'Check parameter';
		}
		
		echo json_encode($output);
	}
	
	public function MyEarninigs(){
		//https://www.webwiders.in/WEB01/rapid/api/MyEarninigs?user_id=3
		
		if(isset($_REQUEST['user_id'])){
			
			$user_id = $_REQUEST['user_id'];
		
			$data = $this->common_model->GetAllData('coin_transaction',"user_id = '".$user_id."'",'id','asc');
			
			$output['data'] = $data;
			$output['status'] = 1;
			$output['message'] = 'Success!';
		
		} else {
			$output['status'] = 0;
			$output['message'] = 'Check parameter';
		}
		
		echo json_encode($output);
	}
		
	public function ClearNotification(){
		//https://www.webwiders.in/WEB01/rapid/api/ClearNotification?user_id=3
		
		if(isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])){
			
			$user_id = $_REQUEST['user_id'];
			
			$this->common_model->DeleteData('notification',array('user_id'=>$user_id));
			
			$output['status'] = 1;
			$output['message'] = 'Success!';
			
		} else {
			$output['status'] = 0;
			$output['message'] = 'Check parameter.';
		}
		echo json_encode($output);
	}
	
	public function GetDiamondsList(){
		//https://www.webwiders.in/WEB01/rapid/api/GetDiamondsList?user_id=3
		
		if(isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])){
			
			$user_id = $_REQUEST['user_id'];
			
			$data = $this->common_model->GetAllData('diamonds',null,'id','asc');
			
			$output['data'] = $data;
			$output['status'] = 1;
			$output['message'] = 'Success!';
			
		} else {
			$output['status'] = 0;
			$output['message'] = 'Check parameter.';
		}
		echo json_encode($output);
	}
	
	public function MarkAsReadNotification(){
		//https://www.webwiders.in/WEB01/rapid/api/MarkAsReadNotification?user_id=3
		
		if(isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])){
			
			$user_id = $_REQUEST['user_id'];
			
			$where['user_id'] = $user_id;
			
			$where = "user_id = $user_id";
			
			if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
				$where .= " and id in (".$_REQUEST['id'].")";
			}
			
			$this->common_model->UpdateData('notification',$where,array('is_read'=>1));
			
			$output['status'] = 1;
			$output['message'] = 'Success!';
			
		} else {
			$output['status'] = 0;
			$output['message'] = 'Check parameter.';
		}
		echo json_encode($output);
	}
	
	public function GetNotification(){
		//https://www.webwiders.in/WEB01/rapid/api/GetNotification?user_id=3
		
		if(isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])){
			
			$user_id = $_REQUEST['user_id'];
			
			$notification = $this->common_model->GetDataByOrderLimit('notification',array('user_id'=>$user_id),'id','desc',0,100);
			$unread = $this->common_model->GetColumnName('notification',array('user_id'=>$user_id,'is_read'=>0),array('count(id) as total'));
			
			$result = array();
			
			if(!empty($notification)){
				
				
				foreach($notification as $key => $value){
					$result[$key] = $value;
					$other = unserialize($value['other']);
					$result[$key]['other'] = $other;
					$result[$key]['create_date'] = time_ago($value['create_date']);
					
					$profile = site_url().'upload/default_Image.png';
					$title = '-';
					
				
					
					if($value['behalf_of']){
						$user = $this->common_model->GetColumnName('users',array('id'=>$value['behalf_of']),array('nickname','image','user_type','gender'));
						
						if($user){
							$title = $user['nickname'];
							if($user['image']){
								$profile = site_url().'upload/users/'.$user['image'];
							} else if($user['gender']=='male'){
								$profile = site_url().'upload/male.png';
							}
						}
						
					}
					
					if($other['screen']=='gift'){
						$gift_send_id = $other['gift_send_id'];
						
						$gift_send_data = $this->common_model->GetDataById('gift_send',$gift_send_id);
						
						if($gift_send_data){
							
						
							$result[$key]['gift_data'] = $this->gift_data($gift_send_data['gift_id']);;
							$result[$key]['gift_send_by'] = $this->short_profile($gift_send_data['send_by']);
							
						
						}
					}
					
					$result[$key]['profile'] = $profile;
					$result[$key]['title'] = $title;
				}
				
				$output['unread'] = ($unread) ? $unread['total'] : 0;
				$output['data'] = $result;
				$output['status'] = 1;
				$output['message'] = 'Success!';
				
			} else {
				$output['data'] = $result;
				$output['status'] = 0;
				$output['message'] = 'We did not find any records.';
			}
		} else {
			$output['status'] = 0;
			$output['message'] = 'Check parameter.';
		}
		echo json_encode($output);
	}
	
	public function GetTopEarners() {
		
		if (isset($_REQUEST['user_id'])) {
			 $date = date('Y-m-d');
			$where2 = '';
		
			
		
		
		
		if(isset($_REQUEST['filter']) && !empty($_REQUEST['filter'])){
			$filter = $_REQUEST['filter'];
			
			if($filter=='Today'){
				$where2 .= " and DATE(coin_transaction.create_date) = DATE('".$date."')";
			} else if($filter=='Week'){
				$where2 .= " and WEEK(coin_transaction.create_date) = WEEK('".$date."')";
			} else if($filter=='Month'){
				$where2 .= " and MONTH(coin_transaction.create_date) = MONTH('".$date."')";
			} else if($filter=='Year'){
				$where2 .= " and YEAR(coin_transaction.create_date) = YEAR('".$date."')";
			}
			
						
		}
		
		
		$where = "";
		if(isset($_REQUEST['country_filter']) && !empty($_REQUEST['country_filter'])){
			$country_filter = $_REQUEST['country_filter'];
			
			$where = " and users.country = $country_filter";
			
						
		}
			
			
			$user_id = $_REQUEST['user_id']; 
			$records = $this->common_model->GetColumnName('users',"(select count(id) from coin_transaction where coin_transaction.user_id = users.id $where2) > 0 $where",array('id',"(select SUM(coin) from coin_transaction where coin_transaction.user_id = users.id $where2) as coin"),true,"(select SUM(coin) from coin_transaction where coin_transaction.user_id = users.id $where2)",'desc');
			//$output['qe'] = $this->db->last_query();
			
			$result = array();
			if(!empty($records)){
				foreach($records as $key => $value){
					$records[$key]['total_earned_coin'] = $value['coin'];
					$records[$key]['user'] = $this->short_profile($value['id']);
				}
			}
			
			$output['data'] = $records;
			$output['status'] = 1;
			$output['message'] = "Success";
		} else {
			$output['status'] = 0;
			$output['message'] = "Check parameters";
		}
		echo json_encode($output);

	}
	
	public function GetTopReciever() {
		
		if (isset($_REQUEST['user_id'])) {
			$date = date('Y-m-d');
			$where2 = '';
		
			
		
		
		
			if(isset($_REQUEST['filter']) && !empty($_REQUEST['filter'])){
				$filter = $_REQUEST['filter'];
				
				if($filter=='Today'){
					$where2 = " and DATE(A.create_date) = DATE('".$date."')";
				} else if($filter=='Week'){
					$where2 = " and WEEK(A.create_date) = WEEK('".$date."')";
				} else if($filter=='Month'){
					$where2 = " and MONTH(A.create_date) = MONTH('".$date."')";
				} else if($filter=='Year'){
					$where2 = " and YEAR(A.create_date) = YEAR('".$date."')";
				}
				
			}
				
			if(isset($_REQUEST['country_filter']) && !empty($_REQUEST['country_filter'])){
				$country_filter = $_REQUEST['country_filter'];
				
				$where2 .= " and (select count(id) from users  where users.country = $country_filter and users.id=A.send_to) > 0";
							
			}
			
			$user_id = $_REQUEST['user_id']; 
			
			$gifts = $this->common_model->GetAllData('gift',null,'daimond','desc');
			$result = array();
			if(!empty($gifts)){
				
				foreach($gifts as $key1 => $value1){
			
					$sql = "select send_to,gift_id from gift_send as A where gift_id = '".$value1['id']."' $where2 group by send_to order by (select count(id) from  gift_send as B where B.gift_id = '".$value1['id']."' and B.send_to = A.send_to) desc";
					
					$run = $this->db->query($sql);
					
					
					
					
					
					if($run->num_rows() > 0){
						
						$value = $run->row_array();
						
						$gift_count = $this->common_model->GetColumnName('gift_send',array('send_to'=>$value['send_to'],'gift_id'=>$value1['id']),array('count(id) as total'));
						$result[$key1]['gift_count'] = $gift_count['total'];
						$result[$key1]['gift_data'] = $this->gift_data($value1['id']);
						$result[$key1]['user'] = $this->short_profile($value['send_to']);
					} else {
						$result[$key1]['gift_count'] = 0;
						$result[$key1]['gift_data'] = $this->gift_data($value1['id']);
						$result[$key1]['user'] = false;
					}
					
					
				
				}
				
				
			}
			
			$output['data'] = $result;
			$output['status'] = 1;
			$output['message'] = "Success";
		} else {
			$output['status'] = 0;
			$output['message'] = "Check parameters";
		}
		echo json_encode($output);

	}
		
	public function GetTopRecieverList() {
		
		if (isset($_REQUEST['user_id']) && isset($_REQUEST['gift_id'])) {
			$date = date('Y-m-d');
			$where2 = '';
			
			$user_id = $_REQUEST['user_id'];
			$gift_id = $_REQUEST['gift_id'];
		
			
		
		
		
		if(isset($_REQUEST['filter']) && !empty($_REQUEST['filter'])){
			$filter = $_REQUEST['filter'];
			
			if($filter=='Today'){
				$where2 = " and DATE(gift_send.create_date) = DATE('".$date."')";
			} else if($filter=='Week'){
				$where2 = " and WEEK(gift_send.create_date) = WEEK('".$date."')";
			} else if($filter=='Month'){
				$where2 = " and MONTH(gift_send.create_date) = MONTH('".$date."')";
			} else if($filter=='Year'){
				$where2 = " and YEAR(gift_send.create_date) = YEAR('".$date."')";
			}
			
		}
		
	
				
			if(isset($_REQUEST['country_filter']) && !empty($_REQUEST['country_filter'])){
				$country_filter = $_REQUEST['country_filter'];
				
				$where2 .= " and (select count(id) from users  where users.country = $country_filter and users.id=gift_send.send_to) > 0";
							
			}
			
			
			$sql = "select send_to,gift_id from gift_send where gift_id = $gift_id $where2 group by send_to order by MAX(diamond) desc";
			
			$run = $this->db->query($sql);
			
			$result = array();
			
			if($run->num_rows() > 0){
				foreach($run->result_array() as $key => $value){
					
					$gift_count = $this->common_model->GetColumnName('gift_send',array('send_to'=>$value['send_to'],'gift_id'=>$value['gift_id']),array('count(id) as total'));
					$records[$key]['gift_count'] = $gift_count['total'];
					$records[$key]['gift_data'] = $this->gift_data($value['gift_id']);
					$records[$key]['user'] = $this->short_profile($value['send_to']);
				}
			}
			
			$output['data'] = $records;
			$output['status'] = 1;
			$output['message'] = "Success";
		} else {
			$output['status'] = 0;
			$output['message'] = "Check parameters";
		}
		echo json_encode($output);

	}
	
	public function GetTopRecieverList2() {
		
		if (isset($_REQUEST['user_id']) && isset($_REQUEST['gift_id']) && isset($_REQUEST['viewer_id'])) {
			$date = date('Y-m-d');
			
			$viewer_id = $_REQUEST['viewer_id'];
			$user_id = $_REQUEST['user_id'];
			$gift_id = $_REQUEST['gift_id'];
			
			$where2 = " and send_to = $viewer_id";
		
			
		
		
		
			if(isset($_REQUEST['filter']) && !empty($_REQUEST['filter'])){
				$filter = $_REQUEST['filter'];
				
				if($filter=='Today'){
					$where2 = " and DATE(gift_send.create_date) = DATE('".$date."')";
				} else if($filter=='Week'){
					$where2 = " and WEEK(gift_send.create_date) = WEEK('".$date."')";
				} else if($filter=='Month'){
					$where2 = " and MONTH(gift_send.create_date) = MONTH('".$date."')";
				} else if($filter=='Year'){
					$where2 = " and YEAR(gift_send.create_date) = YEAR('".$date."')";
				}
				
			}
			
				
			$sql = "select send_by from gift_send where gift_id = $gift_id $where2 group by send_by order by id desc";
				
			$run = $this->db->query($sql);
				
			$result = array();
				
			if($run->num_rows() > 0){
				foreach($run->result_array() as $key => $value){
						
					$gift_count = $this->common_model->GetColumnName('gift_send',array('send_by'=>$value['send_by'],'gift_id'=>$gift_id),array('count(id) as total'));
					$records[$key]['gift_count'] = $gift_count['total'];
					
					$records[$key]['user'] = $this->short_profile($value['send_by']);
				}
			}
			
			
			$output['gift_data'] = $this->gift_data($gift_id);;
			$output['data'] = $records;
			$output['status'] = 1;
			$output['message'] = "Success";
		} else {
			$output['status'] = 0;
			$output['message'] = "Check parameters";
		}
		echo json_encode($output);

	}
	
	public function TopGifterList() {
		
		if (isset($_REQUEST['user_id']) && isset($_REQUEST['gift_id'])) {
			$date = date('Y-m-d');
			$where2 = '';
			
			$user_id = $_REQUEST['user_id'];
			$gift_id = $_REQUEST['gift_id'];
		
			
		
		
		
		if(isset($_REQUEST['filter']) && !empty($_REQUEST['filter'])){
			$filter = $_REQUEST['filter'];
			
			if($filter=='Today'){
				$where2 = " and DATE(gift_send.create_date) = DATE('".$date."')";
			} else if($filter=='Week'){
				$where2 = " and WEEK(gift_send.create_date) = WEEK('".$date."')";
			} else if($filter=='Month'){
				$where2 = " and MONTH(gift_send.create_date) = MONTH('".$date."')";
			} else if($filter=='Year'){
				$where2 = " and YEAR(gift_send.create_date) = YEAR('".$date."')";
			}
			
		}
		
			if(isset($_REQUEST['country_filter']) && !empty($_REQUEST['country_filter'])){
				$country_filter = $_REQUEST['country_filter'];
				
				$where2 .= " and (select count(id) from users  where users.country = $country_filter and users.id=gift_send.send_by) > 0";
							
			}
			
			
			$sql = "select send_by,gift_id from gift_send where gift_id = $gift_id $where2 group by send_by order by MAX(diamond) desc";
			
			$run = $this->db->query($sql);
			
			$result = array();
			
			if($run->num_rows() > 0){
				foreach($run->result_array() as $key => $value){
					
					$gift_count = $this->common_model->GetColumnName('gift_send',array('send_by'=>$value['send_by'],'gift_id'=>$value['gift_id']),array('count(id) as total'));
					$records[$key]['gift_count'] = $gift_count['total'];
					$records[$key]['gift_data'] = $this->gift_data($value['gift_id']);
					$records[$key]['user'] = $this->short_profile($value['send_by']);
				}
			}
			
			$output['data'] = $records;
			$output['status'] = 1;
			$output['message'] = "Success";
		} else {
			$output['status'] = 0;
			$output['message'] = "Check parameters";
		}
		echo json_encode($output);

	}
	
	public function TopGifterList2() {
		
		if (isset($_REQUEST['user_id']) && isset($_REQUEST['gift_id']) && isset($_REQUEST['viewer_id'])) {
			$date = date('Y-m-d');
			
			
			$viewer_id = $_REQUEST['viewer_id'];
			$user_id = $_REQUEST['user_id'];
			$gift_id = $_REQUEST['gift_id'];
		
			$where2 = " and send_by = $viewer_id";
		
		
		
		if(isset($_REQUEST['filter']) && !empty($_REQUEST['filter'])){
			$filter = $_REQUEST['filter'];
			
			if($filter=='Today'){
				$where2 = " and DATE(gift_send.create_date) = DATE('".$date."')";
			} else if($filter=='Week'){
				$where2 = " and WEEK(gift_send.create_date) = WEEK('".$date."')";
			} else if($filter=='Month'){
				$where2 = " and MONTH(gift_send.create_date) = MONTH('".$date."')";
			} else if($filter=='Year'){
				$where2 = " and YEAR(gift_send.create_date) = YEAR('".$date."')";
			}
			
		}
		
			
			
			
			$sql = "select send_to from gift_send where gift_id = $gift_id $where2 group by send_to order by MAX(diamond) desc";
			
			$run = $this->db->query($sql);
			
			$result = array();
			
			if($run->num_rows() > 0){
				foreach($run->result_array() as $key => $value){
					
					$gift_count = $this->common_model->GetColumnName('gift_send',array('send_to'=>$value['send_to'],'gift_id'=>$gift_id),array('count(id) as total'));
					$records[$key]['gift_count'] = $gift_count['total'];
					$records[$key]['user'] = $this->short_profile($value['send_to']);
				}
			}
			$output['gift_data'] = $this->gift_data($gift_id);;
			$output['data'] = $records;
			$output['status'] = 1;
			$output['message'] = "Success";
		} else {
			$output['status'] = 0;
			$output['message'] = "Check parameters";
		}
		echo json_encode($output);

	}
	
	public function GetTopGifter() {
		
		if (isset($_REQUEST['user_id'])) {
			$date = date('Y-m-d');
			$where2 = '';
		
			
		
		
		
			if(isset($_REQUEST['filter']) && !empty($_REQUEST['filter'])){
				$filter = $_REQUEST['filter'];
				
				if($filter=='Today'){
					$where2 = " and DATE(A.create_date) = DATE('".$date."')";
				} else if($filter=='Week'){
					$where2 = " and WEEK(A.create_date) = WEEK('".$date."')";
				} else if($filter=='Month'){
					$where2 = " and MONTH(A.create_date) = MONTH('".$date."')";
				} else if($filter=='Year'){
					$where2 = " and YEAR(A.create_date) = YEAR('".$date."')";
				}
				
			}
			
			if(isset($_REQUEST['country_filter']) && !empty($_REQUEST['country_filter'])){
				$country_filter = $_REQUEST['country_filter'];
				
				$where2 .= " and (select count(id) from users  where users.country = $country_filter and users.id=A.send_by) > 0";
				
							
			}
				
			
			$user_id = $_REQUEST['user_id']; 
			
			$gifts = $this->common_model->GetAllData('gift',null,'daimond','desc');
			$result = array();
			if(!empty($gifts)){
				
				foreach($gifts as $key1 => $value1){
			
					$sql = "select send_by,gift_id from gift_send as A where gift_id = '".$value1['id']."' $where2 group by send_by order by (select count(id) from  gift_send as B where B.gift_id = '".$value1['id']."' and B.send_by = A.send_by) desc";
					
					$run = $this->db->query($sql);
					
					
					
					
					
					if($run->num_rows() > 0){
						
						$value = $run->row_array();
						
						$gift_count = $this->common_model->GetColumnName('gift_send',array('send_by'=>$value['send_by'],'gift_id'=>$value1['id']),array('count(id) as total'));
						$result[$key1]['gift_count'] = $gift_count['total'];
						$result[$key1]['gift_data'] = $this->gift_data($value1['id']);
						$result[$key1]['user'] = $this->short_profile($value['send_by']);
					} else {
						$result[$key1]['gift_count'] = 0;
						$result[$key1]['gift_data'] = $this->gift_data($value1['id']);
						$result[$key1]['user'] = false;
					}
					
					
				
				}
				
				
			}
			
			$output['data'] = $result;
			$output['status'] = 1;
			$output['message'] = "Success";
		} else {
			$output['status'] = 0;
			$output['message'] = "Check parameters";
		}
		echo json_encode($output);
	}

	public function GetTopRich() {
		
		if (isset($_REQUEST['user_id'])) {
			
		 $date = date('Y-m-d');
			$where2 = ' and transaction.type = 1';
		
			
		
		
		
		if(isset($_REQUEST['filter']) && !empty($_REQUEST['filter'])){
			$filter = $_REQUEST['filter'];
			
			if($filter=='Today'){
				$where2 .= " and DATE(transaction.date) = DATE('".$date."')";
			} else if($filter=='Week'){
				$where2 .= " and WEEK(transaction.date) = WEEK('".$date."')";
			} else if($filter=='Month'){
				$where2 .= " and MONTH(transaction.date) = MONTH('".$date."')";
			} else if($filter=='Year'){
				$where2 .= " and YEAR(transaction.date) = YEAR('".$date."')";
			}
			
						
		}
		
		
			$where = "";
			if(isset($_REQUEST['country_filter']) && !empty($_REQUEST['country_filter'])){
				$country_filter = $_REQUEST['country_filter'];
				
				$where = " and users.country = $country_filter";
				
							
			}
			
			$user_id = $_REQUEST['user_id']; 
			$records = $this->common_model->GetColumnName('users',"(select count(id) from transaction where transaction.user_id = users.id $where2) > 0 $where",array('id',"(select SUM(diamond) from transaction where transaction.user_id = users.id $where2) as diamond"),true,"(select SUM(diamond) from transaction where transaction.user_id = users.id $where2)",'desc');
			//$output['qe'] = $this->db->last_query();
			
			$result = array();
			if(!empty($records)){
				foreach($records as $key => $value){
					$records[$key]['total_diamond'] = $value['diamond'];
					$records[$key]['user'] = $this->short_profile($value['id']);
				}
			}
			
			$output['data'] = $records;
			$output['status'] = 1;
			$output['message'] = "Success";
		} else {
			$output['status'] = 0;
			$output['message'] = "Check parameters";
		}
		echo json_encode($output);

	}

	public function GetParty() {
		
		if (isset($_REQUEST['user_id'])) {
			$user_id = $_REQUEST['user_id']; 
			$records = $this->common_model->GetAllData('party',array('status' => 1));
			//$output['qe'] = $this->db->last_query();
			
			$result = array();
			if(!empty($records)){
				foreach($records as $key => $value){
					
					$records[$key] = $value;
					
					$senderp = $this->short_profile($value['user_id']);
					
					$records[$key]['created_by'] = $senderp;

					$records1 = $this->common_model->GetAllData('party_users',array('party_id' => $value['id']));
					$result1 = array();
					if(!empty($records1)){
						foreach ($records1 as $key => $valueParty_users) {
							$result1[$key]["party_data"] = $valueParty_users;
							$result1[$key]["party_join_users"] = $this->short_profile($valueParty_users['user_id']);;
						}
						$records[$key]['party_users'] = $result1;
					} else {
						$records[$key]['party_users'] =$result1;
					}


					
				}
				$output['data'] = $records;
				$output['status'] = 1;
				$output['message'] = "Success";
			
			} else {
				$output['data'] = array();
				$output['status'] = 0;
				$output['message'] = "We did not find any records!";
			}
		} else {
			$output['status'] = 0;
			$output['message'] = "Check parameters";
		}
		echo json_encode($output);

	}

	public function party_detail() {
		if (isset($_REQUEST['party_id']) && isset($_REQUEST['user_id'])) {
			$partyId = $_REQUEST['party_id']; 
			$user_id = $_REQUEST['user_id']; 
			$records = $this->common_model->GetSingleData('party',array('id' => $partyId));
			$result = array();
			if(!empty($records)){
					
				$result["party"] = $records;
				
				$senderp = $this->short_profile($records['user_id']);
				
				$records['created_by'] = $senderp;

				$records1 = $this->common_model->GetAllData('party_users',array('party_id' => $records['id']));
				$result1 = array();
				if(!empty($records1)){
					foreach ($records1 as $key => $valueParty_users) {
						$result1["party_data"] = $valueParty_users;
						$result1["party_join_users"] = $this->short_profile($valueParty_users['user_id']);;
					}
					$records['party_users'][] = $result1;
				} else {
					$records['party_users'] =$result1;
				}


				$output['data'] = $records;
				$output['status'] = 1;
				$output['message'] = "Success";
			
			} else {
				$output['data'] = array();
					$output['status'] = 0;
			$output['message'] = "We did not find any records!";
			}
		} else {
			$output['status'] = 0;
			$output['message'] = "Check parameters";
		}
		
		echo json_encode($output);
	}

	public function reject_party_invitation() {

		if(isset($_REQUEST['user_id']) && isset($_REQUEST['party_id'])) {
			
			$user_id = $_REQUEST['user_id'];
			$party_id = $_REQUEST['party_id'];
			
			$check_party = $this->common_model->DeleteData('party_users',array('user_id'=>$user_id,'party_id'=>$party_id));
			
			$output['status'] = 1;
			$output['message'] = 'Invitation has been rejected successfully.';
			$output['invite_data'] = $check_party;
		}	else{

			$output['status'] = 0;
			$output['message'] = "Check parameters";
		}
		
		echo json_encode($output);

	}
	
	public function leave_party() {

		if(isset($_REQUEST['user_id']) && isset($_REQUEST['party_id'])) {
			
			$user_id = $_REQUEST['user_id'];
			$party_id = $_REQUEST['party_id'];
			
			$check_party = $this->common_model->UpdateData('party_users',array('user_id'=>$user_id,'party_id'=>$party_id),array('status'=>2,'leave_time'=>date('Y-m-d H:i:s'))); //status = 2 = leave
			
			$output['status'] = 2;
			$output['message'] = 'You have been leaved successfully.';
			$output['invite_data'] = $this->common_model->GetDataById('party',$party_id);
			
		}	else{

			$output['status'] = 0;
			$output['message'] = "Check parameters";
		}
		
		
		echo json_encode($output);

	}
	
	public function end_party() {

		if(isset($_REQUEST['user_id']) && isset($_REQUEST['party_id'])) {
			
			$user_id = $_REQUEST['user_id'];
			$party_id = $_REQUEST['party_id'];
			
			$check_party = $this->common_model->UpdateData('party',array('id'=>$party_id),array('status'=>2,'party_end_time'=>date('Y-m-d H:i:s'))); //status = 2 = end
			$check_party = $this->common_model->UpdateData('party_users',array('party_id'=>$party_id),array('status'=>2,'leave_time'=>date('Y-m-d H:i:s'))); //status = 2 = leave
			
			$output['status'] = 2;
			$output['message'] = 'Party has been ended successfully.';
			
		}	else{

			$output['status'] = 0;
			$output['message'] = "Check parameters";
		}
		
		
		echo json_encode($output);

	}
	
	public function accept_party_invitation() {

		if(isset($_REQUEST['user_id']) && isset($_REQUEST['party_id'])) {
			
			$user_id = $_REQUEST['user_id'];
			$party_id = $_REQUEST['party_id'];
			
			$check_party = $this->common_model->UpdateData('party_users',array('user_id'=>$user_id,'party_id'=>$party_id),array('status'=>1,'join_time'=>date('Y-m-d H:i:s')));
			
			$output['status'] = 2;
			$output['message'] = 'Invitation has been accepted successfully.';
			$output['invite_data'] = $this->common_model->GetDataById('party',$party_id);
			
		}	else{

			$output['status'] = 0;
			$output['message'] = "Check parameters";
		}
		
		echo json_encode($output);

	}
	
	public function invite_for_party() {

		if(isset($_REQUEST['user_id']) && isset($_REQUEST['party_id'])) {
			
			$user_id = $_REQUEST['user_id'];
			$party_id = $_REQUEST['party_id'];
			
			$check_party = $this->common_model->GetSingleData('party_users',array('user_id'=>$user_id,'party_id'=>$party_id));
			
			if($check_party){
				$output['status'] = 2;
				$output['message'] = 'You have already started a party.';
				$output['invite_data'] = $check_party;
			} else {
			
				$opentok = new OpenTok($this->opentokApi, $this->opentokApiSecret);

				$session = $opentok->createSession();

				// A session that uses the OpenTok Media Router, which is required for archiving:
				$session = $opentok->createSession(array( 'mediaMode' => MediaMode::ROUTED ));

				// A session with a location hint:
				$session = $opentok->createSession(array( 'location' => '12.34.56.78' ));

				// An automatically archived session:
				$sessionOptions = array(
						'archiveMode' => ArchiveMode::ALWAYS,
						'mediaMode' => MediaMode::ROUTED
				);
				$session = $opentok->createSession($sessionOptions);

				$sessionId = $session->getSessionId();

				$opentok = new OpenTok($this->opentokApi, $this->opentokApiSecret);

				$sessionId2 = $sessionId;

				$token = $opentok->generateToken($sessionId2);
				
				$insert1['user_id'] = $user_id;
				$insert1['sessionId'] = $sessionId2;
				$insert1['token'] = $token;
				$insert1['party_id'] = $party_id;
				$insert1['create_date'] = date('Y-m-d H:i:s');
				$insert1['status'] =  0;


				$run = $this->common_model->InsertData('party_users', $insert1);
				

				if ($run) {
					$output['status'] = 1;
					$output['message'] = 'Invited successfully.';
				
					$party_data['sessionId'] = $sessionId;
					$party_data['token'] = $token;
					$party_data['create_date'] = $insert1['create_date'];
					$party_data['party_id'] = $run;
					$party_data['status'] = $run;
					
					$output['invite_data'] = $party_data;
					
				} else {
					$output['status'] = 0;
					$output['message'] = 'Something went wrong.';
					$output['data'] = array();
				}
					
			}
			
		}	else{

			$output['status'] = 0;
			$output['message'] = "Check parameters";
		}
		
		echo json_encode($output);

	}
	
	public function create_party() {

		if(isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) {
			
			$user_id = $_REQUEST['user_id'];
			
			$check_party = $this->common_model->GetSingleData('party',array('user_id'=>$user_id,'status'=>1));
			
			if($check_party){
				$output['status'] = 2;
				$output['message'] = 'You have already started a party.';
				$output['party_data'] = $check_party;
			} else {
			
				$opentok = new OpenTok($this->opentokApi, $this->opentokApiSecret);

				$session = $opentok->createSession();

				// A session that uses the OpenTok Media Router, which is required for archiving:
				$session = $opentok->createSession(array( 'mediaMode' => MediaMode::ROUTED ));

				// A session with a location hint:
				$session = $opentok->createSession(array( 'location' => '12.34.56.78' ));

				// An automatically archived session:
				$sessionOptions = array(
						'archiveMode' => ArchiveMode::ALWAYS,
						'mediaMode' => MediaMode::ROUTED
				);
				$session = $opentok->createSession($sessionOptions);

				$sessionId = $session->getSessionId();

				$opentok = new OpenTok($this->opentokApi, $this->opentokApiSecret);

				$sessionId2 = $sessionId;

				$token = $opentok->generateToken($sessionId2);
				
				$insert1['user_id'] = $user_id;
				$insert1['sessionId'] = $sessionId2;
				$insert1['token'] = $token;
				$insert1['create_date'] = date('Y-m-d H:i:s');
				$insert1['status'] =  1;


				$run = $this->common_model->InsertData('party', $insert1);
				

				if ($run) {
					$output['status'] = 1;
					$output['message'] = 'Party has been created successfully.';
				
					$party_data['sessionId'] = $sessionId;
					$party_data['token'] = $token;
					$party_data['create_date'] = $insert1['create_date'];
					$party_data['party_id'] = $run;
					$party_data['status'] = $run;
					
					$output['party_data'] = $party_data;
					
				} else {
					$output['status'] = 0;
					$output['message'] = 'Something went wrong.';
					$output['data'] = array();
				}
					
			}
			
		}	else{

			$output['status'] = 0;
			$output['message'] = "Check parameters";
		}
		
		echo json_encode($output);

	}

	public function GetChatDataBetweenUsers(){
		//https://www.webwiders.in/WEB01/rapid/api/GetChatDataBetweenUsers?user_id=1&card_id=3
		if(isset($_REQUEST['sender']) && isset($_REQUEST['receiver'])){
			
			/*$myfile = fopen("debugs/chat_".time().".txt", "w") or die("Unable to open file!");
			$txt = json_encode($_REQUEST);
			fwrite($myfile, $txt);

			fclose($myfile);*/
			
			$sender = $_REQUEST['sender'];
			$receiver = $_REQUEST['receiver'];
			$call_id = $_REQUEST['call_id']*1;
			$where1 = "";
			
			if($call_id!=0){
				$where1 = " and call_id = $call_id";
			}
			
			$records = $this->common_model->GetAllData('user_chatmessage',"((sender = $sender and receiver = $receiver) or (sender = $receiver and receiver = $sender)) $where1",'id','desc');
			//$output['qe'] = $this->db->last_query();
			
			$result = array();
			
			$records = array_reverse($records);
				
			if(!empty($records)){
				foreach($records as $key => $value){
					
					$records[$key] = $value;
					
					$senderp = $this->short_profile($value['sender']);
					
					$records[$key]['sender'] = $senderp;
					$records[$key]['time_ago'] = time_ago($value['cdate']);
					
					if($sender==$value['receiver']){
						$this->common_model->UpdateData('user_chatmessage',array('id'=>$value['id']),array('read_msg'=>1));
						//echo $this->db->last_query();
					}
				}
			}
			
			if($call_id){
				$call_data = $this->common_model->GetDataById('call_records',$call_id);
				
				if($call_data){
					$current = date('Y-m-d H:i:s');
					$attempt_at = date('Y-m-d H:i:s',strtotime($call_data['attempt_at']));
					$diffInSec = strtotime($current)-strtotime($attempt_at);
					
					if($diffInSec > 15){
						$check_friend = $this->common_model->GetColumnName('Friendlist',"status = 1 and ((user_id = '".$call_data['call_by']."' and request_user_id = '".$call_data['call_to']."') or (user_id = '".$call_data['call_to']."' and request_user_id = '".$call_data['call_by']."'))",array('id'));
						
						if(!$check_friend){
							$insert = array();
							$insert['user_id'] = $call_data['call_by'];
							$insert['request_user_id'] = $call_data['call_to'];
							$insert['status'] = 1;
							$this->common_model->InsertData('Friendlist',$insert);
						}
						
					}
					
					$diffInMin = floor($diffInSec/60);
					$currentMinSec = $diffInSec - ($diffInMin*60);
					$payCountByMin = $call_data['payCountByMin']*1;
					$spent_diamonds = $call_data['spent_diamonds']*1;
					
					$diffInMin++;
					
					if($diffInMin > $payCountByMin){
						
						
						if($currentMinSec >= 15){
							
							
							$by_data = $this->common_model->GetColumnName('users',array('id'=>$call_data['call_by']),array('diamond','id','free_call'));
							$to_data = $this->common_model->GetColumnName('users',array('id'=>$call_data['call_to']),array('chat_price','diamond','id','coin','wallet','gender'));
							
							$setting = $this->common_model->GetColumnName('admin',array('id'=>1),array('coin_rate','commission'));
							
							if($by_data['free_call'] > 0 && $payCountByMin==0){
								
								$update1['free_call'] = $by_data['free_call']-1;
								$this->common_model->UpdateData('users',array('id'=>$by_data['id']),$update1);
								
								$coin = 1200;
								
								$amount = round($coin/$setting['coin_rate'],2);
				
								$update2['coin'] = $to_data['coin']+$coin;
								$update2['wallet'] = $to_data['wallet']+$amount;
								$this->common_model->UpdateData('users',array('id'=>$to_data['id']),$update2);
								
								
								$insert4['user_id'] = $to_data['id'];
								$insert4['coin'] = $coin;
								$insert4['amount'] = $amount;
								$insert4['source'] = 'Call';
								$insert4['create_date'] = date('Y-m-d H:i:s');
								$this->common_model->InsertData('coin_transaction',$insert4);
								
								if($to_data['gender']=='female'){
									$this->create_girl_level($to_data['id'],$amount,'Call');
								}
								
								$update['payCountByMin'] = $diffInMin++;
								$update['spent_diamonds'] = $spent_diamonds+$coin;
								
							} else if($by_data['diamond'] >= $to_data['chat_price']){
							
								$update1['diamond'] = $by_data['diamond']-$to_data['chat_price'];
								$this->common_model->UpdateData('users',array('id'=>$by_data['id']),$update1);
							
								
								$commission = $setting['commission'];
								
								$coin = round(($to_data['chat_price']*$commission)/100,0);
								
								$amount = round($coin/$setting['coin_rate'],2);
				
								$update2['coin'] = $to_data['coin']+$coin;
								$update2['wallet'] = $to_data['wallet']+$amount;
								$this->common_model->UpdateData('users',array('id'=>$to_data['id']),$update2);
								
								
								$insert4['user_id'] = $to_data['id'];
								$insert4['coin'] = $coin;
								$insert4['amount'] = $amount;
								$insert4['source'] = 'Call';
								$insert4['create_date'] = date('Y-m-d H:i:s');
								$this->common_model->InsertData('coin_transaction',$insert4);
								
								
								$insert5['user_id'] = $by_data['id'];
								$insert5['diamond'] = $to_data['chat_price'];
								$insert5['transaction_id'] = time();
								$insert5['payment_method'] = 'Call';
								$insert5['free_call'] = 0;
								$insert5['amount'] = 0;
								$insert5['type'] = 2;
								$insert5['date'] = date('Y-m-d H:i:s');
								$insert5['message'] = 'Spend on Call';
								$this->common_model->InsertData('transaction',$insert5);
								
								if($to_data['gender']=='female'){
									$this->create_girl_level($to_data['id'],$amount,'Call');
								}
								
								$update['payCountByMin'] = $diffInMin++;
								$update['spent_diamonds'] = $spent_diamonds+$coin;
							
							} else {
								$update['status'] = 3;
								$status = 2;
								$message = "Call has been disconnected due to insufficient diamonds.";
							}
							
						}
						
					}
					
					//$output['current'] = $current;
					///$output['currentMinSec'] = $currentMinSec;
					//$output['payCountByMin'] = $payCountByMin;
					//$output['diffInSec'] = $diffInSec;
					
					$update['total_call_sec'] = $diffInSec;
					$update['disconnect_at'] = $current;
					
					$this->common_model->UpdateData('call_records',array('id'=>$call_id),$update);
					
				}
			}
			
			$this->common_model->UpdateData('user_chatmessage',array('id'=>$sender),array('is_delivered'=>1));
				
			$output['data'] = $records;
			$output['status'] = isset($status) ? $status : 1;
			$output['message'] = isset($message) ? $message : "Success";
			
		} else {
			$output['status'] = 0;
			$output['message'] = "We did not find any records!";
		}
		
		$output['base_url'] = site_url().'assets/media/';
		echo json_encode($output);
	}
	
	public function GetChatList() {
		
		//https://www.webwiders.in/WEB01/rapid/api/GetChatList?user_id=1
		
		if(isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])){
			
			$user_id = $_REQUEST['user_id'];
			
			$chat_list=array();
			$strangers=array();
			
			$strangers_unread = 0;
				
			$sel4 = "select * from user_chatmessage where user_chatmessage.receiver='$user_id' or user_chatmessage.sender='$user_id'";
			
			//grup condition 
			$sel4 .= " GROUP BY case when user_chatmessage.sender = $user_id then receiver else sender end order by MAX(id) DESC";
			
			$run4 = $this->db->query($sel4);
			
			$records = $run4->result_array();
		
			foreach($records as $key => $row) {
			
				$res=array();
				
				$friend_id=$row['sender'];
				
				
				if($user_id==$row['sender']) {
					$friend_id=$row['receiver'];
				}
				
				$sender = $this->short_profile($friend_id);
				
				$sel_331 = "select * from user_chatmessage where (receiver='$user_id' and sender='$friend_id') or (sender='$user_id' and receiver='$friend_id') order by id DESC Limit 0,1";
			
				$run1 = $this->db->query($sel_331);
				$row
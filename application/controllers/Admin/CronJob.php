<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
/*
*/
/*
*/
error_reporting(0);
class CronJob extends CI_Controller
{
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
	
	public function SendGreeting() { 
		//send greeting text every min by cron
		$today = date('Y-m-d');
		
		$selectedTime = date('Y-m-d H:i:s');
		$endTime = date('Y-m-d H:i:s',strtotime($selectedTime."-15 second"));
		
		$guardian = $this->common_model->GetAllData('greeting_loops',"status = 1 and receivers != ''");
		
		if(!empty($guardian)){
			foreach($guardian as $key => $value){
				
				$user_id = $value['sender'];
				$message = $value['message'];
				$image = $value['image'];
				$thumb = $value['thumb'];
				$message_2 = $value['message'];
				
				
				$userdata = $this->common_model->GetColumnName('users',array('id'=>$user_id),array('gender','nickname','lng1','last_login','image'));
				
				
				if(strtotime($endTime) <= strtotime($userdata['last_login']) and $value['receivers']){
					
					
					//$receivers = explode(',',$value['receivers']);
				
					$users = $this->common_model->GetColumnName('users',"id in (".$value['receivers'].")",array('id','lng1','device_id','last_login'),true);
				
					$converted_lang = array();
					$receivers = array();
					$is_sent_to_1 = 0;
					
					if(!empty($users)){
					
						foreach($users as $key1 => $value1){
							
							if($is_sent_to_1==1){
								$receivers[] = $value1['id'];
							} else {
								
								if(strtotime($endTime) >= strtotime($value1['last_login'])){
									$receivers[] = $value1['id'];
								} else {
									
									$is_sent_to_1 = 1;
									
									$insert = array();
									
									if($image){
										$insert['sender'] = $user_id;
										$insert['receiver'] = $value1['id'];
										$insert['message'] = $image;
										$insert['msg_type'] = 'image';
										$insert['read_msg'] = 1;
										$insert['cdate'] = date('Y-m-d H:i:s');
										$this->common_model->InsertData('user_chatmessage',$insert);
									}
									
									$insert = array();
									
									if(!empty($value1['lng1'])){
										//$l1s = $this->common_model->GetColumnName('language',array('id'=>$userdata['lng1']),array('code'));
										$l2s = $this->common_model->GetColumnName('language',array('id'=>$value1['lng1']),array('code'));
										
										if($l2s){
											if(isset($converted_lang[$l2s['code']])){
												
												$message_2 = $converted_lang[$l2s['code']];
											
											} else {
												
												$auto = $this->auto_transalate(null,$l2s['code'],$message);
												
												if(isset($auto->data->translations[0]->translatedText)){
													$message_2 = $auto->data->translations[0]->translatedText;
													$converted_lang[$l2s['code']] = $message_2;
												}
											}
										}
										
									}
								
									
									$insert['sender'] = $user_id;
									$insert['receiver'] = $value1['id'];
									$insert['message'] = $message;
									$insert['message_2'] = $message_2;
									$insert['msg_type'] = 'text';
									$insert['read_msg'] = 0;
									$insert['cdate'] = date('Y-m-d H:i:s');
									$this->common_model->InsertData('user_chatmessage',$insert);
									
									$notArr['user_id'] = $value1['id'];
									$notArr['message'] = $userdata['nickname']." has sent you a message.";
									
									$notArr['behalf_of'] = $user_id;
									$notArr['device_id'] = $value1['device_id'];
									
									if($userdata['image']){
										$profile = site_url().'upload/users/'.$userdata['image'];
									} else {
										if($userdata['gender']=='male'){
											$profile = site_url().'upload/male.png';
										} else {
											$profile = site_url().'upload/default_Image.png';
										}
									}
									
									$notArr['other'] = array('screen'=>'chat','sender'=>$user_id,'receiver'=>$value1['id'],'sender_name'=>$userdata['nickname'],'profile'=>$profile);
									$this->common_model->send_and_insert_notifi($notArr);
									
								}
							}
						}
					
					}
					
					if(!empty($receivers)){
						
						$inser3['receivers'] = implode(',',$receivers);
						$inser3['status'] = 1;
						
					} else {
						$inser3['receivers'] = '';
						$inser3['status'] = 0;
					}
					
					$this->common_model->UpdateData('greeting_loops',array('id'=>$value['id']),$inser3);
					
				}
			}
		}
		
		
		//auto stop live streaming when user offline
		$liveUsers = $this->common_model->GetAllData('live_users',"status = 1");

		if(!empty($liveUsers)){
			
			foreach($liveUsers as $liveUser){
				
				$user_id = $liveUser['user_id'];
				
				$userdata = $this->common_model->GetColumnName('users',array('id'=>$user_id),array('gender','nickname','lng1','last_login'));
				
				$last_login = date('Y-m-d H:i:s',strtotime($selectedTime."-10 minutes"));
				
				if(strtotime($last_login) >= strtotime($userdata['last_login'])){

					$created_at = date('Y-m-d H:i:s',strtotime($liveUser['created_at']));
				
					
					$time_in_sec = strtotime($selectedTime) - strtotime($created_at);
					
					$update['status'] = 2;
					$update['end_time'] = $end_time;
					$update['time_in_sec'] = $time_in_sec;
					
					$this->common_model->UpdateData('live_users', array('id' => $liveUser['id']),$update);
					$this->common_model->DeleteData('streaming_users', array('streaming_id' => $liveUser['id']));
					
				}
				
			}
			
		}
		
		echo 1;
	}
	public function ResetGuardian()
	{
		//run at every day
		$today = date('Y-m-d');
		
		$guardian = $this->common_model->GetAllData('guardian',"status = 1 and DATE(end_date) < DATE('".$today."')");
		if(!empty($guardian)){
			foreach($guardian as $key => $value){
				$this->common_model->UpdateData('guardian',"id = '".$value['id']."'",array('status'=>0));
				$this->common_model->UpdateData('users',"id = '".$value['primary_user']."'",array('guardian_price'=>2000));
			}
		}
		echo 1;
	}

	public function settlementCoins()
	{
		//run at every sunday evening 
		 //= $this->common_model->GetAllData('coin_transaction', array('isSettlement'=>1));
		$sql = 'SELECT SUM(amount) as total, user_id FROM coin_transaction WHERE isSettlement=1 GROUP BY user_id';
      $result = $this->db->query($sql);
       $result->num_rows();
    $Coins = $result->result_array(); 

		if ($Coins) {

			foreach ($Coins as $key => $value) {
					//wallet


			

					$UserData = $this->common_model->GetSingleData('users', array('id'=>$value["user_id"]));
					if ($UserData["gender"] == "female") {

					$NewAmount = $UserData["wallet"]+$value["total"];

					$update["wallet"] = $NewAmount;
					$run = $this->common_model->UpdateData('users', array('id'=>$value["user_id"]), $update);
					if ($run) {
						$update1["isSettlement"] = 0;
						$this->common_model->UpdateData('coin_transaction', array('user_id'=>$value["user_id"]), $update1);
						$insert["user_id"] = $value["user_id"];
						$insert["user_amount"] = $value["total"];
						$insert["created_at"] = date('Y-m-d H:i:s');

						$this->common_model->InsertData('settlement', $insert);
						if($value["total"] > 10) {
						$this->parent_earning($value["user_id"],$value["total"]);
						}
						echo "Success".$value["total"]."<br>";

							}
							else {
								echo "Failed".$value["total"];
							}


						}





					}		
		}
	}

	private function parent_earning($id,$amount,$source=""){
		$userdata = $this->common_model->GetColumnName('users',array('id'=>$id),array('invited_by'));
		
		if($userdata && $userdata['invited_by']){
			
			$allChild = $this->common_model->GetColumnName('users',array('invited_by'=>$userdata['invited_by']),array('GROUP_CONCAT(id) id'));
			
			$allChild = ($allChild) ? $allChild['id'] : false;
			
			if($allChild){
			
				$total_amount = $this->common_model->GetColumnName('coin_transaction',"user_id in ($allChild)",array('SUM(amount) as total'));
				
				$total_amount = ($total_amount['total']) ? $total_amount['total'] : 0;
			
				$get_level = $this->common_model->GetColumnName('agent_comission',array('amount <= '=>$total_amount),array('percent','id'),false,'amount','desc');
				
				if($get_level && $get_level['percent'] > 0){
					
					$userdata2 = $this->common_model->GetColumnName('users',array('id'=>$userdata['invited_by']),array('wallet','invited_by'));
					
					if($userdata2){
						
						$NewAmout = round(($amount*$get_level['percent'])/100,2);
						
						if($NewAmout > 0){
							$insert['user_id'] = $userdata['invited_by'];
							$insert['amount'] = $NewAmout;
							$insert['source'] = $source;
							$insert['earn_by'] = $id;
							$insert['create_date'] = date('Y-m-d H:i:s');
							$this->common_model->InsertData('agent_transaction',$insert);
							$insert1["user_id"] = $id;
							$insert1["user_amount"] = $NewAmout;
							$insert1["created_at"] = date('Y-m-d H:i:s');
							$this->common_model->InsertData('settlement', $insert1);

							$update['wallet'] = $userdata2['wallet']+$NewAmout;
							$update['level'] = $get_level['id'];
							$this->common_model->UpdateData('users',array('id'=>$userdata['invited_by']),$update);
							
							if($userdata2['invited_by']){
								$this->parent_earning($id,$NewAmout,$source);
							}
						}
					
					}
					
				}
			}
			
		}
		
	}
	
	public function update_coinbase_status(){
		//run at every min
		$transactions = $this->common_model->GetAllData('transaction',array('type'=>1,'status'=>0,'coinbase_id != '=>'','coinbase_code != '=>''),'id','desc');
		
		
		
		if(!empty($transactions)){
			
			foreach($transactions as $transaction){
		
				
				$coinbase_code = $transaction['coinbase_code'];
				
				$curl = curl_init();
		

				curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://api.commerce.coinbase.com/charges/'.$coinbase_code,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'GET',
					CURLOPT_USERAGENT => 'Mozilla Chrome Safari',
					CURLOPT_HTTPHEADER => array(
						'X-Cc-Api-Key: '.coinbase_API,
						'X-Cc-Version: '.coinbase_Version
					),
				));
				
				$result = curl_exec($curl);
						
				if (curl_errno($curl)) {
					echo 0;
				}
				
				curl_close($curl);
				$result = json_decode($result);
				//print_r($result);
				
				$data = $result->data;
				
				$timeline = $data->timeline;
				
				$timeline = array_reverse($timeline);
				
				$ttimeline2 = $timeline[0]; 
				
				
				if($ttimeline2->status=='EXPIRED' || $ttimeline2->status=='CANCELED'){
					
					$this->common_model->UpdateData('transaction',array('id'=>$transaction['id']),array('status'=>2));
					
					
				} else if($ttimeline2->status=='COMPLETED' || $ttimeline2->status=='RESOLVED' && !empty($data->payments)){
					
					
					$payments = $data->payments[0];
					
					
					if(isset($payments->status) && isset($payments->transaction_id) && $payments->status=='CONFIRMED'){
						
						$metadata = $data->metadata;
						
						$user_id = $transaction['user_id'];
						//$transaction_id = time();
						$transaction_id = $payments->transaction_id;
						$diamond_id = $metadata->diamond_id;
						
						$diamondsdata = $this->common_model->GetDataById('diamonds',$diamond_id);
						
					
						$diamond = $diamondsdata['diamonds'];
						$free_call = $diamondsdata['free_call'];
						$amount = $diamondsdata['amount'];
						
						$userdata = $this->common_model->GetColumnName('users',array('id'=>$user_id),array('id','diamond','free_call','device_id','gender'));
						$update['diamond'] = $userdata['diamond']+$diamond;
						$update['free_call'] = $userdata['free_call']+$free_call;
						
						if($userdata['gender']=='male'){
							$total_diamond = $this->common_model->GetColumnName('transaction',array('user_id'=>$user_id,'type'=>1,'status'=>1),array('SUM(diamond) as total'));
							$total_diamond = ($total_diamond['total']) ? $total_diamond['total'] : 0;
							$total_diamond = $total_diamond+$diamond;
							
							$get_level = $this->common_model->GetColumnName('level',array('gender'=>'male','daimond <= '=>$total_diamond),array('level'),false,'daimond','desc');
							
							$get_level = ($get_level['level']) ? $get_level['level'] : 0;
							
							$update['level'] = $get_level;
						}
						
						$run = $this->common_model->UpdateData('users',array('id'=>$user_id),$update);
						
						$update2['transaction_id'] = $transaction_id;
						$update2['status'] = 1;
						$this->common_model->UpdateData('transaction',array('id'=>$transaction['id']),$update2);
						
						
						$notArr['user_id'] = $user_id;
									
						$notArr['message'] = $diamond." diamonds added in your balance";
						
						$notArr['behalf_of'] = 0;
						$notArr['device_id'] = $userdata['device_id'];
						
						$notArr['other'] = array('screen'=>'diamond_added','diamonds'=>$diamond,'admin'=>0);
						$this->common_model->send_and_insert_notifi($notArr);

						
					}
					
													
				}
					
				
			}
			
		}
		
		
		
		echo 1;
		
	}
	
	public function update_coinbase_status2(){
		//not in use
		//$transactions = $this->common_model->GetAllData('transaction',array('type'=>1,'status'=>0,'coinbase_id != '=>''));
		
		$curl = curl_init();
		

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.commerce.coinbase.com/events?order=desc&limit=100&resource=charge',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_USERAGENT => 'Mozilla Chrome Safari',
			CURLOPT_HTTPHEADER => array(
				'X-Cc-Api-Key: '.coinbase_API,
				'X-Cc-Version: '.coinbase_Version
			),
		));
		
		$result = curl_exec($curl);
				
		if (curl_errno($curl)) {
			echo 0;
		}
		
		curl_close($curl);
		$transactions = json_decode($result);
			echo '<pre>';	
	
		
		if(isset($transactions->data) && !empty($transactions->data)){
			
			foreach($transactions->data as $key => $transaction){
				
				$data = $transaction->data;
				
				$tradata = $this->common_model->GetSingleData('transaction',array('type'=>1,'status'=>0,'coinbase_id'=>$data->id));
				
				if($tradata){
					
					$timeline = $data->timeline;
					
					$timeline = array_reverse($timeline);
					
					$ttimeline2 = $timeline[0];

					print_r($ttimeline2);
					
					if($ttimeline2->status=='EXPIRED' || $ttimeline2->status=='CANCELED'){
						
						$this->common_model->UpdateData('transaction',array('id'=>$tradata['id']),array('status'=>2));
						
						
					} else if($ttimeline2->status=='COMPLETED' || $ttimeline2->status=='RESOLVED' && !empty($data->payments)){
						
						
						$payments = $data->payments;
						
						if(isset($payments->status) && isset($payments->transaction_id) && $payments->status=='CONFIRMED'){
							
							$metadata = $data->metadata;
							
							$user_id = $tradata['user_id'];
							//$transaction_id = time();
							$transaction_id = $payments->transaction_id;
							$diamond_id = $metadata->diamond_id;
							
							$diamondsdata = $this->common_model->GetDataById('diamonds',$diamond_id);
							
						
							$diamond = $diamondsdata['diamonds'];
							$free_call = $diamondsdata['free_call'];
							$amount = $diamondsdata['amount'];
							
							$userdata = $this->common_model->GetColumnName('users',array('id'=>$user_id),array('id','diamond','free_call','device_id','gender'));
							$update['diamond'] = $userdata['diamond']+$diamond;
							$update['free_call'] = $userdata['free_call']+$free_call;
							
							if($userdata['gender']=='male'){
								$total_diamond = $this->common_model->GetColumnName('transaction',array('user_id'=>$user_id,'type'=>1,'status'=>1),array('SUM(diamond) as total'));
								$total_diamond = ($total_diamond['total']) ? $total_diamond['total'] : 0;
								$total_diamond = $total_diamond+$diamond;
								
								$get_level = $this->common_model->GetColumnName('level',array('gender'=>'male','daimond <= '=>$total_diamond),array('level'),false,'daimond','desc');
								
								$get_level = ($get_level['level']) ? $get_level['level'] : 0;
								
								$update['level'] = $get_level;
							}
							
							$run = $this->common_model->UpdateData('users',array('id'=>$user_id),$update);
							
							$update2['transaction_id'] = $transaction_id;
							$update2['status'] = 1;
							$this->common_model->UpdateData('transaction',array('id'=>$tradata['id']),$update2);
							
							echo $this->db->last_query();
							
							$notArr['user_id'] = $user_id;
										
							$notArr['message'] = $diamond." diamonds added in your balance";
							
							$notArr['behalf_of'] = 0;
							$notArr['device_id'] = $userdata['device_id'];
							
							$notArr['other'] = array('screen'=>'diamond_added','diamonds'=>$diamond,'admin'=>0);
							$this->common_model->send_and_insert_notifi($notArr);

							
						}
						
														
					}
					
					
				}
				
				
			}
			
		}
		
		echo 1;
		
	}

} ?>
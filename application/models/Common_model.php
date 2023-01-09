<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Common_model extends CI_Model {

	public function __construct() {
		parent::__construct();
		$this->db->query("SET sql_mode = '';");
	} 
	
	public function send_and_insert_notifi($dataArr = array()){
    
    if(!empty($dataArr)){
			
			$other = ($dataArr['other']) ? $dataArr['other'] : array();
			
			$type = isset($dataArr['type']) ? $dataArr['type'] : 1;
    
      $insert['type'] = $type;
      $insert['user_id'] = $dataArr['user_id'];
      $insert['message'] = $dataArr['message'];
      $insert['behalf_of'] = ($dataArr['behalf_of']) ? $dataArr['behalf_of'] : 0;
      $insert['other'] = serialize($other);
      $insert['create_date'] = date('Y-m-d H:i:s');
      $insert['update_date'] = date('Y-m-d H:i:s');
      
      if(isset($dataArr['onlyPushNot']) && $dataArr['onlyPushNot']==true) {
        $run = true;
      } else {
        $run = $this->InsertData('notification',$insert);
      }
      
      if($run){
        if(isset($dataArr['device_id']) && !empty($dataArr['device_id'])){
          $deviceToken[] = $dataArr['device_id'];
        
          $arr['title'] = $insert['message'];
          $arr['deviceToken'] = $deviceToken;
          $arr['other'] = $other;
          $d = $this->AndroidNotification($arr);
					//print_r($d); 
        }
        return true;
      } else {
        return false;
      }
    }
  }
  
	public function AndroidNotification($data = array()){
    if(!empty($data)){
      $content = array(
        "en" => $data['title']
      );
     
      $keyvalue = "f010ac78-a9a3-45d7-8abf-cb49964e3bc9";
      $hashes_array = array();
			
			$pushdata = $data['other'];
      
      $fields = array(
        'app_id' => $keyvalue,
        'include_player_ids' => $data['deviceToken'],
        'data' => $pushdata,
        'contents' => $content,
        'web_buttons' => $hashes_array,
        //'android_channel_id' => '64afdb66-02c1-409c-8411-858b12965761',
        //'android_sound' => 'onesignal_default_sound'
      );
      
      $fields = json_encode($fields);
      //print("\nJSON sent:\n");
      //print($fields);
      
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic ZmZmMDVjZWQtOGM3Mi00NWU4LWE1MWEtYjk1ZjYzMGRhMDI5'
      ));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HEADER, FALSE);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        
      $response = curl_exec($ch);
      curl_close($ch);
			
			
        
      return json_decode($response);
			
    } else {
      return false;
    }
  }

  public function GetDataByOrderLimit($table,$where,$odf=NULL,$odc=NULL,$limit=NULL,$start=0) {
    if($where) {
      $this->db->where($where);
    }		 

    if($odf && $odc){
      $this->db->order_by($odf,$odc);
    }
       
    if($limit){
      $this->db->limit($limit, $start);
    }

    $sql=$this->db->get($table);
    
    if($sql) {
      return $sql->result_array();
    }else{
      return array();
    }
  }

  public function GetDataById($table,$value) {
    $this->db->where('id', $value);
    $obj=$this->db->get($table);
    //echo $this->db->last_query();die;
    if($obj->num_rows() > 0){
      return $obj->row_array();
    } else {
      return false;
    }
  }
   
  public function InsertData($table,$data) {
    $insert = $this->db->insert($table,$data);
     if($insert){
      return $this->db->insert_id();
    }else{
      return false;
    }
  }
  
	
	public function GetAllData($table,$where=null,$ob=null,$obc='DESC',$limit=null,$offset=null,$select=null){
   
		if($select) {
			$this->db->select($select);
		}else{
			$this->db->select('*');
		}
		
		$this->db->from($table);
    if($where) {
      $this->db->where($where);
    }
    if($ob) {
      $this->db->order_by($ob,$obc);
    }
    if($limit) {
      $this->db->limit($limit,$offset);
    }
    $query = $this->db->get();
		// echo   $this->db->last_query();
    if($query->num_rows() > 0) {	
      return $query->result_array();
    } else {
      return array();
    }
  }
 
  public function GetSingleData($table,$where=null,$ob=null,$obc='desc'){
    if($where) {
      $this->db->where($where);
    }
    if($ob) {
      $this->db->order_by($ob,$obc);
    }
    $query = $this->db->get($table);
    if($query->num_rows()) {	
      return $query->row_array();
    } else {
      return false;
    }
  }
  
  public function UpdateData($table, $where, $data) {

    $this->db->where($where);
    $obj=$this->db->update($table,$data);
   // echo $this->db->last_query();die;
    return ($this->db->affected_rows() > 0)?true:true;
  }
  
  public function DeleteData($table, $where) {
    $this->db->where($where);
    $obj=$this->db->delete($table);
    
		return ($this->db->affected_rows() > 0)?true:false;		
  }

  public function GetColumnName($table,$where=null,$name=null,$double=null,$order_by=null,$order_col=null,$group_by=null) {
    if($name){
      $this->db->select(implode(',',$name));
    } else {
      $this->db->select('*');
    }
    
    if($where){
      $this->db->where($where);
    }
		
		if($group_by) {
      $this->db->group_by($group_by);
    }
    
    if($order_by && $order_col){
      $this->db->order_by($order_by,$order_col);
    }
    $sql=$this->db->get($table);
    if($double){
      $data = array();
    } else {
      $data = false;
    }
    if($sql->num_rows() > 0){
      if($double){
        $data = $sql->result_array();
      } else {
        $data = $sql->row_array();
      } 
      
    }
    return $data;
  }

  public function SendMail($toz,$sub,$body) {

    //  $to =$toz;  
    //  $from ='';
    // $headers ="From: ".$admin[0]['mail_from_title']." <".$from."> \n";
    // $headers .= "MIME-Version: 1.0\n";
    // $headers .= "Content-type: text/html; charset=iso-8859-1 \n";
    // $subject =$sub;

   /* $config = array();
    $config['mailtype'] = "html";
    $config['charset'] = "utf-8";
    $config['newline'] = "\r\n";
    $config['wordwrap'] = TRUE;
    $config['validate'] = FALSE;
    
    $this->email->initialize($config);
    
    $this->email->from(Email, Project);
   
    $this->email->to($toz);
    $this->email->set_crlf("\r\n"); 
    //$this->email->set_mailtype("html"); 
    $this->email->subject($sub);*/
		
		$headers = "From: ".Project." <".Email."> \n";			
		
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers .= "X-Priority: 3\r\n";
		$headers .= "X-Mailer: PHP". phpversion() ."\r\n";
    
    $msg = '<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" name="mjqemailid" content="B0WB7P9VV27ACYA96DTTHDGYXR1I0SUB">
            <tbody>
              <tr>
                <td align="center" valign="top">
                  <table border="0" cellpadding="10" cellspacing="0" width="600" style="border:1px solid #ddd;margin:50px 0px 100px 0px;text-align:center;color:#363636;font-family:\'Montserrat\',Arial,Helvetica,sans-serif;background-color:white">
                    <tbody>
                      <tr>
                        <td align="center" valign="top" style="border-bottom:2px solid #fe8a0f;padding:0px;background:-moz-linear-gradient(top,#fff,#f6f6f6);background:#fff;">
                          <table border="0" cellpadding="0" cellspacing="10" width="100%">
                            <tbody>
                              <tr>
                                <td align="center" style="text-align: center;" valign="middle"><a style="font-family:\'Ubuntu\',sans-serif;color:#ff3000;font-weight:300;display:block;letter-spacing:-1.5px;text-decoration:none;margin-top:2px" href="'.base_url().'"><img src="'.base_url().'assets/images/logo.png" style="padding-top:0;display:inline-block;vertical-align:middle;margin-right:0px;height:55px" class="CToWUd"></a></td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                      <tr>
                        <td align="center" valign="top">
                          <table border="0" cellpadding="0" cellspacing="10" width="100%">
                            <tbody>
                              <tr>
                                <td align="left" valign="top" style="color:#444;font-size:14px">
                                  '.$body.'
                                   <p style="margin:0;padding:10px 0px">Thank you,<br>Team '.Project.'</p>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                      <tr>
                        <td align="center" valign="top" style="background-color:#fff;color:white">
                          <table border="0" cellpadding="0" cellspacing="10" width="100%">
                            <tbody>
                              <tr>
                                <td align="left" valign="top" width="80%">
                                  <div style="margin:0;padding:0;color:#fff;font-size:13px"><a href="'.base_url().'/privacy-policy" style="color:white;text-decoration:none">© Copyright '.date('Y').' '.Project . '. All Rights Reserved.</div>
                                </td>
                                
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
            </tbody>
          </table>';
        
    
    $run  = mail($toz,$sub,$msg,$headers);
    
    if($run) {
      return 1;
    } else {
      return 0;
    }

  }
	
	public function SendMailCustom($toz,$sub,$body) {

    //  $to =$toz;  
    //  $from ='';
    // $headers ="From: ".$admin[0]['mail_from_title']." <".$from."> \n";
    // $headers .= "MIME-Version: 1.0\n";
    // $headers .= "Content-type: text/html; charset=iso-8859-1 \n";
    // $subject =$sub;
		
		$headers = "From: ".Project." <".Email."> \n";			
		
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers .= "X-Priority: 3\r\n";
		$headers .= "X-Mailer: PHP". phpversion() ."\r\n";
		
		mail($toz,$sub,$body,$headers);

  }

  public function SendMailCustomNew($toz,$sub,$body) {
      
      
		//    $headers = "From: KeyCoiner <".Email."> \n";      
		//    $headers .= "MIME-Version: 1.0\r\n";
		//    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		//    $headers .= "X-Priority: 3\r\n";
		//    $headers .= "X-Mailer: PHP". phpversion() ."\r\n";

		$headers = "From: ".Project." <".Email."> \n";      
    
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
    $headers .= "X-Priority: 3\r\n";
    $headers .= "X-Mailer: PHP". phpversion() ."\r\n";
    
$msg = '<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" name="mjqemailid" content="B0WB7P9VV27ACYA96DTTHDGYXR1I0SUB">
            <tbody>
              <tr>
                <td align="center" valign="top">
                  <table border="0" cellpadding="10" cellspacing="0" width="600" style="border:1px solid #ddd;margin:50px 0px 100px 0px;text-align:center;color:#363636;font-family:\'Montserrat\',Arial,Helvetica,sans-serif;background-color:white">
                    <tbody>
                      <tr>
                        <td align="center" valign="top" style="border-bottom:2px solid #fe8a0f;padding:0px;background:-moz-linear-gradient(top,#fff,#f6f6f6);background:#fff;">
                          <table border="0" cellpadding="0" cellspacing="10" width="100%">
                            <tbody>
                              <tr>
                                <td align="center" style="text-align: center;" valign="middle"><a style="font-family:\'Ubuntu\',sans-serif;color:#ff3000;font-weight:300;display:block;letter-spacing:-1.5px;text-decoration:none;margin-top:2px" href="'.base_url().'"><img src="'.base_url().'assets/images/logo.png" style="padding-top:0;display:inline-block;vertical-align:middle;margin-right:0px;height:55px" class="CToWUd"></a></td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                      <tr>
                        <td align="center" valign="top">
                          <table border="0" cellpadding="0" cellspacing="10" width="100%">
                            <tbody>
                              <tr>
                                <td align="left" valign="top" style="color:#444;font-size:14px">
                                  '.$body.'
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                      <tr>
                        <td align="center" valign="top" style="background-color:#fff;color:white">
                          <table border="0" cellpadding="0" cellspacing="10" width="100%">
                            <tbody>
                              <tr>
                                <td align="left" valign="top" width="80%">
                                  <div style="margin:0;padding:0;color:#fff;font-size:13px"><a href="'.base_url().'/privacy-policy" style="color:white;text-decoration:none">© Copyright '.date('Y').' '.Project . '. All Rights Reserved.</div>
                                </td>
                                
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
            </tbody>
          </table>';

    $run  = mail($toz,$sub,$msg,$headers);
    
    if($run) {
      return 1;
    } else {
      return 0;
    }
    
// mail($toz,$sub,$body1,$headers);

  }

	public function SendMailCustomNew1($toz,$sub,$body) {
				
				
	//    $headers = "From: KeyCoiner <".Email."> \n";      
	//    $headers .= "MIME-Version: 1.0\r\n";
	//    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	//    $headers .= "X-Priority: 3\r\n";
	//    $headers .= "X-Mailer: PHP". phpversion() ."\r\n";

	$headers = "From: ".Project." <".Email."> \n";      
			
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
			$headers .= "X-Priority: 3\r\n";
			$headers .= "X-Mailer: PHP". phpversion() ."\r\n";
			
	$msg = '<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" name="mjqemailid" content="B0WB7P9VV27ACYA96DTTHDGYXR1I0SUB">
							<tbody>
												<tr>
													<td align="center" valign="top">
														<table border="0" cellpadding="0" cellspacing="10" width="100%">
															<tbody>
																<tr>
																	<td align="left" valign="top" style="color:#444;font-size:14px">
																		'.$body.'
																	</td>
																</tr>
															</tbody>
														</table>
													</td>
												</tr>
												<tr>
													<td align="center" valign="top" style="background-color:#fff;color:white">
														<table border="0" cellpadding="0" cellspacing="10" width="100%">
															<tbody>
																<tr>
																	<td align="left" valign="top" width="80%">
																		<div style="margin:0;padding:0;color:#fff;font-size:13px"><a href="'.base_url().'/privacy-policy" style="color:white;text-decoration:none">© Copyright '.date('Y').' '.Project . '. All Rights Reserved.</div>
																	</td>
																	
																</tr>
															</tbody>
														</table>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>';

			$run  = mail($toz,$sub,$msg,$headers);
			
			if($run) {
				return 1;
			} else {
				return 0;
			}
			
	// mail($toz,$sub,$body1,$headers);

		}


	public function SmtpTest($toz,$sub,$body) {
		error_reporting(0);
    $this->load->library('phpmailer_lib');
		
		$mail = $this->phpmailer_lib->load();
		
		$smtp = $this->GetDataById('smtp',1);
		
		$mail->isSMTP();
		$mail->Host     = $smtp['host'];
		$mail->SMTPAuth = true;
		$mail->Username = $smtp['user_name'];
		$mail->Password = $smtp['password'];
		$mail->SMTPSecure = $smtp['ssl'];
		$mail->Port     = $smtp['port'];
		$mail->CharSet = 'UTF-8';
		$mail->setFrom('info@webwiders.in', Project);
		
		$mail->addAddress($toz);
		
		$mail->Subject = $sub;
		
		$mail->isHTML(true);
		
    $mail->Body = $body;
    $run  = $mail->send();
    
    if($run) {
      return 1;
    } else {
      return 0;
    }

  }
	

}

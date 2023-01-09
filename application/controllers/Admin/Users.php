<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
/*
*/
/*
*/
error_reporting(0);
class Users extends CI_Controller
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

	public function user_list(){
		$where = "user_type='1'" ;	
		
		if(isset($_REQUEST['start_date']) && !empty($_REQUEST['start_date'])){
			$where .=" and DATE(created_at) >= DATE('".$_REQUEST['start_date']."')";	
		}
		
		if(isset($_REQUEST['end_date']) && !empty($_REQUEST['end_date'])){
			$where .=" and DATE(created_at) <= DATE('".$_REQUEST['end_date']."')";	
		}

		if(isset($_REQUEST['country']) && !empty($_REQUEST['country'])){
			$where .=" and country='".$_REQUEST['country']."'";	
		}
		
		if(isset($_REQUEST['UserId']) && !empty($_REQUEST['UserId'])){
			$where .=" and id ='".$_REQUEST['UserId']."'";	
		}
		
		if(isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])){
			$where .=" and (nickname like '%".$_REQUEST['keyword']."%' or gender='".$_REQUEST['keyword']."')";	
		}
			
		$data["userData"] = $this->common_model->GetAllData('users',$where,'id','desc');
		//echo $this->db->last_query(); die;
		$this->load->view('admin/user-list', $data);
	}

	public function edit_coin()
	{
		//$this->form_validation->set_rules('diamond_rate','diamond rate','trim|required');
		$this->form_validation->set_rules('coin_rate','coin rate ','trim|required');
		$this->form_validation->set_rules('commission','commission percentage','trim|required');
		$this->form_validation->set_rules('guardian_percent','guardian percent','trim|required');
		$this->form_validation->set_rules('guardian_duration','guardian duration','trim|required');

		$this->form_validation->set_rules('ban_report_count','ban report count','trim|required');
		$this->form_validation->set_rules('account_per_ip','account per ip','trim|required');
		$this->form_validation->set_rules('album_limit','album limit','trim|required');

		$this->form_validation->set_rules('guardian_amount','guardian amount','trim|required');
		$this->form_validation->set_rules('watch_video_diamonds','watch_video_diamonds','trim|required');

		if($this->form_validation->run()){
			//$update['diamond_rate'] = $this->input->post('diamond_rate');
			$update['coin_rate'] = $this->input->post('coin_rate');
			$update['commission'] = $this->input->post('commission');

			$update['guardian_percent'] = $this->input->post('guardian_percent');
			$update['guardian_duration'] = $this->input->post('guardian_duration');

			$update['ban_report_count'] = $this->input->post('ban_report_count');
			$update['post_max_image'] = $this->input->post('post_max_image');
			$update['account_per_ip'] = $this->input->post('account_per_ip');
			$update['album_limit'] = $this->input->post('album_limit');
			$update['guardian_amount'] = $this->input->post('guardian_amount');
			$update['watch_video_diamonds'] = $this->input->post('watch_video_diamonds');

			$run = $this->common_model->UpdateData('admin', array('id'=>1),$update);
	        if($run)
	        {
				$this->session->set_flashdata('msgs','<div class="alert alert-success">Success! Diamond data updated successfully.</div>');	
				$json['status']=1;
			}
			else
			{
			    // $this->session->set_flashdata('msgs','<div class="alert alert-danger">Something is Worng.</div>');
			    $json['status']=0;
			    $json['message']='<div class="alert alert-danger">Something went worng.</div>';
			}
		}
		else {
			$json['status'] = 0;
			$json['message'] = '<div class="alert alert-danger">'.validation_errors().'</div>';
		}
		
		echo json_encode($json);
	}

	public function transaction_history()
	{
	$where = '1=1';	
	if ($_REQUEST['start_date']) {
	$where .= " and DATE(date) = DATE('".$_REQUEST['start_date']."')";
	}
	if ($_REQUEST['uniqueID']) {
	$where .= " and ( (select count(id) from users where users.id = transaction.user_id and users.uniqueID='".$_REQUEST['uniqueID']."') > 0 )";
	}
	  $data["transactions"] = $this->common_model->GetAllData('transaction',$where,'id','desc');
      $this->load->view('admin/transaction-history', $data);
	}

	public function manage_coin(){
		$data['admin_data'] = $this->common_model->GetSingleData('admin',array('id'=>1));
		$this->load->view('admin/manage-coin', $data);
	}

	public function verified_users(){
		
		 $where = "user_type=1 and is_verified = 1" ;	
		
		if(isset($_REQUEST['start_date']) && !empty($_REQUEST['start_date'])){
			$where .=" and DATE(created_at) >= DATE('".$_REQUEST['start_date']."')";	
		}
		
		if(isset($_REQUEST['end_date']) && !empty($_REQUEST['end_date'])){
			$where .=" and DATE(created_at) <= DATE('".$_REQUEST['end_date']."')";	
		}

		if(isset($_REQUEST['country']) && !empty($_REQUEST['country'])){
			$where .=" and country='".$_REQUEST['country']."'";	
		}
		
		if(isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])){
			$where .=" and (nickname ='".$_REQUEST['keyword']."' or uniqueID='".$_REQUEST['keyword']."' or invited_by='".$_REQUEST['keyword']."' or gender='".$_REQUEST['keyword']."')";	
		}
	    
	    
		$data["userData"] = $this->common_model->GetAllData('users',$where,'id','desc');
		
		
		$this->load->view('admin/verified_users', $data);
	}

	public function unverified_users(){
		
			$where = "user_type=1 and is_verified = 0" ;	
		
		if(isset($_REQUEST['start_date']) && !empty($_REQUEST['start_date'])){
			$where .=" and DATE(created_at) >= DATE('".$_REQUEST['start_date']."')";	
		}
		
		if(isset($_REQUEST['end_date']) && !empty($_REQUEST['end_date'])){
			$where .=" and DATE(created_at) <= DATE('".$_REQUEST['end_date']."')";	
		}
	    
		if(isset($_REQUEST['country']) && !empty($_REQUEST['country'])){
			$where .=" and country='".$_REQUEST['country']."'";	
		}
		
		if(isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])){
			$where .=" and (nickname ='".$_REQUEST['keyword']."' or uniqueID='".$_REQUEST['keyword']."' or invited_by='".$_REQUEST['keyword']."' or gender='".$_REQUEST['keyword']."')";	
		}
	    
	    
		$data["userData"] = $this->common_model->GetAllData('users',$where,'id','desc');
		
		$this->load->view('admin/unverified_users', $data);
	}

	public function boys_list(){
	    
	   
		$where = "gender= 'male' and user_type='1'";	
	    
		 if(isset($_REQUEST['start_date']) && !empty($_REQUEST['start_date'])){
			$where .=" and DATE(created_at) >= DATE('".$_REQUEST['start_date']."')";	
		}
		
		if(isset($_REQUEST['end_date']) && !empty($_REQUEST['end_date'])){
			$where .=" and DATE(created_at) <= DATE('".$_REQUEST['end_date']."')";	
		}
	    
	    
		$data["userData"] = $this->common_model->GetAllData('users',$where,'id','desc');
		
		
	
		$this->load->view('admin/boys_list', $data);
	}

	public function girls_list(){
	 
	    $where = " gender='female' and user_type='1' " ;	
	    
		 if(isset($_REQUEST['start_date']) && !empty($_REQUEST['start_date'])){
			$where .=" and DATE(created_at) >= DATE('".$_REQUEST['start_date']."')";	
		}
		
		if(isset($_REQUEST['end_date']) && !empty($_REQUEST['end_date'])){
			$where .=" and DATE(created_at) <= DATE('".$_REQUEST['end_date']."')";	
		}
	    
	    
		$data["userData"] = $this->common_model->GetAllData('users',$where,'id','desc');
		
		$this->load->view('admin/girls_list', $data);
	}

	public function total_country(){
		
		// $where=array();
		
		// $orderBy = "(SELECT count(id) from users WHERE users.country = country.id)";
		
		// $data["countrys"] = $this->common_model->GetAllData('country',$where,$orderBy,'desc','','',"*, (SELECT count(id) from users WHERE users.country = country.id) as usercount");
		
		// $this->load->view('admin/total_country', $data);


		$sql = 'SELECT country FROM users WHERE user_type="1" GROUP BY country HAVING COUNT(*) > 0';
		$query = $this->db->query($sql);
		$result1=$query->result_array();
		$coutry_ids='';
		for($i=0;$i<count($result1);$i++)
		{
			$coutry_ids = $coutry_ids.','.$result1[$i]['country'];
		}
		$c_ids = ltrim($coutry_ids,","); 
		$where=' id IN ('.$c_ids.')';
		$result = array();
		$orderBy = "(SELECT count(id) from live_users WHERE live_users.country = country.id)";
		$records = $this->common_model->GetAllData('country',$where,$orderBy,'desc');
		foreach($records as $key => $record){
			$result[$key] = $record;
			$flags = site_url().'upload/no.png';
			$flag_big = site_url().'upload/no.png';
			if(file_exists('assets/flags/'.strtolower($record['iso']).'.png')){
				$flags = site_url().'assets/flags/'.strtolower($record['iso']).'.png';
			}
			if(file_exists('assets/flag_big/'.strtolower($record['iso']).'.png')){
				$flag_big = site_url().'assets/flag_big/'.strtolower($record['iso']).'.png';
			}
			$result[$key]['flag_big'] = $flag_big;
			$result[$key]['flag'] = $flags;
		}
		$data["countrys"] = $result;
		$this->load->view('admin/total_country',$data);

		
	}

	public function test(){
		echo 'hello';
		// $sql = 'SELECT country FROM users GROUP BY country HAVING COUNT(*) > 0';
		// $query = $this->db->query($sql);
		// $result1=$query->result_array();
		// $coutry_ids='';
		// for($i=0;$i<count($result1);$i++)
		// {
		// 	$coutry_ids = $coutry_ids.','.$result1[$i]['country'];
		// }
		// $c_ids = ltrim($coutry_ids,","); 
		// $where=' id IN ('.$c_ids.')';
		// $result = array();
		// $orderBy = "(SELECT count(id) from live_users WHERE live_users.country = country.id)";
		// $records = $this->common_model->GetAllData('country',$where,$orderBy,'desc');
		// foreach($records as $key => $record){
		// 	$result[$key] = $record;
		// 	$flags = site_url().'upload/no.png';
		// 	$flag_big = site_url().'upload/no.png';
		// 	if(file_exists('assets/flags/'.strtolower($record['iso']).'.png')){
		// 		$flags = site_url().'assets/flags/'.strtolower($record['iso']).'.png';
		// 	}
		// 	if(file_exists('assets/flag_big/'.strtolower($record['iso']).'.png')){
		// 		$flag_big = site_url().'assets/flag_big/'.strtolower($record['iso']).'.png';
		// 	}
		// 	$result[$key]['flag_big'] = $flag_big;
		// 	$result[$key]['flag'] = $flags;
		// }
		// echo "<pre>";
		// print_r($result);
	}

	public function top_earners(){
	    
		$date = date('Y-m-d');
		$where2 = '';
		
			
		
		
		
		if(isset($_REQUEST['filter']) && !empty($_REQUEST['filter'])){
			$filter = $_REQUEST['filter'];
			
			if($filter=='Today'){
				$where2 = " and DATE(coin_transaction.create_date) = DATE('".$date."')";
			} else if($filter=='Week'){
				$where2 = " and WEEK(coin_transaction.create_date) = WEEK('".$date."')";
			} else if($filter=='Month'){
				$where2 = " and MONTH(coin_transaction.create_date) = MONTH('".$date."')";
			} else if($filter=='Year'){
				$where2 = " and YEAR(coin_transaction.create_date) = YEAR('".$date."')";
			}
			
						
		}
		
		$where = "user_type=1 and is_verified = 1 and (select count(id) from coin_transaction where coin_transaction.user_id = users.id $where2) > 0";
		
		if(isset($_REQUEST['start_date']) && !empty($_REQUEST['start_date'])){
			$where .=" and DATE(created_at) >= DATE('".$_REQUEST['start_date']."')";	
		}
		
		if(isset($_REQUEST['end_date']) && !empty($_REQUEST['end_date'])){
			$where .=" and DATE(created_at) <= DATE('".$_REQUEST['end_date']."')";			
		}
	    
	    
		$data["userData"] = $this->common_model->GetAllData('users',$where,"(select SUM(coin) from coin_transaction where coin_transaction.user_id = users.id $where2)",'desc',null,null,array('*',"(select SUM(coin) from coin_transaction where coin_transaction.user_id = users.id $where2) as total_coin"));
		//echo $this->db->last_query();
		$this->load->view('admin/top_earners', $data);
	}

	public function top_rich(){
	    
		 $date = date('Y-m-d');
		$where2 = '';
		
			
		
		
		
		if(isset($_REQUEST['filter']) && !empty($_REQUEST['filter'])){
			$filter = $_REQUEST['filter'];
			
			if($filter=='Today'){
				$where2 = " and DATE(transaction.date) = DATE('".$date."')";
			} else if($filter=='Week'){
				$where2 = " and WEEK(transaction.date) = WEEK('".$date."')";
			} else if($filter=='Month'){
				$where2 = " and MONTH(transaction.date) = MONTH('".$date."')";
			} else if($filter=='Year'){
				$where2 = " and YEAR(transaction.date) = YEAR('".$date."')";
			}
			
						
		}
		
		$where = "user_type=1 and is_verified = 1 and (select count(id) from transaction where transaction.user_id = users.id $where2) > 0";
		
		if(isset($_REQUEST['start_date']) && !empty($_REQUEST['start_date'])){
			$where .=" and DATE(created_at) >= DATE('".$_REQUEST['start_date']."')";	
		}
		
		if(isset($_REQUEST['end_date']) && !empty($_REQUEST['end_date'])){
			$where .=" and DATE(created_at) <= DATE('".$_REQUEST['end_date']."')";			
		}
	    
	    
		$data["userData"] = $this->common_model->GetAllData('users',$where,"(select SUM(diamond) from transaction where transaction.user_id = users.id $where2)",'desc',null,null,array('*',"(select SUM(diamond) from transaction where transaction.user_id = users.id $where2) as total_coin"));
		
		$this->load->view('admin/top_rich', $data);
	}

	public function change_status(){
		$UserId = $this->input->post('id');
		$update['status'] =$status = $this->input->post('status');
		
        $run = $this->common_model->UpdateData('users', array('id'=>$UserId),$update);
        if($run)
        {
        	if($status==1)
        	{
               $active = "activated";
        	}
        	else
        	{
               $active = "blocked";
        	}
			$this->session->set_flashdata('msgs','<div class="alert alert-success">Success! User has been '.$active.' successfully.</div>');	
			$json['status']=1;
		} else
		{
		    // $this->session->set_flashdata('msgs','<div class="alert alert-danger">Something is Worng.</div>');
		    $json['status']=0;
		    $json['message']='<div class="alert alert-danger">Something went worng.</div>';
		}

		//redirect('admin/user-list');
		echo json_encode($json);

	}

	function language_list()
	{
		$data["language"] = $this->common_model->GetAllData('language','','id','desc');
		$this->load->view('admin/language-list', $data);
	}
	
	function gift_listing()
	{
		$data["gifts"] = $this->common_model->GetAllData('gift','','id','desc');
		$data["category"]= $this->common_model->GetAllData('gift_category','',$ob='id',$obc='DESC');
		$this->load->view('admin/gift_listing', $data);
	}

	public function add_language()
	{

		$this->form_validation->set_rules('name','language name','trim|required|is_unique[language.name]');
		$this->form_validation->set_rules('code','language code','trim|required');
 		if($this->form_validation->run()==FALSE)
 		{
           	$json['message']='<div class="alert alert-danger">'.validation_errors().'</div>';
		$json['status']=0;
 		}
 		else
 		{
 		   $insert['name'] =$name = $this->input->post('name');
 		   $insert['code'] = $this->input->post('code');	
 		   $run=$this->common_model->InsertData('language',$insert);
           $this->session->set_flashdata('msgs','<div class="alert alert-success">Success! Language been added successfully.</div>');
		    $json['status']=1;
 		}
		
		
		echo json_encode($json);
	}

	public function edit_language()
	{
		$this->form_validation->set_rules('name','language name','trim|required');
		$this->form_validation->set_rules('code','language code','trim|required');
 		if($this->form_validation->run()==FALSE)
 		{
           $json['message']='<div class="alert alert-danger">'.validation_errors().'</div>';
		   $json['status']=0;
 		}
 		else
 		{
 		   $insert['name'] = $name = $this->input->post('name');
 		   $insert['code'] = $this->input->post('code');	
 		   $id = $this->input->post('id');	

 		   $alreadyLang = $this->common_model->GetSingleData('language', array('id != '=>$id, 'name' =>$name));
 		  if ($alreadyLang) {
 		  $json['message']='<div class="alert alert-danger">Language already exist.</div>';
		   $json['status']=0;
 		  }
 		  elseif ($this->common_model->UpdateData('language',array('id'=>$id),$insert)) {
 		  $this->session->set_flashdata('msgs','<div class="alert alert-success">Success! Language been updated successfully.</div>');
		    $json['status']=1;
 		  } else {
$json['message']='<div class="alert alert-danger">Something went wrong.</div>';
		   $json['status']=0;
 		  }
           
 		}
		echo json_encode($json);
	}


	public function delete_language()
	{
		
		$this->form_validation->set_rules('id','id','trim|required');
 		if($this->form_validation->run()==FALSE)
 		{
           $json['message']='<div class="alert alert-danger">'.validation_errors().'</div>';
		   $json['status']=0;
 		}
 		else
 		{
 		   $id = $this->input->post('id');	
 		   $run=$this->common_model->DeleteData('language',array('id'=>$id));
 		  
           $this->session->set_flashdata('msgs','<div class="alert alert-success">Success! Language been deleted successfully.</div>');
		    $json['status']=1;
 		}
		echo json_encode($json);
	}
	public function update_user_balance(){
		$user_id = $this->session->userdata('user_id');
		$url = $this->uri->segment(2);
		$id = $this->input->post('user_id');
		$insert['user_balance'] = $this->input->post('user_balance');
		$run = $this->common_model->UpdateData('users',array('id'=>$id),$insert);

          if($run){

             
				$this->session->set_flashdata('msgs','<div class="alert alert-success"><p style="margin-bottom: 0rem!important;">Users Balance Updated Successfully!</p></div>');



			} else {

				 $this->session->set_flashdata('msgs','<div class="alert alert-danger">Something went wrong.</div>');
             
	}
	       redirect('admin/users');
	}
	
	public function add_gift()
	{

		$this->form_validation->set_rules('daimond','daimond','trim|required');
		$this->form_validation->set_rules('name','name','trim|required');
		
 		if($this->form_validation->run()==FALSE)
 		{
 		   $json['status']=0;
           $_SESSION['error']='<div class="alert alert-danger">'.validation_errors().'</div>';
		   redirect('admin/gift_listing');
 		}
 		else
 		{
 		   $insert['daimond'] =$name = $this->input->post('daimond');
 		   $insert['coin'] = $this->input->post('coin');
 		   $insert['category'] =$name = $this->input->post('category');
 		   $insert['name'] =$name = $this->input->post('name');


 		   if($_FILES['sticker']['name']){

				$config['upload_path']="assets/admin/gift";

				$config['allowed_types'] = 'jpeg|gif|jpg|png';

				$config['encrypt_name']=true;

				$this->load->library("upload",$config);

				if ($this->upload->do_upload('sticker')) {

				$u_profile=$this->upload->data("file_name");

				$profile = $u_profile;

				$insert['sticker'] = $profile ;

				} 

				}
			if($_FILES['animation']['name']){

				$config['upload_path']="assets/admin/gift";

				$config['allowed_types'] = 'jpeg|gif|jpg|png';

				$config['encrypt_name']=true;

				$this->load->library("upload",$config);

				if ($this->upload->do_upload('animation')) {

				$u_profile1=$this->upload->data("file_name");

				$insert['animation'] = $u_profile1 ;

				} 

				}	
				
 		   $run=$this->common_model->InsertData('gift',$insert);
 		   
            $this->session->set_flashdata('msgs','<div class="alert alert-success">Success! Gift has been added successfully.</div>');

		    $json['status']=1;
		    $_SESSION['success']='<div class="alert alert-success">Success! Gift has been added successfully.</div>';

		    redirect('admin/gift_listing');

 		}
		
		
		echo json_encode($json);
		
	}
	
	public function edit_gift()
	{
	$id = $this->uri->segment(4);
		$this->form_validation->set_rules('daimond','daimond','trim|required');
		$this->form_validation->set_rules('coin','coin','trim|required');
		$this->form_validation->set_rules('category','category','trim|required');
		$this->form_validation->set_rules('name','name','trim|required');
		
 		if($this->form_validation->run()==FALSE)
 		{
 		   $json['status']=0;
           $_SESSION['error']='<div class="alert alert-danger">'.validation_errors().'</div>';
		   redirect('admin/gift_listing');
 		}
 		else
 		{
 		   $insert['daimond'] =$name = $this->input->post('daimond');
 		   $insert['coin'] = $this->input->post('coin');
 		   $insert['category'] =$name = $this->input->post('category');
 		   $insert['name'] =$name = $this->input->post('name');

 		   if($_FILES['sticker']['name']){

				$config['upload_path']="assets/admin/gift";

				$config['allowed_types'] = 'jpeg|gif|jpg|png';

				$config['encrypt_name']=true;

				$this->load->library("upload",$config);

				if ($this->upload->do_upload('sticker')) {

				$u_profile=$this->upload->data("file_name");

				$profile = $u_profile;

				$insert['sticker'] = $profile ;

				} 

				}
			if($_FILES['animation']['name']){

				$config['upload_path']="assets/admin/gift";

				$config['allowed_types'] = 'jpeg|gif|jpg|png';

				$config['encrypt_name']=true;

				$this->load->library("upload",$config);

				if ($this->upload->do_upload('animation')) {

				$u_profile1=$this->upload->data("file_name");

				$insert['animation'] = $u_profile1 ;

				} 

				}	
				
 		   $run = $this->common_model->UpdateData('gift',array('id'=>$id),$insert);
 		   
            $this->session->set_flashdata('msgs','<div class="alert alert-success">Success! Gift has been upadted successfully.</div>');

		    $json['status']=1;
		    $_SESSION['success']='<div class="alert alert-success">Success! Gift has been upadted successfully.</div>';

		    redirect('admin/gift_listing');

 		}
		
		
		echo json_encode($json);
		
	}
	
	
	public function delete_gift(){

		
		$id = $this->uri->segment(4);

		$run = $this->common_model->DeleteData('gift',array('id'=>$id));

          if($run){

             
				$this->session->set_flashdata('msgs','<div class="alert alert-success"><p>Gift deleted successfully!</p></div>');

                $_SESSION['success']='<div class="alert alert-success">Success! Gift has been deleted successfully.</div>';


			} else {

				 $this->session->set_flashdata('msgs','<div class="alert alert-danger">Something went wrong.</div>');
             
	}
	        redirect('admin/gift_listing');
	}

	public function diamond()
	{
		$data["daimond"] = $this->common_model->GetAllData('diamonds','','id','desc');
		$this->load->view('admin/diamond', $data);
	}

	public function AddDiamond()
	{
		$this->form_validation->set_rules('diamonds','diamonds','trim|required');
		$this->form_validation->set_rules('amount','amount','trim|required');
		if($this->form_validation->run()){
		$insert['diamonds'] = $this->input->post('diamonds');
		$insert['amount'] = $this->input->post('amount');
		$insert['free_call'] = $this->input->post('free_call');

		$run = $this->common_model->InsertData('diamonds', $insert);
			if($run){
								
			$output['status'] = 1;
			$this->session->set_flashdata('msgs','<div class="alert alert-success">Diamond has been added successfully.</div>');	
			}
			else {
			$output['status'] = 0;
			$output['message'] ='<div class="alert alert-danger">Something went Wrong!</div>';	
			}
		}//end if form validation
		else {
			$output['status'] = 0;
			$output['message'] = '<div class="alert alert-danger">'.validation_errors().'</div>';
		}//end else form validation
		echo json_encode($output);
	}

	public function EditDiamond()
	{
		$this->form_validation->set_rules('diamonds','diamonds','trim|required');
		$this->form_validation->set_rules('amount','amount','trim|required');
		if($this->form_validation->run()){
		$Id = $this->input->post('Id');
		$insert['diamonds'] = $this->input->post('diamonds');
		$insert['amount'] = $this->input->post('amount');
		$insert['free_call'] = $this->input->post('free_call');

		$run = $this->common_model->UpdateData('diamonds',array('id' => $Id), $insert);
			if($run){
								
			$output['status'] = 1;
			$this->session->set_flashdata('msgs','<div class="alert alert-success">Diamond has been updated successfully.</div>');	
			}
			else {
			$output['status'] = 0;
			$output['message'] ='<div class="alert alert-danger">Something went Wrong!</div>';	
			}
		}//end if form validation
		else {
			$output['status'] = 0;
			$output['message'] = '<div class="alert alert-danger">'.validation_errors().'</div>';
		}//end else form validation
		echo json_encode($output);
	}

	public function deleteDiamond(){

			$Id =$_GET["Id"];
			
			$run = $this->common_model->DeleteData('diamonds',array('id'=>$Id));
            //echo $this->db->last_query();
			if($run){

				$this->session->set_flashdata('msgs','<div class="alert alert-success">Success! Diamond has been deleted successfully.</div>');
				
			} else {
			    $this->session->set_flashdata('msgs','<div class="alert alert-danger">Something is Worng.</div>');
			    
			}
	redirect('admin/diamond');
 	}

 	public function AddTradeAmount()
 	{
 	 	$this->form_validation->set_rules('trade_balance','trade amount','trim|required');
 	 	if($this->form_validation->run()){
		
		$user_id = $this->input->post('UserId');
		$addAmt = $this->input->post('trade_balance');
		$update['is_trade_account'] = "1";
		$trade_balance = $this->common_model->GetSingleData('users', array('id' => $user_id));
		$update['trade_balance'] = $trade_balance["trade_balance"]+$addAmt;
		
		$run = $this->common_model->UpdateData('users', array('id' => $user_id), $update);
 	 		if($run){
							
			$output['status'] = 1;
			$this->session->set_flashdata('msgs','<div class="alert alert-success">Trade amount has been added successfully.</div>');	
			}
			else {
		$output['status'] = 0;
		$output['message'] ='<div class="alert alert-danger">Something went Wrong!</div>';	
			}



 	 		}//end if form validation
		else {
			$output['status'] = 0;
			$output['message'] = '<div class="alert alert-danger">'.validation_errors().'</div>';
		}//end else form validation
		echo json_encode($output);
 	}

 	public function AddWallet()
 	{
 		$this->form_validation->set_rules('type','type','trim|required');
		$this->form_validation->set_rules('amount','diamond','trim|required');
		if($this->form_validation->run()){

		$UserId = $this->input->post('UserId');
		$type = $this->input->post('type');
		$diamond = $this->input->post('amount');

		//$diamond = $data['diamonds'];
			//$free_call = $data['free_call'];
			//$amount = $data['amount'];
			
			$userdata = $this->common_model->GetColumnName('users',array('id'=>$UserId),array('id','diamond','free_call','device_id','gender'));
			if ($type == 1) {
					$update['diamond'] = $userdata['diamond']+$diamond;
			} else {
					$NewWalletAmt = $userdata["diamond"]-$diamond;
					if($NewWalletAmt > 0){
					  $update["diamond"] = $NewWalletAmt;
					}
					elseif($NewWalletAmt <= 0){
					  $update["diamond"] = 0;
					}
			}
			//$update['free_call'] = $userdata['free_call']+$free_call;
			
			if($userdata['gender']=='male' && $type == 1){
				$total_diamond = $this->common_model->GetColumnName('transaction',array('user_id'=>$UserId,'type'=>1,'status'=>1),array('SUM(diamond) as total'));
				$total_diamond = ($total_diamond['total']) ? $total_diamond['total'] : 0;
				$total_diamond = $total_diamond+$diamond;
				
				$get_level = $this->common_model->GetColumnName('level',array('gender'=>'male','daimond <= '=>$total_diamond),array('level'),false,'daimond','desc');
				
				$get_level = ($get_level['level']) ? $get_level['level'] : 0;
				
				$update['level'] = $get_level;
			}
			
			
			
			
		$run = $this->common_model->UpdateData('users',array('id'=>$UserId),$update);
			

		if($run){
			$output['status'] = 1;
			$this->session->set_flashdata('msgs','<div class="alert alert-success">Diamond has been Updated successfully.</div>');
			if ($type == 1) {
				
				$insert['user_id'] = $UserId;
				$insert['diamond'] = $diamond;
				$insert['transaction_id'] = $transaction_id = time();
				$insert['payment_method'] = 'Transfer';
				//$insert['free_call'] = $free_call;
				//$insert['amount'] = $amount;
				$insert['type'] = 1;
				$insert['status'] = 1;
				$insert['date'] = date('Y-m-d H:i:s');
				$insert['message'] = 'Added by Admin';
				$run = $this->common_model->InsertData('transaction',$insert);

				$notArr['user_id'] = $UserId;
				$notArr['message'] = "Admin added ".$diamond." diamonds in your balance";
				$notArr['behalf_of'] = 0;
				$notArr['device_id'] = $userdata['device_id'];
				
				$notArr['other'] = array('screen'=>'diamond_added','send_by'=>0,'send_to'=>$UserId, 'diamonds'=>$diamond,'sender_name'=>'Admin', 'admin'=>'1');
				$this->common_model->send_and_insert_notifi($notArr);
				
			} else {
				$insert['user_id'] = $UserId;
				$insert['diamond'] = $diamond;
				$insert['transaction_id'] = $transaction_id = time();
				$insert['payment_method'] = 'Deduct';
				//$insert['free_call'] = $free_call;
				//$insert['amount'] = $amount;
				$insert['type'] = 2;
				$insert['status'] = 1;
				$insert['date'] = date('Y-m-d H:i:s');
				$insert['message'] = 'Deduct by admin';
				$run = $this->common_model->InsertData('transaction',$insert);

			}
		} else {
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
 	public function EditAgent()
 	{
 		//print_r($this->input->post()); die;
 		$this->form_validation->set_rules('invited_by','agent','trim|required');
		$this->form_validation->set_rules('reason','reason','trim|required');
		if($this->form_validation->run()){

		$UserId = $this->input->post('UserId');
		$update["invited_by"] = $invited_by = $this->input->post('invited_by');
		$reason = $this->input->post('reason');

		$run = $this->common_model->UpdateData('users', array('id' => $UserId), $update);	
			if ($run) {
			
			$this->session->set_flashdata('msgs','<div class="alert alert-success">Agent has been changed successfully.</div>');
			$UserData = $this->common_model->GetSingleData('users', array('id' => $UserId));
			$Agent = $this->common_model->GetSingleData('users', array('uniqueID' => $invited_by));


			$subject = "Agent changed"; 
			/*$contant= 'Hello, '.$UserData["nickname"].' <br><br>';
			$contant .='<p class="text-center">This is an automated message. Donot reply this mail !!<p>';

			$contant .='<p class="text-center">Your agent has been changed. Because '.$reason.'!!</p>';

			$contant .='<p class="text-center">Your current agent is '.$Agent["nickname"].'!!</p>';
				
			$run = $this->common_model->SendMail($email,$subject,$contant);*/
			$message = $this->common_model->GetSingleData('email_template', array('id'=>7));
			$email_body = str_replace("[username]",$UserData["nickname"],$message["text"]);
			$email_body1 = str_replace("[messageByAdmin]",$reason,$email_body);
			$email_body2 = str_replace("[agentName]",$Agent["nickname"],$email_body1);
			$email_body3 = str_replace("assets/admin/EmailImg/",site_url()."assets/admin/EmailImg/",$email_body2);
			$contant ='<p class="text-center">'.$email_body3.'</p>';
				
			$this->common_model->SendMailCustom($UserData["email"],$subject,$contant);

			}
			else {
			$this->session->set_flashdata('msgs','<div class="alert alert-success">Something went wrong</div>');
			}
		}//end if form validation
		else {
			
			$this->session->set_flashdata('msgs','<div class="alert alert-danger">'.validation_errors().'</div>');
		}

	redirect('admin/users');	
 	}

 	public function ChangePassword($value='')
 	{
 		$this->form_validation->set_rules('password','new password','trim|required');
		$this->form_validation->set_rules('cpassword','confirm password ','trim|required|matches[password]');
		if($this->form_validation->run()){
			$update["password"] = $this->input->post('password');
			$UserId = $this->input->post('UserId');
			$run = $this->common_model->UpdateData('users', array('id'=>$UserId), $update);
					if ($run) {
					$output['status'] = 1;
					$this->session->set_flashdata('msgs','<div class="alert alert-success">password has been added successfully.</div>');
					}
					else {
					$output['status'] = 0;
					$output['message'] = $this->session->set_flashdata('msgs','<div class="alert alert-danger">Something went Wrong!</div>');
					}
			}//end if form validation
		else {
			$output['status'] = 0;
			$output['message'] = '<div class="alert alert-danger">'.validation_errors().'</div>';
		}//end else form validation
		echo json_encode($output);
 	}

 	public function export_csv(){ 
		/* file name */
		$filename = 'users_'.date('Ymd').'.csv'; 
		header("Content-Description: File Transfer"); 
		header("Content-Disposition: attachment; filename=$filename"); 
		header("Content-Type: application/csv; ");
	   /* get data */
	   	$sql = 'SELECT id, uniqueID, nickname, email, phone, dob, gender, country, status, is_verified, trade_balance FROM users WHERE user_type=1 ORDER BY id DESC';
	      $result = $this->db->query($sql);
	       $result->num_rows();
	   	$usersData = $result->result_array(); 

		//print_r($usersData); die;
		/* file creation */
		$file = fopen('php://output', 'w');
		$header = array("ID","uniqueID","User Name","Email", "Phone","Date of birth","Gender","country", "status", "verified","Trade balance"); 
		fputcsv($file, $header);
		foreach ($usersData as $key=>$line){
		$country = $this->common_model->GetSingleData('country',array('id'=>$line['country'])); 
		$line["country"] = $country["nicename"];
			fputcsv($file,$line); 
		}
		fclose($file); 
		exit; 
	}

	public function EditUser(){
		$this->form_validation->set_rules('uniqueID','Unique ID','trim|required');
		if($this->form_validation->run()){
		$update["nickname"] = $this->input->post('nickname');
		$update["dob"] = $this->input->post('dob');
		$update["gender"] = $this->input->post('gender');
		$update["city"] = $countId=  $this->input->post('city');
		$update["lng1"] = $this->input->post('lng1');
		$update["lng2"] = $countId=  $this->input->post('lng2');

		$update["uniqueID"] = $uniqueID=  $this->input->post('uniqueID');

		$User_id = $this->input->post('User_id');

		$update["country"] = $country =  $this->input->post('country');
		$update["phone"] = $phone =  trim($this->input->post('phone'));
		$update["email"] = $email =  trim($this->input->post('email'));

		$countryData = $this->common_model->GetSingleData('country',array('id'=>$country));
		$update["phone_with_code"] = trim($countryData["phonecode"]).$phone;


		$update["guardian_price"] = $this->input->post('guardian_price');
		$check = true;
                    if($_FILES['image']['name']){

                    $config['upload_path']="upload/users";
                    $config['allowed_types'] = 'jpeg|gif|jpg|png';
                    $config['encrypt_name']=true;
                    $this->load->library("upload",$config);
                    if ($this->upload->do_upload('image')) {
                    $u_profile=$this->upload->data("file_name");
                    $update['image'] = $u_profile;


                    } else {
                    $check = false;
                $output['status'] = 0;
                $output['message'] = '<div class="alert alert-danger">'.$this->upload->display_errors().'</div>';   
                    }
                    }

                    if ($phone !='') {
                    $alreadyPhone = $this->common_model->GetSingleData('users', array('phone'=>$phone, 'id != '=>$User_id));	
                    		if ($alreadyPhone) {
                    		$output['status'] = 0;
				$output['message'] = '<div class="alert alert-danger">Phone number already exist</div>';
				$check = false;
                    		}
                    }


                    if ($email !='') {
                    $alreadyEmail = $this->common_model->GetSingleData('users', array('email'=>$email, 'id != '=>$User_id));
                    		if ($alreadyEmail) {
				$output['status'] = 0;
				$output['message'] = '<div class="alert alert-danger">Email already exist</div>';
				$check = false;
                    		}
                    }


                    if ($check) {                    	                    	

                    	$alreadyUnique = $this->common_model->GetSingleData('users', array('uniqueID'=>$uniqueID, 'id != '=>$User_id));
			if ($alreadyUnique) {
                    		$output['status'] = 0;
				$output['message'] = '<div class="alert alert-danger">Unique id already exist.</div>';
                    	} elseif($this->common_model->UpdateData('users', array('id'=>$User_id),$update)){
					
					$output['status'] = 1;
					$this->session->set_flashdata('msgs','<div class="alert alert-success">Profile upadated successfully.</div>');	
					}
			else {
				$output['status'] = 0;
				$output['message'] = '<div class="alert alert-danger">Something went Wrong!</div>';	
					}
                    }
             }
		else {
			$output['status'] = 0;
			$output['message'] = '<div class="alert alert-danger">'.validation_errors().'</div>';
		}
		
		echo json_encode($output);
	}

}?>
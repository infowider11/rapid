<?php
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);
ini_set('display_errors',0);
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

		$this->opentokApi='47382001';
		$this->opentokApiSecret='696d97e8ef45c2275b2edbff28c14819294d8179'; 

		//https://www.webwiders.in/WEB01/rapid/api
  }
	
	
	public function index(){
		$output['status'] = 0;
		$output['message'] = 'Web service not found';
		echo json_encode($output);
	}
	
	/*POST API*/
	
	public function create_post() {
		
	 	$this->form_validation->set_rules('user_id','user_id','required');
 	 	//$this->form_validation->set_rules('message','message','required');
		
 	 	if($this->form_validation->run())
		{
 	 		
			$msgJson = $this->input->post('msgJson');
			
			if($msgJson){
				$msgJson = json_decode($msgJson);
			} else {
				$msgJson = array();
			}

			$msgJson = serialize($msgJson);


			$tags = $this->input->post('tags');
			$message = $this->input->post('message');
			
			if(isset($tags) && !empty($tags)){
				$insert['tags'] = $tags;
			}
			
			
 	 		$insert['msgJson'] = $msgJson;
 	 		$insert['user_id'] = $this->input->post('user_id');
 	 		$insert['hashtag'] = $this->input->post('hashtag');
 	 		$insert['location'] = $this->input->post('location');
 	 		$insert['latitude'] = $this->input->post('latitude');
 	 		$insert['longitude'] = $this->input->post('longitude');
 	 		$insert['message'] = $this->input->post('message');
			$insert['created_at'] = date('Y-m-d H:i:s');
			$insert['updated_at'] = date('Y-m-d H:i:s');
			
			
			$insert['type'] = 'POST';
			
 	 		$run = $this->common_model->InsertData('post',$insert);
 	 		if ($run) {
				
				if(isset($_FILES['images']['name']) && !empty($_FILES['images']['name'])){
					foreach($_FILES['images']['name'] as $key => $newFile){
						
						$file_array=explode(".",$_FILES['images']['name'][$key]);
						$file_ext=end($file_array);
						$rand=rand(222,999999);
						$file_name_thumb='upload/post/'.$rand.time().".".$file_ext;
						
						
						if(move_uploaded_file($_FILES['images']['tmp_name'][$key],$file_name_thumb)) {
							$insert1 = array();
							$insert1['post_id'] =$run;
							$insert1['name'] =$file_name_thumb;
							$this->common_model->InsertData('post_image',$insert1);
						}
						
					}
				}
				
				
				if(isset($tags) && !empty($tags)){
					
					$tag_users = $this->common_model->GetColumnName('users',"id in (".$tags.")",array('device_id','id'),true); 
					
					$userdata = $this->common_model->GetColumnName('users',array('id'=>$insert['user_id']),array('nickname'));
					foreach($tag_users as $tag_user){
						
							$notArr['user_id'] = $tag_user['id'];
							$notArr['message'] = $userdata['nickname']." has mentioned you in his post.";
							$notArr['behalf_of'] = $insert['user_id'];
							$notArr['type'] = 2; //2 for post/moment
							$notArr['device_id'] = $tag_user['device_id'];
							
							$notArr['other'] = array('screen'=>'moment','mention_by'=>$insert['user_id'],'post_id' =>$run);
							$this->common_model->send_and_insert_notifi($notArr);
						
					}
				}
				
				
				
 	 			$output['status'] = 1;
 	 			$output['data'] = $this->post_data($run,$insert['user_id']);
				$output['message'] = "Your post created successfully.";
 	 		} else {
				$output['status'] = 0;
				$output['message'] = "Something went wrong, try again later.";
			}

 	 	}else{
 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
 	 	echo json_encode($output);
	}
	
	public function edit_post() {
	 	$this->form_validation->set_rules('post_id','post_id','required');
	 	$this->form_validation->set_rules('user_id','post_id','required');
 	 	if($this->form_validation->run()) {

			$msgJson = $this->input->post('msgJson');

			if($msgJson){
				$msgJson = json_decode($msgJson);
			} else {
				$msgJson = array();
			}

			$msgJson = serialize($msgJson);
			
			
 	 		$insert['msgJson'] = $msgJson;
			
 	 		$id = $this->input->post('post_id');
 	 		$user_id = $this->input->post('user_id');
 	 		
 	 		$insert['message'] = $this->input->post('message');
			
			
			$insert['start_date'] = date('Y-m-d');
 	 		$insert['end_date'] = date('Y-m-d',strtotime($insert['start_date'] . ' + 5 year'));
			
			if(isset($_FILES['file']['name']) && !empty($_FILES['file']['name'])){
				$config['upload_path']="upload/post/";
				$config['allowed_types'] = '*';
				//$config['encrypt_name']=true;
				$this->load->library("upload",$config);
				
				if ($this->upload->do_upload('file')) {
					$file=$this->upload->data("file_name");
					$insert['file'] = $file;
					$insert['file_type'] = $this->input->post('file_type');
				}
			}
			
			if(isset($_FILES['thumb']['name']) && !empty($_FILES['thumb']['name'])){
				$config['upload_path']="upload/post/";
				$config['allowed_types'] = '*';
				//$config['encrypt_name']=true;
				$this->load->library("upload",$config);
				
				if ($this->upload->do_upload('thumb')) {
					$thumb=$this->upload->data("file_name");
					$insert['thumb'] = $thumb;
				}
			}
				
			$tags = $this->input->post('tags');
			
			if(isset($tags) && !empty($tags)){
				$insert['tags'] = $tags;
			} 
			
			$insert['updated_at'] = date('Y-m-d H:i:s');
 	 		$run = $this->common_model->UpdateData('post',array('id'=>$id,'user_id'=>$user_id),$insert);
 	 		if ($run) {
				
				$output['data'] = $this->post_data($id,$user_id);
				
 	 			$output['status'] = 1;
				$output['message'] = "Your post updated successfully.";
 	 		} else {
				$output['status'] = 0;
				$output['message'] = "Something went wrong, try again later.";
			}

 	 	}else{
 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
 	 	echo json_encode($output);
	}
	
	public function DeletePost() {
	
 	 	if(isset($_REQUEST['post_id']) && isset($_REQUEST['user_id'])) {
			
 	 		$id = $_REQUEST['post_id'];
 	 		$user_id = $_REQUEST['user_id'];
			
 	 		$run = $this->common_model->DeleteData('post',array('id'=>$id,'user_id'=>$user_id));
			
 	 		if ($run) {
				
				$this->common_model->DeleteData('post_comment',array('post_id'=>$id));
				$this->common_model->DeleteData('post_like',array('post_id'=>$id));
				
 	 			$output['status'] = 1;
				$output['message'] = "Your post has been deleted successfully.";
 	 		} else {
				$output['status'] = 0;
				$output['message'] = "Something went wrong, try again later.";
			}

 	 	}else{
 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
 	 	echo json_encode($output);
	}
	
	public function getPosts(){  
		if(isset($_REQUEST['type']) && isset($_REQUEST['user_id'])) {

			$user_id = $_REQUEST['user_id'];
			$type = $_REQUEST['type'];
			
			$today = date('Y-m-d');
			$current = date('Y-m-d H:i:s');
			$stickyTime = date('Y-m-d H:i:s',strtotime($current.' - 7 days'));
			
			
			$sql = "select id from post where ";
			
			$where = "1 = 1";
			
			if($type=='following'){
				$where .= " and (select count(id) from followers where followers.user_id = $user_id and followers.follow_Id = post.user_id) > 0";
			}
			if($type=='featured'){
				$where .= " and DATE(created_at) >= DATE('".$stickyTime."')";
			}
			
			if(isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])){
				$where .= " and (message like '%".$_REQUEST['keyword']."%' or hashtag like '%".$_REQUEST['keyword']."%')";
			}
			
			$sql = "select * from post where $where";
			
			$postCount = $this->common_model->GetColumnName('post',$where,array('count(id) as total'));
			
			$output['postCount'] = ($postCount) ? $postCount['total'] : 0;
			
			
			
			
			if($type=='featured'){
				
				$sql .= " ORDER BY (select count(id) from post_like where post_like.post_id = post.id) DESC";
				$sql .= ", (select count(id) from post_comment where post_comment.post_id = post.id) DESC";
				
				
			} else {
				$sql .= " ORDER BY id DESC";
			}
			
			
			
			//$sql .= " limit $start, $limit";
			
			$run = $this->db->query($sql);
			$getallpost=array();
			
			if($run->num_rows() > 0) {
				foreach ($run->result_array() as $key => $value) {
					
					$getallpost[$key] = $this->post_data($value['id'],$user_id);
					
				}
			}
			
			$output['data'] = $getallpost;
			$output['status'] = 1;
			$output['message'] = 'Success.';
			
		}else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
 	 	echo json_encode($output);
	}
	
	public function GetPostByUserID(){  
		if(isset($_REQUEST['post_user_id']) && isset($_REQUEST['user_id'])) {

			//$company_id = $_REQUEST['company_id'];
			$post_user_id = $_REQUEST['post_user_id'];
			$user_id = $_REQUEST['user_id'];
			
			$today = date('Y-m-d');
			$current = date('Y-m-d H:i:s');
			$stickyTime = date('Y-m-d H:i:s',strtotime($current.' - 12 hours'));
			
			
			$sql = "select id from post where ";
			
			$where = "user_id = $post_user_id";

			if(isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])){
				$where .= " and (message like '%".$_REQUEST['keyword']."%' or hashtag like '%".$_REQUEST['keyword']."%')";
			}
			
			$sql = "select * from post where $where";
			
			$postCount = $this->common_model->GetColumnName('post',$where,array('count(id) as total'));
			
			$output['postCount'] = ($postCount) ? $postCount['total'] : 0;
			
			$sql .= " ORDER BY is_pinned DESC, id DESC";
			
			
			//$sql .= " limit $start, $limit";
			
			$run = $this->db->query($sql);
			$getallpost=array();
			
			if($run->num_rows() > 0) {
				foreach ($run->result_array() as $key => $value) {
					
					$getallpost[$key] = $this->post_data($value['id'],$user_id);
					
				}
			}
			
			$output['data'] = $getallpost;
			$output['status'] = 1;
			$output['message'] = 'Success.';
			
		}else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
 	 	echo json_encode($output);
	}
	
	public function GetSinglePost(){  
		if(isset($_REQUEST['post_id']) && isset($_REQUEST['user_id'])) {

			//$company_id = $_REQUEST['company_id'];
			$post_id = $_REQUEST['post_id'];
			$user_id = $_REQUEST['user_id'];
			
			
			$getpost = $this->post_data($post_id,$user_id);
			
			if ($getpost) {
				
				$output['data'] = $getpost;
				$output['status'] = 1;
				$output['message'] = 'Success.';
				
			} else {
				
				$output['status'] = 0;
				$output['message'] = 'We did not find any records.';
				
			}
			
		}else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
 	 	echo json_encode($output);
	}
	
	public function LikePost() {
	
		if(isset($_REQUEST['post_id']) && isset($_REQUEST['user_id'])) {
			
			$post_id = $_REQUEST['post_id'];
			$user_id = $_REQUEST['user_id'];
			
			$postresult = $this->common_model->GetSingleData('post_like',array('post_id'=>$post_id,'user_id'=>$user_id));
			
			if ($postresult) {
				$output['status'] = 1;
				$output['message'] = "You have like successfully.";
			}else{
				
				$insert['post_id'] = $post_id;
				$insert['user_id'] = $user_id;
				$insert['created_at'] = date('Y-m-d H:i:s');
				
				$run = $this->common_model->InsertData('post_like',$insert);
				
				if($run) {
					
					//$this->common_model->UpdateData('post',array('id'=>$post_id),array('updated_at'=>date('Y-m-d H:i:s')));
					
					$postData = $this->common_model->GetColumnName('post',array('id'=>$post_id),array('user_id'));
					$postUser = $this->common_model->GetColumnName('users',array('id'=>$postData['user_id']),array('device_id'));
					$userdata = $this->common_model->GetColumnName('users',array('id'=>$user_id),array('nickname'));
					
					if($postData['user_id'] != $user_id){

						$notArr['user_id'] = $postData['user_id'];
						$notArr['message'] = $userdata['nickname']." has liked your post.";
						$notArr['behalf_of'] = $user_id;
						$notArr['type'] = 2; //2 for post/moment
						$notArr['device_id'] = $postUser['device_id'];
						
						$notArr['other'] = array('screen'=>'moment','like_by'=>$user_id,'post_user_id'=>$postData['user_id'],'post_id' =>$post_id);
						$this->common_model->send_and_insert_notifi($notArr);
					
					}
	
	 	 			$output['status'] = 1;
					$output['message'] = "You have like successfully.";
				} else {
					$output['status'] = 0;
					$output['message'] = "Something went wrong, try again later!";
				}
			}
		} else {
 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
 	 	echo json_encode($output);

	}
		
	public function postReport() {
	
		$this->form_validation->set_rules('post_id','post_id','required');
		$this->form_validation->set_rules('reported_by','reported_by','required');
 	 	//$this->form_validation->set_rules('message','message','required');
		
 	 	if($this->form_validation->run())
		{
			
			$post_id = $this->input->post('post_id');
			$reported_by = $this->input->post('reported_by');
			$message = $this->input->post('message');
			$category = $this->input->post('category');
			
			$insert['post_id'] = $post_id;
			$insert['reported_by'] = $reported_by;
			$insert['message'] = $message;
			$insert['category'] = $category;
			$insert['create_date'] = date('Y-m-d');
			
			$postresult = $this->common_model->InsertData('post_reports',$insert);
			
			if ($postresult) {
				
				$postData = $this->common_model->GetColumnName('post',array('id'=>$post_id),array('user_id'));
				$postUser = $this->common_model->GetColumnName('users',array('id'=>$postData['user_id']),array('device_id'));
				$userdata = $this->common_model->GetColumnName('users',array('id'=>$reported_by),array('nickname'));
				
				if($postData['user_id'] != $reported_by){

					$notArr['user_id'] = $postData['user_id'];
					$notArr['message'] = $userdata['nickname']." has reported your post.";
					$notArr['behalf_of'] = $reported_by;
					$notArr['type'] = 2; //2 for post/moment
					$notArr['device_id'] = $postUser['device_id'];
					
					$notArr['other'] = array('screen'=>'moment','report_by'=>$reported_by,'post_user_id'=>$postData['user_id'],'post_id' =>$post_id);
					$this->common_model->send_and_insert_notifi($notArr);
				
				}
				
				
				$output['status'] = 1;
				$output['message'] = "You have successfully reported the post.";
			}else{
				$output['status'] = 0;
				$output['message'] = "Something went wrong, try again later!";
			}
		} else {
 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
 	 	echo json_encode($output);

	}
		
	public function pinPost() {
	
		if(isset($_REQUEST['post_id']) && isset($_REQUEST['user_id'])) {
			
			$post_id = $_REQUEST['post_id'];
			$user_id = $_REQUEST['user_id'];
			
			$this->common_model->UpdateData('post',array('user_id'=>$user_id),array('is_pinned'=>0));
			$this->common_model->UpdateData('post',array('id'=>$post_id,'user_id'=>$user_id),array('is_pinned'=>1));
			
			$output['status'] = 1;
			$output['message'] = "You have been pinned successfully.";
		} else {
 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
 	 	echo json_encode($output);

	}
		
	public function unpinPost() {
	
		if(isset($_REQUEST['post_id']) && isset($_REQUEST['user_id'])) {
			
			$post_id = $_REQUEST['post_id'];
			$user_id = $_REQUEST['user_id'];
			
			//$this->common_model->UpdateData('post',array('user_id'=>$user_id),array('is_pinned'=>0));
			$this->common_model->UpdateData('post',array('id'=>$post_id,'user_id'=>$user_id),array('is_pinned'=>0));
			
			$output['status'] = 1;
			$output['message'] = "You have been unpinned successfully.";
		} else {
 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
 	 	echo json_encode($output);

	}
	
	public function UnlikePost() {
	
		if(isset($_REQUEST['post_id']) && isset($_REQUEST['user_id'])) {
			
			$post_id = $_REQUEST['post_id'];
			$user_id = $_REQUEST['user_id'];
			
			$postresult = $this->common_model->DeleteData('post_like',array('post_id'=>$post_id,'user_id'=>$user_id));
			
			if ($postresult) {
				
				//$this->common_model->UpdateData('post',array('id'=>$post_id),array('updated_at'=>date('Y-m-d H:i:s')));

				$output['status'] = 1;
				$output['message'] = "You have been unlike successfully.";
			}else{
				$output['status'] = 0;
				$output['message'] = "Something went wrong, try again later!";
			}
		} else {
 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
 	 	echo json_encode($output);

	}
		
	public function LikeComment() {
	
		if(isset($_REQUEST['comment_id']) && isset($_REQUEST['user_id']) && isset($_REQUEST['post_id'])) {
			
			$comment_id = $_REQUEST['comment_id'];
			$user_id = $_REQUEST['user_id'];
			$post_id = $_REQUEST['post_id'];
			
			$postresult = $this->common_model->GetSingleData('post_like',array('comment_id'=>$comment_id,'user_id'=>$user_id));
			
			if ($postresult) {
				$output['status'] = 1;
				$output['message'] = "You have like successfully.";
			}else{
				
				$insert['comment_id'] = $comment_id;
				$insert['user_id'] = $user_id;
				$insert['created_at'] = date('Y-m-d H:i:s');
				
				$run = $this->common_model->InsertData('post_like',$insert);
				
				if($run) {
					
					
					$postData = $this->common_model->GetColumnName('post',array('id'=>$post_id),array('user_id'));
					$commentData = $this->common_model->GetColumnName('post_comment',array('id'=>$comment_id),array('user_id'));
					$postUser = $this->common_model->GetColumnName('users',array('id'=>$commentData['user_id']),array('device_id'));
					$userdata = $this->common_model->GetColumnName('users',array('id'=>$user_id),array('nickname'));
					
					if($commentData['user_id'] != $user_id){

						$notArr['user_id'] = $commentData['user_id'];
						$notArr['message'] = $userdata['nickname']." has like your comment.";
						$notArr['behalf_of'] = $user_id;
						$notArr['type'] = 2; //2 for post/moment
						$notArr['device_id'] = $postUser['device_id'];
						
						$notArr['other'] = array('screen'=>'moment','liked_by'=>$user_id,'post_user_id'=>$postData['user_id'],'post_id' =>$post_id,'comment_id' =>$comment_id,'comment_by' =>$commentData['user_id']);
						$this->common_model->send_and_insert_notifi($notArr);
					
					}
					
					//$this->common_model->UpdateData('post',array('id'=>$post_id),array('updated_at'=>date('Y-m-d H:i:s')));
	
	 	 			$output['status'] = 1;
					$output['message'] = "You have like successfully.";
				} else {
					$output['status'] = 0;
					$output['message'] = "Something went wrong, try again later!";
				}
			}
		} else {
 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
 	 	echo json_encode($output);

	}
	
	public function UnlikeComment() {
	
		if(isset($_REQUEST['comment_id']) && isset($_REQUEST['user_id'])) {
			
			$comment_id = $_REQUEST['comment_id'];
			$user_id = $_REQUEST['user_id'];
			
			$postresult = $this->common_model->DeleteData('post_like',array('comment_id'=>$comment_id,'user_id'=>$user_id));
			
			if ($postresult) {
				
				//$this->common_model->UpdateData('post',array('id'=>$post_id),array('updated_at'=>date('Y-m-d H:i:s')));

				$output['status'] = 1;
				$output['message'] = "You have been unlike successfully.";
			}else{
				$output['status'] = 0;
				$output['message'] = "Something went wrong, try again later!";
			}
		} else {
 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
 	 	echo json_encode($output);

	}
	
	public function CommentOnPost() {
	
		if(isset($_REQUEST['post_id']) && isset($_REQUEST['user_id']) && isset($_REQUEST['comment'])) {
			
			$post_id = $_REQUEST['post_id'];
			$user_id = $_REQUEST['user_id'];
			$comment = $_REQUEST['comment'];

			$tags = (isset($_REQUEST['tags'])) ? $_REQUEST['tags'] : '';
		
			if(isset($tags) && $tags != ''){
				$insert['tags'] = $tags;
				/*$tagsArray = explode(',',$tags);
				foreach($tagsArray as $tagID){
					$tagUser = $this->common_model->GetColumnName('user',array('id'=>$tagID),array('name'));

					$tagUserName = '@'.$tagUser['name'];
					
					$spanTag = '<span data-id="'.$tagID.'" class="userMention">'.$tagUserName.'<span>';

					$comment = str_replace($tagUserName,$spanTag,$comment);
					
				}
				*/
			}

			
			if(isset($_REQUEST['msgJson']) && !empty($_REQUEST['msgJson'])){
				$msgJson = json_decode($_REQUEST['msgJson']);
			} else {
				$msgJson = array();
			}

			$msgJson = serialize($msgJson);
			
			
 	 		$insert['msgJson'] = $msgJson;
			
			$insert['post_id'] = $post_id;
			$insert['user_id'] = $user_id;
			$insert['comment'] = $comment;
			$insert['create_date'] = date('Y-m-d H:i:s');
			
			
			$run = $this->common_model->InsertData('post_comment',$insert);
			
			if($run) {
				
				$postData = $this->common_model->GetColumnName('post',array('id'=>$post_id),array('user_id'));
		
				$postUser = $this->common_model->GetColumnName('users',array('id'=>$postData['user_id']),array('device_id'));
				$userdata = $this->common_model->GetColumnName('users',array('id'=>$user_id),array('nickname'));
				
				if($postData['user_id'] != $user_id){

					$notArr['user_id'] = $postData['user_id'];
					$notArr['message'] = $userdata['nickname']." has commented at your post.";
					$notArr['behalf_of'] = $user_id;
					$notArr['type'] = 2; //2 for post/moment
					$notArr['device_id'] = $postUser['device_id'];
					
					$notArr['other'] = array('screen'=>'moment','comment_by'=>$user_id,'post_user_id'=>$postData['user_id'],'post_id' =>$post_id,'comment_id' =>$run);
					$this->common_model->send_and_insert_notifi($notArr);
				
				}

				$output['status'] = 1;
				$output['message'] = "You have been commented successfully.";
			} else {
				$output['status'] = 0;
				$output['message'] = "Something went wrong, try again later!";
			}
			
		} else {
 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
 	 	echo json_encode($output);

	}
	
	public function DeletePostComment() {
	
		if(isset($_REQUEST['comment_id']) && isset($_REQUEST['user_id']) && isset($_REQUEST['post_id'])) {
			
			$id = $_REQUEST['comment_id'];
			$user_id = $_REQUEST['user_id'];
			$post_id = $_REQUEST['post_id'];
			
			$postresult = $this->common_model->DeleteData('post_comment',array('id'=>$id,'user_id'=>$user_id));
			
			if ($postresult) {
				
				$output['status'] = 1;
				$output['message'] = "Your comment has been deleted successfully.";
			}else{
				$output['status'] = 0;
				$output['message'] = "Something went wrong, try again later!";
			}
		} else {
 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
 	 	echo json_encode($output);

	}
	
	public function CommentLists() {
		
		if(isset($_REQUEST['post_id']) && isset($_REQUEST['user_id'])) {
			
			$post_id = $_REQUEST['post_id'];
			$user_id = $_REQUEST['user_id'];
			
			$commentlist=$this->common_model->GetAllData('post_comment',array('post_id'=>$post_id),'id','desc');
			
			$result = array();
			
			if (!empty($commentlist)) {

				$i = 0;
				
				foreach($commentlist as $key => $value){
					
					$user = $this->very_short_profile($value['user_id']);
					
					if($user){
						
						if($value['msgJson']){
							$msgJson = unserialize($value['msgJson']);
						} else {
							$msgJson = array();
						}

					
						
						$value['msgJson'] = $msgJson;
						$value['create_date'] = time_ago($value['create_date']);
						$result[$i] = $value;
						$result[$i]['user'] = $user;
						
						$tags = array();
						if($value['tags']){
							$tagUser = $this->common_model->GetColumnName('users',"id in (".$value['tags'].")",array('id'),true);
							
							foreach($tagUser as $key2 => $value2){
								//$tags[] = $value2;
								$tags[] = $this->very_short_profile($value2['id']);
								
							}
						}
						$result[$i]['tags'] = $tags;
						
						
						/*check is like*/
						$is_like = 0;
						$check_like = $this->common_model->GetColumnName('post_like',array('user_id'=>$user_id,'comment_id'=>$value['id']),array('id'));
						if($check_like){
							$is_like = 1;
						}
						$result[$i]['is_like']=$is_like;
						/*check is like*/
						
						
						/*total like and comment*/
						$total_like = $this->common_model->GetColumnName('post_like',array('comment_id'=>$value['id']),array('count(id) as total'));
						
						$result[$i]['total_like']= ($total_like) ? $total_like['total'] : 0;

						$i++;
					}
				}
				
			}	
			$output['data'] = $result;
			$output['status'] = 1;
			$output['message'] = "Success!";
		}else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
 	 	echo json_encode($output);

	}

	public function PostLikeLists() {
		
		if(isset($_REQUEST['post_id'])) {
			
			$post_id = $_REQUEST['post_id'];
			
			$commentlist=$this->common_model->GetAllData('post_like',array('post_id'=>$post_id),'id','desc');
			
			$result = array();
			
			if (!empty($commentlist)) {

				$i = 0;
				
				foreach($commentlist as $key => $value){
					
					$user = $this->very_short_profile($value['user_id']);
					
					if($user){
						$result[$i]['like_data'] = $value;
						$result[$i]['like_data']['created_at'] = time_ago($value['created_at']);
						$result[$i]['user'] = $user;

						$i++;
					}
				}
				
			}	
			$output['data'] = $result;
			$output['status'] = 1;
			$output['message'] = "Success!";
		}else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
 	 	echo json_encode($output);

	}

	public function CommentLikeLists() {
		
		if(isset($_REQUEST['comment_id'])) {
			
			$comment_id = $_REQUEST['comment_id'];
			
			$commentlist=$this->common_model->GetAllData('post_like',array('comment_id'=>$comment_id),'id','desc');
			
			$result = array();
			
			if (!empty($commentlist)) {

				$i = 0;
				
				foreach($commentlist as $key => $value){
					
					$user = $this->very_short_profile($value['user_id']);
					
					if($user){
						$result[$i]['like_data'] = $value;
						$result[$i]['like_data']['created_at'] = time_ago($value['created_at']);
						$result[$i]['user'] = $user;

						$i++;
					}
				}
				
			}	
			$output['data'] = $result;
			$output['status'] = 1;
			$output['message'] = "Success!";
		}else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
 	 	echo json_encode($output);

	}

	private function post_data($id='',$user_id=0) {
		$output = $this->common_model->GetDataById('post',$id);

		if ($output) {

			if($output['msgJson']){
				$msgJson = unserialize($output['msgJson']);
			} else {
				$msgJson = array();
			}

			$output['msgJson'] = $msgJson;
			$output['created_at'] = time_ago($output['created_at']);
			
			$post_by= $this->very_short_profile($output['user_id']);
			
			$output['post_by']=$post_by;
			
			/*check is like*/
			$is_like = 0;
			$check_like = $this->common_model->GetColumnName('post_like',array('user_id'=>$user_id,'post_id'=>$id),array('id'));
			if($check_like){
				$is_like = 1;
			}
			$output['is_like']=$is_like;
			/*check is like*/
			
			/*check is follow*/
			$is_follow = 0;
			$check_follow = $this->common_model->GetColumnName('followers',array('user_id'=>$user_id,'follow_Id'=>$output['user_id']),array('id'));
			if($check_follow){
				$is_follow = 1;
			}
			$output['post_by']['is_follow']=$is_follow;
			/*check is follow*/
			
			/*Gift count*/
			$total_gifts = 0;
			
			
			$check_gifts = $this->common_model->GetColumnName('gift_send',array('send_to'=>$output['user_id']),array('SUM(quantity) as total'));
			if($check_gifts){
				$total_gifts = $check_gifts['total'];
			}
			$output['total_gifts']=$total_gifts;
			/*check is follow*/
			
			
			/*total like and comment*/
			$total_like = $this->common_model->GetColumnName('post_like',array('post_id'=>$id),array('count(id) as total'));
			
			$output['total_like']= ($total_like) ? $total_like['total'] : 0;
			
			
			$total_comment = $this->common_model->GetColumnName('post_comment',array('post_id'=>$id),array('count(id) as total'));
			
			$output['total_comment'] = ($total_comment) ? $total_comment['total'] : 0;
			/*total like and comment*/
			
			$images = $this->common_model->GetAllData('post_image',array('post_id'=>$id),'id','asc',null,null,array('*',"CONCAT('" .site_url() ."',name) AS image")); 
			$output['images'] = $images;
			
			
			
			
			$tags = array();
			if($output['tags']){
				$tagUser = $this->common_model->GetColumnName('users',"id in (".$output['tags'].")",array('id'),true);
				
				foreach($tagUser as $key2 => $value2){
					//$tags[] = $value2;
					$tags[] = $this->very_short_profile($value2['id']);
					
				}
			}
			$output['tags'] = $tags;
			
			
			
		}
		
		return $output;
	}

	/*POST API*/
	
	public function CancelTransaction(){
		
		if(isset($_REQUEST['user_id']) && isset($_REQUEST['last_id'])){
			
			$user_id = $_REQUEST['user_id'];
			$id = $_REQUEST['last_id'];
			
			$update['status'] = 2;
			$where['user_id'] = $user_id;
			$where['id'] = $id;
			
			$data = $this->common_model->UpdateData('transaction',$where,$update); 
			
			$output['status'] = 1;
			$output['message'] = 'Transaction has been cancelled successfully.';
			
			
			
		} else {
			$output['status'] = 0;
			$output['message'] = 'Check Parameter';
		}
		
		
		echo json_encode($output);
		
	}
	
	public function LuckDrawPriceList(){
		
		if(isset($_REQUEST['user_id'])){
			
			$user_id = $_REQUEST['user_id'];
			
			$today = date('Y-m-d');
			
			$data = $this->common_model->GetAllData('luck_draw_diamonds'); 
			
			$result = array();
			
			foreach($data as $key => $value){
				$result[$key] = $value;
				$result[$key]['user_rank'] = explode(',',$value['user_rank']);
				$result[$key]['user_diamond'] = explode(',',$value['user_diamond']);
			}
			
			$output['status'] = 1;
			$output['message'] = 'Success';
			$output['data'] = $result;
			
			
			
		} else {
			$output['status'] = 0;
			$output['message'] = 'Check Parameter';
		}
		
		
		echo json_encode($output);
		
	}
	
	
	public function InsertSpinWheel(){
		//https://www.webwiders.in/WEB01/rapid/api/InsertSpinWheel
		if(isset($_REQUEST['user_id']) && isset($_REQUEST['used_diamonds']) && isset($_REQUEST['win_diamonds']) && isset($_REQUEST['spin_id']) && isset($_REQUEST['qty'])){
			
			$user_id = $_REQUEST['user_id'];
			$used_diamonds = $_REQUEST['used_diamonds'];
			$win_diamonds = isset($_REQUEST['win_diamonds']) ? $_REQUEST['win_diamonds']*1 : 0;
			$is_win = 0;
			if($win_diamonds > 0){
				$is_win = 1;
			}
			
			$insert1['user_id'] = $user_id;
			$insert1['used_diamonds'] = $used_diamonds;
			//$insert1['win_diamonds'] = $win_diamonds;
			$insert1['spin_id'] = $_REQUEST['spin_id'];
			$insert1['qty'] = $_REQUEST['qty'];
			$insert1['is_win'] = $is_win;
			$insert1['create_date'] = date('Y-m-d H:i:s');
			
			$run = $this->common_model->InsertData('spin_wheel_records',$insert1);
			
			$userdata = $this->common_model->GetColumnName('users',array('id'=>$user_id),array('id','diamond','free_call','device_id','gender'));
			
			$update1['diamond'] = $userdata['diamond']-$used_diamonds;
			
			$this->common_model->UpdateData('users',array('id'=>$user_id),$update1);
			
			

			$insert2['user_id'] = $user_id;
			$insert2['diamond'] = $used_diamonds;
			$insert2['transaction_id'] = time().rand();
			$insert2['payment_method'] = 'Spin Wheel';
			$insert2['free_call'] = 0;
			$insert2['amount'] = 0;
			$insert2['type'] = 2;
			$insert2['status'] = 1;
			$insert2['date'] = date('Y-m-d H:i:s');
			$insert2['message'] = 'Used for Spin Wheel';
			$this->common_model->InsertData('transaction',$insert2);
			
			$output['status'] = 1;
			$output['id'] = $run;
			$output['message'] = 'Success';
			 /*
			if($is_win==1){
				$output['is_win'] = 1;
				
				$payment_method = 'Spin Wheel';
				$transaction_id = time();
				
				$diamond = $win_diamonds;
				$free_call = 0;
				$amount = 0;
				
				
				$update['diamond'] = $update1['diamond']+$diamond;
				
				if($userdata['gender']=='male'){
					$total_diamond = $this->common_model->GetColumnName('transaction',array('user_id'=>$user_id,'type'=>1,'status'=>1),array('SUM(diamond) as total'));
					$total_diamond = ($total_diamond['total']) ? $total_diamond['total'] : 0;
					$total_diamond = $total_diamond+$diamond;
					
					$get_level = $this->common_model->GetColumnName('level',array('gender'=>'male','daimond <= '=>$total_diamond),array('level'),false,'daimond','desc');
					
					$get_level = ($get_level['level']) ? $get_level['level'] : 0;
					
					$update['level'] = $get_level;
				}
				
				$run = $this->common_model->UpdateData('users',array('id'=>$user_id),$update);
				
				

				$insert['user_id'] = $user_id;
				$insert['diamond'] = $diamond;
				$insert['transaction_id'] = $transaction_id;
				$insert['payment_method'] = $payment_method;
				$insert['free_call'] = $free_call;
				$insert['amount'] = $amount;
				$insert['type'] = 1;
				$insert['status'] = 1;
				$insert['date'] = date('Y-m-d H:i:s');
				$insert['message'] = 'Win through Spin Wheel';
				$run = $this->common_model->InsertData('transaction',$insert);
				
				/*$notArr['user_id'] = $user_id;
							
				$notArr['message'] = "Congratulations you won ".$diamond." diamonds through Spin Wheel";
				
				$notArr['behalf_of'] = 0;
				$notArr['device_id'] = $userdata['device_id'];
				
				$notArr['other'] = array('screen'=>'diamond_added','diamonds'=>$diamond,'admin'=>0);
				$this->common_model->send_and_insert_notifi($notArr);*/

			/*				
				$output['message'] = 'Thankyou for rating.';
			} else {
				$output['message'] = 'Better luck next time';
			} */
			
			
		} else {
			$output['status'] = 0;
			$output['message'] = 'Check Parameter';
		}
		
		
		echo json_encode($output);
		
	}

	public function UpdateSpinWheel()
	{
		 //https://www.webwiders.in/WEB01/rapid/api/UpdateSpinWheel
		if(isset($_REQUEST['user_id']) && isset($_REQUEST['win_diamonds']) && isset($_REQUEST['id'])) {

				$user_id = $_REQUEST['user_id'];
				$win_diamonds = $_REQUEST['win_diamonds'];
				$id = $_REQUEST['id'];

				$update["win_diamonds"] = $win_diamonds;

				$run = $this->common_model->UpdateData('spin_wheel_records', array('id'=>$id), $update);

				if($run) {

				$output['is_win'] = 1;				
				$payment_method = 'Spin Wheel';
				$transaction_id = time();
				
				$diamond = $win_diamonds;
				$free_call = 0;
				$amount = 0;

				$userdata = $this->common_model->GetColumnName('users',array('id'=>$user_id),array('id','diamond','free_call','device_id','gender'));
				
				$update1['diamond'] = $userdata['diamond']+$diamond;
				
				if($userdata['gender']=='male'){
					$total_diamond = $this->common_model->GetColumnName('transaction',array('user_id'=>$user_id,'type'=>1,'status'=>1),array('SUM(diamond) as total'));
					$total_diamond = ($total_diamond['total']) ? $total_diamond['total'] : 0;
					$total_diamond = $total_diamond+$diamond;
					
					$get_level = $this->common_model->GetColumnName('level',array('gender'=>'male','daimond <= '=>$total_diamond),array('level'),false,'daimond','desc');
					
					$get_level = ($get_level['level']) ? $get_level['level'] : 0;
					
					$update1['level'] = $get_level;
					}
				
					$run1 = $this->common_model->UpdateData('users',array('id'=>$user_id),$update1);
				
				

				$insert['user_id'] = $user_id;
				$insert['diamond'] = $diamond;
				$insert['transaction_id'] = $transaction_id;
				$insert['payment_method'] = $payment_method;
				$insert['free_call'] = $free_call;
				$insert['amount'] = $amount;
				$insert['type'] = 1;
				$insert['status'] = 1;
				$insert['date'] = date('Y-m-d H:i:s');
				$insert['message'] = 'Win through Spin Wheel';
				$run2 = $this->common_model->InsertData('transaction',$insert);
				
				/*$notArr['user_id'] = $user_id;
							
				$notArr['message'] = "Congratulations you won ".$diamond." diamonds through Spin Wheel";
				
				$notArr['behalf_of'] = 0;
				$notArr['device_id'] = $userdata['device_id'];
				
				$notArr['other'] = array('screen'=>'diamond_added','diamonds'=>$diamond,'admin'=>0);
				$this->common_model->send_and_insert_notifi($notArr);*/
					$output['status'] = 1;	
					$output['message'] = 'Success';
				} else {
					$output['status'] = 0;
					$output['message'] = 'Something went wrong';
				}

			} else {

			$output['status'] = 0;
			$output['message'] = 'Check Parameter';

			}
				
		echo json_encode($output);
		
	}
	
	public function GetSpinList(){
		
		if(isset($_REQUEST['user_id'])){
			
			$user_id = $_REQUEST['user_id'];
			
			$today = date('Y-m-d');
			
			$data = $this->common_model->GetAllData('spin_wheel_option',null,'id','asc',null,null,array('*',"CONCAT('" .site_url() ."',icon) AS icon")); 
			
			$myWinData = $this->common_model->GetAllData('spin_wheel_records',array('user_id'=>$user_id,'is_win'=>1),'id','desc'); 
			
			$topWinDataToday = $this->common_model->GetAllData('spin_wheel_records',"is_win = 1 and DATE(create_date) = DATE('".$today."')",'id','desc');
			
			$tempArrKey = array();
			$tempArrVal = array();
			
			$topWinDataToday1 = array();
			if(!empty($topWinDataToday)){
				foreach($topWinDataToday as $key => $value){
					$topWinDataToday1[$key] = $value;
					$topWinDataToday1[$key]['user'] = $this->short_profile($value['user_id']);
				}
			}
			
			$adminEarningToday = $this->common_model->GetColumnName('spin_wheel_records',"DATE(create_date) = DATE('".$today."')",array('(sum(used_diamonds)-sum(win_diamonds)) as total'));
			$adminEarningToday = ($adminEarningToday['total']) ? $adminEarningToday['total'] : 0;
			
			
			$output['status'] = 1;
			$output['message'] = 'Success';
			$output['data'] = $data;
			$output['myWinData'] = $myWinData;
			$output['topWinDataToday'] = $topWinDataToday1;
			$output['adminEarningToday'] = $adminEarningToday;
			
			
			
		} else {
			$output['status'] = 0;
			$output['message'] = 'Check Parameter';
		}
		
		
		echo json_encode($output);
		
	}
	
	public function GetUserRatingList(){
		
		if(isset($_REQUEST['user_id']) && isset($_REQUEST['sessionId'])){
			
			$user_id = $_REQUEST['user_id'];
			$sessionId = $_REQUEST['sessionId'];
			
			$avg_rate = $this->common_model->GetColumnName('ratings',array('rate_to'=>$user_id),array('AVG(rating) as total'));
			
			$avg_rate2 = ($avg_rate && $avg_rate['total']) ? $avg_rate['total'] : 0;
			$avg_rate1 = bcdiv($avg_rate2, 1, 2);
			
			$ratings = $this->common_model->GetColumnName('ratings',array('rate_to'=>$user_id),Null,true,'id','desc','category');
			$result = array();
			$category='';
			$i=0;
			if(!empty($ratings)){

				foreach($ratings as $key => $value){

					$category .= $value['category'].',';
					
					/*foreach($parts as $key1 => $value1){
					$categoryName = $this->common_model->GetDataById('rating_category',$value1);
					$result1[$key1] = $categoryName;
					}
					$result[$i]['category']= $result1;*/

						$categoryCount = $this->common_model->GetColumnName('ratings',array('rate_to'=>$user_id,'category'=>$value['category']),array('count(rating) as total'));
						
						
						$result[$i]['count'] = $categoryCount['total'];
						$i++;					
					
				}
			}
			//print_r(array($category)); die;
			$myArray = explode(',', $category);
			//print_r($myArray); die;
			$arrCategory = array_unique($myArray);
			$result1 = array();
			$i=0;
			foreach ($arrCategory as $key1 => $value1){
				$categoryName = $this->common_model->GetSingleData('rating_category', array('id'=>$value1));
				if ($categoryName) {
					$result1[$i]['categoryName'] = $categoryName["name"];
					$data = $this->common_model->GetAllData('ratings',"FIND_IN_SET($value1,ratings.category)!=0",'id','desc');
					$result1[$i]['count'] = count($data);
				}
				$i++;	
			}

			$output['status'] = 1;
			$output['message'] = 'Success';
			$output['data'] = $result1;
			//$output['category'] = $result1;
			$output['avg_rate'] = $avg_rate1;
			
		} else {
			$output['status'] = 0;
			$output['message'] = 'Check Parameter';
		}
		
		
		echo json_encode($output);
		
	}
	
	
	public function MakeRating(){
		//https://www.webwiders.in/WEB01/rapid/api/MakeRating
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
		

		$data = $this->common_model->GetColumnName('admin',"id = 1",array('min_withdrawal','max_withdrawal','coin_rate','album_limit','watch_video_diamonds'));

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
			$total_diamond = $this->common_model->GetColumnName('transaction',array('user_id'=>$user_id,'type'=>1,'status'=>1),array('SUM(diamond) as total'));
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
			$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 1;
			
			$this->common_model->DeleteData('notification',array('user_id'=>$user_id,'type'=>$type));
			
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
			$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 1;
			
			$where['user_id'] = $user_id;
			
			$where = "user_id = $user_id and type = $type";
			
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
			$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 1;
			
			$notification = $this->common_model->GetDataByOrderLimit('notification',array('user_id'=>$user_id,'type'=>$type),'id','desc'); 
			//print_r($notification); die;
			//echo $this->db->last_query();
			$unread = $this->common_model->GetColumnName('notification',array('user_id'=>$user_id,'type'=>$type,'is_read'=>0),array('count(id) as total'));
			
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
			
			
			if(isset($_REQUEST['party_user_id']) && !empty($_REQUEST['party_user_id']) ){
			   
			   $partyid = $_REQUEST['party_user_id'];
			   $records = $this->common_model->GetAllData('party',array('status' => 1, 'user_id'=>$partyid));
			}
			else{
			   $records = $this->common_model->GetAllData('party',array('status' => 1));
			}
			
		
			

			//$output['qe'] = $this->db->last_query();
			
			$result = array();
			if(!empty($records)){
				foreach($records as $key => $value){
					
					$records[$key] = $value;

					if ($value["image"]) {
						$records[$key]['image'] = site_url().'assets/media/'.$value["image"];
					}
					
					
					$partyUser = $value['user_id'];
					
					$checkBlock = $this->common_model->GetColumnName('block_users',"(block_to = $partyUser and block_by = $user_id) or (block_to = $user_id and block_by = $partyUser)",array('block_by'));
					
					$block_status = 0;
					$block_by = 0;
					if($checkBlock){
						$block_status = 1;
						$block_by = $checkBlock['block_by'];
					}
					
					$records[$key]['block_status']=$block_status;
					$records[$key]['block_by']=$block_by;
					
					
					$senderp = $this->short_profile($value['user_id']);
					
					$records[$key]['created_by'] = $senderp;

					$records1 = $this->common_model->GetAllData('party_users',array('party_id' => $value['id'],'status !='=>2,'join_type'=>1));
					$result1 = array();
					if(!empty($records1)){

						foreach ($records1 as $key1 => $valueParty_users) {
							//$result1[$key1]["party_data"] = $valueParty_users;
							$result1[$key1] = $this->very_short_profile($valueParty_users['user_id']);
						}
					}	
					
					$records[$key]['party_users'] = $result1;
					
					$audience = $this->common_model->GetColumnName('party_users',array('party_id' => $value['id'],'status !='=>2),array('count(id) as total'));
					
					$records[$key]['audience_count'] = $audience['total'];
					
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
			
			$output['status'] = 1;
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
			//$invited_by = $_REQUEST['invited_by'];
			//$join_id = $_REQUEST['join_id'];

			/*$check_party = $this->common_model->GetSingleData('party_users',array('id'=>$join_id));
			
			if(!$check_party){
				$output['status'] = 0;
				$output['message'] = 'Invalid join ID';
				echo json_encode($output);
				exit();
			}*/

			
			/*$opentok = new OpenTok($this->opentokApi, $this->opentokApiSecret);

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

			$token = $opentok->generateToken($sessionId2);*/
			
			/*$insert1['invited_by'] = $invited_by;
			$insert1['sessionId'] = 'sessionId2';
			$insert1['token'] = '';*/
			/*if($user_id==$invited_by){
				$insert1['status'] =  0;
			} else {
				$insert1['status'] =  1;
			}*/
			
			$insert1['status'] =  1;
			$insert1['join_type'] = 1;
			


			$this->common_model->UpdateData('party_users',array('user_id'=>$user_id,'party_id'=>$party_id), $insert1);
				
			$output['status'] = 1;
			$output['message'] = 'Success';
		
			/*$party_data['sessionId'] = $sessionId;
			$party_data['token'] = $token;
			$party_data['party_id'] = $party_id;
			$party_data['status'] = $insert1['status'];*/
			
		} else {
			$output['status'] = 0;
			$output['message'] = "Check parameters";
		}
		
		echo json_encode($output);

	}
	
	public function PartyChat()
	{
		//https://www.webwiders.in/WEB01/rapid/api/PartyChat
		$this->form_validation->set_rules('party_id','party_id','trim|required');
		$this->form_validation->set_rules('sender_id','sender_id','trim|required');
		$this->form_validation->set_rules('message','message','trim|required');
		
		if($this->form_validation->run()){
			
			$insert['party_id'] = $this->input->post('party_id');
			$insert['message'] = $this->input->post('message');
			$insert['sender_id'] = $this->input->post('sender_id');
			$insert['create_date'] = date('Y-m-d H:i:s');
			
			$this->common_model->InsertData('party_chat',$insert);
			
			$output['status'] = 1;
			$output['message'] = "Message has been sent successfully.";
			
		} else {
			$output['status'] = 0;
			$output['message'] = "Check parameter.";
		}
		echo json_encode($output);
	}

	public function GetPartyChat()
	{
		//https://www.webwiders.in/WEB01/rapid/api/GetPartyChat
		if(isset($_REQUEST["party_id"]) && !empty($_REQUEST['party_id'])){
			
			$party_id = $_REQUEST["party_id"];

			$party_data = $this->common_model->GetColumnName('party',array('id'=>$party_id),array('status'));

			$output['party_status'] = $party_data['status'];

			$chat = $this->common_model->GetAllData('party_chat',array('party_id'=>$party_id),'id','asc');
			$chat_data = array();
			if(!empty($chat)){

				foreach($chat as $key => $row){
					$chat_data[$key] = $row;
					$chat_data[$key]['create_date'] = time_ago($row['create_date']);								
					$chat_data[$key]['user_data'] = $this->very_short_profile($row['sender_id']);
				}
				$output1['chat_data'] = $chat_data;				
			} else {
				$output1['chat_data'] = $chat_data;	
			}

			$countryArray = array();

			$audience = $this->common_model->GetAllData('party_users', array('party_id' => $party_id,'status !='=>2));
			$output1['active_audience_count'] = count($audience);
			$audienceData = array();
			if ($audience) {
				foreach ($audience as $key => $rows) {
					$audienceData[$key]['party_data'] = $rows;
					$audienceData[$key]["user"] = $this->very_short_profile($rows['user_id']);

					if ($audienceData[$key]["user"] && !array_key_exists($audienceData[$key]["user"]['country']['id'],$countryArray)){

						$countryArray[$audienceData[$key]["user"]['country']['id']] = $audienceData[$key]["user"]['country'];
						
					}

				}
			}
			$country = array();
			if(!empty($countryArray)){
				foreach($countryArray as $key => $value){
					$country[] = $value;
				}
			}
			$output1['country'] = $country;

			$output1['active_audience_list'] = $audienceData;

			$audienceIn = $this->common_model->GetAllData('party_users', array('party_id' => $party_id, 'status !='=>2, 'join_type'=>1));
			$output1['activeStreamerCount'] = count($audienceIn);
			$activeStreamer = array();
			if ($audienceIn) {
				foreach ($audienceIn as $key => $rows) {
					$activeStreamer[$key]['party_data'] = $rows;
					$activeStreamer[$key]["user"] = $this->very_short_profile($rows['user_id']);
				}
			}

			$output1['activeStreamer'] = $activeStreamer;


			$gift = $this->common_model->GetAllData('gift_send', array('party_id'=>$party_id), 'id');
			$giftArray=array();
			if ($gift) {
				foreach ($gift as $key => $giftV) {
					$giftArray[$key]=$giftV;
					$giftArray[$key]["send_by"] = $this->short_profile($giftV["send_by"]);
					//$giftArray[$key]["send_to"] = $this->short_profile($giftV["send_to"]);
					$gift_data = $this->common_model->GetSingleData('gift',array('id'=>$giftV["gift_id"]));
					if ($gift_data["sticker"]) {
						$gift_data["sticker"] = site_url().'assets/admin/gift/'.$gift_data["sticker"];
					} else {
						$gift_data["sticker"] = site_url().'assets/admin/gift/no-image-icon-23485.png';
					}
					if ($gift_data["animation"]) {
						$gift_data["animation"] = site_url().'assets/admin/gift/'.$gift_data["animation"];
					} else {
						$gift_data["animation"] = site_url().'assets/admin/gift/no-image-icon-23485.png';
					}
					$gift_data["category"] = $this->common_model->GetSingleData('gift_category',array('id'=>$gift_data["category"]));
					$giftArray[$key]["gift_data"] = $gift_data;

				}
				$output1['gifts'] = $giftArray;
			} else {
				$output1['gifts'] = $giftArray;
			}
			$output['data'] = $output1;
			$output['status'] = 1;
			$output['message'] = "Success!";
			
		} else {
			$output['status'] = 0;
			$output['message'] = "Check parameter.";
		}
		echo json_encode($output);
	}
	/*
	public function GetPartyAudience()
	{
		//https://www.webwiders.in/WEB01/rapid/api/GetPartyAudience?party_id=3
		 if(isset($_REQUEST["party_id"]) && !empty($_REQUEST['party_id'])){
		 		$party_id = $_REQUEST["party_id"];

		 		$audience = $this->common_model->GetAllData('party_users', array('party_id' => $party_id, 'status'=>1));
		 		$output['active_audience_count'] = count($audience);
		 		$audienceData = array();
		 		if ($audience) {
		 			 foreach ($audience as $key => $rows) {
		 			 	 $audienceData[$key]['party_join_users'] = $rows;
		 			 	 $audienceData[$key]["party_join_users_data"] = $this->short_profile($rows['user_id']);
		 			 }
		 			$output['data'] = $audienceData;
		 			$output['status'] = 1;
					$output['message'] = "Success!!"; 
		 		} else {
		 			$output['data'] = $audienceData;
		 			$output['status'] = 0;
					$output['message'] = "No data!!";
		 		}
		 	} else {
			$output['status'] = 0;
			$output['message'] = "Check parameter.";
		}
		echo json_encode($output);
	}

	public function GetPartyIncomingAudience()
	{
		//https://www.webwiders.in/WEB01/rapid/api/GetPartyIncomingAudience?party_id=3
		 if(isset($_REQUEST["party_id"]) && !empty($_REQUEST['party_id'])){
		 		$party_id = $_REQUEST["party_id"];

		 		$audience = $this->common_model->GetAllData('party_users', array('party_id' => $party_id, 'status'=>0));
		 		$output['active_audience_count'] = count($audience);
		 		$audienceData = array();
		 		if ($audience) {
		 			 foreach ($audience as $key => $rows) {
		 			 	 $audienceData[$key]['party_join_users'] = $rows;
		 			 	 $audienceData[$key]["party_join_users_data"] = $this->short_profile($rows['user_id']);
		 			 }
		 			$output['data'] = $audienceData;
		 			$output['status'] = 1;
					$output['message'] = "Success!!"; 
		 		} else {
		 			$output['data'] = $audienceData;
		 			$output['status'] = 0;
					$output['message'] = "No data!!";
		 		}
		 	} else {
			$output['status'] = 0;
			$output['message'] = "Check parameter.";
		}
		echo json_encode($output);
	} */

	public function removeUserFromParty()
	{
		//https://www.webwiders.in/WEB01/rapid/api/removeUserFromParty?party_id=3&user_id=1

		 if(isset($_REQUEST["party_id"]) && !empty($_REQUEST['party_id']) && isset($_REQUEST["user_id"]) && !empty($_REQUEST['user_id'])){

		 		$party_id = $_REQUEST["party_id"];
		 		$user_id = $_REQUEST["user_id"];

		 		$run = $this->common_model->DeleteData('party_users', array('party_id' => $party_id , 'user_id' => $user_id));
		 		if ($run) {
		 			 $output['status'] = 1;
					 $output['message'] = "Success";
		 		} else {
		 			 $output['status'] = 0;
					 $output['message'] = "Something went wrong.";
		 		}

		 	} else {
			$output['status'] = 0;
			$output['message'] = "Check parameter.";
		}
		echo json_encode($output);
	}

	public function userMuteUnmuteParty()
	{
		//https://www.webwiders.in/WEB01/rapid/api/userMuteUnmuteParty?party_id=3&user_id=35&is_mute=unmute

		 if(isset($_REQUEST["party_id"]) && !empty($_REQUEST['party_id']) && isset($_REQUEST["user_id"]) && !empty($_REQUEST['user_id']) && isset($_REQUEST["is_mute"]) && !empty($_REQUEST['is_mute'])){

		 		$party_id = $_REQUEST["party_id"];
		 		$user_id = $_REQUEST["user_id"];
		 		$is_mute = $_REQUEST["is_mute"];
		 		//echo $is_mute; die;
		 		if ($is_mute == "mute") {
		 			$update["is_muted"] = 1;
		 		} else {
		 			$update["is_muted"] = 0;
		 		}
		 		$run = $this->common_model->UpdateData('party_users', array('party_id' => $party_id , 'user_id' => $user_id), $update);
		 		if ($run) {
		 			 $output['status'] = 1;
					 $output['message'] = "Success";
		 		} else {
		 			 $output['status'] = 0;
					 $output['message'] = "Something went wrong.";
		 		}

		 	} else {
			$output['status'] = 0;
			$output['message'] = "Check parameter.";
		}
		echo json_encode($output);
	}

	public function userPausedParty()
	{
		//https://www.webwiders.in/WEB01/rapid/api/userPausedParty?party_id=3&user_id=35&is_pause=pause

		 if(isset($_REQUEST["party_id"]) && !empty($_REQUEST['party_id']) && isset($_REQUEST["user_id"]) && !empty($_REQUEST['user_id']) && isset($_REQUEST["is_pause"]) && !empty($_REQUEST['is_pause'])){

		 		$party_id = $_REQUEST["party_id"];
		 		$user_id = $_REQUEST["user_id"];
		 		$is_pause = $_REQUEST["is_pause"];
		 		//echo $is_mute; die;
		 		if ($is_pause == "pause") {
		 			$update["is_paused"] = 1;
		 		} else {
		 			$update["is_paused"] = 0;
		 		}
		 		$run = $this->common_model->UpdateData('party_users', array('party_id' => $party_id , 'user_id' => $user_id), $update);
		 		if ($run) {
		 			 $output['status'] = 1;
					 $output['message'] = "Success";
		 		} else {
		 			 $output['status'] = 0;
					 $output['message'] = "Something went wrong.";
		 		}

		 	} else {
			$output['status'] = 0;
			$output['message'] = "Check parameter.";
		}
		echo json_encode($output);
	}

	public function joinParty() {
		
		//https://www.webwiders.in/WEB01/rapid/api/joinParty?party_id=3&user_id=35&join_type=1
			if(isset($_REQUEST["party_id"]) && isset($_REQUEST["user_id"]) && isset($_REQUEST["join_type"])){

				

				$party_id = $_REQUEST["party_id"]; 
				$user_id = $_REQUEST["user_id"];
				$join_type = $_REQUEST["join_type"];

				$alreadyJoin = $this->common_model->GetSingleData('party_users', array('party_id'=>$party_id, 'user_id' => $user_id));
				
				if($alreadyJoin){
					$output["status"] = 2;
					$output["joinData"] = $alreadyJoin;	
					$output["message"] = 'Aready Joined';
				} else {
					$update["party_id"] = $party_id;
					$update["user_id"] = $user_id;
					$update['status'] = 0;
					$update['create_date'] = date('Y-m-d');
					$update['join_time'] = date('Y-m-d');
					$update['invited_by'] = 0;
					$update['is_muted'] = 0;
					$update['is_paused'] = 0;
					$update['join_type'] = $join_type;

					$this->common_model->InsertData('party_users', $update);	

					$output["status"] = 1;
					$output["joinData"] = $update;	
					$output["message"] = 'Success';
				}

			} else {
				$output['status'] = 0;
				$output['message'] = "Check parameter.";
			}
			echo json_encode($output);
	}

	public function create_party() {
		//https://www.webwiders.in/WEB01/rapid/api/create_party?user_id=20
		if(isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) {
			
			$user_id = $_REQUEST['user_id'];
			
			$check_party = $this->common_model->GetSingleData('party',array('user_id'=>$user_id,'status'=>1));
			
			if($check_party){
				$output['status'] = 2;
				$output['message'] = 'You have already started a party.';
				$output['party_data'] = $check_party;
			} else {
			
				// $opentok = new OpenTok($this->opentokApi, $this->opentokApiSecret);

				// $session = $opentok->createSession();

				// // A session that uses the OpenTok Media Router, which is required for archiving:
				// $session = $opentok->createSession(array( 'mediaMode' => MediaMode::ROUTED ));

				// // A session with a location hint:
				// $session = $opentok->createSession(array( 'location' => '12.34.56.78' ));

				// // An automatically archived session:
				// $sessionOptions = array(
				// 		'archiveMode' => ArchiveMode::ALWAYS,
				// 		'mediaMode' => MediaMode::ROUTED
				// );
				// $session = $opentok->createSession($sessionOptions);

				// $sessionId = $session->getSessionId();

				// $opentok = new OpenTok($this->opentokApi, $this->opentokApiSecret);

				// $sessionId2 = $sessionId;

				// $token = $opentok->generateToken($sessionId2);
				
				$insert1['user_id'] = $user_id;
			    $sessionId =  $_REQUEST['sessionId'];
				$insert1['sessionId'] = $_REQUEST['sessionId'];// $sessionId2;
				$insert1['token'] = $_REQUEST['token'];// $token;
				$insert1['create_date'] = date('Y-m-d H:i:s');
				$insert1['status'] =  1;

				$insert1['title'] = $this->input->post('title');
				$insert1['description'] = $this->input->post('description');

				if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])) {
					
					$file_array=explode(".",$_FILES['image']['name']);
					$file_ext=end($file_array);
					$rand=rand(222,999999);
					$file_name_thumb=$rand.time().".".$file_ext;
					
					
					if(move_uploaded_file($_FILES['image']['tmp_name'],'assets/media/'.$file_name_thumb)) {
						$insert1['image'] =$file_name_thumb;
					}
						
						
				}

				$run = $this->common_model->InsertData('party', $insert1);
				
				$output['status'] = 1;
				$output['message'] = 'Party has been created successfully.';
			

				$friends = $this->common_model->GetAllData('Friendlist',"status = 1 and (user_id = $user_id or request_user_id = $user_id)");
				if ($friends) {
						foreach ($friends as $key => $friend) {


						if($user_id == $friend['user_id']){
							$friend_id = $friend['request_user_id'];
						} else {
							$friend_id = $friend['user_id'];
						}

						$friendUsers = $this->common_model->GetColumnName('users',array('id'=>$friend_id),array('device_id'));
							$userdata = $this->common_model->GetColumnName('users',array('id'=>$user_id),array('nickname'));

						$notArr['user_id'] = $friend_id;
						$notArr['message'] = $userdata['nickname']." has created a party.";
						$notArr['behalf_of'] = $user_id;
						$notArr['device_id'] = $friendUsers['device_id'];
						
						$notArr['other'] = array('screen'=>'party_create','sender'=>$user_id,'receiver'=>$friend_id, 'sessionId' => $sessionId, 'party_id' =>$run);
						$this->common_model->send_and_insert_notifi($notArr);
					}
				}
				

				$partyId = $run;
				$records = $this->common_model->GetSingleData('party',array('id' => $partyId));
				
				
				$output["party"] = $records;
					
				$senderp = $this->short_profile($records['user_id']);
					
				$output['party']['created_by'] = $senderp;

				$output['party_users'] =array();;
			}

		}	else {

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
					
					if($value['msg_type']=='gift'){
						$giftData = $this->common_model->GetDataById('gift_send',$value['message']);
						$gift = $this->gift_data($giftData['gift_id']);
						
						$records[$key]['gift'] = $gift;
						$records[$key]['giftData'] = $giftData;
						
					}
					
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
								$insert5['status'] = 1;
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
			
			$this->common_model->UpdateData('user_chatmessage',array('receiver'=>$sender),array('is_delivered'=>1));
				
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
				
				$blocked = $this->common_model->GetSingleData('block_users',"((block_to = ".$friend_id." and block_by = ".$user_id.") or (block_to = ".$user_id." and block_by = ".$friend_id."))");
				if ($blocked) {
					
					$this->common_model->UpdateData('user_chatmessage',"((sender = ".$friend_id." and receiver = ".$user_id.") or (sender = ".$user_id." and receiver = ".$friend_id."))", array('read_msg'=>1));
					
				} else {
				
					$sender = $this->short_profile($friend_id);
					
					$sel_331 = "select * from user_chatmessage where (receiver='$user_id' and sender='$friend_id') or (sender='$user_id' and receiver='$friend_id') order by id DESC Limit 0,1";
				
					$run1 = $this->db->query($sel_331);
					$row1 = $run1->row_array();
					
				
					$res['sender'] = $sender;
				
					$res['last_message']=$row1['message'];
					$res['is_delivered']=$row1['is_delivered'];
					$res['human_time'] = time_ago($row1['cdate']);
				
				
					$sel2="select id from user_chatmessage where receiver='$user_id' and sender='$friend_id' and read_msg = 0";
				
					$run3 = $this->db->query($sel2);
					
					$res['unread_msg']= $run3->num_rows();
					
					$check_friend = $this->common_model->GetColumnName('Friendlist',"status = 1 and ((user_id = $friend_id and request_user_id = $user_id) or (user_id = $user_id and request_user_id = $friend_id))",array('id'));
					
					if($check_friend){
						$chat_list[]=$res;
					} else {
						$strangers_unread += $res['unread_msg'];
						$strangers[]=$res;
					}
				}

			}
			
			$this->common_model->UpdateData('user_chatmessage',array('receiver'=>$user_id),array('is_delivered'=>1));
				
			
			$output['chat_list']=$chat_list;
			$output['strangers']=$strangers;
			$output['strangers_unread']=$strangers_unread;
			$output['status'] = 1;
			$output['message'] = "Success";
			
		} else {
			$output['status'] = 0;
			$output['message'] = "We did not find any records.";
		}
		echo json_encode($output);
	}
	
	public function SendMessage(){
		//https://www.webwiders.in/WEB01/rapid/api/SendMessage? &sender=1&receiver=2&message=test&msg_type=1&card_id=1
		
		$this->form_validation->set_rules('sender','sender','trim|required');
		$this->form_validation->set_rules('receiver','receiver','trim|required|differs[sender]');
		$this->form_validation->set_rules('message','message','trim|required');
		$this->form_validation->set_rules('msg_type','msg_type','trim|required');
		$this->form_validation->set_rules('call_id','call_id','trim|required');
		
		if($this->form_validation->run()){
			
			$sender = $this->input->post('sender');
			$receiver = $this->input->post('receiver');
			$message = $this->input->post('message');
			$message_2 = $this->input->post('message');
			$msg_type = $this->input->post('msg_type');
			$call_id = $this->input->post('call_id');
			
			$sData = $this->common_model->GetColumnName('users',array('id'=>$sender),array('lng1','nickname','image','gender'));
			$rData = $this->common_model->GetColumnName('users',array('id'=>$receiver),array('lng1','device_id','last_login'));
			
			if($msg_type=='text' && !empty($rData['lng1'])){
				//$l1s = $this->common_model->GetColumnName('language',array('id'=>$sData['lng1']),array('code'));
				$l2s = $this->common_model->GetColumnName('language',array('id'=>$rData['lng1']),array('code'));
				
				if($l2s){
					$auto = $this->auto_transalate(null,$l2s['code'],$message);
					
					if(isset($auto->data->translations[0]->translatedText)){
						$message_2 = $auto->data->translations[0]->translatedText;
					}
				}
				
			}
			
			$insert['sender'] = $sender;
			$insert['receiver'] = $receiver;
			$insert['message_2'] = $message_2;
			$insert['message'] = $message;
			$insert['msg_type'] = $msg_type;
			$insert['call_id'] = $call_id;
			$insert['read_msg'] = 0;
			$insert['cdate'] = date('Y-m-d H:i:s');
			
			$run = $this->common_model->InsertData('user_chatmessage',$insert);
			
			if($run){
				
				$selectedTime = date('Y-m-d H:i:s');
				$endTime = date('Y-m-d H:i:s',strtotime($selectedTime."-15 second"));
			
				if(strtotime($endTime) >= strtotime($rData['last_login'])){
				
					$notArr['user_id'] = $receiver;
					$notArr['message'] = $sData['nickname']." has sent you a message.";
					
					$notArr['behalf_of'] = $sender;
					$notArr['device_id'] = $rData['device_id'];
					
					if($sData['image']){
						$profile = site_url().'upload/users/'.$sData['image'];
					} else {
						if($sData['gender']=='male'){
							$profile = site_url().'upload/male.png';
						} else {
							$profile = site_url().'upload/default_Image.png';
						}
					}
					
					$notArr['other'] = array('screen'=>'chat','sender'=>$sender,'receiver'=>$receiver,'sender_name'=>$sData['nickname'],'profile'=>$profile);
					$this->common_model->send_and_insert_notifi($notArr);
				
				}
	
				
				$output['status'] = 1;
				$output['message'] = "Message has been sent successfully.";
			} else {
				$output['status'] = 0;
				$output['message'] = "Something went wrong, try again later!";
			}
			
			
		} else {
			$output['status'] = 0;
			$output['message'] = "Check parameter!";
		}
		echo json_encode($output);
	}
	
	public function SendGreatingText(){
		//https://www.webwiders.in/WEB01/rapid/api/SendGreating
		
		$this->form_validation->set_rules('user_id','user_id','trim|required');
		$this->form_validation->set_rules('message','message','trim|required');
		//$this->form_validation->set_rules('msg_type','msg_type','trim|required');
		
		if($this->form_validation->run()){
			
			$user_id = $this->input->post('user_id');
			$message = $this->input->post('message');
			$message_2 = $this->input->post('message');
			
			$userdata = $this->common_model->GetColumnName('users',array('id'=>$user_id),array('gender','nickname','lng1','image'));
			
			$image = '';
			$thumb = '';
			
			if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])){
				$image_arr = $_FILES['image']['name'];
					
				$ext = explode('.',$_FILES['image']['name']);
				$ext = end($ext);
				$new_name = rand().time().'.'.$ext;
				$tmp_name = $_FILES["image"]["tmp_name"];
				$path = 'assets/media/'.$new_name;
				if(move_uploaded_file($tmp_name,$path)){
					$image = $new_name;
				}
			}
			
			if(isset($_FILES['thumb']['name']) && !empty($_FILES['thumb']['name'])){
				$image_arr = $_FILES['thumb']['name'];
					
				$ext = explode('.',$_FILES['thumb']['name']);
				$ext = end($ext);
				$new_name = rand().time().'.'.$ext;
				$tmp_name = $_FILES["thumb"]["tmp_name"];
				$path = 'assets/media/'.$new_name;
				if(move_uploaded_file($tmp_name,$path)){
					$thumb = $new_name;
				}
			}
			
		
			$users = $this->common_model->GetColumnName('users',array('gender != '=>$userdata['gender'], 'user_type' => 1),array('id','lng1','device_id','last_login'),true);
			
			$converted_lang = array();
			$receivers = array();
			$is_sent_to_1 = 0;
			
			$selectedTime = date('Y-m-d H:i:s');
			$endTime = date('Y-m-d H:i:s',strtotime($selectedTime."-15 second"));
			
			if(!empty($users)){
			
				foreach($users as $key => $value){
					
					if($is_sent_to_1==1){
						$receivers[] = $value['id'];
					} else {
						
						if(strtotime($endTime) >= strtotime($value['last_login'])){
							$receivers[] = $value['id'];
						} else {
							
							$is_sent_to_1 = 1;
							
							$insert = array();
							
							if($image){
								$insert['sender'] = $user_id;
								$insert['receiver'] = $value['id'];
								$insert['message'] = $image;
								$insert['msg_type'] = 'image';
								$insert['read_msg'] = 1;
								$insert['cdate'] = date('Y-m-d H:i:s');
								$this->common_model->InsertData('user_chatmessage',$insert);
							}
							
							$insert = array();
							
							if(!empty($value['lng1'])){
								//$l1s = $this->common_model->GetColumnName('language',array('id'=>$userdata['lng1']),array('code'));
								$l2s = $this->common_model->GetColumnName('language',array('id'=>$value['lng1']),array('code'));
								
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
							$insert['receiver'] = $value['id'];
							$insert['message'] = $message;
							$insert['message_2'] = $message_2;
							$insert['msg_type'] = 'text';
							$insert['read_msg'] = 0;
							$insert['cdate'] = date('Y-m-d H:i:s');
							$this->common_model->InsertData('user_chatmessage',$insert);
							
							$notArr['user_id'] = $value['id'];
							$notArr['message'] = $userdata['nickname']." has sent you a message.";
							
							$notArr['behalf_of'] = $user_id;
							$notArr['device_id'] = $value['device_id'];
							
							if($userdata['image']){
								$profile = site_url().'upload/users/'.$userdata['image'];
							} else {
								if($userdata['gender']=='male'){
									$profile = site_url().'upload/male.png';
								} else {
									$profile = site_url().'upload/default_Image.png';
								}
							}
							
							$notArr['other'] = array('screen'=>'chat','sender'=>$user_id,'receiver'=>$value['id'],'sender_name'=>$userdata['nickname'],'profile'=>$profile);
							$this->common_model->send_and_insert_notifi($notArr);
							
						}
					}
				}
			
			}
			
			if(!empty($receivers)){
				
				$inser3['sender'] = $user_id;
				$inser3['message'] = $message;
				$inser3['image'] = $image;
				$inser3['thumb'] = $thumb;
				$inser3['receivers'] = implode(',',$receivers);
				$inser3['status'] = 1;
				$inser3['create_date'] = date('Y-m-d H:i:s');
				
				$this->common_model->InsertData('greeting_loops',$inser3);
			}
		
			$output['status'] = 1;
			$output['message'] = "Message has been sent successfully.";
			
			
			
		} else {
			$output['status'] = 0;
			$output['message'] = "Check parameter!";
		}
		echo json_encode($output);
	}
	
	public function Send_media() {
		//https://www.webwiders.in/WEB01/rapid/api/Send_media?&file=image.png&file_type=image
		$data = $_REQUEST;
		if(!isset($_FILES['file']['name']) || empty($data['file_type'])){
			$output['status'] = "0";
			$output['message'] = "Please check parameter";
			
		} else {
			
			$maxsize    = 2097152;
			
			$accual_size = $_FILES['file']['size'];
			
			if($maxsize >= $accual_size){
			
				$file_array=explode(".",$_FILES['file']['name']);
				$file_ext=end($file_array);
				$rand=rand(222,999999);
				$file_name=$rand.time().".".$file_ext;
				
				//$tm = $this->compress_image($_FILES["file"]["tmp_name"],'assets/media/'.$file_name, 150);
				//$update['file_name'] = $file_name;
				
				if(move_uploaded_file($_FILES['file']['tmp_name'],'assets/media/'.$file_name)) {
					if($data['file_type']=='video') {
						if($_FILES['thumb']['name']) {
							$file_array=explode(".",$_FILES['thumb']['name']);
							$file_ext=end($file_array);
							$rand=rand(222,999999);
							$file_name_thumb=$rand.time().".".$file_ext;
							
							//$tm = $this->compress_image($_FILES["thumb"]["tmp_name"],'assets/media/'.$file_name_thumb, 150);
							//$update['thumb'] = $file_name_thumb;
							
							if(move_uploaded_file($_FILES['thumb']['tmp_name'],'assets/media/'.$file_name_thumb)) {
								$output['thumb'] =$file_name_thumb;
							}
						}
					}
					$output['status'] = "1";
					$output['file_name'] =$file_name;
					$output['file_type'] =$data['file_type'];
					$output['message'] = "Media Uploaded";
			
				} else {
					$output['status'] = "0";
					$output['message'] = "Something went wrong";
				}
			} else {
				$output['status'] = "0";
				$output['message'] = "File too large. File must be less than 2 megabytes.";
			}
		}
		echo json_encode($output);
	}
		
	public function AddAlbumMedia() {
		//https://www.webwiders.in/WEB01/rapid/api/Send_media?&file=image.png&file_type=image
		$data = $_REQUEST;
		
		if(!isset($_FILES['file']['name']) || empty($data['file_type'])){
			$output['status'] = "0";
			$output['message'] = "Please check parameter";
			
		} else {
			
			$limit = $this->common_model->GetColumnName('admin',array('id'=>1),array('album_limit'));
			
			$album_limit = $limit['album_limit'];
			
			$count = $this->common_model->GetColumnName('album',array('user_id'=>$data['user_id']),array('count(id) as total'));
			$count = $count['total'];
			
			if($count >= $album_limit){
				$output['status'] = 0;
				$output['message'] = "You can not upload more than $album_limit files.";
			} else {
			
				$maxsize    = 209715200000000;
				
				
				$accual_size = $_FILES['file']['size'];
				
				if($maxsize >= $accual_size){
				
					$file_array=explode(".",$_FILES['file']['name']);
					$file_ext=end($file_array);
					$rand=rand(222,999999);
					$file_name=$rand.time().".".$file_ext;
					
					if(move_uploaded_file($_FILES['file']['tmp_name'],'upload/album/'.$file_name)) {
						if($data['file_type']=='video') {
							if($_FILES['thumb']['name']) {
								$file_array=explode(".",$_FILES['thumb']['name']);
								$file_ext=end($file_array);
								$rand=rand(222,999999);
								$file_name_thumb=$rand.time().".".$file_ext;
								
								//$tm = $this->compress_image($_FILES["thumb"]["tmp_name"],'assets/media/'.$file_name_thumb, 150);
								//$update['thumb'] = $file_name_thumb;
								
								if(move_uploaded_file($_FILES['thumb']['tmp_name'],'upload/album/'.$file_name_thumb)) {
									$insert['thumb'] = 'upload/album/'.$file_name_thumb;
								}
							}
						}
						$insert['file'] ='upload/album/'.$file_name;
						$insert['type'] =$data['file_type'];
						$insert['user_id'] =$data['user_id'];
						$insert['create_date'] = date('Y-m-d');
						
						$this->common_model->InsertData('album',$insert);
						
						$output['status'] = "1";
						$output['message'] = "Media Uploaded";
				
					} else {
						$output['status'] = "0";
						$output['message'] = "Something went wrong";
					}
				} else {
					$output['status'] = "0";
					$output['message'] = "File too large. File must be less than 2 megabytes.";
				}
			}
		}
		echo json_encode($output);
	}
			
	public function DeleteAlbumMedia() {
		$data = $_REQUEST;
		if(isset($_REQUEST['user_id']) && isset($_REQUEST['id'])){
			
			
				$insert['id'] =$data['id'];
				$insert['user_id'] =$data['user_id'];
				
				$this->common_model->DeleteData('album',$insert);
				
				$output['status'] = "1";
				$output['message'] = "Album media has been deleted.";
			
		
		} else {
			$output['status'] = "0";
			$output['message'] = "Please check parameter";
		}
		
		
		echo json_encode($output);
	}
				
	public function GetAlbumMediaList() {
		$data = $_REQUEST;
		if(isset($_REQUEST['user_id'])){
			
				$insert['user_id'] =$data['user_id'];
				
				$limit = $this->common_model->GetColumnName('admin',array('id'=>1),array('album_limit'));
				$album_limit = $limit['album_limit'];
				
				$datad = $this->common_model->GetAllData('album',$insert,'id','desc',$album_limit,0,array('id','type','status',"CONCAT('" .site_url() ."',file) AS file","CONCAT('" .site_url() ."',thumb) AS thumb")); 
				
				$output['data'] = $datad;
				$output['status'] = "1";
				$output['message'] = "Album media has been deleted.";
			
		
		} else {
			$output['status'] = "0";
			$output['message'] = "Please check parameter";
		}
		
		
		echo json_encode($output);
	}
	
	public function openTok() {
		$opentok = new OpenTok($this->opentokApi, $this->opentokApiSecret);
	}

	public function createOpenTokSession() {

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


		if ($sessionId2) {
			$output['status'] = 1;
			$output['message'] = 'Success! Session Created Successfully.';
			$output['sessionId'] = array('sessionId'=>$sessionId);
			$output['token'] = array('Token'=>$token);
		} else {
			$output['status'] = 0;
			$output['message'] = 'no data found.';
			$output['data'] = array();
		}
		echo json_encode($output);

	}
    
	public function startStreaming() {

		if(isset($_FILES['image'])) {
			$config['upload_path'] = "upload/users/";
			$config['allowed_types'] = '*';
			$config['encrypt_name'] = true;
			$this->load->library("upload", $config);
			if ($this->upload->do_upload('image')) {
				$u_profile = $this->upload->data("file_name");
				 $insert1['profile'] = $u_profile;
			}
		}
		
		$user_id = $this->input->post('user_id');
		
		$checkStreaming = $this->common_model->GetSingleData('live_users', array('user_id' => $user_id,'status'=>1),'id','desc');
		
		if($checkStreaming){
			
			$created_at = date('Y-m-d H:i:s',strtotime($checkStreaming['created_at']));
			$end_time = date('Y-m-d H:i:s');
			
			$time_in_sec = strtotime($end_time) - strtotime($created_at);
			
			$update['status'] = 2;
			$update['end_time'] = $end_time;
			$update['time_in_sec'] = $time_in_sec;
			
			$this->common_model->UpdateData('live_users', array('id' => $checkStreaming['id']),$update);
			$this->common_model->UpdateData('streaming_users', array('streaming_id' => $checkStreaming['id']),array('status'=>0));
			
		}
		$u = $this->common_model->GetColumnName('users',array('id'=>$user_id),array('country','lng1','lng2'));
		
		$language = '';
		
		if($u && $u['lng1']){
			$language = $u['lng1'].',';
		}
		
		if($u && $u['lng2']){
			$language = $language.$u['lng2'];
		}

		$insert1['user_id'] = $user_id;
		$insert1['language'] = $language;
		$insert1['type'] = $this->input->post('type');
		$insert1['country'] = $u['country'];
		$insert1['sessionId'] = $this->input->post('sessionId');
		$insert1['token'] = $this->input->post('token');
		$insert1['created_at'] =  date('Y-m-d H:i:s');
		$insert1['status'] =  1;


		$run = $this->common_model->InsertData('live_users', $insert1);
			if ($run) {
				$output['status'] = 1;
				$output['stream_id'] = $run;
				$output['message'] = 'Success! User adedd as live.';
				$output['data'] = array();
			} else {
				$output['status'] = 0;
				$output['message'] = 'no data found.';
				$output['data'] = array();
			}
			echo json_encode($output);

	}
	
	public function stopStreaming() {
		if(isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])){
			$user_id = $_REQUEST['user_id'];
			$data = $this->common_model->GetSingleData('live_users', array('user_id' => $user_id,'status'=>1),'id','desc');
			
			$created_at = date('Y-m-d H:i:s',strtotime($data['created_at']));
			$end_time = date('Y-m-d H:i:s');
			
			$time_in_sec = strtotime($end_time) - strtotime($created_at);
			
			$update['status'] = 2;
			$update['end_time'] = $end_time;
			$update['time_in_sec'] = $time_in_sec;
			
			$run1 = $this->common_model->UpdateData('live_users', array('id' => $data['id']),$update);
			if ($run1) {
				if($data){
					$this->common_model->UpdateData('streaming_users', array('streaming_id' => $data['id']),array('status'=>0));
				}
				$output['status'] = 1;
				$output['message'] = 'Success! Streaming Stopped.';
				$output['data'] =array();
			} else {
				$output['status'] = 0;
				$output['message'] = 'no data found.';
				$output['data'] = array();
			}
		} else {
			$output['status'] = 0;
			$output['message'] = 'Check parameter.';
		}
		echo json_encode($output);
	}

	public function joinStreaming() {
		
		if(isset($_REQUEST['user_id']) && isset($_REQUEST['streaming_id'])) {

			$insert1['streaming_id'] = $_REQUEST['streaming_id'];
			$insert1['user_id'] = $_REQUEST['user_id'];
			$insert1['status'] = 1;
			
			$check = $this->common_model->GetColumnName('streaming_users', $insert1,array('id'));
			
			if($check){
				$run = 1;
			} else {
				$run = $this->common_model->InsertData('streaming_users', $insert1);
			}
			if ($run) {
				$output['status'] = 1;
				$output['message'] = 'Joined successfully.';
			
			} else {
				$output['status'] = 0;
				$output['message'] = 'no data found.';
		
			}
			
		}	else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
		echo json_encode($output);
	}

	public function leaveStreaming() {
		
		if(isset($_REQUEST['user_id']) && isset($_REQUEST['streaming_id'])) {

			$insert1['streaming_id'] = $_REQUEST['streaming_id'];
			$insert1['user_id'] = $_REQUEST['user_id'];
			
			$run = $this->common_model->UpdateData('streaming_users', $insert1,array('status'=>0));
			if ($run) {
				$output['status'] = 1;
				$output['message'] = 'Leaved successfully.';
			
			} else {
				$output['status'] = 0;
				$output['message'] = 'no data found.';
		
			}
		}	else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
		echo json_encode($output);
	}

	public function transaction_history() {
		
		if(isset($_REQUEST['user_id'])) {
			$user_id = $_REQUEST['user_id'];
			$transaction = $this->common_model->GetAllData('transaction',array('user_id'=>$user_id),'id','desc');
			$output['data'] = $transaction;
			$output['status'] = 1;
			$output['message'] = 'Success';
		}	else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
		echo json_encode($output);
	}

	public function add_diamond() {
		
		
		if(isset($_REQUEST['user_id']) && isset($_REQUEST['diamond_id']) && isset($_REQUEST['transaction_id']) && isset($_REQUEST['payment_method'])) {
			$user_id = $_REQUEST['user_id'];
			$payment_method = $_REQUEST['payment_method'];
			$transaction_id = $_REQUEST['transaction_id'];
			$diamond_id = $_REQUEST['diamond_id'];
			
			$data = $this->common_model->GetDataById('diamonds',$diamond_id);
			
		
			$diamond = $data['diamonds'];
			$free_call = $data['free_call'];
			$amount = $data['amount'];
			
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
			
			

			$insert['user_id'] = $user_id;
			$insert['diamond'] = $diamond;
			$insert['transaction_id'] = $transaction_id;
			$insert['payment_method'] = $payment_method;
			$insert['free_call'] = $free_call;
			$insert['amount'] = $amount;
			$insert['type'] = 1;
			$insert['status'] = 1;
			$insert['date'] = date('Y-m-d H:i:s');
			$insert['message'] = 'Add by Topup';
			$run = $this->common_model->InsertData('transaction',$insert);
			
			$notArr['user_id'] = $user_id;
						
			$notArr['message'] = $diamond." diamonds added in your balance";
			
			$notArr['behalf_of'] = 0;
			$notArr['device_id'] = $userdata['device_id'];
			
			$notArr['other'] = array('screen'=>'diamond_added','diamonds'=>$diamond,'admin'=>0);
			$this->common_model->send_and_insert_notifi($notArr);

			if ($run) {

				$output['data'] = $insert;
				$output['status'] = 1;
				$output['message'] = 'Diamond add successfully.';
			} else {
				$output['status'] = 0;
				$output['message'] = 'something went wrong.';
		
			}
		}	else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
		echo json_encode($output);
	}
	
	public function add_diamond_after_watch_video() {
		
		
		if(isset($_REQUEST['user_id'])) {
			$user_id = $_REQUEST['user_id'];
			
			$data = $this->common_model->GetColumnName('admin',array('id'=>1),array('watch_video_diamonds'));
			
		
			$diamond = $data['watch_video_diamonds'];
			$free_call = 0;
			$amount = 0;
			
			$userdata = $this->common_model->GetColumnName('users',array('id'=>$user_id),array('id','diamond','free_call','device_id','gender'));
			
			$update['diamond'] = $userdata['diamond']+$diamond;
			
			if($userdata['gender']=='male'){
				$total_diamond = $this->common_model->GetColumnName('transaction',array('user_id'=>$user_id,'type'=>1,'status'=>1),array('SUM(diamond) as total'));
				$total_diamond = ($total_diamond['total']) ? $total_diamond['total'] : 0;
				$total_diamond = $total_diamond+$diamond;
				
				$get_level = $this->common_model->GetColumnName('level',array('gender'=>'male','daimond <= '=>$total_diamond),array('level'),false,'daimond','desc');
				
				$get_level = ($get_level['level']) ? $get_level['level'] : 0;
				
				$update['level'] = $get_level;
			}
			
			$run = $this->common_model->UpdateData('users',array('id'=>$user_id),$update);
			
			$insert['user_id'] = $user_id;
			$insert['diamond'] = $diamond;
			$insert['transaction_id'] = time().rand();
			$insert['payment_method'] = 'Watch Video';
			$insert['free_call'] = $free_call;
			$insert['amount'] = $amount;
			$insert['type'] = 1;
			$insert['status'] = 1;
			$insert['date'] = date('Y-m-d H:i:s');
			$insert['message'] = 'Add by Watch Video';
			$run = $this->common_model->InsertData('transaction',$insert);
			
			$notArr['user_id'] = $user_id;
						
			$notArr['message'] = $diamond." diamonds added in your balance";
			
			$notArr['behalf_of'] = 0;
			$notArr['device_id'] = $userdata['device_id'];
			
			$notArr['other'] = array('screen'=>'diamond_added','diamonds'=>$diamond,'admin'=>0);
			$this->common_model->send_and_insert_notifi($notArr);

			if ($run) {
				$output['data'] = $insert;
				$output['status'] = 1;
				$output['message'] = 'Diamond add successfully.';
			} else {
				$output['status'] = 0;
				$output['message'] = 'something went wrong.';
		
			}
		}	else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
		echo json_encode($output);
	}
	
	public function send_trade_account_diamond() {
		
		//https://www.webwiders.in/WEB01/rapid/api/send_trade_account_diamond
		if(isset($_REQUEST['send_by']) && isset($_REQUEST['send_to']) && isset($_REQUEST['diamond']) && isset($_REQUEST['type'])) {

			$sendOtp = false;
			$ip = $this->UserIPAddress();
				$json = file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip); 
				//echo $json; die;
				$countryData = json_decode($json);	

			$whereOtp = " user_id='".$_REQUEST['send_by']."' and country='".$countryData->geoplugin_countryName."' and DATE(otp_validity) >= DATE('".date('Y-m-d H:i:s')."') and status=1";

			$checkOtp = $this->common_model->GetSingleData('tradeOtp',$whereOtp,'id','desc');


			$checkAbc = $this->common_model->GetColumnName('users',array('id'=>$_REQUEST['send_by']),array('id','email','phone_with_code','notification_type'));
			
			if($checkAbc && $checkOtp == false){
				
				$insert['user_id'] = $_REQUEST['send_by'];
				$insert['token']= $token = rand(100000,999999);
				$insert['status'] = 0;
				$insert['created_at'] = date('Y-m-d H:i:s');
				$insert['otp_validity']=date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s').' +1 Day'));
				$insert['country'] = $countryData->geoplugin_countryName;

				$runAbc = $this->common_model->InsertData('tradeOtp', $insert);
				
				if ($runAbc) {											
					
					if($checkAbc["notification_type"] == "sms" && $checkAbc["phone_with_code"] != ""){
						$message = $token." is your one time password.\r\n Please enter the OTP to proceed.\r\n Thank you,\r\n Team Rapid";
						$this->Send_sms->send($checkAbc["phone_with_code"],$message);
					} else {
					
						$email_template = $this->common_model->GetDataById('email_template',2);
						$email_body = $email_template['text'];
				
				$email_body = str_replace("[CODE]",$token,$email_body);
				$email_body1 = str_replace("assets/admin/EmailImg/",site_url()."assets/admin/EmailImg/",$email_body);
				$subject = "Verification Mail";
				
				$this->common_model->SendMailCustom($checkAbc['email'],$subject,$email_body1);
					}
					$sendOtp = true;	
					$output['message'] = "OTP sent successfully.";
				} else {
					$output['message'] = "Something went wrong, try again later.";
				}
					}

			if($sendOtp){
				$output['status'] = 2;				
				echo json_encode($output);
				exit();
			}

			$type = $_REQUEST['type'];
			$send_by = $_REQUEST['send_by'];
			$send_to = $_REQUEST['send_to'];
			$diamond = $_REQUEST['diamond'];
			
			$amount = 0;
			
			$by_data = $this->common_model->GetColumnName('users',array('id'=>$send_by),array('id','trade_balance','free_call','gender','nickname'));
			
			$to_data = $this->common_model->GetColumnName('users',array('id'=>$send_to),array('id','diamond','free_call','gender','trade_balance','device_id'));
			
				
				
			if($to_data){
				
				if($by_data['trade_balance'] >= $diamond){
					
					$insert3['send_by'] = $send_by;
					$insert3['send_to'] = $send_to;
					$insert3['type'] = $type;
					$insert3['diamonds'] = $diamond;
					$insert3['status'] = 0;
					$insert3['create_date'] = date('Y-m-d H:i:s');
					$insert3['update_date'] = date('Y-m-d H:i:s');
					$run = $this->common_model->InsertData('user_trade_transfer',$insert3);
					
					$notArr['user_id'] = $send_to;
					if($type=='balance'){
						$notArr['message'] = $by_data['nickname']." has sent you ".$diamond." diamond.";
					} else {
						$notArr['message'] = $by_data['nickname']." has sent you ".$diamond." diamond in your trade account.";	
					}
					
					$notArr['behalf_of'] = $send_by;
					$notArr['device_id'] = $to_data['device_id'];
					
					$notArr['other'] = array('screen'=>'trade_send','send_by'=>$send_by,'send_to'=>$send_to,'transaction_id'=>$run,'diamonds'=>$diamond,'sender_name'=>$by_data['nickname'],'type'=>$type);
					$this->common_model->send_and_insert_notifi($notArr);

					//$output['data'] = $insert;
					$output['status'] = 1;
					$output['message'] = 'Diamond transferred successfully.';
					
				} else {
					$output['status'] = 0;
					$output['message'] = "Insufficient trade blance in your account.";
				}
			
			}	else {

				$output['status'] = 0;
				$output['message'] = "Invalid User ID";
			}
		}	else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
		echo json_encode($output);
	}
	
	public function get_trade_history() {
		
		if(isset($_REQUEST['user_id'])) {
			$user_id = $_REQUEST['user_id'];
			
			$lists = $this->common_model->GetAllData('user_trade_transfer',"send_by = $user_id or send_to = $user_id",'id','desc');
			
			$result = array();
				
			if(!empty($lists)){
				
				foreach($lists as $key => $value){
					$result[$key] = $value;
					if($value['send_by'] == 0){
						$output1['id'] = 0;
						$output1['nickname'] = "Admin";
						$output1['image'] = site_url().'upload/default_Image.png';
						$result[$key]['send_by']=$output1;
					} else {
						$result[$key]['send_by'] = $this->short_profile($value['send_by']);
					}
					
					$result[$key]['send_to'] = $this->short_profile($value['send_to']);
				}
				
			}
			
			$output['status'] = 1;
			$output['result'] = $result;
			$output['message'] = "Success";
		}	else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
		echo json_encode($output);
	}
		
	public function ActivateTradeBalance() {
		
		if(isset($_REQUEST['user_id']) && isset($_REQUEST['transaction_id'])) {
			
			
			$transaction_id = $_REQUEST['transaction_id'];
			$user_id = $_REQUEST['user_id'];
			
			$data = $this->common_model->GetDataById('user_trade_transfer',$transaction_id);
			
			if($data && $data['send_by']==$user_id && $data['status']==0){
			
			
				
				$send_by = $data['send_by'];
				$send_to = $data['send_to'];
				$type = $data['type'];
				$diamond = $data['diamonds'];
				
				$amount = 0;
				
				$by_data = $this->common_model->GetColumnName('users',array('id'=>$send_by),array('id','trade_balance','free_call','gender','nickname'));
				
				$to_data = $this->common_model->GetColumnName('users',array('id'=>$send_to),array('id','diamond','free_call','gender','trade_balance','device_id'));
				
				if($by_data['trade_balance'] >= $diamond){
					
					if($type=='balance'){
					
						$update['diamond'] = $to_data['diamond']+$diamond;
						
						if($to_data['gender']=='male'){
							
							$insert['user_id'] = $send_to;
							$insert['diamond'] = $diamond;
							$insert['transaction_id'] = time();
							$insert['payment_method'] = 'Transfer';
							$insert['free_call'] = 0;
							$insert['amount'] = 0;
							$insert['type'] = 1;
							$insert['status'] = 1;
							$insert['date'] = date('Y-m-d H:i:s');
							$insert['message'] = 'Topup by trade balance';
							$this->common_model->InsertData('transaction',$insert);
							
							
							$total_diamond = $this->common_model->GetColumnName('transaction',array('user_id'=>$send_by,'type'=>1,'status'=>1),array('SUM(diamond) as total'));
							$total_diamond = ($total_diamond['total']) ? $total_diamond['total'] : 0;
							
							
							$get_level = $this->common_model->GetColumnName('level',array('gender'=>'male','daimond <= '=>$total_diamond),array('level'),false,'daimond','desc');
							
							$get_level = ($get_level['level']) ? $get_level['level'] : 0;
							
							$update['level'] = $get_level;
						}
					} else {
						$update['is_trade_account'] = 1;
						$update['trade_balance'] = $to_data['trade_balance']+$diamond;
					}
					
					
					$this->common_model->UpdateData('users',array('id'=>$send_to),$update);
					
					$update2['trade_balance'] = $by_data['trade_balance']-$diamond;
					$this->common_model->UpdateData('users',array('id'=>$send_by),$update2);
					
					

						
						$insert3['status'] = 1;
						$insert3['update_date'] = date('Y-m-d H:i:s');
						$this->common_model->UpdateData('user_trade_transfer',array('id'=>$transaction_id),$insert3);
						
						$notArr['user_id'] = $send_to;
						
						if($type=='balance'){
							$notArr['message'] = $by_data['nickname']." has activated ".$diamond." diamond.";
						} else {
							$notArr['message'] = $by_data['nickname']." has activated ".$diamond." diamond for your trade account.";	
						}
						
						$notArr['behalf_of'] = $send_by;
						$notArr['device_id'] = $to_data['device_id'];
						
						$notArr['other'] = array('screen'=>'diamond_activated','send_by'=>$send_by,'send_to'=>$send_to,'transaction_id'=>$transaction_id,'diamonds'=>$diamond,'sender_name'=>$by_data['nickname'],'type'=>$type);
						$this->common_model->send_and_insert_notifi($notArr);

						$output['status'] = 1;
						$output['message'] = 'Diamond transferred successfully.';
					
				} else {
					$output['status'] = 0;
					$output['message'] = "Insufficient trade blance in your account.";
				}
				
			
			
			}	else{

				$output['status'] = 0;
				$output['message'] = "Something went wrong try again later.";
			}
				
		}	else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
		echo json_encode($output);
	}
		
	public function RecallTradeBalance() {
		
		if(isset($_REQUEST['user_id']) && isset($_REQUEST['transaction_id'])) {
			
			
			$transaction_id = $_REQUEST['transaction_id'];
			$user_id = $_REQUEST['user_id'];
			
			$data = $this->common_model->GetDataById('user_trade_transfer',$transaction_id);
			
			if($data && $data['send_by']==$user_id && $data['status']==0){
			
			
				
				$send_by = $data['send_by'];
				$send_to = $data['send_to'];
				$type = $data['type'];
				$diamond = $data['diamonds'];
				
				$insert3['status'] = 2;
				$insert3['update_date'] = date('Y-m-d H:i:s');
				$this->common_model->UpdateData('user_trade_transfer',array('id'=>$transaction_id),$insert3);

				$output['status'] = 1;
				$output['message'] = 'Recall successfully.';
			
			}	else{

				$output['status'] = 0;
				$output['message'] = "Something went wrong try again later.";
			}
				
		}	else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
		echo json_encode($output);
	}
	
	public function GetUserByUniqueId() {
		
		
		if(isset($_REQUEST['uniqueID'])) {
			$uniqueID = $_REQUEST['uniqueID'];
			
			
			$user = $this->common_model->GetColumnName('users',array('uniqueID'=>$uniqueID),array('id','nickname'));
			
			if($user){
				$output['status'] = 1;
				$output['user'] = $user;
				$output['message'] = "Success";
			} else {
				$output['status'] = 2;
				$output['message'] = "User not found.";
			}
		
		}	else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
		echo json_encode($output);
	}
	
	private function create_girl_level($id,$amount,$source=null){
		$total_coin = $this->common_model->GetColumnName('coin_transaction',array('user_id'=>$id),array('SUM(coin) as total'));
		$total_coin = ($total_coin['total']) ? $total_coin['total'] : 0;
		$total_coin = $total_coin;
		
		$get_level = $this->common_model->GetColumnName('level',array('gender'=>'female','daimond <= '=>$total_coin),array('level'),false,'daimond','desc');
		
		$get_level = ($get_level['level']) ? $get_level['level'] : 0;
		$update['level'] = $get_level;
		$this->common_model->UpdateData('users',array('id'=>$id),$update);
		
		//$this->parent_earning($id,$amount,$source);
		
	}
	
	public function parent_earning($id,$amount,$source){
		$userdata = $this->common_model->GetColumnName('users',array('id'=>$id),array('invited_by'));
		
		if($userdata && $userdata['invited_by']){
			
			$total_amount = $this->common_model->GetColumnName('coin_transaction',array('user_id'=>$id),array('SUM(amount) as total'));
			
			$total_amount = ($total_amount['total']) ? $total_amount['total'] : 0;
		
			$get_level = $this->common_model->GetColumnName('agent_comission',array('amount <= '=>$total_amount),array('percent'),false,'amount','desc');
			
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
						
						//$update['wallet'] = $userdata2['wallet']+$NewAmout;
						$this->common_model->UpdateData('users',array('id'=>$userdata['invited_by']),$update);
						
						if($userdata2['invited_by']){
							$this->parent_earning($id,$NewAmout,$source);
						}
					}
				
				}
				
			}
		
		}
		
	}
	
	public function send_call_request() {

		if(isset($_REQUEST['call_by']) && isset($_REQUEST['call_to'])) {
			
			$call_to = $_REQUEST['call_to'];
			$call_by = $_REQUEST['call_by'];
			
			$by_data = $this->common_model->GetColumnName('users',array('id'=>$call_by),array('diamond','free_call'));
		
			$to_data = $this->common_model->GetColumnName('users',array('id'=>$call_to),array('chat_price'));
			
			
			if($by_data['diamond'] >= $to_data['chat_price']){
				
				$check_call = $this->common_model->GetSingleData('call_records',"(status = 0 or status = 1) and (call_to = $call_to or call_by = $call_to)",'id','desc');
				
				if($check_call){
					$output['status'] = 2;
					$output['message'] = 'Busy on another call.';
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
					
					$insert1['call_by'] = $call_by;
					$insert1['call_to'] = $call_to;
					$insert1['sessionId'] = $sessionId2;
					$insert1['token'] = $token;
					$insert1['create_date'] = date('Y-m-d H:i:s');
					$insert1['status'] =  0;
					
					if($by_data['free_call'] > 0){
						$insert1['is_free'] =  1;
					} else {
						$insert1['is_free'] =  0;
					}


					$run = $this->common_model->InsertData('call_records', $insert1);
					

					if ($run) {
						$output['status'] = 1;
						$output['message'] = 'Success! Call request send Successfully.';
						$output['sessionId'] = array('sessionId'=>$sessionId);
						$output['token'] = array('Token'=>$token);
						$output['call_by'] = $insert1['call_by'];
						$output['call_to'] = $insert1['call_to'];
						$output['is_free'] = $insert1['is_free'];
						$output['create_date'] = $insert1['create_date'];
						$output['call_id'] = $run;
						$output['call_request_status'] = $insert1['status'];
					} else {
						$output['status'] = 0;
						$output['message'] = 'Something went wrong.';
						$output['data'] = array();
					}
				}
			} else {
				$output['status'] = 3;
				$output['message'] = "You do not have sufficient diamond to make this call.";
			}
			
		}	else{

			$output['status'] = 0;
			$output['message'] = "Check parameters";
		}
		
		echo json_encode($output);

	}
		
	public function BecomeGuardian() {

		if(isset($_REQUEST['primary_user']) && isset($_REQUEST['secondry_user'])) {
			
			$primary_user = $_REQUEST['primary_user'];
			$secondry_user = $_REQUEST['secondry_user'];
			
			$primary = $this->common_model->GetColumnName('users',array('id'=>$primary_user),array('diamond','id','guardian_price','device_id','nickname','gender'));
		
			$secondry = $this->common_model->GetColumnName('users',array('id'=>$secondry_user),array('diamond','id','guardian_price','device_id','nickname'));
			
			
			if($secondry['diamond'] >= $primary['guardian_price']){
				
				$check_pre = $this->common_model->GetSingleData('guardian',"status = 1 and primary_user = $primary_user",'id','desc');
				
				$setting = $this->common_model->GetColumnName('admin',null,array('guardian_percent','guardian_duration'));
				$guardian_percent = $setting['guardian_percent'];
				$guardian_duration = $setting['guardian_duration'];
				
				
		
				$insert1['primary_user'] = $primary_user;
				$insert1['secondry_user'] = $secondry_user;
				$insert1['diamond'] = $primary['guardian_price'];
				$insert1['start_date'] = date('Y-m-d');
				$insert1['end_date'] = date('Y-m-d',strtotime($insert1['start_date'].' +'.$guardian_duration.' days'));
				$insert1['status'] =  1;
				
				$run = $this->common_model->InsertData('guardian', $insert1);
				
				
				
				

				if ($run) {
					$output['status'] = 1;
					$output['message'] = 'Success!';
					
					$adminComm = round(($primary['guardian_price']*$guardian_percent)/100);
					
					
					
					$insert = array();
					$insert['user_id'] = $primary_user;
					$insert['diamond'] = $primary['guardian_price']-$adminComm;
					$insert['transaction_id'] = time();
					$insert['payment_method'] = 'Guardian';
					$insert['free_call'] = 0;
					$insert['amount'] = 0;
					$insert['type'] = 1;
					$insert['status'] = 1;
					$insert['date'] = date('Y-m-d H:i:s');
					$insert['message'] = 'Earn by guardian';
					$this->common_model->InsertData('transaction',$insert);
					
					if($primary['gender']=='male'){
					
						$total_diamond = $this->common_model->GetColumnName('transaction',array('user_id'=>$primary_user,'type'=>1,'status'=>1),array('SUM(diamond) as total'));
						$total_diamond = ($total_diamond['total']) ? $total_diamond['total'] : 0;
					
						$get_level = $this->common_model->GetColumnName('level',array('gender'=>'male','daimond <= '=>$total_diamond),array('level'),false,'daimond','desc');
						
						$get_level = ($get_level['level']) ? $get_level['level'] : 0;
					
						$update['level'] = $get_level;
						$update['diamond'] = $primary['diamond']+$primary['guardian_price'];
					
					}
					
					$guardian_price = ($primary['guardian_price']*$guardian_percent)/100;
					$guardian_price = $primary['guardian_price']+$guardian_price;
					$update['guardian_price'] = round($guardian_price,0);
					$this->common_model->UpdateData('users',array('id'=>$primary_user),$update);
					
					$update2['diamond'] = $secondry['diamond']-$primary['guardian_price'];
					$this->common_model->UpdateData('users',array('id'=>$secondry_user),$update2);
					
					$this->common_model->UpdateData('guardian',"primary_user = $primary_user and id != $run",array('status'=>0));
					
					$insert5['user_id'] = $secondry_user;
					$insert5['diamond'] = $primary['guardian_price'];
					$insert5['transaction_id'] = time();
					$insert5['payment_method'] = 'Guardian';
					$insert5['free_call'] = 0;
					$insert5['amount'] = 0;
					$insert5['type'] = 2;
					$insert5['status'] = 1;
					$insert5['date'] = date('Y-m-d H:i:s');
					$insert5['message'] = 'Spend for guardian';
					$this->common_model->InsertData('transaction',$insert5);
					
				} else {
					$output['status'] = 0;
					$output['message'] = 'Something went wrong.';
					$output['data'] = array();
				}
				
			} else {
				$output['status'] = 3;
				$output['message'] = "You do not have sufficient diamond.";
			}
			
		}	else{

			$output['status'] = 0;
			$output['message'] = "Check parameters";
		}
		
		echo json_encode($output);

	}
	
	public function Accept_call_request() {
		
		if(isset($_REQUEST['user_id']) && isset($_REQUEST['call_id'])) {

			
			$call_by = $_REQUEST['user_id'];
			$call_id = $_REQUEST['call_id'];
			
			
				
			$call_data = $this->common_model->GetDataById('call_records',$call_id);
			$call_data['call_id'] = $call_data['id'];
			$output['call_data'] = $call_data;
			
			$to_data = $this->common_model->GetColumnName('users',array('id'=>$call_data['call_to']),array('chat_price','diamond','id'));
			
			$update['status'] = 1;
			$update['attempt_at'] = date('Y-m-d H:i:s');;
			$update['spent_diamonds'] = $to_data['chat_price'];
			$this->common_model->UpdateData('call_records',array('id'=>$call_id),$update);
			
			
			
			
			$output['status'] = 1;
			$output['message'] = 'Request accept successfully.';
				
		
		}	else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
		echo json_encode($output);
	}
	
	public function reject_call_request() {
		
		if(isset($_REQUEST['user_id']) && isset($_REQUEST['call_id'])) {

			$call_by = $_REQUEST['user_id'];
			$call_id = $_REQUEST['call_id'];
			$update['status'] = 2;
			$run = $this->common_model->UpdateData('call_records',array('id'=>$call_id),$update);
			
			if ($run) {
				$output['status'] = 1;
				$output['message'] = 'Call has been rejected successfully.';
			} else {
				$output['status'] = 0;
				$output['message'] = 'no data found.';
			}
		}	else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
		echo json_encode($output);
	}
	
	public function disconnect_call() {
		
		if(isset($_REQUEST['user_id']) && isset($_REQUEST['call_id'])) {

			$call_by = $_REQUEST['user_id'];
			$call_id = $_REQUEST['call_id'];
			$update['status'] = 3;
			$update['disconnect_at'] = date('Y-m-d H:i:s');
			$run = $this->common_model->UpdateData('call_records',array('id'=>$call_id),$update);
			
			if ($run) {
				$output['call_request_status'] = $update['status'];
				$output['userdata'] = $this->customer_profile($call_to);
				$output['status'] = 2;
				$output['message'] = 'Request reject successfully.';
				
			} else {
				$output['status'] = 0;
				$output['message'] = 'no data found.';
		
			}
		}	else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
		echo json_encode($output);
	}
	
	public function block_user() {
		//
		if(isset($_REQUEST['block_to']) && isset($_REQUEST['block_by'])) {
			$insert['block_by'] = $_REQUEST['block_by'];
			$insert['block_to'] = $_REQUEST['block_to'];
			
			$setting = $this->common_model->InsertData('block_users',$insert);
			
			$update["read_msg"] = 1;
			
			$this->common_model->UpdateData('user_chatmessage',"((sender = ".$_REQUEST['block_by']." and receiver = ".$_REQUEST['block_to'].") or (sender = ".$_REQUEST['block_to']." and receiver = ".$_REQUEST['block_by']."))", $update);

			$output['status'] = 1;
			$output['message'] = 'User has been blocked successfully.';
		}	else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
		echo json_encode($output);
	}
	
	public function unblock_user() {
		
		if(isset($_REQUEST['block_to']) && isset($_REQUEST['block_by'])) {
			$insert['block_by'] = $_REQUEST['block_by'];
			$insert['block_to'] = $_REQUEST['block_to'];
			
			$setting = $this->common_model->DeleteData('block_users',$insert);
			
			$output['status'] = 1;
			$output['message'] = 'User has been unblocked successfully.';
		}	else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
		echo json_encode($output);
	}
	
	public function blocked_users_list() {
		
		if(isset($_REQUEST['user_id'])) {
			$user_id = $_REQUEST['user_id'];
			
			$result = array();
			
			$data = $this->common_model->GetColumnName('block_users',array('block_by'=>$user_id),array('block_to'),true,'id','desc');
			
			if(!empty($data)){
				foreach($data as $key => $value){
					$user = $this->customer_profile($value['block_to']);
					if($user){
						$result[] = $this->customer_profile($value['block_to']);
					}
				}
			}
			
			$output['data'] = $result;
			$output['status'] = 1;
			$output['message'] = 'User has been unblocked successfully.';
		}	else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
		echo json_encode($output);
	}

  public function getLiveUsers() {
  	//https://www.webwiders.in/WEB01/rapid/api/getLiveUsers
		if(isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) {
			$user_id = $_REQUEST['user_id'];

			$update['last_login'] = date('Y-m-d H:i:s');
			$this->common_model->UpdateData('users',array('id'=>$user_id),$update);

			$userdata = $this->common_model->GetColumnName('users',array('id'=>$user_id),array('id','lat','lng','gender'));
			if($userdata && (!isset($_REQUEST['lat']) || empty($_REQUEST['lat']))){
				$_REQUEST['lat'] = $userdata['lat'];
				$_REQUEST['lng'] = $userdata['lng'];
			}
			
			// $sql = "select users.id as id";
			
			// if(isset($_REQUEST['lat']) && !empty($_REQUEST['lat']) && isset($_REQUEST['lng']) && !empty($_REQUEST['lng'])){
				
			// 	$lat = $_REQUEST['lat'];
			// 	$lng = $_REQUEST['lng'];
				
			// 	$sql .= ", 111.111 * 	DEGREES(ACOS(LEAST(1.0, COS(RADIANS(".$lat.")) * COS(RADIANS(users.lat)) * COS(RADIANS(".$lng." - users.lng)) + SIN(RADIANS(".$lat.")) * SIN(RADIANS(users.lat))))) AS distance_in_km";
			// }
			
			// $sql .= " ,(select id from live_users where live_users.user_id = users.id and live_users.status = 1 order by id desc limit 1) as stream_id from users where is_verified = 1 and status=1 and (select count(id) from live_users where id != $user_id and live_users.user_id = users.id and live_users.status = 1) > 0 and users.gender != '".$userdata['gender']."' ";

			$selectedTime = date('Y-m-d H:i:s');
			$endTime = strtotime("-2 minutes", strtotime($selectedTime));
			$endTime = date('Y-m-d H:i:s', $endTime);

			$sql = "select id FROM users where gender != '".$userdata['gender']."' and user_type = 1 and last_login >= '".$endTime."' and id != '".$user_id."' and (select count(id) from block_users where (block_users.block_by=$user_id and block_users.block_to=users.id) or (block_users.block_to=$user_id and block_users.block_by=users.id)) = 0";

			if(isset($_REQUEST['country']) && !empty($_REQUEST['country'])){
				$sql .= " and country = '".$_REQUEST['country']."'";
			}
			
			if(isset($_REQUEST['lang']) && !empty($_REQUEST['lang'])){
				$sql .= " and (lng1 = '".$_REQUEST['lang']."' or lng2 = '".$_REQUEST['lang']."')";
			}	
			
			if (isset($_REQUEST['keywords']) && !empty($_REQUEST['keywords'])) {
				$keywords = ucfirst($_REQUEST['keywords']);
				$sql .= " and (nickname like '%".$keywords."%' or (SELECT count(id) from country WHERE country.id = users.country and country.nicename like '%".$keywords."%') > 0)";
			}
			
			if(isset($_REQUEST['orderby']) && !empty($_REQUEST['orderby'])){
				if($_REQUEST['orderby']=='discover'){
					
					$sql .= " order by id desc";
					
				} else if($_REQUEST['orderby']=='nearby' && isset($_REQUEST['lat']) && !empty($_REQUEST['lat']) && isset($_REQUEST['lng']) && !empty($_REQUEST['lng'])){
					
					$sql .= " order by distance_in_km asc";
					
				} else if($_REQUEST['orderby']=='popular'){
					
					$sql .= " order by (select SUM(coin) from coin_transaction where coin_transaction.user_id = users.id) desc";
					
				} else {
					
					$sql .= " order by id desc";
					
				}
			} else {
				$sql .= " order by id desc";
			}

			
			if(isset($_REQUEST['start']) && isset($_REQUEST['limit'])){
			
				$start = $_REQUEST['start'];
				$limit = $_REQUEST['limit'];
				
				$sql .= " limit $start, $limit"; 
			}
			$run = $this->db->query($sql);
			$result = array();
			if($run->num_rows() > 0){
				foreach($run->result_array() as $key => $row){
					// $live_users = $this->common_model->GetDataById('live_users',$row['stream_id']);
					$live_users = $this->common_model->GetSingleData('live_users', array('user_id' => $row['id'],'status' => 1));
					
					$check_call = $this->common_model->GetSingleData('call_records',"(status = 0 or status = 1) and (call_to = $user_id or call_by = $user_id)",'id','desc');
				

					if($live_users){
						$result[$key]['stream_id'] = $live_users['id'];
						$result[$key]['sessionId'] = $live_users['sessionId'];
						$result[$key]['type'] = $live_users['type'];
						$result[$key]['token'] = $live_users['token'];
						
						if($live_users['profile']){
							$result[$key]['profile'] = site_url() . 'upload/users/' . $live_users['profile'];
						} else {
							$result[$key]['profile'] = '';
						}
						$result[$key]['login_status'] = 'Live';
					} else if($check_call) {
						$result[$key]['login_status'] = 'Busy';
					} else {
						$result[$key]['login_status'] = 'Active';
					}
					$block_to = $row['id'];

					$blockUsers = $this->common_model->GetSingleData('block_users','((block_to = '.$block_to.') or (block_by = '.$block_to.'))');
					if ($blockUsers) {

						$result[$key]['block_status']=1;
						$result[$key]['block_by'] = $blockUsers["block_by"];
					} else {
						$result[$key]['block_status']=0;
					}
					$result[$key]['userdata'] = $this->customer_profile($row['id']);
					// if(isset($_REQUEST['lat']) && !empty($_REQUEST['lat']) && isset($_REQUEST['lng']) && !empty($_REQUEST['lng'])){
					//   // $result[$key]['distance_in_km'] = $row['distance_in_km'];
					//   $result[$key]['distance_in_km'] ='';
					// }

					// $result[$key]['start_date'] = $selectedTime;
					// $result[$key]['sql'] = $sql;
					// $result[$key]['userdata'] = $userdata;

				}
			}

		$output['message'] = "Success";
		$output['data'] = $result;

		}	else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
 	 	echo json_encode($output);
	}
	

	public function GetCountry() {
		$where=array();
		
		$result = array();
		
		//$orderBy = "(SELECT count(id) from users WHERE users.country = country.id)";
		$orderBy = "nicename";
		
		$records = $this->common_model->GetAllData('country',$where,$orderBy,'asc','','',"*, (SELECT count(id) from users WHERE users.user_type = 1 and users.country = country.id) as usercount");

		foreach($records as $key => $record){
			$result[$key] = $record;
			
			
			//$usercount = $this->common_model->GetAllData('users',array('country'=>$record['id']),'','asc');
			
			
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
			
			//$result[$key]['usercount'] = count($usercount);
		}
		
		
		$output['status'] = 1;
		$output['message'] = "Success!";
		$output['data'] = $result;
		
		echo json_encode($output);
	}

	public function interval_api(){
		if(isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])){
			
			$user_id = $_REQUEST['user_id'];
			
			$this->common_model->UpdateData('user_chatmessage',array('receiver'=>$user_id),array('is_delivered'=>1));
			
			$update['last_login'] = date('Y-m-d H:i:s');
			$this->common_model->UpdateData('users',array('id'=>$_REQUEST['user_id']),$update);
			
			$check_call = $this->common_model->GetSingleData('call_records',"(status = 0 or status = 1) and (call_to = $user_id or call_by = $user_id)",'id','desc');
			
			$call_status = 0;
			$call_data = Null;
			
			if($check_call){
				$call_status = 1;
				$call_data = $check_call;
				$call_data['call_id'] = $check_call['id'];
				$call_data['call_by'] = $this->short_profile($check_call['call_by']);
				$call_data['callto'] = $this->short_profile($check_call['call_to']);
			} 
			
			$data = $this->common_model->GetColumnName('users',array('id'=>$user_id),array('diamond','status','login_token'));
			$output['diamond'] = $data['diamond'];
			$output['user_status'] = $data['status'];
			$output['login_token'] = $data['login_token'];
			
			$chat_count = $this->common_model->GetColumnName('user_chatmessage',array('receiver'=>$user_id,'read_msg'=>0),array('count(id) as total'));
			$output['chat_count'] = ($chat_count) ? $chat_count['total'] : 0;
			
			$output['call_status'] = $call_status;
			$output['call_data'] = $call_data;
			

			
			$unreadNoti = $this->common_model->GetColumnName('notification',array('user_id'=>$user_id,'type'=>1,'is_read'=>0),array('count(id) as total'));
			$unreadNoti = ($unreadNoti) ? $unreadNoti['total'] : 0;
			
			$output['unreadNotification'] =$unreadNoti;
			
			$unreadNoti2 = $this->common_model->GetColumnName('notification',array('user_id'=>$user_id,'type'=>2,'is_read'=>0),array('count(id) as total'));
			$unreadNoti2 = ($unreadNoti2) ? $unreadNoti2['total'] : 0;
			
			$output['unreadPostNotification'] =$unreadNoti2;
			
			
			$output['status'] = 1;
			$output['message'] = 'Success!';
			
		} else {
			$output['status'] = 0;
			$output['message'] = 'Check parameter.';
		}
		echo json_encode($output);
	}

	public function sendStreamingMessage(){
		$this->form_validation->set_rules('streaming_id','streaming_id','trim|required');
		$this->form_validation->set_rules('sender_id','sender_id','trim|required');
		$this->form_validation->set_rules('message','message','trim|required');
		
		if($this->form_validation->run()){
			
			$insert['streaming_id'] = $this->input->post('streaming_id');
			$insert['message'] = $this->input->post('message');
			$insert['sender_id'] = $this->input->post('sender_id');
			$insert['create_date'] = date('Y-m-d H:i:s');
			
			$this->common_model->InsertData('streaming_chat',$insert);
			
			$output['status'] = 1;
			$output['message'] = "Message has been sent successfully.";
			
		} else {
			$output['status'] = 0;
			$output['message'] = "Check parameter.";
		}
		echo json_encode($output);
	}
		
	public function getStreamingChat(){
		if(isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id']) && isset($_REQUEST['streaming_id']) && !empty($_REQUEST['streaming_id'])) {
			
			$streaming_id = $_REQUEST['streaming_id'];
			$user_id = $_REQUEST['user_id'];
			
			$chat_data = array();
			$join_users = 0;
			
			$currentTime = date('Y-m-d H:i:s');
			$stream = $this->common_model->GetSingleData('live_users',array('status'=>1,'id'=>$streaming_id));
			
			$stream_status = 0;
			$diffInSec = 0;
			
			if($stream){
				$stream_status = 1;
				$diffInSec = strtotime($currentTime)-strtotime($stream['created_at']);
			
			}
			
			$output['diffInTime'] = gmdate('H:i:s',$diffInSec);
			$output['diffInSec'] = $diffInSec;
			
			
			
			$this->common_model->UpdateData('users',array('id'=>$user_id),array('last_login'=>date('Y-m-d H:i:s')));
			
			
			$usersQuery = "select id, user_id from streaming_users where streaming_id = $streaming_id and status = 1 order by (select level from users where users.id=streaming_users.user_id) desc, (select SUM(diamond) from transaction where transaction.user_id=streaming_users.user_id and transaction.type=1 and transaction.status=1) desc";
			$usersRun = $this->db->query($usersQuery);
			
			$usersJoin = array();
			
			if($usersRun->num_rows() > 0){
				foreach($usersRun->result_array() as $key => $user){
					$u = $this->short_profile($user['user_id']);
			
					if($u){
						if($u['last_login_in_min'] >= 1){
							$insert1['streaming_id'] = $streaming_id;
							$insert1['user_id'] = $user['user_id'];
							//$usersJoin[] = $u;
							$this->common_model->UpdateData('streaming_users', $insert1,array('status'=>0));
						} else {
							$usersJoin[] = $u;
						}
					}
					
				}
			}
			
			$chat = $this->common_model->GetAllData('streaming_chat',array('streaming_id'=>$streaming_id),'id','asc');
			
			if(!empty($chat)){
				foreach($chat as $key => $row){
					$chat_data[$key] = $row;
					$chat_data[$key]['create_date'] = time_ago($row['create_date']);
					
					//$u = $this->common_model->GetColumnName('users',array('id'=>$row['sender_id']),array('id','nickname'));
					/*if($u['image']){
						$join_users[$key]['image'] = site_url().'upload/users/'.$u['image'];
					} else {
						$join_users[$key]['image'] = site_url().'upload/default_Image.png';
					}*/
					
					$chat_data[$key]['user_data'] = $this->short_profile($row['sender_id']);
				}
			}
			
			
			$output['joinUserList'] = $usersJoin;
			$output['join_users'] = count($usersJoin);
			$output['stream_status'] = $stream_status;
			$output['chat_data'] = $chat_data;
			$output['status'] = 1;
			$output['message'] = "Success!";
			
		} else {
			$output['status'] = 0;
			$output['message'] = "Check parameter.";
		}
		echo json_encode($output);
	}
	
	public function language() {
		$records = $this->common_model->GetColumnName('language',null,array('id','name'),true,'id','desc');
		$output['status'] = 1;
		$output['message'] = "Success!";
		$output['data'] = $records;
		
		echo json_encode($output);
	}
	
	public function country_list() {
	
		//$records = $this->common_model->GetColumnName('users',array('country >'=>0),array('id','country'),null,null,null,'country');
		$sql = 'SELECT country FROM users where user_type = 1 GROUP BY country HAVING COUNT(*) > 0';

		$query = $this->db->query($sql);
		$result1=$query->result_array();
        
		$coutry_ids='';
		for($i=0;$i<count($result1);$i++)
		{
			$coutry_ids = $coutry_ids.','.$result1[$i]['country'];
		}
        
		$c_ids = ltrim($coutry_ids,","); 
        
		$where =' id IN ('.$c_ids.')';
		
		$result = array();
		
		/*$orderBy = "nicename";
		
		$records = $this->common_model->GetAllData('country',$where,$orderBy,'asc');*/
		
		$selectedTime = date('Y-m-d H:i:s');
		$endTime = strtotime("-10 minutes", strtotime($selectedTime));
		$endTime = date('Y-m-d H:i:s', $endTime);

		$where .= " and (select count(id) from users where users.country = country.id and users.user_type = 1 and last_login >= '".$endTime."') > 0";

		$orderBy = "(select count(id) from users where users.country = country.id and users.user_type = 1 and last_login >= '".$endTime."')";
		
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
		
		$where1 = "(select count(id) from users where users.user_type = 1 and (users.lng1 = language.id or users.lng2 = language.id) and last_login >= '".$endTime."') > 0";
		 
		//$orderBy2 = "(SELECT count(id) from live_users WHERE FIND_IN_SET(language.id,live_users.language))";
		
		$orderBy2 = "(select count(id) from users where users.user_type = 1 and (users.lng1 = language.id or users.lng2 = language.id) and last_login >= '".$endTime."')";
		
		$sql = "select * from language where $where1 order by $orderBy2 desc";
		
		$run = $this->db->query($sql);


		$output['status'] = 1;
		$output['message'] = "Success!";
		$output['data1'] = $result;
		$output['data2'] = $run->result_array();
		
		echo json_encode($output);
	}
	
	public function login() {
		$this->form_validation->set_rules('phone','Email','required|trim');
		$this->form_validation->set_rules('password','Last Name','required|trim');

		if($this->form_validation->run()== FALSE) {
			$output['status'] = 0;
			$output['message']='Check parameter.';
            
		} else {
			
			$phone=$this->input->post('phone');
			$password=$this->input->post('password');
			
			$ip = $this->UserIPAddress();
			
			$checkBlocklistIP = $this->common_model->GetColumnName('blacklisted_ip',array('ip'=>$ip),array('id'));
			
			if($checkBlocklistIP){
				$output['status'] = 0;
				$output['message']='You IP address has been blacklisted. Please contact to administrator.';
				echo json_encode($output);
				exit();
			}
		
			
			$where = "(phone = '".$phone."' or phone_with_code = '".$phone."' or email = '".$phone."') and password = '".$password."' and user_type = 1";

			$result_id=$this->common->GetColumnName('users',$where,array('id','status'));
			if($result_id) {
				if($result_id['status']==1){
					$user_id = $result_id['id'];
					$user_profile=$this->customer_profile($user_id);
					
					$login_token = md5(time());
					
					
					$this->db->query("update users set login_token = '".$login_token."', ip = '".$ip."' where id = $user_id");
					
					$output['login_token'] = $login_token;
					$output['data'] = $user_profile;
					$output['status'] = 1;
					$output['message']='Logged in successfully.';
				} else {
					$output['status'] = 0;
					$output['message']='Your account has been blocked.';
				}
                
			}   else {
				$output['status'] = 0;
				$output['message']='Invalid login details.';
                 
			}
		}
		echo json_encode($output);
	}

  public function change_password(){
		//https://www.webwiders.in/WEB01/rapid/api/change_password&user_id=1&password=123456&current_password=123123
		
		$this->form_validation->set_rules('current_password','current password','trim|required');
		$this->form_validation->set_rules('password','New password','trim|required');
		$this->form_validation->set_rules('user_id','id','trim|required');
		
		if($this->form_validation->run()){
			
			$id = $this->input->post('user_id');
			$cpassword=  $this->input->post('current_password');
			$npassword=  $this->input->post('password');

			$user = $this->common_model->GetColumnName('users',array('id'=>$id),array('password'));
			
			if($user){
				if($user['password']==$cpassword){

					$insert['password']=$npassword;
					
					$run = $this->common_model->UpdateData('users',array('id'=>$id),$insert);
				
					if($run){
						
						$output['status'] = 1;
						$output['message'] = 'Your password has been changed successfully.';
					} else {
						$output['status'] = 0;
						$output['message'] = 'We did not find any changes.';
					}

				} else {
					$output['status'] = 0;
					$output['message'] = 'Your current password is incorrect.';
				}	
				
			} else {
				$output['status'] = 0;
				$output['message'] = 'We did not find any records.';
			}
				
 		} else {
			$output['status'] = 0;
			$output['message'] = 'Check parameter.';
		}
		echo json_encode($output);
	}
	
  public function SetPassowrd(){
		//https://www.webwiders.in/WEB01/rapid/api/SetPassowrd&user_id=1&password=123456&current_password=123123
		
		$this->form_validation->set_rules('password','New password','trim|required');
		$this->form_validation->set_rules('user_id','id','trim|required');
		
		if($this->form_validation->run()){
			
			$id = $this->input->post('user_id');
			$npassword=  $this->input->post('password');

			$user = $this->common_model->GetColumnName('users',array('id'=>$id),array('password'));
			
			if($user){
				
				$insert['password']=$npassword;
				
				$run = $this->common_model->UpdateData('users',array('id'=>$id),$insert);
			
				if($run){
					
					$output['status'] = 1;
					$output['message'] = 'Your password has been changed successfully.';
				} else {
					$output['status'] = 0;
					$output['message'] = 'We did not find any changes.';
				}
				
			} else {
				$output['status'] = 0;
				$output['message'] = 'We did not find any records.';
			}
				
 		} else {
			$output['status'] = 0;
			$output['message'] = 'Check parameter.';
		}
		echo json_encode($output);
	}
	
	public function resend_otp() {
		if(isset($_REQUEST['phone_with_code']) && isset($_REQUEST['LoginType'])){
			//https://www.webwiders.in/WEB01/rapid/api/resend_otp?
			$phone_with_code=$_REQUEST['phone_with_code'];
			$LoginType=$_REQUEST['LoginType'];
			
			$check = $this->common_model->GetColumnName('users',"phone_with_code = '".$phone_with_code."' or email = '".$phone_with_code."'",array('id','email'));
			
			if($check){
				$insert['token']=rand(100000,999999);
				$insert['otp_validity']=date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s').' + 15 minutes'));
				$run = $this->common_model->UpdateData('users',array('id'=>$check["id"]),$insert);
				if ($run) {
					
					$token = $insert['token'];
								
					
					if($LoginType=='phone'){
						$message = $token." is your one time password.\r\n Please enter the OTP to proceed.\r\n Thank you,\r\n Team Rapid";
						$this->Send_sms->send($phone_with_code,$message);
					} else {
						/*$message = $token." is your one time password.\r\n Please enter the OTP to proceed.";
						$this->common_model->SendMail($phone_with_code,'Verification Mail',$message);*/
						$email_template = $this->common_model->GetDataById('email_template',2);
						$email_body = $email_template['text'];
				
				$CODE = $token;
				$email_body = str_replace("[CODE]",$CODE,$email_body);
				$email_body1 = str_replace("assets/admin/EmailImg/",site_url()."assets/admin/EmailImg/",$email_body);
				$subject = "Verification Mail";
				
				$this->common_model->SendMailCustom($check['email'],$subject,$email_body1);
					}
								
					$output['status'] = 1;
					$output['message'] = "OTP sent successfully.";
				}else{
					$output['status'] = 0;
					$output['message'] = "Something went wrong, try again later.";
				}
			} else {
				$output['status'] = 0;
				$output['message'] = "We did not find any records with this phone number.";
			}
					
		} else {
			$output['status'] = 0;
			$output['message'] = 'Check parameter.';
		}
		echo json_encode($output);
	}
	
	public function forget_password() {
		//https://www.webwiders.in/WEB01/rapid/api/forget_password
		if(isset($_REQUEST['phone_with_code']) && isset($_REQUEST['LoginType'])){
			$phone_with_code=$_REQUEST['phone_with_code'];
			$LoginType=$_REQUEST['LoginType'];
			
			$check = $this->common_model->GetColumnName('users',"phone_with_code = '".$phone_with_code."' or email = '".$phone_with_code."'",array('id','password','phone_with_code','email'));
			
			if($check){
					
					if($check['password']){
						$token = $check['password'];
					} else {
						$token = rand(10000000,99999999);
						$this->common_model->UpdateData('users',array('id'=>$check['id']),array('password'=>$token));
					}
				
					
				
					if($LoginType=='phone'){
						$message = $token." is your one temporary password.\r\n Please login using this password.\r\n Thank you,\r\n Team Rapid";
						$this->Send_sms->send($check['phone_with_code'],$message);
						$output['message'] = "Password has been sent to your mobile number.";
					} else {
						$output['message'] = "Password has been sent to your email address.";
						/*$message = $token." is your one temporary password. \r\n Please login using this password.";
						$this->common_model->SendMail($check['email'],'Verification Mail',$message);*/

						$email_template = $this->common_model->GetDataById('email_template',1);
						$email_body = $email_template['text'];
				
						$CODE = $token;
						$email_body = str_replace("[CODE]",$CODE,$email_body);
						$email_body1 = str_replace("assets/admin/EmailImg/",site_url()."assets/admin/EmailImg/",$email_body);
						$subject = "Forget Password";
				
						$this->common_model->SendMailCustom($check['email'],$subject,$email_body1);
					}
					
								
					$output['status'] = 1;
					
			
			} else {
				$output['status'] = 0;
				$output['message'] = "We did not find any records.";
			}
					
		} else {
			$output['status'] = 0;
			$output['message'] = 'Check parameter.';
		}
		echo json_encode($output);
	}
	
	public function verifyPhone() {
		
		$this->form_validation->set_rules('token','token','required');
		$this->form_validation->set_rules('user_id','user_id','required');
		
		if($this->form_validation->run()){
			$user_id=$this->input->post('user_id');
			$token=$this->input->post('token');
			$userdata = $this->common_model->GetColumnName('users',array('id' =>$user_id),array('token','otp_validity'));
			if($userdata && $userdata['token']==$token) {
				
				$time = date('Y-m-d H:i:s');
				$otp_validity = $userdata['otp_validity'];
				
				if(strtotime($otp_validity) >= strtotime($time)){
				
				$insert['is_verified']=1;
				$insert['token']='';
				$insert['otp_validity']=Null;
				$this->common_model->UpdateData('users',array('id'=>$user_id),$insert);
				$user_profile=$this->customer_profile($user_id);
				$output['data'] = $user_profile;
				$output['status'] = 1;
				$output['message'] = 'Your phone number has been verified successfully.';
				
				} else {
					$output['status'] = 0;
					$output['message'] = 'OTP has been expired.';
				}
			}else{
				$output['status'] = 0;
				$output['message'] = 'Invalid OTP.';
			}
			} else {
			$output['status'] = 0;
			$output['message'] = 'Check parameter.';
		}
		echo json_encode($output);
	}
	
	public function verifyOtp() { //Not in use
		
		$this->form_validation->set_rules('token','token','required');
		$this->form_validation->set_rules('user_id','user_id','required');
		
		if($this->form_validation->run()){
			$user_id=$this->input->post('user_id');
			$token=$this->input->post('token');
			$userdata = $this->common_model->GetColumnName('users',array('id' =>$user_id),array('token'));
			if($userdata && $userdata['token']==$token) {
				
				$user_profile=$this->customer_profile($user_id);
				$output['data'] = $user_profile;
				$output['status'] = 1;
				$output['message'] = 'Your phone number has been verified successfully.';
			}else{
				$output['status'] = 0;
				$output['message'] = 'Invalid OTP.';
			}
			} else {
			$output['status'] = 0;
			$output['message'] = 'Check parameter.';
		}
		echo json_encode($output);
	}
	
	private function GetUniqueUserID(){
		$uniqueID = rand(100000,999999);
		$check = $this->common_model->GetColumnName('users',array('uniqueID'=>$uniqueID),array('id'));
		
		if($check){
			$this->GetUniqueUserID();
		} else {
			return $uniqueID;
		}
	}
	
	public function signup() {
		//https://www.webwiders.in/WEB01/rapid/api/signup
		$this->form_validation->set_rules('phone','phone','required|trim');
		$this->form_validation->set_rules('country','country','required|trim');
		$this->form_validation->set_rules('LoginType','LoginType','required|trim');

		if($this->form_validation->run()== FALSE) {
			$output['status'] = 0;
			$output['message']='Check parameter.';
     
		} else {
			
			$login_token = md5(time());
			
			
			$ip = $this->UserIPAddress();
			
			$checkBlocklistIP = $this->common_model->GetColumnName('blacklisted_ip',array('ip'=>$ip),array('id'));
			
			if($checkBlocklistIP){
				$output['status'] = 0;
				$output['message']='You IP address has been blacklisted. Please contact to administrator.';
				echo json_encode($output);
				exit();
			}
			
			$phone = $this->input->post('phone');
			$LoginType = $this->input->post('LoginType');
			
			$check = $this->common_model->GetColumnName('users',"phone = '".$phone."' or email = '".$phone."'",array('id','is_verified','phone_with_code','password','email','nickname','status'));
			
			if($check) {
				
				if($check['status']==1){
					if($check['is_verified']==0 || $check['password']==''){
						
						$device_id = $this->input->post('device_id');
						if ($device_id) {
							$insert['device_id'] = $device_id;
						}
						
						$token = $insert['token']=rand(100000,999999);
						$insert['otp_validity']=date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s').' + 15 minutes'));
						$insert['login_token']=$login_token;
						$insert['ip']=$ip;
						$this->common_model->UpdateData('users',array('id'=>$check['id']),$insert);
						
						if($LoginType=='phone'){
							$message = $token." is your one time password.\r\n Please enter the OTP to proceed.\r\n Thank you,\r\n Team Rapid";
							$this->Send_sms->send($check['phone_with_code'],$message);
						} else {
							
							/*$message = '<p>'.$token." is your one time password.</p>. <p>Please enter the OTP to proceed.</p>";
							$message = $token." is your one time password.\r\n Please enter the OTP to proceed.";
							$this->common_model->SendMail($check['email'],'Verification Mail',$message);*/

							$email_template = $this->common_model->GetDataById('email_template',3);
							$email_body = $email_template['text'];
				
							$CODE = $token;
							$email_body = str_replace("[CODE]",$CODE,$email_body);
							$email_body1 = str_replace("assets/admin/EmailImg/",site_url()."assets/admin/EmailImg/",$email_body);
							$subject = "Verification Mail";
				
							$this->common_model->SendMailCustom($check['email'],$subject,$email_body1);

						}
					
						
						$output['status'] = 2;//
					} else {
						$output['status'] = 3;//
					}
					
					$user_profile=$this->customer_profile($check['id']);
					$output['login_token'] = $login_token;
					$output['data'] = $user_profile;
					$output['message']="Login success.";
				} else {
					$output['status'] = 0;
					$output['message']='Your account has been blocked.';
				}
				
      } else {
				
				$checkBlocklistIP = $this->common_model->GetColumnName('whitelisted_ip',array('ip'=>$ip),array('id'));
				
				if(!$checkBlocklistIP){
					
					$admin = $this->common_model->GetColumnName('admin',array('id'=>1),array('account_per_ip'));
					$account_per_ip = $admin['account_per_ip'];
					
					$ipAccountCount = $this->common_model->GetColumnName('users',array('ip'=>$ip),array('count(id) as total'));
					$ipAccountCount = $ipAccountCount['total'];
					
					if($ipAccountCount >= $account_per_ip){
						$output['status'] = 0;
						$output['message']="You can not create more than $account_per_ip account with same IP Address.";
						echo json_encode($output);
						exit();
					}
					
				}
				
				
				
				if($LoginType=='phone'){
					$insertData['phone']=$phone;
				} else {
					
					$insertData['email']=$phone;
				}
				
				$insertData['country']=$this->input->post('country');
				
				if($LoginType=='phone'){
				
					$country = $this->common_model->GetColumnName('country',array('id'=>$insertData['country']),array('phonecode')); 
					
					$phone_with_code = $country['phonecode'].$insertData['phone'];
					
					$insertData['phone_with_code']=$phone_with_code;
				
				}
				$insertData['created_at']=date('Y-m-d H:i:s');
				$insertData['updated_at']=date('Y-m-d H:i:s');
				$insertData['is_verified']=0;
				$insertData['login_token']=$login_token;
				$insertData['ip']=$ip;
				
				$token= $insertData['token']= rand(100000,999999);
				$insertData['otp_validity']=date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s').' + 15 minutes'));
				
				$LoginType = $this->input->post('LoginType');
				if($LoginType){
					$insertData['LoginType']=$LoginType;
				}
				
				$FacebookID = $this->input->post('FacebookID');
				if($FacebookID){
					$insertData['FacebookID']=$FacebookID;
				}
				
				$GmailID = $this->input->post('GmailID');
				if($GmailID){
					$insertData['GmailID']=$GmailID;
				}

				$lat = $this->input->post('lat');
				if($lat){
					$insertData['lat']=$lat;
				}

				$lng = $this->input->post('lng');
				if($lng){
					$insertData['lng']=$lng;
				}
				
				$city = $this->input->post('city');
				if ($city) {
					$insertData['city'] = $city;
				}
				
				$device_id = $this->input->post('device_id');
				if ($device_id) {
					$insertData['device_id'] = $device_id;
				}
				
				$uniqueID = $this->GetUniqueUserID();

				$insertData['uniqueID'] = $uniqueID;
				$insertData['user_type'] = 1;
				
				$result_id=$this->common->InsertData('users',$insertData);
				if($result_id) {
					
					if($LoginType=='phone'){
						$message = $token." is your one time password.\r\n Please enter the OTP to proceed.\r\n Thank you,\r\n Team Rapid";
						$this->Send_sms->send($phone_with_code,$message);
					} else {
						
						/*$message = $token." is your one time password.\r\n Please enter the OTP to proceed.";
						$this->common_model->SendMail($insertData['email'],'Verification Mail',$message);*/

						$email_template = $this->common_model->GetDataById('email_template',3);
						$email_body = $email_template['text'];
				
						$CODE = $token;
						$email_body = str_replace("[CODE]",$CODE,$email_body);
						$email_body1 = str_replace("assets/admin/EmailImg/",site_url()."assets/admin/EmailImg/",$email_body);
						$subject = "Verification Mail";
						
						$this->common_model->SendMailCustom($insertData['email'],$subject,$email_body1);
				

					}
					
					
					
					$user_profile=$this->customer_profile($result_id);
					$output['login_token'] = $login_token;
					$output['data'] = $user_profile;
					$output['status'] = 1;//new
					$output['message']="To finish your sign up, please enter OTP that sent to your phone number.";
				} else {
					$output['status'] = 0;
					$output['message']="Something went wrong, please try again later.";
				}
			}
		}

		echo json_encode($output);
		
	}

	public function edit_profile(){
		//https://www.webwiders.in/WEB01/rapid/api/edit_profile

 	 	$this->form_validation->set_rules('user_id','User ID','trim|required');
  	
		if($this->form_validation->run()){
		       
			$insert['updated_at'] = date('Y-m-d H:i:s');
			if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])){
				$image_arr = $_FILES['image']['name'];
					
				$ext = explode('.',$_FILES['image']['name']);
				$ext = end($ext);
				$new_name = rand().time().'.'.$ext;
				$tmp_name = $_FILES["image"]["tmp_name"];
				$path = 'upload/users/'.$new_name;
				if(move_uploaded_file($tmp_name,$path)){
					$insert['image']=$new_name;
					//$insert2['post_id']=$run;
					//$this->common_model->InsertData('post_image',$insert2);
				}
			}
			
			$user_id = $this->input->post('user_id');
			
			
			
			if(isset($_REQUEST['gender'])){
				$insert['gender']=$this->input->post('gender');
			}

			if(isset($_REQUEST['notification_type']) && !empty($_REQUEST['notification_type'])) {
				$insert['notification_type']=$this->input->post('notification_type');
			}

			if(isset($_REQUEST['nickname'])){
				
				$insert['nickname']=$this->input->post('nickname');
				
				$checkNickName = $this->common_model->GetColumnName('users',array('id != '=>$user_id,'nickname'=>$insert['nickname']),array('nickname'));
				
				if($checkNickName){
					$output['status'] = 0;
					//$output['error_message'] = validation_errors();
					$output['field'] = 'nickname';
					$output['message'] = 'Nick name aready exist.';
					echo json_encode($output);
					exit();
				}
				
				
			}
			if(isset($_REQUEST['password'])){
				$insert['password']=$this->input->post('password');
			}
			if(isset($_REQUEST['rich_anonymous'])){
				$insert['rich_anonymous']=$this->input->post('rich_anonymous');
			}
			if(isset($_REQUEST['gift_anonymous'])){
				$insert['gift_anonymous']=$this->input->post('gift_anonymous');
			}
			if(isset($_REQUEST['show_city'])){
				$insert['show_city']=$this->input->post('show_city');
			}
			if(isset($_REQUEST['introduction'])){
				$insert['introduction']=$this->input->post('introduction');
			}
			if(isset($_REQUEST['lng1'])){
				$insert['lng1']=$this->input->post('lng1');
			}
			if(isset($_REQUEST['lng2'])){
				$insert['lng2']=$this->input->post('lng2');
			}
			
			if(isset($_REQUEST['lat'])){
				$insert['lat']=$this->input->post('lat');
			}
			if(isset($_REQUEST['lng'])){
				$insert['lng']=$this->input->post('lng');
			}
			
			if(isset($_REQUEST['dob'])){
				$insert['dob']=$this->input->post('dob');
			}
			if(isset($_REQUEST['city'])){
				$insert['city']=$this->input->post('city');
			}
			if(isset($_REQUEST['chat_price'])){
				
				$insert['chat_price_due_date'] = date('Y-m-d',strtotime(date('Y-m-d').' + 7 days'));
				$insert['chat_price']=$this->input->post('chat_price');
			}
			if(isset($_REQUEST['show_translation'])){
				$insert['show_translation']=$this->input->post('show_translation');
			}
			
			
			
			$run = $this->common_model->UpdateData('users',array('id'=>$user_id),$insert);
			//echo $this->db->last_query();
			$output['data'] = $this->customer_profile($user_id);
			$output['status'] = 1;
			$output['message'] = 'Your profile has been updated successfully.';
		} else {
			$output['status'] = 0;
			//$output['error_message'] = validation_errors();
			$output['message'] = 'Check parameter.';
		}
		echo json_encode($output);
	}

	public function GetNextChatPrice(){
		if(isset($_REQUEST['user_id'])){
			$user_id=$_REQUEST['user_id'];
			
			$userdata = $this->common_model->GetColumnName('users',"id = $user_id",array('id','chat_price','chat_price_due_date'));
			
			if($userdata){
				$result = array();
				$k = 0;
				$last_price = $userdata['chat_price']/1000;
				
				$today = date('Y-m-d');
				
				if(strtotime($today) >= strtotime($userdata['chat_price_due_date'])){
					$last_price++;
				}
				
				for($i=2; $i<=$last_price; $i++){
					$j = $i*1000;
					$result[$k]['name'] = $j.' diamonds/min';
					$result[$k]['value'] = $j;
					$k++;
				}
				
				$output['result'] = $result;
				$output['next_due_date'] = ($userdata['chat_price_due_date']) ? date('d-m-Y',strtotime($userdata['chat_price_due_date'])) : '';
				$output['status'] = 1;
				$output['message'] = "Success";
				
			} else {
				$output['status'] = 0;
				$output['message'] = "Something went wrong, try again later.";
			}
					
		} else {
			$output['status'] = 0;
			$output['message'] = 'Check parameter.';
		}
		echo json_encode($output);
	}
	
	private function UserIPAddress(){
		//https://www.webwiders.in/WEB01/rapid/api/UserIPAddress
		$client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = @$_SERVER['REMOTE_ADDR'];
    if(filter_var($client, FILTER_VALIDATE_IP)){
        $ip = $client;
    }elseif(filter_var($forward, FILTER_VALIDATE_IP)){
        $ip = $forward;
    }else{
        $ip = $remote;
    }
		
		return $ip;
		
	}

	public function GetCountryByIP(){ 
	
		//https://www.webwiders.in/WEB01/rapid/api/GetCountryByIP
		
		$ip_detals=false;
		
		$ip = $this->UserIPAddress();
		

		$json = file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip); 
		
		echo $json;
	}
	
	public function UpdateDeviceId(){
		//https://www.webwiders.in/WEB01/rapid/api/UpdateDeviceId
		//id=4
		//device_id=abcxyz
		
		$this->form_validation->set_rules('user_id','id','trim|required');
		
		if($this->form_validation->run()){
			
			$id = $this->input->post('user_id');
			
			$insert['device_id'] = $this->input->post('device_id');
			$insert['updated_at'] = date('Y-m-d H:i:s');
		
			$run = $this->common_model->UpdateData('users',array('id'=>$id),$insert);
		
			if($run){

				$output['status'] = 1;
				$output['message'] = 'Success!';
			} else {
				$output['status'] = 0;
				$output['message'] = "Something went wrong, try again later!";
			}
			
 		} else {
			$output['status'] = 0;
			$output['message'] = 'Check parameter.';
		}
		echo json_encode($output);
	}
	
	public function GetUserProfile(){
		//https://www.webwiders.in/WEB01/rapid/api/GetUserProfile?id=1
	
 	 	if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && isset($_REQUEST['session_id']) && !empty($_REQUEST['session_id']))
		{
			$user_id = $_REQUEST['id'];
			$session_id = $_REQUEST['session_id'];
			$today = date('Y-m-d');

			$user_profile = $this->customer_profile($user_id);
			
			
			/** block user code start */
			$check_block = $this->common_model->GetColumnName('block_users',array('block_by'=>$session_id,'block_to'=>$user_id),array('id'));
			$user_profile['is_blocked'] = ($check_block) ? 1 : 0;
			
			/** block user code start */
			$blockUsers = $this->common_model->GetSingleData('block_users','((block_by = '.$session_id.' and block_to = '.$user_id.') or (block_by = '.$user_id.' and block_to = '.$session_id.'))');
			
			if ($blockUsers) {
				$user_profile['block_status'] =1;
				$user_profile['block_by'] = $blockUsers["block_by"];
			} else {
				$user_profile['block_status'] = 0;
				$user_profile['block_by'] = 0;
			}
			/** follow code start */
			
			$check_follow = $this->common_model->GetColumnName('followers',array('follow_Id'=>$user_id,'user_id'=>$session_id),array('id'));
			$user_profile['is_follow'] = ($check_follow) ? 1 : 0;
			
			$followers = $this->common_model->GetColumnName('followers',array('follow_Id'=>$user_id),array('count(id) as total'));
			$user_profile['followers_count'] = $followers['total'];
			
			$following = $this->common_model->GetColumnName('followers',array('user_id'=>$user_id),array('count(id) as total'));
			$user_profile['following_count'] = $following['total'];
			/** follow code end */
			
			/** friend code start */
			$friends = $this->common_model->GetColumnName('Friendlist',"status = 1 and (user_id = $user_id or request_user_id = $user_id)",array('count(id) as total'));
			
			$user_profile['friends_count'] = $friends['total'];
			/** friend code end */

			/** current guardian code start */
			$guardian = $this->common_model->GetColumnName('guardian',"status = 1 and primary_user = $user_id and DATE(end_date) >= DATE('".$today."')");
			
			$guardian_array['image'] = Null;
			$guardian_array['id'] = Null;
			$guardian_array['nickname'] = Null;
			$guardian_array['data'] = Null;
			
			if($guardian){
				
				$guardian_user = $this->common_model->GetColumnName('users',"id = '".$guardian['secondry_user']."'",array('image','nickname','id','gender'));
				
				if($guardian_user){
				
					if($guardian_user['image']){
						$guardian_array['image'] = site_url().'upload/users/'.$guardian_user['image'];
					} else {
						if($guardian_user['gender']=='male'){
							$guardian_array['image'] = site_url().'upload/male.png';
						} else {
							$guardian_array['image'] = site_url().'upload/default_Image.png';
						}
						
					}
					
					$guardian_array['id'] = $guardian_user['id'];
					$guardian_array['nickname'] = $guardian_user['nickname'];
					$guardian_array['data'] = $guardian;
				
				}
				
			}
			
			$user_profile['guardian'] = $guardian_array;
			/** current guardian code end */
			
			/** previews guardian code start */
			$previews_guardian2 = array();
			$previews_guardian = $this->common_model->GetAllData('guardian',"primary_user = $user_id",'id','desc');
			$i = 0;
			if(!empty($previews_guardian)){
				
				foreach($previews_guardian as $key => $value){
				
					$guardian_user = $this->common_model->GetColumnName('users',"id = '".$value['secondry_user']."'",array('image','nickname','id','gender'));
					
					if($guardian_user){
					
						if($guardian_user['image']){
							$previews_guardian2[$i]['image'] = site_url().'upload/users/'.$guardian_user['image'];
						} else {
							if($guardian_user['gender']=='male'){
								$previews_guardian2[$i]['image'] = site_url().'upload/male.png';
							} else {
								$previews_guardian2[$i]['image'] = site_url().'upload/default_Image.png';
							}
						}
						
						$previews_guardian2[$i]['id'] = $guardian_user['id'];
						$previews_guardian2[$i]['nickname'] = $guardian_user['nickname'];
						$previews_guardian2[$i]['data'] = $value;
						
						$i++;
					}
				}
				
			}
			
			$user_profile['previews_guardian'] = $previews_guardian2;
			/** previews guardian code end */
			
			/** album code start */
			$album = $this->common_model->GetColumnName('album',"user_id = $user_id",array('id','type','status',"CONCAT('" .site_url() ."',file) AS file","CONCAT('" .site_url() ."',thumb) AS thumb"),true,'id','desc'); 
			
			$user_profile['album'] = $album;
			/** album code end */
			
			/** badges code start */
			$my_badges = array();
			if(!empty($user_profile['badges'])){
				$my_badges = $this->common_model->GetColumnName('badges',"id in (".$user_profile['badges'].")",array('id','title',"CONCAT('" .site_url() ."',image) AS image"),true,'id','desc'); 
			}
			
			$user_profile['badges'] = $my_badges;
			/** badges code end */
			
			/** post count */
			$post_count = $this->common_model->GetColumnName('post',"user_id = $user_id",array('count(id) as total'));  
			
			$user_profile['post_count'] = $post_count['total'];
			/** post count */
			
			/** calculate daily rank */
			$daily_rank = '100+';
			if($user_profile['gender']=='male'){
				
				$daily_rank_sql ="select id, (select SUM(diamond) from transaction where transaction.user_id = users.id and transaction.type = 1 and DATE(transaction.date) = DATE('".$today."')) as d from users where users.gender = 'male' and users.is_verified = 1 order by (select SUM(diamond) from transaction where transaction.user_id = users.id and transaction.type = 1 and DATE(transaction.date) = DATE('".$today."')) desc limit 100";
				
				
			} else {
				
				$daily_rank_sql ="select id from users where users.gender = 'female' and users.is_verified = 1 order by (select SUM(coin) from coin_transaction where coin_transaction.user_id = users.id and DATE(coin_transaction.create_date) = DATE('".$today."')) desc limit 100";
			
				
			}
			$daily_ranks = $this->db->query($daily_rank_sql)->result_array();
		
			$i = 1;
			if(!empty($daily_ranks)){
				foreach($daily_ranks as $key => $value){
					if($value['id']==$user_id){
						$daily_rank = $i;
						break;
					}
					$i++;
				}
			}
			
			$user_profile['daily_rank'] = $daily_rank;
			/** calculate daily rank */
			
			
			

			$RingData = $this->common_model->GetSingleData('ringTone', array('id'=>1));
			$verification_limit = $this->common_model->GetSingleData('verification_limit', array('id'=>1));
			$userdata = $this->common_model->GetSingleData('users', array('id'=>$session_id ));

			if ($userdata["gender"] == "female") {
				if ($userdata["coin"] >= $verification_limit["min_coins_girl"] && $userdata["level"] >= $verification_limit["min_level_girl"]) {
					$eligible_verification = 1;
				} else 
				{
					$eligible_verification = 0;
				}
			} else if ($userdata["gender"] == "male") {
					if ($userdata["diamond"] >= $verification_limit["min_daimonds_boy"] && $userdata["level"] >= $verification_limit["min_level_boy"]) {
					$eligible_verification = 1;
					} else 
					{
						$eligible_verification = 0;
					}
				} else {
						$eligible_verification = 0;
				}
			
			$PaymentSetting = $this->common_model->GetColumnName('admin',"id = 1",array('min_withdrawal','max_withdrawal','coin_rate','album_limit','level_min_diamond','level_min_coin','watch_video_diamonds','post_max_image'));

			$output['data'] = $user_profile;
			$output['PaymentSetting'] = $PaymentSetting;
			$output['eligible_verification']= $eligible_verification;
			$output['rings'] = site_url().'upload/rings/'.$RingData["file"];
			$output['status'] = 1;
			$output['message'] = 'Success.';

		}else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
 	 	echo json_encode($output);
	}
	
	public function InserUserMeta(){
		//https://www.webwiders.in/WEB01/rapid/api/InserUserMeta
		if(isset($_REQUEST['social_id']) && isset($_REQUEST['email']) && isset($_REQUEST['fname']) && isset($_REQUEST['lname'])) {

			$insert['email'] = $_REQUEST['email'];
			$insert['fname'] = $_REQUEST['fname'];
			$insert['social_id'] = $_REQUEST['social_id'];
			$insert['lname'] = ($_REQUEST['lname']) ? $data['lname'] : '';
			
			$run = $this->common_model->InsertData('user_meta_data',$insert);
			
			if($run){
				
				$output['status'] = 1;
				$output['message'] = 'success.'; 
			} else {
				$output['status'] =0;
				$output['message'] = 'Something went wrong.';
			}
		} else {
			
			$output['status']=0;
			$output['message'] = 'Check parameter.';
		}
		echo json_encode($output);
	}
	
	public function GetUserMeta(){
		//https://www.webwiders.in/WEB01/rapid/api/GetUserMeta
		if(isset($_REQUEST['social_id'])) {
		
			$result = $this->common_model->GetSingleData('user_meta_data',array('social_id'=>$_REQUEST['social_id']));
			
			if($result){
				
				$output['status'] = 1;
				$output['message'] = 'Success';
				$output['data'] = $result; 
			} else {
				$output['status'] = 0;
				$output['message'] = 'Something went wrong, try again later.';
			}
		} else {
			$output['status'] = 0;
			$output['message'] = 'Check parameter.';
		}
		echo json_encode($output);
	}
	
	public function SocialLogin(){
		//https://www.webwiders.in/WEB01/rapid/api/SocialLogin
		
		if(isset($_REQUEST['nickname']) && isset($_REQUEST['LoginType']) && (isset($_REQUEST['FacebookID']) || isset($_REQUEST['GmailID']))) { 
			
			$login_token = md5(time());
			
			$FacebookID = isset($_REQUEST['FacebookID']) ? $_REQUEST['FacebookID'] : '';
			$GmailID = isset($_REQUEST['GmailID']) ? $_REQUEST['GmailID'] : '';
			$email = isset($_REQUEST['email']) ? $_REQUEST['email'] : '';
			
			$insert['email'] = $_REQUEST['email'];
	
			
			$insert['LoginType']=$_REQUEST['LoginType'];
			$insert['updated_at'] = date('Y-m-d H:i:s');
			
			if($insert['LoginType']=='Googleplus'){
				//$where = "GmailID = '$GmailID'";
				$where = "GmailID = '$GmailID' or email = '$email'";
				$insert['GmailID']=$GmailID;
			} else {
				//$where = "FacebookID = '$FacebookID'";
				$where = "FacebookID = '$FacebookID' or email = '$email'";
				$insert['FacebookID']=$FacebookID;
			}
			
			
			$check = $this->common_model->GetColumnName('users',$where,array('id','status','id','nickname'));
			
			$device_id = $this->input->post('device_id');
			if ($device_id) {
				$insert['device_id'] = $device_id;
			}
			
			$insert['is_verified'] = 1;
			$insert['login_token'] = $login_token;
			
			if(isset($_REQUEST['lat'])){
				$insert['lat']=$this->input->post('lat');
			}
			if(isset($_REQUEST['lng'])){
				$insert['lng']=$this->input->post('lng');
			}
			
			if(isset($_REQUEST['city'])){
				$insert['city']=$this->input->post('city');
			}

			if(isset($_REQUEST['country'])){
				$insert['country']=$this->input->post('country');
			}
			
			$output['login_token'] = $login_token;
			
			if($check){
				
				$run = $check['id'];
				
				$insert['updated_at'] = date('Y-m-d H:i:s');
				
				
				
				$this->common_model->UpdateData('users',array('id'=>$run),$insert);
				$output['message'] = 'Welcome '.$check["nickname"];
				$output['status'] = 1;
				
			} else {
				
				$uniqueID = $this->GetUniqueUserID();

				$insert['uniqueID'] = $uniqueID;
				
				$nickname = $insert['nickname'] = $_REQUEST['nickname'];
				
				$insert['updated_at'] = date('Y-m-d H:i:s');
				$insert['created_at'] = date('Y-m-d H:i:s');
				
				$run = $this->common_model->InsertData('users',$insert);
				$output['message'] = 'Welcome '.$nickname;
				
				$output['status'] = 1;
				
			}
			
			if($run){
			
				$user = $this->customer_profile($run);
			
				$output['user'] = $user;
				
			
			}
			
			
 		} else {
			$output['status'] = 0;
			$output['message'] = "Check parameter!";
		}
		echo json_encode($output);
	}

	public function getMetaTagsFromUrl(){
		//https://www.webwiders.in/WEB01/Blend/Api/api/getMetaTagsFromUrl?url=
		if(isset($_REQUEST['url']) && !empty($_REQUEST['url'])) {
			
			error_reporting(0);
			
			$url = trim($_REQUEST['url']);
			
			$doc = new DOMDocument();
			@$doc->loadHTML(file_get_contents($url));
			$res['title'] = $doc->getElementsByTagName('title')->item(0)->nodeValue;

			foreach ($doc->getElementsByTagName('meta') as $m){
					$tag = $m->getAttribute('name') ?: $m->getAttribute('property');
					if(in_array($tag,['description','keywords']) || strpos($tag,'og:')===0) $res[str_replace('og:','',$tag)] = $m->getAttribute('content');
			}
			$tags = $specificTags? array_intersect_key( $res, array_flip($specificTags) ) : $res;
				
				/*$doc = new DOMDocument();
				$doc->loadHTML('<?xml encoding="utf-8" ?>' . implode($matches[0]));
				$tags = array();
				foreach($doc->getElementsByTagName('meta') as $metaTag) {
					if($metaTag->getAttribute('name') != "") {
						$tags[$metaTag->getAttribute('name')] = $metaTag->getAttribute('content');
					}
					elseif ($metaTag->getAttribute('property') != "") {
						$tags[$metaTag->getAttribute('property')] = $metaTag->getAttribute('content');
					}
				}*/
			
			
			$output['tags'] = $tags;
			//$output['data'] = $_SERVER;
			$output['status'] = 1;
			$output['message'] = 'Success.';
			
		}else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
 	 	echo json_encode($output);
	}
	
	private function customer_profile($id=null){
		
		$data = $this->common_model->GetDataById('users',$id);
		$output = false;
		if($data){
            $year = '';
			if(isset($data['dob']) && $data['dob']!='')
			{
				$from = new DateTime($data['dob']);
				$to   = new DateTime('today');
				$year =  $from->diff($to)->y;
			} 
			

			$output['id'] = $data['id'];
			$output['uniqueID'] = $data['uniqueID'];
			$output['show_translation'] = $data['show_translation'];
			$output['show_city'] = $data['show_city'];
			$output['nickname'] = $data['nickname'];
			$output['notification_type'] = $data['notification_type'];
			$output['gift_anonymous'] = $data['gift_anonymous'];
			$output['rich_anonymous'] = $data['rich_anonymous'];
			$output['is_trade_account'] = $data['is_trade_account'];
			$output['trade_balance'] = $data['trade_balance'];
			$output['badges'] = $data['badges'];
			$output['guardian_price'] = $data['guardian_price'];
			$output['phone'] = $data['phone'];
			$output['phone_with_code'] = $data['phone_with_code'];
			$output['free_call'] = $data['free_call'];
			$output['email'] = $data['email'];
			$output['status'] = $data['status'];
			$output['introduction'] = $data['introduction'];
			$output['lng1'] = $this->common_model->GetDataById('language',$data['lng1']);
			$output['lng2'] = $this->common_model->GetDataById('language',$data['lng2']);
			$output['lat'] = $data['lat'];
			$output['lng'] = $data['lng'];
			$output['password'] = $data['password'];
			$output['is_verified'] = $data['is_verified'];
			$output['dob'] = isset($data['dob']) ? date('Y-m-d',strtotime($data['dob'])) : '';
			$output['year'] = $year;
			$output['gender'] = $data['gender'];
			$output['coin'] = $data['coin'];
			$output['diamond'] = $data['diamond'];
			$output['wallet'] = $data['wallet'];
			$output['custom_status'] = $data['custom_status'];
			$output['chat_price'] = $data['chat_price'];
			$output['city'] = $data['city'];
			$output['avg_rate'] = $data['avg_rate'];
			$output['official_badges'] = $data['official_badges'];
			$output['verification_status'] = $verification_icon = $data['verification_icon'];
			
			if($verification_icon == 2){
				$output['verification_icon_image'] = site_url().'assets/verification/UserIcone/verified_account_icon.gif';
			}	

			if ($data['temporary_block'] == 1 && $data['blocked_till_date'] > date('Y-m-d')) {
				$output['temporary_block'] = 1;
				$output['blocked_for'] = $data['blocked_for'];
				$output['blocked_till_date'] = $data['blocked_till_date'];
			} else {
				$output['temporary_block'] = 0;
			}
			$output['level_privilege'] = array();
			$output['level'] = $data['level'];
			$level = $this->common_model->GetSingleData('level',array('level'=>$data['level'],'gender'=>$data['gender']));
			
			
			if($level && $level['privilege']){
				
				$level_privilege = $this->common_model->GetAllData('level_privilege',"id in (".$level['privilege'].")");
				$output['level_privilege'] = $level_privilege;
			}
			
			$totalCall = $this->common_model->GetColumnName('call_records',array('call_to'=>$id),array('sum(total_call_sec) as total'));
			
			$totalCall = isset($totalCall['total']) ? $totalCall['total'] : 0;
			$output['total_call_sec'] = $totalCall*1;
			
			$totalLive = $this->common_model->GetColumnName('live_users',array('user_id'=>$id),array('sum(time_in_sec) as total'));
			
			$totalLive = isset($totalLive['total']) ? $totalLive['total'] : 0;
			$output['total_live_sec'] = $totalLive*1;	
			
			$country = $this->common_model->GetDataById('country',$data['country']);
			
			if($country){
			
				$flags = site_url().'upload/no.png';
				$flag_big = site_url().'upload/no.png';
				
				
				if(file_exists('assets/flags/'.strtolower($country['iso']).'.png')){
					$flags = site_url().'assets/flags/'.strtolower($country['iso']).'.png';
				}
					
				if(file_exists('assets/flag_big/'.strtolower($country['iso']).'.png')){
					$flag_big = site_url().'assets/flag_big/'.strtolower($country['iso']).'.png';
				}
				
				$country['flag_big'] = $flag_big;
				$country['flag'] = $flags;
				
				$output['country'] = $country;
				
			} else {
				$output['country'] = false;
			}
			
			if($data['image']){
				$output['image'] = site_url().'upload/users/'.$data['image'];
			} else {
				if($data['gender']=='male'){
					$output['image'] = site_url().'upload/male.png';
				} else {
					$output['image'] = site_url().'upload/default_Image.png';
				}
				
			}
		
			/* User login status */
			$live_users = $this->common_model->GetColumnName('live_users', array('user_id' => $id,'status' => 1),array('id'));
			$check_call = $this->common_model->GetColumnName('call_records',"(status = 0 or status = 1) and (call_to = $id or call_by = $id)",array('id'));
			
			$endTime = date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s')." -15 second"));
			
			
		
			if($live_users){
				$output['live_status'] = 'Live';
			} else if($check_call) {
				$output['live_status'] = 'Busy';
			} else if(strtotime($endTime) <= strtotime($data['last_login'])) {
				$output['live_status'] = 'Active';
			} else {
				$output['live_status'] = 'Offline';
			}
			
			$lastLoginTime = $data['last_login'];
			$CurrentTime = date('Y-m-d H:i:s');
			
			$last_login_in_min = (strtotime($CurrentTime)-strtotime($lastLoginTime))/60;
			$output['last_login_in_min'] = round($last_login_in_min,0);
			
			/* User login status */
			
			if($data['gender']=='male'){
				$total_diamond = $this->common_model->GetColumnName('transaction',array('user_id'=>$id,'type'=>1,'status'=>1),array('SUM(diamond) as total'));
				$total_diamond = ($total_diamond['total']) ? $total_diamond['total'] : 0;
			} else {
				$total_coin = $this->common_model->GetColumnName('coin_transaction',array('user_id'=>$id),array('SUM(coin) as total'));
				$total_coin = ($total_coin['total']) ? $total_coin['total'] : 0;
				$total_diamond = $total_coin;
			}
			
			$output['total_diamond'] = $total_diamond; //coin use for girl and diamond use for boys
			
		}
		return $output;
	}	

	private function short_profile($id=null){
		
		$data = $this->common_model->GetDataById('users',$id);
		$output = false;
		if($data){
			$year = '';
			if(isset($data['dob']) && $data['dob']!='')
			{
				$from = new DateTime($data['dob']);
				$to   = new DateTime('today');
				$year =  $from->diff($to)->y;
			} 
			

			$output['id'] = $data['id'];
			$output['uniqueID'] = $data['uniqueID'];
			$output['show_translation'] = $data['show_translation'];
			$output['guardian_price'] = $data['guardian_price'];
			$output['show_city'] = $data['show_city'];
			$output['nickname'] = $data['nickname'];
			$output['gift_anonymous'] = $data['gift_anonymous'];
			$output['rich_anonymous'] = $data['rich_anonymous'];
			//$output['is_trade_account'] = $data['is_trade_account'];
			//$output['trade_balance'] = $data['trade_balance'];
			$output['introduction'] = $data['introduction'];
			$output['dob'] = isset($data['dob']) ? date('Y-m-d',strtotime($data['dob'])) : '';
			$output['year'] = $year;
			$output['gender'] = $data['gender'];
			$output['coin'] = $data['coin'];
			$output['diamond'] = $data['diamond'];
			$output['free_call'] = $data['free_call'];
			$output['avg_rate'] = $data['avg_rate'];
			$output['official_badges'] = $data['official_badges'];
			$output['verification_status'] = $verification_icon = $data['verification_icon'];
			
			if($verification_icon == 2){
				$output['verification_icon_image'] = site_url().'assets/verification/UserIcone/verified_account_icon.gif';
			}
			$output['level_privilege'] = array();
			$output['level'] = $data['level'];
			$level = $this->common_model->GetSingleData('level',array('level'=>$data['level'],'gender'=>$data['gender']));
			
			
			if($level && $level['privilege']){
				
				$level_privilege = $this->common_model->GetAllData('level_privilege',"id in (".$level['privilege'].")");
				$output['level_privilege'] = $level_privilege;
			} 
			
			$country = $this->common_model->GetDataById('country',$data['country']);
			
			if($country){
			
				$flags = site_url().'upload/no.png';
				$flag_big = site_url().'upload/no.png';
				
				
				if(file_exists('assets/flags/'.strtolower($country['iso']).'.png')){
					$flags = site_url().'assets/flags/'.strtolower($country['iso']).'.png';
				}
					
				if(file_exists('assets/flag_big/'.strtolower($country['iso']).'.png')){
					$flag_big = site_url().'assets/flag_big/'.strtolower($country['iso']).'.png';
				}
				
				$country['flag_big'] = $flag_big;
				$country['flag'] = $flags;
				
				$output['country'] = $country;
				
			} else {
				$output['country'] = false;
			}
			
		
			if($data['image']){
				$output['image'] = site_url().'upload/users/'.$data['image'];
			} else {
				if($data['gender']=='male'){
					$output['image'] = site_url().'upload/male.png';
				} else {
					$output['image'] = site_url().'upload/default_Image.png';
				}
			}
			
			/* User login status */
			$live_users = $this->common_model->GetColumnName('live_users', array('user_id' => $id,'status' => 1),array('id'));
			$check_call = $this->common_model->GetColumnName('call_records',"(status = 0 or status = 1) and (call_to = $id or call_by = $id)",array('id'));
			
			$endTime = date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s')." -15 second"));
			
		
		
			if($live_users){
				$output['live_status'] = 'Live';
			} else if($check_call) {
				$output['live_status'] = 'Busy';
			} else if(strtotime($endTime) <= strtotime($data['last_login'])) {
				$output['live_status'] = 'Active';
			} else {
				$output['live_status'] = 'Offline';
			}
			/* User login status */
			
			$lastLoginTime = $data['last_login'];
			$CurrentTime = date('Y-m-d H:i:s');
			
			$last_login_in_min = (strtotime($CurrentTime)-strtotime($lastLoginTime))/60;
			$output['last_login_in_min'] = round($last_login_in_min,0);
			
			
			if($data['gender']=='male'){
				$total_diamond = $this->common_model->GetColumnName('transaction',array('user_id'=>$id,'type'=>1,'status'=>1),array('SUM(diamond) as total'));
				$total_diamond = ($total_diamond['total']) ? $total_diamond['total'] : 0;
			} else {
				$total_coin = $this->common_model->GetColumnName('coin_transaction',array('user_id'=>$id),array('SUM(coin) as total'));
				$total_coin = ($total_coin['total']) ? $total_coin['total'] : 0;
				$total_diamond = $total_coin;
			}
			
			$output['total_diamond'] = $total_diamond; //coin use for girl and diamond use for boys
			
		}
		return $output;
	}	

	private function very_short_profile($id=null){
		
		$data = $this->common_model->GetDataById('users',$id);
		$output = false;
		if($data){
			$year = '';
			if(isset($data['dob']) && $data['dob']!='')
			{
				$from = new DateTime($data['dob']);
				$to   = new DateTime('today');
				$year =  $from->diff($to)->y;
			} 
			

			$output['id'] = $data['id'];
			$output['uniqueID'] = $data['uniqueID'];
			$output['show_city'] = $data['show_city'];
			$output['nickname'] = $data['nickname'];
			$output['introduction'] = $data['introduction'];
			$output['dob'] = isset($data['dob']) ? date('Y-m-d',strtotime($data['dob'])) : '';
			$output['year'] = $year;
			$output['gender'] = $data['gender'];
			$output['coin'] = $data['coin'];
			$output['diamond'] = $data['diamond'];
			$output['avg_rate'] = $data['avg_rate'];
			$output['official_badges'] = $data['official_badges'];
			$output['verification_status'] = $verification_icon = $data['verification_icon'];
			$output['level'] = $data['level'];
			if($verification_icon == 2){
				$output['verification_icon_image'] = site_url().'assets/verification/UserIcone/verified_account_icon.gif';
			}
			
			$country = $this->common_model->GetDataById('country',$data['country']);
			
			if($country){
			
				$flags = site_url().'upload/no.png';
				$flag_big = site_url().'upload/no.png';
				
				
				if(file_exists('assets/flags/'.strtolower($country['iso']).'.png')){
					$flags = site_url().'assets/flags/'.strtolower($country['iso']).'.png';
				}
					
				if(file_exists('assets/flag_big/'.strtolower($country['iso']).'.png')){
					$flag_big = site_url().'assets/flag_big/'.strtolower($country['iso']).'.png';
				}
				
				$country['flag_big'] = $flag_big;
				$country['flag'] = $flags;
				
				$output['country'] = $country;
				
			} else {
				$output['country'] = false;
			}
			
		
			if($data['image']){
				$output['image'] = site_url().'upload/users/'.$data['image'];
			} else {
				if($data['gender']=='male'){
					$output['image'] = site_url().'upload/male.png';
				} else {
					$output['image'] = site_url().'upload/default_Image.png';
				}
			}
			
		}
		return $output;
	}	
	private function gift_data($id=null){
		
		$gift_data = $this->common_model->GetDataById('gift',$id);
	
		if($gift_data){
			
			if($gift_data['sticker']){
				$gift_data['sticker']=base_url().'assets/admin/gift/'.$gift_data['sticker'];
			 } else {
				$gift_data['sticker']=site_url().'upload/default_Image.png';
			}
			if($gift_data['animation']){
				$gift_data['animation']=base_url().'assets/admin/gift/'.$gift_data['animation'];
			}
		}
		
		
		return $gift_data;
	}	

	public function termsPage(){
		$output['data'] = $this->common_model->GetDataById('content_management',1);
		$output['status'] = 1;
		$output['message']='Success.';
		echo json_encode($output);
	}

	public function policyPage(){
		$output['data'] = $this->common_model->GetDataById('content_management',2);
		$output['status'] = 1;
		$output['message']='Success.';
		echo json_encode($output);
	}
	public function aboutUs(){
		$output['data'] = $this->common_model->GetDataById('content_management',3);
		$output['status'] = 1;
		$output['message']='Success.';
		echo json_encode($output);
	}
	public function rateus_page(){
		$output['data'] = $this->common_model->GetDataById('content_management',4);
		$output['status'] = 1;
		$output['message']='Success.';
		echo json_encode($output);
	}
	
	public function userSearch() {
		if(isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) {
			
			$user_id = $_REQUEST['user_id'];
			
			$userdata = $this->common_model->GetColumnName('users',array('id'=>$user_id, 'lat != '=>'','lng != '=>''),array('lat','lng'));
			
			if($userdata && (!isset($_REQUEST['lat']) || empty($_REQUEST['lat']))){
				$_REQUEST['lat'] = $userdata['lat'];
				$_REQUEST['lng'] = $userdata['lng'];
			}
			
			$sql = "select users.id as id";
			
			if(isset($_REQUEST['lat']) && !empty($_REQUEST['lat']) && isset($_REQUEST['lng']) && !empty($_REQUEST['lng'])){
				
				$lat = $_REQUEST['lat'];
				$lng = $_REQUEST['lng'];
				
				$sql .= ", 111.111 * 	DEGREES(ACOS(LEAST(1.0, COS(RADIANS(".$lat.")) * COS(RADIANS(users.lat)) * COS(RADIANS(".$lng." - users.lng)) + SIN(RADIANS(".$lat.")) * SIN(RADIANS(users.lat))))) AS distance_in_km";
			}
			
			$sql .= " from users where id != $user_id and is_verified = 1 and status=1 and user_type=1";
			
			if(isset($_REQUEST['country']) && !empty($_REQUEST['country'])){
				$sql .= " and country = '".$_REQUEST['country']."'";
			}
			
			if(isset($_REQUEST['lang']) && !empty($_REQUEST['lang'])){
				$sql .= " and (lng1 = '".$_REQUEST['lang']."' or lng2 = '".$_REQUEST['lang']."')";
			}	
			
			if (isset($_REQUEST['keywords']) && !empty($_REQUEST['keywords'])) {
				$keywords = ucfirst($_REQUEST['keywords']);
				$sql .= " and (nickname like '%".$keywords."%' or (SELECT count(id) from country WHERE country.id = users.country and country.nicename like '%".$keywords."%') > 0)";
			}
			
			if(isset($_REQUEST['orderby']) && !empty($_REQUEST['orderby'])){
				if($_REQUEST['orderby']=='discover'){
					
					$sql .= " order by id desc";
					
				} else if($_REQUEST['orderby']=='nearby' && isset($_REQUEST['lat']) && !empty($_REQUEST['lat']) && isset($_REQUEST['lng']) && !empty($_REQUEST['lng'])){
					
					$sql .= " order by distance_in_km asc";
					
				} else if($_REQUEST['orderby']=='popular'){
					
					$sql .= " order by (select SUM(coin) from coin_transaction where coin_transaction.user_id = users.id) desc";
					
				} else {
					
					$sql .= " order by id desc";
					
				}
			} else {
				$sql .= " order by id desc";
			}

			
			if(isset($_REQUEST['start']) && isset($_REQUEST['limit'])){
			
				$start = $_REQUEST['start'];
				$limit = $_REQUEST['limit'];
				
				$sql .= " limit $start, $limit"; 
			}

			$run = $this->db->query($sql);
		
			$result = array();
			if($run->num_rows() > 0){
				foreach($run->result_array() as $key => $row){
					$result[$key] = $this->customer_profile($row['id']);
					if(isset($_REQUEST['lat']) && !empty($_REQUEST['lat']) && isset($_REQUEST['lng']) && !empty($_REQUEST['lng'])){
					$result[$key]['distance_in_km'] = $row['distance_in_km'];
					}
				}
			}
				$output['message'] = "Success";
				$output['data'] = $result;

		}	else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
 	 	echo json_encode($output);
	}

	public function startFollow() {

		if(isset($_REQUEST['userId']) && !empty($_REQUEST['userId']) && isset($_REQUEST['follow_Id']) && !empty($_REQUEST['follow_Id']))
		{
			
			$check_2_follow = $this->common_model->GetColumnName('followers',"user_id = '".$_REQUEST['follow_Id']."' and follow_Id = '".$_REQUEST['userId']."'",array('id'));
						
			if($check_2_follow){
				$check_friend = $this->common_model->GetColumnName('Friendlist',"status = 1 and ((user_id = '".$_REQUEST['userId']."' and request_user_id = '".$_REQUEST['follow_Id']."') or (user_id = '".$_REQUEST['follow_Id']."' and request_user_id = '".$_REQUEST['userId']."'))",array('id'));
						
				if(!$check_friend){
					$insert3 = array();
					$insert3['user_id'] = $_REQUEST['userId'];
					$insert3['request_user_id'] = $_REQUEST['follow_Id'];
					$insert3['status'] = 1;
					$this->common_model->InsertData('Friendlist',$insert3);
				}
			}

			$alreadyFollow = $this->common_model->GetSingleData('followers',array('user_id'=>$_REQUEST['userId'], 'follow_Id' => $_REQUEST['follow_Id']));

			if ($alreadyFollow) {
				$output['status'] = 1;
				$output['message'] = "Success, user already following this user.";
			} else {
					$insert["user_id"] = $_REQUEST['userId'];
					$insert["follow_Id"] = $_REQUEST['follow_Id'];
					$insert['created_at'] = date('Y-m-d H:i:s');
					
					$run = $this->common_model->InsertData('followers',$insert);
					if ($run) {
						
						$output['status'] = 1;
						$output['message'] = "Success.";
					}
					else {
						$output['status'] = 0;
						$output['message'] = "Something went wrong.";
					}
			}
			
		}
		else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters.";
 	 	}
 	 	echo json_encode($output);
	}
	
	public function unFollow() {
			if(isset($_REQUEST['userId']) && !empty($_REQUEST['userId']) && isset($_REQUEST['follow_Id']) && !empty($_REQUEST['follow_Id']))
		{

					$user_id = $_REQUEST['userId'];
					$follow_Id= $_REQUEST['follow_Id'];
					
					$run = $this->common_model->DeleteData('followers',array('user_id'=>$user_id, 'follow_Id' => $follow_Id));
					if ($run) {
						
						$output['status'] = 1;
						$output['message'] = "Success. unfollow user";
					}
					else {
						$output['status'] = 0;
						$output['message'] = "Something went wrong.";
					}

			
		}
		else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters.";
 	 	}
 	 	echo json_encode($output);
	}

	public function followingList() {
		if(isset($_REQUEST['userId']) && !empty($_REQUEST['userId'])) {
				$run = $this->common_model->GetAllData('followers',array('user_id'=>$_REQUEST['userId']));
				$result = array();
				if ($run) {
					foreach ($run as $data) {

						
						$result[] = $this->short_profile($data["follow_Id"]);
					}

						$output['status'] = 1;
						$output['message'] = "Success!";
						$output['data'] = $result;
					} else {
						$output['status'] = 1;
						$output['message'] = "No data.";
						$output['data'] = $result;
					}
		} else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters.";
 	 	}
 	 	echo json_encode($output);
	}

	public function Myfollowerslist() {
		if(isset($_REQUEST['userId']) && !empty($_REQUEST['userId'])) {
				$run = $this->common_model->GetAllData('followers',array('follow_Id'=>$_REQUEST['userId']));
			
				$result = array();
				if ($run) {
					foreach ($run as $data) {

				
						$result[] = $this->short_profile($data["user_id"]);
					}

						$output['status'] = 1;
						$output['message'] = "Success!";
						$output['data'] = $result;
					} else {
						$output['status'] = 1;
						$output['message'] = "No data.";
						$output['data'] = $result;
					}
		} else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters.";
 	 	}
 	 	echo json_encode($output);
	}


	public function FriendRequestSend() {

		if(isset($_REQUEST['userId']) && !empty($_REQUEST['userId']) && isset($_REQUEST['request_user_id']) && !empty($_REQUEST['request_user_id']))
		{

			$alreadyFollow = $this->common_model->GetSingleData('Friendlist',array('user_id'=>$_REQUEST['userId'], 'request_user_id' => $_REQUEST['request_user_id']));

			if ($alreadyFollow) {
				$output['status'] = 1;
				$output['message'] = "Success, You have already send friend request this user.";
			} else {
					$insert["user_id"] = $_REQUEST['userId'];
					$insert["request_user_id"] = $_REQUEST['request_user_id'];
					$insert['created_at'] = date('Y-m-d H:i:s');
					
					$run = $this->common_model->InsertData('Friendlist',$insert);
					if ($run) {
						
						$output['status'] = 1;
						$output['message'] = "Success. You have send friend request this user.";
					}
					else {
						$output['status'] = 0;
						$output['message'] = "Something went wrong.";
					}
			}
			
		}
		else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters.";
 	 	}
 	 	echo json_encode($output);
	}

	public function IncommingFriendRequest() {
		if(isset($_REQUEST['userId']) && !empty($_REQUEST['userId'])) {
				$run = $this->common_model->GetAllData('Friendlist',array('user_id'=>$_REQUEST['userId']));
				$result = array();
				if ($run) {
					foreach ($run as $data) {

						$data["following"]=$this->short_profile($data["request_user_id"]); 
						$result[] = $data;
					}

						$output['status'] = 1;
						$output['message'] = "Success! Incoming friend request listing.";
						$output['data'] = $result;
					} else {
						$output['status'] = 1;
						$output['message'] = "No data.";
						$output['data'] = $result;
					}
		} else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters.";
 	 	}
 	 	echo json_encode($output);
	}

	public function get_friend_list() {
		if(isset($_REQUEST['userId']) && !empty($_REQUEST['userId'])) {
			$userId = $_REQUEST['userId'];
				$run = $this->common_model->GetAllData('Friendlist',"(user_id = '".$userId."' or request_user_id = '".$userId."')");
				$result = array();
				if ($run) {
					foreach ($run as $data) {
						
						if($data['user_id']==$userId){
							$result[] = $this->short_profile($data['request_user_id']);;
						} else {
							$result[] = $this->short_profile($data['user_id']);;
						}
					
						
					}

						$output['status'] = 1;
						$output['message'] = "Success! Incoming friend request listing.";
						$output['data'] = $result;
					} else {
						$output['status'] = 1;
						$output['message'] = "No data.";
						$output['data'] = $result;
					}
		} else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters.";
 	 	}
 	 	echo json_encode($output);
	}

	public function DeleteFriendRequest() {
			if(isset($_REQUEST['userId']) && !empty($_REQUEST['userId']) && isset($_REQUEST['request_user_id']) && !empty($_REQUEST['request_user_id']))
		{

					$user_id = $_REQUEST['userId'];
					$request_user_id= $_REQUEST['request_user_id'];
					
					$run = $this->common_model->DeleteData('Friendlist',array('user_id'=>$user_id, 'request_user_id' => $request_user_id));
					if ($run) {
						
						$output['status'] = 1;
						$output['message'] = "Success. You have reject friend request this user.";
					}
					else {
						$output['status'] = 0;
						$output['message'] = "Something went wrong.";
					}

			
		}
		else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters.";
 	 	}
 	 	echo json_encode($output);
	}


	public function AcceptFriendRequest() {
			if(isset($_REQUEST['userId']) && !empty($_REQUEST['userId']) && isset($_REQUEST['request_user_id']) && !empty($_REQUEST['request_user_id']))
		{

					$user_id = $_REQUEST['userId'];
					$request_user_id= $_REQUEST['request_user_id'];
					$Insert['status']= 1;
					
					$run = $this->common_model->UpdateData('Friendlist',array('user_id'=>$user_id,'request_user_id' => $request_user_id),$Insert);
					if ($run) {
						
						$output['status'] = 1;
						$output['message'] = "Success. You have accept friend request this user.";
					}
					else {
						$output['status'] = 0;
						$output['message'] = "Something went wrong.";
					}

			
		}
		else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters.";
 	 	}
 	 	echo json_encode($output);
	}
	
	public function MyRecieveGiftlist() {
		if(isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) {
			$user_id = $_REQUEST['user_id'];
			$sql = "select *, (select SUM(A.quantity) from gift_send as A where A.gift_id = B.gift_id and A.send_to = $user_id) as counts from gift_send as B where B.send_to = $user_id  group by B.gift_id order by MAX(B.id) DESC";
			$run = $this->db->query($sql);
			//$run = $this->common_model->GetAllData('gift_send',array('send_to'=>$_REQUEST['user_id']));
			
			$result = array();
			$i = 0;
			if ($run->num_rows()) {
				foreach ($run->result_array() as $key => $data) {
					$result[$i] = $data;
					
					$send_by = $this->customer_profile($data['send_by']);
					
					if($send_by){
						
						$result[$i]['gift_data'] = $this->gift_data($data['gift_id']);
						$result[$i]['send_by'] = $send_by;

						
						$i++;
					}								 
				}

				$output['status'] = 1;
				$output['message'] = "Success!";
				$output['data'] = $result;
				
				} else {
					$output['status'] = 1;
					$output['message'] = "No data found.";
					$output['data'] = $result;
				}
		} else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters.";
 	 	}
 	 	echo json_encode($output);
	}
		
	public function MySendGiftlist() {
		if(isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) {
			//$run = $this->common_model->GetAllData('gift_send',array('send_by'=>$_REQUEST['user_id']));
			
			$user_id = $_REQUEST['user_id'];
			$sql = "select *, (select count(id) from gift_send as A where A.gift_id = B.gift_id and A.send_by = $user_id) as counts from gift_send as B where B.send_by = $user_id  group by B.gift_id order by MAX(B.id) DESC";
			$run = $this->db->query($sql);
			//$run = $this->common_model->GetAllData('gift_send',array('send_by'=>$_REQUEST['user_id']));
			
			$result = array();
			$i = 0;
			if ($run->num_rows()) {
				foreach ($run->result_array() as $key => $data) {
					$result[$i] = $data;
					
					$gift_data = $this->common_model->GetDataById('gift',$data['gift_id']);
					$send_to = $this->customer_profile($data['send_to']);
					
					if($send_to){
						
					
					
						$result[$i]['gift_data'] = $this->gift_data($data['gift_id']);
						$result[$i]['send_to'] = $send_to;

						
						$i++;
					}								 
				}

				$output['status'] = 1;
				$output['message'] = "Success!";
				$output['data'] = $result;
				
				} else {
					$output['status'] = 1;
					$output['message'] = "No data found.";
					$output['data'] = $result;
				}
		} else{

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters.";
 	 	}
 	 	echo json_encode($output);
	}
		
	
	
	
	public function send_gift() {
		//
		if(isset($_REQUEST['send_by']) && isset($_REQUEST['send_to']) && isset($_REQUEST['quantity']) && isset($_REQUEST['gift_id'])) {
		

    $user = $this->common_model->GetSingleData('users',array('id'=>$_REQUEST['send_by']));
    
			$user_daimond = $user['diamond'];
    
			$gift = $this->common_model->GetSingleData('gift',array('id'=>$_REQUEST['gift_id']));
			
			$quantity = $_REQUEST['quantity'];
			
			$total_diamond = $gift['daimond']*$quantity;
       
			$total_coin = $gift['coin']*$quantity;
			
			
      if($user_daimond >= $total_diamond){
        
        $userTo = $this->common_model->GetSingleData('users',array('id'=>$_REQUEST['send_to']));
        
        $update['diamond'] = $user['diamond']-$total_diamond;
      
				$send_by = $insert1['send_by'] = $_REQUEST['send_by'];
				$send_to = $insert1['send_to'] = $_REQUEST['send_to'];
				$insert1['quantity'] = $quantity;
				
				$insert1['gift_id'] = $_REQUEST['gift_id'];
				 if (isset($_REQUEST["party_id"]) && !empty($_REQUEST["party_id"])){
					$insert1['party_id'] = $_REQUEST['party_id'];
				}
				
				$insert1['diamond'] = $total_diamond;
				$insert1['coin'] = $total_coin;

        $run_1 = $this->common_model->UpdateData('users',array('id'=>$send_by),$update);
        	
				$run = $this->common_model->InsertData('gift_send', $insert1);
				
				$setting = $this->common_model->GetColumnName('admin',array('id'=>1),array('coin_rate','commission'));
				
		
				$amount = round($total_coin/$setting['coin_rate'],2);
				
				$update2['coin'] = $userTo['coin']+$total_coin;
				//$update2['wallet'] = $userTo['wallet']+$amount;
				$this->common_model->UpdateData('users',array('id'=>$send_to),$update2);
				
				
				$insert4['user_id'] = $userTo['id'];
				$insert4['coin'] = $gift['coin'];
				$insert4['amount'] = $amount;
				$insert4['source'] = 'Gift';
				$insert4['create_date'] = date('Y-m-d H:i:s');
				$this->common_model->InsertData('coin_transaction',$insert4); 
				
				if($userTo['gender']=='female'){
					$this->create_girl_level($send_to,$amount,'Gift');
				}

				$notArr['user_id'] = $userTo['id'];
				$notArr['message'] = $user['nickname']." has sent you a gift.";
				$notArr['behalf_of'] = $send_by;
				$notArr['device_id'] = $userTo['device_id'];
				
				$notArr['other'] = array('screen'=>'gift','gift_send_id'=>$run,'gift_id'=>$_REQUEST['gift_id']);
				$this->common_model->send_and_insert_notifi($notArr);
				
				$insert5['user_id'] = $user['id'];
				$insert5['diamond'] = $total_diamond;
				$insert5['transaction_id'] = time();
				$insert5['payment_method'] = 'Gift';
				$insert5['free_call'] = 0;
				$insert5['amount'] = 0;
				$insert5['type'] = 2;
				$insert5['status'] = 1;
				$insert5['date'] = date('Y-m-d H:i:s');
				$insert5['message'] = 'Spend on gift';
				$this->common_model->InsertData('transaction',$insert5);
		
				if ($run) {

					if (isset($_REQUEST["gift_type"]) && $_REQUEST["gift_type"] == 'party') {
						// code...
					} else {
					
					$insert6['sender'] = $user['id'];
					$insert6['receiver'] = $userTo['id'];
					$insert6['message_2'] = $run;
					$insert6['message'] = $run;
					$insert6['msg_type'] = 'gift';
					$insert6['call_id'] = (isset($_REQUEST['call_id']) && !empty($_REQUEST['call_id'])) ? $_REQUEST['call_id'] : 0;

					$insert6['read_msg'] = 0;
					$insert6['read_msg'] = 0;
					$insert6['cdate'] = date('Y-m-d H:i:s');
					
					$this->common_model->InsertData('user_chatmessage',$insert6);
					}
					$output['status'] = 1;
					$output['message'] = 'Gift has been sent successfully.';
				} else {
					$output['status'] = 0;
					$output['message'] = 'no data found.';
				}
				
			} else {
				
				$output['status'] = 2;
				$output['message'] = 'You have not sufficient diamond.';
				
		
      }
		
			
		}	else{
 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
		
		
		echo json_encode($output);

	}

	public function send_gift_multiple() {
		//
		if(isset($_REQUEST['send_by']) && isset($_REQUEST['send_to']) && isset($_REQUEST['quantity']) && isset($_REQUEST['gift_id'])) {
			
			$send_to_arr = explode(',',$_REQUEST['send_to']);
			
			if(empty($send_to_arr)){
				$output['status'] = 0;
				$output['message'] = 'Check parameters.';
				echo json_encode($output);
				exit();
			}
			
			foreach($send_to_arr as $key => $send_to){
			
				$user = $this->common_model->GetSingleData('users',array('id'=>$_REQUEST['send_by']));
    
				$user_daimond = $user['diamond'];
    
				$gift = $this->common_model->GetSingleData('gift',array('id'=>$_REQUEST['gift_id']));
			
				$quantity = $_REQUEST['quantity'];
			
				$total_diamond = $gift['daimond']*$quantity;
       
				$total_coin = $gift['coin']*$quantity;
			
			
				if($user_daimond < $total_diamond){
					$output['status'] = 2;
					$output['message'] = 'You have not sufficient diamond.';
					echo json_encode($output);
					exit();
				}
					
				$update = array();
			
				$userTo = $this->common_model->GetSingleData('users',array('id'=>$send_to));
			
				$update['diamond'] = $user['diamond']-$total_diamond;
				
				$insert1 = array();
		
				$send_by = $insert1['send_by'] = $_REQUEST['send_by'];
				$insert1['send_to'] = $send_to;
				$insert1['quantity'] = $quantity;
			
				$insert1['gift_id'] = $_REQUEST['gift_id'];
				
				if (isset($_REQUEST["party_id"]) && !empty($_REQUEST["party_id"])){
					$insert1['party_id'] = $_REQUEST['party_id'];
				}
			
				$insert1['diamond'] = $total_diamond;
				$insert1['coin'] = $total_coin;

				$run_1 = $this->common_model->UpdateData('users',array('id'=>$send_by),$update);
				
				$run = $this->common_model->InsertData('gift_send', $insert1);
			
				$setting = $this->common_model->GetColumnName('admin',array('id'=>1),array('coin_rate','commission'));
			
	
				$amount = round($total_coin/$setting['coin_rate'],2);
			
				$update2['coin'] = $userTo['coin']+$total_coin;
				//$update2['wallet'] = $userTo['wallet']+$amount;
				$this->common_model->UpdateData('users',array('id'=>$send_to),$update2);
				//echo $this->db->last_query();
			
				$insert4['user_id'] = $userTo['id'];
				$insert4['coin'] = $gift['coin'];
				$insert4['amount'] = $amount;
				$insert4['source'] = 'Gift';
				$insert4['create_date'] = date('Y-m-d H:i:s');
				$this->common_model->InsertData('coin_transaction',$insert4); 
			
				if($userTo['gender']=='female'){
					$this->create_girl_level($send_to,$amount,'Gift');
				}

				$notArr['user_id'] = $userTo['id'];
				$notArr['message'] = $user['nickname']." has sent you a gift.";
				$notArr['behalf_of'] = $send_by;
				$notArr['device_id'] = $userTo['device_id'];
			
				$notArr['other'] = array('screen'=>'gift','gift_send_id'=>$run,'gift_id'=>$_REQUEST['gift_id']);
				$this->common_model->send_and_insert_notifi($notArr);
			
				$insert5['user_id'] = $user['id'];
				$insert5['diamond'] = $total_diamond;
				$insert5['transaction_id'] = time();
				$insert5['payment_method'] = 'Gift';
				$insert5['free_call'] = 0;
				$insert5['amount'] = 0;
				$insert5['type'] = 2;
				$insert5['status'] = 1;
				$insert5['date'] = date('Y-m-d H:i:s');
				$insert5['message'] = 'Spend on gift';
				$this->common_model->InsertData('transaction',$insert5);
	
				
				$insert6['sender_id'] = $user['id'];
				$insert6['party_id'] = (isset($_REQUEST['party_id']) && !empty($_REQUEST['party_id'])) ? $_REQUEST['party_id'] : 0;
				$insert6['message'] = $run;
				$insert6['msg_type'] = 'gift';
				$insert6['create_date'] = date('Y-m-d H:i:s');
			
				$this->common_model->InsertData('party_chat',$insert6);
					
				$output['status'] = 1;
				$output['message'] = 'Gift has been sent successfully.';
				
			
				
			}
		
		}	else{
			$output['status'] = 0;
			$output['message'] = "Check parameters";
		}
		
			echo json_encode($output);

	}

	public function level(){
		$output['data'] = $this->common_model->GetAllData('level');
		$output['status'] = 1;
		$output['message']='Success.';
		echo json_encode($output);
	}

	public function AllGiftList() {

		$run = $this->common_model->GetAllData('gift_category',null,'id','desc');
		
		$result = array();
		
		if ($run) {
			foreach ($run as $key => $data) {
				$result[$key] = $data;

				$gistData = $this->common_model->GetAllData('gift', array('category'=>$data["id"]));
				$gift_data = array();
				foreach($gistData as $key1 => $value1){
					
					$gift_data[$key1] = $this->gift_data($value1['id']);
					
					
				}

				
								

				$result[$key]["giftData"] = $gift_data;
			}

				$output['status'] = 1;
				$output['message'] = "Success!";
				$output['data'] = $result;
			} else {
				$output['status'] = 1;
				$output['message'] = "No data found.";
				$output['data'] = $result;
			}
 	 	echo json_encode($output);
	}

	public function verificationRequest() {
		//https://www.webwiders.in/WEB01/rapid/api/verificationRequest?user_id=42
		 if(isset($_REQUEST['user_id']) && isset($_REQUEST['user_id']) && isset($_REQUEST['doc_type']) && isset($_REQUEST['doc_type']) && isset($_REQUEST['full_name']) && isset($_REQUEST['full_name'])){

		 	$user_id = $_REQUEST['user_id'];

		 	$insert["doc_type"] = $_REQUEST['doc_type'];
		 	$insert["full_name"] = $_REQUEST['full_name'];

					if(isset($_FILES['front_pic']['name']) && !empty($_FILES['front_pic']['name'])){
						$image_arr = $_FILES['front_pic']['name'];
							
						$ext = explode('.',$_FILES['front_pic']['name']);
						$ext = end($ext);
						$new_name1 = rand().time().'.'.$ext;
						$tmp_name = $_FILES["front_pic"]["tmp_name"];
						$path1 = 'assets/verification/'.$new_name1;
						if(move_uploaded_file($tmp_name,$path1)){
							$front_pic = 'assets/verification/'.$new_name1;
						}
					}


					if(isset($_FILES['back_pic']['name']) && !empty($_FILES['back_pic']['name'])){
						$image_arr = $_FILES['back_pic']['name'];
							
						$ext = explode('.',$_FILES['back_pic']['name']);
						$ext = end($ext);
						$new_name2 = rand().time().'.'.$ext;
						$tmp_name = $_FILES["back_pic"]["tmp_name"];
						$path2 = 'assets/verification/'.$new_name2;
						if(move_uploaded_file($tmp_name,$path2)){
							$back_pic = 'assets/verification/'.$new_name2;
						}
					}


					if(isset($_FILES['written_doc']['name']) && !empty($_FILES['written_doc']['name'])){
						$image_arr = $_FILES['written_doc']['name'];
							
						$ext = explode('.',$_FILES['written_doc']['name']);
						$ext = end($ext);
						$new_name3 = rand().time().'.'.$ext;
						$tmp_name = $_FILES["written_doc"]["tmp_name"];
						$path3 = 'assets/verification/'.$new_name3;
						if(move_uploaded_file($tmp_name,$path3)){
							$written_doc = 'assets/verification/'.$new_name3;
						}
					}

					$insert["written_doc"] = $written_doc;
					$insert["user_id"] = $user_id;					
					$insert["front_pic"] = $front_pic;
					$insert["back_pic"] = $back_pic;	
					$insert["created_at"] = date('Y-m-d H:i:s');

					$already = $this->common_model->GetSingleData('verification_users', array('user_id'=>$user_id));
					
					if ($already) {
						$insert["status"] = 0;
					$run = $this->common_model->UpdateData('verification_users', array('id'=>$already["id"]), $insert);	
					} else {
					$run = $this->common_model->InsertData('verification_users', $insert);
					}
					if ($run) {
						$update["verification_icon"] = 1;
						$this->common_model->UpdateData('users', array('id'=>$user_id),$update);
						 $output['status'] = 1;
						 $output['message'] = "Success!";
					} else {
						$output['status'] = 0;
						$output['message'] = "Something went wrong!";
						}
		 }
		 else{
 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";
 	 	}
		
		
		echo json_encode($output);
	}

	public function userReport()
	{
		//https://www.webwiders.in/WEB01/rapid/api/userReport?
			if(isset($_REQUEST['report_by']) && isset($_REQUEST['report_by']) 
				&& isset($_REQUEST['report_to']) && isset($_REQUEST['report_to']) 
				&& isset($_REQUEST['category']) && isset($_REQUEST['category']) 
				&& isset($_REQUEST['message']) && isset($_REQUEST['message']) ) {

				$insert["report_by"] = $_REQUEST['report_by'];
				$insert["report_to"] = $_REQUEST['report_to'];
				$insert["category"] = $_REQUEST['category'];
				$insert["message"] = $_REQUEST['message'];
				$insert["report_on"] = date('Y-m-d H:i:s');

				$run = $this->common_model->InsertData('reports', $insert);
						if ($run) {
						 $output['status'] = 1;
						 $output['message'] = "Success!";
					} else {
						$output['status'] = 0;
						$output['message'] = "Something went wrong!";
						}

				}
			 else{
	 	 		$output['status'] = 0;
				$output['message'] = "Check parameters";
	 	 	}
			
			
			echo json_encode($output);
	}

	public function reporting_category()
	{
		//https://www.webwiders.in/WEB01/rapid/api/reporting_category
		$category = $this->common_model->GetAllData('reporting_category','','id','desc');

		$result = array();
		if ($category) {

			foreach ($category as $key => $value) {
					$result[$key]= $value;
			}
			 			
			 			$output['data'] = $result;
			 			$output['status'] = 1;
						$output['message'] = "Success!";
		} else {
						$output['data'] = $result;
						$output['status'] = 0;
						$output['message'] = "No data!";
		}

		echo json_encode($output);
	}

	public function paymentLink()
	{
		//https://www.webwiders.in/WEB01/rapid/api/paymentLink
		 if(isset($_REQUEST['name']) && !empty($_REQUEST['name']) && isset($_REQUEST['amount']) && !empty($_REQUEST['amount'])) {

		 			$ch = curl_init();

					$name = $_REQUEST['name'];
					$amount = $_REQUEST['amount'];
					$diamond_id = $_REQUEST['diamond_id'];
					$user_id = $_REQUEST['user_id'];
					
				
					$postData['name'] = 'Deposit';
					$postData['description'] = 'For Add diamond in '.Project.' wallet.';
					$postData['pricing_type'] = 'fixed_price';
					$postData['local_price']['amount'] = $_REQUEST['amount'];
					$postData['local_price']['currency'] = currency;
					$postData['metadata']['user_id'] = $user_id;
					$postData['metadata']['amount'] = $amount;
					$postData['metadata']['diamond_id'] = $diamond_id;
					$postData['metadata']['name'] = $_REQUEST['name'];
					$postData['redirect_url'] = site_url().'coinbase/success';
					$postData['cancel_url'] = site_url().'coinbase/cancel';
					
					$data = json_encode($postData); 

					curl_setopt($ch, CURLOPT_URL, 'https://api.commerce.coinbase.com/charges/');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

					curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla Chrome Safari');

					$headers = array();
					$headers[] = 'Content-Type: application/json';
					$headers[] = 'X-Cc-Api-Key: '.coinbase_API;
					$headers[] = 'X-Cc-Version: '.coinbase_Version;
					curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

					$result = curl_exec($ch);
					
					if (curl_errno($ch)) {
						$output['message'] = 'Error:' . curl_error($ch);
						$output['status'] = 0;
						
						echo json_encode($output);
						exit();
					}
					
					curl_close($ch);
					$result = json_decode($result);

					//print_r($result);
					 
					 if(isset($result->data)){
							
							$insert = array();
							
							$data = $this->common_model->GetDataById('diamonds',$diamond_id);
			
							$diamond = $data['diamonds'];
							$free_call = $data['free_call'];
							$amount = $data['amount'];
							
							$insert['user_id'] = $user_id;
							$insert['diamond'] = $diamond;
							$insert['transaction_id'] = Null;
							$insert['payment_method'] = 'Coinbase';
							$insert['free_call'] = $free_call;
							$insert['coinbase_id'] = $result->data->id;
							$insert['coinbase_code'] = $result->data->code;
							$insert['amount'] = $amount;
							$insert['type'] = 1;
							$insert['status'] = 0;
							$insert['date'] = date('Y-m-d H:i:s');
							$insert['message'] = 'Add by Coinbase';
							$run = $this->common_model->InsertData('transaction',$insert);
							
							
							$output['link'] = $result->data->hosted_url;
							$output['data'] = $result;
							$output['last_id'] = $run;
							$output['status'] = 1;
							$output['message'] = "Success !!";
					 } else {
						 $output['message'] = $result->error->message;
							$output['status'] = 0;
					 }

					 

		 	} else {
						$output['status'] = 0;
						$output['message'] = "Check parameters !!";
		}

		echo json_encode($output);
	}

	public function VerifyTradeOtp()
	{
		//https://www.webwiders.in/WEB01/rapid/api/VerifyTradeOtp
		 if(isset($_REQUEST['user_id']) && isset($_REQUEST['user_id']) && isset($_REQUEST['token']) && isset($_REQUEST['token']) && isset($_REQUEST['type']) && isset($_REQUEST['type']) ) {
		 	$user_id = $_REQUEST['user_id'];
		 	$token = $_REQUEST['token'];
		 	$type = $_REQUEST['type'];
		 	$userData = $this->common_model->GetSingleData('tradeOtp', array('user_id' =>$user_id, 'type' => $type,'token' => $token, 'status' => 0),'id','desc');


		 	if ($userData) {


		 		 $output['status'] = 1;
				 $output['message'] = "Success!";
				 $update["status"] = 1;
				 $this->common_model->UpdateData('tradeOtp', array('id'=>$userData["id"]), $update);
		 	} elseif ($userData && $countryData->geoplugin_countryName != $userData["country"]) {
		 		 $output['status'] = 0;
				 $output['message'] = "User country changed!";
		 	} elseif ($userData && $userData["otp_validity"] < date('Y-m-d H:i:s')) {
		 		 $output['status'] = 0;
				 $output['message'] = "OTP expired";
		 	} elseif ($userData && $token != $userData["token"]) {
		 		 $output['status'] = 0;
				 $output['message'] = "Token not matched";
		 	} else {
		 		 $output['status'] = 0;
				 $output['message'] = "No data found";
		 	}
		 
		 } else {

 	 		$output['status'] = 0;
			$output['message'] = "Check parameters";

 	 	}
		echo json_encode($output);
	}

	
}
?>
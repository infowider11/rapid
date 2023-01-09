<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Users<small>management</small></h1>
		<!-- <ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="active"><a href="#">Profile</a></li>
		</ol> -->
    </section>

    <!-- Main content -->
    <section class="content">
		<?php echo $this->session->flashdata('msgs'); ?>
		<div class="error-msg"></div>
		<div class="row">
			<!-- left column -->
			<div class="col-md-12">
				<!-- general form elements -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Total Users List</h3>


			<form class="form_login" method="get">
						    
                <div class="row">
                    
                  	<div class="col-md-2">
                        <div class="form-group">
                        	<label>Start Date</label>
                          <input type="date" value="<?= (isset($_REQUEST['start_date']) && !empty($_REQUEST['start_date'])) ? $_REQUEST['start_date'] : ''; ?>" name="start_date" class="form-control" >
        				</div>
                     </div>

                     <div class="col-md-2">
                        <div class="form-group">
                        	<label>End Date</label>
                         <input type="date" value="<?= (isset($_REQUEST['end_date']) && !empty($_REQUEST['end_date'])) ? $_REQUEST['end_date'] : ''; ?>" name="end_date" class="form-control"  > 
        				</div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group">
                          <label>Country</label>
                          <select class="form-control select2" name="country">
                            <option value="">---Select Country---</option>
<?php
$AllCountry = $this->common_model->GetAllData('country','','nicename','asc');
$AllLanguage = $this->common_model->GetAllData('language','','name','asc');
foreach ($AllCountry as $key => $value) {
?>
                            <option value="<?php echo $value["id"]; ?>" <?php if ($_REQUEST['country'] == $value["id"]) { echo "selected"; } ?> ><?php echo $value["nicename"]; ?></option>
<?php
}
?>                            
                          </select>
                    </div>
                     </div>

                     <div class="col-md-3">
                        <div class="form-group">
                          <label>Keyword</label>
                          <input type="text" name="keyword" class="form-control" placeholder="Keyword" value="<?php echo $_REQUEST['keyword']; ?>" />
                </div>
                     </div>



                     <div class="col-md-3">
                        <div class="form-group">
                        	<br>
                          <button class="btn btn-primary py-3 px-5" style="width:auto; height:auto">Filter</button>
                          <a href="<?php echo site_url().'admin/users'; ?>" class="btn btn-primary py-3 px-5" style="width:auto; height:auto">Reset Filter</a>
        				</div>
                     </div>

                </div> 
                 
            </form>
            <div class="row hide">
                    
                    <div class="col-md-2"><a class="btn btn-danger btn-xs" href="<?php echo site_url().'Admin/Users/export_csv'; ?>" target="_blank">Export CSV</a>
                    </div>
            </div>        

					</div>
					<!-- /.box-header -->
					<!-- form start -->
					<div id="MessageUpdateErr"></div>	
					<div class="table-responsive" style="padding: 20px;">			
					<table class="table table-striped table-bordered DataTable">
						<thead>
						<tr>
							<th>Sl. No.</th>
              <th>Unique ID</th>
							<th>Nick Name</th>
							<th>Profile</th>
							<th>Email</th>
							<th>Phone</th>
							<th>Date of birth</th>
							<th>Level </th>
							<th>Earned point</th>
							<th>Gender</th>
							<th>Country Name</th>
              <th>IP Address</th>
							<th>Status</th>
							<th>Verified</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
<?php
$x=1;
foreach ($userData as $user){
?>

	<?php $country = $this->common_model->GetSingleData('country',array('id'=>$user['country']));?>

						<tr>
							<td><?php echo $x; ?></td>
              <td><?php echo $user["uniqueID"]; ?></td>
							<td><?php echo $user["nickname"]; ?></td>
              <td>
                           <?php
                              if($user['image']!='')
                              {
                              	?>
                           <img style="width: 50px;" src="<?php echo base_url();?>upload/users/<?php echo $user['image'];?>">
                           <?php
                              }else{
                              	?>
                          <img src="<?php echo base_url();  ?>assets/site/default.jpg" height="50" width="50">
                           <?php
                              }
                              ?>
                        </td>
							<td><?php echo $user["email"]; ?></td>
							<td><?php echo $user["phone_with_code"]; ?></td>
							<td><?php if ($user["dob"]) {
               echo date('d-m-Y', strtotime($user["dob"]));
              }  ?></td>
							<td><?php echo $user["level"]; ?></td>
							<td><?php echo $user["coin"]; ?></td>
							<td><?php echo $user["gender"]; ?></td>
							<td><?php echo $country["nicename"]; ?> <?php if ($user['country']) {

              if(file_exists('assets/flags/'.strtolower($country['iso']).'.png')){
                  $flags = site_url().'assets/flags/'.strtolower($country['iso']).'.png';
                      }
                  }     
            ?>
            <img src="<?php echo $flags ?>"></td>
						<td><?php echo $user["ip"]; ?></td>						
													
<td>
<?php
if ($user["status"] ==1) {
	echo "<span class='btn btn-xs btn-success'>Active<span>";

} else { echo "<span class='btn btn-xs btn-danger'>Blocked<span>"; }
 ?></td>
<td>
<?php
if ($user["is_verified"] ==1) {
	echo "<span class='btn btn-xs btn-success'>Verified<span>";

} else { echo "<span class='btn btn-xs btn-danger'>Un-verified<span>"; }
?></td>	
<!-- <td>
<a class="btn <?php if($user['status'] == '1'){echo "btn-danger"; } else { echo "btn-success"; } ?> btn-xs" href="<?php echo base_url();?>Admin/Users/statusUpdate/?UserId=<?php echo $user['id']; ?>&Status=<?php echo $user['status']; ?>" >
    <?php if($user['status'] == '0'){echo "Unblock"; } else { echo "Blocked"; } ?>
</a> 								
</td> -->
<td>
  <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#EditProfile<?php echo $user['id'] ; ?>"><i class="fa fa-edit"></i></button>

   <?php if($user['status'] == 1){ ?> 	
   	<a class="btn btn-danger btn-xs" href="javascript:void(0)" onclick="change_status(<?php echo $user['id']; ?>,0)">Block</a> 
   <?php }else{ ?>
   	<a class="btn btn-success btn-xs" href="javascript:void(0)" onclick="change_status(<?php echo $user['id']; ?>,1)">Unblock</a> 
   <?php } ?>	
   <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal<?php echo $user['id']; ?>">Change Agent</button>
     <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#myModal1<?php echo $user['id']; ?>">Change Password</button>

     <?php
     $UserVefi = $this->common_model->GetSingleData('verification_users', array('user_id'=>$user["id"]));
if ($user["verification_icon"]=="1" && $UserVefi) {
?>
<a class="btn btn-success btn-xs" onclick="return confirm('Are you sure?')" href="<?php echo base_url();?>Admin/RatingCategory/VerificationAccept?id=<?php echo $UserVefi["id"];?>&userId=<?php echo $user["id"]; ?>&page=users" >Accepted Verification</a>

<a class="btn btn-danger btn-xs" onclick="return confirm('Are you sure?')" href="<?php echo base_url();?>Admin/RatingCategory/VerificationReject?id=<?php echo $UserVefi["id"];?>&userId=<?php echo $user["id"]; ?>&page=users" >Reject Verification</a>

<?php
} ?>
<button style="background-color: cadetblue;" class="btn btn-xs btn-success"  data-toggle="modal" data-target="#editComment<?php echo $user['id'] ; ?>" >View Diamond</button>
<button style="background-color: cadetblue;" class="btn btn-xs btn-success"  data-toggle="modal" data-target="#editComment1<?php echo $user['id'] ; ?>" >View Badges</button>
<button style="background-color: cadetblue;" class="btn btn-xs btn-success"  data-toggle="modal" data-target="#guardianModal<?php echo $user['id'] ; ?>" >View Guardian</button>
<a class="btn btn-danger btn-xs" href="<?php echo base_url();?>admin/transaction-history?uniqueID=<?= $user["uniqueID"]; ?>" >Transaction History</a>
</td>

						</tr>
	<div class="modal" id="EditProfile<?php echo $user["id"];?>">
  <div class="modal-dialog ">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Edit Profile</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

  <form method="post" onsubmit="return Edit('<?php echo $user["id"]; ?>');" id="Edit<?php echo $user["id"]; ?>" enctype="multipart/form-data" >    
          <!-- Modal body -->
      <div class="modal-body">
      <div id="EditErr<?php echo $user["id"]; ?>" style="color: red; text-align: center;"></div>  

        <div class="form-group">
            <label >Nick Name :</label>
            <input type="text" class="form-control" placeholder="Nick Name" name="nickname" value="<?php echo $user["nickname"]; ?>" />
        </div>

        <div class="form-group">
            <label >Unique ID :</label>
            <input type="text" class="form-control" placeholder="Unique Id" name="uniqueID" value="<?php echo $user["uniqueID"]; ?>" required/>
        </div>


         <div class="form-group">
            <label >Email :</label>
            <input type="email" class="form-control" placeholder="Email" name="email" value="<?php echo $user["email"]; ?>" />
          </div>
           
          <div class="form-group">
            <label >Date of birth :</label>
            <input type="date" class="form-control" placeholder="Date of birth" name="dob" value="<?php echo $user["dob"]; ?>" max="<?php echo date('Y-m-d'); ?>" />
          </div> 
          <div class="form-group">
            <label >Gender :</label>
            <select class="form-control" name="gender" >
              <option value="">---Select Gender---</option>
              <option value="male" <?php if ($user["gender"]=="male") { echo "selected"; } ?> >Male</option>
              <option value="female" <?php if ($user["gender"]=="female") { echo "selected"; } ?> >Female</option>
            </select>
          </div>
          <div class="form-group">
            <div class="row">
              <div class="col-md-6">
                            <label >Country :</label>
            <select class="form-control select2" name="country">
              <option value="">---Select Country---</option>
<?php 

foreach ($AllCountry as $countryEdit){
?>
          <option value="<?php echo $countryEdit["id"]; ?>" <?php if ($countryEdit["id"] == $user["country"]) {echo "selected"; } ?> ><?php echo $countryEdit["nicename"]; ?> (<?php echo $countryEdit["phonecode"]; ?>)</option>
<?php
}

?>                            
            </select>
              </div>
              <div class="col-md-6">
             <div class="form-group">
            <label>Phone :</label>
            <input type="text" class="form-control" placeholder="Phone" name="phone" value="<?php echo $user["phone"]; ?>"/>
          </div>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label >City :</label>
            <input type="text" class="form-control" placeholder="City" name="city" value="<?php echo $user["city"]; ?>"/>
          </div>

          <div class="form-group">
            <label >Language 1 :</label>
            <select class="form-control select2" name="lng1">
              <option value="">---Select Language 1---</option>
<?php
foreach ($AllLanguage as $key => $lng1) {
            ?>
              <option value="<?php echo $lng1["id"]; ?>" <?php if ($lng1["id"] == $user["lng1"]) {echo "selected"; } ?> ><?php echo $lng1["name"]; ?></option>
            <?php
}
?>
            </select>
          </div>

          <div class="form-group">
            <label >Language 2 :</label>
            <select class="form-control select2" name="lng2">
              <option value="">---Select Language 2---</option>
<?php
foreach ($AllLanguage as $key => $lng1) {
            ?>
              <option value="<?php echo $lng1["id"]; ?>" <?php if ($lng1["id"] == $user["lng2"]) {echo "selected"; } ?> ><?php echo $lng1["name"]; ?></option>
            <?php
}
?>
            </select>
          </div> 
          <div class="form-group">
            <label>Guardian Diamond</label>
            <input class="form-control" type="number" name="guardian_price" min="1" value="<?php echo $user["guardian_price"]; ?>" placeholder="Guardian price">
          </div>
         
        <div class="form-group">
            <label >Image:</label>
            <span style="color:red; font-size:10px">Only .jpeg, .jpg, .png, .gif formats are allowed.</span>
            <input type="file" name="image" accept="image/*" class="form-control" id="imgInp<?php echo $user["id"]; ?>"/>
          </div> 
          <div class="form-group">
          <input type="hidden" name="User_id" value="<?php echo $user["id"]; ?>" />
          <?php
if ($user["image"] !="") {
  ?>
  <img id="blah<?php echo $user["id"]; ?>" src="<?php echo base_url(); ?>upload/users/<?php echo $user['image']; ?>" height="100px" width="100px"/><?php
      }
      else
      { ?>
  <img id="blah<?php echo $user["id"]; ?>" src="<?php echo base_url(); ?>assets/site/default.jpg" height="100px" width="100px"/>      
<?php       
      }
?>
          </div>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="EditBtn">Update<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw btn-loadEdit"></i></button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
          </form>
    </div>
  </div>
</div>  					
						
						<!-- Post cmment Modal -->
<div id="editComment<?php echo $user['id'] ; ?>" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add user diamond</h4>
      </div>

   	<div class="alert alert-success">
   		Current Diamond Balance : <?php echo $user['diamond']; ?></div>
<form method="post" onsubmit="return AddWallet('<?php echo $user['id'] ; ?>');" id="AddWallet<?php echo $user['id'] ; ?>"> 
      <div class="modal-body">
			<div id="MessageWalletErr<?php echo $user['id'] ; ?>"></div>
           <div class="form-group">
						<label class="form-label">Select Type</label>
       			<select class="form-control" name="type" required="">
       				<option value="">----Select type----</option>	
       				<option value="1">Add</option>
              <option value="2">Remove</option>
       			</select>
       		</div> 
       		<input type="hidden" name="UserId" value="<?php echo $user['id'] ; ?>" />
           <div class="form-group">
						<label class="form-label">Enter Diamond</label>
       			<input class="form-control" type="number" name="amount" min="1" placeholder="Enter diamond" required="">
       		</div> 
   </div>                                 

      <div class="modal-footer">
      	<button type="submit" class="btn btn-dark" id="WalletBtn<?php echo $user['id'] ; ?>">Update<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw" id="btn-load<?php echo $user['id'] ; ?>"></i></button>
        <button type="button" class="btn btn-default edit-btn" data-dismiss="modal">Close</button>
      </div>
</form> 
    </div>
 </div>
  </div>




  <div id="editComment1<?php echo $user['id'] ; ?>" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add Badges to user</h4>
      </div>

    <div class="alert alert-warning">
      Current Badges  :    <?php
$parts = explode(",",$user['badges']);
foreach ($parts as $key => $valueBatch) {
$ShowBatch = $this->common_model->GetSingleData('badges', array('id'=>$valueBatch));
if ($ShowBatch) {
  echo $ShowBatch["title"].", ";
} else {
  echo "No batch";
  }

}
?></div>
<form method="post" onsubmit="return AddBatch('<?php echo $user['id'] ; ?>');" id="AddBatch<?php echo $user['id'] ; ?>"> 
      <div class="modal-body">
      <div id="MessageBatchErr<?php echo $user['id'] ; ?>"></div>
           <div class="form-group">
            <label class="form-label">Select Batch</label>
            <select class="form-control multi_select questions_id" name="batches[]" multiple="multiple" required="">
<?php
$AllBatches = $this->common_model->GetAllData('badges','','id','desc');
foreach ($AllBatches as $batch){
?>
            <option value="<?php echo $batch['id'];?>" <?php if (strpos($user['badges'], $batch['id']) !== false) {echo "selected"; } ?> ><?php echo $batch['title'];?></option>
<?php
}
?>
            </select>
          </div> 
          <input type="hidden" name="UserId" value="<?php echo $user['id'] ; ?>" />
   </div>                                 

      <div class="modal-footer">
        <button type="submit" class="btn btn-dark" id="AddBatch1<?php echo $user['id'] ; ?>">Update<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw" id="btn-loadBatch<?php echo $user['id'] ; ?>"></i></button>
        <button type="button" class="btn btn-default edit-btn" data-dismiss="modal">Close</button>
      </div>
</form> 
    </div>
 </div>
  </div>


  <div id="guardianModal<?php echo $user['id'] ; ?>" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Current Guardian View</h4>
      </div>

      <div class="modal-body">
            <div class="row">
              <div class="col-md-1">Sl. No.</div>
              <div class="col-md-3">Name of Guardian</div>
              <div class="col-md-2">Diamonds</div>
              <div class="col-md-2">Start Date</div>
              <div class="col-md-2">End Date</div>
              <div class="col-md-2">Status</div>
            </div>
<?php
$guardian = $this->common_model->GetSingleData('guardian', array('secondry_user'=>$user['id'], 'DATE(end_date) >= '=> date('Y-m-d'), 'status' => 1),'id','desc');
if ($guardian) {

$primary = $this->common_model->GetSingleData('users',array('id'=>$guardian["primary_user"]));
?>
              <div class="row">
              <div class="col-md-1"><?php echo 1; ?></div>
              <div class="col-md-3"><a href="<?php echo base_url(); ?>admin/users?UserId=<?php echo $guardian["primary_user"]; ?>" ><?php echo $primary["nickname"]; ?></a>                <br><?php echo $primary["uniqueID"]; ?></div>
              <div class="col-md-2"><?php echo $guardian["diamond"]; ?></div>
              <div class="col-md-2"><?php echo $guardian["start_date"]; ?></div>
              <div class="col-md-2"><?php echo $guardian["end_date"]; ?></div>
              <div class="col-md-2"><?php
                    if (date('Y-m-d') > $guardian['end_date']) {
                    echo "<span class='btn btn-xs btn-danger'>Expired<span>";
                                              }
                    elseif ($guardian['status'] == 1) {
                    echo "<span class='btn btn-xs btn-success'>Active<span>";
                    } else {
                    echo "<span class='btn btn-xs btn-danger'>Expired<span>";
                    }
            ?></div>
              </div>
<?php

} else {
  ?>
              <div class="row">
              <div class="col-md-12" style="text-align: center;">No data found</div>
            </div>

<?php
}

?>            
      </div>                                 

      <div class="modal-footer">
        <button type="button" class="btn btn-default edit-btn" data-dismiss="modal">Close</button>
      </div>
    </div>
 </div>
  </div>
  
 <div class="modal" id="myModal<?php echo $user['id']; ?>">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Change Agent</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
<form method="post" action="<?php echo base_url(); ?>Admin/Users/EditAgent" enctype="multipart/form-data">      
      <div class="modal-body">
      	<div id="MessageErr<?php echo $user["id"]; ?>"></div>
        <div class="form-group">
        		<label>Agent</label>
        		<select class="form-control select2" name="invited_by" required="">
        		
<?php
$Agents = $this->common_model->GetAllData('users', array('user_type' => 2));
foreach ($Agents as $key => $AgentValue) {
?>      			
        			<option value="<?php echo $AgentValue["uniqueID"]; ?>" <?php if ($AgentValue["uniqueID"] == $user['invited_by']) { echo "selected"; } ?> ><?php echo $AgentValue["nickname"].' (ID : '.$AgentValue["uniqueID"].')'; ?></option>
<?php } ?>        			
        		</select>
        </div>
        <input type="hidden" name="UserId" value="<?php echo $user["id"]; ?>" />
        <div class="form-group">
        	<label>Reason</label>
        	<textarea class="form-control ckeditor" required="" placeholder="Enter reason" name="reason"></textarea>	
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
      	<button type="submit" class="btn btn-primary" id="AgentBtn<?php echo $user["id"]; ?>">Update<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw" id="btn-loadEdit<?php echo $user["id"]; ?>"></i></button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
</form>
    </div>
  </div>
</div>

<div class="modal" id="myModal1<?php echo $user['id']; ?>">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Change Password</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
<form method="post" onsubmit="return ChangePassword('<?php echo $user["id"]; ?>');" id="ChangePassword<?php echo $user["id"]; ?>">      
      <div class="modal-body">
        <div id="MessageErr1<?php echo $user["id"]; ?>"></div>
        <div class="form-group">
            <label>New password</label>
            <input type="password" name="password" class="form-control" placeholder="password" />
        </div>
        <input type="hidden" name="UserId" value="<?php echo $user["id"]; ?>" />
        <div class="form-group">
            <label>Confirm password</label>
            <input type="password" name="cpassword" class="form-control" placeholder="confirm password" />
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id="ChangeBtn<?php echo $user["id"]; ?>">Change<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw" id="btn-ChangeBtn<?php echo $user["id"]; ?>"></i></button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
</form>
    </div>
  </div>
</div>
<script>
    $("#imgInp<?php echo $user["id"]; ?>").change(function() {

              var fileExtension = ['jpeg', 'jpg', 'png', 'gif'];
                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                    // alert("Only '.jpeg','.jpg' formats are allowed.");
                    alert("Only .jpeg, .jpg, .png, .gif formats are allowed.");
                    $('#imgInp<?php echo $user["id"]; ?>').val("");
                    $('#blah<?php echo $user["id"]; ?>').css("display", "none");
                    $('#UpdateImgMsg<?php echo $user["id"]; ?>').html('');

                }
                else {
                  readURL(this,'blah<?php echo $user["id"]; ?>', 'UpdateImgMsg<?php echo $user["id"]; ?>');
                }
  
});
</script>
<?php
$x++;
}
?>					</tbody>	
					</table>
				</div>
				</div>
			</div>

		</div>
    </section>
</div>
 
<?php include_once('include/footer.php'); ?>

<script>
  $(document).ready(function() {  
  CKEDITOR.replaceClass = 'ckeditor';    
});


   function change_status(id,status)
   {
   	if(confirm('Are you sure?'))
   	{
		$.ajax({
	        type: "POST",
	        url: "<?php echo site_url('Admin/Users/change_status'); ?>",
	        data: {id:id,status:status},
	        dataType: "json",
	        success: function(data){
	        	if(data.status == 1)
	        	{
	              location.reload();
	        	}
	        	else
	        	{
	        		$('.error-msg').html(data.message);
	        	}
	            
	        },
	        
	   });
   	}

   }
function AddWallet(FormId) {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Users/AddWallet",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#AddWallet'+FormId)[0]),
    dataType:'json',
    beforeSend:function(){
      $('#WalletBtn'+FormId).prop('disabled',true);
      $('#btn-load'+FormId).show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageWalletErr"+FormId).html(data.message);
        $('#WalletBtn'+FormId).prop('disabled',false);
        $('#btn-load'+FormId).hide();
      }
    }
  });
    return false;

}  


function EditAgent(FormId) {

 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Users/EditAgent",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#EditAgent'+FormId)[0]),
    dataType:'json',
    beforeSend:function(){
      $('#AgentBtn'+FormId).prop('disabled',true);
      $('#btn-loadEdit'+FormId).show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageErr"+FormId).html(data.message);
        $('#AgentBtn'+FormId).prop('disabled',false);
        $('#btn-loadEdit'+FormId).hide();
      }
    }
  });
    return false;

}

function ChangePassword(FormId) {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Users/ChangePassword",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#ChangePassword'+FormId)[0]),
    dataType:'json',
    beforeSend:function(){
      $('#ChangeBtn'+FormId).prop('disabled',true);
      $('#btn-ChangeBtn'+FormId).show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageErr1"+FormId).html(data.message);
        $('#ChangeBtn'+FormId).prop('disabled',false);
        $('#btn-ChangeBtn'+FormId).hide();
      }
    }
  });
    return false;

}
function readURL(input,imgid='',msgid='') {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      $('#'+imgid).attr('src', e.target.result);
      $('#'+imgid).css("display", "block");
      $('#'+msgid).html('<p>Live preview</p>');
    }
    reader.readAsDataURL(input.files[0]); // convert to base64 string
  }
}
$("#imgInp")[0].change(function() {
  readURL(this);
});

function Edit(FormId) {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Users/EditUser",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#Edit'+FormId)[0]),
    dataType:'json',
    beforeSend:function(){
      $('#EditBtn').prop('disabled',true);
      $('.btn-loadEdit').show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#EditErr"+FormId).html(data.message);
        $('#EditBtn').prop('disabled',false);
        $('.btn-loadEdit').hide();
      }
    }
  });
    return false;

}

function AddBatch(FormId) {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Batch/AddBatchUser",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#AddBatch'+FormId)[0]),
    dataType:'json',
    beforeSend:function(){
      $('#AddBatch1'+FormId).prop('disabled',true);
      $('#btn-loadBatch'+FormId).show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageBatchErr"+FormId).html(data.message);
        $('#AddBatch1'+FormId).prop('disabled',false);
        $('#btn-loadBatch'+FormId).hide();
      }
    }
  });
    return false;

}
</script>

<script src="https://cdn.ckeditor.com/4.15.1/standard/ckeditor.js"></script>  
<script type="text/javascript">
            $(function() {
               CKEDITOR.replace('ckeditor');
            });
        </script>
<script>

my_fun1();
function my_fun1(){
  $('.questions_id').multiselect({
    numberDisplayed: 1,
    includeSelectAllOption: true,
    //maximumSelectionLength:5,
    search: true,
    allSelectedText: 'All user selected',
    nonSelectedText: 'No user selected',
    selectAllValue: 'All',
    selectAllText: 'Select All',
    unselectAllText: 'Unselect All',
    onSelectAll: function(checked) {
      var all = $('.questions_id ~ .btn-group .dropdown-menu .multiselect-all .checkbox');
      all
      // get all child nodes including text and comment
        .contents()
        // iterate and filter out elements
        .filter(function() {
          // check node is text and non-empty
          return this.nodeType === 3 && this.textContent.trim().length;
          // replace it with new text
        }).replaceWith(checked ? this.unselectAllText : this.selectAllText);
    },
    onChange: function() {
        //debugger;
      var select = $(this.$select[0]);
      var dropdown = $(this.$ul[0]);
      var options = select.find('option').length;
      var selected = select.find('option:selected').length;
      var all = dropdown.find('.multiselect-all .checkbox');
      all
      // get all child nodes including text and comment
        .contents()
        // iterate and filter out elements
        .filter(function() {
          // check node is text and non-empty
          return this.nodeType === 3 && this.textContent.trim().length;
          // replace it with new text
        }).replaceWith(options === selected ? this.options.unselectAllText : this.options.selectAllText);
    }
  });
  $('#multiselect').multiselect();
  $('.questions_id').multiselect({
      includeSelectAllOption: true,
      enableFiltering: true,
      enableCaseInsensitiveFiltering: true,
      filterPlaceholder: 'Search for something...'
  });  
  
}
</script>
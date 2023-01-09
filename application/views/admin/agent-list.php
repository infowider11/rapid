<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1><?php echo $content; ?><small>management</small></h1>
		
    </section>

    <!-- Main content -->
    <section class="content">
		<div class="error-msg"></div>
		<div class="row">
			<!-- left column -->
			<div class="col-md-12">
				<!-- general form elements -->
				<?php echo $this->session->flashdata('msgs'); ?>
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title"><?php echo $content; ?> List</h3>
						<div style="float: right;">
							<?php if ($content == "Agent") {

							 ?>
							<a href="javascript:void(0)" class="btn btn-success" data-toggle="modal" data-target="#myModal">Add New</a>
						<?php } ?>
						<a class="btn btn-danger hide" href="<?php echo site_url().'Admin/Agent/export_csv'; ?>" target="_blank">Export CSV</a>
						</div>
					</div>
						
					<div class="table-responsive" style="padding: 20px;">			
					<table class="table table-striped table-bordered DataTable">
						<thead>
						<tr>
							<th>S. No.</th>
							<th>Unique Id</th>
							<th>Nick Name</th>
							<th>Email</th>
							<th>Password</th>
							<th>Phone</th>
							<th>Date of birth</th>
							<th>Gender</th>
							<th>Country</th>
							<th>City</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
                       <?php
$x = 1;                       
                        foreach($data as $Users){ ?>
                       	 <tr>
                       	 	<td><?php echo $x; ?></td>
                       	 	<td><?php echo $Users["uniqueID"]; ?></td>
                       	 	<td><?php echo $Users["nickname"]; ?></td>
                       	 	<td><?php echo $Users["email"]; ?></td>
                       	 	<td><?php echo $Users["password"]; ?></td>
                       	 	<td><?php echo $Users["phone"]; ?></td>
                       	 	<td><?php echo $Users["dob"]; ?></td>
                       	 	<td><?php echo $Users["gender"]; ?></td>
                       	 	<td><?php
$countryName = $this->common_model->GetSingleData('country', array('id' => $Users["country"]));
echo $countryName["nicename"];
                       	 	?></td>
                       	 	<td><?php echo $Users["city"]; ?></td>
                       	 	<td>
<?php
if ($Users["isAgentBlock"] ==1) {
	echo "<span class='btn btn-xs btn-success'>Unblock<span>";

} else { echo "<span class='btn btn-xs btn-danger'>Blocked<span>"; }
 ?>                       	 		
                       	 	</td>
                       	 	<td>
<?php if ($content == "Agent") { ?>                       	 		
                       	 		<!-- data-toggle="modal" data-target="#myModal" -->
                       	 	<a    data-toggle="modal" data-target="#editmyModal<?php echo $Users['id'];?>" class="btn btn-xs btn-success">Edit</a>

                    <a onclick="return  confirm('Are you sure want to delete this Users?');" href="<?php echo base_url();?>Admin/Agent/delete_Users?UserId=<?php echo $Users['id'];?>"  class="btn btn-xs btn-danger">Delete</a>
<?php } ?>
										<?php if($Users['isAgentBlock'] == 1){ ?> 
										<button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal<?php echo $Users['id'];?>">Block</button>	
								   <!--	<a class="btn btn-danger btn-xs" href="javascript:void(0)" onclick="change_status(<?php echo $Users['uniqueID']; ?>,0)">Block</a>--> 
								   <?php } ?>
                       	 	</td>
                       	 </tr>
                       	 
                       	 
                       	 	<!-- Modal -->
  <div class="modal fade" id="editmyModal<?php echo $Users['id'];?>" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Edit Agent</h4>
        </div>
	<div id="MessageUpdateErr<?php echo $Users['id']; ?>"></div>
       <form method="post" onsubmit="return Edit('<?php echo $Users['id']; ?>');" id="Edit<?php echo $Users['id']; ?>" autocomplete="off">		

			<input type="hidden" name="UserId" value="<?php echo $Users['id'];?>">

	        <div class="modal-body">

			  <div class="form-group">
			    <label for="">Nick Name</label>
			    <input type="text" class="form-control" name="nickname" value="<?php echo $Users["nickname"]; ?>" required placeholder="Enter nick name" />
			  </div>

			  <div class="form-group">
			    <label for="">Email</label>
			    <input type="email" class="form-control" name="email" value="<?php echo $Users["email"]; ?>" required placeholder="Enter email" />
			  </div>

			  <div class="form-group">
			    <label for="">Phone</label>
			    <input type="text" class="form-control" name="phone" value="<?php echo $Users["phone"]; ?>" required placeholder="Enter phone" />
			  </div>

			  <div class="form-group">
			    <label for="">Date of birth</label>
			    <input type="date" class="form-control" name="dob" value="<?php echo $Users["dob"]; ?>" required max="<?php echo date('Y-m-d'); ?>"/>
			  </div>

	        <div class="form-group">
			    <label for="">Gender</label>
			    <select class="form-control" name="gender" required>
			    	<option value="">---Select gender---</option>
			    	<option value="male" <?php if ($Users["gender"] == "male") { echo "selected"; } ?>>Male</option>
			    	<option value="female" <?php if ($Users["gender"] == "female") { echo "selected"; } ?> >Female</option>
			    </select>
			  </div>

			  <div class="form-group">
			    <label for="">Country</label>
			    <select class="form-control" name="country" required>
			    	<option value="">---Select country---</option>
<?php
foreach ($countryList as $country) {
	?>
					<option value="<?php echo $country["id"]; ?>" <?php if ($country["id"] == $Users["country"]) { echo "selected"; } ?> ><?php echo $country["nicename"];?></option>
	<?php
}
?>			    	

			    </select>
			  </div>

			  <div class="form-group">
			    <label for="">City</label>
			    <input type="text" class="form-control" name="city" value="<?php echo $Users["city"]; ?>" required placeholder="Enter city" />
			  </div>

	        </div>

	        <div class="modal-footer">
	          <button type="submit" class="btn btn-info" id="EditBtn<?php echo $Users['id'];?>">Update <i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw btn-loadEdit<?php echo $Users['id'];?>"></i></button>	
	          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
	        </div>
        </form>
      </div>
      
    </div>
  </div> 
  
  <div class="modal" id="myModal<?php echo $Users['id'];?>">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Block User</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
<form method="post" action="<?php echo base_url(); ?>Admin/Agent/BlockUser" enctype="multipart/form-data" >       
      <div class="modal-body">
       <div class="form-group">
       	<label>Reason</label>
       	<textarea class="form-control ckeditor" name="reason" placeholder="Reason for block"></textarea>
       	<input type="hidden" name="uniqueID" value="<?php echo $Users['uniqueID']; ?>">
       </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
      	<button type="submit" class="btn btn-primary" id="BlockBtn<?php echo $Users['id']; ?>">Block<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw" id="Block-load<?php echo $Users['id']; ?>" ></i></button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
</form>
    </div>
  </div>
</div>
                       <?php $x++; } ?>
                     </tbody>	

					</table>
				</div>
				</div>
			</div>

		</div>
    </section>
</div>
 
 <?php include_once('include/footer.php'); ?>
 
 <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add Agent</h4>
        </div>
        <div id="MessageErr"></div>
       <form method="post" onsubmit="return Add();" id="Add" autocomplete="off">		

	        <div class="modal-body">

			   <div class="form-group">
			    <label for="">Nick Name</label>
			    <input type="text" class="form-control" name="nickname" required placeholder="Enter nick name" />
			  </div>

			  <div class="form-group">
			    <label for="">Email</label>
			    <input type="email" class="form-control" name="email" required placeholder="Enter email" />
			  </div>

			  <div class="form-group">
			    <label for="">Phone</label>
			    <input type="text" class="form-control" name="phone" required placeholder="Enter phone" />
			  </div>

			  <div class="form-group">
			    <label for="">Date of birth</label>
			    <input type="date" class="form-control" name="dob" required max="<?php echo date('Y-m-d'); ?>"/>
			  </div>

	        <div class="form-group">
			    <label for="">Gender</label>
			    <select class="form-control" name="gender" required>
			    	<option value="">---Select gender---</option>
			    	<option value="male">Male</option>
			    	<option value="female">Female</option>
			    </select>
			  </div>

			  <div class="form-group">
			    <label for="">Country</label>
			    <select class="form-control" name="country" required>
			    	<option value="">---Select country---</option>
<?php
foreach ($countryList as $country) {
	?>
					<option value="<?php echo $country["id"]; ?>"><?php echo $country["nicename"];?></option>
	<?php
}
?>			    	

			    </select>
			  </div>

			  <div class="form-group">
			    <label for="">City</label>
			    <input type="text" class="form-control" name="city" required placeholder="Enter city" />
			  </div>
			  <div class="form-group">
			    <label for="">Password</label>
			    <input type="password" class="form-control" name="password" required placeholder="Enter password" />
			  </div>
			  <div class="form-group">
			    <label for="">Confirm Password</label>
			    <input type="password" class="form-control" name="cpass" required placeholder="Confirm password" />
			  </div>

	        </div>

	        <div class="modal-footer">
	          <button type="submit" class="btn btn-info" id="AddBtn">Submit<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw btn-load"></i>
	          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        </div>
        </form>
      </div>
      
    </div>
  </div> 
<script>
	function BlockUser(FormId) {

 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Agent/BlockUser",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#BlockUser'+FormId)[0]),
    dataType:'json',
    beforeSend:function(){
      $('#BlockBtn'+FormId).prop('disabled',true);
      $('#Block-load'+FormId).show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageUpdateErr"+FormId).html(data.message);
        $('#BlockBtn'+FormId).prop('disabled',false);
        $('#Block-load'+FormId).hide();
      }
    }
  });
    return false;

}
 function Add() {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Agent/AddAgent",
    type:"POST",
    cache:false,
        contentType: false,
        processData: false,
    data:new FormData($('#Add')[0]),
    dataType:'json',
    beforeSend:function(){
      $('#AddBtn').prop('disabled',true);
      $('.btn-load').show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageErr").html(data.message);
        $('#AddBtn').prop('disabled',false);
        $('.btn-load').hide();
      }
    }
  });
    return false;

}

 function Edit(FormId) {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Agent/EditAgent",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#Edit'+FormId)[0]),
    dataType:'json',
    beforeSend:function(){
      $('#EditBtn'+FormId).prop('disabled',true);
      $('.btn-loadEdit'+FormId).show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageUpdateErr"+FormId).html(data.message);
        $('#EditBtn'+FormId).prop('disabled',false);
        $('.btn-loadEdit'+FormId).hide();
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

<?php include ('include/header.php'); ?>
 <div id="page-wrapper" class="container">
	<div class="row">
	   <div id="nav-col">
		  <?php include ('include/sidebar.php'); ?>
	   </div>
	   <div id="content-wrapper">
		  <div class="row">
			 <div class="col-lg-12">
				<div class="row">
				   <div class="col-lg-12">
					  <ol class="breadcrumb">
						 <li><a href="#">Home</a></li>
						 <li class="active"><span>Profile</span></li>
					  </ol>
				   </div>
				</div>
				
				
				
				<div class="main-box clearfix">
				<div class="row">
					<?php echo $this->session->flashdata('msg'); ?>	
				   <div class="col-lg-6">
					  <div class="main-box">
						 <header class="main-box-header clearfix">
							<h2>Edit profile</h2>
						 </header>
						 <div class="main-box-body clearfix">
<div id="MessageErr"></div>						 	
<?php
?>						 	
		<form method="post" onsubmit="return Edit();" id="Edit" autocomplete="off">		

			   <div class="form-group">
			    <label for="">Nick Name</label>
			    <input type="text" class="form-control" name="nickname" required placeholder="Enter nick name" value="<?php echo $data["nickname"]; ?>" />
			  </div>

			  <div class="form-group">
			    <label for="">Email</label>
			    <input type="email" class="form-control" name="email" required placeholder="Enter email" value="<?php echo $data["email"]; ?>" readonly="" />
			  </div>

			  <div class="form-group">
			    <label for="">Phone</label>
			    <input type="text" class="form-control" name="phone" required placeholder="Enter phone" readonly="" value="<?php echo $data["phone"]; ?>" />
			  </div>

			  <div class="form-group">
			    <label for="">Date of birth</label>
			    <input type="date" class="form-control" name="dob" required value="<?php echo $data["dob"]; ?>" />
			  </div>

	        <div class="form-group">
			    <label for="">Gender</label>
			    <select class="form-control" name="gender" required>
			    	<option value="">---Select gender---</option>
			    	<option value="male" <?php if ($data["gender"] == "male") { echo "selected"; } ?> >Male</option>
			    	<option value="female" <?php if ($data["gender"] == "female") { echo "selected"; } ?> >Female</option>
			    </select>
			  </div>

			  <div class="form-group">
			    <label for="">Country</label>
			    <select class="form-control" name="country" required>
			    	<option value="">---Select country---</option>
<?php
foreach ($countryList as $country) {
	?>
					<option value="<?php echo $country["id"]; ?>" <?php if ($country["id"] == $data["country"]) { echo "selected"; } ?> ><?php echo $country["nicename"];?></option>
	<?php
}
?>			    	

			    </select>
			  </div>

			  <div class="form-group">
			    <label for="">City</label>
			    <input type="text" class="form-control" name="city" required placeholder="Enter city" value="<?php echo $data["city"]; ?>" />
			  </div>

			  <div class="form-group">
			    <label for="">Introduction</label>
			    <textarea class="form-control" name="introduction" required placeholder="Enter introduction" ><?php echo $data["introduction"]; ?></textarea>
			  </div>
			  <div class="form-group">
			    <label for="">Image</label><br>
<?php
if ($data["image"] !="") {
  ?>
  <img src="<?php echo base_url(); ?>assets/admin/agentImg/<?php echo $data['image']; ?>" height="100px" width="100px" id="output" /><?php
      }
      else
      { ?>
  <img src="<?php echo base_url(); ?>assets/admin/agentImg/Dummy_image.png" height="100px" width="100px" id="output"/>      
<?php       
      }
?>

			    <input type="file" class="form-control" name="image" accept="image/*" id="fileUpload">
			  </div>
			  <div class="form-group">
			  	<button type="submit" class="btn btn-primary" id="AddBtn">Update<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw btn-load"></i></button>
			  </div>
			</form>
			
						 </div>
					  </div>
				   </div> 
				    <div class="col-lg-6">
					  <div class="main-box">
						 <header class="main-box-header clearfix">
							<h2>Change password</h2>
						 </header>
						 <div class="main-box-body clearfix">
					 	
						 	<form role="form" id="change_password_form" method="post" action="<?php echo base_url().'agent/User/change_password'; ?>">
						<div class="box-body">
							<div id="error_pass"></div>
							<div class="form-group">
								<label class=" form-control-label">Current Password</label>
								<input type="password" name="admin_password" id="Current_Password" class="form-control" required placeholder="Current password">
							</div>
							<div class="form-group">
								<label class=" form-control-label">New Password</label>
								<input type="password" name="New_Password" id="New_Password" class="form-control" placeholder="New password" required>
							</div>
							<div class="form-group">
								<label class=" form-control-label">Confirm Password</label>
								<input type="password" name="Confirm_Password" id="Confirm_Password" class="form-control" placeholder="Confirm password" required>
							</div>
						</div>

						<div class="box-footer edit-password-btn">
							<input type="submit"  class="btn btn-success">
						</div>
					</form>
						 </div>
						</div>  
					</div>	 
				</div>

				
				
				</div>
			 </div>
		  </div>
	   </div>
	</div>
 </div>
</div>
<?php include ('include/footer.php') ?>
<script>
	$('#fileUpload').on("change",function () {

                var fileExtension = ['jpeg', 'jpg', 'png', 'gif'];
                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                    // alert("Only '.jpeg','.jpg' formats are allowed.");
                    alert("Only .jpeg, .jpg, .png, .gif formats are allowed.");
                    $('#fileUpload').val("");
                    $(".imgUploadImgMsg").css("display", "none");
                }
                else {
                    function readURL(input) {
              if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                  $('#output').attr('src', e.target.result);
                  $('.imgUploadImgMsg').css("display", "block");
                }
                reader.readAsDataURL(input.files[0]); // convert to base64 string
              }
            }
            readURL(this);
                }
                
 }); 


function Edit() {
 $.ajax({
    url: "<?php echo base_url(); ?>agent/User/EditAgent",
    type:"POST",
    cache:false,
        contentType: false,
        processData: false,
    data:new FormData($('#Edit')[0]),
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
</script>
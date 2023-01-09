<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Profile<small>Preview</small></h1>
		<!-- <ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="active"><a href="#">Profile</a></li>
		</ol> -->
    </section>

    <!-- Main content -->
    <section class="content">
		<?php echo $this->session->flashdata('msg'); ?>
		<div class="error-msg"></div>
		<div class="row">
			<!-- left column -->
			<div class="col-md-6">
				<!-- general form elements -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Personal Information</h3>
					</div>
					<!-- /.box-header -->
					<!-- form start -->
					<form role="form" method="post" id="profile_form" action="<?php echo base_url().'Admin/Profile/change_name_email'; ?>">
						<div class="box-body">
							<input type="hidden" name="id" value="<?php echo $admindata['id'] ?>">
							<div class="form-group">
								<label class=" form-control-label">Name</label>
								<input type="text" name="name" id="name" value="<?php echo $admindata['name'] ?>" class="form-control" required>
							</div>
							<div class="form-group">
								<label class=" form-control-label">Email</label>
								<input type="email" name="email" id="email" value="<?php echo $admindata['email'] ?>" class="form-control" required>
							</div>
						</div>
						<div class="box-footer edit-profile-btn">
							<input type="submit" value="Submit" class="btn btn-success">
						</div>
					</form>
				</div>
			</div>

			<div class="col-md-6">
				<!-- general form elements -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Change Password</h3>
					</div>
			
					<form role="form" id="change_password_form" method="post" action="<?php echo base_url().'Admin/Profile/change_password'; ?>">
						<div class="box-body">
							<div id="error_pass"></div>
							<input type="hidden" name="id" value="<?php echo $admindata['id'] ?>">
							<div class="form-group">
								<label class=" form-control-label">Current Password</label>
								<input type="password" name="admin_password" id="Current_Password" class="form-control" required>
							</div>
							<div class="form-group">
								<label class=" form-control-label">New Password</label>
								<input type="password" name="New_Password" id="New_Password" class="form-control" required>
							</div>
							<div class="form-group">
								<label class=" form-control-label">Confirm Password</label>
								<input type="password" name="Confirm_Password" id="Confirm_Password" class="form-control" required>
							</div>
						</div>

						<div class="box-footer edit-password-btn">
							<input type="submit"  class="btn btn-success">
						</div>
					</form>
				</div>
			</div>
		</div>
    </section>
</div>
 
<?php include_once('include/footer.php'); ?>

<script>
	$('form#profile_form').submit(function(e) {

    var form = $(this);

    e.preventDefault();

    $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/profile/edit'); ?>",
        data: form.serialize(), // <--- THIS IS THE CHANGE
        dataType: "json",
        beforeSend: function() {
          $(".edit-profile-btn").html('<button class="btn btn-danger"> <i class="fa fa-spinner fa-spin" style="font-size:24px"></i> Processing.. </button>')
        },
        success: function(data){
        	if(data.status == 1)
        	{
              location.reload();
        	}
        	else
        	{
        		$(".edit-profile-btn").html('<input type="submit" value="Submit" class="btn btn-success">');
        	}
            
        },
        
   });

});

	$('form#change_password_form').submit(function(e) {

    var form = $(this);

    e.preventDefault();

    $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/profile/change_password'); ?>",
        data: form.serialize(), // <--- THIS IS THE CHANGE
        dataType: "json",
        beforeSend: function() {
          $(".edit-password-btn").html('<button class="btn btn-danger"> <i class="fa fa-spinner fa-spin" style="font-size:24px"></i> Processing.. </button>')
        },
        success: function(data){
        	if(data.status == 1)
        	{
              location.reload();
        	}
        	else
        	{
        		$('.error-msg').html(data.message);
        		$(".edit-password-btn").html('<input type="submit"  class="btn btn-success">');
        	}
            
        },
        
   });

});
</script>

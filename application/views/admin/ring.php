<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Ringtone<small>Management</small></h1>
		<!-- <ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="active"><a href="#">Profile</a></li>
		</ol> -->
    </section>

    <!-- Main content -->
    <section class="content">

		<div class="error-msg"></div>
		<div class="row">
			<!-- left column -->
		<?php echo $this->session->flashdata('msgs'); ?>
			<div class="col-md-6">
				<!-- general form elements -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Change Ringtone</h3>
					</div>
			         <audio controls>
                          <source src="<?php echo base_url();?>upload/rings/<?php echo $ring["file"]; ?>" >
                        </audio>
					<form role="form" method="post" action="<?php echo base_url().'Admin/Level/editRing'; ?>" enctype="multipart/form-data">
						<div class="box-body">
							<div id="error_pass"></div>
							<input type="hidden" name="id" value="<?php echo $ring['id'] ?>">
							<div class="form-group">
								<label class=" form-control-label">Edit file</label>
								<input type="file" name="file" class="form-control" required accept="audio/mp3,audio/*;capture=microphone">
							</div>
							
						</div>

						<div class="box-footer edit-password-btn">
							<input type="submit" value="Update" class="btn btn-success">
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

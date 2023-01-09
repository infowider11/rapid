<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Email<small>Preview</small></h1>
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
						<h3 class="box-title">Email Information Hostess Add Via Invitation Link</h3>
						<div id="MessageErr"></div>
					</div>
					<!-- /.box-header -->
					<!-- form start -->
					<form role="form" method="post" action="<?php echo base_url(); ?>Admin/Email/email_builder_hostess_Update">
				<div class="box-body">
					<div class="row">
						<div class="col-md-8 alert alert-success">Keywords: [otp] (for otp), [login_link] (for agent login link)
						</div>
					</div>
						<div class="row">
							<div class="col-md-6">
							<div class="form-group">
								<label>User type</label>
								<input class="form-control" type="text" name="name" value="<?php echo $data["name"];?>" readonly>
							</div>								
							</div>

						</div>
						<div class="row">
							<div class="col-md-12">
							<div class="form-group">
								<label>Message</label>
								<textarea class="form-control ckeditor" name="text" placeholder="content" required=""><?php echo $data["text"];?></textarea>
							</div>
						</div>
						</div>
						<button type="submit" class="btn btn-primary" id="AddVehicleBtn">Update<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw btn-load"></i></button>	
				</div>		
						
					</form>
				</div>
			</div>

		</div>
    </section>
</div>
 
<?php include_once('include/footer.php'); ?>

<script>

</script>
<script src="https://cdn.ckeditor.com/4.15.1/standard/ckeditor.js"></script>  
<script type="text/javascript">
            $(function() {
               CKEDITOR.replace('ckeditor');
            });
</script>
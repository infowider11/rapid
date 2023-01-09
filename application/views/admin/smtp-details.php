<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>SMTP<small>Preview</small></h1>
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
			<div class="col-md-6">
				<!-- general form elements -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">SMTP Information</h3>
					</div>
					<!-- /.box-header -->
					<!-- form start -->
					<form role="form" method="post" action="<?php echo base_url().'Admin/Trade/smtp_edit'; ?>">
						<div class="box-body">
							<input type="hidden" name="id" value="<?php echo $admindata['id'] ?>">
							<div class="form-group">
								<label class=" form-control-label">Host</label>
								<input type="text" name="host" value="<?php echo $data['host']; ?>" class="form-control" required placeholder="Host">
							</div>
							<div class="form-group">
								<label class=" form-control-label">Password</label>
								<input type="text" name="password" value="<?php echo $data['password']; ?>" class="form-control" required placeholder="Password">
							</div>
							<div class="form-group">
								<label class=" form-control-label">SSL</label>
								<input type="text" name="ssl" value="<?php echo $data['ssl']; ?>" class="form-control" required placeholder="SSL">
							</div>
							<div class="form-group">
								<label class=" form-control-label">User Name</label>
								<input type="text" name="user_name" value="<?php echo $data['user_name']; ?>" class="form-control" required placeholder="User name" >
							</div>
							<div class="form-group">
								<label class=" form-control-label">Port</label>
								<input type="text" name="port" value="<?php echo $data['port']; ?>" class="form-control" required placeholder="Port" > 
							</div>
						</div>
						<div class="box-footer edit-profile-btn">
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
	
</script>

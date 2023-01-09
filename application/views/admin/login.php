<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?= Project; ?></title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/images/logo.png">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/bower_components/bootstrap/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/bower_components/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/bower_components/Ionicons/css/ionicons.min.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/dist/css/AdminLTE.min.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/dist/css/skins/_all-skins.min.css">

	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/bower_components/bootstrap-daterangepicker/daterangepicker.css">
	
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
	
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/custom/style.css">
<!-- Favicons -->
	<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/site/img/favi.png">
	<style type="text/css">
		
.login-page, .register-page {
    background: #fff;
}
.login-box-body, .register-box-body {
	background: #f7f7f7;
	padding: 20px;
	border-top: 0;
	color: #666;
	border: 1px solid #e1e1e1;
}
	</style>
</head>
	<body class="hold-transition login-page">
		<div class="login-box">
			<div class="login-logo">
				<img src="<?php echo base_url(); ?>assets/images/logo.png"  width="150px"  alt="User Image">
				<!-- <a href="../../index2.html"><b>Admin</b>LTE</a> -->
			</div>
			<?php echo $this->session->flashdata('msg'); ?>
			<!-- /.login-logo -->
			<div class="login-box-body">
				<p class="login-box-msg">Sign in to start your session</p>
				<form role="form" id="login_form1" action="<?php echo base_url().'Admin/login/do_login'; ?>" method="post">
					<div class="form-group">
						<label>Email address</label>
						<input type="email" class="form-control" name="email" id="email"> 
					</div>
					<div class="form-group">
						<label>Password</label>
						<input type="password" class="form-control" name="password" id="password">
					</div>
					<button type="submit" style="background-color: blue;" class="btn btn-success btn-flat m-b-30 m-t-30">Sign in</button>
					<div class="social-login-content" style="display:none;">
						<div class="social-button">
							<button type="button" class="btn social facebook btn-flat btn-addon mb-3"><i class="ti-facebook"></i>Sign in with facebook</button>
							<button type="button" class="btn social twitter btn-flat btn-addon mt-2"><i class="ti-twitter"></i>Sign in with twitter</button>
						</div>
					</div>
					<div class="register-link m-t-15 text-center hide"  style="display:none;">
						<p>Don't have account ? <a href="#"> Sign Up Here</a></p>
					</div>
				</form>
			</div>
		</div>
		<script src="<?php echo base_url(); ?>assets/admin/bower_components/jquery/dist/jquery.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/admin/bower_components/jquery-ui/jquery-ui.min.js"></script>

		<script src="<?php echo base_url(); ?>assets/admin/dist/js/adminlte.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.js"></script>
		<script src="<?php echo base_url(); ?>assets/admin/custom/custom.js"></script>
	</body>
</html>
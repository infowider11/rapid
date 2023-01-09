<?php include_once('include/header.php'); ?> 
<div class="content-wrapper">
	<section class="content-header">
		<h1>Dashboard<small>Control panel</small></h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="active">Dashboard</li>
		</ol>
   </section>
   
   <!-- Main content -->
   <section class="content">

		<!-- Small boxes (Stat box) -->
		<div class="row">
			
			
		<div class="col-lg-3 col-xs-6">
			<!-- small box -->

				<div class="small-box bg-yellow">
					<div class="inner">
						<h3><?php echo count($userData); ?></h3>
						<p>Users</p>
					</div>
					<div class="icon">
						<i class="ion ion-person-add"></i>
					</div>
					<a href="<?php echo base_url() ?>admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
				</div>
			</div>

			<div class="col-lg-3 col-xs-6">
			<!-- small box -->

				<div class="small-box bg-red">
					<div class="inner">
						<h3><?php echo count($userBlocked); ?></h3>
						<p>Blocked</p>
					</div>
					<div class="icon">
						<i class="fa fa-ban" aria-hidden="true"></i>
					</div>
					<a href="<?php echo base_url() ?>admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
				</div>
			</div>

			<div class="col-lg-3 col-xs-6">
			<!-- small box -->

				<div class="small-box bg-yellow">
					<div class="inner">
						<h3><?php echo count($userUnblocked); ?></h3>
						<p>Un-blocked</p>
					</div>
					<div class="icon">
						<i class="fa fa-unlock" aria-hidden="true"></i>
					</div>
					<a href="<?php echo base_url() ?>admin/users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
				</div>
			</div>
			<!-- ./col
			</div>
			<div class="row">
			<h2>Site Visitors</h2>

			<div class="col-lg-3 col-xs-6">
			<!-- small box -->
				<!--<div class="small-box" style="background-color: #419c87 !important;    color: #fff !important;">
					<div class="inner">
						<h3>0</h3>
						<p>Current Date Visitors</p>
					</div>
					<div class="icon">
						<i class="ion ion-person-add"></i>
					</div>-->
					<!-- <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
				<!--</div>
			</div>-->
			<!-- ./col -->
			<!-- ./col -->
			<!--<div class="col-lg-3 col-xs-6">-->
			<!-- small box -->

			<!--	<div class="small-box" style="background-color: #9c4195 !important;    color: #fff !important;">
					<div class="inner">
						<h3>0</h3>
						<p>Current Month Visitors</p>
					</div>
					<div class="icon">
						<i class="ion ion-person-add"></i>
					</div>
					<!-- <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
			<!--	</div>
			</div>-->
			<!-- ./col -->
		<!--	<div class="col-lg-3 col-xs-6">-->
			<!-- small box -->

			<!--	<div class="small-box" style="background-color: #27367f !important;    color: #fff !important;">
					<div class="inner">
						<h3>0</h3>
						<p>Current Week Visitors</p>
					</div>
					<div class="icon">
						<i class="ion ion-person-add"></i>
					</div>
					<!-- <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
			<!--	</div>
			</div>-->
			<!-- ./col -->
			<!-- ./col -->
			<!--<div class="col-lg-3 col-xs-6">-->
			<!-- small box -->

			<!--	<div class="small-box" style="background-color: #ae9435 !important;    color: #fff !important;">
					<div class="inner">
						<h3>0<h3>
						<p>Current Year Visitors</p>
					</div>
					<div class="icon">
						<i class="ion ion-person-add"></i>
					</div>-->
					<!-- <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
				<!--</div>
			</div>-->
			<!-- ./col -->
			<!-- ./col -->
		</div>
			<!-- /.row -->
	</section>
	<!-- /.content -->
</div>
<!-- /.content-wrapper -->
<?php include_once('include/footer.php'); ?>
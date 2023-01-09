<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Transaction<small>management</small></h1>
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
						<h3 class="box-title">transaction management</h3>
						<form class="form_login" method="get">
						    
                <div class="row">
                    
                  	<div class="col-md-2">
                        <div class="form-group">
                        	<label>Date</label>
                          <input type="date" value="<?= (isset($_REQUEST['start_date']) && !empty($_REQUEST['start_date'])) ? $_REQUEST['start_date'] : ''; ?>" name="start_date" class="form-control" >
        				</div>
                     </div>

                     <div class="col-md-2">
                        <div class="form-group">
                          <label>Unique Id</label>
                          <input type="text" name="uniqueID" class="form-control" placeholder="Unique Id" value="<?php echo $_REQUEST['uniqueID']; ?>" />
                </div>
                     </div>



                     <div class="col-md-3">
                        <div class="form-group">
                        	<br>
                          <button class="btn btn-primary py-3 px-5" style="width:auto; height:auto">Filter</button>
                          <a href="<?php echo site_url().'admin/transaction-history'; ?>" class="btn btn-primary py-3 px-5" style="width:auto; height:auto">Reset Filter</a>
        				</div>
                     </div>

                </div> 
                 
            </form>
					</div>
					<!-- /.box-header -->
					<!-- form start -->
					<div id="MessageUpdateErr"></div>	
					<div class="table-responsive" style="padding: 20px;">			
					<table class="table table-striped table-bordered DataTable">
						<thead>
						<tr>
							<th>SNo.</th>
							<th>User Name</th>
							<th>Diamonds</th>
							<th>Transaction Id</th>
							<th>Payment Method</th>
							<th>Status</th>
							<th>Date-Time</th>
						</tr>
					</thead>
					<tbody>
<?php
if($transactions){
$x=1;
foreach ($transactions as $transaction){
	$userdata = $this->common_model->GetColumnName('users',array('id'=>$transaction['user_id']),array('id','nickname','uniqueID'));
?>

	

		<tr>
			<td><?php echo $x; ?></td>
			<td>
<?php if ($userdata["nickname"] !="") {
?>
<a href="<?php echo base_url(); ?>admin/users?UserId=<?php echo $transaction['user_id']; ?>" ><?php echo $userdata["nickname"]; ?></a><br>
<?php
} else {
	?>
	<a href="<?php echo base_url(); ?>admin/users?UserId=<?php echo $transaction['user_id']; ?>" >Profile not updated</a><br>
	<?php
}

echo $userdata["uniqueID"];?>
				</td>
	        <td><?php echo $transaction["diamond"]; ?></td>	
	        <td><?php echo $transaction["transaction_id"]; ?></td>
	        <td><?php echo $transaction["payment_method"]; ?></td>				
	        <td>
						<?php if($transaction["status"]==1) { ?>
						<span class="label label-success">Completed</span>
						<?php } else if($transaction["status"]==2){ ?>
						<span class="label label-danger">Failed</span>
						<?php } else if($transaction["status"]==0){ ?>
						<span class="label label-warning">Pending</span>
						<?php } ?>			
	        <td><?php echo $transaction["date"]; ?></td>					
		</tr>
<?php $x++; } } ?>					</tbody>	
					</table>
				</div>
				</div>
			</div>

		</div>
    </section>
</div>
 
<?php include_once('include/footer.php'); ?>

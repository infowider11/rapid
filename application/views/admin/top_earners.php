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
						<h3 class="box-title">Top Earners List</h3>
						
						<form class="form_login" method="get">
						    
                <div class="row">
                    
                  	<div class="col-md-3">
                        <div class="form-group">
                          
        					<label>Filters</label>

                <select  name="filter" class="form-control" >
										<option <?= (isset($_REQUEST['filter']) && $_REQUEST['filter']=='All') ? 'selected' : ''; ?> value="All">All</option>
                    <option <?= (isset($_REQUEST['filter']) && $_REQUEST['filter']=='Today') ? 'selected' : ''; ?>  value="Today">Today</option>
                    <option <?= (isset($_REQUEST['filter']) && $_REQUEST['filter']=='Week') ? 'selected' : ''; ?>  value="Week">This Week</option>
                    <option <?= (isset($_REQUEST['filter']) && $_REQUEST['filter']=='Month') ? 'selected' : ''; ?>  value="Month">This Month</option>
                    <option <?= (isset($_REQUEST['filter']) && $_REQUEST['filter']=='Year') ? 'selected' : ''; ?>  value="Year">Monthly Rank</option>
                </select>

        				  
        				</div>
                     </div>

                     


                     <div class="col-md-3">
                        <div class="form-group">
                        	<br>
                          <button class="btn btn-primary py-3 px-5" style="width:auto; height:auto">Filter</button>
                          <a href="<?php echo site_url().'admin/top_earners'; ?>" class="btn btn-primary py-3 px-5" style="width:auto; height:auto">Reset Filter</a>
        				</div>
                     </div>

                </div> 
                 
            </form>

					
            
					</div>
					<!-- /.box-header -->
					<!-- form start -->
					<div id="MessageUpdateErr"></div>	
					<div class="table-responsive" style="padding: 20px;" >			
					<table class="table table-striped table-bordered DataTable">
						<thead>
						<tr>
							<th>Sl. No.</th>
							<th>Unique ID</th>
							<th>Nick Name</th>
							<th>Profile</th>
							<th>Total Coin Earned</th>
							<th>Country Name</th>
							<th>Country Flag</th>
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
							<td>
<?php if ($user["nickname"] !="") {
?>
<a href="<?php echo base_url(); ?>admin/users?UserId=<?php echo $user['id']; ?>" ><?php echo $user["nickname"]; ?></a>
<?php
} else {
	echo "Profile not updated";
}?>
							</td><td>
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
						<td><?php echo $user["total_coin"]; ?></td>
						<td><?php echo $country["nicename"]; ?></td>
						<td><?php if(file_exists('assets/flags/'.strtolower($country['iso']).'.png')){
			            $flags = site_url().'assets/flags/'.strtolower($country['iso']).'.png';
	                    }
						?>
						<img src="<?php echo $flags ?>">
						</td>						


						</tr>
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


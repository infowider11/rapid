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
						<h3 class="box-title">Verified Users List</h3>
						
						<form class="form_login design_searh" method="get">
						    
                <div class="row">
                    
                  	<div class="col-md-2">
                        <div class="form-group">
                        	<label>Start Date</label>
                          <input type="date" value="<?= (isset($_REQUEST['start_date']) && !empty($_REQUEST['start_date'])) ? $_REQUEST['start_date'] : ''; ?>" name="start_date" class="form-control" >
        				</div>
                     </div>

                     <div class="col-md-2">
                        <div class="form-group">
                        	<label>End Date</label>
                         <input type="date" value="<?= (isset($_REQUEST['end_date']) && !empty($_REQUEST['end_date'])) ? $_REQUEST['end_date'] : ''; ?>" name="end_date" class="form-control"  > 
        				</div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group">
                          <label>Country</label>
                          <select class="form-control select2" name="country">
                            <option value="">---Select Country---</option>
<?php
$AllCountry = $this->common_model->GetAllData('country','','nicename','asc');
foreach ($AllCountry as $key => $value) {
?>
                            <option value="<?php echo $value["id"]; ?>" <?php if ($_REQUEST['country'] == $value["id"]) { echo "selected"; } ?> ><?php echo $value["nicename"]; ?></option>
<?php
}
?>                            
                          </select>
                    </div>
                     </div>

                     <div class="col-md-3">
                        <div class="form-group">
                          <label>Keyword</label>
                          <input type="text" name="keyword" class="form-control" placeholder="Keyword" value="<?php echo $_REQUEST['keyword']; ?>" />
                </div>
                     </div>

                     <div class="col-md-3">
                        <div class="form-group">
                        	<br>
                          <button class="btn btn-primary py-3 px-5" style="width:auto; height:auto">Filter</button>
                          <a href="<?php echo site_url().'admin/verified_users'; ?>" class="btn btn-primary py-3 px-5" style="width:auto; height:auto">Reset Filter</a>
        				</div>
                     </div>

                </div> 
                 
            </form>

						<div class="row hide">
                    
                    <div class="col-md-2"><a class="btn btn-danger btn-xs" href="<?php echo site_url().'Admin/Users/export_csv'; ?>" target="_blank">Export CSV</a>
                    </div>
            </div>
            
            
					</div>
					<!-- /.box-header -->
					<!-- form start -->
					<div id="MessageUpdateErr"></div>	
					<div class="table-responsive" style="padding: 20px;">			
					<table class="table table-striped table-bordered DataTable">
						<thead>
						<tr>
							<th>Sl. No.</th>
							<th>Unique ID</th>
							<th>Nick Name</th>
							<th>Profile</th>
							<th>Email</th>
							<th>Phone</th>
							<th>Date of birth</th>
							<th>Level </th>
							<th>Earned point</th>
							<th>Gender</th>
							<th>Country Name</th>
							<th>Status</th>							
							<th>Action</th>
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
							<td><?php echo $user["nickname"]; ?></td><td>
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
							<td><?php echo $user["email"]; ?></td>
							<td><?php echo $user["phone_with_code"]; ?></td>
							<td><?php echo date('d-m-Y', strtotime($user["dob"])); ?></td>
							<td><?php echo $user["level"]; ?></td>
							<td><?php echo $user["coin"]; ?></td>
							<td><?php echo $user["gender"]; ?></td>
							<td><?php echo $country["nicename"]; ?> 
						<?php if(file_exists('assets/flags/'.strtolower($country['iso']).'.png')){
			            $flags = site_url().'assets/flags/'.strtolower($country['iso']).'.png';
	                    }
						?>
						<img src="<?php echo $flags ?>"></td>
													
<td>
<?php
if ($user["status"] ==1) {
	echo "<span class='btn btn-xs btn-success'>Active<span>";

} else { echo "<span class='btn btn-xs btn-danger'>Blocked<span>"; }
 ?></td>

<!-- <td>
<a class="btn <?php if($user['status'] == '1'){echo "btn-danger"; } else { echo "btn-success"; } ?> btn-xs" href="<?php echo base_url();?>Admin/Users/statusUpdate/?UserId=<?php echo $user['id']; ?>&Status=<?php echo $user['status']; ?>" >
    <?php if($user['status'] == '0'){echo "Unblock"; } else { echo "Blocked"; } ?>
</a> 								
</td> -->

<td>
   <?php if($user['status'] == 1){ ?> 	
   	<a class="btn btn-danger btn-xs" href="javascript:void(0)" onclick="change_status(<?php echo $user['id']; ?>,0)">Block</a> 
   <?php }else{ ?>
   	<a class="btn btn-success btn-xs" href="javascript:void(0)" onclick="change_status(<?php echo $user['id']; ?>,1)">Unblock</a> 
   <?php } ?>							
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

<script>
   function change_status(id,status)
   {
   	if(confirm('Are you sure?'))
   	{
		$.ajax({
	        type: "POST",
	        url: "<?php echo site_url('Admin/Users/change_status'); ?>",
	        data: {id:id,status:status},
	        dataType: "json",
	        success: function(data){
	        	if(data.status == 1)
	        	{
	              location.reload();
	        	}
	        	else
	        	{
	        		$('.error-msg').html(data.message);
	        	}
	            
	        },
	        
	   });
   	}

   }

 function AddTradeAmount(FormId) {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Users/AddTradeAmount",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#AddTradeAmount'+FormId)[0]),
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
        $("#MessageTradeError"+FormId).html(data.message);
        $('#EditBtn'+FormId).prop('disabled',false);
        $('.btn-loadEdit'+FormId).hide();
      }
    }
  });
    return false;

}    
</script>
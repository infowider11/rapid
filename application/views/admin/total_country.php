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
						<h3 class="box-title">Total Country List</h3>
					</div>
					<!-- /.box-header -->
					<!-- form start -->
					<div id="MessageUpdateErr"></div>	
					<div class="table-responsive" style="padding: 20px;">			
					<table class="table table-striped table-bordered DataTableNew">
						<thead>
						<tr>
							<!-- <th>SNo.</th> -->
							<th>Country Name</th>
							<th>Country Flag</th>
							<th>Total User</th>
						</tr>
					</thead>
					<tbody>
<?php
$x=1;
foreach ($countrys as $country){
	$user_data = $this->common_model->GetColumnName('users',array('country'=>$country['id']),array('id'),true);
?>

	<!--?php $country = $this->common_model->GetSingleData('country',array('id'=>$user['country']));?>-->

					<tr>
						<!-- <td><?php echo $x; ?></td> -->
					    <td><a href="<?php echo site_url(); ?>admin/users?country=<?php echo $country['id']; ?>"><?php echo $country["nicename"]; ?></a></td>
						<td>
							<?php if(file_exists('assets/flags/'.strtolower($country['iso']).'.png')){
			                  $flags = site_url().'assets/flags/'.strtolower($country['iso']).'.png';
	                        }
						    ?>
						<img src="<?php echo $flags ?>">
						</td>							
                        <td><?php echo count($user_data); ?></td>

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
	$('.sorting_desc').click();
	$('.sorting_desc').click();
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
</script>

<script>
	$(document).ready(function() {
    $('.DataTableNew').DataTable( {
        "order": [[ 2, "desc" ]]
    } );
} );
</script>
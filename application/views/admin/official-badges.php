<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Official Badges<small>management</small></h1>
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
			<div class="col-md-12">
				<!-- general form elements -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Official Badges List</h3>
						<?php echo $this->session->flashdata('msgs'); ?>

            <div class="row">
            <div class="col-md-12 text-right" >                           
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Add</button>
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
							<th>S. No.</th>
							<th>User</th>
              <th>Email</th>
						</tr>
					</thead>
					<tbody>
<?php
if($data){
$x=1;
foreach ($data as $value){
?>

	

		<tr>
			<td><?php echo $x; ?></td>
	    <td><?php
$report_by = $this->common_model->GetSingleData('users', array('id'=>$value["id"]));
 if ($report_by["nickname"] !="") {
?>
<a href="<?php echo base_url(); ?>admin/users?UserId=<?php echo $value["id"]; ?>" ><?php echo $report_by["nickname"]; ?></a><br>
<?php
} else {
  ?>
<a href="<?php echo base_url(); ?>admin/users?UserId=<?php echo $value['id']; ?>" >Profile not updated</a><br>
  <?php
}
echo $report_by["uniqueID"]; ?></td>
      <td><?php echo $value["email"];?></td> 
	    
		</tr>

<?php $x++; } } ?>					</tbody>	
					</table>
				</div>
				</div>
			</div>

		</div>
    </section>
</div>
 <div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add Official Badges</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div id="MessageErr"></div>
      <form method="post" onsubmit="return Add_Official();" id="Add_Official" enctype="multipart/form-data">
      <div class="modal-body">
        <div class="form-group">
          <label>Select Users</label>
          <select class="form-control select2" name="UserId">
            <option value="">---Select---</option>
<?php
if ($data1) {
  foreach ($data1 as $key => $value1) {
    ?>
          <option value="<?php echo $value1["id"]; ?>"><?php echo $value1["nickname"].' '.$value1["uniqueID"]; ?></option>
    <?php
  }
}
?>            
          </select>
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button class="btn btn-info" type="submit" name="submit" id="submit">Submit<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw" id="btn_load"></i></button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      </form>
    </div>
  </div>
</div>
<?php include_once('include/footer.php'); ?>
<script>
function Add_Official() {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/RatingCategory/add_official_badges",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#Add_Official')[0]),
    dataType:'json',
    beforeSend:function(){
      $('#submit').prop('disabled',true);
      $('#btn_load').show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageErr").html(data.message);
        $('#submit').prop('disabled',false);
        $('#btn-load').hide();
      }
    }
  });
    return false;

}
</script>
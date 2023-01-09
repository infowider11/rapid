<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1><?php echo $content; ?> Rating Category<small>management</small></h1>
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
						<h3 class="box-title"><?php echo $content; ?> rating category list</h3>
						<div style="float: right;">
							 <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Add</button>
						</div>		
						<form class="form_login hide" method="get">
						    
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
							<th>Rating</th>
							<th>Name</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
<?php
if($data){
$x=1;
foreach ($data as $rows){
?>

	

		<tr>
			<td><?php echo $x; ?></td>
	        <td><?php echo $rows["rating"]; ?></td>	
	        <td><?php echo $rows["name"]; ?></td>
	        <td>
	       <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal<?php echo $rows["id"];?>"><i class="fa fa-edit"></i></button>
	       <a class="btn btn-danger btn-xs" onclick="return confirm('Are you sure want to delete this?')" href="<?php echo base_url();?>Admin/RatingCategory/deleteCat/?id=<?php echo $rows["id"];?>&pageName=<?php echo $this->uri->segment(2); ?>" >
<i class="fa fa-trash" aria-hidden="true"></i>
</a>
	   		</td>					
		</tr>
 <div class="modal" id="myModal<?php echo $rows["id"];?>">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Edit <?php echo $content; ?> Rating Category</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form method="post" onsubmit="return Edit('<?php echo $rows["id"];?>');" id="Edit<?php echo $rows["id"];?>" enctype="multipart/form-data" >
      <!-- Modal body -->
      <div class="modal-body">
        	<div id="MessageErr<?php echo $rows["id"];?>" style="color: red;"></div>
        	<div class="form-group">
            <label>Name :</label>
            <input type="text" class="form-control" placeholder="Enter Name" name="name" required value="<?php echo $rows["name"]; ?>" />
            <input type="hidden" name="id" value="<?php echo $rows["id"]; ?>"/>
          </div>
          <div class="form-group">
            <label>Rating :</label>
            <select class="form-control" name="rating" required="">
            	<option value="">---Select Rating---</option>
            	<option value="1" <?php if ($rows["rating"] == 1) {echo "selected"; } ?> >1</option>
            	<option value="2" <?php if ($rows["rating"] == 2) {echo "selected"; } ?> >2</option>
            	<option value="3" <?php if ($rows["rating"] == 3) {echo "selected"; } ?> >3</option>
            	<option value="4" <?php if ($rows["rating"] == 4) {echo "selected"; } ?> >4</option>
            	<option value="5" <?php if ($rows["rating"] == 5) {echo "selected"; } ?> >5</option>
            </select>
          </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
      	<button type="submit" class="btn btn-primary" id="EditBtn<?php echo $rows["id"];?>">Update<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw" id="btn-load<?php echo $rows["id"];?>"></i></button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
  	</form>
    </div>
  </div>
</div>		
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
        <h4 class="modal-title">Add <?php echo $content; ?> Rating Category</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form method="post" onsubmit="return Add();" id="Add" enctype="multipart/form-data" >
      <!-- Modal body -->
      <div class="modal-body">
        	<div id="MessageErr" style="color: red;"></div>
        	<div class="form-group">
            <label>Name :</label>
            <input type="text" class="form-control" placeholder="Enter Name" name="name" required />
            <input type="hidden" name="gender" value="<?php echo $content; ?>"/>
          </div>
          <div class="form-group">
            <label>Rating :</label>
            <select class="form-control" name="rating" required="">
            	<option value="">---Select Rating---</option>
            	<option value="1">1</option>
            	<option value="2">2</option>
            	<option value="3">3</option>
            	<option value="4">4</option>
            	<option value="5">5</option>
            </select>
          </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
      	<button type="submit" class="btn btn-primary" id="AddVehicleBtn">Submit<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw btn-load"></i></button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
  	</form>
    </div>
  </div>
</div>
<?php include_once('include/footer.php'); ?>
<script>
 function Add() {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/RatingCategory/AddCategory",
    type:"POST",
    cache:false,
        contentType: false,
        processData: false,
    data:new FormData($('#Add')[0]),
    dataType:'json',
    beforeSend:function(){
      $('#AddVehicleBtn').prop('disabled',true);
      $('.btn-load').show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageErr").html(data.message);
        $('#AddVehicleBtn').prop('disabled',false);
        $('.btn-load').hide();
      }
    }
  });
    return false;

} 
 function Edit(FormId) {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/RatingCategory/EditCategory",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#Edit'+FormId)[0]),
    dataType:'json',
    beforeSend:function(){
      $('#EditBtn'+FormId).prop('disabled',true);
      $('#btn-load'+FormId).show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageErr"+FormId).html(data.message);
        $('#EditBtn'+FormId).prop('disabled',false);
        $('#btn-load'+FormId).hide();
      }
    }
  });
    return false;

}
</script>
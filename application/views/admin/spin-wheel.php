<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Spin Wheel<small>management</small></h1>
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
						<h3 class="box-title">Spin Wheel management</h3>
						<?php echo $this->session->flashdata('msgs'); ?>
					</div>
					
					<div class="table-responsive" style="padding: 20px;">			
					<table class="table table-striped table-bordered DataTable">
						<thead>
						<tr>
							<th>S. No.</th>
							<th>Diamonds</th>
							<th>Title</th>
							<th>Is Empty</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
<?php
if($records){
$x=1;
foreach ($records as $key => $record){
?>

	

		<tr>
			<td><?php echo $key++; ?></td>
			<td><?php echo $record['diamond']?></td>
			<td><?php echo $record['text']?></td>
			<td>
				<?php if($record['is_empty']==1) { ?>
				Yes
				<?php } else { ?>
				No
				<?php } ?>
			</td>
	    <td>
				<a href="javascript:;" class="btn btn-info" data-target="#editModdal<?php echo $record["id"];?>" data-toggle="modal">Edit</a>
	    </td>				
		</tr>

<div class="modal" id="editModdal<?php echo $record["id"];?>">
  <div class="modal-dialog ">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Edit</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

			<form method="post" onsubmit="return RejectRequest('<?php echo $record["id"]; ?>');" id="RejectRequest<?php echo $record["id"]; ?>" enctype="multipart/form-data" >    
				<!-- Modal body -->
				<div class="modal-body">
					<div class="MessageUpdateErr<?php echo $record["id"]; ?>"></div>
					<div class="form-group">
						<label >Diamond</label>
            <input class="form-control" <?php echo ($record['is_empty']==1) ? 'readonly' : 'required min="1"'?> name="diamond" value="<?php echo $record["diamond"]; ?>" placeholder="diamond">
            <input type="hidden" name="id" value="<?php echo $record["id"]; ?>" />
            <input type="hidden" name="is_empty" value="<?php echo $record["is_empty"]; ?>" />
          </div>
					<div class="form-group">
						<label >Title</label>
            <input class="form-control" required  value="<?php echo $record["text"]; ?>" name="text" placeholder="Title">
          </div>
				</div>

				<!-- Modal footer -->
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary" id="EditVehicleBtn<?php echo $record["id"]; ?>">Update</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form>
    </div>
  </div>
</div>
<?php } } ?>					</tbody>	
					</table>
				</div>
				</div>
			</div>

		</div>
    </section>
</div>
 
<?php include_once('include/footer.php'); ?>
<script>
function RejectRequest(FormId) {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/SpinWheel/editWheel",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#RejectRequest'+FormId)[0]),
    dataType:'json',
    beforeSend:function(){
      $('#EditVehicleBtn'+FormId).prop('disabled',true);
      $('#EditVehicleBtn'+FormId).html('Processing...');
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageUpdateErr"+FormId).html(data.message);
        $('#EditVehicleBtn'+FormId).prop('disabled',false);
        $('#EditVehicleBtn'+FormId).html('Update');
      }
    }
  });
    return false;

}
</script>
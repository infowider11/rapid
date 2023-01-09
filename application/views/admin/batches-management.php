<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Badges<small>management</small></h1>
		<!-- <ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="active"><a href="#">Profile</a></li>
		</ol> -->
    </section>

    <!-- Main content -->
    <section class="content">

		<div class="row">
			<!-- left column -->
			<div class="col-md-12">
				<!-- general form elements -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Badges List</h3>
						<hr>
                <?php echo $this->session->flashdata('msgs'); ?>
           <div class="row">
            <div class="col-md-8">&nbsp;</div>
            <div class="col-md-4 text-right" >                
                <button type="button" class="btn btn-primary hide" data-toggle="modal" data-target="#myModalcsv">Import Category</button>                
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
              <th>Sl. No.</th>
							<th>Title</th>
              <th>Image</th>
							<th>Action</th>							
						</tr>
					</thead>
					<tbody>
<?php
$x=1;
foreach ($data as $values){
?>
						<tr>
              <td><?php echo $x; ?></td>
							<td><?php echo $values["title"]; ?></td>
              <td>
<?php
if ($values["image"] !="") {
  ?>
  <img src="<?php echo base_url(); ?><?php echo $values['image']; ?>" height="100px" width="100px"/><?php
      }
      else
      { ?>
  <img src="<?php echo base_url(); ?>assets/admin/batchesImg/no-image-icon-23485.png" height="100px" width="100px"/>      
<?php       
      }
?>
              </td>
							<td>
								<button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal<?php echo $values["id"];?>"><i class="fa fa-edit"></i></button>

								<a class="btn btn-danger btn-xs" onclick="return confirm('Are you sure want to delete this?')" href="<?php echo base_url();?>Admin/Batch/deleteBatch?Id=<?php echo $values["id"];?>" >
<i class="fa fa-trash" aria-hidden="true"></i>
</a>
							</td>					
						</tr>
<div class="modal" id="myModal<?php echo $values["id"];?>">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Edit Badges</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div id="MessageUpdateErr<?php echo $values["id"];?>"></div>
      <form method="post" onsubmit="return Edit('<?php echo $values["id"];?>');" id="Edit<?php echo $values["id"];?>">
      <div class="modal-body">
        <div class="form-group">
          <label>Title</label>
          <input class="form-control" type="text" name="title" placeholder="Enter title" required value="<?php echo $values["title"]; ?>" />
        </div>
        <div class="form-group">
          <input type="hidden" name="id" value="<?php echo $values["id"]; ?>" />
          <label>Image</label>
          <br>
          <?php
if ($values["image"] !="") {
  ?>
  <img id="blah<?php echo $values["id"]; ?>" src="<?php echo base_url(); ?>assets/admin/batchesImg/<?php echo $values['image']; ?>" height="100px" width="100px"/><?php
      }
      else
      { ?>
  <img id="blah<?php echo $values["id"]; ?>" src="<?php echo base_url(); ?>assets/admin/batchesImg/no-image-icon-23485.png" height="100px" width="100px"/>      
<?php       
      }
?>
          <input type="file" name="image" accept="image/*" class="form-control" id="imgInp<?php echo $values["id"]; ?>"/>
        
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
      	<button class="btn btn-info" type="submit" name="submit" id="update">Upate <i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw btn-loadEdit"></i></button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      </form>
    </div>
  </div>
</div>	
<script>
    $("#imgInp<?php echo $values["id"]; ?>").change(function() {

              var fileExtension = ['jpeg', 'jpg', 'png', 'gif'];
                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                    // alert("Only '.jpeg','.jpg' formats are allowed.");
                    alert("Only .jpeg, .jpg, .png, .gif formats are allowed.");
                    $('#imgInp<?php echo $values["id"]; ?>').val("");
                    $('#blah<?php echo $values["id"]; ?>').css("display", "none");
                    $('#UpdateImgMsg<?php echo $values["id"]; ?>').html('');

                }
                else {
                  readURL(this,'blah<?php echo $values["id"]; ?>', 'UpdateImgMsg<?php echo $values["id"]; ?>');
                }
  
});
</script>					
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
 

<!-- The Modal -->
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add Badges</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div id="MessageErr"></div>
      <form method="post" onsubmit="return Add();" id="Add">
      <div class="modal-body">
      	<div class="form-group">
      		<label>Title</label>
      		<input class="form-control" type="text" name="title" placeholder="Enter title" required />
      	</div>
        <div class="form-group">
          <label>Image</label>
          <input type="file" name="image" accept="image/*" class="form-control" required id="fileUpload"/>
          <img class="imgUploadImgMsg" id="output" height="100px" width="100px" style="display:none"/>
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
      	<button class="btn btn-info" type="submit" name="submit" id="submit">Submit<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw btn-load"></i></button>
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
    url: "<?php echo base_url(); ?>Admin/Batch/AddBatch",
    type:"POST",
    cache:false,
        contentType: false,
        processData: false,
    data:new FormData($('#Add')[0]),
    dataType:'json',
    beforeSend:function(){
      $('#submit').prop('disabled',true);
      $('.btn-load').show();
    },
    success:function(data) {
      if(data.status==1){
       location.reload();
      } 
      else {
        $("#MessageErr").html(data.message);
        $('#submit').prop('disabled',false);
        $('.btn-load').hide();
      }
    }
  });
    return false;

} 

function Edit(FormId) {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Batch/EditBatch",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#Edit'+FormId)[0]),
    dataType:'json',
    beforeSend:function(){
      $('#update').prop('disabled',true);
      $('.btn-loadEdit').show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageUpdateErr"+FormId).html(data.message);
        $('#update').prop('disabled',false);
        $('.btn-loadEdit').hide();
      }
    }
  });
    return false;

}
$('#fileUpload').on("change",function () {

                var fileExtension = ['jpeg', 'jpg', 'png', 'gif'];
                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                    // alert("Only '.jpeg','.jpg' formats are allowed.");
                    alert("Only .jpeg, .jpg, .png, .gif formats are allowed.");
                    $('#fileUpload').val("");
                    $(".imgUploadImgMsg").css("display", "none");
                }
                else {
                    function readURL(input) {
              if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                  $('#output').attr('src', e.target.result);
                  $('.imgUploadImgMsg').css("display", "block");
                }
                reader.readAsDataURL(input.files[0]); // convert to base64 string
              }
            }
            readURL(this);
                }
                
 });

     function readURL(input,imgid='',msgid='') {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      $('#'+imgid).attr('src', e.target.result);
      $('#'+imgid).css("display", "block");
      $('#'+msgid).html('<p>Live preview</p>');
    }
    reader.readAsDataURL(input.files[0]); // convert to base64 string
  }
}
$("#imgInp")[0].change(function() {
  readURL(this);
});
</script>
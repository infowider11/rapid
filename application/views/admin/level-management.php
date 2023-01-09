<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Level<small>management</small></h1>
		<!-- <ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="active"><a href="#">Profile</a></li>
		</ol> -->
    </section>

    <!-- Main content -->
    <section class="content">
		<?php echo $this->session->flashdata('msgs'); ?>
		<div class="row">
			<!-- left column -->
			<div class="col-md-12">
				<!-- general form elements -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Level List (<?= ($gender=='male') ? 'Boys' : 'Girls'?>)</h3>          
            <hr>
           <div class="row">
            <div class="col-md-8">&nbsp;</div>
            <div class="col-md-4 text-right hide">                                          
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
							<th>Level</th>
              <th>Level Icon</th>
							<th>Required Coins</th>
              <th>Privilege</th>
							<th>Action</th>							
						</tr>
					</thead>
					<tbody>
<?php
$x=1;
foreach ($data as $level){
?>
						<tr>
              <td ><?php echo $x; ?></td>
							<td><?php echo $level["level"]; ?></td>
              <td>
<?php
if ($level["icon"] !="") {
  ?>
  <img src="<?php echo base_url(); ?>assets/admin/levelIcone/<?php echo $level['icon']; ?>" height="100px" width="100px"/><?php
      }
      else
      { ?>
  <img src="<?php echo base_url(); ?>assets/admin/levelIcone/no-image-icon-23485.png" height="100px" width="100px"/>      
<?php       
      }
?>
              </td>
							<td><?php echo $level["daimond"]; ?></td>
              <td><?php

if (!empty($level["privilege"])) {

    $parts = explode(",",$level["privilege"]);
    foreach($parts as $key1 => $value2){
    $privilege[$key1] = $this->common_model->GetSingleData('level_privilege', array('id' => $value2));
    echo $privilege[$key1]["name"]."<br>";
    }
  } else {
    echo "No privilege.";
  }             

               ?></td>
							<td>
								<button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal<?php echo $level["id"];?>"><i class="fa fa-edit"></i></button>
                <a class="btn btn-danger btn-xs hide" onclick="return confirm('Are you sure want to delete this?')" href="<?php echo base_url();?>Admin/Level/deleteLevel?Levelid=<?php echo $level["id"];?>&gender=<?php echo $gender;?>" >
<i class="fa fa-trash" aria-hidden="true"></i>
</a>
							</td>					
						</tr>
<div class="modal" id="myModal<?php echo $level["id"];?>">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Edit Level</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div id="MessageUpdateErr<?php echo $level["id"];?>"></div>
      <form method="post" onsubmit="return Edit('<?php echo $level["id"];?>');" id="Edit<?php echo $level["id"];?>" enctype="multipart/form-data">
      <div class="modal-body">
      	<div class="form-group">
          <label>Level</label>      	
      		<input class="form-control" type="text" name="level" placeholder="Enter level" value="<?php echo $level["level"]; ?>" required readonly=""/>
      	</div>
        <div class="form-group">
          <label>Required Coins</label>   
          <input class="form-control" type="number" name="daimond" placeholder="Enter daimond" value="<?php echo $level["daimond"]; ?>" required min="1" />
        </div>
        <div class="form-group">
          <label>Level Icon</label> 
          <br>  
          <?php
if ($level["image"] !="") {
  ?>
  <img id="blah<?php echo $level["id"]; ?>" src="<?php echo base_url(); ?>assets/admin/levelIcone/<?php echo $level['image']; ?>" height="100px" width="100px"/><?php
      }
      else
      { ?>
  <img id="blah<?php echo $level["id"]; ?>" src="<?php echo base_url(); ?>assets/admin/levelIcone/no-image-icon-23485.png" height="100px" width="100px"/>      
<?php       
      }
?> 
<input type="file" name="image" accept="image/*" class="form-control" id="imgInp<?php echo $level["id"]; ?>"/>
          </div>
        <div class="form-group">
          <label>Level Privilege</label> <br> 
           <?php
foreach ($level_p as $key => $value1) {
?>
        <input type="checkbox" name="privilege[]" value="<?php echo $value1["id"]; ?>" <?php if (
          strpos($level["privilege"], $value1["id"]) !== false) { echo "checked"; } ?> /> 
        <span><?php echo $value1["name"]; ?></span> <br>
<?php
}
           ?>
          
        </div>

      	<input type="hidden" name="leveId" value="<?php echo $level["id"];?>" />
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
      	<button class="btn btn-info update" type="submit" name="submit">Update<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw btn-loadEdit"></i></button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      </form>
    </div>
  </div>
</div>	

  <script>
    $("#imgInp<?php echo $level["id"]; ?>").change(function() {

              var fileExtension = ['jpeg', 'jpg', 'png', 'gif'];
                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                    // alert("Only '.jpeg','.jpg' formats are allowed.");
                    alert("Only .jpeg, .jpg, .png, .gif formats are allowed.");
                    $('#imgInp<?php echo $level["id"]; ?>').val("");
                    $('#blah<?php echo $level["id"]; ?>').css("display", "none");
                    $('#UpdateImgMsg<?php echo $level["id"]; ?>').html('');

                }
                else {
                  readURL(this,'blah<?php echo $level["id"]; ?>', 'UpdateImgMsg<?php echo $level["id"]; ?>');
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

<?php include_once('include/footer.php'); ?>

<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add Level</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div id="MessageUpdateErr"></div>
      <form method="post" onsubmit="return Add();" id="Add">
      <div class="modal-body">
        <div class="form-group">         
          <input type="hidden" name="page" value="<?php echo $gender; ?>">
          <label>Level</label>        
          <input class="form-control" type="text" name="level" placeholder="Enter level" required="" />
        </div>
        <div class="form-group">
          <label>Required Coins</label>   
          <input class="form-control" type="number" name="daimond" placeholder="Enter daimond"required min="1" />
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button class="btn btn-info" type="submit" id="AddBtn">Add</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      </form>
    </div>
  </div>
</div>  

<script>
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
  function Add() {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Level/AddLevel",
    type:"POST",
    cache:false,
        contentType: false,
        processData: false,
    data:new FormData($('#Add')[0]),
    dataType:'json',
    beforeSend:function(){
      $('#AddBtn').prop('disabled',true);
      $('#AddBtn').text('Processing...');
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageErr").html(data.message);
        $('#AddBtn').prop('disabled',false);
        $('#AddBtn').text('Add');
      }
    }
  });
    return false;

}
function Edit(FormId) {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Level/EditLevel",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#Edit'+FormId)[0]),
    dataType:'json',
    beforeSend:function(){
      $('.update').prop('disabled',true);
      $('.btn-loadEdit').show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageUpdateErr"+FormId).html(data.message);
        $('.update').prop('disabled',false);
        $('.btn-loadEdit').hide();
      }
    }
  });
    return false;

}



</script>
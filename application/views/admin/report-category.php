<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Report Category<small>management</small></h1>
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
						<h3 class="box-title">Report Category management</h3>
						<?php echo $this->session->flashdata('msgs'); ?>
            <div class="row">
            <div class="col-md-12 text-right" >                           
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Add</button>
              </div>
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
                          <a href="<?php echo site_url().'admin/album-list'; ?>" class="btn btn-primary py-3 px-5" style="width:auto; height:auto">Reset Filter</a>
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
							<th>S. No.</th>
							<th>Name</th>
							<th>Action</th>
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
      <td><?php echo $value["name"]; ?></td>
	    <td>
<button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal<?php echo $value["id"];?>"><i class="fa fa-edit"></i></button> 
        <a class="btn btn-danger btn-xs" onclick="return confirm('Are you sure?')" href="<?php echo base_url();?>Admin/Report/DeleteCat?id=<?php echo $value["id"];?>&page=<?php echo $this->uri->segment(2); ?>" ><i class="fa fa-trash" aria-hidden="true"></i></a> </td>				
		</tr>
<div class="modal" id="myModal<?php echo $value["id"];?>">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Edit Category</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div id="MessageErr<?php echo $value["id"];?>"></div>
      <form method="post" onsubmit="return Edit('<?php echo $value["id"];?>');" id="Edit<?php echo $value["id"];?>" enctype="multipart/form-data">
      <div class="modal-body">
        <div class="form-group">
              <label class="control-label">Name</label>  
              <input class="form-control" type="text" name="name" required placeholder="name" value="<?php echo $value["name"]; ?>" />   
              <input type="hidden" name="id" value="<?php echo $value["id"];?>">
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button class="btn btn-info" type="submit" name="submit" id="submit<?php echo $value["id"];?>">Update<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw " id="btn_load<?php echo $value["id"];?>"></i></button>
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
        <h4 class="modal-title">Add Category</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div id="MessageErr"></div>
      <form method="post" onsubmit="return Add();" id="Add" enctype="multipart/form-data">
      <div class="modal-body">
        <div class="form-group">
              <label class="control-label">Name</label>  
              <input class="form-control" type="text" name="name" required placeholder="name" />   
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
    url: "<?php echo base_url(); ?>Admin/Report/AddCat",
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

function Edit(ID) {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Report/EditCat",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#Edit'+ID)[0]),
    dataType:'json',
    beforeSend:function(){
      $('#submit'+ID).prop('disabled',true);
      $('#btn_load'+ID).show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageErr"+ID).html(data.message);
        $('#submit'+ID).prop('disabled',false);
        $('#btn_load'+ID).hide();
      }
    }
  });
    return false;

}
</script>
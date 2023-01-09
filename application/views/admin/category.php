<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Category<small>management</small></h1>
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
						<h3 class="box-title">Categorty List</h3>
						<hr>
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
							<th>CategoryName</th>
							<th>Action</th>							
						</tr>
					</thead>
					<tbody>
<?php
$x=1;
foreach ($category as $categoryData){
?>
						<tr>
              <td><?php echo $x; ?></td>
							<td><?php echo $categoryData["name"]; ?></td>
							<td>
								<button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal<?php echo $categoryData["id"];?>"><i class="fa fa-edit"></i></button>

								<a class="btn btn-danger btn-xs" onclick="return confirm('Are you sure want to delete this?')" href="<?php echo base_url();?>Admin/Level/deleteCategory/?Id=<?php echo $categoryData["id"];?>" >
<i class="fa fa-trash" aria-hidden="true"></i>
</a>
							</td>					
						</tr>
<div class="modal" id="myModal<?php echo $categoryData["id"];?>">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Edit Category</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div id="MessageUpdateErr<?php echo $categoryData["id"];?>"></div>
      <form method="post" onsubmit="return EditCategory('<?php echo $categoryData["id"];?>');" id="EditCategory<?php echo $categoryData["id"];?>">
      <div class="modal-body">
      	<div class="form-group">
      		<label>Name</label>
      		<input class="form-control" type="text" name="name" placeholder="Enter category name" value="<?php echo $categoryData["name"]; ?>" required />
      	</div>
      	<input type="hidden" name="categoryId" value="<?php echo $categoryData["id"];?>" />
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
        <h4 class="modal-title">Add Category</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div id="MessageErr"></div>
      <form method="post" onsubmit="return AddCategory();" id="AddCategory">
      <div class="modal-body">
      	<div class="form-group">
      		<label>Name</label>
      		<input class="form-control" type="text" name="name" placeholder="Enter category name" required />
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
<div class="modal" id="myModalcsv">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Import Category</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div id="MessageErr"></div>
      <form method="post" enctype="multipart/form-data" action="<?php echo base_url();?>Admin/SportManagement/ImportCategory">
      <div class="modal-body">
      <div class="form-group">
              <label class="control-label">Select file</label>  
              <input class="form-control" type="file" name="file" required accept=".csv"/>   
      </div>
      <div class="form-group">
               <a href="<?php echo base_url();?>assets/admin/samplefile.csv" class="btn btn-sm btn-warning">Download sample file</a>
        </div>
             
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="submit" class="btn btn-info" name="import">Import</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
     </form>
      </form>
    </div>
  </div>
</div>
<?php include_once('include/footer.php'); ?>

<script>
function AddCategory() {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Level/AddCategory",
    type:"POST",
    cache:false,
        contentType: false,
        processData: false,
    data:new FormData($('#AddCategory')[0]),
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

function EditCategory(FormId) {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Level/EditCategory",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#EditCategory'+FormId)[0]),
    dataType:'json',
    beforeSend:function(){
      $('#update').prop('disabled',true);
      $('.btn-loadEdit').show();
    },
    success:function(data) {
      if(data.status==1){
        window.location.href='category';
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
</script>
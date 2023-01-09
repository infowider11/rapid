<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Diamond<small>management</small></h1>
		
    </section>

    <!-- Main content -->
    <section class="content">
		<?php
      if(isset($_SESSION['success'])){
        echo $_SESSION['success'];
        unset($_SESSION['success']);
      }
      if(isset($_SESSION['error'])){
        echo $_SESSION['error'];
        unset($_SESSION['error']);
      }
    ?>
		<div class="error-msg"></div>
		<div class="row">
			<!-- left column -->
			<div class="col-md-12">
				<!-- general form elements -->
				<?php echo $this->session->flashdata('msgs'); ?>
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Diamond List</h3>
						<div style="float: right;"><a href="javascript:void(0)" class="btn btn-success" data-toggle="modal" data-target="#myModal">Add New</a></div>
					</div>
					<div id="MessageUpdateErr"></div>	
					<div class="table-responsive" style="padding: 20px;">			
					<table class="table table-striped table-bordered DataTable">
						<thead>
						<tr>
							<th>SNo.</th>
							<th>Diamond</th>
							<th>Amount</th>
							<th>Free Card</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
                       <?php
                        if($daimond){ 
                        foreach($daimond as $key=>$daimondVal){ ?>
                       	 <tr>
                       	 	<td><?=$key+1?></td>
                       	 	<td><?=$daimondVal['diamonds']?></td>
                       	 	<td><?=$daimondVal['amount']?></td>
                       	 	<td><?=$daimondVal['free_call']?></td>
                       	 	<td>
                       	 		<!-- data-toggle="modal" data-target="#myModal" -->
                       	 	<a    data-toggle="modal" data-target="#editmyModal<?php echo $daimondVal['id'];?>" class="btn btn-success">Edit</a>

                    <a   onclick="return  confirm('Are you sure want to delete this?');" href="<?php echo base_url();?>Admin/Users/deleteDiamond?Id=<?php echo $daimondVal['id'];?>"  class="btn btn-danger">Delete</a>

                       	 	</td>
                       	 </tr>
                       	 
                       	 
                       	 	<!-- Modal -->
  <div class="modal fade" id="editmyModal<?php echo $daimondVal['id'];?>" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Edit Diamond</h4>
        </div>

      	<form method="post" onsubmit="return edit('<?php echo $daimondVal['id'];?>');" id="edit<?php echo $daimondVal['id'];?>" autocomplete="off">		
       	<div id="MessageUpdateErr<?php echo $daimondVal['id'];?>"></div>
	        <div class="modal-body">
	        <input type="hidden" name="Id" value="<?php echo $daimondVal['id'];?>" />	
			   <div class="form-group">
			    <label for="">Diamond</label>
			    <input type="number" class="form-control" name="diamonds" required placeholder="Diamond" min="1" value="<?php echo $daimondVal['diamonds'];?>">
			  </div>
			  <div class="form-group">
			    <label for="">Amount</label>
			    <input type="number" class="form-control" name="amount" required placeholder="Amount" min="1" value="<?php echo $daimondVal['amount'];?>">
			  </div>
			  <div class="form-group">
			    <label for="">Free Card</label>
			    <input type="number" class="form-control" name="free_call" placeholder="Free card" min="0" value="<?php echo $daimondVal['free_call'];?>">
			  </div>

	        </div>

	        <div class="modal-footer">
	          <button  type="submit" class="btn btn-primary" id="AddBtn<?php echo $daimondVal['id'];?>">Update</button>	
	          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        </div>
        </form>
      </div>
      
    </div>
  </div> 
  
  
                       <?php } } ?>
                     </tbody>	

					</table>
				</div>
				</div>
			</div>

		</div>
    </section>
</div>
 
 <?php include_once('include/footer.php'); ?>
 
 <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add Daimond</h4>
        </div>

       <form method="post" onsubmit="return Add();" id="Add" autocomplete="off">		
       	<div id="MessageErr"></div>
	        <div class="modal-body">
			   <div class="form-group">
			    <label for="">Diamond</label>
			    <input type="number" class="form-control" name="diamonds" required placeholder="Diamond" min="1">
			  </div>
			  <div class="form-group">
			    <label for="">Amount</label>
			    <input type="number" class="form-control" name="amount" required placeholder="Amount" min="1">
			  </div>
			  <div class="form-group">
			    <label for="">Free Card</label>
			    <input type="number" class="form-control" name="free_call" placeholder="Free card" min="0">
			  </div>

	        </div>

	        <div class="modal-footer">
	          <button  type="submit" class="btn btn-primary" id="AddBtn">Add</button>	
	          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        </div>
        </form>
      </div>
      
    </div>
  </div> 
<script>
 function Add() {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Users/AddDiamond",
    type:"POST",
    cache:false,
        contentType: false,
        processData: false,
    data:new FormData($('#Add')[0]),
    dataType:'json',
    beforeSend:function(){
      $('#AddBtn').prop('disabled',true);
      $('#AddBtn').text('processing...');
      
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

function edit(FormId) {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Users/EditDiamond",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#edit'+FormId)[0]),
    dataType:'json',
    beforeSend:function(){
      $('#AddBtn'+FormId).prop('disabled',true);
      $('#AddBtn'+FormId).text('processing...');
    },
    success:function(data) {
      if(data.status==1){
       location.reload();
      } 
      else {
        $("#MessageUpdateErr"+FormId).html(data.message);
        $('#AddBtn'+FormId).prop('disabled',false);
        $('#AddBtn'+FormId).text('Update');
      }
    }
  });
    return false;

}
</script>
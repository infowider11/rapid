<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Agent Comission<small>management</small></h1>
		
    </section>

    <!-- Main content -->
    <section class="content">
		<div class="error-msg"></div>
		<div class="row">
			<!-- left column -->
			<div class="col-md-12">
				<!-- general form elements -->
				<?php echo $this->session->flashdata('msgs'); ?>
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Comission List</h3>
						<div style="float: right;"><a href="javascript:void(0)" class="btn btn-success" data-toggle="modal" data-target="#myModal">Add New</a></div>
					</div>
						
					<div class="table-responsive" style="padding: 20px;">			
					<table class="table table-striped table-bordered DataTable">
						<thead>
						<tr>
							<th>S. No.</th>
							<th>Amount</th>
							<th>Percent (%)</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
                       <?php
$x = 1;                       
                        foreach($data as $value){ ?>
                       	 <tr>
                       	 	<td><?php echo $x; ?></td>
                       	 	<td><?php echo $value["amount"]; ?></td> 
                       	 	<td><?php echo $value["percent"]; ?></td>                       	 	
                       	 	<td>
                       	 		<!-- data-toggle="modal" data-target="#myModal" -->
                       	 	<a    data-toggle="modal" data-target="#editmyModal<?php echo $value['id'];?>" class="btn btn-xs btn-success">Edit</a>

                    <a onclick="return  confirm('Are you sure want to delete this value?');" href="<?php echo base_url();?>Admin/Agent/delete_agent_Com?Id=<?php echo $value['id'];?>"  class="btn btn-xs btn-danger">Delete</a>

                       	 	</td>
                       	 </tr>
                       	 
                       	 
                       	 	<!-- Modal -->
  <div class="modal fade" id="editmyModal<?php echo $value['id'];?>" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Edit Agent</h4>
        </div>
	<div id="MessageUpdateErr<?php echo $value['id']; ?>"></div>
       <form method="post" onsubmit="return Edit('<?php echo $value['id']; ?>');" id="Edit<?php echo $value['id']; ?>" autocomplete="off">		

			<input type="hidden" name="Id" value="<?php echo $value['id'];?>">

	        <div class="modal-body">

			  	 <div class="form-group">
			    <label for="">Amount</label>
			    <input type="number" class="form-control" name="amount" required placeholder="Enter amount" min="1" value="<?php echo $value['amount'];?>" />
			  </div>

			  <div class="form-group">
			    <label for="">Percent (%)</label>
			    <input type="number" class="form-control" name="percent" required placeholder="Enter percent" value="<?php echo $value['percent'];?>" min="1"/>
			  </div>

	        </div>

	        <div class="modal-footer">
	          <button type="submit" class="btn btn-info" id="EditBtn<?php echo $value['id'];?>">Update <i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw btn-loadEdit<?php echo $value['id'];?>"></i></button>	
	          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
	        </div>
        </form>
      </div>
      
    </div>
  </div> 
  
  
                       <?php $x++; } ?>
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
          <h4 class="modal-title">Add Agent Comission</h4>
        </div>
        <div id="MessageErr"></div>
       <form method="post" onsubmit="return Add();" id="Add" autocomplete="off">		

	        <div class="modal-body">

			   <div class="form-group">
			    <label for="">Amount</label>
			    <input type="number" class="form-control" name="amount" required placeholder="Enter amount" min="1" />
			  </div>

			  <div class="form-group">
			    <label for="">Percent (%)</label>
			    <input type="number" class="form-control" name="percent" required placeholder="Enter percent" min="1" />
			  </div>

			  </div>

	        <div class="modal-footer">
	          <button type="submit" class="btn btn-info" id="AddBtn">Submit<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw btn-load"></i>
	          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        </div>
        </form>
      </div>
      
    </div>
  </div> 
<script>
 function Add() {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Agent/AddAgentComission",
    type:"POST",
    cache:false,
        contentType: false,
        processData: false,
    data:new FormData($('#Add')[0]),
    dataType:'json',
    beforeSend:function(){
      $('#AddBtn').prop('disabled',true);
      $('.btn-load').show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageErr").html(data.message);
        $('#AddBtn').prop('disabled',false);
        $('.btn-load').hide();
      }
    }
  });
    return false;

}

 function Edit(FormId) {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Agent/EditAgentComission",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#Edit'+FormId)[0]),
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
        $("#MessageUpdateErr"+FormId).html(data.message);
        $('#EditBtn'+FormId).prop('disabled',false);
        $('.btn-loadEdit'+FormId).hide();
      }
    }
  });
    return false;

}
</script>
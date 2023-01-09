<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Trade<small>management</small></h1>
		
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
						<h3 class="box-title">Trade List</h3><br>
						<?php echo $this->session->flashdata('msgs'); ?>

						<div style="float: right;">
							<a href="javascript:void(0)" class="btn btn-success btn-xs" data-toggle="modal" data-target="#myModalRemove">Remove trade balance</a>
							<a href="javascript:void(0)" class="btn btn-success btn-xs" data-toggle="modal" data-target="#myModal">Add trade balance</a></div>
					</div>
					<div id="MessageUpdateErr"></div>	
					<div class="table-responsive" style="padding: 20px;">			
					<table class="table table-striped table-bordered DataTable">
						<thead>
						<tr>
							<th>S.No.</th>
							<th>Send by</th>
							<th>Send to</th>
							<th>Trade</th>
						</tr>
					</thead>
					<tbody>
                       <?php
                        if($trade){ 
                        foreach($trade as $key=>$value){ ?>
                       	 <tr>
                       	 	<td><?=$key+1?></td>
                       	 	<td><?php
                       	 	if ($value['send_by'] ==0) {
                       	 		echo "Admin";
                       	 	} else {
$UserData = $this->common_model->GetSingleData('users', array('id'=>$value['send_by']));
 if ($UserData["nickname"] !="") {
?>
<a href="<?php echo base_url(); ?>admin/users?UserId=<?php echo $value['send_by']; ?>" ><?php echo $UserData["nickname"]; ?></a><br>
<?php
echo $UserData["uniqueID"];
} else {
	?>
<a href="<?php echo base_url(); ?>admin/users?UserId=<?php echo $value['send_by']; ?>" >Profile not updated</a><br>
	<?php
	echo $UserData["uniqueID"];
}

}
?>
</td>

                       	 		<td><?php
$UserData1 = $this->common_model->GetSingleData('users', array('id'=>$value['send_to']));
 if ($UserData1["nickname"] !="") {
?>
<a href="<?php echo base_url(); ?>admin/users?UserId=<?php echo $value['send_to']; ?>" ><?php echo $UserData1["nickname"]; ?></a><br>
<?php
} else {
	?>
<a href="<?php echo base_url(); ?>admin/users?UserId=<?php echo $value['send_to']; ?>" >Profile not updated</a><br>
	<?php
}
echo $UserData1["uniqueID"]; 
?>
</td>
                       	 	<td>
<?php echo $value['diamonds']; ?>
</td>                      	 	
                       	 </tr>
  
                       <?php } } ?>
                     </tbody>	

					</table>
				</div>
				</div>
			</div>

		</div>
    </section>
</div>
 <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add Trade Balance</h4>
        </div>

       <form method="post"  onsubmit="return Add();" id="Add" autocomplete="off">		

	        <div class="modal-body">
	        	<div id="MessageErr"></div>
	        	<div class="form-group">
			    <label for="">User Id</label>
			    <input class="form-control" type="text" name="user_id" placeholder="Enter user id" id="user_id" />
			    <div id="UserName"></div>
			  </div>
			  <input type="hidden" name="type" value="trade">
			  <div class="form-group">
			    <label for="">Amount</label>
			    <input type="number" class="form-control" name="trade_amount" min="1" required placeholder="Amount">
			  </div>

	        </div>

	        <div class="modal-footer">
	          <button type="submit" class="btn btn-default" id="AddBtn" style="display: none;">Add<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw btn-load"></i></button>	
	          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        </div>
        </form>
      </div>
      
    </div>
  </div>  
  
  <div class="modal fade" id="myModalRemove" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Remove Trade Balance</h4>
        </div>

       <form method="post" onsubmit="return RemoveTrade();" id="RemoveTrade" autocomplete="off">		

	        <div class="modal-body">
	        	<div id="RemoveMessageErr"></div>
	        	<div class="form-group">
			    <label for="">User Id</label>
			    <input class="form-control" type="text" name="user_id" placeholder="Enter user id" id="RemoveUser_id" />
			    <div id="RemoveUserName"></div>
			  </div>
			  <div class="form-group">
			    <label for="">Amount</label>
			    <input type="number" class="form-control" name="trade_amount" min="1" required placeholder="Amount">
			  </div>

	        </div>

	        <div class="modal-footer">
	          <button type="submit" class="btn btn-default" id="RemoveBtn" style="display: none;">Remove<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw btn-load"></i></button>	
	          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        </div>
        </form>
      </div>
      
    </div>
  </div>
 <?php include_once('include/footer.php'); ?>
 
<script>
$(document).on('change','#user_id',function(){
var UserId = $(this).val();
	
	$.ajax({
		url : '<?php echo base_url(); ?>Admin/Trade/GetUser',
		method : 'POST',
		data : {UserId:UserId},
		dataType:'json',
		success: function(data) {
			if (data.status==1) {
			$("#AddBtn").css("display","inline-block");
			}
			$('#UserName').html(data.message);
		}
	});

});

$(document).on('change','#RemoveUser_id',function(){
var RemoveUser_id = $(this).val();
	
	$.ajax({
		url : '<?php echo base_url(); ?>Admin/Trade/GetUser',
		method : 'POST',
		data : {UserId:RemoveUser_id},
		dataType:'json',
		success: function(data) {
			if (data.status==1) {
			$("#RemoveBtn").css("display","inline-block");
			}
			$('#RemoveUserName').html(data.message);
		}
	});

});

function Add() {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Trade/AddTrade",
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

function RemoveTrade() {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Trade/RemoveTrade",
    type:"POST",
    cache:false,
        contentType: false,
        processData: false,
    data:new FormData($('#RemoveTrade')[0]),
    dataType:'json',
    beforeSend:function(){
      $('#RemoveBtn').prop('disabled',true);
      $('.btn-load').show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#RemoveMessageErr").html(data.message);
        $('#RemoveBtn').prop('disabled',false);
        $('.btn-load').hide();
      }
    }
  });
    return false;

} 
</script>

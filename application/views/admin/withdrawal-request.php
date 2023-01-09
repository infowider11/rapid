<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1><?php echo $content; ?> Withdrawal<small>management</small></h1>
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
						<h3 class="box-title">Withdrawal management</h3>
						<form class="form_login" method="get">
						    
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
                          <a href="<?php echo site_url().'admin/withdrawal-request'; ?>" class="btn btn-primary py-3 px-5" style="width:auto; height:auto">Reset Filter</a>
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
							<th>User Name</th>
							<th>Amount</th>
							<th>Country</th>
							<th>Payment Id</th>
							<th>Data</th>
							<th>Date</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
<?php
if($data){
$x=1;
foreach ($data as $value){
	$userdata = $this->common_model->GetColumnName('users',array('id'=>$value['user_id']),array('id','nickname', 'uniqueID'));
?>

	

		<tr>
			<td><?php echo $x; ?></td>
			<td>
<?php if ($userdata["nickname"] !="") {
?>
<a href="<?php echo base_url(); ?>admin/users?UserId=<?php echo $value['user_id']; ?>" ><?php echo $userdata["nickname"]; ?></a><br>
<?php
echo $userdata["uniqueID"];
} else {
	echo "Profile not updated";
}?></td>
	        <td><?php echo $value["amount"]; ?></td>					
	        <td><?php
	$countryName = $this->common_model->GetSingleData('country', array('id'=>$value["country"]));
	echo $countryName["nicename"];
	 ?></td>					
	        <td><?php
$paymentName = $this->common_model->GetSingleData('payment_form', array('id'=>$value["payment_id"]));
	echo $paymentName["name"];
	         ?></td>
	        <td><?php 
$payment_data = unserialize($value['payment_data']);
foreach($payment_data as $key1 => $value1){
			$formConent = $this->common_model->GetSingleData('payment_form_field', array('id'=>$value1["id"]));
			if($formConent){
				if ($formConent["field_type"] == "file") {
				$file = "<a download href='".base_url()."assets/media/".$value1["value"]."'>download</a>";	
				} else {
					$file = $value1["value"];
				}
				echo $formConent["label"]." : ".$file."<br>";
				
			} else {
				echo $value1["label"]." : ".$value1["value"]."<br>";
			}
					
	}
	    ?></td>	
	    <td><?php echo $value["create_date"]; ?></td>
	    <td>
<?php
if($value["status"] == 0){
echo "<span class='btn btn-danger btn-xs'>Pending</span>";
} elseif($value["status"] == 1){
	echo "<span class='btn btn-success btn-xs'>Approved</span>";
} else {
echo "<span class='btn btn-danger btn-xs'>Rejected</span>";
}
?>
	    </td>
	    <td>	    
<?php
if ($value["status"]=="0") {
?>
<a class="btn btn-danger btn-xs" onclick="return confirm('Are you sure?')" href="<?php echo base_url();?>Admin/FormBuilder/withdrawalAccept?id=<?php echo $value["id"];?>" >Accepted</a>

<button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal1<?php echo $value["id"];?>">Reject</button>
<?php
} elseif ($value["status"]=="2") {
?>
<button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal2<?php echo $value["id"];?>">View Reason</button>
<?php
}

?>
	    
	    </td>				
		</tr>

<div class="modal" id="myModal1<?php echo $value["id"];?>">
  <div class="modal-dialog ">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Reject Request of User : <?php echo $userdata["nickname"]; ?></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

  <form method="post" onsubmit="return RejectRequest('<?php echo $value["id"]; ?>');" id="RejectRequest<?php echo $value["id"]; ?>" enctype="multipart/form-data" >    
          <!-- Modal body -->
      <div class="modal-body">
      <div id="MessageUpdateErr<?php echo $value["id"]; ?>" style="color: red; text-align: center;"></div>  

        <div class="form-group">
            <label >Reasone :</label>
            <textarea class="form-control" name="reason" placeholder="Reason for reject"></textarea>
            <input type="hidden" name="id" value="<?php echo $value["id"]; ?>" />
          </div>  
        

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="EditVehicleBtn<?php echo $value["id"]; ?>">Update<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw" id="btn-loadEdit<?php echo $value["id"]; ?>"></i></button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
          </form>
    </div>
  </div>
</div>

<div class="modal" id="myModal2<?php echo $value["id"];?>">
  <div class="modal-dialog ">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Reject Reason for User : <?php echo $userdata["nickname"]; ?></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

          <!-- Modal body -->
      <div class="modal-body">

        <div class="form-group">
            <label > <?php echo $value["reason"]; ?></label>
          </div>  
        

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">

        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
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
 
<?php include_once('include/footer.php'); ?>
<script>
function RejectRequest(FormId) {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/FormBuilder/withdrawalReject",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#RejectRequest'+FormId)[0]),
    dataType:'json',
    beforeSend:function(){
      $('#EditVehicleBtn'+FormId).prop('disabled',true);
      $('#btn-loadEdit'+FormId).show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageUpdateErr"+FormId).html(data.message);
        $('#EditVehicleBtn'+FormId).prop('disabled',false);
        $('#btn-loadEdit'+FormId).hide();
      }
    }
  });
    return false;

}
</script>
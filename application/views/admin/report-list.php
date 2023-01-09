<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Report<small>management</small></h1>
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
						<h3 class="box-title">Report management</h3>
						<?php echo $this->session->flashdata('msgs'); ?>
            <div class="row hide">
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
							<th>Reported by</th>
							<th>Reported to</th>
              <th>Category</th>
              <th>Message</th>
              <th>Date</th>
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
	    <td><?php
$report_by = $this->common_model->GetSingleData('users', array('id'=>$value["report_by"]));
 if ($report_by["nickname"] !="") {
?>
<a href="<?php echo base_url(); ?>admin/users?UserId=<?php echo $value["report_by"]; ?>" ><?php echo $report_by["nickname"]; ?></a><br>
<?php
} else {
  ?>
<a href="<?php echo base_url(); ?>admin/users?UserId=<?php echo $value['report_by']; ?>" >Profile not updated</a><br>
  <?php
}
echo $report_by["uniqueID"]; ?></td>
	    <td><?php 
$report_to = $this->common_model->GetSingleData('users', array('id'=>$value["report_to"]));
 if ($report_to["nickname"] !="") {
?>
<a href="<?php echo base_url(); ?>admin/users?UserId=<?php echo $value["report_to"]; ?>" ><?php echo $report_to["nickname"]; ?></a><br>
<?php
} else {
  ?>
<a href="<?php echo base_url(); ?>admin/users?UserId=<?php echo $value['report_to']; ?>" >Profile not updated</a><br>
  <?php
}
echo $report_to["uniqueID"];
    ?></td>					     
	    <td><?php
$category = $this->common_model->GetSingleData('reporting_category', array('id'=>$value["category"]));
     echo $category["name"]; ?></td>
      <td><?php echo $value["message"]; ?></td>
      <td><?php echo $value["report_on"]; ?></td>
	    <td>
<?php
if ($value["status"] ==0) {
?>
<button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal<?php echo $value["id"];?>"><i class="fa fa-ban"></i></button>
<?php
}
  
?>
        </td>				
		</tr>

<div class="modal" id="myModal<?php echo $value["id"];?>">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Action on report</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div id="MessageErr<?php echo $value["id"]; ?>"></div>
      <form method="post" onsubmit="return Block('<?php echo $value["id"]; ?>');" id="Block<?php echo $value["id"]; ?>" enctype="multipart/form-data">
      <div class="modal-body">
        <div class="form-group">
              <label class="control-label">Block To</label>  
              <select class="form-control" required="" name="block_to">
                <option value="">---Select---</option>
                <option value="<?php echo $value["report_by"]; ?>">Reported By</option>
                <option value="<?php echo $value["report_to"]; ?>">Reported To</option>
              </select>  
              <input type="hidden" name="report_id" value="<?php echo $value["id"]; ?>" /> 
        </div>
        <div class="form-group">
              <label class="control-label">Block For</label>  
              <select class="form-control" required="" name="blocked_for">
                <option value="">---Select---</option>
                <option value="live_party">Live Party</option>
                <option value="live_stream">Live Stream</option>
                <option value="chat">Chat</option>
                <option value="video_call">Video Call</option>
              </select>   
        </div>
        <div class="form-group">
              <label class="control-label">Till Date</label>  
              <input type="date" name="till_date" class="form-control" required="" min="<?php echo date('Y-m-d'); ?>">  
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button class="btn btn-info" type="submit" name="submit" id="submit<?php echo $value["id"]; ?>">Submit<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw" id="btn_load<?php echo $value["id"]; ?>"></i></button>
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
 
<?php include_once('include/footer.php'); ?>
<script>
function Block(ID) {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Report/BlockAction",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#Block'+ID)[0]),
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
        $('#btn-load'+ID).hide();
      }
    }
  });
    return false;

}
</script>
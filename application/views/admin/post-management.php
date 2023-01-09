<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Post<small>management</small></h1>
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
						<h3 class="box-title">Post management</h3>
						<?php echo $this->session->flashdata('msgs'); ?>
           
					</div>
					<!-- /.box-header -->
					<!-- form start -->
					<div id="MessageUpdateErr"></div>	
					<div class="table-responsive" style="padding: 20px;">			
					<table class="table table-striped table-bordered DataTable">
						<thead>
						<tr>
							<th>S. No.</th>
							<th>Created by</th>
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
	
	$images = $this->common_model->GetAllData('post_image',array('post_id'=>$value['id']),'id','asc',null,null,array('*',"CONCAT('" .site_url() ."',name) AS image"));
	
	$created_by = $this->common_model->GetSingleData('users',array('id'=>$value['user_id']));
	if($created_by){
?>

	

		<tr>
			<td><?php echo $x; ?></td>
	    <td>
				<?php if ($created_by["nickname"] !="") { ?>
				<a href="<?php echo base_url(); ?>admin/users?UserId=<?php echo $value["report_by"]; ?>" ><?php echo $created_by["nickname"]; ?></a><br>
				<?php } else { ?>
				<a href="<?php echo base_url(); ?>admin/users?UserId=<?php echo $value['report_by']; ?>" >Profile not updated</a><br>
				<?php }
				echo $created_by["uniqueID"]; ?>
			</td>				     
      <td><?php echo $value["message"]; ?></td>
      <td><?php echo date('d M y h:i A',strtotime($value["created_at"])); ?></td>
	    <td>
	<?php if(!empty($images)) { ?>
<button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModalV<?php echo $value["id"];?>"><i class="fa fa-eye"></i> View Images</button>
	<?php } ?>
        </td>				
		</tr>

<div class="modal" id="myModalV<?php echo $value["id"];?>">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Post</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      
      <div class="modal-body">
			
				<?php if(!empty($images)) { ?>
				<div class="row">
					<?php foreach($images as $img) { ?>
					<div class="col-md-3">
						<img style="width:100%; height:150px"  src="<?php echo $img['image']?>">
					</div>
					<?php } ?>
				</div>
				<?php } ?>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php $x++; } } } ?>					</tbody>	
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
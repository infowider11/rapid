<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Merchant<small>management</small></h1>
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
						<h3 class="box-title">Merchant List</h3>
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
							<th>Email</th>
              <th>Phone</th>
              <th>Unique Id</th>
              <th>Merchant Authorization Key</th>
              <th>Description</th>
              <th>Diamond</th>
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
	    <td><?php echo $value["email"]; ?></td>	
      <td><?php echo $value["phone"]; ?></td>				     
	    <td><?php echo $value["unique_id"]; ?></td> 
      <td><?php echo $value["authorization_key"]; ?></td>  
      <td><?php echo $value["description"]; ?></td>  
      <td><button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#diamondModal<?php echo $value["id"];?>">Add</button>
        <button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#diamondRModal<?php echo $value["id"];?>">Remove</button>
      <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#listModal<?php echo $value["id"];?>">List</button>
    </td>
      <td>
<button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal<?php echo $value["id"];?>"><i class="fa fa-edit"></i></button>
      	
 <a class="btn btn-danger btn-xs" onclick="return confirm('Are you sure want to delete this?')" href="<?php echo base_url();?>Admin/Merchant/deleteMerchant?id=<?php echo $value["id"];?>" >
<i class="fa fa-trash" aria-hidden="true"></i>
</a>	
<button type="button" class="btn btn-primary btn-xs" onclick="return revoke('<?php echo $value["id"];?>');" id="revoke<?php echo $value["id"];?>">Revoke <i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw" id="revoke_load<?php echo $value["id"];?>"></i></button>
</td>  	
		</tr>
<div class="modal" id="listModal<?php echo $value["id"];?>">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Daimond History</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->

      <div class="modal-body">
          <div class="row">
            <div class="col-md-4"><b>Sl. No.</b></div>
            <div class="col-md-4"><b>Diamond</b></div>
            <div class="col-md-4"><b>Date</b></div>
          </div>
<?php
$diamond = $this->common_model->GetAllData('merchant_diamond',array('merchant_id'=>$value["id"]),'id','desc');
$x1 = 1;
if ($diamond) {


foreach ($diamond as $key => $value1) {
?>
        <div class="row">
          <div class="col-md-4"><?php echo $x1; ?></div>
            <div class="col-md-4" <?php if($value1["type"]==1){echo "style='color:green'";} else {echo "style='color:red'";} ?>><?php echo $value1["diamond"]; ?></div>
            <div class="col-md-4"><?php echo $value1["created_at"]; ?></div>
        </div>
<?php
$x1++;
} }
else {
?>  
        <div class="row">
          <div class="col-md-12 text-center">No record found.</div>
        </div>
<?php
}
?>          
          

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal" id="diamondModal<?php echo $value["id"];?>">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add Daimond</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <form method="post" onsubmit="return Diamond('<?php echo $value["id"]; ?>');" id="Diamond<?php echo $value["id"]; ?>" enctype="multipart/form-data">
      <div class="modal-body">
          <div class="alert alert-warning">Total Daimond : <?php echo $value["diamond"]; ?></div>
          <div id="MessageDErr<?php echo $value["id"]; ?>"></div>

          <div class="form-group">
              <label>Diamond :</label>
              <input type="number" class="form-control" name="diamond" placeholder="Add diamond" required="" />
              <input type="hidden" name="merchant_id" value="<?php echo $value["id"]; ?>" />
          </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button class="btn btn-info" type="submit" name="submit" id="submitD<?php echo $value["id"]; ?>">Submit<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw" id="btn_loadD<?php echo $value["id"]; ?>"></i></button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      </form>
    </div>
  </div>
</div>

<div class="modal" id="diamondRModal<?php echo $value["id"];?>">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Remove Daimond</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <form method="post" onsubmit="return RemoveDiamond('<?php echo $value["id"]; ?>');" id="RemoveDiamond<?php echo $value["id"]; ?>" enctype="multipart/form-data">
      <div class="modal-body">
          <div class="alert alert-warning">Total Daimond : <?php echo $value["diamond"]; ?></div>
          <div id="MessageRErr<?php echo $value["id"]; ?>"></div>

          <div class="form-group">
              <label>Diamond :</label>
              <input type="number" class="form-control" name="diamond" placeholder="Remove diamond" required="" min="1" />
              <input type="hidden" name="merchant_id" value="<?php echo $value["id"]; ?>" />
          </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button class="btn btn-info" type="submit" name="submit" id="submitRD<?php echo $value["id"]; ?>">Submit<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw" id="btn_loadRD<?php echo $value["id"]; ?>"></i></button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      </form>
    </div>
  </div>
</div>

<div class="modal" id="myModal<?php echo $value["id"];?>">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Edit Merchant</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <form method="post" onsubmit="return Edit('<?php echo $value["id"]; ?>');" id="Edit<?php echo $value["id"]; ?>" enctype="multipart/form-data">
      <div class="modal-body">
          <div id="MessageErr<?php echo $value["id"]; ?>"></div>
          <div class="form-group">
              <label >Name :</label>
              <input type="text" class="form-control" placeholder="Enter name" name="name" value="<?php echo $value["name"]; ?>" required />
              <input type="hidden" name="id" value="<?php echo $value["id"]; ?>">
          </div>
          <div class="form-group">
              <label >Email :</label>
              <input type="email" class="form-control" placeholder="Enter email" name="email" value="<?php echo $value["email"]; ?>" required />
          </div>

          <div class="form-group">
              <label>Phone :</label>
              <input type="text" class="form-control" placeholder="Enter phone" name="phone" value="<?php echo $value["phone"]; ?>" required />
          </div>

          <div class="form-group">
              <label>Description :</label>
              <textarea rows="5" class="form-control" placeholder="Enter description" name="description" required><?php echo $value["description"]; ?></textarea>
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
 

 <div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add Merchant</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <form method="post" onsubmit="return Add();" id="Add" enctype="multipart/form-data" >
      <div class="modal-body">
        <div id="MessageErr"></div>
        <div class="form-group">
            <label >Name :</label>
            <input type="text" class="form-control" placeholder="Enter name" name="name" required />
        </div>


        <div class="form-group">
            <label >Email :</label>
            <input type="email" class="form-control" placeholder="Enter email" name="email" required />
        </div>

        <div class="form-group">
            <label>Phone :</label>
            <input type="text" class="form-control" placeholder="Enter phone" name="phone" required />
        </div>

        <div class="form-group">
            <label>Description :</label>
            <textarea rows="5" class="form-control" placeholder="Enter description" name="description" required></textarea> 
        </div>


      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id="submit">Submit<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw" id="btn_load"></i></button>
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
    url: "<?php echo base_url(); ?>Admin/Merchant/AddMerchant",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#Add')[0]),
    dataType:'json',
    beforeSend:function(){
      $('#submit').prop('disabled',true);
      $('#btn_load').show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageErr").html(data.message);
        $('#submit').prop('disabled',false);
        $('#btn_load').hide();
      }
    }
  });
    return false;

}



function Edit(ID) {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Merchant/EditMerchant",
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


function Diamond(ID) {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Merchant/AddDiamond",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#Diamond'+ID)[0]),
    dataType:'json',
    beforeSend:function(){
      $('#submitD'+ID).prop('disabled',true);
      $('#btn_loadD'+ID).show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageDErr"+ID).html(data.message);
        $('#submitD'+ID).prop('disabled',false);
        $('#btn_loadD'+ID).hide();
      }
    }
  });
    return false;

}


function RemoveDiamond(ID) {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Merchant/removeDiamond",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#RemoveDiamond'+ID)[0]),
    dataType:'json',
    beforeSend:function(){
      $('#submitRD'+ID).prop('disabled',true);
      $('#btn_loadRD'+ID).show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageRErr"+ID).html(data.message);
        $('#submitRD'+ID).prop('disabled',false);
        $('#btn_loadRD'+ID).hide();
      }
    }
  });
    return false;

}

function revoke(id) {
  //alert(id);
  if (confirm('Are you sure!!')) {
   $.ajax({
    url: "<?php echo base_url(); ?>Admin/Merchant/revoke",
    type:"POST",
    data: {id:id},
    dataType:'json',
    beforeSend:function(){
      $('#revoke'+id).prop('disabled',true);
      $('#revoke_load'+id).show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        alert(data.message);
        $('#revoke'+id).prop('disabled',false);
        $('#revoke_load'+id).hide();
      }
    }
  });

   }
    return false;
}

</script>
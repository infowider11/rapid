<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Language<small>management</small></h1>
		
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
						<h3 class="box-title">Language List</h3>
						<div style="float: right;"><a href="javascript:void(0)" class="btn btn-success" data-toggle="modal" data-target="#myModal">Add New</a></div>
					</div>
					<div id="MessageUpdateErr"></div>	
					<div class="table-responsive" style="padding: 20px;">			
					<table class="table table-striped table-bordered DataTable">
						<thead>
						<tr>
							<th>SNo.</th>
							<th>Name</th>
							<th>Code</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
                       <?php
                        if($language){ 
                        foreach($language as $key=>$lang){ ?>
                       	 <tr>
                       	 	<td><?=$key+1?></td>
                       	 	<td id="edit_lang_<?=$lang['id']?>"><?=$lang['name']?></td>
                       	 	<td><?=$lang['code']?></td>
                       	 	<td>
                       	 		<!-- data-toggle="modal" data-target="#myModal" -->
                       	 		<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal<?php echo $lang["id"];?>"><i class="fa fa-edit"></i></button> 
                       	 		<a href="javascript:void(0)" class="btn btn-danger" onclick="delete_language(<?=$lang['id']?>)" >Delete</a>
                       	 	</td>
                       	 </tr>
  <div class="modal fade" id="myModal<?php echo $lang["id"];?>" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Edit Language</h4>
        </div>
        <div id="MessageErr<?php echo $lang["id"];?>"></div>
        <form method="post" onsubmit="return Edit('<?php echo $lang["id"];?>');" id="Edit<?php echo $lang["id"];?>">
	        <div class="modal-body">
			   <div class="form-group">
			    <label for="email">Name</label>
			    <input type="text" class="form-control" id="laguage" name="name" placeholder="Language name" value="<?=$lang['name']?>"  required/>
			  </div>

			 <div class="form-group">
			    <label for="email">Code</label>
			    <input type="text" class="form-control" id="code" name="code" placeholder="Language code" value="<?=$lang['code']?>">
			    <input type="hidden" name="id" value="<?=$lang['id']?>" />
			  </div>
	        </div>
	        <div class="modal-footer">
<button type="submit" class="btn btn-primary" id="AddVehicleBtn<?php echo $lang["id"];?>">Update<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw" id="btn-load<?php echo $lang["id"];?>"></i></button>	          
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
 

  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add Language</h4>
        </div>
        <div id="MessageErr"></div>
        <form method="post" onsubmit="return AddVehicle();" id="AddVehicle">
	        <div class="modal-body">
			   <div class="form-group">
			    <label for="email">Name</label>
			    <input type="text" class="form-control" id="laguage" name="name" placeholder="Language name">
			  </div>

			 <div class="form-group">
			    <label for="email">Code</label>
			    <input type="text" class="form-control" id="code" name="code" placeholder="Language code">
			  </div>
	        </div>
	        <div class="modal-footer">
<button type="submit" class="btn btn-primary" id="AddVehicleBtn">Submit<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw btn-load"></i></button>	          
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        </div>
        </form>
      </div>
      
    </div>
  </div> 


    <!-- Modal -->
  <div class="modal fade" id="edit_language_modal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Edit Language</h4>
        </div>
        <form action="">
	        <div class="modal-body">
			   <div class="form-group">
			    <label for="email">name</label>
			    <input type="text" class="form-control" id="edit_laguage" >
			    <input type="hidden" class="form-control" id="edit_id" >
			  </div>
	        </div>
	        <div class="modal-footer">
	          <button type="button" class="btn btn-default" onclick="edit_language()">Submit</button>	
	          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        </div>
        </form>
      </div>
      
    </div>
  </div> 
<?php include_once('include/footer.php'); ?>

<script>
   function delete_language(id)
   {
   	if(confirm('Are you sure?'))
   	{
		$.ajax({
	        type: "POST",
	        url: "<?php echo site_url('Admin/Users/delete_language'); ?>",
	        data: {id:id},
	        dataType: "json",
	        success: function(data){
	        	if(data.status == 1)
	        	{
	              location.reload();
	        	}
	        	else
	        	{
	        		$('.error-msg').html(data.message);
	        	}
	            
	        },
	        
	   });
   	}

   }

   

   function get_language(id)
   {
   	    var lang = $('#edit_lang_'+id).text()
       
        $('#edit_laguage').val(lang)
        $('#edit_id').val(id)

        $('#edit_language_modal').modal('show')
        
   }

    


function AddVehicle() {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Users/add_language",
    type:"POST",
    cache:false,
        contentType: false,
        processData: false,
    data:new FormData($('#AddVehicle')[0]),
    dataType:'json',
    beforeSend:function(){
      $('#AddVehicleBtn').prop('disabled',true);
      $('.btn-load').show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageErr").html(data.message);
        $('#AddVehicleBtn').prop('disabled',false);
        $('.btn-load').hide();
      }
    }
  });
    return false;

} 

function Edit(id) {
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Users/edit_language",
    type:"POST",
    cache:false,
        contentType: false,
        processData: false,
    data:new FormData($('#Edit'+id)[0]),
    dataType:'json',
    beforeSend:function(){
      $('#AddVehicleBtn'+id).prop('disabled',true);
      $('#btn-load'+id).show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageErr"+id).html(data.message);
        $('#AddVehicleBtn'+id).prop('disabled',false);
        $('#btn-load'+id).hide();
      }
    }
  });
    return false;

} 
</script>
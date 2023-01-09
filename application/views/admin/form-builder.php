<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Form Builder<small>management</small></h1>
		
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
						<h3 class="box-title">Form Builder List</h3>
						<div style="float: right;">
							<a href="javascript:void(0)" class="btn btn-success" data-toggle="modal" data-target="#myModal">Add New</a>
						</div>
					</div>
						
					<div class="table-responsive" style="padding: 20px;">			
					<table class="table table-striped table-bordered DataTable">
						<thead>
						<tr>
							<th>S. No.</th>
							<th>Form Name</th>
							<th>Notes</th>
							<th>Description</th>
							<th>Country</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
                       <?php
$AllCountry = $this->common_model->GetAllData('country', '','nicename','asc');                       
$x = 1;                       
                        foreach($data as $form){ ?>
                       	 <tr>
                       	 	<td><?php echo $x; ?></td>
                       	 	<td><?php echo $form["name"]; ?></td>
                       	 	<td><?php echo $form["note"]; ?></td>
                       	 	<td><?php echo $form["description"]; ?></td>
                       	 	<td><?php
$parts = explode(',',$form["country"]);

foreach ($parts as $key => $value) {
	$countryName = $this->common_model->GetSingleData('country', array('id'=>$value));
	echo $countryName["nicename"].", ";
}
?></td>
                       	 	<td>   
<button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal<?php echo $form["id"]; ?>"><i class="fa fa-eye"></i></button>

	<a type="button" class="btn btn-primary btn-xs" href="<?php echo base_url();?>admin/form-edit/<?php echo $form['id'];?>"><i class="fa fa-edit"></i></a> 

                    <a onclick="return  confirm('Are you sure want to delete this Users?');" href="<?php echo base_url();?>Admin/FormBuilder/delete?Id=<?php echo $form['id'];?>"  class="btn btn-xs btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>
                       	 	</td>
                       	 </tr>
 <div class="modal" id="myModal<?php echo $form["id"]; ?>">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Form View</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">

       <form >
       	<div class="form-group">
       		<label><?php echo $form["name"]; ?></label>
       	</div>
       	<div class="form-group">
       		<label><?php echo $form["note"]; ?></label>
       	</div>
       	<div class="form-group">
       		<label><?php echo $form["description"]; ?></label>
       	</div>
<?php 
$formContent = $this->common_model->GetAllData('payment_form_field', array('payment_form_id'=>$form["id"]));
foreach ($formContent as $key => $value) {
?>
			  <div class="form-group">
       		<label><?php echo $value["label"] ?></label>
       		<input class="form-control" type="<?php echo $value["field_type"] ?>" name="<?php echo $value["name"] ?>" <?php if ($value["is_required"] == 1) { echo required; } ?> placeholder="<?php echo $value["placeholder"] ?>">
       	</div>
<?php
}
?>       
			<div class="form-group">
				<button type="button">Submit</button>
			</div>
       </form>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

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
          <h4 class="modal-title">Add Form Content</h4>
        </div>
        <div id="MessageErr"></div>
       <form method="post" onsubmit="return Add();" id="Add" autocomplete="off">		

	        <div class="modal-body">

			   <div class="form-group">
			    <label for="">Form Name</label>
			    <input type="text" class="form-control" name="name1" required placeholder="Form name" required="" />
			  </div>

			  <div class="form-group">
			    <label for="">Form Note</label>
			    <input type="text" class="form-control" name="note" required placeholder="Form note" />
			  </div>

			  <div class="form-group">
			    <label for="">Description</label>
			    <textarea class="form-control ckeditor" name="description" required placeholder="Form description"></textarea>
			  </div>

			  <div class="form-group">
			    <label for="">Country</label>
			    <select class="form-control multi_select" name="country[]" id="questions_id" multiple="multiple" required="">
<?php
foreach ($AllCountry as $key => $country) {
	?>
						<option value="<?php echo $country["id"]; ?>"><?php echo $country["nicename"]; ?></option>
	<?php
}
?>			    	
			    </select>
			  </div>
			  <div class="AddMoreParent">
			  <hr>
			  <div class="form-group">
			    <label for="">Is Required ?</label>
			    <select class="form-control" name="is_required[]" required="" >
			    	<option value="">---Is required ?---</option>
			    	<option value="1">Yes</option>
			    	<option value="0">No</option>
			    </select>
	    
			  </div>
			  <div class="form-group">
			    <label for="">Field Type</label>
			    <select class="form-control" name="field_type[]" required="">
			    	<option value="">---Select field type---</option>
			    	<option value="text">Text</option>
			    	<option value="file">File</option>
			    	<option value="email">Email</option>
			    	<option value="number">Number</option>

			    </select>
			  </div>
			  <div class="form-group">
			    <label for="">Label</label>
			    <input type="text" class="form-control" name="label[]" required="" placeholder="Label" />		    
			  </div>

			  <div class="form-group">
			    <label for="">Placeholder</label>
			    <input type="text" class="form-control" name="placeholder[]" required="" placeholder="placeholder" />		    
			  </div>

			  <div class="form-group">
			    <label for="">Name <span style="color: red;"> Form name should be unique.</span></label>
			    <input type="text" class="form-control" name="name[]" required="" placeholder="name" />		
			  </div>			  

			  <div id="AddMore"></div>

			  <div class="form-group">
			    <button type="button" id="AddMoreFields">+Add</button>		
			  </div>

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
	function BlockUser(FormId) {

 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Agent/BlockUser",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#BlockUser'+FormId)[0]),
    dataType:'json',
    beforeSend:function(){
      $('#BlockBtn'+FormId).prop('disabled',true);
      $('#Block-load'+FormId).show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageUpdateErr"+FormId).html(data.message);
        $('#BlockBtn'+FormId).prop('disabled',false);
        $('#Block-load'+FormId).hide();
      }
    }
  });
    return false;

}
 function Add() {
 	for (instance in CKEDITOR.instances) {
    CKEDITOR.instances[instance].updateElement();
}
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/FormBuilder/AddForm",
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
 		for (instance in CKEDITOR.instances) {
    CKEDITOR.instances[instance].updateElement();
}
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/FormBuilder/EditFrom",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#Edit'+FormId)[0]),
    dataType:'json',
    beforeSend:function(){
      $('#EditBtn'+FormId).prop('disabled',true);
      $('#btn-loadEdit'+FormId).show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageUpdateErr"+FormId).html(data.message);
        $('#EditBtn'+FormId).prop('disabled',false);
        $('#btn-loadEdit'+FormId).hide();
      }
    }
  });
    return false;

}

my_fun1();

function my_fun1(){
  $('#questions_id').multiselect({
    numberDisplayed: 1,
    includeSelectAllOption: true,
    //maximumSelectionLength:5,
    search: true,
    allSelectedText: 'All country selected',
    nonSelectedText: 'No country selected',
    selectAllValue: 'All',
    selectAllText: 'Select All',
    unselectAllText: 'Unselect All',
    onSelectAll: function(checked) {
      var all = $('#questions_id ~ .btn-group .dropdown-menu .multiselect-all .checkbox');
      all
      // get all child nodes including text and comment
        .contents()
        // iterate and filter out elements
        .filter(function() {
          // check node is text and non-empty
          return this.nodeType === 3 && this.textContent.trim().length;
          // replace it with new text
        }).replaceWith(checked ? this.unselectAllText : this.selectAllText);
    },
    onChange: function() {
        //debugger;
      var select = $(this.$select[0]);
      var dropdown = $(this.$ul[0]);
      var options = select.find('option').length;
      var selected = select.find('option:selected').length;
      var all = dropdown.find('.multiselect-all .checkbox');
      all
      // get all child nodes including text and comment
        .contents()
        // iterate and filter out elements
        .filter(function() {
          // check node is text and non-empty
          return this.nodeType === 3 && this.textContent.trim().length;
          // replace it with new text
        }).replaceWith(options === selected ? this.options.unselectAllText : this.options.selectAllText);
    }
  });

  $('#multiselect').multiselect();
  $('#questions_id').multiselect({
      includeSelectAllOption: true,
      enableFiltering: true,
      enableCaseInsensitiveFiltering: true,
      filterPlaceholder: 'Search for something...'
  });

}

function my_fun(Id){
  $('#questions_idss'+Id).multiselect({
    numberDisplayed: 1,
    // includeSelectAllOption: true,
    maximumSelectionLength:5,
    //search: true,
    allSelectedText: 'All company selected',
    nonSelectedText: 'No company selected',
    selectAllValue: 'All',
    selectAllText: 'Select All',
    unselectAllText: 'Unselect All',
    onSelectAll: function(checked) {
      var all = $('#questions_idss'+Id+' ~ .btn-group .dropdown-menu .multiselect-all .checkbox');
      all
      // get all child nodes including text and comment
        .contents()
        // iterate and filter out elements
        .filter(function() {
          // check node is text and non-empty
          return this.nodeType === 3 && this.textContent.trim().length;
          // replace it with new text
        }).replaceWith(checked ? this.unselectAllText : this.selectAllText);
    },
    onChange: function() {
        //debugger;
      var select = $(this.$select[0]);
      var dropdown = $(this.$ul[0]);
      var options = select.find('option').length;
      var selected = select.find('option:selected').length;
      var all = dropdown.find('.multiselect-all .checkbox');
      all
      // get all child nodes including text and comment
        .contents()
        // iterate and filter out elements
        .filter(function() {
          // check node is text and non-empty
          return this.nodeType === 3 && this.textContent.trim().length;
          // replace it with new text
        }).replaceWith(options === selected ? this.options.unselectAllText : this.options.selectAllText);
    }
  });

  $('#questions_idss'+Id).multiselect();
  $('#questions_idss'+Id).multiselect({
      includeSelectAllOption: true,
      enableFiltering: true,
      enableCaseInsensitiveFiltering: true,
      filterPlaceholder: 'Search for something...'
  });

}

$(document).on("click","#AddMoreFields", function(){
var html ="<div class='RemoveParent'><hr> <div class='form-group'>          <label for=''>Is Required ?</label>          <select class='form-control' name='is_required[]' required='' >            <option value=''>---Is required ?---</option>            <option value='1'>Yes</option>            <option value='0'>No</option>          </select>              </div><div class='form-group'>          <label for=''>Field Type</label>          <select class='form-control' name='field_type[]' required='' placeholder='Field type'>            <option value=''>---Select field type---</option>            <option value='text'>Text</option>            <option value='file'>File</option>            <option value='email'>Email</option>            <option value='number'>Number</option>          </select>        </div>        <div class='form-group'>          <label for=''>Label</label>          <input type='text' class='form-control' name='label[]' required='' placeholder='Label' />               </div>        <div class='form-group'>          <label for=''>Placeholder</label>          <input type='text' class='form-control' name='placeholder[]' required='' placeholder='placeholder' />               </div>        <div class='form-group'>          <label for=''>Name <span style='color: red;'> Form name should be unique.</span></label>          <input type='text' class='form-control' name='name[]' required='' placeholder='name' />           </div>  <button type='button' id='RemoveFields'>-Remove</button></div>";
    $("#AddMore").append(html);
});

$(document).on("click","#RemoveFields", function(){
	$(this).closest(".RemoveParent").remove();
})


$(document).on("click","#AddMoreFields1", function(){
var html ="<div class='RemoveParent'><hr> <div class='form-group'>          <label for=''>Is Required ?</label>          <select class='form-control' name='is_required[]' required='' >            <option value=''>---Is required ?---</option>            <option value='1'>Yes</option>            <option value='0'>No</option>          </select>              </div><div class='form-group'>          <label for=''>Field Type</label>          <select class='form-control' name='field_type[]' required='' placeholder='Field type'>            <option value=''>---Select field type---</option>            <option value='text'>Text</option>            <option value='file'>File</option>            <option value='email'>Email</option>            <option value='number'>Number</option>          </select>        </div>   <input type='hidden' name='formContentId[]' value='0' />     <div class='form-group'>          <label for=''>Label</label>          <input type='text' class='form-control' name='label[]' required='' placeholder='Label' />               </div>        <div class='form-group'>          <label for=''>Placeholder</label>          <input type='text' class='form-control' name='placeholder[]' required='' placeholder='placeholder' />               </div>        <div class='form-group'>          <label for=''>Name <span style='color: red;'> Form name should be unique.</span></label>          <input type='text' class='form-control' name='name[]' required='' placeholder='name' />           </div>  <button type='button' id='RemoveFields'>-Remove</button></div>";
    $("#AddMore1").append(html);
});

$(document).on("click","#RemoveFields1", function(){
	$(this).closest(".RemoveParent").remove();
})


$(document).on("click","#EditRemoveFields", function(){
			$(this).closest(".EditRemoveParent").remove();
	var Id = $(this).val();

	$.ajax({
		url : '<?php echo base_url(); ?>Admin/FormBuilder/RemoveFromFields',
		method: 'post',
		data : {Id:Id},
		success: function(data) {
			if (data == 1) {
			alert("Field removed successfully.");
			} else {
				alert("Something went wrong. try again later.");
			}
			
		}
	});
	
return false;
});

</script>
<script src="https://cdn.ckeditor.com/4.15.1/standard/ckeditor.js"></script>  
<script type="text/javascript">
            $(function() {
               CKEDITOR.replace('ckeditor');
            });
</script>

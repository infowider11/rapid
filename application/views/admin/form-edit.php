<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Form Edit<small></small></h1>
		
    </section>

    <!-- Main content -->
    <section class="content">
		<div class="error-msg"></div>
		<div class="row">
			<!-- left column -->
			<div class="col-md-8">
				<!-- general form elements -->
				<?php
$AllCountry = $this->common_model->GetAllData('country', '','nicename','asc');
         echo $this->session->flashdata('msgs'); ?>
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Form</h3>

					</div>
						 <div id="MessageUpdateErr"></div> 

<form method="post" onsubmit="return Edit();" id="Edit" autocomplete="off" style="padding: 0 10px">
<div class="form-group">
          <label for="">Form Name</label>
          <input type="text" class="form-control" name="name1" required placeholder="Form name" required="" value="<?php echo $form["name"]; ?>" />
        </div>
        
        <div class="form-group">
          <input type="hidden" name="formId" value="<?php echo $form["id"];?>" />
          <label for="">Form Note</label>
          <input type="text" class="form-control" name="note" required placeholder="Form note" value="<?php echo $form["note"]; ?>"/>
        </div>

        <div class="form-group">
          <label for="">Description</label>
          <textarea class="form-control ckeditor" name="description" required placeholder="Form description"><?php echo $form["description"]; ?></textarea>
        </div>

        <div class="form-group">
          <label for="">Country</label>
          <select class="form-control multi_select" name="country[]" id="questions_id" multiple="multiple" required="">
<?php
foreach ($AllCountry as $key => $country) {
  ?>
            <option value="<?php echo $country["id"]; ?>" <?php if (strpos($form["country"], $country["id"]) !== false) { echo "selected"; } ?> ><?php echo $country["nicename"]; ?></option>
  <?php
}
?>            
          </select>
        </div>
<?php 
$formData = $this->common_model->GetAllData('payment_form_field', array('payment_form_id'=>$form["id"]));
$x1 = 1;
foreach ($formData as $formData1) {
?>  <div class="EditRemoveParent">
  <hr>  <div><?php echo $x1; ?></div>
        <div class="form-group">
          <label for="">Is Required ?</label>
          <input type="hidden" name="formContentId[]" value="<?php echo $formData1["id"]; ?>" />
          <select class="form-control" name="is_required[]" required="" >
            <option value="">---Is required ?---</option>
            <option value="1" <?php if ($formData1["is_required"] == 1) { echo "selected"; } ?>>Yes</option>
            <option value="0" <?php if ($formData1["is_required"] == 0) { echo "selected"; } ?> >No</option>
          </select>
      
        </div>
        <div class="form-group">
          <label for="">Field Type</label>
          <select class="form-control" name="field_type[]" required="">
            <option value="">---Select field type---</option>
            <option value="text" <?php if ($formData1["field_type"] == "text") { echo "selected"; } ?> >Text</option>
            <option value="file" <?php if ($formData1["field_type"] == "file") { echo "selected"; } ?> >File</option>
            <option value="email" <?php if ($formData1["field_type"] == "email") { echo "selected"; } ?> >Email</option>
            <option value="number" <?php if ($formData1["field_type"] == "number") { echo "selected"; } ?> >Number</option>

          </select>
        </div>
        <div class="form-group">
          <label for="">Label</label>
          <input type="text" class="form-control" name="label[]" required="" placeholder="Label" value="<?php echo $formData1["label"]; ?>" />       
        </div>

        <div class="form-group">
          <label for="">Placeholder</label>
          <input type="text" class="form-control" name="placeholder[]" required="" placeholder="placeholder" value="<?php echo $formData1["label"]; ?>" />       
        </div>

        <div class="form-group">
          <label for="">Name <span style="color: red;">Form name should be unique.</span></label>
          <input type="text" class="form-control" name="name[]" required="" placeholder="name" value="<?php echo $formData1["label"]; ?>"/>    
        </div>
        <?php if ($x1 > 1) {
       ?><button type="button" id="EditRemoveFields" value="<?php echo $formData1["id"]; ?>" >-Remove</button>
       <?php
        } ?>
        <br>
      </div>  
<?php
$x1++;
}
?>

        <div id="AddMore1"></div>
        <br>
        <div class="form-group">
          <button type="button" id="AddMoreFields1">+Add</button>   
        </div>
        <div class="form-group">
          <br>
          <button type="submit" class="btn btn-primary " id="EditBtn">Update<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw btn-loadEdit"></i></button>
        </div>

</form>



        
				</div>
			</div>

		</div>
    </section>
</div>
 
 <?php include_once('include/footer.php'); ?>
 
<script>

 function Edit() {
 		for (instance in CKEDITOR.instances) {
    CKEDITOR.instances[instance].updateElement();
}
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/FormBuilder/EditFrom",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#Edit')[0]),
    dataType:'json',
    beforeSend:function(){
      $('#EditBtn').prop('disabled',true);
      $('.btn-loadEdit').show();
    },
    success:function(data) {
      if(data.status==1){
        window.location.href='../form-builder';
      } 
      else {
        $("#MessageUpdateErr").html(data.message);
        $('#EditBtn').prop('disabled',false);
        $('.btn-loadEdit').hide();
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


$(document).on("click","#AddMoreFields1", function(){
var html ="<div class='RemoveParent'><hr> <div class='form-group'>          <label for=''>Is Required ?</label>          <select class='form-control' name='is_required[]' required='' >            <option value=''>---Is required ?---</option>            <option value='1'>Yes</option>            <option value='0'>No</option>          </select>              </div><div class='form-group'>          <label for=''>Field Type</label>          <select class='form-control' name='field_type[]' required='' placeholder='Field type'>            <option value=''>---Select field type---</option>            <option value='text'>Text</option>            <option value='file'>File</option>            <option value='email'>Email</option>            <option value='number'>Number</option>          </select>        </div>   <input type='hidden' name='formContentId[]' value='0' />     <div class='form-group'>          <label for=''>Label</label>          <input type='text' class='form-control' name='label[]' required='' placeholder='Label' />               </div>        <div class='form-group'>          <label for=''>Placeholder</label>          <input type='text' class='form-control' name='placeholder[]' required='' placeholder='placeholder' />               </div>        <div class='form-group'>          <label for=''>Name <span style='color: red;'> Form name should be unique.</span></label>          <input type='text' class='form-control' name='name[]' required='' placeholder='name' />           </div>  <button type='button' id='RemoveFields'>-Remove</button></div><br>";
    $("#AddMore1").append(html);
});

$(document).on("click","#RemoveFields1", function(){
	$(this).closest(".RemoveParent").remove();
})


$(document).on("click","#EditRemoveFields", function(){

  if (confirm('Are you sure want to delete this?')) {
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
  }
		
	

  });

</script>
<script src="https://cdn.ckeditor.com/4.15.1/standard/ckeditor.js"></script>  
<script type="text/javascript">
            $(function() {
               CKEDITOR.replace('ckeditor');
            });
</script>

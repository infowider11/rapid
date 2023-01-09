<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Email Broadcast<small>Preview</small></h1>
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
						<h3 class="box-title">Email Broadcast</h3>
						<div id="MessageErr"></div>
					</div>
					<!-- /.box-header -->
					<!-- form start -->
					<form role="form" method="post"  onsubmit="return SendMail();" id="SendMail">
				<div class="box-body">
					<div class="row">
						<div class="col-md-6 alert alert-primary">Keywords: [username] (for name), [email] (for email)
						</div>
					</div>
						<div class="row">
							<div class="col-md-6">
							<div class="form-group">
								<label class=" form-control-label">User type</label>
								<select class="form-control" name="user_type" id="user_type" onchange="GetData();">
									<option value="1">All Hostess</option>
									<option value="2">Male Hostess</option>
									<option value="3">Female Hostess</option>
									<option value="4">All Agent</option>
									<option value="5">Male Agent</option>
									<option value="6">Female Agent</option>
									
								</select>
								<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw btn-loadEdit"></i>
							</div>								
							</div>
							<div class="col-md-6">
							<div class="form-group">
								<label>User Name</label>
								<select class="form-control multi_select users_data" name="user_id[]" id="questions_id" multiple="multiple" required=""></select>
							</div>								
							</div>


						</div>
						<div class="row">
							<div class="col-md-12">
							<div class="form-group">
								<label>Message</label>
								<textarea class="form-control textarea" name="message" placeholder="Message" required=""></textarea>
							</div>
						</div>
						</div>	
						<button type="submit" class="btn btn-primary" id="AddVehicleBtn">Send Mail<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw btn-load"></i></button>
				</div>		
						
					</form>
				</div>
			</div>

		</div>
    </section>
</div>
 
<?php include_once('include/footer.php'); ?>

<script>
	GetData();
function GetData() {
	var user_type = $("#user_type").val();
	$.ajax({
		url : '<?php echo base_url(); ?>Admin/Email/GetUserData',
		method : 'post',
		data:{user_type:user_type},
		beforeSend:function(){
	      $('.btn-loadEdit').show();
	    },
		success: function(data) {
			$(".users_data").html(data);
			$('.btn-loadEdit').hide();
			my_fun1();
		}
	});
}

function my_fun1(){
  $('#questions_id').multiselect({
    numberDisplayed: 1,
    includeSelectAllOption: true,
    //maximumSelectionLength:5,
    search: true,
    allSelectedText: 'All user selected',
    nonSelectedText: 'No user selected',
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
  $("#questions_id").multiselect('destroy');
  $('#questions_id').multiselect({
      includeSelectAllOption: true,
      enableFiltering: true,
      enableCaseInsensitiveFiltering: true,
      filterPlaceholder: 'Search for something...'
  });  
  
  



}

function SendMail() {

 $.ajax({
    url: "<?php echo base_url(); ?>Admin/Email/SendMail",
    type:"POST",
    cache:false,
        contentType: false,
        processData: false,
    data:new FormData($('#SendMail')[0]),
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
</script>
	<script>
tinymce.init({
  selector: '.textarea',
  height:480,
  plugins: [ 
        "advlist autolink lists link image charmap print preview anchor",
        "searchreplace visualblocks code fullscreen",
        "insertdatetime media table contextmenu paste"
    ],
  toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
	document_base_url: site_url,images_upload_handler : function(blobInfo, success, failure) {
		var xhr, formData;
		xhr = new XMLHttpRequest();
		xhr.withCredentials = false; 
		xhr.open('POST', site_url+'Admin/Email/addeditorimage');
		xhr.onload = function() {
			var json;
			if (xhr.status != 200) {
				failure('HTTP Error: ' + xhr.status);
				return;
			}
			json = JSON.parse(xhr.responseText);
			if (!json || typeof json.file_path != 'string') {
				failure('Invalid JSON: ' + xhr.responseText);
				return;
			}
			success(json.file_path);
			console.log(json.file_path);
		};
      formData = new FormData();
      formData.append('file', blobInfo.blob(), blobInfo.filename());
      xhr.send(formData);
	}, 
  setup: function (editor) {
    editor.on('change', function () {
      tinymce.triggerSave();
    });
  }
});
</script> 
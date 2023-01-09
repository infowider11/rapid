<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Email<small>Preview</small></h1>
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
						<h3 class="box-title">Email Information Signup</h3>
						<div id="MessageErr"></div>
					</div>
					<!-- /.box-header -->
					<!-- form start -->
					<form role="form" method="post" action="<?php echo base_url(); ?>Admin/Email/EmailSignUpUpdate">
				<div class="box-body">
					<div class="row">
						<div class="col-md-6 alert alert-primary">Keywords: [CODE] (for OTP)
						</div>
					</div>
						<div class="row">
							<div class="col-md-6">
							<div class="form-group">
								<label>User type</label>
								<input class="form-control" type="text" name="name" value="<?php echo $data["name"];?>" readonly>
							</div>								
							</div>

						</div>
						<div class="row">
							<div class="col-md-12">
							<div class="form-group">
								<label>Message</label>
								<textarea class="form-control textarea" name="text" placeholder="content" required=""><?php echo $data["text"];?></textarea>
							</div>
						</div>
						</div>
						<button type="submit" class="btn btn-primary" id="AddVehicleBtn">Update<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw btn-load"></i></button>	
				</div>		
						
					</form>
				</div>
			</div>

		</div>
    </section>
</div>
 
<?php include_once('include/footer.php'); ?>

<script>

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
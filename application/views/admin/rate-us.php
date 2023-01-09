<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Rate Us<small>Preview</small></h1>
		<!-- <ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="active"><a href="#">Profile</a></li>
		</ol> -->
    </section>

    <!-- Main content -->
    <section class="content">
		<?php echo $this->session->flashdata('msg'); ?>
		<div class="error-msg"></div>
		<div class="row">
			<!-- left column -->
			<div class="col-md-12">
				<!-- general form elements -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Rate Us</h3>
					</div>
					<!-- /.box-header -->
					<!-- form start -->
					<div id="MessageErr"></div>
					<form role="form" method="post" onsubmit="return updatePrivacy();" id="updatePrivacy">
						<div class="box-body">
							<div class="form-group">
								<label class="form-control-label">Page Title</label>
								<input type="text" class="form-control" name="title" value="<?php echo $terms_condition['title'];?>" required>
							</div>
							<div class="form-group">
								<label class="form-control-label">Description</label>
								<textarea class="form-control ckeditor" name="text" required><?php echo $terms_condition['text'];?></textarea>
							</div>
						</div>
						<div class="box-footer edit-profile-btn">
							<button type="submit" class="btn btn-success">Update<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw btn-loadEdit"></i></button>
						</div>
					</form>
				</div>
			</div>
		</div>
    </section>
</div>
 
<?php include_once('include/footer.php'); ?>

<script>
 function updatePrivacy() {
 	for (instance in CKEDITOR.instances) {
    CKEDITOR.instances[instance].updateElement();
}
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/ContentManagment/rateUsEdit",
    type:"POST",
    cache:false,
        contentType: false,
        processData: false,
    data:new FormData($('#updatePrivacy')[0]),
    dataType:'json',
    beforeSend:function(){
      $('.btn-success').prop('disabled',true);
      $('.btn-loadEdit').show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageErr").html(data.message);
        $('.btn-success').prop('disabled',false);
        $('.btn-loadEdit').hide();
      }
    }
  });
    return false;

} 
</script>
<style>
#cke_1_contents {
	height:400px !important;
}
</style>
<script src="https://cdn.ckeditor.com/4.15.1/standard/ckeditor.js"></script>  
 
<script type="text/javascript">
            $(function() {
               CKEDITOR.replace('ckeditor');
							 CKEDITOR.editorConfig = function( config )
								{
										// Define changes to default configuration here. For example:
										// config.language = 'fr';
										// config.uiColor = '#AADC6E';
										config.height = '800px';
								};
            });
        </script>
<script src="<?php echo site_url(); ?>assets/colorbutton/plugin.js"></script> 
<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Verification Limit<small>management</small></h1>
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
						<h3 class="box-title">Verification Limit</h3>
					</div>
				<div id="MessageErr"></div>	
					<form role="form" method="post" onsubmit="return manage_coin();" id="updateTerms">
						<div class="box-body">
							<input type="hidden" name="admin_id" value="<?=$data['id']?>">
							<div class="form-group">
									<label class="form-control-label">Minimum Diamonds For Boys</label>
								<div class="input-group">
									<input type="number" step="1" class="form-control"  name="min_daimonds_boy" value="<?=$data['min_daimonds_boy']?>" min="1" required placeholder="Enter diamond value" />
									<span class="input-group-addon">Diamonds</span>
								</div>
							</div>

							<div class="form-group">
								<label class="form-control-label">Mininum Coins For Girls</label>
								<div class="input-group">
									<input type="number" class="form-control"  name="min_coins_girl" value="<?=$data['min_coins_girl']?>" min="1" max="100" required placeholder="Enter percent" />
										<span class="input-group-addon">Coins</span>
								</div>
							</div>

							<div class="form-group">
								<label class="form-control-label">Minimum Levels For Boys</label>
								<div class="input-group">
									<input type="number" step="1" class="form-control"  name="min_level_boy" value="<?=$data['min_level_boy']?>" min="1" required placeholder="Enter levels value" />
									<span class="input-group-addon">Nos</span>
								</div>
							</div>

							<div class="form-group">
								<label class="form-control-label">Minimum Levels For Girls</label>
								<div class="input-group">
									<input type="number" step="1" class="form-control"  name="min_level_girl" value="<?=$data['min_level_girl']?>" min="1" required placeholder="Enter levels value" />
									<span class="input-group-addon">Nos</span>
								</div>
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
 function manage_coin(){
 $.ajax({
    url: "<?php echo base_url(); ?>Admin/RatingCategory/edit_coin",
    type:"POST",
    cache:false,
        contentType: false,
        processData: false,
    data:new FormData($('#updateTerms')[0]),
    dataType:'json',
    beforeSend:function(){
      $('.btn-success').prop('disabled',true);
      $('.btn-loadEdit').show();
    },
    success:function(data) {
      if(data.status==1){
        location. reload();
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

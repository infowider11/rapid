<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Diamond<small>coins</small></h1>
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
						<h3 class="box-title">Diamond Coins</h3>
					</div>
				<div id="MessageErr"></div>	
					<form role="form" method="post" onsubmit="return manage_coin();" id="updateTerms">
						<div class="box-body">
							<input type="hidden" name="admin_id" value="<?=$admin_data['id']?>">
							<div class="form-group hide">
									<label class="form-control-label">Diamond conversion rate for 1 unit amount</label>
								<div class="input-group">
									<span class="input-group-addon">1$ &nbsp; = </span>
									<input type="number" step="1" class="form-control"  name="diamond_rate" value="<?=$admin_data['diamond_rate']?>" min="1" required placeholder="Enter diamond value" />
									<span class="input-group-addon">Diamonds</span>
								</div>
							</div>

							<div class="form-group">
								<label class="form-control-label">Diamond to coin rate in percent (%)</label>
								<div class="input-group">
									<span class="input-group-addon">Percent</span>
									<input type="number" class="form-control"  name="commission" value="<?=$admin_data['commission']?>" min="1" max="100" required placeholder="Enter percent" />
										<span class="input-group-addon">%</span>
								</div>
							</div>

							<div class="form-group">
								<label class="form-control-label">Coin conversion rate for 1 unit amount</label>
								<div class="input-group">
									<span class="input-group-addon">1 $&nbsp; = </span>
									<input type="number" step="1" class="form-control"  name="coin_rate" value="<?=$admin_data['coin_rate']?>" min="1" required placeholder="Enter coin value" />
									<span class="input-group-addon">Coins</span>
								</div>
							</div>

							<div class="form-group">
								<label class="form-control-label">Guardian Percentage</label>
								<div class="input-group">
									<span class="input-group-addon">Percent</span>
									<input type="number" step="1" class="form-control"  name="guardian_percent" value="<?=$admin_data['guardian_percent']?>" min="1" max="100" required placeholder="Enter coin value" />
									<span class="input-group-addon">%</span>
								</div>
							</div>

							<div class="form-group">
								<label class="form-control-label">Gurdian duration in days</label>
								<div class="input-group">
									<input type="number" step="1" class="form-control"  name="guardian_duration" value="<?=$admin_data['guardian_duration']?>" min="1" required placeholder="Enter coin value" />
									<span class="input-group-addon">Days</span>
								</div>
							</div>

							<div class="form-group">
								<label class="form-control-label">Minmum Guardian Amount</label>
								<div class="input-group">
									<input type="number" step="1" class="form-control"  name="guardian_amount" value="<?=$admin_data['guardian_amount']?>" min="1" required placeholder="Enter diamond value" />
									<span class="input-group-addon">Diamond</span>
								</div>
							</div>


							<div class="form-group">
								<label class="form-control-label">Ban report</label>
								<div class="input-group">
									<input type="number" step="1" class="form-control"  name="ban_report_count" value="<?=$admin_data['ban_report_count']?>" min="1" required placeholder="Enter coin value" />
									<span class="input-group-addon">Nos</span>
								</div>
							</div>


							<div class="form-group">
								<label class="form-control-label">Account per ip</label>
								<div class="input-group">
									<input type="number" step="1" class="form-control"  name="account_per_ip" value="<?=$admin_data['account_per_ip']?>" min="1" required placeholder="Enter account per id" />
									<span class="input-group-addon">Nos</span>
								</div>
							</div>

							<div class="form-group">
								<label class="form-control-label">Album</label>
								<div class="input-group">
									<input type="number" step="1" class="form-control"  name="album_limit" value="<?=$admin_data['album_limit']?>" min="1" required placeholder="Album Limit" />
									<span class="input-group-addon">Nos</span>
								</div>
							</div>
							<div class="form-group">
								<label class="form-control-label">Per watch video ad Diamonds </label>
						
									<input type="number" step="1" class="form-control"  name="watch_video_diamonds" value="<?=$admin_data['watch_video_diamonds']?>" min="1" required placeholder="" />
							
							</div>
							<div class="form-group">
								<label class="form-control-label">Max. Images Per post</label>
						
									<input type="number" step="1" class="form-control"  name="post_max_image" value="<?=$admin_data['post_max_image']?>" min="0" max="9" required placeholder="" />
							
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
    url: "<?php echo base_url(); ?>Admin/users/edit_coin",
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

<?php include ('include/header.php'); ?>
 <div id="page-wrapper" class="container">
	<div class="row">
	   <div id="nav-col">
		  <?php include ('include/sidebar.php'); ?>
	   </div>
	   <div id="content-wrapper">
		  <div class="row">
			 <div class="col-lg-12">
				<div class="row">
				   <div class="col-lg-12">
					  <ol class="breadcrumb">
						 <li><a href="#">Home</a></li>
						 <li class="active"><span>Dashboard</span></li>
					  </ol>
				   </div>
				</div>
				
				
				
				<div class="row">
				<div class="col-sm-8 col-sm-offset-2">
					<div class="main-box clearfix profile-box-stats">
						<div class="main-box-body clearfix">
						<div class="profile-box-header purple-bg clearfix">							
							<img src="<?php echo site_url(); ?>assets/agent/img/samples/robert-300.jpg" alt="" class="profile-img img-responsive">
						</div>
							<div class="profile-box-footer clearfix">
							<div class="userdet text-center">
								<h2><?php echo $user["nickname"]; ?></h2>
								<div class="job-position"></div>
							</div>
							<div class="clearfix"></div>
							
							<div class="buton text-center mt-10">
								<button class="btn btn-default btn-sm">Bind ePay</button>								
								<button class="btn btn-default btn-sm" onclick="myFunction()" >Invitation Link for Hostesses </button>

								<button class="btn btn-default btn-sm" onclick="myFunction1()" >Invitation Link for Agents</button>
								
							</div>
							<div class="clearfix"></div>
								<a href="#">
									<span class="value">
<?php echo $user["uniqueID"]; ?>
									</span>
									<span class="label">Agent ID</span>
								</a>
								<a href="#">
									<span class="value">
<?php echo date('d-m-Y'); ?>
									</span>
									<span class="label">Date</span>
								</a>
								<a href="#">
									<span class="value">0%</span>
									<span class="label">My Commission Ratio</span>
								</a>
								
							<div class="clearfix"></div>
								<div class=""  style="padding:0px 20px;">
								<div class="row">
									<div class="col-sm-6 text text-danger">
										0%
									</div>
									<div class="col-sm-6 text-right text text-danger">
										100%
									</div>
								</div>
								<div class="progress">
									<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
									<span class="sr-only">40% Complete (success)</span>
									</div>
								</div>
									<p class="text-center"> $150.00 more to reach the ratio of 5%</p>
								</div>
							</div>
						</div>
					</div>
				</div>
				</div>
				
				<div class="row">
				   <div class="col-sm-3 col_md_c1 col-xs-12">
					  <div class="main-box infographic-box">
						 <i class="fa fa-star red-bg"></i>
						 <span class="headline">Existing Number of Hostesses</span>
						 <span class="value">
						 <span class="timer" data-from="120" data-to="2562" data-speed="1000" data-refresh-interval="50">
						 <?php echo count($hostess)."<br>";?>
						 </span>
						 </span>
					  </div>
				   </div>
				   <div class="col-sm-3 col_md_c1 col-xs-12">
					  <div class="main-box infographic-box">
						 <i class="fa fa-clock-o emerald-bg"></i>
						 <span class="headline"> Total Time of Calls(Hostess)</span>
						 <span class="value">
						 <span class="timer" data-from="30" data-to="658" data-speed="800" data-refresh-interval="30">
						 <?php
if (!empty($hostessCall)) {
	
					 
foreach ($hostessCall as $key => $value1) {
	echo round(($value1["total"]/60),2)."Min.";
}

						 }	else {echo 0;}

						 ?>
						 </span>
						 </span>
					  </div>
				   </div>
				   <div class="col-sm-3 col_md_c1 col-xs-12">
					  <div class="main-box infographic-box">
						 <i class="fa fa-usd green-bg"></i>
						 <span class="headline">Total Earning from Calls(Hostess)</span>
						 <span class="value">
						 <span class="timer" data-from="83" data-to="8400" data-speed="900" data-refresh-interval="60">
						 &#36; <?php 
						 //print_r($HostessCallEarn);
						 $HostessCalltotal = 0;
if (!empty($HostessCallEarn)) {
						 
						 			foreach ($HostessCallEarn as $key => $value3) {
						 				if (!empty($value3["total"])) {

										 	echo $value3["total"];
										 	$HostessCalltotal = $value3["total"];
										 	} else { echo 0; }
								 }

						 }			 else { echo 0; }
						  ?>
						 </span>
						 </span>
					  </div>
				   </div>
				   <div class="col-sm-3 col_md_c1 col-xs-12">
					  <div class="main-box infographic-box">
						 <i class="fa fa-gift yellow-bg"></i>
						 <span class="headline">Total Earning from Gifts(Hostess)</span>
						 <span class="value">
						 <span class="timer" data-from="539" data-to="12526" data-speed="1100">
						 &#36;

						 <?php
						 $HostessGiftTotal= 0;
if (!empty($HostessGiftEarn)) {
						 
									 	foreach ($HostessGiftEarn as $key => $value6) {
									 		if (!empty($value6["total"])) {
									 			echo $value6["total"];
									 			$HostessGiftTotal= $value6["total"]; 
									 		} else {
									 			echo 0;
									 		}
									 	
									 	}
						 	
						 } else { echo 0; }									 	
						 ?>

						 </span>
						 </span>
					  </div>
				   </div>
				   <div class="col-sm-3 col_md_c1 col-xs-12">
					  <div class="main-box infographic-box">
						 <i class="fa fa-usd yellow-bg"></i>
						 <span class="headline">In Total</span>
						 <span class="value">
						 <span class="timer" data-from="539" data-to="12526" data-speed="1100">
						 &#36;
						 <?php   echo ($HostessCalltotal+$HostessGiftTotal); ?>
						 </span>
						 </span>
					  </div>
				   </div>
				   
				   
				   
				   
				   
				   <div class="col-sm-3 col_md_c1 col-xs-12">
					  <div class="main-box infographic-box">
						 <i class="fa fa-star red-bg"></i>
						 <span class="headline">Existing Number of Sub-agent</span>
						 <span class="value">
						 <span class="timer" data-from="120" data-to="2562" data-speed="1000" data-refresh-interval="50">
						 <?php echo count($subAgents); ?>
						 </span>
						 </span>
					  </div>
				   </div>
				   <div class="col-sm-3 col_md_c1 col-xs-12">
					  <div class="main-box infographic-box">
						 <i class="fa fa-clock-o emerald-bg"></i>
						 <span class="headline"> Total Time of Calls(Sub-agent)</span>
						 <span class="value">
						 <span class="timer" data-from="30" data-to="658" data-speed="800" data-refresh-interval="30">
						 	<?php
if (!empty($subAgentsCall)) {
					 	
foreach ($subAgentsCall as $key => $value2) {
	echo ($value2["total"]/60)."Min.";
}
						 	}	else { echo 0; }
						 ?>
						 </span>
						 </span>
					  </div>
				   </div>
				   <div class="col-sm-3 col_md_c1 col-xs-12">
					  <div class="main-box infographic-box">
						 <i class="fa fa-usd green-bg"></i>
						 <span class="headline">Total Earning from Calls(Sub-agent)</span>
						 <span class="value">
						 <span class="timer" data-from="83" data-to="8400" data-speed="900" data-refresh-interval="60">
						 &#36; <?php 
						 //print_r($SubagentCallEarn);
						 $SubagentCallEarnTotal = 0;
				if (!empty($SubagentCallEarn)) {
						 
									 	foreach ($SubagentCallEarn as $key => $value4) {
									 		if (!empty($value4["total"])) {

									 	echo $value4["total"];
									 	$SubagentCallEarnTotal = $value4["total"]; 
									 	 } else { echo 0; }
									 	}
						} else { echo 0; }
						  ?>
						 </span>
						 </span>
					  </div>
				   </div>
				   <div class="col-sm-3 col_md_c1 col-xs-12">
					  <div class="main-box infographic-box">
						 <i class="fa fa-gift yellow-bg"></i>
						 <span class="headline">Total Earning from Gifts(Sub-agent)</span>
						 <span class="value">
						 <span class="timer" data-from="539" data-to="12526" data-speed="1100">
						 &#36;

						 <?php
						 $SubagentGiftEarnTotal = 0;
						 if (!empty($SubagentGiftEarn)) {
						 
						 foreach ($SubagentGiftEarn as $key => $value5) {
									 		if (!empty($value5["total"])) {
									 			echo $value5["total"];
									 			$SubagentGiftEarnTotal = $value5["total"];
									 		} else {
									 			echo 0;
									 		}
									 	
									 	}
							} else { echo 0; }		 	
						 ?>
						 </span>
						 </span>
					  </div>
				   </div>
				   <div class="col-sm-3 col_md_c1 col-xs-12">
					  <div class="main-box infographic-box">
						 <i class="fa fa-usd yellow-bg"></i>
						 <span class="headline">In Total</span>
						 <span class="value">
						 <span class="timer" data-from="539" data-to="12526" data-speed="1100">
						 &#36;

						 <?php echo ($SubagentCallEarnTotal+$SubagentGiftEarnTotal);  ?>
						 </span>
						 </span>
					  </div>
				   </div>
				   
				</div>
				
				
				
				
			 </div>
		  </div>
	   </div>
	</div>
 </div>
</div>
						 <input style="opacity: 0; " type="text" id="myInput" value="<?php echo site_url(); ?>hostess/sign-up/<?php echo $userdata['referal_code']; ?>" />
						<input style="opacity: 0; " type="text" id="myInput1" value="<?php echo site_url(); ?>sub-agent/sign-up/<?php echo $userdata['referal_code']; ?>" />
<?php include ('include/footer.php') ?>

<script>
function myFunction() {
  var copyText = document.getElementById("myInput");
  copyText.select();
  copyText.setSelectionRange(0, 99999)
  document.execCommand("copy");
  alert("Hostesses invitation link copied.");
}
function myFunction1() {
  var copyText = document.getElementById("myInput1");
  copyText.select();
  copyText.setSelectionRange(0, 99999)
  document.execCommand("copy");
  alert("Agent invitation link copied.");
}
</script>

<?php 
$page=$this->uri->segment(2);
?>
<aside class="main-sidebar">
	<section class="sidebar" style="height: auto;">
		
		<!-- sidebar menu: : style can be found in sidebar.less -->
		<ul class="sidebar-menu" data-widget="tree">
			

			<li class="">
				<a href="<?php echo base_url().'admin/dashboard';?>">
					<i class="fa fa-dashboard"></i> <span>Dashboard</span>
				</a>
			</li>
			<li class="treeview">
				<a href="#">
					<i class="fa fa-pie-chart"></i>
					<span>User Management</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
				</a>
				<!-- <ul class="treeview-menu">
					<li class=""><a href="<?php echo base_url(); ?>admin/user-list">User List</a></li>
				</ul> -->
				<ul class="treeview-menu">
					<li class=""><a href="<?php echo base_url(); ?>admin/users">Total Users</a></li>
					<li class=""><a href="<?php echo base_url(); ?>admin/verified_users">Verified Users</a></li>
					<li class=""><a href="<?php echo base_url(); ?>admin/unverified_users">Unverified  Users</a></li>
					<li class=""><a href="<?php echo base_url(); ?>admin/total_country">Total Country</a></li>
					<li class=""><a href="<?php echo base_url(); ?>admin/top_earners">Top Earners</a></li>
					<li class=""><a href="<?php echo base_url(); ?>admin/top_rich">Top Rich</a></li>

					<li class="treeview">
				<a href="#">
					<i class="fa fa-pie-chart"></i>
					<span>Gender</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
				</a>
				<!-- <ul class="treeview-menu">
					<li class=""><a href="<?php echo base_url(); ?>admin/user-list">User List</a></li>
				</ul> -->
				<ul class="treeview-menu">
					<li class=""><a href="<?php echo base_url(); ?>admin/boys_list">Boys List</a></li>
					<li class=""><a href="<?php echo base_url(); ?>admin/girls_list">Girls List</a></li>
					
				</ul>
				
			</li>

					
				</ul>
				
			</li>
			<li class="treeview">
				<a href="#">
					<i class="fa fa-pie-chart"></i>
					<span>Agent Management</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
				</a>
				<ul class="treeview-menu">					
					<li class=""><a href="<?php echo base_url().'admin/agent';?>">Agent List</a></li>
					<li class=""><a href="<?php echo base_url().'admin/sub-agent';?>">Sub-agent List</a></li>
					<li class=""><a href="<?php echo base_url(); ?>admin/agent-comission">Agent Comission</a></li>
				</ul>
			</li>

			<li class="">
				<a href="<?php echo base_url().'admin/form-builder';?>">
					<i class="fa fa-dashboard"></i> <span>Payment Method</span>
				</a>
			</li>
			<li class="treeview">
				<a href="#">
					<i class="fa fa-pie-chart"></i>
					<span>Trade Management</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
				</a>
				<ul class="treeview-menu">					
					<li class=""><a href="<?php echo base_url().'admin/trade';?>">Trade List</a></li>
				</ul>
			</li>
			<li class="treeview">
				<a href="#">
					<i class="fa fa-pie-chart"></i>
					<span>Rating Management</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
				</a>
				<ul class="treeview-menu">	
					<li class=""><a href="<?php echo base_url().'admin/rating-list';?>">Rating List</a></li>				
					<li class=""><a href="<?php echo base_url().'admin/boys-rating-category';?>">Boys Rating Category</a></li>
					<li class=""><a href="<?php echo base_url().'admin/girls-rating-category';?>">Girls Rating Category</a></li>

				</ul>
			</li>

			<li class="treeview">
				<a href="#">
					<i class="fa fa-pie-chart"></i>
					<span>Verification Management</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
				</a>
				<ul class="treeview-menu">	
					<li class=""><a href="<?php echo base_url().'admin/verification-limit';?>">Verification Limit</a></li>	
					<li class=""><a href="<?php echo base_url().'admin/verification-request';?>">Verification Request</a></li>	
					<li class=""><a href="<?php echo base_url().'admin/official-badges';?>">Official Badges</a></li>		

				</ul>
			</li>

			<li class="treeview">
				<a href="#">
					<i class="fa fa-pie-chart"></i>
					<span>Merchant Management</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
				</a>
				<ul class="treeview-menu">	
					<li class=""><a href="<?php echo base_url().'admin/merchant-list';?>">List</a></li>		
				</ul>
			</li>

			<li class="treeview">
				<a href="#">
					<i class="fa fa-pie-chart"></i>
					<span>Setting</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
				</a>
				<ul class="treeview-menu">					
					<li class=""><a href="<?php echo base_url().'admin/manage-coin';?>">Coins Rate</a></li>
					<li class=""><a href="<?php echo base_url(); ?>admin/diamond">Diamond</a></li>
					<li class=""><a href="<?php echo base_url().'admin/email';?>">Email Broadcast</a></li>
					<li class=""><a href="<?php echo base_url().'admin/email-builder-forget';?>">Email Builder Forget Password</a></li>
					<li class=""><a href="<?php echo base_url().'admin/email-builder-otp';?>">Email Builder OTP</a></li>
					<li class=""><a href="<?php echo base_url().'admin/email-builder-signup';?>">Email Builder Signup</a></li>
					<li class=""><a href="<?php echo base_url().'admin/email-builder-agent';?>">Email Builder Agents</a></li>
					<li class=""><a href="<?php echo base_url().'admin/email-block-agent';?>">Email Builder Block Agent</a></li>
					<li class=""><a href="<?php echo base_url().'admin/email-edit-agent';?>">Email Builder Change Agent</a></li>
					<li class=""><a href="<?php echo base_url().'admin/email-builder-block-by';?>">Email (Reported User)</a></li>
					<li class=""><a href="<?php echo base_url().'admin/email-builder-report-other';?>">Email (Reporter)</a></li>
					<!--<li class=""><a href="<?php echo base_url().'admin/email-builder-hostess';?>">Email Builder Hostess</a></li>-->
					<li class=""><a href="<?php echo base_url().'admin/smtp-details';?>">SMTP Settings</a></li>
					<li class=""><a href="<?php echo base_url().'admin/ring';?>">Ringtone Management</a></li>
					<li class=""><a href="<?php echo base_url().'admin/language';?>">Language Management</a></li>
					<li class=""><a href="<?php echo base_url().'admin/level-boys';?>">Level Boys</a></li>
					<li class=""><a href="<?php echo base_url(); ?>admin/level-girls">Level Girls</a></li>
					<li class=""><a href="<?php echo base_url().'admin/gift_listing';?>">Gift List</a></li>
					<li class=""><a href="<?php echo base_url(); ?>admin/category">Category</a></li>
					<li class=""><a href="<?php echo base_url(); ?>admin/batches-management">Badges Management</a></li>
					<li class=""><a href="<?php echo base_url(); ?>admin/spin-wheel">Spin Wheel</a></li>
					<li class="treeview">
				<a href="#">
					<i class="fa fa-pie-chart"></i>
					<span>IP Management</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
				</a>
				<!-- <ul class="treeview-menu">
					<li class=""><a href="<?php echo base_url(); ?>admin/user-list">User List</a></li>
				</ul> -->
				<ul class="treeview-menu">
					<li class=""><a href="<?php echo base_url(); ?>admin/white-list">White List IP</a></li>
					<li class=""><a href="<?php echo base_url(); ?>admin/black-list">Black List IP</a></li>
					
				</ul>
				
			</li>
				</ul>
			</li>
			<li class="">
				<a href="<?php echo base_url().'admin/transaction-history';?>">
					<i class="fa fa-dashboard"></i> <span>Transaction History</span>
				</a>
			</li>
			<li class="">
				<a href="<?php echo base_url().'admin/post-management';?>">
					<i class="fa fa-dashboard"></i> <span>Post Management</span>
				</a>
			</li>
			
			<li class="treeview">
				<a href="#">
					<i class="fa fa-pie-chart"></i>
					<span>Withdrawal Management</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
				</a>
				<ul class="treeview-menu">					
					<li class=""><a href="<?php echo base_url().'admin/withdrawal-request';?>">Pending</a></li>
					<li class=""><a href="<?php echo base_url().'admin/withdrawal-approved';?>">Approved</a></li>
					<li class=""><a href="<?php echo base_url().'admin/withdrawal-rejected';?>">Rejected</a></li>
				</ul>
			</li>

			<li class="treeview">
				<a href="#">
					<i class="fa fa-pie-chart"></i>
					<span>Report Management</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
				</a>
				<ul class="treeview-menu">					
					<li class=""><a href="<?php echo base_url().'admin/report-category';?>">Category</a></li>
					<li class=""><a href="<?php echo base_url().'admin/report-list';?>">List</a></li>
					<li class=""><a href="<?php echo base_url().'admin/report-post';?>">Post reports</a></li>
				</ul>
			</li>

			<li class="">
				<a href="<?php echo base_url().'admin/guardian-list';?>">
					<i class="fa fa-dashboard"></i> <span>Guardian list</span>
				</a>
			</li>


			<li class="treeview">
				<a href="#">
					<i class="fa fa-pie-chart"></i>
					<span>Album Review</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
				</a>
				<ul class="treeview-menu">					
					<li class=""><a href="<?php echo base_url().'admin/album-list';?>">Pending</a></li>
					<li class=""><a href="<?php echo base_url().'admin/album-list-approved';?>">Approved</a></li>
					<li class=""><a href="<?php echo base_url().'admin/album-list-rejected';?>">Rejected</a></li>
				</ul>
			</li>
			
			<li class="treeview">
				<a href="#">
					<i class="fa fa-pie-chart"></i>
					<span>Content Management</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
				</a>
				<ul class="treeview-menu">					
					<li class=""><a href="<?php echo base_url().'admin/privacy-policy';?>">Privacy Policy</a></li>
					<li class=""><a href="<?php echo base_url().'admin/terms-condition';?>">Terms & Condition</a></li>
					<li class=""><a href="<?php echo base_url().'admin/about-us';?>">About Us</a></li>
					<li class=""><a href="<?php echo base_url().'admin/rate-us';?>">Rate Us</a></li>
				</ul>
			</li>
			 
		</ul>
	</section>
</aside>
<section id="col-left" class="col-left-nano">
 <div id="col-left-inner" class="col-left-nano-content">	
	<div class="collapse navbar-collapse navbar-ex1-collapse" id="sidebar-nav">
	   <ul class="nav nav-pills nav-stacked">
		  <li><!-- class="active" -->
			 <a href="<?php echo site_url().'agent/dashboard'; ?>">
			 <i class="fa fa-dashboard"></i>
			 <span>Dashboard</span>
			 </a>
		  </li>
		  
		   
		  <li>
			 <a href="#" class="dropdown-toggle">
			 <i class="fa  fa-users"></i>
			 <span>Member List</span>
			 <i class="fa fa-chevron-circle-right drop-icon"></i>
			 </a>
			 <ul class="submenu">
				<li><a href="<?php echo site_url();?>agent/hostess-list">Hostess List</a></li>
				<li><a href="<?php echo site_url();?>agent/sub-agent">Sub-agent List</a></li>
			 </ul>
		  </li>
		  
		  <li>
			 <a href="#" class="dropdown-toggle">
			 <i class="fa  fa-file-text-o"></i>
			 <span>Daily Report</span>
			 <i class="fa fa-chevron-circle-right drop-icon"></i>
			 </a>
			 <ul class="submenu">
				<li><a href="<?php echo site_url();?>agent/daily-report">Hostess Daily Report</a></li>
				<li><a href="<?php echo site_url();?>agent/daily-report-sub">Sub-agent Daily Report</a></li>
			 </ul>
		  </li>
		  <li>
			 <a href="#" class="dropdown-toggle">
			 <i class="fa fa-folder-open-o"></i>
			 <span>Settlement</span>
			 <i class="fa fa-chevron-circle-right drop-icon"></i>
			 </a>
			 <ul class="submenu">
				<li><a href="<?php echo site_url();?>agent/hostess-settlement">Hostess</a></li>
				<li><a href="<?php echo site_url();?>agent/subagent-settlement">Sub-agents</a></li>
			 </ul>
		  </li>
		  <!--li>
			 <a href="notification.php">
			 <i class="fa fa-bell"></i>
			 <span>Notification</span>
			 </a>
		  </li-->
		  
	   </ul>
	</div>
 </div>
</section>
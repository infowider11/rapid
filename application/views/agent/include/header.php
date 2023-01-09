<!DOCTYPE html>
<html>
   <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
      <title><?= Project; ?></title>
      <link rel="stylesheet" type="text/css" href="<?php echo site_url(); ?>assets/agent/css/bootstrap/bootstrap.min.css" />
      <script src="<?php echo site_url(); ?>assets/agent/js/demo-rtl.js"></script>
      <link rel="stylesheet" type="text/css" href="<?php echo site_url(); ?>assets/agent/css/libs/font-awesome.css" />
      <link rel="stylesheet" type="text/css" href="<?php echo site_url(); ?>assets/agent/css/libs/nanoscroller.css" />
      <link rel="stylesheet" type="text/css" href="<?php echo site_url(); ?>assets/agent/css/compiled/theme_styles.css" />
      <link rel="stylesheet" href="<?php echo site_url(); ?>assets/agent/css/libs/fullcalendar.css" type="text/css" />
      <link rel="stylesheet" href="<?php echo site_url(); ?>assets/agent/css/libs/fullcalendar.print.css" type="text/css" media="print" />
      <link rel="stylesheet" href="<?php echo site_url(); ?>assets/agent/css/compiled/calendar.css" type="text/css" media="screen" />
      <link rel="stylesheet" href="<?php echo site_url(); ?>assets/agent/css/libs/morris.css" type="text/css" />
      <link rel="stylesheet" href="<?php echo site_url(); ?>assets/agent/css/libs/daterangepicker.css" type="text/css" />
      <link rel="stylesheet" href="<?php echo site_url(); ?>assets/agent/css/libs/jquery-jvectormap-1.2.2.css" type="text/css" />
      <link type="image/x-icon" href="<?php echo site_url(); ?>assets/image/logo.png" rel="shortcut icon" />
      <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300|Titillium+Web:200,300,400' rel='stylesheet' type='text/css'>
 
   </head>
   <body class="">
<?php
$agent_id = $this->session->userdata('agent_id');
$userdata = $this->common_model->GetDataById('users',$agent_id);
?>
      <div id="theme-wrapper">
         <header class="navbar" id="header-navbar">
            <div class="container">
               <a href="" id="logo" class="navbar-brand">
               <img src="<?php echo site_url(); ?>assets/agent/img/logo.png" alt="" class="normal-logo logo-white" />
               <img src="<?php echo site_url(); ?>assets/agent/img/logo.png" alt="" class="normal-logo logo-black" />
               <img src="<?php echo site_url(); ?>assets/agent/img/logo.png" alt="" class="small-logo hidden-xs hidden-sm hidden" />
               </a>
               <div class="clearfix">
                  <button class="navbar-toggle" data-target=".navbar-ex1-collapse" data-toggle="collapse" type="button">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="fa fa-bars"></span>
                  </button>
                  <div class="nav-no-collapse navbar-left pull-left hidden-sm hidden-xs">
                     <ul class="nav navbar-nav pull-left">
                        <li>
                           <a class="btn" id="make-small-nav">
                           <i class="fa fa-bars"></i>
                           </a>
                        </li>
                     </ul>
                  </div>
                  <div class="nav-no-collapse pull-right" id="header-nav">
                     <ul class="nav navbar-nav pull-right">
                        <!--li class="mobile-search">
                           <a class="btn">
                           <i class="fa fa-search"></i>
                           </a>
                           <div class="drowdown-search">
                              <form role="search">
                                 <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Search...">
                                    <i class="fa fa-search nav-search-icon"></i>
                                 </div>
                              </form>
                           </div>
                        </li>
                        <li class="">
                           <a class="btn" href="notification.php">
                           <i class="fa fa-bell"></i>
                           <span class="count">8</span>
                           </a>
                        </li-->
                        
                        <li class="dropdown profile-dropdown">
                           <a href="#" class="dropdown-toggle" data-toggle="dropdown">
													 
													 <?php
													 $pro_url = site_url().'upload/default_Image.png';
													 if($userdata['image']){
														 $pro_url = site_url().'assets/admin/agentImg/'.$userdata['image'];
													 }
													 ?>
													 
                           <img src="<?php echo $pro_url; ?>" alt="" />
                           <span class="hidden-xs"><?php echo $userdata['nickname']; ?></span> <b class="caret"></b>
                           </a>
                           <ul class="dropdown-menu">
                              <li><a href="<?php echo site_url().'agent/dashboard'; ?>"><i class="fa fa-dashboard"></i>Dashboard</a></li>
                              <li><a href="<?php echo site_url().'agent/edit-profile'; ?>"><i class="fa fa-user"></i>Profile</a></li>
                              <li><a href="<?php echo site_url().'agent/user/logout'; ?>"><i class="fa fa-power-off"></i>Logout</a></li>
                           </ul>
                        </li>
                     </ul>
                  </div>
               </div>
            </div>
         </header>
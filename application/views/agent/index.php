<!DOCTYPE html>
<html>
   <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
      <title><?php echo Project; ?></title>
      <link rel="stylesheet" type="text/css" href="<?php echo site_url(); ?>assets/agent/css/bootstrap/bootstrap.min.css" />
      <script src="<?php echo site_url(); ?>assets/agent/js/demo-rtl.js"></script>
      <link rel="stylesheet" type="text/css" href="<?php echo site_url(); ?>assets/agent/css/libs/font-awesome.css" />
      <link rel="stylesheet" type="text/css" href="<?php echo site_url(); ?>assets/agent/css/libs/nanoscroller.css" />
      <link rel="stylesheet" type="text/css" href="<?php echo site_url(); ?>assets/agent/css/compiled/theme_styles.css" />
      <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300|Titillium+Web:200,300,400' rel='stylesheet' type='text/css'>
      <link type="image/x-icon" href="<?php echo site_url(); ?>assets/images/logo.png" />
			<script>var site_url = '<?php echo site_url(); ?>';</script>
   </head>
   <body id="login-page">
      <div class="container">
         <div class="row">
            <div class="col-xs-12">
               <div id="login-box">
                  <div id="login-box-holder">
                     <div class="row">
                        <div class="col-xs-12">
<?php echo $this->session->flashdata('msgs'); ?>                           
                           <header id="login-header">
                              <div id="login-logo">
                                 <img src="<?php echo site_url(); ?>assets/images/logo.png" alt="" />
                              </div>
                           </header>
                           <div id="login-box-inner">
                              <div class="login-error"><?php echo $this->session->flashdata('message');?></div>
															<form id="login_form" method="post" action="<?php echo site_url().'agent/auth/do_login'?>" onsubmit="login_form();">
                                 <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                    <input class="form-control" type="text" name="email" required placeholder="Email address">
                                 </div>
                                 <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                    <input type="password" class="form-control" name="password" required placeholder="Password">
                                 </div>
                                 <div id="remember-me-wrapper">
                                    <div class="row">
                                       <div class="col-xs-6">
                                          
                                       </div>
                                       <a href="<?php echo site_url().'agent/forgot-password'?>" id="login-forget-link" class="col-xs-6">
                                       Forgot password?
                                       </a>
                                    </div>
                                 </div>
                                 <div class="row">
                                    <div class="col-xs-12">
                                       <button type="submit" class="btn btn-success col-xs-12 submit_btn">Login</button>
                                    </div>
                                 </div>
                              </form>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <script src="<?php echo site_url(); ?>assets/agent/js/demo-skin-changer.js"></script> 
      <script src="<?php echo site_url(); ?>assets/agent/js/jquery.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/bootstrap.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/jquery.nanoscroller.min.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/demo.js"></script> 
      <script src="<?php echo site_url(); ?>assets/agent/js/scripts.js"></script>
   </body>
<script>
function login_form(){
	$('.submit_btn').prop('disabled',true);
	$('.submit_btn').html('<i class="fa fa-spin fa-spinner"></i>');
	$('#login_form').submit();
	
}
function topFunction() {
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
}
</script>
</html>


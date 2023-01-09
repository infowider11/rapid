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
                           <header id="login-header">
                              <div id="login-logo">
                                 <img src="<?php echo site_url(); ?>assets/images/logo.png" alt="" />
                              </div>
                           </header>
                           <div id="login-box-inner">
                              <div class="login-error"><?php echo $this->session->flashdata('message');?></div>
															<form id="forget_password_form" onsubmit="return forget_password_form();">
                                 <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                    <input class="form-control" type="email" name="email" required placeholder="Enter Email address">
                                 </div>
                                 <div id="remember-me-wrapper">
                                    <div class="row">
                                       <div class="col-xs-6">
                                          
                                       </div>
                                       <a href="<?php echo site_url().'agent/login'?>" id="login-forget-link" class="col-xs-6">
                                       Login
                                       </a>
                                    </div>
                                 </div>
                                 <div class="row">
                                    <div class="col-xs-12">
                                       <button type="submit" class="btn btn-success col-xs-12 submit_btn">Submit</button>
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
function forget_password_form(){
	//var formdata = $('#forget_password_form').serialize();
	$.ajax({	
		type:'POST',
		url:site_url+'agent/auth/forget_password_form',
		data:$('#forget_password_form').serialize(),
		dataType:'JSON',
		beforeSend:function()
		{
			$('.submit_btn').prop('disabled',true);
			$('.submit_btn').html('<i class="fa fa-spin fa-spinner"></i> Processing...');
			$('.login-error').html('');
		},
		success:function(resp)
		{	
			$('.submit_btn').prop('disabled',false);
			$('.submit_btn').html('Submit');
			if(resp.status==1)
			{
				$('#forget_password_form')[0].reset();
				$('.login-error').html('<div class="alert alert-success">Your password has been sent to your email address! Please check your mail or spam folder!</div>');
			}
			else if(resp.status==2)
			{
				$('.login-error').html('<div class="alert alert-danger">This email is not registered with us!</div>');
			}
			else if(resp.status==3)
			{
				$('.login-error').html('<div class="alert alert-danger">Your account has been blocked!</div>');
			}
			else
			{
				$('.login-error').html(resp.msg);
			}

		}
	});
	return false;
}
function topFunction() {
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
}
</script>
</html>


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
                              <div id="MessageErr"></div>
										<form method="post" onsubmit="return SignUP();" id="SignUP">
                                 <input type="hidden" name="UserId" value="<?php echo $this->session->userdata('User_id'); ?>">
                                 <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                    <input class="form-control" type="text" name="nickname" required placeholder="Your name" >
                                 </div>
                                 <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-key"></i></span>

                                    <select class="form-control" name="country" required>
               <option value="">---Select country---</option>
<?php
$countryList = $this->common_model->GetAllData('country','', 'nicename', 'ASC');
foreach ($countryList as $country) {
   ?>
               <option value="<?php echo $country["id"]; ?>"><?php echo $country["nicename"];?></option>
   <?php
}
?>             

             </select>
                                 </div>
                                 <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                    <input type="text" class="form-control" name="phone" required placeholder="phone" >
                                 </div>
                                 <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                    <input type="password" class="form-control" name="password" required placeholder="password" >
                                 </div>
                                 <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                    <input type="password" class="form-control" name="cpassword" required placeholder="Confirm password" >
                                 </div>                        
                                 <div class="row">
                                    <div class="col-xs-12">
                                       <button type="submit" class="btn btn-success col-xs-12 submit_btn" id="SignUPbtn">Submit<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw btn-load"></i></button>
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


function SignUP() {
 $.ajax({
    url: "<?php echo base_url(); ?>Home/sign_user_action",
    type:"POST",
    cache:false,
        contentType: false,
        processData: false,
    data:new FormData($('#SignUP')[0]),
    dataType:'json',
    success:function(data) {
      if(data.status==1){
        window.location.href='<?php echo base_url(); ?>agent/login';
      } 
      else {
        $("#MessageErr").html(data.message);
      }
    }
  });
    return false;

}

</script>
</html>


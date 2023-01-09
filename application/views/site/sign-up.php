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
                              <?php
if ($User) {
   ?>
                              <div id="MessageErr"></div>
										<form method="post" onsubmit="return SignUP();" id="SignUP">
                                 <input type="hidden" name="referalCode" value="<?php echo $this->uri->segment(3); ?>">
                                 <input type="hidden" name="invited_by" value="<?php echo $User["uniqueID"]; ?>" id="invited_by"/>
                                 <div class="input-group">
                                    <span class="input-group-addon">I'm an agent of rapid,<br><?php echo $User["nickname"]; ?> (ID : <?php echo $User["uniqueID"]; ?>)
                                      </span>
                                 </div>
                                 <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                    <input class="form-control" type="text" name="email" required placeholder="Email address" id="email" >

                                 </div>
                                 
                                 <div class="input-group" id="inputgroup123" style="display: none;">
                                    <input class="form-control" type="password" name="token" required placeholder="Enter Otp">

                                 </div>  

                                 <div class="otp_submit row">
                                    <div class="col-xs-12 text-center">
                                       <button type="submit" class="btn btn-success col-xs-12 submit_btn" id="SignUPbtn" style="display: none;">Verify <i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw btn-load"></i></button>
                                    </div>
                                 </div>
                                 <div class="otp_submit text-center form-group">
                                     <span class=""><button type="button" class="btn btn-primary col-xs-12" id="sendCode">Send otp</button></span><!-- input-group-addon -->
                                    <span class="" id="sendAgainCode" style="display: none; margin-top: 5px;"><button type="button" id="sendCodeAgain">Resend OTP</button></span><!-- input-group-addon -->
                                 </div>                           

                              </form>
                              <?php   
} else {
   ?>
   <h3>Invalid Url</h3>
   <?php
}
?> 
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
    url: "<?php echo base_url(); ?>Home/sign_up_action",
    type:"POST",
    cache:false,
        contentType: false,
        processData: false,
    data:new FormData($('#SignUP')[0]),
    dataType:'json',
    success:function(data) {
      if(data.status==1){
        window.location.href='../sign-up-form';
      } 
      else {
        $("#MessageErr").html(data.message);
      }
    }
  });
    return false;

}

$(document).on('click', '#sendCode', function () {
   var Email = $("#email").val();
   var invited_by = $("#invited_by").val();
   

   if (Email === '') {
     alert("Please enter your email id."); 
   } else {
      $.ajax({
         url : "<?php echo base_url(); ?>Home/sentCodeEmail",
         method: "post",
         dataType:'json',
         data : {Email:Email, invited_by: invited_by},
         success: function(data) {
         if(data.status==1){
            $("#MessageErr").html(data.message);
            $('#sendCode').css('display', 'none');
            $("#sendAgainCode").css('display', 'block');
            $("#SignUPbtn").css('display', 'block');
            $("#inputgroup123").css('display', 'block');
            
            
         } 
         else {
           $("#MessageErr").html(data.message);
         }
         }
      });
   }
})

$(document).on('click', '#sendCodeAgain', function () { 

      $.ajax({
         url : "<?php echo base_url(); ?>Home/sentAgainCode",
         method: "post",
         dataType:'json',
         success: function(data) {
         if(data.status==1){
            $("#MessageErr").html(data.message);
            
         } 
         else {
           $("#MessageErr").html(data.message);
         }
         }
      });
   
})


</script>
</html>


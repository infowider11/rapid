$(document).ready(function(){
	if($('#login_form').length > 0){
		$('#login_form').validate({
			rules: {
				'email': {
					required: true,
                    email: true
				},
				'password': {
					required: true
				}
				
			},
			messages: {
				'email': {
					required: 'Email address is required',
					email: 'Valid Email is required'
				},
				'password': {
					required: 'Password field is required'
				}
			},
			submitHandler: function(form) {
				form.submit();
			}
		});
	}
	
	if($('#profile_form').length > 0){
		$('#profile_form').validate({
			rules: {
				email: {
					required: true,
                    email: true
				},
				name: {
					required: true
				}
				
			},
			messages: {
				email: {
					required: 'Email address is required',
					email: 'Valid Email is required'
				},
				name: {
					required: 'Name field is required'
				}
			},
			submitHandler: function(form) {
				form.submit();
			}
		});
	}
	
	if($('#passoword_form').length > 0){
		$('#passoword_form').validate({
			rules: {
				'Current_Password': {
					required: true
				},
				'New_Password': {
					required: true
				},
				'Confirm_Password': {
					required: true,
					equalTo: '#New_Password'
				}	
			},
			submitHandler: function(form) {
				$.ajax({
					type:'POST',
					url:'process/process.php?action=change_password',
					data: $("#passoword_form").serialize(),
					success: function (data) {
						if(data == 1)
						{
							$('#Current_Password').val('');
							$('#New_Password').val('');
							$('#Confirm_Password').val('');
							$('#error_pass').html('<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Success!</strong>Your Password Change Successfully.</div>');
							return false;
						}
						else if(data == 0)
						{
							$('#Current_Password').val('');
							$('#New_Password').val('');
							$('#Confirm_Password').val('');
							$('#error_pass').html('<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Error!</strong>Current Password is not matched.</div>');
							return false;	
						}
					}
				});
				return false;
			}
		});
	}
});

function addcategory(){

	$.ajax({   
	   type : 'post',
	   data : new FormData($('#addcategory')[0]),
	   url  : site_url+'Admin/Category/add_category',
	   contentType: false,
       cache: false,    
       processData:false, 
	    beforeSend:function()
		{
			$('.btn-prop').prop('disabled',true);
			$('.btn-load').show();
		},
	   success : function(data){
	   	console.log(data);
		     $('.btn-prop').prop('disabled',false);
			 $('.btn-load').hide();
			if(data==1){   
			   window.location.href='categorylist';    
			}else if(data==0){
				$("#error").html('<div class="alert alert-danger">Something went wrong</div>');
				return false;
			}    
			else{
				$("#error").html(data);
		        return false;
			}  
	   } 
	});    
	 return false;
}

function editcategory(id){

	$.ajax({   
	   type : 'post',
	   data : new FormData($('#editcategory'+id)[0]),
	   url  : site_url+'Admin/Category/edit_category',
	   contentType: false,
       cache: false,    
       processData:false, 
	    beforeSend:function()
		{
			$('.btn-prop').prop('disabled',true);
			$('.btn-load').show();
		},
	   success : function(data){
	   	console.log(data);
		     $('.btn-prop').prop('disabled',false);
			 $('.btn-load').hide();
			if(data==1){   
			   window.location.href='categorylist';    
			}else if(data==0){
				$("#error"+id).html('<div class="alert alert-danger">Something went wrong</div>');
				return false;
			}    
			else{
				$("#error"+id).html(data);
		        return false;
			}  
	   } 
	});    
	 return false;
}

function addmanufacturer(){

	$.ajax({   
	   type : 'post',
	   data : new FormData($('#addmanufacturer')[0]),
	   url  : site_url+'Admin/Manufacturer/add_manufacturer',
	   contentType: false,
       cache: false,    
       processData:false, 
	    beforeSend:function()
		{
			$('.btn-prop').prop('disabled',true);
			$('.btn-load').show();
		},
	   success : function(data){
	   	console.log(data);
		     $('.btn-prop').prop('disabled',false);
			 $('.btn-load').hide();
			if(data==1){   
			   window.location.href='manufacturerlist';    
			}else if(data==0){
				$("#error").html('<div class="alert alert-danger">Something went wrong</div>');
				return false;
			}    
			else{
				$("#error").html(data);
		        return false;
			}  
	   } 
	});    
	 return false;
}

function editmanufacturer(id){

	$.ajax({   
	   type : 'post',
	   data : new FormData($('#editmanufacturer'+id)[0]),
	   url  : site_url+'Admin/Manufacturer/edit_manufacturer',
	   contentType: false,
       cache: false,    
       processData:false, 
	    beforeSend:function()
		{
			$('.btn-prop').prop('disabled',true);
			$('.btn-load').show();
		},
	   success : function(data){
	   	console.log(data);
		     $('.btn-prop').prop('disabled',false);
			 $('.btn-load').hide();
			if(data==1){   
			   window.location.href='manufacturerlist';    
			}else if(data==0){
				$("#error"+id).html('<div class="alert alert-danger">Something went wrong</div>');
				return false;
			}    
			else{
				$("#error"+id).html(data);
		        return false;
			}  
	   } 
	});    
	 return false;
}
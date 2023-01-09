<?php include ('include/header.php'); ?>


<?php

    function timeDiffrence($date1,$date2,$formate='') {
   // Declare and define two dates 
 
$date1 = strtotime($date1);  
$date2 = strtotime($date2);  
  
// Formulate the Difference between two dates 
$diff = abs($date2 - $date1);  
  
  
// To get the year divide the resultant date into 
// total seconds in a year (365*60*60*24) 
$years = floor($diff / (365*60*60*24));  
  
  
// To get the month, subtract it with years and 
// divide the resultant date into 
// total seconds in a month (30*60*60*24) 
$months = floor(($diff - $years * 365*60*60*24) 
                               / (30*60*60*24));  
  
  
// To get the day, subtract it with years and  
// months and divide the resultant date into 
// total seconds in a days (60*60*24) 
$days = floor(($diff - $years * 365*60*60*24 -  
             $months*30*60*60*24)/ (60*60*24)); 
  
  
// To get the hour, subtract it with years,  
// months & seconds and divide the resultant 
// date into total seconds in a hours (60*60) 
$hours = floor(($diff - $years * 365*60*60*24  
       - $months*30*60*60*24 - $days*60*60*24) 
                                   / (60*60));  
  
  
// To get the minutes, subtract it with years, 
// months, seconds and hours and divide the  
// resultant date into total seconds i.e. 60 
$minutes = floor(($diff - $years * 365*60*60*24  
         - $months*30*60*60*24 - $days*60*60*24  
                          - $hours*60*60)/ 60);  
  
  
// To get the minutes, subtract it with years, 
// months, seconds, hours and minutes  
$seconds = floor(($diff - $years * 365*60*60*24  
         - $months*30*60*60*24 - $days*60*60*24 
                - $hours*60*60 - $minutes*60));  

// Print the result 
//$result= $years.$months.$days.$hours.$minutes.$seconds; 
$daysstr = $days.' Days ';
if($days<=0){
$daysstr = ' ';

}

$hourstr = $hours.' Hours ';

if($hours<=0){
$hourstr = ' ';

}

$result=$daysstr.$hourstr.$minutes.' Minutes'; 
//$result=$daysstr.$hours.' Hours '.$minutes.' Minutes '; 

return $result;
}
?>

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
						 <li class="active"><span>Settlement List</span></li>
					  </ol>
				   </div>
				</div>
				
				
				
				<div class="main-box clearfix">
				
				<div class="widget-users">
					<li style="height:auto; border:0px">
						<div class="details" style="margin:10px 10px;">
							<div class="name">
								<a href="#">Hostess Settlement List</a>
							</div>
							<div style="margin:10px 0px;">
								<i class="fa fa-user"></i> Agency Name : <?php echo $this->session->userdata('nickname');?>
							</div>
							<div class="type">
								<span class="label label-success">ID: <?php echo $this->session->userdata('uniqueID');?></span>
							</div>
						</div>
					</li>
				</div>
		<div class="tab-content tab-content-body clearfix">
		   <div class="tab-pane fade active in" id="Agreed-List">
			  <div class="main-box clearfix">
				   <header class="main-box-header clearfix">
					  <div class="filter-block pull-left">
						 <form class="form_login" method="get">
						    
                <div class="row hide">
                    
                  	<div class="col-md-3">
                        <div class="form-group">
                        	<label>Start Date</label>
                          <input type="date" value="<?= (isset($_REQUEST['start_date']) && !empty($_REQUEST['start_date'])) ? $_REQUEST['start_date'] : ''; ?>" name="start_date" class="form-control" >
        				</div>
                     </div>

                     <div class="col-md-3">
                        <div class="form-group">
                        	<label>End Date</label>
                         <input type="date" value="<?= (isset($_REQUEST['end_date']) && !empty($_REQUEST['end_date'])) ? $_REQUEST['end_date'] : ''; ?>" name="end_date" class="form-control"  > 
        				</div>
                     </div>


                     <div class="col-md-3">
                        <div class="form-group">
                        	<br>
                 <button class="btn btn-primary py-3 px-5" style="width:auto; height:auto">Filter</button>
        				</div>
                     </div>
                <div class="col-md-3">
                	<br>
                	<a href="<?php echo site_url().'agent/hostess-list'; ?>" class="btn btn-primary py-3 px-5" style="width:auto; height:auto">Reset Filter</a>                          
        				</div>
                     </div>
                 
            </form>
						 <div class="pull-left">


						 <a class="hide" href="javascript:void(0)" data-toggle="modal" data-target="#myModal" class="btn btn-primary">
							<i class="fa fa-plus-circle fa-lg"></i> Add
						  </a>
<br>

						 </div>
					  </div>
					  <h2 class="pull-right" style="clear:none">Total of bound hostesses : <?php echo count($data);?></h2>
				   </header>

				   <?php echo $this->session->flashdata('msgs'); ?>

				   <div class="main-box-body clearfix">
					  <div class="table-responsive">
						 <table class="table DataTable">
							<thead>
							   <tr>
								  <!-- <th><a href="#"><span>Sub-Agency Name</span></a></th>
								  <th class="text-center"><span>ID</span></th>
								  <th class="text-center"><span>Phone</span></th>
								  <th class="text-center"><span>Number Of Hostesses</span></th>
								  <th class="text-center"><span>Number Of His Sub-Agents</span></th>
								  <th class="text-center"><span>Bind Time</span></th>
								  <th class="text-center"><span>Register  Time</span></th> -->

								  <th class="text-center" ><span> Sl. No. </span></th>
									<th class="text-center" ><span> User Name </span></th>
								  <th class="text-center" ><span> Unique ID </span></th>
									<th class="text-center" ><span> Amount </span></th>
									<th class="text-center" ><span> Created Date </span></th>

							   </tr>
							</thead>

								<tbody>
                       <?php
$x = 1;                       
                        foreach($data as $value){ 


?>
                       	 <tr>
                       	 	<td class="text-center" ><?php echo $x; ?></td>
                       	 	<td class="text-center" ><?php
$UserName = $this->common_model->GetSingleData('users', array('id' => $value["user_id"]));
echo $UserName["nickname"]; ?></td>
													<td class="text-center" ><?php echo $UserName["uniqueID"]; ?></td>
                       	 	<td class="text-center" ><?php echo $value["user_amount"]; ?></td>
                       	 	<td class="text-center" ><?php echo $value["created_at"]; ?></td>                       	 	
                       	 </tr>
                       	 
            
  
  
                       <?php $x++; } ?>
                     </tbody>


							<!-- <tbody>
							   <tr>
								  <td class="text-center">
									Sameer
								  </td>
								  <td class="text-center">
									#250
								  </td>
								  <td class="text-center">
									987654321
								  </td>
								  <td class="text-center">
									1
								  </td>
								  <td class="text-center">
									2
								  </td>
								  <td class="text-center">
									1h
								  </td>
								  <td class="text-center">
									21-04-2021
								  </td>
							   </tr>
							</tbody> -->
						 </table>
					  </div>
				   </div>
				</div>
		   </div>
						   
					</div>
				</div>
			 </div>
		  </div>
	   </div>
	</div>
 </div>
</div>


<!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add Hostess</h4>
        </div>
        <div id="MessageErr"></div>
       <form method="post" onsubmit="return Add();" id="Add" autocomplete="off">		

	        <div class="modal-body">

			   <div class="form-group">
			    <label for="">Nick Name</label>
			    <input type="text" class="form-control" name="nickname" required placeholder="Enter nick name" />
			  </div>

			  <div class="form-group">
			    <label for="">Email</label>
			    <input type="email" class="form-control" name="email" required placeholder="Enter email" />
			  </div>

			  <div class="form-group">
			    <label for="">Phone</label>
			    <input type="text" class="form-control" name="phone" required placeholder="Enter phone" />
			  </div>

			  <div class="form-group">
			    <label for="">Date of birth</label>
			    <input type="date" class="form-control" name="dob" required/>
			  </div>

	        <div class="form-group">
			    <label for="">Gender</label>
			    <select class="form-control" name="gender" required>
			    	<option value="">---Select gender---</option>
			    	<option value="male">Male</option>
			    	<option value="female">Female</option>
			    </select>
			  </div>

			  <div class="form-group">
			    <label for="">Country</label>
			    <select class="form-control" name="country" required>
			    	<option value="">---Select country---</option>
<?php
foreach ($countryList as $country) {
	?>
					<option value="<?php echo $country["id"]; ?>"><?php echo $country["nicename"];?></option>
	<?php
}
?>			    	

			    </select>
			  </div>

			  <div class="form-group">
			    <label for="">City</label>
			    <input type="text" class="form-control" name="city" required placeholder="Enter city" />
			  </div>
			  <div class="form-group">
			    <label for="">Language 1</label>
			    <select class="form-control" name="lng1" required="">
			    	<option value="">---Select language 1---</option>
<?php 
foreach ($language as $valLang) {
?>
			    	<option value="<?php echo $valLang["id"]; ?>"><?php echo $valLang["name"]; ?></option>
<?php
}
?>			    	

			    </select>
			  </div>
			  <div class="form-group">
			    <label for="">Language 2</label>
			    <select class="form-control" name="lng2" required="">
			    	<option value="">---Select language 2---</option>
<?php 
foreach ($language as $valLang) {
?>
			    	<option value="<?php echo $valLang["id"]; ?>"><?php echo $valLang["name"]; ?></option>
<?php
}
?>			    	

			    </select>
			  </div>

	        </div>

	        <div class="modal-footer">
	          <button type="submit" class="btn btn-info" id="AddBtn">Submit<i style="display:none" class="spinner fa fa-spinner fa-spin fa-fw btn-load"></i>
	          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        </div>
        </form>
      </div>
      
    </div>
  </div> 


<?php include ('include/footer.php') ?>



<script>
 function Add() {
 $.ajax({
    url: "<?php echo base_url(); ?>agent/User/AddHostess",
    type:"POST",
    cache:false,
        contentType: false,
        processData: false,
    data:new FormData($('#Add')[0]),
    dataType:'json',
    beforeSend:function(){
      $('#AddBtn').prop('disabled',true);
      $('.btn-load').show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageErr").html(data.message);
        $('#AddBtn').prop('disabled',false);
        $('.btn-load').hide();
      }
    }
  });
    return false;

}

 function Edit(FormId) {
 $.ajax({
    url: "<?php echo base_url(); ?>agent/User/EditHostess",
    type:"POST",
    cache:false,
    contentType: false,
    processData: false,
    data:new FormData($('#Edit'+FormId)[0]),
    dataType:'json',
    beforeSend:function(){
      $('#EditBtn'+FormId).prop('disabled',true);
      $('.btn-loadEdit'+FormId).show();
    },
    success:function(data) {
      if(data.status==1){
        location.reload();
      } 
      else {
        $("#MessageUpdateErr"+FormId).html(data.message);
        $('#EditBtn'+FormId).prop('disabled',false);
        $('.btn-loadEdit'+FormId).hide();
      }
    }
  });
    return false;

}
</script>

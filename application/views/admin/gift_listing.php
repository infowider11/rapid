<?php 
include_once('include/header.php'); 
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<h1>Gift<small>management</small></h1>
		
    </section>

    <!-- Main content -->
    <section class="content">
		<?php
      if(isset($_SESSION['success'])){
        echo $_SESSION['success'];
        unset($_SESSION['success']);
      }
      if(isset($_SESSION['error'])){
        echo $_SESSION['error'];
        unset($_SESSION['error']);
      }
    ?>
		<div class="error-msg"></div>
		<div class="row">
			<!-- left column -->
			<div class="col-md-12">
				<!-- general form elements -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Gift List</h3>
						<div style="float: right;"><a href="javascript:void(0)" class="btn btn-success" data-toggle="modal" data-target="#myModal">Add New</a></div>
					</div>
					<div id="MessageUpdateErr"></div>	
					<div class="table-responsive" style="padding: 20px;">			
					<table class="table table-striped table-bordered DataTable">
						<thead>
						<tr>
							<th>SNo.</th>
							<th>Name</th>
							<th>Diamond</th>
							<th>Coin</th>
							<th>Category</th>
							<th>Sticker</th>
							<th>Animation</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
                       <?php
                        if($gifts){ 
                        foreach($gifts as $key=>$gift){ ?>
                       	 <tr>
                       	 	<td><?=$key+1?></td>
                       	 	<td><?=$gift['name']?></td>
                       	 	<td><?=$gift['daimond']?></td>
                       	 	<td><?=$gift['coin']?></td>
                       	 	<td><?php
$categoryF = $this->common_model->GetSingleData('gift_category',array('id' => $gift['category']));
echo $categoryF["name"];
                       	 	?></td>
                       	 	<td>
                       	 	    	<img style="height: 50px;width: 50px;" src="<?php echo base_url('assets/admin/gift/');?>	<?=$gift['sticker']?>">
						
					</td>
					<td>
						<?php 
						if ($gift['animation']) {
						?>
<img style="height: 50px;width: 50px;" src="<?php echo base_url('assets/admin/gift/');?>	<?=$gift['animation']?>">
						<?php
						} 
						?>
                       	 	    	
						
					</td>
                       	 	<td>
                       	 		<!-- data-toggle="modal" data-target="#myModal" -->
                       	 	<a    data-toggle="modal" data-target="#editmyModal<?php echo $gift['id'];?>" class="btn btn-success">Edit</a>

                    <a   onclick="return  confirm('Are you sure want to delete this gift?');" href="<?php echo base_url();?>Admin/Users/delete_gift/<?php echo $gift['id'];?>"  class="btn btn-danger">Delete</a>

                       	 	</td>
                       	 </tr>
                       	 
                       	 
                       	 	<!-- Modal -->
  <div class="modal fade" id="editmyModal<?php echo $gift['id'];?>" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Edit Gift</h4>
        </div>

       <form action="<?php echo base_url(); ?>Admin/Users/edit_gift/<?php echo $gift['id'];?>" method="post" enctype="multipart/form-data" autocomplete="off">		

			<input type="hidden"  value="<?php echo $gift['id'];?>">

	        <div class="modal-body">
	        	<div class="form-group">
			    <label for="">Category</label>
			    <select class="form-control" name="category" required="">
			    	<option value="">---Select category---</option>
<?php
foreach ($category as $valuCat) {
					?> 
					<option value="<?php echo $valuCat["id"]; ?>" <?php if ($valuCat["id"] == $gift['category']){ echo "selected"; }?> ><?php echo $valuCat["name"]; ?></option>
					<?php
}

?>			    	

			    </select>
			  </div>
			  <div class="form-group">
			    <label for="">Name</label>
			    <input type="text" class="form-control" name="name" required placeholder="Name" value="<?php echo $gift['name'];?>">
			  </div>
			   <div class="form-group">
			    <label for="">Diamond</label>
			    <input type="text" class="form-control" value="<?php echo $gift['daimond'];?>"  name="daimond" required >
			  </div>
	        <div class="form-group">
			    <label for="">Coin</label>
			    <input type="text" class="form-control" name="coin" value="<?php echo $gift['coin'];?>" required placeholder="coin">
			  </div>   	
			   
			   <div class="form-group">
			    <label for="">Sticker</label>
			  <input type="file" class="form-control" id="image1" value="<?php echo $data['sticker'];?>" onchange="loadFile1(event)" accept="image/*" name="sticker"  >
			  </div>

			  <div class="form-group">
			    <label for="">Animation</label><br>
			    <?php 
						if ($gift['animation']) {
						?>
<img style="height: 50px;width: 50px;" src="<?php echo base_url('assets/admin/gift/');?>	<?=$gift['animation']?>">
						<?php
						} 
						?>
		<input type="file" class="form-control" accept="image/*" name="animation" >
			  </div>

	        </div>

	        <div class="modal-footer">
	          <button  class="btn btn-default" >Update</button>	
	          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        </div>
        </form>
      </div>
      
    </div>
  </div> 
  
  
                       <?php } } ?>
                     </tbody>	

					</table>
				</div>
				</div>
			</div>

		</div>
    </section>
</div>
 
 <?php include_once('include/footer.php'); ?>
 
 <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add Gift</h4>
        </div>

       <form action="<?php echo base_url(); ?>Admin/Users/add_gift" method="post" enctype="multipart/form-data" autocomplete="off">		

	        <div class="modal-body">

	        	<div class="form-group">
			    <label for="">Category</label>
			    <select class="form-control" name="category" required="">
			    	<option value="">---Select category---</option>
<?php
foreach ($category as $valuCat) {
					?> 
					<option value="<?php echo $valuCat["id"]; ?>"><?php echo $valuCat["name"]; ?></option>
					<?php
}

?>			    	

			    </select>
			  </div>
			  <div class="form-group">
			    <label for="">Name</label>
			    <input type="text" class="form-control" name="name" required placeholder="Name">
			  </div>
			   <div class="form-group">
			    <label for="">Diamond</label>
			    <input type="text" class="form-control" name="daimond" required placeholder="Diamond">
			  </div>
			  <div class="form-group">
			    <label for="">Coin</label>
			    <input type="text" class="form-control" name="coin" required placeholder="coin">
			  </div>
	        
	       
	       
			   <div class="form-group">
			    <label for="">Image</label>
		<input type="file" class="form-control" onchange="loadFile(event)" id="sticker" accept="image/*" name="sticker" required >
			  </div>

			  <div class="form-group">
			    <label for="">Animation</label>
		<input type="file" class="form-control" accept="image/*" name="animation" >
			  </div>

	        </div>

	        <div class="modal-footer">
	          <button  class="btn btn-default" >Add</button>	
	          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        </div>
        </form>
      </div>
      
    </div>
  </div> 

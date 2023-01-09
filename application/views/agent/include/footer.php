<!--div id="config-tool" class="closed">
         <a id="config-tool-cog">
         <i class="fa fa-cog"></i>
         </a>
         <div id="config-tool-options">
            <h4>Layout Options</h4>
            <ul>
               <li>
                  <div class="checkbox-nice">
                     <input type="checkbox" id="config-fixed-header" />
                     <label for="config-fixed-header">
                     Fixed Header
                     </label>
                  </div>
               </li>
               <li>
                  <div class="checkbox-nice">
                     <input type="checkbox" id="config-fixed-sidebar" />
                     <label for="config-fixed-sidebar">
                     Fixed Left Menu
                     </label>
                  </div>
               </li>
               <li>
                  <div class="checkbox-nice">
                     <input type="checkbox" id="config-fixed-footer" />
                     <label for="config-fixed-footer">
                     Fixed Footer
                     </label>
                  </div>
               </li>
               <li>
                  <div class="checkbox-nice">
                     <input type="checkbox" id="config-boxed-layout" />
                     <label for="config-boxed-layout">
                     Boxed Layout
                     </label>
                  </div>
               </li>
               <li>
                  <div class="checkbox-nice">
                     <input type="checkbox" id="config-rtl-layout" />
                     <label for="config-rtl-layout">
                     Right-to-Left
                     </label>
                  </div>
               </li>
            </ul>
            <br />
            <h4>Skin Color</h4>
            <ul id="skin-colors" class="clearfix">
               <li>
                  <a class="skin-changer" data-skin="" data-toggle="tooltip" title="Default" style="background-color: #34495e;">
                  </a>
               </li>
               <li>
                  <a class="skin-changer" data-skin="theme-white" data-toggle="tooltip" title="White/Green" style="background-color: #2ecc71;">
                  </a>
               </li>
               <li>
                  <a class="skin-changer blue-gradient" data-skin="theme-blue-gradient" data-toggle="tooltip" title="Gradient">
                  </a>
               </li>
               <li>
                  <a class="skin-changer" data-skin="theme-turquoise" data-toggle="tooltip" title="Green Sea" style="background-color: #1abc9c;">
                  </a>
               </li>
               <li>
                  <a class="skin-changer" data-skin="theme-amethyst" data-toggle="tooltip" title="Amethyst" style="background-color: #9b59b6;">
                  </a>
               </li>
               <li>
                  <a class="skin-changer" data-skin="theme-blue" data-toggle="tooltip" title="Blue" style="background-color: #2980b9;">
                  </a>
               </li>
               <li>
                  <a class="skin-changer" data-skin="theme-red" data-toggle="tooltip" title="Red" style="background-color: #e74c3c;">
                  </a>
               </li>
               <li>
                  <a class="skin-changer" data-skin="theme-whbl" data-toggle="tooltip" title="White/Blue" style="background-color: #3498db;">
                  </a>
               </li>
            </ul>
         </div>
      </div-->
      <script 	src="<?php echo site_url(); ?>assets/agent/js/demo-skin-changer.js"></script> 
      <script src="<?php echo site_url(); ?>assets/agent/js/jquery.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/bootstrap.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/jquery.nanoscroller.min.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/demo.js"></script> 
      <script src="<?php echo site_url(); ?>assets/agent/js/jquery-ui.custom.min.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/fullcalendar.min.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/jquery.slimscroll.min.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/raphael-min.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/morris.min.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/moment.min.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/daterangepicker.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/jquery-jvectormap-1.2.2.min.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/jquery-jvectormap-world-merc-en.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/gdp-data.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/flot/jquery.flot.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/flot/jquery.flot.min.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/flot/jquery.flot.pie.min.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/flot/jquery.flot.stack.min.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/flot/jquery.flot.resize.min.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/flot/jquery.flot.time.min.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/flot/jquery.flot.threshold.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/jquery.countTo.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/scripts.js"></script>
      <script src="<?php echo site_url(); ?>assets/agent/js/pace.min.js"></script>
      <script src="<?php echo base_url(); ?>assets/admin/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript">
   
  $(function(){

        $('#sidebar-nav > .nav-pills > li > a, #sidebar-nav .submenu li a').filter(function(){return this.href==location.href}).parent().addClass('active').siblings().removeClass('active')

        $('#sidebar-nav > .nav-pills > li > a, #sidebar-nav .submenu li a').click(function(){

            $(this).parent().addClass('active').siblings().removeClass('active')    

        })

    })

</script>
   </body>
</html>
<script>
  $(function() {
    $('.DataTable').DataTable();
  });
</script>
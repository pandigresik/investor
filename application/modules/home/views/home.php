<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	  <base href="<?php echo base_url(); ?>" />
    <!-- Bootstrap -->
    <link href="assets/libs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="assets/libs/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="assets/libs/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="assets/libs/iCheck/skins/flat/green.css" rel="stylesheet">
    <!-- bootstrap-datetimepicker -->
    <link href="assets/libs/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css" rel="stylesheet">

	  <link href="assets/libs/jquery-ui/css/jquery-ui.min.css" rel="stylesheet">
    <!-- Select2 -->
    <link href="assets/libs/select2/css/select2.min.css" rel="stylesheet" />
    <!-- tooltipster -->
    <link href="assets/libs/tooltipster/css/tooltipster.bundle.min.css" rel="stylesheet" />

    <!-- FullCalendar -->
    <link href="assets/libs/fullcalendar/dist/fullcalendar.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="assets/css/custom.min.css" rel="stylesheet">
    <link href="assets/css/base.css" rel="stylesheet">
    <link href="assets/css/colorbox.css" rel="stylesheet">



	<!-- Custom Theme Scripts -->


  </head>

  <body class="nav-md">
    
    <div class="container body">
      <div class="main_container">


        <!-- top navigation -->
        <div class="top_nav">
          <div class="nav_menu">
            <nav>
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>
              <div class="navbar nav_title" style="border: 0;margin-left:-20px">
                <div class="brand-img">
                  <a href="<?php echo base_url(); ?>" class="" style="float:left">
                    <img style="display:inline-block" src="<?php echo site_url('assets/images/brand-ico.png'); ?>" alt="">
                  </a>
                  <div class="brand-header-toolbar">
                    Kreditansyari.com
                  </div>
                  <div class="brand-motto-toolbar">
                    CV. Syariah Solution
                  </div>
                </div>
                
              </div>
              
              <ul class="nav navbar-nav navbar-right">
                <li>
                  <div class="nav toggle">
                    <a href="user/user/logout"><i class="fa fa-sign-out pull-right"></i> &nbsp;</a>
                  </div>
                </li>
                <li>
                    <div class="menu-name"><?php echo isset($user['NAMABP']) ? $user['NAMABP'] : 'Not Defined'; ?><br><small style="margin-top: 6px;"> <?php echo $waktuAkses; ?></small></div>                  
                </li>
              </ul>
            </nav>
          </div>
        </div>
        <!-- /top navigation -->
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <!-- <div class="clearfix"></div> -->

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <!-- <h3>General</h3> -->
                <ul class="nav side-menu" id="navbar">
                    <?php echo $menu; ?>
                </ul>
              </div>

            </div>
            
            <!-- /sidebar menu -->

          </div>
        </div>
        <!-- page content -->
        <!-- page content -->
        <div class="right_col" role="main">
          <div id="main_content" role="main">

          </div>
          <div id="loadingContentView" class="text-center" style="display:none;">
            <img src="assets/images/iLoading.gif" alt="">
          </div>
        </div>
        <!-- /page content -->

        <!-- /page content -->

        <!-- footer content -->
        <footer>

        </footer>
        <!-- /footer content -->
      </div>
    </div>

		<!-- jQuery -->
    <script src="assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="assets/libs/jquery_price_format/jquery.priceformat.min.js"></script>
    <script src="assets/libs/jquery/jquery.redirect.js"></script>
    <script src="assets/libs/jquery/jquery.colorbox-min.js"></script>

    <!-- Bootstrap -->
    <script src="assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="assets/libs/moment/min/moment.min.js"></script>
	  <script src="assets/libs/moment/locale/id.js" charset="UTF-8"></script>
    <script src="assets/libs/bootbox/js/bootbox.js"></script>
    <script src="assets/libs/datepicker/daterangepicker.js"></script>


    <script src="assets/libs/jquery-ui/js/jquery-ui.min.js"></script>
    <script src="assets/libs/jquery-ui/js/jquery.ui.datepicker-id.js"></script>
  
    <!-- Library select 2 -->
    <script src="assets/libs/select2/js/select2.min.js"></script>
    <!-- tooltipster -->
    <script src="assets/libs/tooltipster/js/tooltipster.bundle.min.js"></script>
    <!-- bootstrap-datetimepicker -->
    <script src="assets/libs/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    <!-- validate -->
    <script src="assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
    <script src="assets/libs/fullcalendar/dist/fullcalendar.min.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/common.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="assets/js/index.js"></script>
    
  </body>
</html>

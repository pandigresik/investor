<!DOCTYPE html>
<html>
<head>
	<title>Dashboard</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<base href="<?php echo base_url()?>" />
  <script type="text/javascript" src="assets/libs/jquery/jquery-2.0.0.min.js"></script>
  <script type="text/javascript" src="assets/js/common.js"></script>
</head>
<body>
   <div class="container col-md-12 ">
		<nav class="navbar navbar-default">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <span class="btn navbar-brand" href="#" onclick="window.location.reload()">KS</span>
          </div>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
              <?php //echo $menu ?>
			  
			</ul>
            <ul class="nav navbar-nav navbar-right">
               <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Tes<span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                  <li><a id="popup_gantipassword">Ganti Password</a></li>
                  <li><a href="user/user/logout">Logout</a></li>
                </ul>
              </li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>
		<div id="main_content" class="main_content">
			  <?php echo $content ?>
		</div>
	</div>
</body>
<link rel="stylesheet" media="all" type="text/css" href="assets/libs/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="assets/css/home.css" >


</html>

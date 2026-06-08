<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="keyword" content="">
	<link rel="icon" type="image/png" href="images/ico.png">

    <title>Zuzuvama</title>

    <!-- Bootstrap core CSS -->
    <link href="styles/bootstrap.css" rel="stylesheet">
    <!--external css-->
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="styles/zabuto_calendar.css">
    <link rel="stylesheet" type="text/css" href="assets/js/gritter/css/jquery.gritter.css" />
    <link rel="stylesheet" type="text/css" href="assets/lineicons/style.css">

    <!-- Custom styles for this template -->
    <link href="styles/style.css" rel="stylesheet">
    <link href="styles/style-responsive.css" rel="stylesheet">
	<link href="styles/table-responsive.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

  </head>

  <body>

  <section id="container" >
      <!-- **********************************************************************************************************************************************************
      TOP BAR CONTENT & NOTIFICATIONS
      *********************************************************************************************************************************************************** -->
      <!--header start-->
      <header class="header white-bg">
              <div class="sidebar-toggle-box">
                  <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
              </div>
            <!--logo start-->
            <a href="index.html" class="logo"><strong>A</strong>dmin</a></a>
            <!--logo end-->
            <div class="nav notify-row" id="top_menu">
                <!--  notification start -->
                <ul class="nav top-menu">

                </ul>
                <!--  notification end -->
            </div>
            <div class="top-menu">
            	<ul class="nav pull-right top-menu">
                    <li>
					<?php if(isset($_SESSION['ID'])){ ?>
						<a class="logout" href="?page=logout">Log Out</a>
					<?php }else{ ?>
                       <a class="logout" data-toggle="modal" href="#modalLogin">Log In</a>
					<?php } ?>
					</li>
            	</ul>
            </div>
        </header>
      <!--header end-->

      <!-- **********************************************************************************************************************************************************
      MAIN SIDEBAR MENU
      *********************************************************************************************************************************************************** -->
      <!--sidebar start-->
      <aside>
          <div id="sidebar"  class="nav-collapse ">
              <!-- sidebar menu start-->
              <ul class="sidebar-menu" id="nav-accordion">
			<?php if(isset($_SESSION['ID'])){?>
              	  <p class="centered"><a href="?page=profile"><img src="assets/img/logo.png" class="img-circle" width="60"></a></p>
              	  <h5 class="centered"><?php echo $_SESSION['name']; ?></h5>
			<?php
include("include/menu/sub-menu.php");
				/* if ( in_array($_SESSION['role'], array('ADM','ADS'), true ) ) {
					 include("include/menu/sub-menu-adm.php");
				 }else{
					 include("include/menu/sub-menu.php");
				 }*/
			}else{
				 include("include/menu/sub-menu.php");
			}
			?>
              </ul>
              <!-- sidebar menu end-->
          </div>
      </aside>
      <!--sidebar end-->

	    <!-- **********************************************************************************************************************************************************
      MAIN CONTENT
      *********************************************************************************************************************************************************** -->
  <!--main content start-->
  <section id="main-content">
      <section class="wrapper site-min-height">
      <br>
      <?php if($page!='login'){  require_once 'include/lib/logerror.lib.php'; }  ?>

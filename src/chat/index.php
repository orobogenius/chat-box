<?php

  setcookie('userID', '12345678', time() + (60*60));

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta name="author" content="orobogenius" >
  	<meta name="description" content="Chatbox">
  	<meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
   <title>Chat</title>
   <!-- CSS Includes -->
   <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
   <link rel="stylesheet" type="text/css" href="font-awesome/css/font-awesome.css" />
   <link rel="stylesheet" type="text/css" href="css/style.css" />
   <!-- ./css includes -->
</head>
<body>

    <div class="chat-page">

      <!-- Top Navigation -->
      <nav class="navbar navbar-static-top navbar-default" role="navigation">
        <div class="container">
          <!-- Navigation Header -->
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#admin-sidebar-nav" aria-expanded="false">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar top-bar"></span>
              <span class="icon-bar mid-bar"></span>
              <span class="icon-bar bot-bar"></span>
            </button>
            <a class="navbar-brand" href="#" style="color: #ffffff;">Chat-Box</a>
          </div>
          <!-- /.Navigation Header-->

          <!-- Navigation Top Links -->
          <ul class="nav navbar-right nav-top-links">
            <!-- Notifcations Menu -->
            <li class="dropdown notifications-menu">
               <a class="dropdown-toggle" href="#">
                <i class="fa fa-bell-o"></i>  <i class="fa fa-caret-down"></i>
                <span class="label label-warning" id="admin-notif-num"></span>
              </a>
              <ul class="dropdown-menu notifications-dropdown" id="request-list">
              </ul>
            </li><!-- /.Notifications Menu-->
          </ul> <!-- /.Nav TopLinks -->
        </div>
      </nav>
      <!-- /.Top Navigation -->
      <!-- Sidebar Navigation -->
      <div class="admin-sidebar-nav navbar-default sidebar-effect collapse navbar-collapse" id="admin-sidebar-nav">
         <div class="profile">
          <a href="#" id="cam-icon"><span class="glyphicon glyphicon-camera"></span></a>
					<form action="" method="post" id="picture-form" enctype="multipart/form-data">
						<input type="file" id="pic" name="profilePic" style="display: none;" accept="image/*" />
					</form>
					<ul id="upload-menu">
						<li>
							<a href="#" id="upload-pic">Upload Image</a>
						</li>
						<li>
							<a href="#" id="remove-pic">Remove Image</a>
						</li>
					</ul>
       </div>
       <div class="sidebar-search">
         <div class="input-group">
           <input type="search" class="form-control" name="query" id="search" placeholder="Search by Chat ID" aria-describedby="search-addon" onkeypress="return isNumberKey(this)" />
           <span class="input-group-addon admin-input-addon"><i class="fa fa-search"></i></span>
         </div>
       </div>
       <div class="contacts">
          <h4 style="text-align: center;">Contacts</h4>
          <hr />
          <div class="contact-list">
            <ul id="contact-list">

            </ul>
          </div>
       </div>
      </div> <!-- /.Sidebar Navigation -->

      <div class="container admin-content-wrapper">
         <div class="current-chat-contact" id="current-chat-contact">

         </div>
         <div class="chat" id="chat">

         </div>
      </div>
      <footer>
         <div id="send-message" class="form-inline">
            <textarea id="message-box" class="form-control"></textarea>
            <button class="btn btn-primary" id="send"><img  src="images/sending.gif" class="loading-icon" alt="Operation in progress" /> Send</button>
        </div>
      </footer>
      <input type="hidden" id="userID" value="<?php echo $_COOKIE['userID']; ?>" />
   </div>
    <!-- Javascript Includes -->
   <script type="text/javascript" src="js/jquery.js"></script>
   <script type="text/javascript" src="js/bootstrap.js"></script>
   <script type="text/javascript" src="js/script.js"></script>
   <!-- ./js includes -->
</body>
</html>

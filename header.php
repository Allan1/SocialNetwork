<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<script src='js/javascript.js'></script>
<link rel='stylesheet' href='css/styles.css' type='text/css'>
<style type="text/css">
  #header {
    padding-bottom: 5px;
    box-shadow: 0px 1px 4px #CCC;
    z-index: 200;
    background: none repeat scroll 0% 0% #F4F4F4;
    border-bottom: 1px solid #CDCDCD;
    position: fixed;
    top: 0px;
    width: 100%;
}
#header .container {
    margin: 0px auto;
    /*width: 980px;*/
    width: 80%;
}
#header .container .login, #header .container .menu {
    margin-right: 12px;
    float: right;
}
ol, ul {
    list-style: outside none none;
}
#header h1 {
    float: left;
    margin: 5px;
}
#header .container .login li {
    padding-left: 10px;
    float: left;
}
label {
    font-weight: bold;
    color: #000;
    font-size: 12px;
    line-height: 16px;
}
#header .container .login .forgot-pwd {
    display: inline-block;
    vertical-align: middle;
    margin-left: 5px;
    font-size: 11px;
    margin-top: -4px;
}
#login, #login ul{
  margin: 0;
}
#messageBox{
  position: fixed;
  top: 70px;
  width: 100%;
  z-index: 300;
  text-align: center;
}
#messageBox p {
  background-color: #E1E1E1;
}
</style>
<?php // Example 26-2: header.php
  ob_start();

  session_start();

  require_once 'functions.php';

  $userstr = ' (Guest)';

  if (isset($_SESSION['user']))
  {
    $user     = $_SESSION['user'];
    $loggedin = TRUE;
    $userstr  = " ($user)";
  }
  else $loggedin = FALSE;
  echo "<title>$appname$userstr</title>";
?>
</head>
<body>
  <center>
<?php echo "<canvas id='logo' width='624' height='96'>$appname</canvas></center>"             .
       "<div class='appname'>$appname$userstr</div>";

  
?>
<div id="header">
  <div class="container">
    <h1>
      <img src="img/robin.gif" alt="Robin" class="logo" >
      <!--<canvas id="logo"></canvas>-->
    </h1>

    <?php if($loggedin): ?>
      <ul class='menu'>
      <?php echo "<li><a href='members.php?view=$user'>Home</a></li>";?>

         <li><a href='members.php'>Members</a></li>
         <li><a href='friends.php'>Friends</a></li>
         <li><a href='messages.php'>Messages</a></li>
         <li><a href='profile.php'>Edit Profile</a></li>
         <li><a href='logout.php'>Log out</a></li>
      </ul>
    <?php else: ?>
      <div class="login">
        <form action="login.php" method="POST" novalidate="novalidate" id="login">
          <fieldset>
            <legend>Sign In</legend>
              <ul>
                  <li>
                    <label>Username</label>
                    <div >
                      <input name="user" value="" autofocus="" tabindex="1" size="27" type="text">
                    </div>
                  </li>
                  <li>
                    <label>Password</label>
                      <a href="#" class="forgot-pwd" tabindex="4">Forgot your password?</a>
                    <div >
                      <input name="pass" value="" tabindex="2" size="27" type="password">
                    </div>
                  </li>
                <li >
                  <input  name="signin" value="Sign In" id="signin" tabindex="3" type="submit">
                </li>
              </ul>
          </fieldset>
        </form>
      </div>
    <?php endif;?>
  </div>
</div>
<div id="messageBox">
  <p>
      <?php
      if (isset($_SESSION['message'])) {
        echo "<span class=''>&#8658;".$_SESSION['message']."</span>";
        $_SESSION['message'] = NULL;  
      }
      ?>
    </p>  
</div>

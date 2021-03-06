<?php // Example 26-7: login.php
  require_once 'header.php';

  $_SESSION['message'] = $user = $pass = "";
  if (isset($_POST['user']))
  {
    $user = sanitizeString($_POST['user']);
    $pass = sanitizeString($_POST['pass']);
    
    if ($user == "" || $pass == "")
        $_SESSION['message'] = "<span class='error'>Not all fields were entered</span>";
    else
    {
      $hashedPW = hash('sha256', $pass);
      $result = queryMySQL("SELECT user,pass FROM members WHERE user='$user' AND pass='$hashedPW'");

      if ($result->num_rows == 0)
      {
        $_SESSION['message'] = "<span class='error'>Username/Password invalid</span>";
      }
      else
      {
        $_SESSION['user'] = $user;
        // $_SESSION['pass'] = $pass;
        $_SESSION['message'] = "You are now logged in.";
        ob_clean();
        header("Location: home.php");
        
        die();
        //die("You are now logged in. Please <a href='members.php?view=$user'>" ."click here</a> to continue.<br><br>");
      }
    }
  }
  header("Location: index.php");
  die();
?>
  </body>
</html>

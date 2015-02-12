<?php // Example 26-6: checkuser.php
  require_once 'functions.php';
  if (isset($_POST['user']))
  {
    $user   = sanitizeString($_POST['user']);
    $result = queryMysql("SELECT * FROM members WHERE user='$user'");
    if ($result->num_rows)
      echo "taken";
    else
      echo "available";
  }
?>

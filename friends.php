<?php // Example 26-10: friends.php
  require_once 'header.php';

  if (!$loggedin) die();

  if (isset($_GET['view'])) $view = sanitizeString($_GET['view']);
  else                      $view = $user;

  if ($view == $user)
  {
    $name1 = $name2 = "Your";
    $name3 =          "You are";
  }
  else
  {
    $name1 = "<a href='members.php?view=$view'>$view</a>'s";
    $name2 = "$view's";
    $name3 = "$view is";
  }

  echo "<div class='main'>";

  // Uncomment this line if you wish the user’s profile to show here
  // showProfile($view);

  $followers = array();
  $following = array();

  $result = queryMysql("SELECT * FROM friends WHERE user='$view'");
  $num    = $result->num_rows;
  for ($j = 0 ; $j < $num ; ++$j)
  {
    $row           = $result->fetch_array(MYSQLI_ASSOC);
    $followers[$j] = $row['friend'];
  }

  $result = queryMysql("SELECT * FROM friends WHERE friend='$view'");
  $num    = $result->num_rows;

  for ($j = 0 ; $j < $num ; ++$j)
  {
      $row           = $result->fetch_array(MYSQLI_ASSOC);
      $following[$j] = $row['user'];
  }

  $mutual    = array_intersect($followers, $following);
  $followers = array_diff($followers, $mutual);
  $following = array_diff($following, $mutual);
  $friends   = FALSE;

  //retrive sugestions
  $sugestions = array();
  $list = "(";
  foreach ($following as $value) {
    $list.="'".$value."',";
  }
  if ($list[strlen($list)-1]==",") {
    $list = substr($list, 0, strlen($list)-1);
  }
  $list.=")";

  if($list!='()'){
    $result = queryMysql("SELECT * FROM friends WHERE friend in $list group by user");
    $num    = $result->num_rows;
    for ($j = 0 ; $j < $num ; ++$j)
    {
      $row           = $result->fetch_array(MYSQLI_ASSOC);
      $sugestions[$j] = $row['user'];
    }
  }
  



  if (sizeof($mutual))
  {
    echo "<span class='subhead'>$name2 mutual friends</span><ul>";
    foreach($mutual as $friend)
      echo "<li><a href='members.php?view=$friend'>$friend</a>";
    echo "</ul>";
    $friends = TRUE;
  }

  if (sizeof($followers))
  {
    echo "<span class='subhead'>$name2 followers</span><ul>";
    foreach($followers as $friend)
      echo "<li><a href='members.php?view=$friend'>$friend</a>";
    echo "</ul>";
    $friends = TRUE;
  }

  if (sizeof($following))
  {
    echo "<span class='subhead'>$name3 following</span><ul>";
    foreach($following as $friend)
      echo "<li><a href='members.php?view=$friend'>$friend</a>";
    echo "</ul>";
    $friends = TRUE;
  }

  if (!$friends) echo "<br>You don't have any friends yet.<br><br>";

  //echo "<a class='button' href='messages.php?view=$view'>View $name2 messages</a><br>";

  if (sizeof($sugestions))
  {
    echo "<span class='subhead'>People you may want to follow</span><ul>";
    foreach($sugestions as $friend)
      echo "<li><a href='members.php?view=$friend'>$friend</a> [<a href='members.php?add=".$friend ."'>follow</a>]</li>";
    echo "</ul>";
  }

  echo "</div>";
?>
  </body>
</html>

<?php // Example 26-1: functions.php
  $dbhost  = 'localhost';       // Unlikely to require changing
  $dbname  = 'social';           // Modify these...
  $dbuser  = 'root';           // ...variables according
  $dbpass  = 'root';           // ...to your installation
  $appname = "Social Network";  // ...and preference
  define('DS', '/');
  define("IMG_PATH", 'img'.DS);
  define("PROFILE_PICS_PATH", IMG_PATH.'profile_pics'.DS);

  $connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
  if ($connection->connect_error) die($connection->connect_error);

  function createTable($name, $query)
  {
    queryMysql("CREATE TABLE IF NOT EXISTS $name($query)");
    echo "Table '$name' created or already exists.<br>";
  }

  function queryMysql($query)
  {
    global $connection;
    $result = $connection->query($query);
    if (!$result) die($connection->error);
    return $result;
  }

  function destroySession()
  {
    $_SESSION=array();

    if (session_id() != "" || isset($_COOKIE[session_name()]))
      setcookie(session_name(), '', time()-2592000, '/');

    session_destroy();
  }

  function logout(){
    $_SESSION['user'] = null;
    $_SESSION['pass'] = null;
  }

  function sanitizeString($var)
  {
    global $connection;
    $var = strip_tags($var);
    $var = htmlentities($var);
    $var = stripslashes($var);
    return $connection->real_escape_string($var);
  }

  function showProfile($user)
  {
    $pic_path = PROFILE_PICS_PATH.$user.'.jpg';
    if (file_exists($pic_path))
      echo "<img src='$pic_path' style='float:left;'>";

    $result = queryMysql("SELECT * FROM profiles WHERE user='$user'");

    if ($result->num_rows)
    {
      $row = $result->fetch_array(MYSQLI_ASSOC);
      echo stripslashes($row['text']) . "<br style='clear:left;'><br>";
    }
  }

  function getMessages($recip,$auth = null,$pm = null)
  {
    $conditions = "recip='$recip'";
    if ($auth) {
      $conditions.= "and auth='$auth'";
    }
    if ($pm!==null) {
      $conditions.= "and pm='$pm'";
    }
    $result = queryMysql("SELECT * FROM messages WHERE ".$conditions);
    return $result;
  }
?>
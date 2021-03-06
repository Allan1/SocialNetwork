<?php // Example 26-1: functions.php
  $dbhost  = 'localhost';       // Unlikely to require changing
  $dbname  = 'social';           // Modify these...
  $dbuser  = 'root';           // ...variables according
  $dbpass  = '';           // ...to your installation
  $appname = "Social Network";  // ...and preference
  define('DS', '/');
  define("IMG_PATH", 'img'.DS);
  define("PROFILE_PICS_PATH", IMG_PATH.'profile_pics'.DS);
  date_default_timezone_set('GMT');
  
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
    // $_SESSION['pass'] = null;
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
    $result2 = queryMysql("SELECT * FROM members WHERE user='$user'");
    echo "</br>";
    if ($result->num_rows)
    {
      $row = $result->fetch_array(MYSQLI_ASSOC);
      echo 'About me: '.stripslashes($row['text']) . "</br>";
    }
    if ($result2->num_rows)
    {
      $row = $result2->fetch_array(MYSQLI_ASSOC);
      if($row['birth']!='0000-00-00')
        echo 'Born in '.dateToStringDate($row['birth']) . "</br>";
      echo "City: ".$row['city']."</br>";
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
    $result = queryMysql("SELECT * FROM messages WHERE ".$conditions." ORDER BY messages.id desc");
    return $result;
  }

  function getAllFriends($user)
  {
    $result = queryMysql("SELECT members.* FROM friends INNER JOIN members ON ((friends.user = members.user and friends.friend = '{$user}') OR (friends.friend = members.user and members.user = '{$user}')) WHERE members.user<>'{$user}' GROUP BY members.user ORDER BY members.first_name, members.user asc");
    $num    = $result->num_rows;
    $rows = array();
    for ($j = 0 ; $j < $num ; ++$j)
    {
      $rows[] = $result->fetch_array(MYSQLI_ASSOC);
    }
    $result = $rows;
    return $result;
  }

  function getConversation($user,$currentFriend)
  {
    $conversation = queryMysql("SELECT messages.* FROM messages WHERE ((messages.auth = '{$currentFriend}' and messages.recip = '{$user}') OR (messages.recip = '{$currentFriend}' and messages.auth = '{$user}')) and messages.pm=1 ORDER BY messages.id desc");
    $num    = $conversation->num_rows;
    for ($j = 0 ; $j < $num ; ++$j)
    {
      $aux = $conversation->fetch_array(MYSQLI_ASSOC);
      $aux['time'] = date('d/m/Y H:i:s', $aux['time']);
      $rows[] = $aux;
    }
    $conversation = $rows;
    return $conversation;
  }

  function getUser($user)
  {
    $result = queryMysql("SELECT members.* FROM members WHERE members.user = '{$user}'");
    $num    = $result->num_rows;
    for ($j = 0 ; $j < $num ; ++$j)
    {
      $rows[] = $result->fetch_array(MYSQLI_ASSOC);
    }
    $result = $rows;
    return $result;
  }

  function isDate($value)
  {
    // echo preg_match("/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/", $value);
    return preg_match("^\\d{1,2}/\\d{2}/\\d{4}^", $value);
  }

  function stringDateToDate($value)
  {
    if (isDate($value)) {
      $date = DateTime::createFromFormat('d/m/Y', $value);
      return $date->format('Y-m-d');
    }
    return null;
  }

  function dateToStringDate($value)
  {
    if ($value=='0000-00-00') {
      return '';
    }
    $date = DateTime::createFromFormat('Y-m-d', $value);
    return $date->format('d/m/Y');
  }
?>

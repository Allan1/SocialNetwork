<?php // home.php, similar to members.
  require_once 'header.php';

  if (!$loggedin) {
    header("Location: index.php");
    die();
  }

  echo "<div class='main'>";

  if (isset($_POST['text']) and strlen($_POST['text'])>0) {
    $_POST['text'] = sanitizeString($_POST['text']);
    $pm   = substr(sanitizeString($_POST['pm']),0,1);
    $time = time();
    $text = $_POST['text'];
    $r = queryMysql("INSERT INTO messages VALUES(NULL, '$user',
      '$user', '$pm', $time, '$text')");
    if($r)
      echo "<span>Message posted successfully</span>";
    else
      echo "<span>Couldn't post the message. Please, try again.</span>";
  }

  if (isset($_POST['erase']))
  {
    $erase = sanitizeString($_POST['erase']);
    $r2 = queryMysql("DELETE FROM messages WHERE id=$erase AND recip='$user'");
    if($r2)
      echo "<span>Message deleted successfully</span>";
    else
      echo "<span>Couldn't delete the message. Please, try again.</span>";
  }

  showProfile($user);
  echo "<span>
    <form method='post' action='home.php'>
      What's on your mind? <input type='submit' value='Post'><br>
      <textarea name='text' cols='40' rows='3' style='resize:none'></textarea><br>
      <input type='hidden' name='pm' value='0' checked='checked'>
      </form><br>
  </span>";
  
  $result = queryMysql("SELECT messages.* FROM messages INNER JOIN friends ON messages.recip=friends.user WHERE friends.friend='$user' AND messages.pm=0 ORDER BY messages.id desc");
  $result2 = queryMysql("SELECT messages.* FROM messages WHERE messages.pm=0 and (messages.recip='$user' OR messages.auth='$user') ORDER BY messages.id desc");
  $num    = $result->num_rows;
  $rows = array();
  for ($j = 0 ; $j < $num ; ++$j)
  {
    $rows[] = $result->fetch_array(MYSQLI_ASSOC);
  }
  $result = $rows;
  $num    = $result2->num_rows;
  $rows2 = array();
    for ($j = 0 ; $j < $num ; ++$j)
    {
      $rows2[] = $result2->fetch_array(MYSQLI_ASSOC);
    }
    $result2 = $rows2;
  $results = array_merge($result,$result2);

  function cmp($a,$b){
    // print_r($a);
    // echo $a['id'].' '.$b['id'].'</br>';
    if ($a['id']>$b['id'])
      return 0;
    else
      return 1;
  }
  // print_r($results);
  // echo "</br>";
  usort($results, "cmp");
  // print_r($results);
  // echo "</br>";
  $results = array_map("unserialize", array_unique(array_map("serialize", $results)));
  
  foreach ($results as $row)
  {
    //print_r($row);
    echo date('M jS \'y g:ia:', $row['time']);
    echo " <a href='members.php?view=" . $row['auth'] . "'>" . $row['auth']. "</a> ";
    echo "wrote on ";
    echo " <a href='members.php?view=" . $row['recip'] . "'>" . $row['recip']. "</a> ";
    echo "'s wall: &quot;" . $row['message'] . "&quot;";
    if ($row['recip'] == $user){
      $id = $row["id"];
      echo "<button class='deleteMessage' data='$id'>delete</button>";
      echo "<form id='message$id' method='post' action='home.php'>
      <input type='hidden' name='erase' value='{$id}'>
      </form>";
    }
    echo "</br>";
  }
?>
  </div>
  <script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
  <script type="text/javascript">
    $('.deleteMessage').click(function  () {
      $('#message'+$(this).attr('data')).submit();
    })
  </script>
  </body>
</html>

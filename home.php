<?php // home.php, similar to members.
  require_once 'header.php';

  if (!$loggedin) {
    header("Location: index.php");
    die();
  }

  echo "<div class='main'>";

    showProfile($user);
    //echo "<a class='button' href='messages.php?view=$view'>" .
      //   "Write $view a message</a><br><br>";
    /*$messages = getMessages($view,null,0);
    if ($messages->num_rows) {
      $num = $messages->num_rows;
      for ($j = 0 ; $j < $num ; ++$j)
      {
        $row = $messages->fetch_array(MYSQLI_ASSOC);
        echo date('M jS \'y g:ia:', $row['time']);
        echo " <a href='members.php?view=" . $row['auth'] . "'>" . $row['auth']. "</a> ";
        echo "wrote: &quot;" . $row['message'] . "&quot; </br>";
      }
    }
  
  $result = queryMysql("SELECT messages.* FROM messages INNER JOIN friends ON messages.recip=friends.user WHERE friends.friend='$user' AND messages.pm=0 UNION SELECT messages.* FROM messages WHERE messages.pm=0 and (messages.recip='$user' OR messages.auth='$user') ORDER BY messages.id desc ");
  //print_r($result);
  $num    = $result->num_rows;
  for ($j = 0 ; $j < $num ; ++$j)
  {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    echo date('M jS \'y g:ia:', $row['time']);
    echo " <a href='members.php?view=" . $row['auth'] . "'>" . $row['auth']. "</a> ";
    echo "wrote on ";
    echo " <a href='members.php?view=" . $row['recip'] . "'>" . $row['recip']. "</a> ";
    echo "'s wall: &quot;" . $row['message'] . "&quot; </br>";      
  }*/
?>
  </div>
  </body>
</html>

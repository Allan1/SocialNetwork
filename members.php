<?php // Example 26-9: members.php
  require_once 'header.php';

  if (!$loggedin) {
    header("Location: http://".$_SERVER['SERVER_NAME']."/social-network/index.php");
    die();
  }

  echo "<div class='main'>";

  if (isset($_GET['view']))
  {
    $view = sanitizeString($_GET['view']);
    
    if ($view == $user) $name = "Your";
    else                $name = "$view's";
    
    echo "<h3>$name Profile</h3>";
    showProfile($view);
    echo "<a class='button' href='messages.php?view=$view'>" .
         "Write $view a message</a><br><br>";
    $messages = getMessages($view,null,0);
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
    die("</div></body></html>");
  }

  if (isset($_GET['add']))
  {
    $add = sanitizeString($_GET['add']);

    $result = queryMysql("SELECT * FROM friends WHERE user='$add' AND friend='$user'");
    if (!$result->num_rows)
      queryMysql("INSERT INTO friends VALUES ('$add', '$user')");
  }
  elseif (isset($_GET['remove']))
  {
    $remove = sanitizeString($_GET['remove']);
    queryMysql("DELETE FROM friends WHERE user='$remove' AND friend='$user'");
  }

  $result = queryMysql("SELECT user FROM members ORDER BY user");
  $num    = $result->num_rows;

  echo "<h3>Other Members</h3><ul>";

  for ($j = 0 ; $j < $num ; ++$j)
  {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    if ($row['user'] == $user) continue;
    
    echo "<li><a href='members.php?view=" .
      $row['user'] . "'>" . $row['user'] . "</a>";
    $follow = "follow";

    $result1 = queryMysql("SELECT * FROM friends WHERE
      user='" . $row['user'] . "' AND friend='$user'");
    $t1      = $result1->num_rows;
    $result1 = queryMysql("SELECT * FROM friends WHERE
      user='$user' AND friend='" . $row['user'] . "'");
    $t2      = $result1->num_rows;

    if (($t1 + $t2) > 1) echo " &harr; is a mutual friend";
    elseif ($t1)         echo " &larr; you are following";
    elseif ($t2)       { echo " &rarr; is following you";
      $follow = "recip"; }
    
    if (!$t1) echo " [<a href='members.php?add="   .$row['user'] . "'>$follow</a>]</li>";
    else      echo " [<a href='members.php?remove=".$row['user'] . "'>drop</a>]</li>";
  }
?>
  </ul>
  </div>
  </body>
</html>

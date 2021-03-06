<?php // Example 26-9: members.php
  require_once 'header.php';

  if (!$loggedin) {
    header("Location: index.php");
    die();
  }

  if (isset($_POST['text']) && strlen($_POST['text'])>0) {
    $time = time();
    $pm   = substr(sanitizeString($_POST['pm']),0,1);
    $text = sanitizeString($_POST['text']);
    $recip = $_POST['recip'];
    queryMysql("INSERT INTO messages VALUES(NULL, '$user','$recip', '$pm', $time, '$text')");
  }

  echo "<div class='main'>";
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


  if (isset($_GET['view']))
  {
    $view = sanitizeString($_GET['view']);
    
    if ($view == $user) $name = "Your";
    else                $name = "$view's";
    
    echo "<h3>$name Profile</h3>";
    //////////////////////////////////////////////////
    $follow = "follow";

    $result1 = queryMysql("SELECT * FROM friends WHERE
      user='" . $view . "' AND friend='$user'");
    $t1      = $result1->num_rows;
    $result1 = queryMysql("SELECT * FROM friends WHERE
      user='$user' AND friend='" . $view . "'");
    $t2      = $result1->num_rows;

    if (($t1 + $t2) > 1) echo " &harr; is a mutual friend";
    elseif ($t1)         echo " &larr; you are following";
    elseif ($t2)       { echo " &rarr; is following you";
      $follow = "recip"; }
    
    if (!$t1) echo " [<a href='members.php?add="   .$view . "&view=".$view."'>$follow</a>]</li>";
    else      echo " [<a href='members.php?remove=".$view . "&view=".$view."'>drop</a>]</li>";
    ///////////////////////////////////////////////////
    showProfile($view);
    echo "<span>
    <form method='post' action='members.php?view=$view'>
      Post something on $view's wall <input type='submit' value='Post'><br>
      <textarea name='text' cols='40' rows='3' style='resize:none'></textarea><br>
      <input type='hidden' name='pm' value='0' checked='checked'>
      <input type='hidden' name='recip' value='$view'>
      </form><br>
  </span>";
    echo "</br></br><a class='button' href='messages.php?view=$view'>" .
         "Write $view a private message</a><br><br>";
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

  
  $result = queryMysql("SELECT user FROM members ORDER BY user");
  $num    = $result->num_rows;
  /* SEARCH BOX */  

  echo " <div id='search-box' align='center'>
         <form action='search.php' method='post' name='search' id='search'> 
          Search: <input type='text' name='search' placeholder='search..'/> 
          <input type='submit' value='Submit' /> 
          </form></div>";

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

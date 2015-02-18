  <?php // Example 26-9: members.php
  require_once 'header.php';

  if (!$loggedin) {
    header("Location: index.php");
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
  echo "<h3 align='center'>Search Results</h3>";

  if (isset($_POST['search'])) {
    if (strpos($_POST['search'],'@') !== false) {
      $query = sanitizeString($_POST['search']);
      $result= queryMysql("SELECT email FROM members WHERE email LIKE '$query'");
      $num   = $result->num_rows;
      for ($j = 0 ; $j < $num ; ++$j){ 
        $row = $result->fetch_array(MYSQLI_ASSOC);
        if ($row['user'] == $user) continue;
        $u = $row['user'];
        echo "<a href='members.php?view=".$u."'>".$u."</a>";
        }
      } else {
        $query = sanitizeString($_POST['search']);
        $result= queryMysql("SELECT user FROM members WHERE user LIKE '%$query%'");
        $num   = $result->num_rows;
        echo "<ul>";
        for ($j = 0 ; $j < $num ; ++$j){ 
          $row = $result->fetch_array(MYSQLI_ASSOC);
          if ($row['user'] == $user) continue;
          /**/
          $u = $row['user'];
          $pic_path = PROFILE_PICS_PATH.$u.'.jpg';
          if (file_exists($pic_path))
            echo "<li><img src='$pic_path' style='width:40px;height:40px'><a href='members.php?view=".$u."'>".$u."</a></li>";
          //echo "<a href='members.php?view=".$u."'>".$u."</a></li>"; 
        }
        echo "";

      }

    } 

    

    ?>
  </div>
  </body>
  </html>
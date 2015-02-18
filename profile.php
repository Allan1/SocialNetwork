<?php // Example 26-8: profile.php
  require_once 'header.php';

  if (!$loggedin) die();

  echo "<div class='main'><h3>Your Profile</h3>";

  $result = queryMysql("SELECT * FROM profiles WHERE user='$user'");
  $result2 = queryMysql("SELECT * FROM members WHERE user='$user'");
  

  
  if (isset($_POST['text']))
  {

    $text = sanitizeString($_POST['text']);
    $first_name = sanitizeString($_POST['first_name']);
    $last_name = sanitizeString($_POST['last_name']);
    $birth = $_POST['birth'];
    $email = $_POST['email'];
    $city  = sanitizeString($_POST['city']);    
    $text = preg_replace('/\s\s+/', ' ', $text);

    if ($result->num_rows) {
         queryMysql("UPDATE profiles SET text='$text' where user='$user'");
         queryMysql("UPDATE members SET first_name='$first_name', last_name= '$last_name', birth = '$birth', email='$email',city='$city'  where user='$user'");
        };
  }
  else
  {
    if ($result->num_rows)
    {
      $row  = $result->fetch_array(MYSQLI_ASSOC);
      $row2 = $result2->fetch_array(MYSQLI_ASSOC);
      $first_name = stripslashes($row2['first_name']);
      $last_name =  stripslashes($row2['last_name']);
      $birth = $row2['birth'];
      $email = stripslashes($row2['email']);
      $city = stripslashes($row2['city']);
      $text = stripslashes($row['text']);
    }
    else {
      $text = "";
    }
  }

  $text = stripslashes(preg_replace('/\s\s+/', ' ', $text));

  if (isset($_FILES['image']['name']))
  {
    $saveto = PROFILE_PICS_PATH."$user.jpg";
    move_uploaded_file($_FILES['image']['tmp_name'], $saveto);
    $typeok = TRUE;

    switch($_FILES['image']['type'])
    {
      case "image/gif":   $src = imagecreatefromgif($saveto); break;
      case "image/jpeg":  // Both regular and progressive jpegs
      case "image/pjpeg": $src = imagecreatefromjpeg($saveto); break;
      case "image/png":   $src = imagecreatefrompng($saveto); break;
      default:            $typeok = FALSE; break;
    }

    if ($typeok)
    {
      list($w, $h) = getimagesize($saveto);

      $max = 100;
      $tw  = $w;
      $th  = $h;

      if ($w > $h && $max < $w)
      {
        $th = $max / $w * $h;
        $tw = $max;
      }
      elseif ($h > $w && $max < $h)
      {
        $tw = $max / $h * $w;
        $th = $max;
      }
      elseif ($max < $w)
      {
        $tw = $th = $max;
      }

      $tmp = imagecreatetruecolor($tw, $th);
      imagecopyresampled($tmp, $src, 0, 0, 0, 0, $tw, $th, $w, $h);
      imageconvolution($tmp, array(array(-1, -1, -1),
        array(-1, 16, -1), array(-1, -1, -1)), 8, 0);
      imagejpeg($tmp, $saveto);
      imagedestroy($tmp);
      imagedestroy($src);
    }
  }

  showProfile($user);

  echo <<<_END
    <form method='post' action='profile.php' enctype='multipart/form-data'>
    <h3>Enter or edit your details and/or upload an image</h3>
    <textarea name='text' cols='50' rows='3'>$text</textarea><br>
    First name: <input type='text' name='first_name' value=$first_name /><br>
    Last name: <input type='text' name='last_name' value=$last_name /><br>
    Birth: <input type='text' name='birth' value=$birth /><br>
    Email:  <input type='text' name='email' value=$email  /><br>
    City: <input type='text' name='city' value=$city /><br>
    Image: <input type='file' name='image' size='14'>
    <input type='submit' value='Save Profile'>
    </form>
    </div><br>
_END;
?>
  </body>
</html>

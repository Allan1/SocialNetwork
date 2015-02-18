<?php // Example 26-11: messages.php
  require_once 'header.php';

  date_default_timezone_set('GMT');

  if (!$loggedin) die();

  if (isset($_GET['view'])) $view = sanitizeString($_GET['view']);
  else                      $view = $user;

  if (isset($_POST['text']))
  {
    $text = sanitizeString($_POST['text']);

    if ($text != "")
    {
      $pm   = substr(sanitizeString($_POST['pm']),0,1);
      $time = time();
      $recip = $view;
      if (isset($_POST['recip']) and strlen($_POST['recip'])>0) {
        $recip = $_POST['recip'];
      }
      queryMysql("INSERT INTO messages VALUES(NULL, '$user',
        '$recip', '$pm', $time, '$text')");
    }
  }
  $recip = "";
  if($view == $user){
    // List recent friends that user had conversation with, most recent conversation open.
    echo "<div class='main'></br>";
    //echo $user;
    $result = queryMysql("SELECT members.* FROM messages INNER JOIN members ON ((messages.auth = members.user and messages.recip = '{$user}') OR (messages.recip = members.user and messages.auth = '{$user}')) WHERE messages.pm=1 and members.user<>'{$user}' GROUP BY members.user ORDER BY messages.id desc");
    $num    = $result->num_rows;
    $rows = array();
    for ($j = 0 ; $j < $num ; ++$j)
    {
      $rows[] = $result->fetch_array(MYSQLI_ASSOC);
    }
    $result = $rows;
    //echo $user;
    $allFriends = getAllFriends($user);
    $result = array_unique(array_merge($result,$allFriends), SORT_REGULAR);
    //print_r($result);
    
    echo "<ul id='chatFriends'>";
    foreach ($result as $value) {
      echo "<li>";
      echo "<a href='#' data='".$value['user']."'>".$value['first_name']." ".$value['last_name']."</a>";
      echo "</li>";
    }
    echo "</ul>";
    echo "<div id='currentMessage'>";
    if (count($result)>0) {
      $currentFriend = $result[0]['user'];
      $currentFriendName = $result[0]['first_name'].' '.$result[0]['last_name'];
      $conversation = getConversation($user,$currentFriend);
      echo "<div id='currentFriend'>Conversation with $currentFriendName (<a href='members.php?view=" . $currentFriend . "'>" . $currentFriend. "</a>)</div>";
      echo "<div id='conversation'>";
      foreach ($conversation as $value) {
        echo date('d/m/Y H:i:s', $value['time']);
        echo " " . $value['auth'];
        echo ": <span class='whisper'>&quot;".$value['message']. "&quot;</span> ";
        echo "</br>";
      }
      echo "</div>";
      $recip = $currentFriend;
      echo "<form id='formConversation' method='post' action='messages.php'>
      Type here to leave a message:<br>
      <textarea name='text' cols='40' rows='3'></textarea><br>
      <input id='FormRecip' type='hidden' name='recip' value='{$recip}'>
      <input type='hidden' name='auth' value='{$user}'>
      <input type='hidden' name='pm' value='1'>
      <input type='submit' value='Post Message'></form><br>";
    }
    else{
      echo "No messages yet. Choose somebody from your friends and start a conversation!";
    }
    echo "</div>";
    echo "<div style='clear:both'></div>";
  }
  else if ($view != "")
  {
    if ($view == $user) $name1 = $name2 = "Your";
    else
    {
      $name1 = "<a href='members.php?view=$view'>$view</a>'s";
      $name2 = "$view's";
    }

    echo "<div class='main'><h3>$name1 Messages</h3>";
    showProfile($view);
    
    echo <<<_END
      <form method='post' action='messages.php?view=$view'>
      Type here to leave a message:<br>
      <textarea name='text' cols='40' rows='3'></textarea><br>
      <input type='hidden' name='pm' value='1'>
      <input type='submit' value='Post Message'></form><br>
_END;

    if (isset($_GET['erase']))
    {
      $erase = sanitizeString($_GET['erase']);
      queryMysql("DELETE FROM messages WHERE id=$erase AND recip='$user'");
    }
    
    $query  = "SELECT * FROM messages WHERE recip='$view' ORDER BY time DESC";
    $result = queryMysql($query);
    $num    = $result->num_rows;
    
    for ($j = 0 ; $j < $num ; ++$j)
    {
      $row = $result->fetch_array(MYSQLI_ASSOC);

      if ($row['pm'] == 0 || $row['auth'] == $user || $row['recip'] == $user)
      {
        echo date('M jS \'y g:ia:', $row['time']);
        echo " <a href='messages.php?view=" . $row['auth'] . "'>" . $row['auth']. "</a> ";

        if ($row['pm'] == 0)
          echo "wrote: &quot;" . $row['message'] . "&quot; ";
        else
          echo "whispered: <span class='whisper'>&quot;" .
            $row['message']. "&quot;</span> ";

        if ($row['recip'] == $user)
          echo "[<a href='messages.php?view=$view" .
               "&erase=" . $row['id'] . "'>erase</a>]";

        echo "<br>";
      }
    }
  }

  // if (!$num) echo "<br><span class='info'>No messages yet</span><br><br>";
  echo "</br>";
  echo "<br><a class='button' href='messages.php?view=$view'>Refresh messages</a></div><br>";
?>
<script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
<script type="text/javascript">
  var user = "<?php echo($user); ?>";
  $('#chatFriends li:first').addClass('selected')
  $('#chatFriends a').click(function () {
    $('#chatFriends li').removeClass('selected')
    $(this).parent("li").addClass('selected')
    var friend = $(this).attr('data');
    console.log(user+' '+friend);
    $.ajax({
      type: "POST",
      url: "getConversation.php",
      data: { user: user, friend: friend }
    })
    .done(function( msg ) {
      var result = JSON.parse(msg);
      if (result['success']) {
        console.log(result)
        $('#currentFriend').html('Conversation with '+result['friend'][0]['first_name']+' '+result['friend'][0]['last_name']+" (<a href='members.php?view=" +result['friend'][0]['user']+ "'>"+result['friend'][0]['user']+ '</a>)');
        $('#FormRecip').val(result['friend'][0]['user']);
        $('#conversation').html('');
        for(i=0;i<result['conversation'].length;i++){
          var content = (new Date(result['conversation'][i]['time']*1000)).toLocaleString();
          content+=' '+result['conversation'][i]['auth']+': <span class="whisper">"'+result['conversation'][i]['message']+'"</span></br>';
          $('#conversation').append(content);
        }
      }
      else{

      }
      
    });
  });

  $('#formConversation').submit(function (event) {
    event.preventDefault();
    // console.log($('#formConversation textarea[name="text"]').val())
    var time = "<?php echo(time()); ?>";
    if($('#formConversation textarea[name="text"]').val()=="")
      return;
    $.ajax({
      type: "POST",
      url: "postMessage.php",
      data: { 
        auth: $('#formConversation input[name="auth"]').val(),
        recip: $('#formConversation input[name="recip"]').val(), 
        text: $('#formConversation textarea[name="text"]').val(),
        time: time,
        pm: $('#formConversation input[name="pm"]').val()
      }
    })
    .done(function( msg ) {
      console.log(msg);
      var result = JSON.parse(msg);
      if (result['success']) {
        // console.log(result)
        $('#conversation').prepend(result['fullMessage']);
        $('#formConversation textarea[name="text"]').val("")
      }
      else{
        alert('Couldn\'t post the message. Please, try again later.')
      }
      
    });
  });
</script>
  </body>
</html>

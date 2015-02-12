<style type="text/css">
  input.valid+span::before{
    content:"✔";
    color:#008000;
  }
  input.invalid+span::before{
    content: "✘";
    color: #F00;
  }
  form input{
    width: 220px;
  }
</style>
<?php // Example 26-5: index.php
require_once 'header.php';


  $error = $user = $pass = "";
  if ($loggedin) {
    header('Location: home.php');
    die();
  }

  if (isset($_POST['user']))
  {
    $user = sanitizeString($_POST['user']);
    $pass = sanitizeString($_POST['pass']);
    $passConf = sanitizeString($_POST['passConf']);

    if ($user == "" || $pass == "")
      $error = "Not all fields were entered.<br><br>";
    else if($pass != $passConf){
      $error = "Password and password confimation don't match.<br><br>";
    }
    else
    {
      $result = queryMysql("SELECT * FROM members WHERE user='$user'");

      if ($result->num_rows)
        $error = "That username already exists<br><br>";
      else
      {
        queryMysql("INSERT INTO members VALUES('$user', '$pass')");
        die("<h4>Account created</h4>Please Log in.<br><br>");
      }
    }
  }

  echo <<<_END
  <div class='main'><h3>Not an user yet? Please enter your details to sign up.</h3>
  <form method='post' action='index.php'>$error
    <span class='fieldname'>Username</span>
    <input id='formUser' type='text' maxlength='16' name='user' value='$user' placeholder="4 to 16 characters"
    ><span id='info'></span><br>
    <span class='fieldname'>Password</span>
    <input id='formPass' type='password' maxlength='16' name='pass' placeholder="4 to 8 characters, at least one number"
    value='$pass'>
    <span id='infoPass'></span><br>
    <span class='fieldname'>Confirm Password</span>
    <input id='formPassConf' type='password' maxlength='16' name='passConf'
    value='$pass'>
    <span id='infoPassConf'></span><br>
    <span class='fieldname'>&nbsp;</span>
    <input id='formSubmit' type='submit' value='Sign up' disabled>
  </form>
</div><br>
_END;
?>
<script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
<script>
  function checkUser(user)
  {
    if (user.value == '')
    {
      O('info').innerHTML = ''
      return
    }
    console.log(user);
    params  = "user=" + user.value
    request = new ajaxRequest()
    request.open("POST", "checkuser.php", true)
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
    request.setRequestHeader("Content-length", params.length)
    request.setRequestHeader("Connection", "close")

    request.onreadystatechange = function()
    {
      if (this.readyState == 4)
        if (this.status == 200)
          if (this.responseText != null){
            console.log(this.responseText);
            if (this.responseText == 'taken') {
              O('info').innerHTML = "<span class='taken'>&nbsp;&#x2718; This username is taken</span>";
              $('#formUser').removeClass('valid');
              validateFormActivateSubmit();
            }
            else if(this.responseText == 'available'){
              O('info').innerHTML = "";
              $('#formUser').addClass('valid');
              $('#formUser').removeClass('invalid');
              validateFormActivateSubmit();
            }
          }
    }
    request.send(params)
  }

  function ajaxRequest()
  {
    try { var request = new XMLHttpRequest() }
    catch(e1) {
      try { request = new ActiveXObject("Msxml2.XMLHTTP") }
      catch(e2) {
        try { request = new ActiveXObject("Microsoft.XMLHTTP") }
        catch(e3) {
          request = false
        } 
      } 
    }
    return request
  }

  function validateFormActivateSubmit () {
    if ($('#formUser').hasClass('valid') && $('#formPass').hasClass('valid') && $('#formPassConf').hasClass('valid')) {
      $('#formSubmit').removeAttr('disabled');
    }
    else
      $('#formSubmit').attr('disabled','disabled');
  }

  $(function () {
    $('#formUser').bind('keyup blur',function(){
      console.log(this.value);
      if (this.value.match(/^[a-z0-9]{4,16}$/)) {
        checkUser(this);
      }
      else{
        $(this).removeClass('valid');
        $(this).addClass('invalid');
      }
    });
    $('#formPass').bind('keyup blur',function(){
      if (this.value.match(/^(?=.*\d).{4,8}$/)) {
        $(this).addClass('valid');
      }
      else
        $(this).removeClass('valid');
      validateFormActivateSubmit();
    });
    $('#formPassConf').bind('keyup blur',function(){
      if ($('#formPass').val() != this.value) {
        $(this).removeClass('valid');
      }
      else
        $(this).addClass('valid');
      validateFormActivateSubmit();
    });
  })
</script>
</body>
</html>

<?php
	include "functions.php";

	if (isset($_POST['text']))
	{
		$text = sanitizeString($_POST['text']);

		if ($text != "")
		{
		  $pm   = substr(sanitizeString($_POST['pm']),0,1);
		  $time = time();
		  $recip = $_POST['recip'];
		  $auth = $_POST['auth'];
		  
		  if(queryMysql("INSERT INTO messages VALUES(NULL, '$auth','$recip', '$pm', $time, '$text')")){
		  	$str = date('d/m/Y H:i:s', $time)." " . $auth.": <span class='whisper'>&quot;".$text. "&quot;</span></br>";
		  	echo json_encode(array('success'=>1,'fullMessage'=>$str));
		  }
			else{
				echo json_encode(array('success'=>0));
			}
		}
	}
?>
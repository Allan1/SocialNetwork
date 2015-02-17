<?php
	include "functions.php";

	if (isset($_POST['user']) && isset($_POST['friend'])) {
		echo json_encode(array('success'=>1,'conversation'=>getConversation($_POST['user'],$_POST['friend']),'friend'=>getUser($_POST['friend'])));
	}
	else
		echo json_encode(array('success'=>0));
?>
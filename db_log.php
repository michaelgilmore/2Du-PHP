<?php
include "config.php";
include "session.php";

function db_log($msg, $level, $page) {
	$user_id = $_SESSION['login_user_id'];
	
	$sql = "insert into tudu_log (msg, level, page, user_id) ".
	   "values ( '$msg', '$level', '$page', $user_id)";

	$result = mysqli_query($db, $sql);
	if (!$result) {
		$server_log = fopen('server.log', 'a');
		fwrite($server_log, mysqli_error($db));
		fclose($server_log);
	}
}
?>

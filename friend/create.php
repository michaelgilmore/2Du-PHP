<?php
header('Access-Control-Allow-Methods: POST');

include "session.php";
include "config.php";
include "friend.php";


$friend_param = $_POST['friend_username']];

$friend_username = mysqli_real_escape_string($db, $friend_param);

$friend = new Friend();
$friend->friend_username = $friend_username;

$sql = $friend->getInsertSQL();
server_log('creating new friend:'.$sql);
$result = mysqli_query($db, $sql);

if (!$result) {
	server_log("SQL query ($sql) failed ($result) returning 404");
	server_log(mysqli_error($db));
	fclose($server_log);
	http_response_code(404);
    die('api call failed');
	die(mysqli_error($db));
}

?>

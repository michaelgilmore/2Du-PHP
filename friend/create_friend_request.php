<?php
session_start();

header('Access-Control-Allow-Methods: POST');

//include "session.php";
include "../config.php";
include "friend_request.php";


$friend_param = $_POST['friend_username'];

//server_log('creating new friend request, param:'.$friend_param);
echo 'friend_param: ' . $friend_param . '<br>';

if (!isset($db)) {
	echo "No db set<br>";
}

if ($db->connect_error) {
   echo "Connection failed: " . $db->connect_error;
}

$friend_username = mysqli_real_escape_string($db, $friend_param);
echo 'friend_username: ' . $friend_username . '<br>';

$friendRequest = new FriendRequest();
$friendRequest->friend_username = $friend_username;

$sql = $friendRequest->getInsertSQL();
echo $sql;
//server_log('creating new friend request, sql:'.$sql);
$result = mysqli_query($db, $sql);


//if (!$result) {
//	server_log("SQL query ($sql) failed ($result) returning 404");
//	server_log(mysqli_error($db));
//	fclose($server_log);
//	http_response_code(404);
 //   die('api call failed');
//	die(mysqli_error($db));
//}

?>

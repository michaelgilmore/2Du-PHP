<?php
session_start();

header('Access-Control-Allow-Methods: POST');

//include "session.php";
include "../config.php";
include "friend_request.php";


$accepter_id = $_POST['accepter_id'];
$new_friend_id = $_POST['new_friend_id'];
$accept = $_POST['accept'];

if (strlen(trim($accepter_id)) > 0 && strlen(trim($new_friend_id)) > 0 && strlen(trim($accept)) > 0) {

	$friendRequest = new FriendRequest();
	$friendRequest->new_friend_id = $new_friend_id;

	mysqli_begin_transaction($db);
	
	if ($accept == 1) {
		$sql = $friendRequest->getAcceptForwardSQL($accept);
		$result = mysqli_query($db, $sql);

		$sql = $friendRequest->getAcceptReverseSQL($accept);
		$result = mysqli_query($db, $sql);
	}

	$sql = $friendRequest->getDeleteRequestSQL($accept);
	$result = mysqli_query($db, $sql);
	
	mysqli_commit($db);
}
?>

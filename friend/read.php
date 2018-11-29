<?php
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/json');

include "session.php";
include "config.php";
include "friend.php";

$friend = new Friend();

$result = FALSE;
$return_array = array();
$num_friends = 0;

$sql = $friend->readAllQuery();

$result = mysqli_query($db, $sql);

if($result) {
    $num_friends = mysqli_num_rows($result);
}

if($num_friends > 0) {
    
    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        array_push($return_array, $row);
    }
    
    echo json_encode($return_array);
}
else {
    echo json_encode(array('message' => 'No friends'));
}

?>

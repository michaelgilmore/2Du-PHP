<?php
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/json');

include "../config.php";
include "list.php";

$list = new TodoList();

$result = FALSE;
$return_array = array();
$num_lists = 0;

$sql = $list->readAllQuery();
array_push($return_array, $sql);

$result = mysqli_query($db, $sql);

if($result) {
    $num_lists = mysqli_num_rows($result);
}

if($num_lists > 0) {
    
    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        array_push($return_array, $row);
    }
    
    echo json_encode($return_array);
}
else {
    echo json_encode(array('message' => 'No lists'));
}

?>

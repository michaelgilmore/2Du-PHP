<?php
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/json');

include "session.php";
include "config.php";
include "todo.php";

$todo = new Todo();

$result = FALSE;
$num_todos = 0;
$return_array = array();

//$method = $_SERVER['REQUEST_METHOD'];
//array_push($return_array, 'method='.$method);

if(isset($_GET['id'])) {
    //array_push($return_array, 'id='.$_GET['id']);
    $sql = $todo->readQuery($_GET['id']);
}
elseif(isset($_GET['list_id'])) {
    //array_push($return_array, 'list_id='.$_GET['list_id']);
    $_SESSION['selected_list_id'] = $_GET['list_id'];
    $sql = $todo->readListQuery($_GET['list_id']);
}
else {
    //array_push($return_array, 'all');
    $sql = $todo->readAllActiveQuery();
}

//array_push($return_array, $sql);

$result = mysqli_query($db, $sql);

if($result) {
    $num_todos = mysqli_num_rows($result);
}

if($num_todos > 0) {
    
    //array_push($return_array, $sql);
    
    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        array_push($return_array, $row);
    }
    
    echo json_encode($return_array);
}
else {
    echo json_encode(array('message' => 'No todos'));
}

?>

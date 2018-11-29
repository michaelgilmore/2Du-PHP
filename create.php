<?php
header('Access-Control-Allow-Methods: POST');

include "session.php";
include "config.php";
include "todo.php";

$server_log = fopen('server.log', 'a');
function server_log($str) {
	global $server_log;
	fwrite($server_log, date("Y-m-d H:i:s ")."$str\n");
}

$txt = getPostOrGet('txt');
$cat = getPostOrGet('cat');
$lbl = getPostOrGet('lbl');
$due = getPostOrGet('due');
$det = getPostOrGet('det');
$title = getPostOrGet('title');

$tuduText = mysqli_real_escape_string($db, $txt);
$tuduCategory = mysqli_real_escape_string($db, $cat);
$tuduLabel = mysqli_real_escape_string($db, $lbl);
$tuduDue = mysqli_real_escape_string($db, $due);
$tuduDetails = mysqli_real_escape_string($db, $det);

$tuduDue = checkDateFormat($tuduDue);

$todo = new Todo();
$todo->text = $tuduText;
$todo->category = $tuduCategory;
$todo->label = $tuduLabel;
$todo->due_date = $tuduDue;
$todo->details = $tuduDetails;

$sql = $todo->getInsertSQL();
server_log('creating new todo:'.$sql);
$result = mysqli_query($db, $sql);

if (!$result) {
	server_log("SQL query ($sql) failed ($result) returning 404");
	server_log(mysqli_error($db));
	fclose($server_log);
	http_response_code(404);
    die('api call failed');
	die(mysqli_error($db));
}

function getPostOrGet($key) {
	if(isset($_POST[$key])) {
	  return $_POST[$key];
	}
	elseif(isset($_GET[$key])) {
	  return $_GET[$key];
	}
	else {
	  return '';
	}
}

function checkDateFormat($raw_date) {
	if(!$raw_date) return null;
	return date('Y-m-d H:i:s', strtotime($raw_date));
}

?>

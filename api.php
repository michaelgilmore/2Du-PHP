<?php
include('session.php');
include('config.php');
include('db_log.php');


if(!isset($_SESSION['login_user_id'])){
   header("location:.");
}

$server_log = fopen('server.log', 'a');
function server_log($str) {
	global $server_log;
	fwrite($server_log, date("Y-m-d H:i:s ")."$str\n");
}

$user_id = $_SESSION['login_user_id'];
//echo "found user id: $user_id";

// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
//foreach ($request as $key => $value)
//	echo "request:$key => $value\n";

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

$txt = getPostOrGet('txt');
$cat = getPostOrGet('cat');
$lbl = getPostOrGet('lbl');
$due = getPostOrGet('due');
$don = getPostOrGet('don');
$det = getPostOrGet('det');

$tuduText = mysqli_real_escape_string($db, $txt);
$tuduCategory = mysqli_real_escape_string($db, $cat);
$tuduLabel = mysqli_real_escape_string($db, $lbl);
$tuduDue = mysqli_real_escape_string($db, $due);
$tuduDone = mysqli_real_escape_string($db, $don);
$tuduDetails = mysqli_real_escape_string($db, $det);

$tuduDue = checkDateFormat($tuduDue);
$tuduDone = checkDateFormat($tuduDone);

server_log("txt:$tuduText,cat:$tuduCategory,lbl:$tuduLabel,due:$tuduDue,don:$tuduDone,det:$tuduDetails");

// connect to the mysql database
mysqli_set_charset($db,'utf8');
 
// retrieve the table and key from the path
$table = preg_replace('/[^a-z0-9_]+/i','',array_shift($request));
$key = array_shift($request)+0;
server_log("key:$key");

// create SQL based on HTTP method
switch ($method) {
  case 'GET':
    $sql = "select * from `$table` where user_id = $user_id".($key?" and id=$key":''); 
	//server_log("GET:$sql");
	break;
  case 'PUT':
    echo 'PUT not supported';
	server_log("PUT:not supported");
	break;
	/*
    //ini_set('allow_url_fopen', 'true');
	//echo 'allow_url_fopen:' . ini_get('allow_url_fopen');

	echo "case PUT\n";
	$putfp = fopen('php://input', 'r');
	$putdata = '';
	while($data = fread($putfp, 1024))
		$putdata .= $data;
	fclose($putfp);
	echo "$putdata\n";
	echo "after putdata\n";
	
	parse_str(file_get_contents("php://input"),$post_vars);

	$tuduText = mysqli_real_escape_string($db, $post_vars['txt']);
	$tuduCategory = mysqli_real_escape_string($db, $post_vars['cat']);
	$tuduLabel = mysqli_real_escape_string($db, $post_vars['lbl']);
	$tuduDue = mysqli_real_escape_string($db, $post_vars['due']);
	//echo "php input due:$tuduDue\n";
	//foreach ($post_vars as $key => $value)
	//	echo "request:$key => $value\n";
	if(!$key or (!$tuduText and !$tuduCategory and !$tuduLabel and !$tuduDue)) {
		print 'bad put';
		break;
    }
	$sql = "update `$table` set ";
	if($tuduText) $sql .= "text = '$tuduText'  ";
	if($tuduCategory) $sql .= "category = '$tuduCategory'  ";
	if($tuduLabel) $sql .= "label = '$tuduLabel'  ";
	if($tuduDue) $sql .= "due_date = '$tuduDue'";
	$sql = preg_replace('/  /i',',',trim($sql));
	$sql .= " where id=$key and user_id = $user_id"; 
	echo "\n$sql\n";
	break;
    */
  case 'POST':
    if(!$key) {
		$tuduDueOrNull = "'$tuduDue'" ?: 'NULL';
		//insert
		$sql = "insert into tudus (text,category,due_date,label,details,user_id) ".
		   "values ( '$tuduText', '$tuduCategory', $tuduDueOrNull, '$tuduLabel', '$tuduDetails', $user_id)";
		   
		db_log("Added '$tuduText'", 'info', $_SERVER['PHP_SELF']);
	}
	else {
		//update
		$sql = "update `$table` set";
		if($tuduText) $sql .= " text = '$tuduText' ";
		if($tuduCategory) $sql .= " category = '$tuduCategory' ";
		if($tuduLabel) $sql .= " label = '$tuduLabel' ";
		if($tuduDue) $sql .= " due_date = '$tuduDue' ";
		if($tuduDone) $sql .= " completed_date = '$tuduDone' ";
		if($tuduDetails) $sql .= " details = '$tuduDetails' ";
		$sql = preg_replace('/  /i',',',trim($sql));
		$sql .= "where id=$key and user_id = $user_id"; 
	}
	server_log("POST:$sql");
    break;
}

//echo "$sql\n";
$result = mysqli_query($db, $sql);
if (!$result) {
	server_log("SQL query ($sql) failed ($result) returning 404");
	server_log(mysqli_error($db));
	fclose($server_log);
	http_response_code(404);
	die(mysqli_error($db));
}

/* 
// print results, insert id or affected row count
if ($method == 'GET') {
  if (!$key) echo '[';
  for ($i=0;$i<mysqli_num_rows($result);$i++) {
    echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
  }
  if (!$key) echo ']';
} elseif ($method == 'POST' and !$key) {
  echo "New id: " . mysqli_insert_id($db);
} else {
  echo "Affected rows: " . mysqli_affected_rows($db);
}
*/

mysqli_close($db);
fclose($server_log);

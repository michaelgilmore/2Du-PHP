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
$title = getPostOrGet('title');

$tuduText = mysqli_real_escape_string($db, $txt);
$tuduCategory = mysqli_real_escape_string($db, $cat);
$tuduLabel = mysqli_real_escape_string($db, $lbl);
$tuduDue = mysqli_real_escape_string($db, $due);
$tuduDone = mysqli_real_escape_string($db, $don);
$tuduDetails = mysqli_real_escape_string($db, $det);
$new_list_title = mysqli_real_escape_string($db, $title);

$tuduDue = checkDateFormat($tuduDue);
$tuduDone = checkDateFormat($tuduDone);

server_log("txt:$tuduText,cat:$tuduCategory,lbl:$tuduLabel,due:$tuduDue,don:$tuduDone,det:$tuduDetails");

// connect to the mysql database
mysqli_set_charset($db,'utf8');
 
// retrieve the table and key from the path
$table = preg_replace('/[^a-z0-9_]+/i','',array_shift($request));
$key = array_shift($request)+0;
$list_id = array_shift($request)+2;
server_log("table:$table,key:$key,list_id:$list_id");

$sql = 'init';

// create SQL based on HTTP method
switch ($method) {
  case 'GET':
    if($table == 'tudus') {
        if($list_id > 0) {
            $sql = "select t.* from tudus t, tudu_lists l, tudu_list_access la where t.list_id = l.id and l.id = la.list_id and la.user_id = $user_id and t.list_id = $list_id and completed_date is null limit 0, 200";
        }
        else {
            $sql = "select * from tudus where user_id = $user_id".($key?" and id=$key":'').' and completed_date is null limit 0, 200';
        }
        //server_log("GET:$sql");
        $result = mysqli_query($db, $sql);
    }
    elseif($table == 'tudu_lists') {
        $sql = "select t.* from tudus t, tudu_lists l, tudu_list_access la where t.list_id = l.id and l.id = la.list_id and la.user_id = $user_id".($key?" and l.id=$key":'').' limit 0, 200';
        $result = mysqli_query($db, $sql);
    }
    if($result) {
        echo '[';
        for ($i=0;$i<mysqli_num_rows($result);$i++) {
            echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
        }
        echo ']';
    }
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
        if($table == 'tudus') {
            $tuduDueOrNull = "'$tuduDue'" ?: 'NULL';
            $sql = "insert into tudus (text,category,due_date,label,details,user_id) ".
               "values ( '$tuduText', '$tuduCategory', $tuduDueOrNull, '$tuduLabel', '$tuduDetails', $user_id)";
               
            server_log("POST:$sql");
            //echo "$sql\n";
            $result = mysqli_query($db, $sql);

            db_log("Added '$tuduText'", 'info', $_SERVER['PHP_SELF']);
        }
        elseif($table == 'tudu_lists') {
            $sql = "insert into tudu_lists (title) ".
               "values ( '$new_list_title')";
            $result = mysqli_query($db, $sql);
            $new_list_id = mysqli_insert_id($db);

            $sql = "insert into tudu_list_access (user_id, list_id, access_level) ".
               "values ( $user_id, $new_list_id, 'read_write')";
            $result = mysqli_query($db, $sql);
               
            db_log("Added list '$new_list_title'", 'info', $_SERVER['PHP_SELF']);
        }
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
        server_log("POST/UPDATE:$sql");
        //echo "$sql\n";
        $result = mysqli_query($db, $sql);
        
        if('' != $due) {
            // Record all due date changes in a separate file for determining how many times
            // it has been moved
            $sqlTrackDueDateChange = "insert into tudu_moved (tudu_id, new_due_date) ".
               "values ($key, '$tuduDue')";
            $result = mysqli_query($db, $sqlTrackDueDateChange);
            server_log("SQL query ($sqlTrackDueDateChange) failed ($result)");
            server_log(mysqli_error($db));
        }
	}
    break;
}

if (!$result) {
	server_log("SQL query ($sql) failed ($result) returning 404");
	server_log(mysqli_error($db));
	fclose($server_log);
	http_response_code(404);
    die('api call failed');
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

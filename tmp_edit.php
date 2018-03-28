<?php
	include "session.php";
	include "db_log.php";

	$user_id = $_SESSION['login_user_id'];
	$user_tier = $_SESSION['login_user_tier'];


/*
	//If an edit was submitted save it
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$id = $_POST["id"];
		$text = $_POST["text"];
		$category = $_POST["category"];
		$due_date = $_POST["due_date"];
		$label = $_POST["label"];
		$completed_date = $_POST["completed_date"];
	
		$url = 'http://gilmore.cc/todo/api.php/tudus/'.$id;
		$data = array('txt' => $text, 'cat' => $category, 'due' => $due_date, 'lbl' => $label, 'don' => $completed_date);

		// use key 'http' even if you send the request to https://...
		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data)
			)
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		if ($result === FALSE) { print "file_get_contents() returned false\n$url\n$context"; }

		var_dump($result);
	}
*/  
    $todo_id = '';
	if(isset($_POST['id'])) {
	  $todo_id = $_POST['id'];
	}
	elseif(isset($_GET['id'])) {
	  $todo_id = $_GET['id'];
	}

	$tudu_fields = "id, text, created_date, updated_date, category, due_date, original_due_date, label, completed_date";
	$sql = "SELECT $tudu_fields FROM tudus WHERE id = '$todo_id' and user_id = '$user_id'";
	$result = mysqli_query($db,$sql);

	$num_todos = mysqli_num_rows($result);

	if($num_todos != 1) {
		db_log($msg, 'error', $_SERVER['PHP_SELF']);
	}

	
	function dateToString($in_date) {
		if(!$in_date) return '';
		return date('m/d/Y', strtotime($in_date));
	}
?>

<html>
   
	<head>
		<title>Edit</title>
      
		<link rel="stylesheet" href="gilmore_todo.css">

		<!-- jQuery library -->
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

		<script>
		$(function() {
			$("#due-datepicker").datepicker({
				changeMonth: true,
				changeYear: true
			});
			$("#completed-datepicker").datepicker({
				changeMonth: true,
				changeYear: true
			});
		});

		function updateTodo() {

			var xhr = new XMLHttpRequest();
			xhr.open("POST", 'api.php/tudus/'+<?php echo $todo_id; ?>, true);

			var params = 'txt=' + $('#todo_text').val()
				+ '&cat=' + $('#todo_category').val()
				+ '&lbl=' + $('#todo_label').val();
			
			if($('#todo_due_date').val()) {
				params = params + '&due=' + $('#todo_due_date').val()
			}
			if($('#todo_completed_date').val()) {
				params = params + '&don=' + $('#todo_completed_date').val();
			}
			
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

			xhr.onreadystatechange = function() {
				if(xhr.readyState == 4 && xhr.status == 200) {
					//success
					//alert(xhr.responseText);
					location.reload();
					//document.getElementById(todo_id).parentNode.style.display = 'none';
					//$('#todo-list-action-dialog').modal('hide');
				}
				else {
					//alert(xhr.responseText);
				}
			}
			xhr.send(params);
		}
		</script>
	
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	</head>
   
	<body bgcolor = "#FFFFFF">
	
		<div align = "center">
 
			<div class="container">

			<?php
				include "nav.php";
			  
				$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
				echo "<table cellpadding=5>";
				
				echo "<tr><td><b>text</b></td><td><input name=\"text\" id=\"todo_text\" style=\"width: 40vw\" value=\"".$row['text']."\"/></td></tr>";
				echo "<tr><td><b>created_date</b></td><td>".$row['created_date']."</td></tr>";
				echo "<tr><td><b>updated_date</b></td><td>".$row['updated_date']."</td></tr>";
				echo "<tr><td><b>category</b></td><td><input name=\"category\" id=\"todo_category\" value=\"".$row['category']."\"/></td></tr>";
				
				echo "<tr><td><b>due_date</b></td>
					<td><input name=\"due_date\" id=\"todo_due_date\" type=\"text\" id=\"due-datepicker\" style=\"width: 100px\" value=\"".dateToString($row['due_date'])."\"/>
					</td></tr>";
				
				echo "<tr><td><b>original_due_date</b></td><td>".$row['original_due_date']."</td></tr>";
				echo "<tr><td><b>label</b></td><td><input name=\"label\" id=\"todo_label\" value=\"".$row['label']."\"/></td></tr>";
				
				echo "<tr><td><b>completed_date</b></td><td>
					<input name=\"completed_date\" id=\"todo_completed_date\" type=\"text\" id=\"completed-datepicker\" style=\"width: 100px\" value=\"".dateToString($row['completed_date'])."\"/>
					</td></tr>";
				
				echo "</table>";
				echo "<input type=\"button\" value=\"Save\" onclick=\"updateTodo();\"/>";
			?>
			
			</div>

		</div>

		<!-- Popper JS -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.6/umd/popper.min.js"></script>

		<!-- Latest compiled JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>
	</body>
</html>

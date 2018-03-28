<?php
  include "session.php";

  $user_id = $_SESSION['login_user_id'];
  $sql = "SELECT id, text, completed_date FROM tudus WHERE user_id = '$user_id' and completed_date is not null ORDER BY completed_date desc LIMIT 0, 1000";
  $result = mysqli_query($db,$sql);
  
  $num_todos = mysqli_num_rows($result);
?>

<html>
   
	<head>
		<title>You</title>
      
		<link rel="stylesheet" href="gilmore_todo.css">

		<!-- jQuery library -->
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	</head>
   
	<body bgcolor = "#FFFFFF">
	
		<div align = "center">
 
			<div class="container">

			<?php
			  include "nav.php";
			?>

			  <div class="table-responsive">
			  <table id="todo_main_table" class="table table-dark table-striped">
				<thead style="background-color: gray">
					<tr>
						<th>todo</th>
						<th onclick="attributeOverlayOn()">done</th>
					</tr>
				</thead>
				<tbody>
					<?php
					//function copied from home.php, should be moved to a shared js file
					function dueDateToString($due_date) {
						if(!$due_date) return '';
						return date('m/d/Y', strtotime($due_date));
					}

					function todoTouchCompletedTodo() {
						//Show todo details
					}
					
					$todo_num = 0;
					while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
						$todo_num++;
						echo "<tr>";
						echo "<td onclick=\"todoTouchCompletedTodo(this);\">".$row['text']."</td>";
						echo "<td id=\"".$row['id']."\">"
							."<input type='text' id='datepicker".$todo_num."' value='".dueDateToString($row['completed_date'])."' readonly"
							." class='todo_list_due_date'/>"
							."</td>";
						echo "</tr>";
					}
					?>

				</tbody>
			  </table>
			  </div>
			</div>

		</div>

		<!-- Popper JS -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.6/umd/popper.min.js"></script>

		<!-- Latest compiled JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>

	</body>
</html>

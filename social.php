<?php
  include "session.php";

  $user_id = $_SESSION['login_user_id'];
  
  $sql = "SELECT count(id) FROM tudu_users";
  $result = mysqli_query($db,$sql);
  $row = $result->fetch_row();
  $user_count = $row[0];

  $sql = "SELECT count(id) FROM tudus";
  $result = mysqli_query($db,$sql);
  $row = $result->fetch_row();
  $todo_count = $row[0];

  $sql = "SELECT count(id) FROM tudus where completed_date is not null";
  $result = mysqli_query($db,$sql);
  $row = $result->fetch_row();
  $done_count = $row[0];
  
?>

<html>
   
	<head>
		<title>Social</title>
      
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

				<div class="row" style="font-size: 48px; font-weight: 800; margin-top: 50px;">
					<div class="col-sm">
					</div>
					<div class="col-sm">
					<?php echo $user_count; ?> Users
					</div>
					<div class="col-sm">
					<?php echo $todo_count; ?> Todos
					</div>
					<div class="col-sm">
					</div>
					<div class="col-sm" style="color: red">
					<?php echo $done_count; ?> Done
					</div>
					<div class="col-sm">
					</div>
				</div>			

			</div>
			
			<div style="margin-top: 100px;">
				<b>Connect to Friend</b> <form><input placeholder="username" /><input type="submit"/></form>
			</div>

		</div>

		<!-- Popper JS -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.6/umd/popper.min.js"></script>

		<!-- Latest compiled JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>

	</body>
</html>

<?php
  include "session.php";

  $user_id = $_SESSION['login_user_id'];

  $sql = "SELECT email FROM tudu_users WHERE id = '$user_id'";
  $result = mysqli_query($db,$sql);
  $row = mysqli_fetch_array($result);
  $email = $row['email'];

  $sql = "SELECT id, text, completed_date FROM tudus WHERE user_id = '$user_id' and completed_date is not null ORDER BY completed_date desc LIMIT 0, 1000";
  $result = mysqli_query($db,$sql);
  $num_todos = mysqli_num_rows($result);

  if (count($_POST) > 0) {
	  if (isset($_POST['newEmail'])) {
		mysqli_query($db, "UPDATE tudu_users set email='" . $_POST["newEmail"] . "' WHERE id='" . $_SESSION["login_user_id"] . "'");
		$email = $_POST['newEmail'];
		$message = "Email Changed";
	  }
	  if (isset($_POST['currentPassword']) && isset($_POST['newPassword'])) {
		$result = mysqli_query($db, "SELECT p, sha1(".$_POST["currentPassword"].") as currentPassword from tudu_users WHERE id=".$_SESSION["login_user_id"]);
		$row = mysqli_fetch_array($result);
		if ($row["currentPassword"] == $row["p"]) {
			mysqli_query($db, "UPDATE tudu_users set p=sha1('" . $_POST["newPassword"] . "') WHERE id='" . $_SESSION["login_user_id"] . "'");
			$message = "Password Changed";
		} else
			$message = "Current Password is not correct";
	  }
  }
?>

<html>
   
	<head>
		<title>You</title>
      
		<link rel="stylesheet" href="gilmore_todo.css">

		<!-- jQuery library -->
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	
	<script>
		function validatePassword() {
			var currentPassword,newPassword,confirmPassword,output = true;

			currentPassword = document.frmChange.currentPassword;
			newPassword = document.frmChange.newPassword;
			confirmPassword = document.frmChange.confirmPassword;

			if(!currentPassword.value) {
				currentPassword.focus();
				document.getElementById("currentPassword").innerHTML = "required";
				output = false;
			}
			else if(!newPassword.value) {
				newPassword.focus();
				document.getElementById("newPassword").innerHTML = "required";
				output = false;
			}
			else if(!confirmPassword.value) {
				confirmPassword.focus();
				document.getElementById("confirmPassword").innerHTML = "required";
				output = false;
			}
			if(newPassword.value != confirmPassword.value) {
				newPassword.value="";
				confirmPassword.value="";
				newPassword.focus();
				document.getElementById("confirmPassword").innerHTML = "not same";
				output = false;
			} 	
			return output;
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
			?>

			  
			  <div id="account_info">
			    
				<div class="message"><?php if(isset($message)) { echo $message; } ?></div>

				<!-- Change email -->
				<form name="frmChange" method="post" action="" onSubmit="return validatePassword()">
				<div style="width:500px;">
				<table border="0" cellpadding="5" cellspacing="0" width="500" align="center" class="tblSaveForm">
				<tr>
				<td><label>Email Address</label></td>
				<td><input name="newEmail" class="txtField" value="<?php echo $email; ?>"/></td>
				</tr>
				<tr>
				<td colspan="2"><input type="submit" name="submit" value="Save Email Change" class="btnSubmit"></td>
				</tr>
				</table>
				</div>
				</form>

				<!-- Change password -->
				<form name="frmChange" method="post" action="" onSubmit="return validatePassword()">
				<div style="width:500px;">
				<table border="0" cellpadding="5" cellspacing="0" width="500" align="center" class="tblSaveForm">
				<tr class="tableheader">
				<td colspan="2">Change Password</td>
				</tr>
				<tr>
				<td width="40%"><label>Current Password</label></td>
				<td width="60%"><input type="password" name="currentPassword" class="txtField"/><span id="currentPassword"  class="required"></span></td>
				</tr>
				<tr>
				<td><label>New Password</label></td>
				<td><input type="password" name="newPassword" class="txtField"/><span id="newPassword" class="required"></span></td>
				</tr>
				<td><label>Confirm Password</label></td>
				<td><input type="password" name="confirmPassword" class="txtField"/><span id="confirmPassword" class="required"></span></td>
				</tr>
				<tr>
				<td colspan="2"><input type="submit" name="submit" value="Save New Password" class="btnSubmit"></td>
				</tr>
				</table>
				</div>
				</form>
			  </div>
			  
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

<?php
  include "footer.php";
?>

	</body>
</html>

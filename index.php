<?php
	include("config.php");
   
	session_save_path("tmp");
	session_id($_GET['sid']);
	session_start();
   
	function maskEmail($e) {
		return substr($e, 0, 1) . '..@..' . substr($e, strlen($e)-1);
	}
  
	if($_SERVER["REQUEST_METHOD"] == "POST") {
	   
	  if (isset($_POST['submit']) && 'Forgot Password' == $_POST['submit'] && isset($_POST['username'])) {
		  
		  echo '<!-- forgot password -->';

		  $username = $_POST['username'];
		  $sql = "SELECT email FROM tudu_users WHERE name = '$username'";
		  $result = mysqli_query($db,$sql);
		  $row = mysqli_fetch_array($result);
		  $email = '';

		  if (strlen($row['email']) > 0) {
 		    $email = $row['email'];
 			$message = "Password reset and emailed to you at " . maskEmail($email);
		  }
		  else {
 		    $email = 'todo@gilmore.cc';
 			$message = "We do not have an email address on file for you. Password reset and emailed to Todo admin at todo@gilmore.cc. Email this address to get your new password.";
		  }
		  
			$tempPwd = 'changeme'.rand(1,100);
			mysqli_query($db, "UPDATE tudu_users set p=sha1('".$tempPwd."') WHERE name='".$_POST['username']."'");
			
			$to      = $email;
			$subject = 'Gilmore Todo New Password';
			$body = "$username,\r\n\r\nYour new password for Gilmore Todo is $tempPwd. Go to your account page and change it after you successfully log in.\r\n\r\nWishing you what you wish for yourself.\r\n-Mike";
			$headers = 'From: todo@gilmore.cc' . "\r\n" .
				'Reply-To: todo@gilmore.cc' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();

			mail($to, $subject, $body, $headers);
			
			echo '<!-- mailed new password -->';
	  }
	  elseif (isset($_POST['username']) && isset($_POST['password'])) {

		echo '<!-- logging in with' . $_POST['username'] . ' -->';

		  $name = mysqli_real_escape_string($db,$_POST['username']);
		  $p = mysqli_real_escape_string($db,$_POST['password']); 
		  
		  $pos = strpos($name, '@');
		  if ($pos === true) {
			  $email = $name;
			  $name = '';
		  }
		  
		  $sql = "SELECT u.id, u.name, u.state, u.tier, l.id as personal_list_id FROM tudu_users u, tudu_lists l, tudu_list_access la WHERE (name = '$name' OR email = '$email') AND p = sha1('$p') AND l.id = la.list_id AND u.id = la.user_id AND l.title = 'personal'";
		  
		  $result = mysqli_query($db,$sql);
		  $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
		  $state = $row['state'];
		  
		  $count = mysqli_num_rows($result);
		  
		  // If result matched, table row must be 1 row
			
		  if($count == 1 and $state == 'active') {
			 $_SESSION['login_user'] = $row['name'];
			 $_SESSION['login_user_id'] = $row['id'];
			 $_SESSION['login_user_tier'] = $row['tier'];
			 $_SESSION['selected_list_id'] = $row['personal_list_id'];
			 
			 header("location: .");
		  }else {
			 $message = "Your Login Name or Password is invalid ($sql, $state, $count, $name, $p)";
		  }
	  }
   }
?>
<html>
   
	<head>
		<title>To Do</title>
      
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

			<div class="message"><?php if(isset($message)) { echo $message; } ?></div>

			<?php
			if(isset($_SESSION['login_user'])) {
				include "home.php";
			}
			else {
				include "login.php";
			}
			?>
		</div>

		<!-- Popper JS -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.6/umd/popper.min.js"></script>

		<!-- Latest compiled JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>

	</body>
</html>

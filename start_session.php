<?php
   include("config.php");
   
   if($_SERVER["REQUEST_METHOD"] == "GET") {
      
      $name = mysqli_real_escape_string($db,$_GET['username']);
      $p = mysqli_real_escape_string($db,$_GET['password']); 
      
      $sql = "SELECT u.id, u.state, u.tier, l.id as personal_list_id FROM tudu_users u, tudu_lists l, tudu_list_access la WHERE name = '$name' AND p = sha1('$p') AND l.id = la.list_id AND u.id = la.user_id AND l.title = 'personal'";
      $result = mysqli_query($db,$sql);
      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
      $state = $row['state'];
      
      $count = mysqli_num_rows($result);
      
      // If result matched, table row must be 1 row
		
      if($count == 1 and $state == 'active') {
         $_SESSION['login_user'] = $name;
         $_SESSION['login_user_id'] = $row['id'];
         $_SESSION['login_user_tier'] = $row['tier'];
         $_SESSION['selected_list_id'] = $row['personal_list_id'];
         
         session_start();
         echo "sid=".session_id();
      }else {
         $error = "Your Login Name or Password is invalid ($sql, $state, $count, $name, $p)";
      }
   }
?>

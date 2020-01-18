<?php
   include("config.php");
   
   session_save_path("tmp");
   session_id($_GET['sid']);
   session_start();
   
   $response = array();
   $response['success'] = false;
   
   if($_SERVER["REQUEST_METHOD"] == "POST") {
      
      $name = mysqli_real_escape_string($db,$_POST['username']);
      $p = mysqli_real_escape_string($db,$_POST['password']); 
      
      $sql = "SELECT u.id, u.state, u.tier, l.id as personal_list_id FROM tudu_users u, tudu_lists l, tudu_list_access la WHERE name = '$name' AND p = sha1('$p') AND l.id = la.list_id AND u.id = la.user_id AND l.title = 'personal'";
      $result = mysqli_query($db,$sql);
      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
      $state = $row['state'];
      
      $count = mysqli_num_rows($result);
      
      if($count == 1 and $state == 'active') {
          $response['success'] = true;
          $response['sid'] = session_id();
          $response['user'] = array();
          $response['user']['login_user'] = $name;
          $response['user']['login_user_id'] = $row['id'];
          $response['user']['login_user_tier'] = $row['tier'];
          $response['user']['selected_list_id'] = $row['personal_list_id'];
      } else {
          $error = "Your Login Name or Password is invalid ($state, $count, $name, $p)";
      }
   }
      
   return $response;
?>

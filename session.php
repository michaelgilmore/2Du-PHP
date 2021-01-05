<?php
   //session_save_path("tmp");
   //session_id($_GET['sid']);
   session_start();

   include('config.php');

   
   $user_check = $_SESSION['login_user'];
   
   $ses_sql = mysqli_query($db,"select name from tudu_users where name = '$user_check' ");
   
   $row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);
   
   $login_session = $row['name'];
   
   if(!isset($_SESSION['login_user'])){
      header("location:.");
   }
?>

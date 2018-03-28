<?php
   include('config.php');
   include('session.php');

   $num_todos = 100;
?>
<html">
   
   <head>
      <title>Welcome </title>
   </head>
   
   <script>
   /*function UserAction() {
     var xhttp = new XMLHttpRequest();
     xhttp.open("POST", "Your Rest URL Here", true);
     xhttp.setRequestHeader("Content-type", "application/json");
     xhttp.send();
     var response = JSON.parse(xhttp.responseText);
   }*/
   </script>
   
   <body>
      <h1>Welcome <?php echo $login_session; ?></h1>
      <h2>You have <?php echo $num_todos; ?> active todos</h2>	  
      <h3><a href = "logout.php">Sign Out</a></h2>
   </body>
   
</html>

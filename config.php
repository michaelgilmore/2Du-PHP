<?php
   define('DB_SERVER', 'gilmorec'.'.ipowermysql.com');
   define('DB_USERNAME', 'gilmore'.'c');
   define('DB_PASSWORD', '2'.'^'.'chrissy'.'c');
   define('DB_DATABASE', 'gilmorec_pda');
   $db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
   // Check connection
   //if (mysqli_connect_errno()) {
   //  echo "Failed to connect to MySQL: " . mysqli_connect_error();
   //}
?>
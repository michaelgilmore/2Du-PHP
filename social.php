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

  $sql = "SELECT count(id) FROM tudus WHERE user_id = $user_id";
  $result = mysqli_query($db,$sql);
  $row = $result->fetch_row();
  $todo_count_for_user = $row[0];

  $sql = "SELECT count(id) FROM tudus where completed_date is not null";
  $result = mysqli_query($db,$sql);
  $row = $result->fetch_row();
  $done_count = $row[0];

  $sql = "SELECT count(id) FROM tudus where completed_date is not null AND user_id = $user_id";
  $result = mysqli_query($db,$sql);
  $row = $result->fetch_row();
  $done_count_for_user = $row[0];
  
  $sql = "SELECT * FROM tudu_friends WHERE user_id = $user_id";
  $friends_result = mysqli_query($db,$sql);
?>

<html>
<!-- meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" -->
<!-- meta charset="UTF-8" -->
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
					<?php echo $todo_count_for_user; ?>/<?php echo $todo_count; ?> Todos
					</div>
					<div class="col-sm">
					</div>
					<div class="col-sm" style="color: red">
					<?php echo $done_count_for_user; ?>/<?php echo $done_count; ?> Done
					</div>
					<div class="col-sm">
					</div>
				</div>			

			</div>
			
            <hr>
            
			<div name="friend-div" style="margin-top: 100px;">
				<b>Connected Friends:</b><br>
                <table style="border-collapse:separate">
                    <tr style="background-color:gray"><th>Friend</th><th>Todos done/total</th><th>Friends since</th><th>Last todo added</th></tr>
                <?php
                    while($friend_connection_row = mysqli_fetch_array($friends_result, MYSQLI_ASSOC)) {

                        $sql = "SELECT name FROM tudu_users WHERE id = ".$friend_connection_row['friend_user_id'];
                        $result = mysqli_query($db,$sql);
                        $row = $result->fetch_row();
                        $friend_name = $row[0];
                        
                        $friends_since = $friend_connection_row['created_date'];

                        $sql = "SELECT count(id), max(created_date) as last_add_date FROM tudus WHERE user_id = ".$friend_connection_row['friend_user_id'];
                        $result = mysqli_query($db,$sql);
                        $row = $result->fetch_row();
                        $todo_count_for_user = $row[0];
                        $last_add_date = $row[1];

                        $sql = "SELECT count(id) FROM tudus where completed_date is not null AND user_id = ".$friend_connection_row['friend_user_id'];
                        $result = mysqli_query($db,$sql);
                        $row = $result->fetch_row();
                        $done_count_for_user = $row[0];

                        echo "<tr><td><b>$friend_name</b></td><td>$done_count_for_user/$todo_count_for_user</td><td>$friends_since</td><td>$last_add_date</td></tr>";
                  }
                ?>
                </table>
                <br>
				<b>Connect to Friend</b>
                <input id="connect_to_username" placeholder="username"/>
                <input value="Connect" type="button" onclick="connectFriends()"/>
                <br>
                <!--
				<b>Share list with Friend</b>
                <select id="share_list_dropdown"></select>
                <select id="share_with_user_dropdown"></select>
                <input value="Share" type="button" onclick="shareList()"/>
                -->
			</div>

		</div>

		<!-- Popper JS -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.6/umd/popper.min.js"></script>

		<!-- Latest compiled JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>

        <script>
        /*
        $(function() {

            populateListDropdownForSharing();
            populateUserDropdownForSharing();
        
        });
        
        function populateListDropdownForSharing() {
            var xhr = new XMLHttpRequest();
            var url = 'list/read.php';
            xhr.open("GET", url, true);

            xhr.onreadystatechange = function() {
                if(xhr.readyState == 4 && xhr.status == 200) {

                    alert('xhr text pldfs:'+xhr.responseText);
                    //alert('xhr type:'+xhr.responseType);
                    //alert('xhr json:'+xhr.responseJSON);
                    //alert('xhr xml:'+xhr.responseXML);

                    var select = $('#share_list_dropdown');
                    
                    var arr = JSON.parse(xhr.responseText);

                    for(var i = 0; i < arr.getLength(); i++) {
                        select.append('<option value=>' + arr[i] + '</option>');
                    }
                }
                else {
                    //we come here for state 2 and 3 before we get 4
                    //alert('state:' + xhr.readyState + ', status:' + xhr.status);
                }
            }
            xhr.send();
        }

        function populateUserDropdownForSharing() {
            var xhr = new XMLHttpRequest();
            var url = 'friend/read.php';
            xhr.open("GET", url, true);

            xhr.onreadystatechange = function() {
                if(xhr.readyState == 4 && xhr.status == 200) {

                    //alert('xhr text:'+xhr.responseText);
                    //alert('xhr type:'+xhr.responseType);
                    //alert('xhr json:'+xhr.responseJSON);
                    //alert('xhr xml:'+xhr.responseXML);

                    var select = $('#share_with_user_dropdown');
                    
                    var arr = JSON.parse(xhr.responseText);

                    $.each(arr, function(key, value) {
                        select.append('<option value=' + key + '>' + value + '</option>');
                    });
                }
                else {
                    //we come here for state 2 and 3 before we get 4
                    //alert('state:' + xhr.readyState + ', status:' + xhr.status);
                }
            }
            xhr.send();
        }
        
        function shareList() {
            var share_list_id = $('#share_list_dropdown').val();
            var share_with_user_id = $('#share_with_user_dropdown').val();
            
            var xhr = new XMLHttpRequest();
            var params = "list_id=" + share_list_id + "&user_id=" + share_with_user_id;
            var url = 'list/update.php';
            //alert(url);
            xhr.open("POST", url, true);

            xhr.onreadystatechange = function() {
                if(xhr.readyState == 4 && xhr.status == 200) {

                    //alert('xhr text:'+xhr.responseText);
                    //alert('xhr type:'+xhr.responseType);
                    //alert('xhr json:'+xhr.responseJSON);
                    //alert('xhr xml:'+xhr.responseXML);
                    //alert('You have shared ' + share_list + ' with ' + share_with_user);
                    alert('Your list has been shared');
                }
                else {
                    //we come here for state 2 and 3 before we get 4
                    //alert('state:' + xhr.readyState + ', status:' + xhr.status);
                }
            }
            xhr.send(params);
        }
        */
        
        function connectFriends() {
            var friend_username = $('#connect_to_username').val();
            
            var xhr = new XMLHttpRequest();
            var params = 'friend_username=' + friend_username;
            var url = 'friend/create_friend_request.php';
            alert(url);
            xhr.open("POST", url, true);

            xhr.onreadystatechange = function() {
                if(xhr.readyState == 4 && xhr.status == 200) {

                    //alert('xhr text:'+xhr.responseText);
                    //alert('xhr type:'+xhr.responseType);
                    //alert('xhr json:'+xhr.responseJSON);
                    //alert('xhr xml:'+xhr.responseXML);
                    alert('Your friend request has been sent to ' + friend_username);
                }
                else {
                    //we come here for state 2 and 3 before we get 4
                    //alert('state:' + xhr.readyState + ', status:' + xhr.status);
                }
            }
            xhr.send(params);
        }
        </script>
<?php
  include "footer.php";
?>

	</body>
</html>

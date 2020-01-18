<?php
require_once 'config.php';

$response = array(); 
$response['success'] = false;
$user_id;

if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $param_username = trim($_POST["username"]);
    
    // Validate username
    if(empty($param_username)) {
        $response['error'] = "Missing username\n";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM tudu_users WHERE name = ?";
        
        if($stmt = mysqli_prepare($db, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);
            
                if(mysqli_stmt_num_rows($stmt) == 1) {
                    $response['error'] = "Username, ".$param_username.", is already taken";
                }
            } else {
                $response['error'] = 'DB error 1';
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }

    if(isset($response['error'])) {
        return $response;
    }

    // Validate password
    $param_password = trim($_POST['password']);
    if(empty($param_password)) {
        $response['error'] = "Missing password";
    } elseif(strlen($param_password) < 4) {
        $response['error'] = "Password must have at least 4 characters";
    }

    if(isset($response['error'])) {
        return $response;
    }

    if(addNewUserToDB($db, $param_username, $param_password)) {
        $response['success'] = true;
    } else {
        $response['error'] = 'Failed to add new user records to database';
    }
    
    mysqli_close($db);
} else {
    $response['error'] = 'Unsupported call';
}

echo json_encode($response);


function addNewUserToDB($db, $username, $password) {
    
    $added_user = addUserRecord($db, $username, $password);
    if(!$added_user) {echo "failed to add user\n";}
    $added_users_personal_list = addInitialListRecord($db, $username);
    if(!$added_users_personal_list) {echo "failed to add list\n";}
    
    return $added_user && $added_users_personal_list;
}

// Add new user record in user table
function addUserRecord($db, $username, $password) {
    
    global $user_id, $response;
    
    $sql = "INSERT INTO tudu_users (name, p) VALUES (?, sha1(?))";
    $return_value = false;
    
    if($stmt = mysqli_prepare($db, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
        
        // Attempt to execute the prepared statement
        $added_user = mysqli_stmt_execute($stmt);
        if($added_user) {
            $user_id = mysqli_insert_id($db);
            $return_value = true;
        } else {
            $response['error'] = 'Failed to add user';
        }

        mysqli_stmt_close($stmt);
    } else {
        $response['error'] = 'Failed to prepare add user statement';
    }
    
    return $return_value;
}

// Add default 'personal' list for new user
function addInitialListRecord($db, $username) {
    
    global $user_id, $response;

    $sql = "INSERT INTO tudu_lists (title) VALUES ('personal')";

    $added_personal_list_record = $db->query($sql);
    if(!$added_personal_list_record) {
        $response['error'] = 'Failed to add personal list';
        return false;
    }

    $list_id = mysqli_insert_id($db);

    $return_value = false;
    
    // Set list access level for user's new personal list
    $set_list_access = false;
    $sql = "INSERT INTO tudu_list_access (user_id, list_id, access_level) VALUES (?, ?, 'read_write')";
    if($stmt = mysqli_prepare($db, $sql)) {
        mysqli_stmt_bind_param($stmt, 'dd', $user_id, $list_id);
        $set_list_access = mysqli_stmt_execute($stmt);
        if($set_list_access) {
            $return_value = true;
        } else {
            $response['error'] = 'Failed to set list access for new personal list';
        }
    }
    mysqli_stmt_close($stmt);
    
    return $return_value;
}
?>

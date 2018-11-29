<?php
require_once 'config.php';
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM tudu_users WHERE name = ?";
        
        if($stmt = mysqli_prepare($db, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Validate password
    if(empty(trim($_POST['password']))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST['password'])) < 4){
        $password_err = "Password must have at least 4 characters.";
    } else{
        $password = trim($_POST['password']);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = 'Please confirm password.';     
    } else{
        $confirm_password = trim($_POST['confirm_password']);
        if($password != $confirm_password){
            $confirm_password_err = 'Password did not match.';
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        
        if(addNewUserToDB($db, $param_username, $param_password)){
            // Redirect to login page
            header("location: index.php");
        } else{
            echo "Oh no! Something went wrong. Sorry about that. Please try again later or send a message to help@gilmore.cc.";
        }
    }
    
    // Close connection
    mysqli_close($db);
}

function addNewUserToDB($db, $username, $password) {
    
    $added_user = addUserRecord($db, $username, $password);
    $added_users_personal_list = addInitialListRecord($db, $username);
    
    return $added_user && $added_users_personal_list;
}

function addUserRecord($db, $username, $password) {
    
    // Prepare an insert statement
    $sql = "INSERT INTO tudu_users (name, p) VALUES (?, sha1(?))";
     
    if($stmt = mysqli_prepare($db, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
        
        // Attempt to execute the prepared statement
        $added_user = mysqli_stmt_execute($stmt);
        
        if(!$added_user) {
            echo "Error code: 101";
        }
    }
     
    // Close statement
    mysqli_stmt_close($stmt);
    
    return $added_user;
}

function addInitialListRecord($db, $username) {
    
    // Add default 'personal' list for new user
    $sql = "INSERT INTO tudu_lists (title) VALUES ('personal'))";
    $added_personal_list_record = $db->query($sql);
    if(!$added_personal_list_record) {
        echo "Error code: 102";
        return false;
    }
    $list_id = mysqli_insert_id($db);
    
    // Set list access level for user's new personal list
    $set_list_access = false;
    $sql = "INSERT INTO tudu_list_access (user_id, list_id, access_level) VALUES (?, ?, ?)";
    if($stmt = mysqli_prepare($db, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "dds", $user_id, $list_id, 'read_write');
        // Attempt to execute the prepared statement
        $set_list_access = mysqli_stmt_execute($stmt);
        
        if(!$set_list_access) {
            echo "Error code: 103";
        }
    }
    mysqli_stmt_close($stmt);
    
    return $set_list_access;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username:<sup>*</sup></label>
                <input type="text" name="username"class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password:<sup>*</sup></label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password:<sup>*</sup></label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>    
</body>
</html>

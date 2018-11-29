<?php
include "session.php";

class Friend
{
  public $table = 'tudu_friends';
  
  public $friend_username;

  private $user_id;
  
  function __construct() {
    $this->user_id = $_SESSION['login_user_id'];
  }
  
function readAllQuery() {
    return 'SELECT f.id, u.name FROM '.$this->table.' f, tudu_users u WHERE f.friend_user_id = u.id AND f.user_id = '.$this->user_id;
  }

  function getInsertSQL() {
      $sql = "INSERT into ".$this->table." SET "
        ."user_id = ".$this->user_id.","
        ."friend_username = '".$this->friend_username."'";
      return $sql;
  }
}

?>

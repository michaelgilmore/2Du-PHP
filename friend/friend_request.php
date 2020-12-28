<?php
include "session.php";

class FriendRequest
{
  public $table = 'tudu_friend_requests';
  
  public $friend_username;

  private $user_id;
  
  function __construct() {
    $this->user_id = $_SESSION['login_user_id'];
  }
  
  function getInsertSQL() {
      $sql = "INSERT into ".$this->table." SET "
        ."requester_id = ".$this->user_id.","
        ."requested_friend_id IN (SELECT id FROM tudu_users WHERE name = '".$this->friend_username."')";
      return $sql;
  }
}

?>

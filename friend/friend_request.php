<?php
session_start();

class FriendRequest
{
  public $table = 'tudu_friend_requests';
  public $friend_table = 'tudu_friends';
  
  public $friend_username;
  public $new_friend_id;

  private $user_id;
  
  function __construct() {
    $this->user_id = $_SESSION['login_user_id'];
  }
  
  function getInsertSQL() {
      $sql = "INSERT into ".$this->table." SET "
        ."requester_id = ".$this->user_id.","
        ."requested_friend_id = (SELECT id FROM tudu_users WHERE name = '".$this->friend_username."')";
      return $sql;
  }
  
  function getAcceptForwardSQL() {
      $sql = "INSERT INTO ".$this->friend_table." SET user_id=".$this->user_id.", friend_user_id=".$this->new_friend_id;
      return $sql;
  }
  
  function getAcceptReverseSQL() {
      $sql = "INSERT INTO ".$this->friend_table." SET user_id=".$this->new_friend_id.", friend_user_id=".$this->user_id;
      return $sql;
  }
  
  function getDeleteRequestSQL() {
      $sql = "DELETE FROM ".$this->table." WHERE "
        ."requester_id = ".$this->new_friend_id
		." AND requested_friend_id = ".$this->user_id;
      return $sql;
  }
}

?>

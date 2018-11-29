<?php
include "session.php";

class TodoList
{
  public $table = 'tudu_lists';
  
  public $id;
  public $title;
  public $created_date;

  private $user_id;
  
  function __construct() {
    $this->user_id = $_SESSION['login_user_id'];
  }
  
  function readAllQuery() {
    return 'SELECT l.id, l.title, l.created_date FROM '.$this->table.' l, tudu_list_access la WHERE l.id = t.list_id AND l.id = la.list_id AND la.user_id = '.$this->user_id;
  }
}

?>

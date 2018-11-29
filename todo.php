<?php
include "session.php";

class Todo
{
  public $table = 'tudus';
  
  public $id;
  public $text;
  public $created_date;
  public $updated_date;
  public $category;
  public $due_date;
  public $original_due_date;
  public $label;
  public $completed_date;
  public $details;

  private $user_id;
  private $list_id;
  
  function __construct() {
    $this->user_id = $_SESSION['login_user_id'];
    $this->list_id = $_SESSION['selected_list_id'];
  }
  
  function readQuery($id) {
    $this->id = $id;
    return 'SELECT t.id, t.text, t.created_date, t.updated_date, t.category, t.due_date, t.original_due_date, t.label, t.completed_date, t.user_id, t.details, t.list_id FROM '.$this->table.' t, tudu_lists l, tudu_list_access la WHERE l.id = t.list_id AND l.id = la.list_id AND la.user_id = '.$this->user_id.' AND t.id = '.$id;
  }
  function readAllActiveQuery() {
    return 'SELECT t.id, t.text, t.created_date, t.updated_date, t.category, t.due_date, t.original_due_date, t.label, t.completed_date, t.user_id, t.details, t.list_id FROM '.$this->table.' t, tudu_lists l, tudu_list_access la WHERE l.id = t.list_id AND l.id = la.list_id AND la.user_id = '.$this->user_id.' AND t.completed_date is null ORDER BY t.due_date ASC';
  }
  function readAllCompletedQuery() {
    return 'SELECT t.id, t.text, t.created_date, t.updated_date, t.category, t.due_date, t.original_due_date, t.label, t.completed_date, t.user_id, t.details, t.list_id FROM '.$this->table.' t, tudu_lists l, tudu_list_access la WHERE l.id = t.list_id AND l.id = la.list_id AND la.user_id = '.$this->user_id.' AND t.completed_date is not null ORDER BY t.due_date ASC';
  }
  function readListQuery($list_id) {
    $this->list_id = $list_id;
    return 'SELECT t.id, t.text, t.created_date, t.updated_date, t.category, t.due_date, t.original_due_date, t.label, t.completed_date, t.user_id, t.details, t.list_id FROM '.$this->table.' t, tudu_lists l, tudu_list_access la WHERE l.id = '.$list_id.' AND l.id = t.list_id AND l.id = la.list_id AND la.user_id = '.$this->user_id.' AND t.completed_date is null ORDER BY t.due_date ASC';
  }
  
  function getInsertSQL() {
      $sql = "INSERT into ".$this->table." SET "
        ."text = '".$this->text."',"
        ."category = '".$this->category."',"
        ."label = '".$this->label."',"
        ."user_id = ".$this->user_id.","
        ."details = '".$this->details."',"
        ."list_id = ".$this->list_id;
      if(!empty($this->due_date)) {
        $sql .= ",due_date = '".$this->due_date."'";  
      }
      return $sql;
  }
}

?>

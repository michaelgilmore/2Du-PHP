<?php
include "session.php";

class Todo
{
  public $table = 'tudus';
  
  public $select_fields = 't.id, t.text, t.created_date, t.updated_date, t.category, t.due_date, t.original_due_date, t.label, t.completed_date, t.user_id, t.details, t.list_id';
  public $select_limit = ' LIMIT 0, 1000';
  
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
    return 'SELECT '.$this->select_fields.' FROM '.$this->table.' t, tudu_lists l, tudu_list_access la WHERE l.id = t.list_id AND l.id = la.list_id AND la.user_id = '.$this->user_id.' AND t.id = '.$id.$this->select_limit;
  }
  function readAllActiveQuery() {
    return 'SELECT '.$this->select_fields.' FROM '.$this->table.' t, tudu_lists l, tudu_list_access la WHERE l.id = t.list_id AND l.id = la.list_id AND la.user_id = '.$this->user_id.' AND t.completed_date is null UNION SELECT t2.id, t2.text, t2.created_date, t2.updated_date, t2.category, t2.due_date, t2.original_due_date, t2.label, t2.completed_date, t2.user_id, t2.details, t2.list_id FROM '.$this->table.' t2 WHERE t2.list_id = 0 AND t2.user_id = '.$this->user_id.' AND t2.completed_date is null ORDER BY due_date ASC'.$this->select_limit;
  }
  function readAllCompletedQuery() {
    return 'SELECT '.$this->select_fields.' FROM '.$this->table.' t, tudu_lists l, tudu_list_access la WHERE l.id = t.list_id AND l.id = la.list_id AND la.user_id = '.$this->user_id.' AND t.completed_date is not null ORDER BY t.due_date ASC'.$this->select_limit;
  }
  function readListQuery($list_id) {
    $this->list_id = $list_id;
    return 'SELECT '.$this->select_fields.' FROM '.$this->table.' t, tudu_lists l, tudu_list_access la WHERE l.id = '.$list_id.' AND l.id = t.list_id AND l.id = la.list_id AND la.user_id = '.$this->user_id.' AND t.completed_date is null ORDER BY t.due_date ASC'.$this->select_limit;
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

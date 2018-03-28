<?php

include 'CTodo.php';


class CCategory
{
  var $m_categoryText;
  var $m_todos;

  function __construct( $text )
  {
    $this->setText( $text );
  }

  function setText( $text )
  {
    $this->m_categoryText = $text;
  }
  function getText()
  {
    return $this->m_categoryText;
  }

  function addTodo( $newTodoText )
  {
    $todoIndex = count( $this->m_todos );
    $this->m_todos[ $todoIndex ] = new CTodo( $newTodoText );
    $this->m_todos[ $todoIndex ]->setText( $newTodoText );
  }

  function getNumTodos()
  {
    return count( $this->m_todos );
  }

  function show()
  {
    print $this->getText();
/*
   $db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
   // Check connection
   if (mysqli_connect_errno()) {
     echo "Failed to connect to MySQL: " . mysqli_connect_error();
	 return;
   }
*/
   for( $i = 0; $i < count( $this->m_todos ); $i++ )
    {
      print $this->m_todos[ $i ]->getText();
	  
	  //$this->insertTodoToDB($db, $this->m_todos[ $i ]->getText(), $this->getText());
    }

	//mysqli_close($db);
  }
/*  
  function insertTodoToDB($db, $todo_text, $category) {
	  //remove the leading dot from todo text
	  $todo_text = ltrim(trim($todo_text), ".");
	  
	  //remove asterisks from category
	  $category = ltrim(trim($category), "*");
	  $category = rtrim(trim($category), "*");
	  
	  $this->add_new_todo($db, $todo_text, $category);
  }
  
  function add_new_todo($db, $tuduText, $tuduCategory) {

    $tuduText = mysqli_real_escape_string($db, $tuduText);
    $tuduCategory = mysqli_real_escape_string($db, $tuduCategory);
	
	$sql = "insert into tudus (text,status,category,due_date,type,label,user_id) ".
	   "values ( '$tuduText', 'A', '$tuduCategory', NULL, NULL, NULL, 1)";
	   
	//print "$sql\n";
	
	$result = mysqli_query($db, $sql);
	if (!$result) {
		http_response_code(404);
		die(mysqli_error($db));
	}
  }
*/
}
?>


<?php

include 'CCategory.php';


class CTodosApp
{
  var $m_categories;
  var $m_archiveFileName = 'whatgoeson.archive';

  function addCategory( $newCategoryTitle )
  {
    $categoryIndex = count( $this->m_categories );
    $this->m_categories[ $categoryIndex ] = new CCategory( $newCategoryTitle );
    $this->m_categories[ $categoryIndex ]->setText( $newCategoryTitle );
  }
  function addTodoToLastCategory( $todoText )
  {
    $categoryIndex = count( $this->m_categories ) - 1;

    if( !strstr( $this->m_categories[ $categoryIndex ]->getText(), "ARCHIVE" ) )
	{
      $this->m_categories[ $categoryIndex ]->addTodo( $todoText );
	}
	else
	{
	
		$filename = $this->m_archiveFileName;
		$somecontent = $todoText;

		// Let's make sure the file exists and is writable first.
		if (is_writable($filename)) {

			// In our example we're opening $filename in append mode.
			// The file pointer is at the bottom of the file hence
			// that's where $somecontent will go when we fwrite() it.
			if (!$handle = fopen($filename, 'a')) {
				 echo "Cannot open file ($filename)";
				 exit;
			}

			// Write $somecontent to our opened file.
			if (fwrite($handle, $somecontent) === FALSE) {
				echo "Cannot write to file ($filename)";
				exit;
			}

			//echo "Success, wrote ($somecontent) to file ($filename)";

			fclose($handle);

		} else {
			echo "The file $filename is not writable";
		}
	}
  }

  function readTodoFile()
  {
    $todoFile = file( "whatgoeson.note" );

    for( $i = 0; $i < count( $todoFile ); $i++ )
    {
      $todoFileLine = rtrim( $todoFile[ $i ] );
      if( strlen( $todoFileLine ) > 0 )
      {
        if( strstr( $todoFileLine, "**" ) )
        {
          $this->addCategory( $todoFile[ $i ] );
        }
        else
        {
		  $this->addTodoToLastCategory( $todoFile[ $i ] );
        }
      }
    }
  }

  function show()
  {
    $numCategories = count( $this->m_categories );
	$numTodos = 0;
	for( $i = 0; $i < count( $this->m_categories ); $i++ )
	{
	  $numTodos += $this->m_categories[ $i ]->getNumTodos();
	}

	print "<script>";
	print "function moveCarrot(lines){";
	print "document.getElementById('todos').scrollTop = (lines * 16);";
	print "}";
	print "</script>";
	
	print "<div align='center'>";
	print "<form method='POST' action='saveWhatGoesOn.php'>";
	print "<table border='1' bgcolor='#ffff97' height='95%'>";
	print "<tr><td><b>Categories:</b> $numCategories</td><td><b>Todos:</b> $numTodos</td></tr>";
	
	print "<tr>";
	print "<td>";

    $cumCategoryCount = 0;

    for( $i = 0; $i < $numCategories; $i++ )
    {
      print "<div onclick='javascript:moveCarrot($cumCategoryCount);'>";
	  
	  if( strstr( $this->m_categories[ $i ]->getText(), "ARCHIVE" ) )
	  {
        print $this->getNumArchivedTodos();
	  }
	  else
	  {
        print $this->m_categories[ $i ]->getNumTodos();
	  }
	  
      print $this->m_categories[ $i ]->getText();
      print "</div>";
      $cumCategoryCount = $cumCategoryCount + $this->m_categories[ $i ]->getNumTodos() + 2;
    }

	print "</td>";
	print "<td>";
    print "<textarea name='todos' cols='50' rows='25%'>";

    for( $i = 0; $i < $numCategories; $i++ )
    {
      print $this->m_categories[ $i ]->show();
      print "\n";
    }

    print "</textarea>";
	print "</td>";
	print "</tr>";
	print "</table>";
    print "<input type='submit' value='Save'></input>";
    print "</form>";
	print "</div>";
  }
  
  function getNumArchivedTodos()
  {
    $archives = file( $this->m_archiveFileName );

    return count( $archives );  
  }
}

?>
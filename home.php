<?php
  session_start();
?>
<!-- TODO: Remove all reload calls -->

<?php
  //include "session.php";
  include "db_queries.php";

  $user_id = $_SESSION['login_user_id'];

  if(isset($_GET['selected_list_id'])) {
      $_SESSION['selected_list_id'] = $_GET['selected_list_id'];
  }

  if(isset($_GET['d'])) {
      $_SESSION['active_day'] = $_GET['d'];
  }
  else {
      $_SESSION['active_day'] = '';
  }
  
  $sql = sql_select_lists_for_user($user_id);
  $accessible_lists_result = mysqli_query($db,$sql);
?>

<!--
<h1>Welcome <?php echo $_SESSION['login_user']; ?></h1>
<h3><a href = "">Sign Out</a></h2>
-->

<div class="container">

<?php
  include "nav.php";
?>

  <div class="table-responsive">
  <table id="todo-main-table" class="table table-dark table-striped">
    <thead style="background-color: gray">
      <tr>
        <th>
			<div style="display: inline">
			todo<div id="num-todos" style="color: #bbbbbb; display: inline"></div>
			</div><br>
			<div id="view-options" style="margin-left: 20px; color: #aaaaaa; display: inline">
				pa<input id="view-past-check-box" onclick="viewPast(this)" type="checkbox"/>
				&nbsp;
				fu<input id="view-future-check-box" onclick="viewFuture(this)" type="checkbox"/>
			</div>
		</th>
        <th>
            <div id="active-date-block">
            <div id="go-to-yesterday" style="display:inline" onclick="moveToYesterday()"><</div>
            <div id="active-date" style="display:inline"></div>
            <div id="go-to-tomorrow" style="display:inline" onclick="moveToTomorrow()">></div>
            </div>
            
            <div id="lists-dropdown">
                <select id="select-list" onchange="javascript:selectList()">
                    <?php
                      echo "<option value=\"all\"";
                      if($_SESSION['selected_list_id'] == 0) {
                        echo " selected";
                      }
                      echo ">View All Todos</option>";  
                      while($accessible_list_row = mysqli_fetch_array($accessible_lists_result, MYSQLI_ASSOC)) {
                        echo "<option value=\"".$accessible_list_row['id']."\"";
                        if($accessible_list_row['id'] == $_SESSION['selected_list_id']) {
                            echo " selected";
                        }
                        echo ">".$accessible_list_row['title']."</option>";  
                      }
                    ?>
                    <option value="">+Add New List+</option>
                </select>
            </div>
        </th>
        <th>due</th>
      </tr>
    </thead>
    <tbody>
		<!-- New todo text box row -->
		<tr id="new-todo-row" class="new-todo-row" style="display: none">
			<td colspan="2">
				<input name="new-todo" id="new-todo" style="width: 100%"></input>
			</td>
			<td>
				<input name="new-todo-due-date" type="text" id="datepicker" style="width: 100px" value="<?php echo date('m/d/Y'); ?>">
				<a href="javascript:addNewTodo()"><i class="fa fa-plus-circle" style="padding-left: 20px;font-size:20px;color:red;"></i></a>
			</td>
		</tr>
		
		<!-- New todo button bar row -->
        <!--
		<tr id="new-todo-row-actions" class="new-todo-row" style="display: none">
			<td colspan="3">
			<input type="button" value="Save" onclick="javascript:addNewTodo()"/><input type="button" value="Edit Details"/><input type="button" value="Send to Help"/><input type="button" value="Send to Other User"/>
			</td>
		</td>
        -->

		<!-- Todo table is inserted here -->

    </tbody>
  </table>
  </div>
</div>

<div id="todo-list-action-dialog" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content" style="width: 350px">
			<div class="modal-body" style="background-color: #444444;">
				<div id="completed-todo-text" style="color: white; font-size: 30px; padding-bottom: 30px;"></div>
				<div id="completed-date-div">
					<div id="" style="color: white;">Completed: 
						<input name="completed_date" type="text" id="completed-datepicker" style="width: 100px" value="<?php echo date('m/d/Y'); ?>">
					</div>
				</div>
				<a href="#" onclick="markTodoCompleted();"><i class="fa fa-check" style="padding: 5 25 5 5; font-size: 70px; color: green;"></i></a>
				<a id="edit-anchor" href="#" onclick="showEditTodoDialog();"><i class="fa fa-pencil" style="padding: 5 5 5 25; font-size: 60px; color: blue;"></i></a>
				<a href="#" onclick="deleteTodo();"><i class="fa fa-times" style="padding: 5 5 5 25; font-size: 60px; color: red;"></i></a>
				<input type="hidden" id="touched-todo-id" value=""/>
			</div>
		</div>
	</div>
</div>

<div id="add-list-dialog" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content" style="width: 350px">
			<div class="modal-body" style="background-color: #444444;">
				<div id="completed-todo-text" style="color: white; font-size: 30px; padding-bottom: 30px;">Add New List</div>
                <input name="new-list-title" id="new-list-title"/><br>
                <input value="Create List" type="button" onclick="addNewList();"/>
				<input type="hidden" id="user-id" value=""/>
			</div>
		</div>
	</div>
</div>

<div id="edit-todo-dialog" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content" style="width: 500px">
			<div align="right"><a href="#" onclick="closeEditDialog();">X</a>&nbsp;&nbsp;</div>
			<div class="modal-body" style="background-color: #444444;">
				<div class="container">

					<table cellpadding=5 style="color: white">

						<tr><td colspan="2"><b><input name="text" id="edit-todo-text" style="width: 40vw" value="text" onchange="updateText(this);"/></b></td></tr>
						<tr><td><b>created_date</b></td><td><div id="edit-created-date"></div></td></tr>
						<tr><td><b>updated_date</b></td><td><div id="edit-updated-date"></div></td></tr>
						<tr><td><b>category</b></td><td><input name="category" id="edit-category" value="category" onchange="updateCategory(this);"/></td></tr>
						
						<tr><td><b>due_date</b></td>
							<td><input name="due_date" type="text" id="edit-due-datepicker" style="width: 100px" value=""/>
							</td></tr>
						
						<tr><td><b>original_due_date</b></td><td><div id="edit-original-due-date"></div></td></tr>
						<tr><td><b>label</b></td><td><input name="label" id="edit-label" value="label"/></td></tr>
						
						<tr><td><b>completed_date</b></td><td>
							<input name="completed_date" type="text" id="edit-completed-datepicker" style="width: 100px" value=""/>
							</td></tr>

						<tr><td><b>details</b></td><td><input name="details" id="edit-details" value="" onchange="updateDetails(this);"/></td></tr>
							
					</table>
					
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="todo.js"></script>
<script>
// Active day defaults to today
var now = new Date();
var session_day = '<?php echo $_SESSION['active_day']; ?>';
var active_day;
if(session_day == '') {
    active_day = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0, 0);
}
else {
    active_day = new Date(session_day);
}

var count_past = false;
var count_future = false;

function viewPast(cb) {
	if(cb.checked) {
		$(".past-due").addClass("show");
        count_past = true;
	}
	else {
		$(".past-due").removeClass("show");
        count_past = false;
	}
	
	countRows();
}
function viewFuture(cb) {
	if(cb.checked) {
		$(".future-due").addClass("show");
        count_future = true;
	}
	else {
		$(".future-due").removeClass("show");
        count_future = false;
	}
	
	countRows();
}

function moveToYesterday() {
    var yesterday = new Date();
    yesterday.setDate(active_day.getDate()-1);
    gotoDay(yesterday);
}
function moveToTomorrow() {
    var tomorrow = new Date();
    tomorrow.setDate(active_day.getDate()+1);
    gotoDay(tomorrow);
}
function gotoDay(d) {
	window.location.href = 'http://gilmore.cc/todo?d=' + d.getFullYear() + '-' + (1+d.getMonth()) + '-' + d.getDate() + ' 00:00:00';
}

function countRows() {

	var row_count = 0;
    var total_not_complete = 0;
	var table = document.getElementById("todo-main-table");
    $('.past-due').each(function(i, obj) {
        if(count_past) row_count++;
		total_not_complete++;
    });
	$('.future-due').each(function(i, obj) {
        if(count_future) row_count++;
		total_not_complete++;
	});
	$('.due-today').each(function(i, obj) {
		total_not_complete++;
		row_count++;
	});
	$('#num-todos').text('(' + row_count + '/' + total_not_complete + ')');
    return total_not_complete;
}

document.getElementById("new-todo").addEventListener("keyup", function(event) {
  // Cancel the default action, if needed
  event.preventDefault();
  // 13 is the enter key
  if (event.keyCode === 13) {
      addNewTodo();
  }
});

function addList() {
	$('#add-list-dialog').modal({keyboard: true});
}

function selectList() {

    if($('#select-list').val() == 0) {
        addList();
        return;
    }
    
    $('#todo-main-table').find('tr:gt(1)').remove();
	

    var xhr = new XMLHttpRequest();
    var params = '';
    var url = '';
    if($('#select-list').val() == 'all') {
        url = 'read.php';
    }
    else {
        params = 'list_id=' + $('#select-list').val();
        url = 'read.php?' + params;
    }
    //alert(url);
	xhr.open('GET', url, true);

	xhr.onreadystatechange = function() {
		if(xhr.readyState == 4 && xhr.status == 200) {

   			//alert('xhr text:'+xhr.responseText);
			//alert('xhr type:'+xhr.responseType);
			//alert('xhr json:'+xhr.responseJSON);
			//alert('xhr xml:'+xhr.responseXML);
            
            var arr = JSON.parse(xhr.responseText);
            var out = "";
            var i;
            if(arr.length == 0) {
                out += "<tr><td colspan=3>";
                out += "<h1>You're done for today!</h1>";
	            out += "<h2>Reward yourself.</h2>";
                out += "</td></tr>";
            }
            else {
                for(i = 0; i < arr.length; i++) {
                    out += arr[i].id + '.' + arr[i].text + '\n';

                    out += "<tr class=\"" + getRowClassFromDueDate(arr[i].due_date) + "\">";
                    out += "<td id=\"" + arr[i].id + "\" colspan=2 class='todo-text-td' onclick='javascript:todoTouch(this);'>" + arr[i].text + "</td>";
                    out += "<td>"
                        + "<input type='text' id='datepicker" + i + "' value='" + formatDate(arr[i].due_date) + "' readonly"
                        + " class='todo-list-due-date' onchange='javascript:todoDueDateTouch(this, " + arr[i].id + ")'/>"
                        + "</td>";
                    out += "</tr>";
                }
            }

            $('#todo-main-table > tbody:last-child').append(out);
            
            $('#view-past-check-box')[0].checked = false;
            count_past = false;
            $('#view-future-check-box')[0].checked = false;
            count_future = false;
            
            var numTodos = countRows();
            for (var i = 0; i <= numTodos; i++) {
                $("#datepicker" + i).datepicker({
                    changeMonth: true,
                    changeYear: true
                });
            }
		}
		else {
            //we come here for state 2 and 3 before we get 4
			//alert('state:' + xhr.readyState + ', status:' + xhr.status);
		}
	}
	xhr.send();
}

function getRowClassFromDueDate(due_date_string) {

    var due_date = Date.parse(due_date_string);
    //alert('due date'+due_date);
    
    var tomorrow = new Date();
    tomorrow.setDate(active_day.getDate()+1);
    //alert('tomorrow'+tomorrow);
    var tonight = new Date(tomorrow.getFullYear(), tomorrow.getMonth(), tomorrow.getDate(), 0, 0, 0);

    row_class = "due-today";
	
	if(due_date < active_day) {
		row_class = "past-due";
	}
	if(due_date >= tonight) {
		row_class = "future-due";
	}

	return row_class;
}

function formatDate(date_string) {
	if(!date_string) return '';
    var m = new Date(date_string);
    var formatted_date = (m.getMonth()+1) + "/" + m.getDate() + "/" + m.getFullYear();
    return formatted_date;
}

function deleteTodo() {
	alert("Can't do that yet.");
}

function closeEditDialog() {
	$('#edit-todo-dialog').modal('hide');
	location.reload();
}

function addNewList() {
	var new_list = document.getElementById('new-list-title');
	
	if(new_list.value) {
		var http = new XMLHttpRequest();
		var params = "title=" + new_list.value;
		http.open("POST", "api.php/tudu_lists", true);

		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		http.onreadystatechange = function() {
			if(http.readyState == 4 && http.status == 200) {
				//success
				//alert(http.responseText);
                $('#select-list').val(new_list.value);
                $('#add-new-list').modal('hide');
				alert('hide add-new-list');
				//location.reload();
			}
		}
		http.send(params);
		//var response = JSON.parse(xhttp.responseText);
	}
	else {
		alert("I didn't understand your entry. Please try again.");
	}
}

function todoTouch(todo_text_td) {
	$('#completed-todo-text').text(todo_text_td.innerHTML);
	$('#touched-todo-id').val(todo_text_td.id);
	$('#edit-anchor').attr('href', $('#edit-anchor').attr('href') + '?id=' + todo_text_td.nextSibling.id);
	$('#todo-list-action-dialog').modal({keyboard: true});
}

function todoDueDateTouch(input_element, todo_id) {
	$('#touched-todo-id').val(todo_id);
	updateDueDate(input_element);
}

function showEditTodoDialog() {

	$('#edit-todo-text').val($('#completed-todo-text').text());
	$('#edit-todo-dialog').modal({keyboard: true});
	$('#todo-list-action-dialog').modal('hide');
	
	var xhr = new XMLHttpRequest();
	xhr.open("GET", 'api.php/tudus/'+$('#touched-todo-id').val(), true);

	xhr.onreadystatechange = function() {
		if(xhr.readyState == 4 && xhr.status == 200) {
			//success
			//alert(xhr.responseText);
			//alert(xhr.responseType);
			//alert(xhr.responseJSON);
			//alert(xhr.responseXML);
			//location.reload();
			
			//$('#edit-created-date').val(dateToString($row['created_date']));
			//$('#edit-updated-date').val(dateToString($row['updated_date']));
			//$('#edit-category').val(category);
			//$('#edit-due-date').val(dateToString($row['due_date']));
			//$('#edit-original-due-date').val(dateToString($row['original_due_date']));
			//$('#edit-label').val(label);
			//$('#edit-completed-date').val(dateToString($row['completed_date']));
			//$('#edit-details').val(details);
		}
		else {
			//alert('state:' + xhr.readyState + ', status:' + xhr.status);
		}
	}
	xhr.send();
}

$(function() {
	$("#datepicker").datepicker({
		changeMonth: true,
		changeYear: true
	});
	$("#completed-datepicker").datepicker({
		changeMonth: true,
		changeYear: true
	});
	$("#edit-due-datepicker").datepicker({
		changeMonth: true,
		changeYear: true
	});
	$("#edit-completed-datepicker").datepicker({
		changeMonth: true,
		changeYear: true
	});
    
    var active_date_div = document.getElementById("active-date");
    active_date_div.innerHTML = (1+active_day.getMonth()) + '/' + active_day.getDate() + '/' + active_day.getFullYear();

    // Initial load of todo table
    selectList();
});

function markTodoCompleted() {
    //alert("markTodoCompleted()");
	
	var completed_date_input = document.getElementById('completed-datepicker');

	updateTodoField("don", completed_date_input, false);

	$('#todo-list-action-dialog').modal({keyboard: true});
	location.reload();
}

function updateText(input_element) {
	updateTodoField("txt", input_element, false);
}

function updateDueDate(input_element) {
	updateTodoField("due", input_element, false);
}

function updateCategory(input_element) {
	updateTodoField("cat", input_element, false);
}

function updateDetails(input_element) {
	updateTodoField("det", input_element, false);
}

function updateTodoField(field, input_element, should_reload_page) {
	
	var value = input_element.value;
	var todo_id = $('#touched-todo-id').val();

	//alert(todo_id + "," + field + "," + value);
	
	if(!todo_id || !field) {
		return;
	}

	var xhr = new XMLHttpRequest();
	xhr.open("POST", 'api.php/tudus/' + $('#touched-todo-id').val(), true);

	var params = field + '=' + value;
	
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

	xhr.onreadystatechange = function() {
		if(xhr.readyState == 4 && xhr.status == 200) {
			//success
			//alert('success, response text: ' + xhr.responseText);
			if(should_reload_page) {
				location.reload();
			}
		}
		else {
			//alert('state=' + xhr.readyState + ', status=' + xhr.status);
		}
	}
	xhr.send(params);
}

function addNewTodo() {
	var new_todo = document.getElementById('new-todo');
	var new_todo_due_date = document.getElementsByName('new-todo-due-date')[0];
	
	if(new_todo.value) {
		var http = new XMLHttpRequest();
		var params = "txt=" + encodeURIComponent(new_todo.value) + "&due=" + new_todo_due_date.value;
        //alert(params);
		http.open("POST", "create.php", true);

		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		http.onreadystatechange = function() {
			if(http.readyState == 4 && http.status == 200) {
				//success
				//alert(http.responseText);
				location.reload();
			}
            else {
                //alert('state=' + http.readyState + ', status=' + http.status);
            }
		}
		http.send(params);
		//var response = JSON.parse(xhttp.responseText);
	}
	else {
		alert("I didn't understand your entry. Please try again.");
	}
}
</script>

<?php
  include "footer.php";
?>

<?php
  include "session.php";

  $user_id = $_SESSION['login_user_id'];
  $sql = "SELECT id, text, due_date, category FROM tudus WHERE user_id = '$user_id' and completed_date is null ORDER BY due_date, created_date desc LIMIT 0, 1000";
  $result = mysqli_query($db,$sql);
  
  $num_todos = mysqli_num_rows($result);
?>

<!--
<h1>Welcome <?php echo $_SESSION['login_user']; ?></h1>
<h2>You have <?php echo $num_todos; ?> active todos</h2>	  
<h3><a href = "logout.php">Sign Out</a></h2>
-->

<script>
function viewPast(cb) {
	var past_count = 0;
	if(cb.checked) {
		$(".past-due").addClass("show");
		$('.past-due').each(function(i, obj) {
			past_count++;
		});
	}
	else {
		$(".past-due").removeClass("show");
	}
	
	countRows(past_count);
}
function viewFuture(cb) {
	var future_count = 0;
	if(cb.checked) {
		$(".future-due").addClass("show");
		$('.future-due').each(function(i, obj) {
			future_count++;
		});
	}
	else {
		$(".future-due").removeClass("show");
	}
	
	countRows(future_count);
}

function countRows(initial_count) {
	var rowCount = initial_count;
	var table = document.getElementById("todo_main_table");
	$('.due-today').each(function(i, obj) {
		rowCount++;
	});
	$('#num-todos').text('(' + rowCount + '/<?php echo $num_todos; ?>)');
}
</script>

<div class="container">

<?php
  include "nav.php";
?>

  <div class="table-responsive">
  <table id="todo_main_table" class="table table-dark table-striped">
    <thead style="background-color: gray">
      <tr>
        <th>
			<div style="display: inline">
			todo<div id="num-todos" style="color: #bbbbbb; display: inline"></div>
			</div>
			<div id="view-options" style="margin-left: 20px; color: #aaaaaa; display: inline">
				past<input onclick="viewPast(this)" type="checkbox"/>
				&nbsp;
				future<input onclick="viewFuture(this)" type="checkbox"/>
			</div>
		</th>
        <!--th>cat</th-->
        <th>due</th>
      </tr>
    </thead>
    <tbody>
		<!-- New todo text box row -->
		<tr id="new_todo_row" class="new_todo_row" style="display: none">
			<td colspan="2">
				<input name="new_todo" id="new_todo" style="width: 100%"></input>
			</td>
			<td>
				<input name="new_todo_due_date" type="text" id="datepicker" style="width: 100px" value="<?php echo date('m/d/Y'); ?>">
				<a href="javascript:add_new_todo()"><i class="fa fa-plus-circle" style="padding-left: 20px;font-size:20px;color:red;"></i></a>
			</td>
		</tr>
		
		<!-- New todo button bar row -->
		<tr id="new_todo_row_actions" class="new_todo_row" style="display: none">
			<td colspan="3">
			<input type="button" value="Save" onclick="javascript:add_new_todo()"/><input type="button" value="Edit Details"/><input type="button" value="Send to Help"/><input type="button" value="Send to Other User"/>
			</td>
		</td>

		<!-- Todos rows -->
<?php
function dateToString($due_date) {
	if(!$due_date) return '';
	return date('m/d/Y', strtotime($due_date));
}

$num_todos_for_today = 0;

function getRowClassFromDueDate($due_date) {
	global $num_todos_for_today;
	
    $due_date_timestamp = strtotime($due_date);
	
	$today_timestamp = strtotime("today");
    $tomorrow_timestamp = strtotime("today +1 day");

    $row_class = "due-today";
	
	if($due_date_timestamp < $today_timestamp) {
		$row_class = "past-due";
	}
	if($due_date_timestamp >= $tomorrow_timestamp) {
		$row_class = "future-due";
	}
	
	$num_todos_for_today++;
	return $row_class;
}

$todo_num = 0;
while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
	$todo_num++;
	echo "<tr class=\"".getRowClassFromDueDate($row['due_date'])."\">";
	echo "<td id=\"".$row['id']."\" class='todo_text_td' onclick='javascript:todoTouch(this);'>".$row['text']."</td>";
	//<i class=\"fa fa-tags\" aria-hidden=\"true\"></i>
	//echo "<td>".$row['category']."</td>";
	echo "<td>"
		."<input type='text' id='datepicker".$todo_num."' value='".dateToString($row['due_date'])."' readonly"
		." class='todo_list_due_date' onchange='javascript:todoDueDateTouch(this, ".$row['id'].")'/>"
		."</td>";
	echo "</tr>";
}
?>

    </tbody>
  </table>
<?php
if($num_todos_for_today <= 0) {
	echo "<h1>You're done for today!</h1>";
	echo "<h2>Reward yourself.</h2>";
}
?>
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
				<a href="#" onclick="markTodoCompletedAndCreateAnother();"><i class="fa fa-check" style="padding: 5 25 5 5; font-size: 70px; color: orange;"></i></a>
				<a id="edit-anchor" href="#" onclick="showEditTodoDialog();"><i class="fa fa-pencil" style="padding: 5 5 5 25; font-size: 60px; color: blue;"></i></a>
				<a href="#" onclick="deleteTodo();"><i class="fa fa-times" style="padding: 5 5 5 25; font-size: 60px; color: red;"></i></a>
				<input type="hidden" id="touched-todo-id" value=""/>
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

<script>
function deleteTodo() {
	alert("Can't do that yet.");
}

function closeEditDialog() {
	$('#edit-todo-dialog').modal('hide');
	location.reload();
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

	//xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

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
			//alert(xhr.responseText);
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
	
	for ($i = 1; $i <= <?php echo $num_todos; ?>; $i++) {
		$("#datepicker" + $i).datepicker({
		changeMonth: true,
		changeYear: true
	});
	}
});

function markTodoCompleted() {
	
	var completed_date_input = document.getElementById('completed-datepicker');

	updateTodoField("don", completed_date_input, false);

	$('#todo-list-action-dialog').modal({keyboard: true});
	location.reload();
}

function copyTodoForRecurring() {
	
	//...
	
}

function markTodoCompletedAndCreateAnother() {
	
	copyTodoForRecurring();
	markTodoCompleted();
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
			//alert('success ' + xhr.responseText);
			if(should_reload_page) {
				location.reload();
			}
		}
		else {
			//alert(xhr.responseText);
		}
	}
	xhr.send(params);
}

function add_new_todo() {
	var new_todo = document.getElementById('new_todo');
	var new_todo_due_date = document.getElementsByName('new_todo_due_date')[0];
	
	if(new_todo.value) {
		var http = new XMLHttpRequest();
		var params = "txt=" + new_todo.value + "&due=" + new_todo_due_date.value;
		http.open("POST", "api.php/tudus", true);

		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		http.onreadystatechange = function() {
			if(http.readyState == 4 && http.status == 200) {
				//success
				//alert(http.responseText);
				location.reload();
			}
		}
		http.send(params);
		//var response = JSON.parse(xhttp.responseText);
	}
	else {
		alert("I didn't understand your entry. Please try again.");
	}
}

countRows(0);
</script>

<script>
function showNewTodoRow() {
	document.getElementById("new-todo-row").style.display = "";
	//document.getElementById("new-todo-row-actions").style.display = "";
    //window.setTimeout(function () {
        document.getElementById('new-todo').focus();
    //}, 0);
}
</script>


<div class="row" style="padding: 12">
  <div class="col">
	<a href=".">
	  <i class="fa fa-home" style="font-size:10vw;color:blue;"></i>
	</a>
  </div>
  <div class="col">
	<a href="account.php">
	  <i class="fa fa-user" style="font-size:10vw;color:blue;"></i>
	</a>
  </div>
  <div class="col">
	<a href="social.php">
	  <i class="fa fa-group" style="font-size:10vw;color:blue;"></i>
	</a>
  </div>
  <div class="col">
	<a href="stats.php">
	  <i class="fa fa-bar-chart" style="font-size:10vw;color:blue;"></i>
	</a>
  </div>
  <div class="col">
	<a href="javascript:showNewTodoRow()">
	  <i class="fa fa-plus-circle" style="font-size:10vw;color:red;"></i>
	</a>
  </div>
</div>

<script>
function add_todo() {
	document.getElementById("new_todo_row").style.display = "";
	document.getElementById("new_todo").focus();
}
function submit_help_todo() {
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
	<a href="javascript:submit_help_todo()">
	  <i class="fa fa-question" style="font-size:10vw;color:orange;"></i>
	</a>
  </div>
  <div class="col">
	<a href="javascript:add_todo()">
	  <i class="fa fa-plus-circle" style="font-size:10vw;color:red;"></i>
	</a>
  </div>
</div>

<?php
  include "session.php";

  $user_id = $_SESSION['login_user_id'];

  $sql = "SELECT CONCAT(CONCAT(MONTH(completed_date), '-'), DAY(completed_date)), count(id) FROM tudus WHERE user_id = '$user_id' and completed_date is not null and completed_date >= '2018-01-01' GROUP BY CONCAT(CONCAT(MONTH(completed_date), '/'), DAY(completed_date)) ORDER BY completed_date LIMIT 0, 1000";
  $result = mysqli_query($db,$sql);
  
  $fp = fopen("bar-data$user_id.csv", 'w');
  fwrite($fp, "date,value\n");
  while ($row = $result->fetch_array()) {
    fwrite($fp, $row[0] . "," . $row[1] . "\n");
  }
  fclose($fp);
  
  $sql = "SELECT text FROM tudus WHERE user_id = '$user_id' AND completed_date is null AND id = (SELECT MIN(id) FROM tudus WHERE completed_date is null)";
  $result = mysqli_query($db,$sql);
  $row = $result->fetch_array();
  $oldest = $row[0];

  $sql = "SELECT text FROM tudus WHERE user_id = '$user_id' AND completed_date is null AND id = (SELECT MIN(id) FROM tudus WHERE completed_date is null)";
  $result = mysqli_query($db,$sql);
  $row = $result->fetch_array();
  $most_moved = $row[0];
?>

<html>
   
	<head>
		<title>Stats</title>
      
		<link rel="stylesheet" href="gilmore_todo.css">

		<!-- jQuery library -->
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>

	.axis {
	  font: 10px sans-serif;
	}

	.axis path,
	.axis line {
	  fill: none;
	  stroke: #000;
	  shape-rendering: crispEdges;
	}

</style>
	</head>
	
	<body bgcolor = "#FFFFFF">
	
		<div align = "center">
 
			<div class="container">

			<?php
			  include "nav.php";
			?>
			
			<div id="bar-chart" align="center" style="margin-top: 50px"></div>

<script src="http://d3js.org/d3.v3.min.js"></script>

<script>
var margin = {top: 20, right: 20, bottom: 70, left: 40},
    width = 600 - margin.left - margin.right,
    height = 300 - margin.top - margin.bottom;

// Parse the date / time
var	parseDate = d3.time.format("%m-%d").parse;

var x = d3.scale.ordinal().rangeRoundBands([0, width], .05);

var y = d3.scale.linear().range([height, 0]);

var xAxis = d3.svg.axis()
    .scale(x)
    .orient("bottom")
    .tickFormat(d3.time.format("%m-%d"));

var yAxis = d3.svg.axis()
    .scale(y)
    .orient("left")
    .ticks(10);

var svg = d3.select("#bar-chart").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
  .append("g")
    .attr("transform", 
          "translate(" + margin.left + "," + margin.top + ")");

d3.csv("bar-data<?php echo $user_id; ?>.csv", function(error, data) {

    data.forEach(function(d) {
        d.date = parseDate(d.date);
        d.value = +d.value;
    });
	
  x.domain(data.map(function(d) { return d.date; }));
  y.domain([0, d3.max(data, function(d) { return d.value; })]);

  svg.append("g")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + height + ")")
      .call(xAxis)
    .selectAll("text")
      .style("text-anchor", "end")
      .attr("dx", "-.8em")
      .attr("dy", "-.55em")
      .attr("transform", "rotate(-90)" );

  svg.append("g")
      .attr("class", "y axis")
      .call(yAxis)
    .append("text")
      .attr("transform", "rotate(-90)")
      .attr("y", 6)
      .attr("dy", ".71em")
      .style("text-anchor", "end")
      .text("# Todos");

  svg.selectAll("bar")
      .data(data)
    .enter().append("rect")
      .style("fill", "steelblue")
      .attr("x", function(d) { return x(d.date); })
      .attr("width", x.rangeBand())
      .attr("y", function(d) { return y(d.value); })
      .attr("height", function(d) { return height - y(d.value); });

});

</script>			
			
			</div>

		</div>
		
		<div>

			<div>Oldest: <?php echo $oldest; ?></div>
			<div>Most Moved: <?php echo $most_moved; ?></div>
		
		</div>

		<!-- Popper JS -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.6/umd/popper.min.js"></script>

		<!-- Latest compiled JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>

	</body>
</html>

<?php

$class = 'default';
foreach ($forecasts as $forecast):

$conditions = $forecast['conditions'];

switch ($conditions) {

case strpos($conditions, 'rain'):
$class='rain';
break;

case strpos($conditions, 'sun'):
$class='sun';
break;

case strpos($conditions, 'cloud'):
$class='cloud';
break;

case strpos($conditions, 'rain'):
$class='rain';
break;

}

endforeach;



 ?>	

<div class="container <?php echo $class; ?>">
<div class="row-fluid">

<div class="col-sm-12">

<div class="col-sm-6 forecast-box">
<div class="alert alert-success">
  <strong><?php echo $success_message; ?>:</strong> Below the are the results from the Database
</div>
<div class="col-sm-6">
<h4 class="date">Today, <?php echo date('D M Y');?></h4>
</div>
<div class="col-sm-6 right">
<h5><i class="fa fa-map-pin"></i> <?php echo $town; ?></h5>
</div>
 
<!--<div class="alert alert-success">
  <strong><?php //echo $success_message; ?>:</strong> Below the are the results from the Database
</div> -->


<?php foreach ($forecasts as $forecast): ?>
	<div class="col-sm-12">
	<div class="col-sm-4">
	<p class="forecast-head"><small>Avg Temp</small></p>
	<h1 class="temp"><?php echo $forecast['avg_temp']; ?><small>&deg;C</small></h1>
	<p class="copyright"><?php echo $source[0]['copyright']; ?></p>
	</div>
	<div class="col-sm-4">
	<p class="forecast-head"><small>Conditions </small></p>
	<p class="conditions"><?php echo $forecast['conditions']; ?></p>
	
    </div>
	<div class="col-sm-4 icon">
	<i class="fa fa-sun-o"></i>
	</div>
	</div>
<?php endforeach; ?>

</div>

<div class="col-sm-5 col-sm-offset-1 forecast-box">

<canvas id="weatherChart"></canvas>

<script>


</script>

</div>

</div>

</div>

</div>
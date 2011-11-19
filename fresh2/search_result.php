<?php 
	require_once("config.php"); 
?>
<?php header('Content-type: text/html; charset=utf-8'); ?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
<head>
<?php require_once("css.php"); ?>
</head>
<body>
<?php
	require_once("GPlaceAPI.php");
	$place = new GPlaceAPI();
	
	$lat = $_POST['lat'];
	$lng = $_POST['lng'];
	$userId = $_POST['user_id'];
	$firstName = $_POST['first_name'];
	if ($lat==""){
		die("Your device does not support GPS, or you did not allow application using location information");
	}
	if (!array_key_exists('search_key',$_POST)){
		$searchResult = $place->searchBusiness("",$lat,$lng);
	}
	else{
		$searchResult = $place->searchBusiness($_POST['search_key'],$lat,$lng);
	}
	
	$listResult = $searchResult["results"];
	?>
	<form action="search_result.php" method="post">	
		<input type="hidden" name="lat" id="lat" value="<?php echo $lat; ?>" >
		<input type="hidden" name="lng" id="lng" value="<?php echo $lng; ?>">
		<input type="hidden" name="user_id" id="first_name" value="<?php echo $firstName; ?>" >	
		<input type="hidden" name="user_id" id="user_id" value="<?php echo $userId; ?>">
		<br />
		<input type="text" name="search_key" id="search_key" >
		<input type="submit" value="Search" >
	<form>
	<?php
	echo '<table>';
	$count =1;
	foreach ($listResult as $i => $value) {
		echo '<tr>';												
		echo  "<td><a href='checkin.php?place_id=" . $value['id'] . "&reference=" . $value['reference'] ."&lat=" . $value['geometry']['location']['lat'] ."&lng=" . $value['geometry']['location']['lng'] ."&uid=" . $userId . "'>" . $value['name'] . "</a></td>";		
		echo '</tr>';
		if ($count++ > 25)
			break;
	}
	echo '</table>';
?>
</body>
</html>
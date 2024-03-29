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
	$lat = 40.7558;
	$lng = -73.9675;
	
/* COMMENTING THIS OUT FOR IRISH EXIT TEST

	$lat = $_POST['lat'];
	$lng = $_POST['lng'];

*/
	$userId = $_POST['user_id'];
	$access_token = $_POST['access_token'];
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
	
	<b>
	Pick this place from the list below
	<br>
	to join the conversation!
	</b>
	<br/>
	<br/>
	(Or search using the search box)
	<br/>
	<br/>
	
	
	<?php
	echo '<table>';
	// Here we test whether near Irish Exit, then display link for Irish Exit with id=IrishExitForced
	if ($lat > 40.74 && $lat < 40.77 && $lng > -73.98 && $lng < -73.955) {
		echo '<tr>';
		echo "<td><a href='checkin.php?place_id=IrishExitForced&uid=" . $userId . "&access_token=" . $access_token . "'>The Irish Exit</a></td>";
		echo '</tr>';
		}
	// Here we test whether near Rivergate Balcony, then display link for Rivergate Balcony with id=RivBalcForced
	if ($lat > 40.73 && $lat < 40.755 && $lng > -73.98 && $lng < -73.965) {
		echo '<tr>';
		echo "<td><a href='checkin.php?place_id=RivBalcForced&uid=" . $userId . "'>Rivergate Balcony</a></td>";
		echo '</tr>';
		}
	// Now the actual regular results
	$count =1;
	foreach ($listResult as $i => $value) {
		echo '<tr>';												
		echo  "<td><a href='checkin.php?place_id=" . $value['id'] . "&reference=" . $value['reference'] ."&lat=" . $value['geometry']['location']['lat'] ."&lng=" . $value['geometry']['location']['lng'] ."&uid=" . $userId . "&access_token=" . $access_token . "'>" . $value['name'] . "</a></td>";		
		echo '</tr>';
		if ($count++ > 15)
			break;
	}
	echo '</table>';
	
	
?>

	<form action="search_result.php" method="post">	
		<input type="hidden" name="lat" id="lat" value="<?php echo $lat; ?>" >
		<input type="hidden" name="lng" id="lng" value="<?php echo $lng; ?>">
		<input type="hidden" name="user_id" id="first_name" value="<?php echo $firstName; ?>" >	
		<input type="hidden" name="access_token" id="access_token" value="<?php echo $access_token; ?>" >	
		<input type="hidden" name="user_id" id="user_id" value="<?php echo $userId; ?>">
		<br />
		<input type="text" name="search_key" id="search_key" >
		<input type="submit" value="Search" >
	</form>

</body>
</html>
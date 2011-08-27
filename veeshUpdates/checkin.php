<?php
	require_once("config.php");
	require_once("GPlaceAPI.php");
	$place = new GPlaceAPI();
?>
<?php header('Content-type: text/html; charset=utf-8'); ?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
<head>
<?php require_once("css.php"); ?>
</head>
<body>
<?php	
	$userId = 0;
	$placeId  = 0;
	$placeReference = "";
	$placeName  = "";
	$lat = 0;
	$lng  = 0;
	//if(isset($_SESSION['user_id'])){
	//	$userId = $_SESSION['user_id'];
	//}
	//else{
	if (isset($_GET['uid'])){
		$userId = $_GET['uid'];				
	}
	//}
	if (isset($_GET['place_id'])){
		$placeId = $_GET['place_id'];
	}
	if (isset($_GET['reference'])){
		$placeReference = $_GET['reference'];
	}
	if (isset($_GET['place_name'])){
		$placeName = $_GET['place_name'];
	}
	if (isset($_GET['lat'])){
		$lat = $_GET['lat'];
	}
	if (isset($_GET['lng'])){
		$lng = $_GET['lng'];
	}
	$query = sprintf("Select * from places where place_id='%s'",mysql_real_escape_string($placeId));
	$result = mysql_query($query);
	if (!$result) {
		$message  = 'Could not get place detail!' . $query;    
		die($message);
	}
	$freshPlaceId = 0;
	$isOldPlace = false;
	if ($row = mysql_fetch_assoc($result)) {
		$freshPlaceId = $row['fresh_place_id'];
		$isOldPlace = true;
	}	
	else{
		$query = sprintf("Insert into places(place_id,source,place_name,lat,lng,place_ref) values ('%s','G','%s',%s,%s,'%s') ",$placeId,mysql_real_escape_string($placeName),$lat,$lng,mysql_real_escape_string($placeReference));
		$result = mysql_query($query);
		$freshPlaceId = mysql_insert_id();	
	}
	$query = sprintf("Insert into checkin(fresh_place_id,person_id) values (%s,'%s')",$freshPlaceId,$userId);
	$result = mysql_query($query);
	
	
	// Check result		
	if ($isOldPlace) {
		//Place already existed
		$query = sprintf("SELECT place_name,vicinity,phone,address,rating FROM place_detail where place_id='%s' ",$placeId);
		// Perform Query
		$result = mysql_query($query);
		// Check result
		// This shows the actual query sent to MySQL, and the error. Useful for debugging.
		if (!$result) {
			$message  = 'Could not get place detail!' . $query;    
			die($message);
		}
		// Use result
		// One of the mysql result functions must be used		
		if ($row = mysql_fetch_assoc($result)) {
			echo "You are now Checked Into: ";
			echo $row['place_name'] . "!";
			// commenting these results out because now we are going to focus on messages, but they were originally there to enable confirmation of successful check-in and access to Gplaces
			// echo "<br />Vicinity: " . $row['vicinity'];
			// echo "<br />Phone: " . $row['phone'];
			// echo "<br />Address: " . $row['address'];
			// echo "<br />Rating: " . $row['rating'];
		}
	}
	else{
		// commenting out text like phone number etc, to focus on messaging, but they were there for debugging
		echo "You are now Checked Into: ";
		$businessDetail = $place->getBusinessDetail($placeReference);
		$result = $businessDetail['result'];		 
		echo "<br />Location Name: " . $result['name'];
		$locationName = mysql_real_escape_string($result['name']);
		$vicinity = "";
		if (isset($result['vicinity'])){
			//echo "<br />Vicinity: " . $result['vicinity'];
			$vicinity = mysql_real_escape_string($result['vicinity']);
		}
		$phone = "";
		if (isset($result['formatted_phone_number'])){
			// echo "<br />Phone: " . $result['formatted_phone_number'];
			$phone = mysql_real_escape_string($result['formatted_phone_number']);
		}
		echo "<br />Address: " . $result['formatted_address'];
		$address = mysql_real_escape_string($result['formatted_address']); 
		$rating = 0;
		if (isset($result['rating'])){
			// echo "<br />Rating: " . $result['rating'];
			$rating = mysql_real_escape_string($result['rating']); 
		}
		$query = sprintf("Insert into place_detail(fresh_place_id,place_id,place_name,lat,lng,place_ref,vicinity,phone,address,rating) values (%s,'%s','%s',%s,%s,'%s','%s','%s','%s',%s) ",$freshPlaceId,$placeId,$locationName,$lat,$lng,mysql_real_escape_string($placeReference),$vicinity,$phone,$address,$rating);
		$result = mysql_query($query);
		//echo $query;
	}
	
?>
<!-- Now we let people leave and read messages -->
Leave a message!
<form action="messageboard.php" method="post">	
		<input type="hidden" name="user_id" id="user_id" value="<?php echo $userId; ?>">
		<input type="hidden" name="place_id" id="place_id" value="<?php echo $placeId; ?>">
		<br />
		<input type="text" name="msg_content" id="msg_content" >
		<input type="submit" value="Yodel It!" >
	</form>

<!-- Read messages -->
<br>
'Da Message Board:
<br>
<?php
	// SQL QUERY TO RETREIVE MESSAGES, AND RETREIVE THE FIRST NAME OF THE AUTHOR OF EACH MESSAGE
	$query = sprintf("SELECT DATE_FORMAT(messages.date_and_time,'%W %r'),messages.msg_content,person.first_name FROM messages LEFT JOIN person USING (person_id) WHERE messages.place_id='%s' ",mysql_real_escape_string($placeId);
		// Perform Query
		$result = mysql_query($query);
		// Check result
		//echo $query;
		// This shows the actual query sent to MySQL, and the error. Useful for debugging.
		if (!$result) {
			$message  = 'Zero Messages';    
			die($message);
		}
		// Fetch the messages from mysql and echo in a table
		echo '<table>';
		$count =1;
		while ($row = mysql_fetch_assoc($result)) {
			echo '<tr>';
			echo "<b>" . $row['first_name'] . "</b>" . " (" . $row['date_and_time'] . ") : " $row['msg_content'];
			echo '</tr>';
			if ($count++ > 20)
				break;
		}
		echo '</table>';
		else{		
			die("Sorry, we had an error accessing rows for this place in the MySQL database.");
		}

?>

</body>
</html>
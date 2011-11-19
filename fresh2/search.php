<?php 
	require_once("config.php");
	$firstName = "";
	$pic = "";
	$userId = 0;
	$isFb = false;
	$access_token = false;
	if(isset($_COOKIE['fbs_222936987739560'])){
		$authCode = $_COOKIE['fbs_222936987739560'];			
		$pieces = explode("&", $authCode);
		$access_token = "";
		//access_token=222936987739560|2.AQAFPph1x4YEMHGJ.3600.1309590000.1-703078927|7mHGzDUlGA-laxGJCCivYLRz5O8&
		if (isset($pieces[0])){
			$tmp = explode("=", $pieces[0]);
			if (isset($tmp[1])){
				$access_token = $tmp[1];
			}
		}	
	}
	if(isset($_COOKIE['access_token'])){
		$access_token = $_COOKIE['access_token'];
	}
	
	if ($access_token){
		$requestUrl = "https://graph.facebook.com/me?access_token=" . $access_token;
		$result = json_decode(@file_get_contents($requestUrl), true);
		if (isset($result['id'])){
			$isFb = true;
			$userId = $result['id'];
		}
		if (isset($result['first_name'])){					
			$firstName = $result['first_name'];
		}
		$pic = "https://graph.facebook.com/me/picture?access_token=" . $access_token;
		/*
		if (isset($result['username'])){					
			$userName = $result['username'];
			$pic = "https://graph.facebook.com/" . $userName . "/picture";
		}
		*/
	}
	
	
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
<head>
<?php require_once("css.php"); ?>
<script type="text/javascript">
    function updateLocation(position) {	
     	//alert("OK: " + position.coords.latitude + ":" + position.coords.longitude); 
		try{
			var lat = document.getElementById("lat");
			lat.value = position.coords.latitude;
			var lng = document.getElementById("lng");
			lng.value = position.coords.longitude;			
			var searchForm = document.getElementById("searchForm");
			searchForm.submit();
		}
		catch (Ex){
			var messageDiv = document.getElementById("messageDiv");
			messageDiv.innerHTML = "Error" + Ex;
		}
    }
	function handleError(error){
		var messageDiv = document.getElementById("messageDiv");
		messageDiv.innerHTML = "Error" + error;
	}    
	function doSearch(){
		if (navigator.geolocation){
			navigator.geolocation.getCurrentPosition(updateLocation,handleError,{enableHighAccuracy: true});
			var messageDiv = document.getElementById("messageDiv");
			messageDiv.innerHTML = "Getting your current location, please wait...";
		}
		else{
			var messageDiv = document.getElementById("messageDiv");
			messageDiv.innerHTML = "Your device does not support geo location. Search button won't work, sorry!";
		}
	}
</script>

</head>
<body>
<?php
	
	
	if ($isFb){

		/// insert fb user into person database
		$query = sprintf("Insert into person(fb_user_id, last_name, first_name, password,pic) values ('%s','%s','%s','%s','images/logo.jpeg') ",mysql_real_escape_string($userId),mysql_real_escape_string($lastName),mysql_real_escape_string($firstName),mysql_real_escape_string($password));
		$result = mysql_query($query);
		// Check result		
		if (!$result) {
			$message  = 'Register error';    
			die($message);
		}
		
		/// get user row back
		$query = sprintf("SELECT person_id, first_name,pic FROM person where fb_user_id='%s' ",mysql_real_escape_string($userId));
		// Perform Query
		$result = mysql_query($query);
		// Check result
		//echo $query;
		// This shows the actual query sent to MySQL, and the error. Useful for debugging.
		if (!$result) {
			$message  = 'Login error!';    
			die($message);
		}

		
	}
	
	
	else{
		$email = $_POST['email'];
		$password = $_POST['password'];
		if ($email == "" || $password == ""){
			die("Please enter user and password");
		}
		$query = sprintf("SELECT person_id,password, first_name,pic FROM person where email='%s' ",mysql_real_escape_string($email));
		// Perform Query
		$result = mysql_query($query);
		// Check result
		//echo $query;
		// This shows the actual query sent to MySQL, and the error. Useful for debugging.
		if (!$result) {
			$message  = 'Login error!';    
			die($message);
		}
	}
		
		
		// Use result
		// One of the mysql result functions must be used
		if ($row = mysql_fetch_assoc($result)) {
			if (!$isFb && $password != $row['password']){
				die("Login failed!");
			}    
			$firstName = $row['first_name'];
			$pic = $row['pic'];
			$userId = $row['person_id'];
			$_SESSION['user_id'] = 	$row['person_id'];	
			$_SESSION['first_name'] = 	$firstName;
		}
		else{		
			die("Login falied!");
		}
	
	echo 'Welcome ' . $firstName;
	echo "<br/><img src='$pic' width='80'>";
?>
<form action="search_result.php" method="post" id="searchForm">	
	<input type="hidden" name="lat" id="lat" value="" >
	<input type="hidden" name="lng" id="lng" value="" >
	<input type="hidden" name="user_id" id="first_name" value="<?php echo $firstName; ?>" >	
	<input type="hidden" name="user_id" id="user_id" value="<?php echo $userId; ?>" >
	<br />	
	<input type="button" value="Search Near By" id="SearchButton" onclick="doSearch();" >
	<br />
	<br />
	<input type="button" value="Log Out" id="LogOutButton" onclick="jscript:window.location.href = 'logout.php';" >
	<br />
	<div id='messageDiv'>...</div>	
<form>

</body>
</html>
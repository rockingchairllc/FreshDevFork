<?php 
session_start(); 
if(isset($_COOKIE['fbs_222936987739560'])){
	if ($_COOKIE['fbs_222936987739560'] != "")
		header("Location: search.php");
}
if (isset($_GET['code'])){
	$code = $_GET['code'];
	$requestUrl = "https://graph.facebook.com/oauth/access_token?type=client_cred&client_id=222936987739560&redirect_uri=http://ec2-174-129-1-223.compute-1.amazonaws.com&client_secret=f0504763d513b9791e005799afd9f66d&code=" . $code;
	//$result = json_decode(@file_get_contents($requestUrl), true);
	$result = @file_get_contents($requestUrl);
	setrawcookie('fbs_222936987739560', $result);
	//header("Location: search.php");
	//echo 'Request OK <hr /> ' . $requestUrl;
	echo '<hr />';
	echo $result;
	echo '<hr />';
	//$_COOKIE['fbs_222936987739560'] = $result;
	//$result = getHttpResult(https://graph.facebook.com/oauth/access_token?client_id=YOUR_APP_ID&redirect_uri=YOUR_URL&client_secret=YOUR_APP_SECRET&code=THE_CODE_FROM_ABOVE);
}
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
<head>
<script type="text/javascript">
function setCookie(c_name,value,exdays)
{
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	//var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	var c_value=value + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie=c_name + "=" + c_value;
}

var hashString = location.hash;
var nvPairs = hashString.split(";");
var nvPair = new Array();
hashString = (hashString.substring(1));
if (hashString.indexOf('access_token') > -1){
	hashString = hashString.substring(13);
	setCookie('access_token',hashString,1);
	window.location.href = 'search.php';	
}
</script>
<?php 
require_once("css.php"); 
//fbs_222936987739560
/*
if(isset($_COOKIE['fbs_222936987739560'])){
	$authCode = $_COOKIE['fbs_222936987739560'];
	//print_r($authCode);
	//echo "<br />";
	$pieces = explode("&", $authCode);
	$access_token = "";
	//access_token=222936987739560|2.AQAFPph1x4YEMHGJ.3600.1309590000.1-703078927|7mHGzDUlGA-laxGJCCivYLRz5O8&
	if (isset($pieces[0])){
		$tmp = explode("=", $pieces[0]);
		if (isset($tmp[1])){
			$access_token = $tmp[1];
			//print($access_token);
			$requestUrl = "https://graph.facebook.com/me?access_token=" . $access_token;
			$result = json_decode(@file_get_contents($requestUrl), true);
			if (isset($result['id'])){
				
			}
			//echo "<hr />";
			//print($result);		
		}
	}	
}
*/

//echo "Login Time: " . time() . " <br />";
//print_r($_GET);
?>
</head>
<body>
<b>Welcome to MyFresh</b>
<br>
<br>
A place to connect with
<br>
people in the same
<br>
place as you.
<br>
<br>
<form method="post" action="search.php">
	<table >	
		<tr><td colspan="2"><input type="button" value="Connect Using Facebook"  onclick="jscript:window.location.href = 'fblogin.php';"></td></tr>
		<tr><td colspan="2"><input type="button" value="Sign Up the Old Way"  onclick="jscript:window.location.href = 'register.php';"></td></tr>
		<tr><td colspan="2"><br></td></tr>
		<tr><td colspan="2">Login:</td></tr>
		<tr><td>Email:</td><td><input type="text" name="email" ></td></tr>
		<tr><td>Password:</td><td><input type="password" name="password" ></td></tr>
		<tr><td colspan="2"><input type="submit" value="Login" name="Login"> </td></tr>
		
	</table> 
</form>
</body>
</html>
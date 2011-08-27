<?php
session_start();
//Killing the cookie:
$cookie_name="access_token";
//here we assign a "0" value to the cookie, i.e. disabling the cookie:
$cookie_value="";
//When deleting a cookie you should assure that the expiration date is in the past,
//to trigger the removal mechanism in your browser.
$cookie_expire=time()-60;
$cookie_domain="ec2-174-129-1-223.compute-1.amazonaws.com";
setcookie($cookie_name, $cookie_value, $cookie_expire, "/", $cookie_domain,0);
$cookie_name="PHPSESSID";
setcookie($cookie_name, $cookie_value, $cookie_expire, "/", $cookie_domain,0);
//re-direct to login screen (or any other you like):
//header( "Location:index.php");
echo 'You have signed out! ';
echo '<a href="index.php">Login Again</a>';
?>
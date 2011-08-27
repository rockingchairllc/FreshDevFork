<?php
	require_once("config.php");
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
<head>
<?php require_once("css.php"); ?>
<script type="text/javascript">
    function submitForm(){
    }
</script>
</head>
<body>
<?php	
	$email = "";
	$firstName = "";
	$lastName = "";
	$password = "";
	$confirmPassword = "";
	$valid = true;
	$error = "";
	if (array_key_exists('email',$_POST)){	
		$email = $_POST['email'];
		if (strlen($email) > 45){
			$valid = false;
			$error = $error . "Email too long <br />";
		}		
		if (stripos($email, '@') === false || stripos($email, '.') === false){
			$valid = false;
			$error = $error . "Invalid email <br />";
		}
	}
	if ($email == ""){
		$valid = false;
		$error = $error . "Invalid email<br />";
	}
	if (array_key_exists('first_name',$_POST)){	
		$firstName = $_POST['first_name'];
		if (strlen($firstName) > 20){
			$valid = false;
			$error = $error . "First Name too long <br />";
		}
	}	
	if ($firstName == ""){	
		$valid = false;
		$error = $error . "Invalid first name<br />";
	}
	if (array_key_exists('last_name',$_POST)){	
		$lastName = $_POST['last_name'];
		if (strlen($lastName) > 20){
			$valid = false;
			$error = $error . "Last Name too long <br />";
		}
	}
	if ($lastName == ""){
		$valid = false;
		$error = $error . "Invalid last name<br />";
	}
	if (array_key_exists('password',$_POST)){	
		$password = $_POST['password'];
		if (strlen($password) > 20){
			$valid = false;
			$error = $error . "Password too long <br />";
		}
	}
	if ($password == ""){
		$valid = false;
		$error = $error . "Invalid password<br />";
	}	
	if (!$valid){
?>
Fresh Sign Up
<form method="post" action="register.php">
	<table>	
		<?php if ($email != "") {
			echo "<tr><td colspan='2'><font color='red'>" . $error . "</font></td></tr>";
		}?>
		<tr><td>Email:</td><td><input type="text" name="email" value="<?php echo $email; ?>">*</td></tr>
		<tr><td>First Name:</td><td><input type="text" name="first_name" value="<?php echo $firstName; ?>">*</td></tr>
		<tr><td>Last Name:</td><td><input type="text" name="last_name" value="<?php echo $lastName; ?>">*</td></tr>
		<tr><td>Password:</td><td><input type="password" name="password" >*</td></tr>		
		<tr><td colspan="2"><input type="submit" value="Register" name="Register"> </td></tr>		
	</table> 
</form>
<?php 
	} else {	
		$query = sprintf("SELECT first_name FROM person where email='%s' ",mysql_real_escape_string($email));
		// Perform Query
		$result = mysql_query($query);
		if (!$result) {
			$message  = 'Register error!';    
			die($message);
		}
		// Use result		
		if ($row = mysql_fetch_assoc($result)) {
			$message  = 'Account ' . $email . " already existed!";    
			die($message);
		}

		$query = sprintf("Insert into person(email, last_name, first_name, password,pic) values ('%s','%s','%s','%s','images/logo.jpeg') ",mysql_real_escape_string($email),mysql_real_escape_string($lastName),mysql_real_escape_string($firstName),mysql_real_escape_string($password));
		$result = mysql_query($query);
		// Check result		
		if (!$result) {
			$message  = 'Register error';    
			die($message);
		}
		else{
			 echo "Thank you for joining us.<br />";
			 $to = $email;
			 $subject = "[FRESH] Register Confirm";
			 $body =  "Welcome ". $_POST['first_name'];
			 $body .= "<br />Thank you for joining us, you can login here http://ec2-174-129-1-223.compute-1.amazonaws.com <br />";	
			 $body .= "Best Regards,<br />Fresh Team<br />";	
			 $headers  = 'MIME-Version: 1.0' . "\r\n";
			 $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			 if (mail($to, $subject, $body,$headers)) {
			   echo("<p>Message successfully sent! Please wait system redirecting to the home page</p>");
				?>
				<script type="text/javascript">
					setTimeout("Redirect()", 5000);
					function Redirect(){
						window.location.href = 'index.php';
					}
				</script>
				<?php
			 } 
			 else {
				echo("<p>Message delivery failed...Please wait system redirecting to the home page</p>");
				?>
				<script type="text/javascript">
					setTimeout("Redirect()", 5000);
					function Redirect(){
						window.location.href = 'index.php';
					}
				</script>
				<?php
			 }
			
		}
	}
?>
</body>
</html>
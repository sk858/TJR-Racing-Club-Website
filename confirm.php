<?php session_start(); ?>

<!--
This page sets the con_code field of a user to "" in order to make sure that the account has been activated by the admin.
Users are only able to login when the con_code if their account is "".

This page also sends an email to the user when their account has been activated by the admin, so that the users knows that they can now use the website.
-->

<!DOCTYPE html>
<html>
	<head>
		<?php include 'includes/head.php' ?>
	</head>
	<body>
		<?php include 'includes/navbar.php' ?>

		<?php
			
				require_once 'includes/config.php';
				$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
				if( $mysqli->connect_errno ) {
					echo "<p>$mysqli->connect_error<p>";
					die( "Couldn't connect to database");
				}

				echo "<a href='login.php'>Log In</a>";

				//Getting the con_code and email from a user from the URL
				if(isset($_GET['con_code'])){
					$con_code = $_GET['con_code'];
				}
				if(isset($_GET['email'])){
					$email = $_GET['email'];
				}
				
				if(isset($email)){
				//Setting the con_code of the user with this $email to ""
					$stmt = $mysqli->prepare(" UPDATE users SET con_code = ? WHERE email = ?;");
					$emptystring = '';
					$stmt->bind_param('ss', $emptystring, $email);
					$stmt->execute();
				}
				//Seeing if the account was successfully activated...
				if(isset($stmt)) {
					//Telling the admin that they successfully activated the account
					echo "The account has been activated!";

					//Sending an email to the user that tells them that their account was activated
					$to = "$email";
						$subject = "Your Account for the TJU Racing Team site has been activated";
						$header = 'Content-type: text/html; charset=utf-8'. "\r\n";
						$header .= "From: wangda@tjuracing.com";
						$message = "Your Account for the TJU Racing Team site has been activated! You can now login into the site. \n";

						$mail = mail($to,$subject,$message,$header);
						
				//Telling the admin that the activation was not successfull
				} else {
					echo "The account could not be activated.";
				}
			
		?>
	</body>
</html>
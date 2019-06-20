<!--
This page asks the user for their email and their answer to the security question. 
If they match the ones on the databse, an email with a link to reset their password is sent to the user.
-->
<?php session_start(); ?>
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

			echo "<a href='login.php'>Login</a>";

		?>

		<!--Form that asks user for their email and answer to the security question-->
		<form action="forgotPassword.php" method="post">
			<p>
				<label for="email">Email:</label>
			  	<input name="email" type="text" id="email">
			</p>
			<p>
				<label for="question">Security Question: What was the name of your favorite teacher?</label>
			  	<input name="question" type="text" id="question">
			</p>
			<p>
				<input name="submit" type="submit" value="Submit"/>
		 	</p>
		</form>

		<?php

		//If submit was cliked...
		if(isset($_POST['submit'])) {

			//Sanitizing $question and $email
			$question = filter_input( INPUT_POST, 'question', FILTER_SANITIZE_STRING );
			$email = filter_input( INPUT_POST, 'email', FILTER_VALIDATE_EMAIL );

			//If no answer to the question was provided, create an error and echo it
			if($question == '') { 
				$_SESSION['error']['question'] = "Answer to the question is required.";
				echo $_SESSION['error']['question'];
				exit;
			}

			//If no email was provided, create an error and echo it
			if($email == '') { 
				$_SESSION['error']['email'] = "Email is required.";
				echo $_SESSION['error']['email'];
				exit;
			//If an email was provided...
			} else {
				//Checking if inputted email is a valid email
				if(preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9._-]+)+$/", $_POST['email'])) {
					//Checking if this email already corresponds to a user in the database
					$sqlemail = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
					$sqlemail->bind_param('s', $email);
					$sqlemail->execute() or die(mysqli_error());
					$resultemail = $sqlemail->get_result();
				   	if ($resultemail && count($resultemail) < 1) {
				    	$_SESSION['error']['email'] = "This email does not exist on this site.";
				    	echo $_SESSION['error']['email'];
						
						exit;
				   	}
				//If email is not valid because it's not in the databse or does not match the regular expression, create an error and echo it
				} else {
					$_SESSION['error']['email'] = "This email is not valid. Try again.";
					echo $_SESSION['error']['email'];
					exit;
				}
			}

			//Getting all the fields from user with this email
			$sqlemail2 = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
			$sqlemail2->bind_param('s', $email);
			$sqlemail2->execute() or die(mysqli_error());
			$resultemail2 = $sqlemail2->get_result();
			//If there is only 1 instance of this email in the database...
			if($resultemail2 && count($resultemail2) == 1 && $row = $resultemail2->fetch_assoc()) {
				//If inputted answer to the question matched the one on the databse...
				if($row['question'] === $question){
					//If con_code is ""...
					if($row['con_code'] === ''){
						//Send an email to the user with a link to reset their password, with their email in the URL
						$to = $email;
						$subject = "Reset password of your the TJU Racing Team site";
						$header = 'Content-type: text/html; charset=utf-8'. "\r\n";
						$header .= "From: tjuracing@gmail.com";
						$message = "Click the link in order to reset your password: \n";
						$message .= "<a href='https://info2300.coecis.cornell.edu/users/fp_thunder/www/FP/resetPassword.php?email=$email'>Click Here</a>"; 

						$mail = mail($to,$subject,$message,$header);

						//If email was sent, echo a message saying it was sent
						if($mail) {
							echo "A link to reset your password has been sent to your email address.";
						//If email was not sent, echo a message saying it was not sent
						} else {
						 	echo "Cannot send link to reset your password to your email address.";
						}
					//If con_code was not "", do not let them reset their password, and echo a message saying that their account needs to be activated first
					} else {
						echo "Your account needs to be activated before you are able to change your password.";
					}
				//If answer to the security question does not match the one on the database, echo a message saying that.
				} else {
					echo "The answer to the security question was incorrect.";
				}
			}
		}

	?>
	</body>
</html>
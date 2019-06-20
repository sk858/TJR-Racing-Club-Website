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

			echo "<a href='login.php'>Login</a>;";
		?>

		<!--Form that asks the user for their email and answer to the security question-->
		<form action="forgotUsername.php" method="post">
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

		//If submit button was clicked..
		if(isset($_POST['submit'])) {
			//Sanitizing and validating email and answer
			$question = filter_input( INPUT_POST, 'question', FILTER_SANITIZE_STRING );
			$email = filter_input( INPUT_POST, 'email', FILTER_VALIDATE_EMAIL );

			//Checking if email or password was empty
			if($question == '') { 
				$_SESSION['error']['question'] = "Answer to the question is required.";
				echo $_SESSION['error']['question'];
				exit;
			}

			if($email == '') { 
				$_SESSION['error']['email'] = "Email is required.";
				echo $_SESSION['error']['email'];
				exit;
			//Checking if inputted email is a valid email (mathces the regular expression and there is only 1 in the database). If there is an error, echo it
			} else {
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
				} else {
					$_SESSION['error']['email'] = "This email is not valid. Try again.";
					echo $_SESSION['error']['email'];
					exit;
				}
			}

			//If email was valid...
			$sqlemail2 = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
			$sqlemail2->bind_param('s', $email);
			$sqlemail2->execute() or die(mysqli_error());
			$resultemail2 = $sqlemail2->get_result();
			//If there is only 1 instance of this email in the database...
			if($resultemail2 && count($resultemail2) == 1 && $row = $resultemail2->fetch_assoc()) {
				//Check if inputted answer mathced the one in the database
				if($row['question'] === $question){
					//Check if con_code was empty
					if($row['con_code'] === ''){
						//Send an email to the user containing their username
						$to = $email;
						$subject = "Username for the TJU Racing Team site";
						$header = 'Content-type: text/html; charset=utf-8'. "\r\n";
						$header .= "From: tjuracing@gmail.com";
						$message = "Your username in the TJU Racing Team site is: ".$row['username'];

						$mail = mail($to,$subject,$message,$header);

						//If email was sent, echo that
						if($mail) {
							echo "Your username has been sent to your email address.";
						//If email was not sent, echo that
						} else {
						 	echo "Cannot send your username to your email address.";
						}
					//If account is not active, tell the user that their account needs to be activated before you are able to ask for your username
					} else {
						echo "Your account needs to be activated before you are able to ask for your username.";
					}
				//If answer to the security question was not correct, echo that
				} else {
					echo "The answer to the security question was incorrect.";
				}
			}
		}

	?>
	</body>
</html>
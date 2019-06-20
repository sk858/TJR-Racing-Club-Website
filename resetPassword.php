<?php session_start(); ?>
<!DOCTYPE html>
<html>
	<head>
		<?php include 'includes/head.php' ?>
	</head>
	<body>
		<?php include 'includes/navbar.php' ?>
		
		<?php
		//Getting email from the URL
		if(isset($_GET['email'])){
			$email = $_GET['email'];
		
			?>

			<!--Form that asks the user for their new password and for them to retype it-->
			<form action="resetPassword.php?email=<?php print($email) ?>" method="post">
				<p>
					<label for="password1">New Password:</label>
				  	<input name="password1" type="password" id="password1">
				</p>
				<p>
					<label for="password2">Retype New Password:</label>
				  	<input name="password2" type="password" id="password2">
				</p>
				<p>
					<input name="submit" type="submit" value="Submit"/>
			 	</p>
			</form>

			<?php
				require_once 'includes/config.php';
				$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
				if( $mysqli->connect_errno ) {
					echo "<p>$mysqli->connect_error<p>";
					die( "Couldn't connect to database");
				}

				if(isset($_POST['submit'])) {

					//Sanitize the passwords
					$password1 = filter_input( INPUT_POST, 'password1', FILTER_SANITIZE_STRING );
					$password2 = filter_input( INPUT_POST, 'password2', FILTER_SANITIZE_STRING );
					
					//Check if passwords are identical and if they are, hash the password
					if($password1 === $password2){
						$password = $_POST['password1'];
						//Checking if password is 8 characters long and contains at least 1 special character
						if(mb_strlen($password) < 8 || !preg_match('/[^0-9A-Za-z]/', $password)) {
							$_SESSION['error']['password'] = "Password must contain at least 8 characters and at least one special character.";
							echo $_SESSION['error']['password'];
						}
						//Hashing the password
						$hashpassword = password_hash($password, PASSWORD_DEFAULT);

						//Update users table with the new password for this user
						$sql = $mysqli->prepare(" UPDATE users SET hashpassword = ? WHERE email = ? ");
						$sql->bind_param('ss', $hashpassword, $email);
						$sql->execute();

						//If table was updatted, send an email to the user with their new password
						if($sql) {
							echo "Your password has been reset! You can now <a href='login.php'>Log In</a>.";
							$to = $email;
							$subject = "Your password has been reset!";
							$header = 'Content-type: text/html; charset=utf-8'. "\r\n";
							$header .= "From: tjuracing@gmail.com";
							$message = "Your password has been reset. Your new password is: \n";
							$message .= "$password";

							$mail = mail($to,$subject,$message,$header);

						//If password could not be updatted, echo that
						} else {
							echo "Your password could not be reset.";
						}
					//If passwords were not identical, echo that
					} else { echo "The passwords do not match!";}
				}
		}
		?>

	</body>
</html>
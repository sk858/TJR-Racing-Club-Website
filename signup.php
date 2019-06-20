<?php session_start();?>
<!DOCTYPE html>
<html>
	<head>
		<?php include 'includes/head.php' ?>
		<title>Sign Up</title>
	</head>
	<body>

		<?php include 'includes/navbar.php' ?>

		<!--Form that asks the user for First Name, Last Name, username, email, password, retypeing their password, and answer to the security question-->
		<div class="space-buffer"></div>

		<div class="container narrow">
			
		<div class="signup card">
				<div class="t-c">
					<h2>Sign up</h2>
				</div>
				<hr>
			<form action="signup.php" method="post">
				<div class="form-group">
					<label for="firstName">First Name</label>
		 			<input name="firstName" type="text" id="firstName" class="form-control">
		 		</div>
				<div class="form-group">
					<label for="lastName">Last Name</label>
		 			<input name="lastName" type="text" id="lastName" class="form-control">
		 		</div>
				<div class="form-group">
					<label for="username">Username</label>
		 			<input name="username" type="text" id="username" class="form-control">
		 		</div>
				<div class="form-group">
					<label for="email">Email</label>
		  			<input name="email" type="text" id="email" class="form-control">
		 		</div>
				<div class="form-group">
					<label for="password">Password</label>
		  			<input name="password" type="password" id="password" class="form-control">
		 		</div>
				<div class="form-group">
		  			<label for="password2">Confirm Password</label>
		  			<input name="password2" type="password" id="password2" class="form-control">
		 		</div>
				<div class="form-group">
		  			<label for="question">Security Question: What was the name of your favorite teacher?</label>
		  			<input name="question" type="text" id="question" class="form-control">
		 		</div>
				<div class="form-group">
	  				<input name="submit" type="submit" value="Submit" class="btn primary block" />
		 		</div>
	  		</form>
	  		<hr>
			<?php
			//If user is still logged in, ask them to logout
			if (isset($_SESSION['logged_user_by_sql'])) {
				print('Log Out before creating a new account');
				print("<a class='logout' href='logout.php'>Click here to Logout</a>");
			//If user is logged out...
			}else {
				//If there is an error, clear the error
				if(isset($_SESSION['error'])) {
					unset($_SESSION['error']);
				}
				print("<a href='login.php'>Log In</a>");
				require_once 'includes/config.php';
				$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
				if( $mysqli->connect_errno ) {
					echo "<p>$mysqli->connect_error<p>";
					die( "Couldn't connect to database");
				}
			}
			?>
	  	</div>


		</div>

	<?php
		//If submit button was clicked...
		if(isset($_POST['submit'])) {
			//Sanitize and validate all form inputs
			$username = filter_input( INPUT_POST, 'username', FILTER_SANITIZE_STRING );
			$firstName = filter_input( INPUT_POST, 'firstName', FILTER_SANITIZE_STRING );
			$lastName = filter_input( INPUT_POST, 'lastName', FILTER_SANITIZE_STRING );
			$email = filter_input( INPUT_POST, 'email', FILTER_VALIDATE_EMAIL );
			$password = filter_input( INPUT_POST, 'password', FILTER_SANITIZE_STRING );
			$password2 = filter_input( INPUT_POST, 'password2', FILTER_SANITIZE_STRING );
			$question = filter_input( INPUT_POST, 'question', FILTER_SANITIZE_STRING );
			//Determining if any of the fields were empty

			if($firstName == '') { 
				$_SESSION['error']['firstName'] = "First Name is required.";
				echo $_SESSION['error']['firstName'];
				exit;
			}
			if($lastName == '') { 
				$_SESSION['error']['lastName'] = "Last Name is required.";
				echo $_SESSION['error']['lastName'];
				exit;
			}
			if($question == '') { 
				$_SESSION['error']['question'] = "Answer to the question is required.";
				echo $_SESSION['error']['question'];
				exit;
			}
			
			if($email == '') { 
				$_SESSION['error']['email'] = "Email is required.";
				echo $_SESSION['error']['email'];
				exit;
			//Checking if inputted email is a valid email (mathced the regular expression and is not in the databse yet). Echo any errors.
			} else {
				if(preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9._-]+)+$/", $_POST['email'])) {
					//Checking if this email already corresponds to a user in the database
					$sqlemail = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
					$sqlemail->bind_param('s', $email);
					$sqlemail->execute() or die(mysqli_error());
					$resultemail = $sqlemail->fetch();
				   	if ($resultemail && count($resultemail) > 0) {
				    	$_SESSION['error']['email'] = "This email is already in use.";
				    	echo $_SESSION['error']['email'];
						exit;
				   	}
				} else {
					$_SESSION['error']['email'] = "This email is not valid. Try again.";
					echo $_SESSION['error']['email'];
					exit;
				}
			}
			if($username == '') { 
				$_SESSION['error']['username'] = "Username is required.";
				echo $_SESSION['error']['username'];
				exit;
			}else {
				//Checking if username is already in the databse
				$sqlusername = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
				$sqlusername->bind_param('s', $username);
				$sqlusername->execute() or die(mysqli_error());
				$resultusername = $sqlusername->fetch();
				if ($resultusername && count($resultusername) > 0) {
				  	$_SESSION['error']['username'] = "This username is already in use.";
				    echo $_SESSION['error']['username'];
					exit;
				}
			}
			//Checking if any of the passwords is blank
			if($password == '') { $_SESSION['error']['password'] = "Password is required.";}
			//Checking if password is 8 characters long and contains at least 1 special character
			if(mb_strlen($password) < 8 || !preg_match('/[^0-9A-Za-z]/', $password)) {
				$_SESSION['error']['password'] = "Password must contain at least 8 characters and at least one special character.";
				echo $_SESSION['error']['password'];
			}
			if($password2 == '') { 
				$_SESSION['error']['password2'] = "Password Confirmation is required."; 
				echo $_SESSION['error']['password'];
			}
			//Checking of the passwords match each other
			if($password !== $password2){
				$_SESSION['error']['password2'] = "Password Confirmation does not match the Password."; 
				echo $_SESSION['error']['password'];
			}
			//If there was any error, exit...
			if(isset($_SESSION['error'])) {
				exit;
			//If there were no errors...
			} else {
				//Create a unique random con_code
				$con_code = uniqid(rand());
				//Hash the inputted password
				$hashpassword = password_hash($password, PASSWORD_DEFAULT);
				//Insert all fields into the "users" table
				$sql = $mysqli->prepare(" INSERT INTO users (username, email, firstName, lastName, hashpassword, question, con_code) VALUES (?, ?, ?, ?, ?, ?, ?); ");
				$sql->bind_param('sssssss', $username, $email, $firstName, $lastName, $hashpassword, $question, $con_code);
				$sql->execute();
				$result = $sql->get_result();
				//If information was inserted into "users"...
				if($sql) {
					//Send an email to the admin (containing the name of the user) with a link to activate the users account. The URL contains the user's email and con_code
					$to = "paulamoyani@gmail.com"; //Once the site is uploaded use: wangda@tjuracing.com
					$subject = "Confirm account for the TJU Racing Team site";
					$header = 'Content-type: text/html; charset=utf-8'. "\r\n";
					$header .= "From: wangda@tjuracing.com";
					$message = "Click the link in order to activate the account of ".$firstName." ".$lastName.": \n";
					$message .= "<a href='https://info2300.coecis.cornell.edu/users/fp_thunder/www/FP/confirm.php?con_code=$con_code&email=$email'>Click Here</a>"; 
					$mail = mail($to,$subject,$message,$header);
					//If email was sent, echo that it was sent and account is pending for confirmation
					if($mail) {
						echo "Your signup request has been recorded. A member of the team must approve your account before you are able to login.";
					//If email was not sent, echo that in a message.
					} else {
					 	echo "Cannot send your signup request to a team member for their approval.";
					}
				}
			}
		}
	?>

		<?php include 'includes/footer.php' ?>

	</body>
</html>

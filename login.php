<?php session_start(); ?>
<!DOCTYPE html>
<html>
	<head>
		<?php include 'includes/head.php' ?>
	</head>
	<body>
		<?php include 'includes/navbar.php' ?>

		<?php

		// Sanitizing username and password
		$post_username = filter_input( INPUT_POST, 'username', FILTER_SANITIZE_STRING );
		$post_password = filter_input( INPUT_POST, 'password', FILTER_SANITIZE_STRING );

		// If the login fields were not filled in
		if ( empty( $post_username ) || empty( $post_password ) ) {
		?>

		<div class="space-buffer"></div>

		<div class="container narrow">
			
			<div class="loginDiv card">
				<div class="t-c">
					<h2>Log in</h2>
				</div>
				<hr>

				<!--Form that asks the user for their username and password-->
				<form action="login.php" method="post">
					<div class="form-group">
						<label>Username</label>
						<input type="text" name="username" class="form-control"> 
					</div>
					<div class="form-group">
						<label>Password</label>
						<input type="password" name="password" class="form-control"> 
					</div>
					<div class="form-group">
						<input type="submit" value="Submit" class="btn primary block">
					</div>
				</form>

				<hr>

				<!--Links for if the user forgot their username, or password, and if the user wants to create an account(sing up)-->
				<a class="signup" href="signup.php">Sign Up</a><br>
				<a class="forgotUsername" href="forgotUsername.php">Forgot My Username</a><br>
				<a class="forgotPassword" href="forgotPassword.php">Forgot My Password</a> 
			</div>


		</div>

		<?php

		//If the login fields were filled in
		} else {
			require_once 'includes/config.php';
			$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			if( $mysqli->connect_errno ) {
				echo "<p>$mysqli->connect_error<p>";
				die( "Couldn't connect to database");
			}

			//Get info from user with this username if the con_code is "" (account was activated)
			$stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ? AND con_code = ?;");

			$emptystring = '';
			$stmt->bind_param('ss', $post_username, $emptystring);
			$stmt->execute();
			$result = $stmt->get_result();
			$num_rows = $stmt->fetch();

			//If there is only 1 activated account with this username...
			if ( $result && count($num_rows) == 1) {

				$row = $result->fetch_assoc();
				
				//Check that the password (hashed) matches the hashpassword of the account
				$db_hash_password = $row['hashpassword'];
				
				//If password is correct, activate session
				if( password_verify( $post_password, $db_hash_password ) ) {
					$db_username = $row['username'];
					$_SESSION['logged_user_by_sql'] = $db_username;
				}
			} 
			$mysqli->close();

			//If login was successful, welcome the user
			if ( isset($_SESSION['logged_user_by_sql'] ) ) {
				// Redirect user to their previous page
				header("Location: " . "index.php");
				exit;
			//If login was unsuccessful, tell the user that the login was unsuccessful
			} else {
				echo '<div class="space-buffer"></div>';
				echo '<div class="container narrow">';
				echo '<p>You did not login successfully.</p>';
				echo '<p>Please <a href="login.php">try again</a>.</p>';
				echo '</div>';
			}
		}

		?>


		<?php include 'includes/footer.php' ?>
		
	</body>
</html>
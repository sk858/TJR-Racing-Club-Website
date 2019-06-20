<?php
if(isset($_SESSION['logged_user_by_sql'])){

    require_once 'includes/config.php';
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($mysqli->errno) {
       print($mysqli->error);
       exit();
	}

    //Getting name of the user
    $username = $_SESSION['logged_user_by_sql'];
    
    //Getting information of this specific user
    $sql = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
	$sql->bind_param('s', $username);
	$sql->execute() or die(mysqli_error());
	$result = $sql->get_result();
	$row = $result->fetch_assoc();

	$email = $row['email'];
	$firstName = $row['firstName'];
	$lastName = $row['lastName'];
	$hashpassword = $row['hashpassword'];
	$question = $row['question'];

?>

<!--Form for upadting fields of this specific user-->
<div class="card">
	<h3>Update Account Details</h3>
	<form method="post" enctype="multipart/form-data">
		<div class="form-group">
			<label>Username:</label>
			<input type="text" name="username" value="<?php echo $username; ?>" class="form-control">
		</div>
		<div class="form-group">
			<label>Email:</label>
			<input type="text" name="email" value="<?php echo $email; ?>" class="form-control">
		</div>
		<div class="form-group">
			<label>First Name:</label>
			<input type ="text" name="firstName" value="<?php echo $firstName; ?>" class="form-control">
		</div>
		<div class="form-group">
			<label>Last Name:</label>
			<input type ="text" name="lastName" value="<?php echo $lastName; ?>" class="form-control">
		</div>
		<div class="form-group">
			<label>Security Question. What was the name of your favorite teacher?</label>
			<input type ="text" name="question" value="<?php echo $question; ?>" class="form-control">
		</div>
		<div class="form-group">
			<label>Password:</label>
			<input type ="password" name="password1" class="form-control">
		</div>
		<div class="form-group">
			<label>If you are changing the password, retype the password:</label>
			<input type ="password" name="password2" class="form-control">
		</div>
		<div class="form-group">
			<input type="submit" name="submit" value="Submit Changes" onclick="return confirm('Are you sure you want to submit these changes?')" class="btn primary">
		</div>
		<div class="form-group">
			<label> <b> Do you want to delete your account? </b> </label>
			<input type="submit" name="delete" value="Delete Account" onclick="return confirm('Are you sure you want to delete your account?')" class="btn primary">
		</div>
	</form>
</div>
	


<?php

	//If submit button was clicked...
	if(isset($_POST['submit'])) {
		//Sanitize and validate all form inputs
		$username_input = filter_input( INPUT_POST, 'username', FILTER_SANITIZE_STRING );
		$email_input = filter_input( INPUT_POST, 'email', FILTER_SANITIZE_STRING );
		$firstName_input = filter_input( INPUT_POST, 'firstName', FILTER_SANITIZE_STRING );
		$lastName_input = filter_input( INPUT_POST, 'lastName', FILTER_SANITIZE_STRING );
		$question_input = filter_input( INPUT_POST, 'question', FILTER_SANITIZE_STRING );
		$password1_input = filter_input( INPUT_POST, 'password1', FILTER_SANITIZE_STRING );
		$password2_input = filter_input( INPUT_POST, 'password2', FILTER_SANITIZE_STRING );


		//If there is a new username, check if valid. If valid, update "users" table.
		if($username_input !== $username & $username_input !== '') { 
			//Checking if username is already in the databse
			$sqlusername = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
			$sqlusername->bind_param('s', $username_input);
			$sqlusername->execute() or die(mysqli_error());
			$resultusername = $sqlusername->fetch();
			if ($resultusername && count($resultusername) > 0) {
			  	$_SESSION['error']['username'] = "This username is already in use.";
			    echo $_SESSION['error']['username'];
				exit;
			}
			$username = $username_input;
			$sqlusername_new = $mysqli->prepare(" UPDATE users SET username = ? WHERE email = "."'$email'"." ");
			$sqlusername_new->bind_param('s', $username_input);
			$sqlusername_new->execute();
			//Updating logged user in the site
			$_SESSION['logged_user_by_sql'] = "$username";
		}

		//If there is a new email, check if valid. If valid, update "users" table.
		if($email_input !== $email && $email_input !== '') { 
		//Checking if inputted email is a valid email (mathced the regular expression and is not in the databse yet). Echo any errors.
			if(preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9._-]+)+$/", $_POST['email'])) {
				//Checking if this email already corresponds to a user in the database
				$sqlemail = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
				$sqlemail->bind_param('s', $email_input);
				$sqlemail->execute() or die(mysqli_error());
				$resultemail = $sqlemail->fetch();
			   	if ($resultemail && count($resultemail) > 0) {
			    	$_SESSION['error']['email'] = "This email is already in use.";
			    	echo $_SESSION['error']['email'];
					exit;
			   	}
			   	$email = $email_input;
			   	$sqlemail_new = $mysqli->prepare(" UPDATE users SET email = ? WHERE username = "."'$username'"." ");
				$sqlemail_new->bind_param('s', $email);
				$sqlemail_new->execute();
			} else {
				$_SESSION['error']['email'] = "This email is not valid. Try again.";
				echo $_SESSION['error']['email'];
				exit;
			}
		}

		//If there is a new firstName, update "users" table.
		if($firstName_input !== $firstName && $firstName_input !== ''){
			$firstName = $firstName_input;
			$sqlFName_new = $mysqli->prepare(" UPDATE users SET firstName = ? WHERE username = "."'$username'"." ");
			$sqlFName_new->bind_param('s', $firstName);
			$sqlFName_new->execute();
		}

		//If there is a new lastName, update "users" table.
		if($lastName_input !== $lastName && $lastName_input !== ''){
			$lastName = $lastName_input;
			$sqlLName_new = $mysqli->prepare(" UPDATE users SET lastName = ? WHERE username = "."'$username'"." ");
			$sqlLName_new->bind_param('s', $lastName);
			$sqlLName_new->execute();
		}

		//If there is a new security question answer, update "users" table.
		if($question_input !== $question && $question_input !== ''){
			$question = $question_input;
			$question_new = $mysqli->prepare(" UPDATE users SET question = ? WHERE username = "."'$username'"." ");
			$question_new->bind_param('s', $question);
			$question_new->execute();
		}

		//If there is a new password, update "users" table.
		if($password1_input !== '' && $password2_input !== ''){
			if($password1_input === $password2_input){
				//Checking if password is 8 characters long and contains at least 1 special character
				if(mb_strlen($password1_input) < 8 || !preg_match('/[^0-9A-Za-z]/', $password1_input)) {
					$_SESSION['error']['password'] = "Password must contain at least 8 characters and at least one special character.";
					echo $_SESSION['error']['password'];
				}
				$password = $password1_input;
				$hashpassword = password_hash($password, PASSWORD_DEFAULT);
				$password_new = $mysqli->prepare(" UPDATE users SET hashpassword = ? WHERE username = "."'$username'"." ");
				$password_new->bind_param('s', $hashpassword);
				$password_new->execute();
			}
			else {
				$_SESSION['error']['password2'] = "Retyped password does not match the Password."; 
				echo $_SESSION['error']['password'];
			}
		}
		else if(($password1_input !== '' && $password2_input === '') || ($password1_input === '' && $password2_input !== '')) {
			$_SESSION['error']['password2'] = "You must retype the password before submitting."; 
			echo $_SESSION['error']['password'];
		}
		if(isset($_SESSION['error'])) {
				exit;
		//If there were no errors...
		} else {
			echo 'Your changes have been recorded! You can see your changes whenever you come back to this page.';

		}
	}
	//If user wants to delete their account
	if(isset($_POST['delete'])){
		//Delete user from database
		$stmtDelete = $mysqli->prepare("DELETE FROM users WHERE username = ?;");
		$stmtDelete->bind_param('s', $_SESSION['logged_user_by_sql']);
		$stmtDelete->execute();

		//Logout
		unset($_SESSION['logged_user_by_sql']);
		session_destroy();
		// Redirect user to their previous page
		header('Location: ' . $_SERVER['HTTP_REFERER']);
	}
?>
	

<?php
}
?>



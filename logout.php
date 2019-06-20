<?php
	// Log out the user
	session_start();
	unset($_SESSION['logged_user_by_sql']);
	session_destroy();
	// Redirect user to their previous page
	header('Location: ' . $_SERVER['HTTP_REFERER']);
	exit;
?>
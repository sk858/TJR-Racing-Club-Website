<?php
	// handles case when no currentPage is set
	if (!isset($currentPage)) { $currentPage = "none"; }
?>

<div id="navbar" class="navbar">
	<div class="container">
		<a href="index.php" class="no-decoration"><img src="images/logo.png" alt="TJI logo" class="logo"></a>
		<span id="menu-toggle">MENU</span>
		<div class="right-box">
			<ul>
				<li><a href="index.php" <?php if($currentPage == "home") { echo "class='current'"; } ?>>Home</a></li>
				<li><a href="about.php" <?php if($currentPage == "about") { echo "class='current'"; } ?>>About</a></li>
				<li><a href="history.php" <?php if($currentPage == "history") { echo "class='current'"; } ?>>History</a></li>
				<li><a href="gallery.php" <?php if($currentPage == "gallery") { echo "class='current'"; } ?>>Gallery</a></li>
				<li><a href="contact.php" <?php if($currentPage == "contact") { echo "class='current'"; } ?>>Contact</a></li>
				<?php if (isset($_SESSION['logged_user_by_sql'])) { ?>
				<li><a href="settings.php" <?php if($currentPage == "settings") { echo "class='current'"; } ?>>Settings</a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<?php
		// lets users know who they are logged in as
		if ( isset($_SESSION['logged_user_by_sql']) ) {
				echo "<div class='container alert'>";
				echo "<div class='inner'>";
				echo "Logged in as <b>" . $_SESSION['logged_user_by_sql'] . "</b>";
				echo "</div>";
				echo "</div>";
		}
	?>
</div>

<div class="space-buffer"></div>



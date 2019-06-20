<?php session_start(); ?>
<!DOCTYPE html>
<html>
	<head>
		<?php include 'includes/head.php' ?>
		<?php include('includes/edit_ajax.php'); ?>
	</head>
	<body>
		<?php
			$currentPage = "settings";
			include 'includes/navbar.php'
		?>

		<div class="space-buffer"></div>

		<?php
			if (isset($_SESSION['logged_user_by_sql'])) {
		?>
		<div class="container">

			<h1>Admin Panel Settings</h1>
			<p>This is where you can create, update, and destroy content from your website.</p>
			<hr>
			<div class="tabbed">
				<div class="tabs">
					<a id="section1-tab" href="#section1" data-target="section1">Gallery</a>
					<a id="section2-tab" href="#section2" data-target="section2">Events</a>
					<a id="section3-tab" href="#section3" data-target="section3">Account</a>
					<a id="section4-tab" href="#section4" data-target="section4">History</a>
				</div>

				<section id="section1">
					<?php include('includes/update_gallery.php'); ?>
				</section>
				<section id="section2">
					<?php include('includes/edit_event.php'); ?>
				</section>
				<section id="section3">
					<?php include('includes/update_account.php'); ?>
				</section>
				<section id="section4">
					<?php include('includes/update_history.php'); ?>
				</section>
			</div>
		</div>
		<?php
		} else {
			echo "<div class='container'>";
			echo "<h1>You must be logged in to view this page</h1>";
			echo "</div>";
		}
		?>

		<?php include 'includes/footer.php' ?>

		<script
		src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
  	integrity="sha256-k2WSCIexGzOj3Euiig+TlR8gA0EmPjuc79OEeY5L45g="
  	crossorigin="anonymous"></script>
  		<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>

		<script type="text/javascript">
			var tabs = $(".tabs a");
			var currentTab = window.location.hash;
			// sets the last tab after submitting
			$(".tabbed section").css("display", "none");
			if (currentTab) {
				tabs.each( function() {
					console.log($(this).attr("href"));
					if ($(this).attr("href") == currentTab) {
						$(currentTab).css("display", "block");
						$(currentTab + "-tab").eq(0).addClass("active");
						$(currentTab).addClass("active");
					}
				});
			} else {
				$(".tabbed section").eq(0).css("display", "block");
				$("#section1-tab").eq(0).addClass("active");
				$("#section1").addClass("active");
			}

			$(".tabs a").on("click", function() {
				$(".tabs a").removeClass("active")
				$(this).addClass("active");

				var target = $(this).data("target");
				$(".tabbed section").css("display", "none");
				$("#" + target).css("display", "block");
			});
		</script>

	</body>
</html>
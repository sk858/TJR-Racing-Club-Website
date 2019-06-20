<?php session_start(); ?>

<!DOCTYPE HTML>
<html>
	<head>
		<?php include 'includes/head.php' ?>
	</head>
	<body>
		<?php
			$currentPage = "home";
			include 'includes/navbar.php';
		?>

		<div id="hero" class="dim container edge bg-image bg-image-racecar">
		<div class="container t-c">

			<h1>TJU Racing</h1>

		</div>
		</div>


		<?php
			function renderEvents() {
				$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
				if ($mysqli->errno) {
						print($mysqli->error);
						exit();
				}
				$events_load= $mysqli->prepare("SELECT * FROM `events` WHERE (`start` >= CURRENT_TIMESTAMP) ORDER BY `start` ASC") ;
				$events_load ->execute();
				$result = $events_load->get_result();
				if( $events_load ) {
					while($row = $result->fetch_assoc()) {
						echo "<div class='schedule card'>";
						echo "<div class='header'>";
							echo "<h3 class='event-name'>{$row['event_name']}</h3>";
							echo "<p class='event-details'>{$row['start']} @ {$row['location']}</p>";
						echo "</div>";
						echo "<p class='event-description'>{$row['detail']}</p>";
						echo "</div>";
					}
				}

			}
		?>

		<div id="sponsorship-banner" class="container edge bg-light-gray t-c">
			<h1>Interested in sponsoring our team?</h1>
			<a href="contact.php" class="btn primary">Contact us about sponsorship</a>
		</div>

		<div class="container content static-1">

					<p>TJU Racing Team is a non-commercial formula racing team. It is mainly funded by the college and sponsors. We design and produce one new racing car each year and take part in the FSAE competitions. Besides, our team is a student organization with full independence; we have our own management system and operate independently. Our team has won public focus during these years. We' ve received interviews from the state-run CCTV, sina.com and many other media. Every year, we will be invited to visit and participate in various of exhibitions and activities.</p>

					<p>With the power and resources of our school , more importantly with the great support of our dear professors (Special thanks to Mr. Li Liguang), the TJU Racing Team will keep on chasing our dreams and fighting to be the champion within China and establish a leading position in the worldwide competition.</p>
		</div>

		<div class="container edge schedule-wrapper bg-light-gray">
			<div class="t-c">
				<h1>Upcoming Events</h1>
			</div>
			<div class="schedule-container">
			<?php
				// shows the schedule of events
				require_once 'includes/config.php';
				$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
				if ($mysqli->errno) {
						echo($mysqli->error);
						exit();
				}

				if(isset($_SESSION['logged_user_by_sql'])) {

					$events_load = "SELECT * FROM `events`";
					$events_load_query = $mysqli->query($events_load);

					if($events_load_query) {
				?>
				<?php
					renderEvents();
					if(isset($_POST['add_event'])){
						$name = strip_tags($_POST['event_title']);
						$loc = strip_tags($_POST['event_location']);
						$time = strip_tags($_POST['start']);
						$pieces = explode("T", $time);
						$new_time = $pieces[0] . ' ' . $pieces[1] . ":00";
						$add_events="INSERT INTO events (event_name, location, start)
		                      VALUES ('$name', '$loc', '$new_time');";
						$add_events_query = $mysqli ->query($add_events);
					}
				}
				} else {
					renderEvents();
				}
			 ?>
			</div>
		</div>

		<?php include 'includes/footer.php' ?>


	</body>
</html>

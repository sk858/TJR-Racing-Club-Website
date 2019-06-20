<?php
if(isset($_SESSION['logged_user_by_sql'])){

    require_once 'includes/config.php';
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($mysqli->errno) {
       print($mysqli->error);
        exit();
    }
?>

<?php
	$msg ="";
    $error = false;
    if(isset($_POST['add_event'])){
    	$name = filter_input( INPUT_POST, 'event_title', FILTER_SANITIZE_STRING );
    	$loc = filter_input( INPUT_POST, 'event_location', FILTER_SANITIZE_STRING );
    	$time = filter_input( INPUT_POST, 'start', FILTER_SANITIZE_STRING );
    	$des = filter_input( INPUT_POST, 'detail', FILTER_SANITIZE_STRING );
    	if(!empty($name) && !empty($loc) && !empty($time) && !empty($des) && strlen($des)>0){

    		$pieces = explode("T", $time);
			$new_time = $pieces[0] . ' ' . $pieces[1] . ":00";
			$add_events= $mysqli->prepare("INSERT INTO events (event_name, location, start, detail) VALUES (?, ?, ?, ?); ") ;
			$add_events->bind_param('ssss', $name, $loc, $new_time, $des);
			$add_events ->execute();
			
			if( !empty($add_events) ){
				$msg .= 'Event Saved';
			}else{
				$msg .= 'Event Exists';
			}
		}
		else{
		$msg .= 'Incomplete Event Information';

		}
		echo "<div><p>$msg</p><div>";
	}

	
?>

<div class="card">
	<h3>Add Event</h3>
	<form action="settings.php#section2" method="post" enctype="multipart/form-data">
		<div class="form-group">
			<label>Event Title:</label>
			<input type="text" name="event_title" class="form-control">
		</div>
		<div class="form-group">
			<label>Location:</label>
			<input type="text" name="event_location" class="form-control">
		</div>
		<div class="form-group">
			<label>Start Time:</label>
			<input type ="datetime-local" name="start" class="form-control">
		</div>
		<div class="form-group">
			<label>Details:</label>
			<input type ="text" name="detail" class="form-control">
		</div>
		<div class="form-group">
			<input type="submit" name="add_event" class="btn primary" value="add new event">
		</div>
	</form>
</div>

<?
	$msg ="";
    $error = false;
    if(isset($_POST['delete_submit'])){
    	$del_name = filter_input( INPUT_POST, 'del_event', FILTER_SANITIZE_STRING );
    	if(!empty($del_name)){
			$del_events= $mysqli->prepare("DELETE FROM events WHERE event_id = ?") ;
			$del_events->bind_param('i', $del_name);
			$del_events ->execute();
			
			if( !empty($del_events) ){
				$msg .= 'Event Deleted';
			}
		}
		else{
		$msg .= 'Choose an Event to Delete';

		}
		echo "<div><p>$msg</p><div>";
	}

?>
<div class="card">
	<h3>Delete Event</h3>
	<form action="settings.php#section2" method="post" enctype="multipart/form-data">
		<div class="form-group">
			<label>Event Title:</label>
			<?php
				$events_load= $mysqli->prepare("SELECT * FROM `events` WHERE (`start` >= CURRENT_TIMESTAMP) ORDER BY `start` ASC") ;
				$events_load ->execute();
				$result = $events_load->get_result();
				echo'<select name="del_event"><option value="">Delete an event</option>';
				while ($row = $result -> fetch_assoc()){
					$title = $row['event_name'];
					$delete_event_id = $row['event_id'];
					print("<option value='".$delete_event_id."'>$title</option>");
					}
				print("</select>");
			?>
		</div>
		<div class="form-group">
			<input type="submit" name="delete_submit" class="btn primary" value="delete an event">
		</div>
	</form>
</div>
<?php
}
?>
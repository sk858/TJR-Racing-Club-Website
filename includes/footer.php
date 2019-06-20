<div class="space-buffer"></div>

<div class="footer container edge">
	<div class="container">
			<hr>
			<p>&copy; TJU Racing 2017</p>

			<?php 
				if ( isset($_SESSION['logged_user_by_sql']) ) {
					echo "<a href='logout.php'>Log Out</a>";
				} else {
					echo "<a href='login.php'>Log In</a>";
				}
			?>
	</div>
</div>

<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script type="text/javascript">
  $.noConflict();
  jQuery(document).ready(function($) {
	$("#menu-toggle").on("click", function(){
		if ($("#navbar").hasClass("open")) {
			$("#navbar").removeClass("open");
		} else {
			$("#navbar").addClass("open");
		}
	});
  });
</script>
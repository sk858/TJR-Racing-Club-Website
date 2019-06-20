<?php session_start(); ?>
<!DOCTYPE html>
<html>
	<head>
		<?php include 'includes/head.php' ?>
	</head>
	<body>
		<?php
			$currentPage = "contact";
			include 'includes/navbar.php'
		?>

		<div class="space-buffer"></div>
		<div class="container">

			<?php
				require_once 'includes/config.php';
				
				include_once './securimage/securimage.php';
				$securimage = new Securimage();

				if( isset($_POST['email_from']) && !empty($_POST['email_from'])
				&& isset($_POST['email_message']) && !empty($_POST['email_message']) 
				&& isset($_POST['captcha_code']) && !empty($_POST['captcha_code']) && ($securimage->check($_POST['captcha_code'])) ) {
					
					// validates inputs TODO: beef these up
					$email_from = htmlentities($_POST['email_from']);
					$email_message = htmlentities($_POST['email_message']);
					echo "<h2>Message successfully sent!</h2>";

					//$to = "wangda@tjuracing.com"; TODO: uncomment in final version
					$to = "sv295@cornell.edu"; // TODO: remove from final version
					$subject = "Message from website";
					$header = 'Content-type: text/html; charset=utf-8'. "\r\n";
					$header .= "From: ".$email_from; // change
					$message = "A message here";

					$mail = mail($to,$subject,$message,$header);

				} else {
				?>
					<div class="row">
						<div class="col-md-7">
							<div class="t-c spaced">
								<h1>Interested in sponsoring our team?</h1>
								<h2>Get in touch with us below</h2>
							</div>
							<form action="contact.php" method="post" class="form-narrow">
								<div class="form-group">
									<label>Your email</label>
									<input type="email" name="email_from" class="form-control" value="<?php echo isset($_POST['email_from']) ? $_POST['email_from'] : ''; ?>" >
								</div>
								<div class="form-group">
									<label>Message</label>
									<textarea class="form-control" name="email_message"><?php echo isset($_POST['email_message']) ? $_POST['email_message'] : ''; ?></textarea>
								</div>
								<div class="form-group captcha-group">
									<div class="row">
									<div class="col-md-5">
										<img id="captcha" src="/users/fp_thunder/www/FP/securimage/securimage_show.php" alt="CAPTCHA Image" />
									</div>
									<div class="col-md-5">
										<label>Captcha Code</label>
										<input id="captcha_code" type="text" name="captcha_code" size="10" maxlength="6" class="form-control" />
										<a href="#" onclick="document.getElementById('captcha').src = './securimage/securimage_show.php?' + Math.random(); return false">[ Different Image ]</a>
									</div>
									</div>
								</div>
								<div class="form-group">
									<input type="submit" value="Submit" class="btn primary block">
								</div>
							</form>

						</div>
						<div class="col-md-5">
							<img src="images/sponsors.jpg" alt="sponsors images" class="sponsors">
						</div>
					</div>
					<hr>
					<div class="t-c">
							<h2>Feel free to connect with us on Weibo and WeChat too!</h2>
							<img src="images/qrcodes.jpg" alt="qr codes" class="sponsors">
					</div>
			<?php
				}
			?>

		</div>

		<?php include 'includes/footer.php' ?>
	</body>
</html>
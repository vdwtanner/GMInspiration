<DOCTYPE html>
<html>
<head>
	<title>Email Verification</title>
</head>
<body>
	<div id="header">
		<h2>Dungeon Crawlers - Email verification</h2>
	</div>
	<div id="wrapper">
		<?php
			$mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
			if ($mysql->connect_error) {
				die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
			}
			
			if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash'])){
				// Verify data
				$email = mysql_escape_string($_GET['email']); // Set email variable
				$hash = mysql_escape_string($_GET['hash']); // Set hash variable
				$search = $mysql->query("SELECT email, hash, active FROM users WHERE email='".$email."' AND hash='".$hash."' AND active='0'") or die(mysqli_error()); 
				$match  = $search->num_rows;
				if($match > 0){
					// We have a match, activate the account
					$mysql->query("UPDATE users SET active='1' WHERE email='".$email."' AND hash='".$hash."' AND active='0'") or die(mysql_error());
					echo '<div class="statusmsg">Your account has been activated, you can now login</div>';
				}else{
					// No match -> invalid url or account has already been activated.
					 echo '<div class="statusmsg">The url is either invalid or you already have activated your account.</div>';
				}
			}else{
				// Invalid approach
				echo '<div class="statusmsg">Invalid approach, please use the link that has been send to your email.</div>';
			}
		?>
	</div>
	<a href="index.html"><button style="radius: 4px;">Home</button></a>
</body>
</html>
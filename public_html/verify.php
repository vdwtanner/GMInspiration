<DOCTYPE html>
<html>
<head>
	<title>Email Verification</title>
	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all and (min-width: 33.236em)">
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
			try{
				$mysql->query("START TRANSACTION");
				if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash'])){
					// Verify data
					$email = $_GET['email']; // Set email variable
					$hash = $_GET['hash']; // Set hash variable
					//$search = $mysql->query("SELECT email, hash, active FROM users WHERE email='".$email."' AND hash='".$hash."' AND active='0'") or die(mysqli_error()); 
					$stmt = $mysql->prepare("SELECT email, hash, active FROM users WHERE email=? AND hash=? AND active='0'");
					$stmt->bind_param("si", $email, $hash);
					if(!$stmt->execute()){
						echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
					}
					$e = null; $h = null; $a = null;
					$stmt->bind_result($e, $h, $a);
					$stmt->fetch();
					$stmt->close();
					if($e){
						// We have a match, activate the account
						//$mysql->query("UPDATE users SET active='1' WHERE email='".$email."' AND hash='".$hash."' AND active='0'") or die(mysql_error());
						$stmt = $mysql->prepare("UPDATE users SET active='1' WHERE email=? AND hash=? AND active='0'");
						$stmt->bind_param("si", $email, $hash);
						if(!$stmt->execute()){
							echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
						}
						$stmt->close();
						echo '<div class="statusmsg">Your account has been activated, you can now login</div>';
					}else{
						// No match -> invalid url or account has already been activated.
						 echo '<div class="statusmsg">The url is either invalid or you already have activated your account.</div>';
					}
				}else{
					// Invalid approach
					echo '<div class="statusmsg">Invalid approach, please use the link that has been send to your email.</div>';
				}
				$mysql->commit();
			}catch(Exception $e){
				echo '<div class="statusmsg">It looks like something went wrong, please try again or contact the DungeonCrawler admins if problem persists.</div>';
				$mysql->rollback();
			}
		?>
	</div>
	<a href="index.html"><button style="border-radius: 10px;">Home</button></a>
</body>
</html>

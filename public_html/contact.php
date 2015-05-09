<?php
	// Define variables for SEO
	$pageTitle = "Contact Us - The GM's Inspiration";
	$pageDescription = "Get in contact with the developers of The GM's Inspiration";
	include "header.php";
	session_start();
?>
<DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all and (min-width: 33.236em)">

	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
</head>

<body>
<div id='container'>
	<h2>Contact Information</h2>
	<p>Got a comment, complaint, or suggestion for the devs? Contact us at</p>
		<ul>
		<li>Email: gminspiration@gmail.com</li>
		</ul>
	<p>Need to report a user or contribution for inappropriate content? Contact an admin!</p>
		<ul>
	<?php
		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if ($mysql->connect_error) {
			die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}

		$stmt = $mysql->prepare("SELECT username FROM users WHERE admin = 1");
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$user = null;
		$stmt->bind_result($user);
		while($stmt->fetch()){
			$row["username"] = htmlspecialchars($user, ENT_QUOTES, "UTF-8");
			$rowarr[] = $row;
		}

		foreach($rowarr as $key => $value){
			echo "<li><a href='profile.php?user=".$value["username"]."'>".$value["username"]."</a></li>";
		}
		

	?>
		</ul>

<!--
	<h4 style='font-size: 120%'>Your Profile</h4>
	<p>After signing up, you should take a second to check out your profile page. From here you can,</p>
		<ul>
		<li>Update your profile description.</li>
		<li>Update your profile picture.</li>
		<li>Find a list of all of the contributions you've submitted.</li>
		<li>Change your password.</li>
		</ul>

	<h4 style='font-size: 120%'>Your Inbox</h4>
	<p>Your inbox contains all your private messages from other users. Use this service to discreetly tell a user his/her contribution is terrible to save them from public embarassment.
	  What a nice person you are!</p>
	<h4 style='font-size: 120%'>Contribution Privacy</h4>
	<p>There are three settings for privacy options on contributions,</p>
		<ul>
		<li>Private: only the creator may view or edit this contribution.</li>
		<li>Public: all visitors to the site may view this contribution. Users may comment and rate.</li>
		<li>Protected: This contribution will not show up in any search results, however it may be shared via direct link or through the collections system.</li>
		</ul>
-->

</div>
</body>
</html>
<?php include 'footer.php';?>
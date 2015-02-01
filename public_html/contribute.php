<?php
	session_start();
?>
<DOCTYPE html>
<html>
<head>
	<title>Contribute</title>
	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all and (min-width: 33.236em)">
</head>
<body>
	<h2>Dungeon Crawlers - Contribute</h2>
	<?php
		echo "Welcome to the contribution screen, ".$_SESSION["username"];
	?>
	<form method="POST">
		<label for="name">Name:</label>
	</form>
	<a href="index.html"><button style="border-radius: 10px;">Home</button></a>
</body>
</html>

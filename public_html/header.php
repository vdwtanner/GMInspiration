
<?php
	session_start();
	echo "<div id='headcontainer'>";
	echo "<a id='homelink' href='index.html'>Dungeon Crawlers</a>";
	echo "<div id='headerlinks'>";
	if($_SESSION["username"]){
		echo "<h2>Welcome, ".$_SESSION["username"]." <a href='logoff.php'>logout</a></h4>";
	}else{
		echo "<h4><a href='login.html'>Login</a><br><a href='signup.html'>Sign up</a></h4>";
	}
	echo "</div>";

	echo "<div style='clear: both;'>";
	echo "<hr>";
	echo "<br>";
	echo "<hr>";
	echo "</div>";
	echo "</div>";


?>


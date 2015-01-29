<?php
	session_start();
?>
<DOCTYPE html>
<html>
<head>
</head>
<body>
	<?php
		echo $_SESSION["username"] . ", ";
		try{
			session_unset();
			session_destroy();
		}catch(Exception $e){
			echo "there was an error during logoff. Please go cry in a corner.";
		}
		echo "you were successfully logged off.";
	?>
	<a href="index.html"><button style="radius: 4px;">Home</button></a>
</body>
</html>
<?php
	session_start();
?>
<DOCTYPE html>
<html>
<head>
</head>
<body>
	<h4>Discontinued testing function. Use search function instead.</h4>
	<form method="GET" action="view_contribution.php">
		<label for="contid">Select an ID: </label><select id="contid" name="contid">
			<?php
				$mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
				if ($mysql->connect_error) {
					die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
				}
				$result=$mysql->query("SELECT * from contributions");
				$count=$result->num_rows;
				for($x=1;$x<=$count; $x++){
					echo "<option value='".$x."'>".$x."</option>";
				}
			?>
		</select>
		<input type="submit" value="Oh hai! It works now:P"/>
	</form>
</body>
</html>
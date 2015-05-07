<?php
	session_start();
	mb_internal_encoding("UTF-8");
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	
</head>
<body>

<?php



	echo "<div id='footcontainer'>";
	echo "<hr>";

	echo "<div style='width: 50%; float: left;'>";
	echo "<br>";	
		echo "<div style='display:inline-block; padding-left:20%; padding-right: 20%;'>";
		echo "<b><a class='hlink' href='home.php'>Home</a>&nbsp;</b>";
		echo "<b><a class='hlink' href='about.php'>About</a>&nbsp;</b>";
		echo "<b><a class='hlink' href='contact.php'>Contact Us</a>&nbsp;</b>";
		echo "</div>";
	echo "</div>";
	
	echo "<div style='width: 50%; float: right; display:inline '>";
	echo "&copy <p style='display:inline;'>GMInspiration </p>";
	echo "<br>";	
	echo "<p style='font-size: x-small;'>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque volutpat sapien ac sem sagittis tempus. Nullam dui magna, lacinia ut rhoncus vel, consequat vel tortor. Nulla finibus ultrices massa, in semper tellus rhoncus a. Curabitur vitae eleifend dolor. Pellentesque pretium tempus enim, ut eleifend felis congue et. </p>";
	echo "</div>";
	
	
	
	echo "<br>";
	echo "<br>";
	echo "<br>";	
	echo "<br>";
	echo "<br>";
	
	echo "</div>";

?>
</body>
</html>


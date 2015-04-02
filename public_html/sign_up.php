<?php
	include "header.php";
?>

<DOCTYPE html>
<html>
<head>

	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all and (min-width: 33.236em)">

	<script type="text/javascript" language="javascript">
		function check(input) {
			if (input.value != document.getElementById('pass').value) {
				input.setCustomValidity('Password Must be Matching.');
				input.style.borderColor = "red";
			} else {
				// input is valid -- reset the error message
				input.setCustomValidity('');
				input.style.borderColor = null;
			}
		}

		function validateUsername(input){
			if(/^[0-9a-zA-Z_.-[\s]]+$/.test(input.value.concat(" ")) || /[^\s][\s]/.test(input.value) || input.value.length < 3 || input.value.length > 20) {
				input.setCustomValidity("Usernames must be 3-20 characters long, and may only contain 0-9, a-z, A-Z, -, _");
				input.style.borderColor = "red";
			} else {
				input.setCustomValidity("");
				input.style.borderColor = null;
			}
		}
	</script>
</head>
<body>
<div id='container'>
	<form method="POST" action="sign_up_completion.php">
		<label for="username">Username:</label>
		<input id="username" name="username" type="text" maxlength="20" required oninput="validateUsername(this)" onclick="validateUsername(this)"/><div title="Usernames must be 3-20 characters long, and may only contain 0-9, a-z, A-Z, -, _" style='display:inline;'>&nbsp?</div></br>
		<label for="email">Email:</label>
		<input id="email" name="email" type="email" maxlength="60" required/></br>
		<label for="pass">Password:</label>
		<input id="pass" name="pass" type="password" pattern=".{6,25}" title="6-25 characters required" required /></br>
		<label for="pass">Confirm Password:</label>
		<input id="confpass" name="confpass" type="password" pattern=".{6,25}" title="6-25 characters required" required oninput="check(this)" /></br>
		<input type="submit" value="Sign Up"/>
	</form>
</div>
</body>
</html>

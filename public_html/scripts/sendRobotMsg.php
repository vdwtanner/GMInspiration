
<?php
/******************************************************************
	Call this function when the webserver needs to send a
	message to a users inbox.

	Use:
	
	function sendMsg(sender, subject, body, recipient){
		$.ajax({
			url: "scripts/sendRobotMsg.php",
			type: "POST",
			data: {
				msgsubject: subject,
				msgbody: body,
				msgsender: sender,
				msgrecipient: recipient
			},
			success: function(html){
				console.log(html);
				$("#"+id).remove();
			}

		});
	}

	(THIS ^ IS IMPLEMENTED IN "ajaxMsgFunctions.js"
	IMPORT IT INTO YOUR FILE USING

	<script src="scripts/ajaxMsgFunctions.js"></script>)

******************************************************************/

	$mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
	if($mysql->connect_error){
		die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
	}

	try{
		$emsgsub = htmlspecialchars($_POST["msgsubject"]);
		$emsgbody= htmlspecialchars($_POST["msgbody"]);


		
		$mysql->query("START TRANSACTION");
		$result = $mysql->query("INSERT INTO private_messages (sender, recipient, subject, message) VALUES ('".$_POST["msgsender"]."','".$_POST["msgrecipient"]."','".$emsgsub."','".$emsgbody."')");
		echo "\"".$emsgsub."\" was successfully sent! Yay!";
		
	}catch(Exception $e){
		echo "An error occurred while trying to send a message.";
	}

?>

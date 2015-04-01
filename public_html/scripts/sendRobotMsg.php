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

	$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
	if($mysql->connect_error){
		die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
	}

	try{
		$smsgsender	= stripslashes($_POST["msgsender"]);
		$smsgrecipient	= stripslashes($_POST["msgrecipient"]);
		$smsgsub 	= stripslashes($_POST["msgsubject"]);
		$smsgbody	= stripslashes($_POST["msgbody"]);


		
		$mysql->query("START TRANSACTION");
		//$result = $mysql->query("INSERT INTO private_messages (sender, recipient, subject, message) VALUES ('".$_POST["msgsender"]."','".$_POST["msgrecipient"]."','".$emsgsub."','".$emsgbody."')");
		$stmt = $mysql->prepare("INSERT INTO private_messages (sender, recipient, subject, message) VALUES (?,?,?,?)");
		$stmt->bind_param("ssss", $smsgsender, $smsgrecipient, $smsgsub, $smsgbody);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$stmt->close();
		echo "\"".$emsgsub."\" was successfully sent! Yay!";
		$mysql->commit();		
	}catch(Exception $e){
		$mysql->rollback();
		echo "An error occurred while trying to send a message.";
	}

?>

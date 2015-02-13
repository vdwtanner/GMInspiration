<?php
	function deleteMsg($id){

		$mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
		if($mysql->connect_error){
			die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
		}
		try{
			$mysql->query("START TRANSACTION");
			$result = $mysql->query("DELETE FROM private_messages WHERE id='".$id."'");
			echo "Message with ID=".$id." was successfully deleted.";
			$mysql->commit();
		}catch(Exception $e){
			$mysql->rollback();
			header("HTTP/1.1 412 ".$e);
		}
		$mysql->close();
	}
	
	if(!$_POST["msg"]){
		header("HTTP/1.1 412 No delete ID provided");
		die();
	}
	deleteMsg($_POST["msg"]);
	
?>
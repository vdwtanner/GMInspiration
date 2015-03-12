<?php
	function deleteMsg($id){

		$mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
		if($mysql->connect_error){
			die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
		}
		try{
			$mysql->query("START TRANSACTION");
			//$mysql->query("UPDATE private_messages SET `read`='1' WHERE `id`=".$id."");
			$stmt = $mysql->prepare("UPDATE private_messages SET `read`='1' WHERE `id`=?");
			$stmt->bind_param("i", $id);
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}
			$stmt->close();
			$mysql->commit();
			echo "UPDATE private_messages SET `read`='1' WHERE `id`=".$id;
			echo "Yay, did the things. Message with ID=".$id." was marked as read.";
		}catch(Exception $e){
			$mysql->rollback();
			header("HTTP/1.1 412 ".$e);
		}
		$mysql->close();
	}
	
	if(!$_POST["msg"]){
		header("HTTP/1.1 412 No msg ID provided");
		die();
	}
	deleteMsg($_POST["msg"]);
?>

<?php
	/**For use by the commenter*/
	function deleteComment($id){
		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if ($mysql->connect_error) {
			die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}
		try{
			$mysql->query("START TRANSACTION");
			$stmt=$mysql->prepare("UPDATE contribution_comments SET comment=? WHERE contribution_id=?");
			$comment= $_SESSION["username"]." deleted thier comment.";
			$stmt->bind_param($comment, $id);
			if(!$stmt->execute()){
				echo "ERROR: MYSQL";
			}
			$mysql->commit();
		}catch(Exception $e){
			$mysql->rollback();
		}
		$mysql->close();
	}
	
	/**For use by the commenter*/
	function deleteRating($id){
		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if ($mysql->connect_error) {
			die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}
		$mysql->close();
	}
?>
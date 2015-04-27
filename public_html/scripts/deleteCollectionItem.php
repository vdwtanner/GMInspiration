<?php
	session_start();
	$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
	if ($mysql->connect_error) {
		die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}
	
	$collection_id = $_POST["collectionID"];
	$contribution_id = $_POST["contriID"];

	try{
		$stmt = $mysql->prepare("SELECT username, contribution_ids_json FROM collections WHERE id=?");
		$stmt->bind_param("i", $collection_id);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$u = null; $c = null;
		$stmt->bind_result($u, $c);
		$stmt->fetch();
		$stmt->close();

		if($u == $_SESSION["username"]){

			if($c)
				$cjsonArr = json_decode($c, true);
			else
				$cjsonArr = array();


			$index = array_search($contribution_id, $cjsonArr);
			if($index === false){
				echo "That contribution isnt in this collection!";
				exit();
			}else{
				unset($cjsonArr[$index]);
			}

			$cjson = json_encode($cjsonArr);

			$stmt = $mysql->prepare("UPDATE collections SET contribution_ids_json=?, size=? WHERE id=?");
			$stmt->bind_param("sii", $cjson, count($cjsonArr), $collection_id);
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}
			$stmt->close();
		

			echo "The contribution was removed from your collection.";
		}else{
			echo "You are not the owner of this collection.";
		}
		$mysql->commit();
	}catch(Exception $e){
		$mysql->rollback();
		echo "Whoops, something went wrong.";
	}

?>

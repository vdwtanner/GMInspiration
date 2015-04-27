<?php
	session_start();
	$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
	if ($mysql->connect_error) {
		die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}
	
	$contriID = $_POST["contriID"];
	$collectionID = $_POST["collectionID"];


	try{
		/******************************************
			Adding contribution to JSON Code
		******************************************/

		$stmt = $mysql->prepare("SELECT username, name, contribution_ids_json FROM collections WHERE id=?");
		$stmt->bind_param("i", $collectionID);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$u = null; $n = null; $json = null;
		$stmt->bind_result($u, $n, $json);
		$stmt->fetch();
		$stmt->close();
		
		if($u == $_SESSION["username"]){

			if($json)
				$contriIDArray = json_decode($json, true);
			else
				$contriIDArray = array();

			if(in_array( $contriID, $contriIDArray)){
				echo "You've already added this contribution to that collection.";
				$mysql->rollback();
				exit();
			}
			$contriIDArray[] = $contriID;

			$contriIDjson = json_encode($contriIDArray);

			// Then lets save that array
			$stmt = $mysql->prepare("UPDATE collections SET contribution_ids_json=?, size=? WHERE id=?");
			$stmt->bind_param("sii", $contriIDjson, count($contriIDArray), $collectionID);
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}
			$stmt->close();

			unset($contriIDArray);
			unset($json);
			echo "This contribution has been successfully added to \"".htmlspecialchars($n, ENT_QUOTES, "UTF-8")."\"";

		}else{
			echo "You cant add to that collection";

		}
		$mysql->commit();
	}catch(Exception $e){
		$mysql->rollback();
		echo "Whoops, something went wrong.";
	}
	

?>

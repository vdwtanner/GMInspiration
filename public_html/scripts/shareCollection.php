<?php
	session_start();
?>
<DOCTYPE html>
<html>
<head>

</head>
<body>
	
<?php
	$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
	if ($mysql->connect_error) {
		die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}
	
	$share_username = $_POST["share_username"];
	$id = $_POST["id"];
	$add = $_POST["add"];	// 1 or 0
	try{	
		// First lets check if the username inputed is valid; 
		$stmt = $mysql->prepare("SELECT email FROM `users` WHERE username=?");
		$stmt->bind_param("s", $_POST["share_username"]);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$e = null;
		$stmt->bind_result($e);
		$stmt->fetch();
		$stmt->close();
		if($e){
			if($add == 1){
				/************************************
					Adding User to JSON Code
				************************************/
				// Then lets see what we had beforehand in the json.
				// Lets make a temp array, load in our current json in the database
				// then add our new username.
				$tempUserArray = array();

				$stmt = $mysql->prepare("SELECT sharedusers_json FROM collections WHERE id=?");
				$stmt->bind_param("i", $id);
				if(!$stmt->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}
				$json = null;
				$stmt->bind_result($json);
				$stmt->fetch();
				$stmt->close();
				if($json)
					$userArray = json_decode($json, true);
				if($userArray){
					$tempUserArray = array_merge($userArray, $tempUserArray);
					if(in_array( $share_username, $tempUserArray)){
						echo "You've already shared this collection with ".$share_username;
						$mysql->rollback();
						exit();
					}
				}

				$tempUserArray[] = $share_username;
				$tempUserJson = json_encode($tempUserArray);
				// Then lets save that array
				$stmt = $mysql->prepare("UPDATE collections SET sharedusers_json=? WHERE id=?");
				$stmt->bind_param("si", $tempUserJson, $id);
				if(!$stmt->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}
				$stmt->close();
				unset($tempUserArray);
				unset($tempUserJson);
				unset($userArray);
				unset($json);
				echo "This collection has been successfully shared with ".$share_username;


			}else{
				/***************************************
					Removing User from JSON Code
				***************************************/
				$stmt = $mysql->prepare("SELECT sharedusers_json FROM collections WHERE id=?");
				$stmt->bind_param("i", $id);
				if(!$stmt->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}
				$json = null;
				$stmt->bind_result($json);
				$stmt->fetch();
				$stmt->close();
				if($json)
					$userArray = json_decode($json, true);
				if($userArray){
					$index = array_search($share_username, $userArray);
					if($index === false){
						echo "You weren't sharing with that person in the first place!";
						exit();
	
					}else{
						unset($userArray[$index]);
					}
				}else{
					echo "You aren't currently sharing this collection with anyone.";
					exit();
				}
				
				$userJson = json_encode($userArray);
				$stmt = $mysql->prepare("UPDATE collections SET sharedusers_json=? WHERE id=?");
				$stmt->bind_param("si", $userJson, $id);
				if(!$stmt->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}
				$stmt->close();
				unset($userJson);
				unset($userArray);
				unset($json);
				echo "You are no longer sharing this collection with ".$share_username;
						

				
			}
		}else{
			echo "That is not a valid username";
			exit();
		}

		$mysql->commit();
	}catch(Exception $e){
		$mysql->rollback();
		echo "Whoops, something went wrong.";
	}

?>

</body>
</html>

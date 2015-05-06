<?php
	function loadComments($id, $numComments, $offset, $totalComments){

		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if ($mysql->connect_error) {
		die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}

		$stmt = $mysql->prepare("SELECT id, username, timestamp, comment FROM contribution_comments WHERE contribution_id = ? ORDER BY timestamp DESC LIMIT ? OFFSET ?");
		$stmt->bind_param("iii", $id, $numComments, $offset);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$u=null; $t=null; $c=null; $i=null;
		$stmt->bind_result($i, $u, $t, $c);
		while($stmt->fetch()){
			$row["id"] = $i;
			$row["username"] = htmlspecialchars($u, ENT_QUOTES, "UTF-8");
			$row["timestamp"] = $t;
			$row["comment"] = $c;
			$rowarr[] = $row;
		}
		$stmt->close();
		if(!empty($rowarr)){
			foreach($rowarr as $key => $row){
				//$result2 = $mysql->query("SELECT picture from users WHERE username='".$row["username"]."'");
				$stmt = $mysql->prepare("SELECT picture FROM users WHERE username=?");
				$stmt->bind_param("s", $row["username"]);
				if(!$stmt->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}
				$img=null;
				$stmt->bind_result($img);
				$stmt->fetch();
				$stmt->close();

				echo "<div class='comment' ><a href='profile.php?user=".$row["username"]."'><img src='".$img."' alt='".$row["username"]."&#39s profile picture' width='50' height='50' style='float: left;'><div id='namedate_".$row["id"]."' style='margin-top:.4em;><b><em style='margin-bottom: .2em;'>".$row["username"]."</em></b></a>";
				if($_SESSION["username"]==$row["username"]){
					echo "<a id='delete_".$row["id"]."' class='button' style='display:none; float: right' onclick='deleteComment(".$row["id"].", this)'>Delete</a>";
				}
				echo "<h5 style='margin-top: .2em; margin-bottom: .4em;'>".date('F j, Y g:i A',strtotime($row["timestamp"]))."</h5></div></br>";
				echo "<p style=' margin: 0em;'>".$row["comment"]."</p>";
		
				echo "</div>";
			}
		}
		if(($offset + count($rowarr)) < $totalComments){
			echo "<button id='showmore_comments' onclick='showMoreComments(".$id.",".($offset+count($rowarr)).", ".$totalComments.")'>show more</button>";
		}

		unset($row);
		unset($rowarr);
	}

	if($_POST["action"] == "loadComments"){
		loadComments($_POST["id"], $_POST["numComments"], $_POST["offset"], $_POST["totalComments"]);
	}


?>

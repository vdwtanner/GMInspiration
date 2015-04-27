<?php
	include "header.php";
	session_start();
?>

<DOCTYPE html>
<html>
<head>

	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all and (min-width: 33.236em)">

	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
</head>



<script type='text/javascript'>
	function addCollection(){
		$("#collection_modal_area").load("newCollection.html");
		$("#collection_modal_area").dialog({
			height: 300,
			width: 350,
			modal: true,
			position: {my: "center top", at: "center top", of: window},
			buttons: {
				"Add": function(){
					submitAddCollection();
				},
				"Cancel": function(){
					$(this).dialog("close");
				} 
			}
		});
	}

	function submitAddCollection(){
		var name=$("#collection_name").val();
		var game=$("#collection_game").val();
		var img =$("#collection_img").val();
		$.ajax({
			url: "addCollection.php",
			type: "POST",
			data: ({
				name: name,
				game: game,
				img: img,
			}),
			success: function(html){
				$("#formContainer").html(html);
				$("#collection_modal_area").dialog("option", "buttons", [{
					text: "Close",
					click: function(){
						$(this).dialog("close");
						location.reload();

					}
				}]);
				//setTimeout(function(){location.reload()},1500);
			},
			error: function(xhr, status, error){
				$("#formContainer").html(error);
				$("#collection_modal_area").dialog("option", "buttons", [{
					text: "Close",
					click: function(){
						$(this).dialog("close");
					}
				}]);
				
			}

		});
	}

	function shareCollection(id){
		var div = document.createElement("div");
		$(div).load("shareCollection.html");
		$(div).dialog({
			height: 210,
			width: 400,
			title: "Share with who?",
			modal: true,
			position: {my: "center top", at: "center top", of: window },
			buttons: ({
				"Add": function(){
					var share_username=$("#share_username").val();
					var add = 1;
					$.ajax({
						url: "scripts/shareCollection.php",
						type: "POST",
						data: {
							share_username: share_username,
							id: id,
							add: add,
						},
						success: function(html){
							$(div).html(html);
							$(div).dialog("option", "buttons", [{
								text: "Close",
								click: function(){
									$(this).dialog("close");
									window.location.href="collections.php";
								}
							}]);
							//setTimeout(function(){location.reload()},1200);
						},
						error: function(xhr, status, html){
							$(div).html(html);
							$(this).dialog("option", "buttons", [{
								text: "Close",
								click: function(){
									$(this).dialog("close");
								}
							}]);
						}
					});
				},
				"Remove": function(){
					var share_username=$("#share_username").val();
					var add = 0;
					$.ajax({
						url: "scripts/shareCollection.php",
						type: "POST",
						data: {
							share_username: share_username,
							id: id,
							add: add,
						},
						success: function(html){
							$(div).html(html);
							$(div).dialog("option", "buttons", [{
								text: "Close",
								click: function(){
									$(this).dialog("close");
									window.location.href="collections.php";
								}
							}]);
							//setTimeout(function(){location.reload()},1200);
						},
						error: function(xhr, status, html){
							$(div).html(html);
							$(this).dialog("option", "buttons", [{
								text: "Close",
								click: function(){
									$(this).dialog("close");
								}
							}]);
						}
					});
				},
				"Cancel": function(){
					$(this).dialog("close");
				}
			})
		});
	}

	function deleteCollection(id){
		var div = document.createElement("div");
		$(div).html("<b>This cannot be undone.</b>");
		$(div).dialog({
			height: 175,
			width: 400,
			title: "Are you sure?",
			dialogClass: "ui-state-error",
			modal: true,
			position: {my: "center top", at: "center top", of: window },
			buttons: ({
				"Yes": function(){
					$.ajax({
						url: "scripts/deleteCollection.php",
						type: "POST",
						data: {
							id: id
						},
						success: function(html){
							$(div).html(html);
							$(div).dialog("option", "buttons", [{
								text: "Close",
								click: function(){
									$(this).dialog("close");
									window.location.href="collections.php";
								}
							}]);
							//setTimeout(function(){location.reload()},1200);
						},
						error: function(xhr, status, html){
							$(div).html(html);
							$(this).dialog("option", "buttons", [{
								text: "Close",
								click: function(){
									$(this).dialog("close");
								}
							}]);
						}
					});
				},
				"No": function(){
					$(this).dialog("close");
				}
			})
		});
	}



</script>


<body>
<div id='container'>
<?php
	if($_SESSION["username"]){
		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if ($mysql->connect_error) {
			die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}

	/********************************************
		My Collections Header and Content
	********************************************/
	try{
		$stmt = $mysql->prepare("SELECT id, name, img, size, sharedusers_json, game FROM collections WHERE username=?");
		$stmt->bind_param("s", $_SESSION["username"]);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$id = null; $n = null; $img = null; $s = null; $suj = null; $g = null;
		$stmt->bind_result($id, $n, $img, $s, $suj, $g);
		while($stmt->fetch()){
			$row["id"] = $id;
			$row["name"] = htmlspecialchars($n, ENT_QUOTES, "UTF-8");
			$row["img"] = $img;
			$row["size"] = $s
			$row["sharedusers_json"] = $suj;
			$row["game"] = htmlspecialchars($g, ENT_QUOTES, "UTF-8");
			$rowarr[] = $row;
		}

		echo "<div class='collectionHeader'>";
		echo "<h2 style='display:inline; margin-right:10px;'>My Collections</h2>";
		echo "<a class='button' onclick='addCollection()' >New...</a>";
		echo "</div>";
		echo "<hr>";


		echo "<div class='collectionList'>";
		if($rowarr){
			foreach($rowarr as $key => $row){
				$sharedusernames = json_decode($row["sharedusers_json"], true);	// get associative json array
				echo "<div class='collectionListItem'>";

				// Item Title and Sidebar decoration
				echo "<span class='collectionItemTitle'>";
				echo "<span style='float:right;'>";
				echo "<a class='collectionItemEditDelete' onclick='shareCollection(".$row["id"].")'>share</a><a class='collectionItemEditDelete' onClick='deleteCollection(".$row["id"].")'>delete</a>";
				echo "</span>";
				echo "</span>";
				echo "<span class='collectionItemSideBar'></span>";
		
					// Item Content
					echo "<a class='collectionItemContent' href='view_collection.php?id=".$row["id"]."'>";
					// Item Picture
					echo "<img class='collectionPicture' src='".$row["img"]."'>";			
					// Item Name, edition, and shared with				
					echo "<div class='collectionText'><b>".$row["name"]."<br>".$row["game"]."</b><br>";
					if($sharedusernames){
						echo "<br>Shared With: ";
						$userkeys = array_keys($sharedusernames);
						for($i = 0; $i < sizeof($sharedusernames)-1; $i++){
							echo htmlspecialchars($sharedusernames[$userkeys[$i]], ENT_QUOTES, "UTF-8").", ";
						}
						echo htmlspecialchars($sharedusernames[$userkeys[$i]], ENT_QUOTES, "UTF-8");
					}
					echo "</div>";
					// Num of Items in Collections
					echo "<div class='collectionNumItems'><b>".$row["size"]." Items</b></div>";
					echo "</a>";
				echo "</div>";
			}
		}else{
			echo "<b style='margin: 10px; display: inline-block'>You dont have any Collections!</b>";
		}
		echo "</div>";
		unset($row);
		unset($rowarr);
		$mysql->commit();
	}catch(Exception $e){
		$mysql->rollback();
	}

	/***********************************************
		Shared Collections Header and Content
	************************************************/
	try{
		$stmt = $mysql->prepare("SELECT id, username, name, img, size, sharedusers_json, game FROM collections WHERE sharedusers_json LIKE ?");
		$usernameTemplate = "%\"".$_SESSION["username"]."\"%";
		$stmt->bind_param("s", $usernameTemplate);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$id = null; $u = null; $n = null; $img = null; $s = null; $suj = null; $g = null;
		$stmt->bind_result($id, $u, $n, $img, $s, $suj, $g);
		while($stmt->fetch()){
			$row["id"] = $id;
			$row["username"] = htmlspecialchars($u, ENT_QUOTES, "UTF-8");
			$row["name"] = htmlspecialchars($n, ENT_QUOTES, "UTF-8");
			$row["img"] = $img;
			$row["size"] = $s;
			$row["sharedusers_json"] = $suj;
			$row["game"] = htmlspecialchars($g, ENT_QUOTES, "UTF-8");
			$rowarr[] = $row;
		}

		echo "<br><br>";
		echo "<div class='collectionHeader'>";
		echo "<h2 style='display:inline; margin-right:10px;'>Collections Shared With Me</h2>";
		echo "</div>";
		echo "<hr>";

		echo "<div class='collectionList'>";
		if($rowarr){
			foreach($rowarr as $key => $row){
				$sharedusernames = json_decode($row["sharedusers_json"], true);	// get associative json array
				echo "<div class='collectionListItem'>";

				// Item Title and Sidebar decoration
				echo "<span class='collectionItemTitle'>";
				echo "<b class='collectionItemEditDelete'>Shared by:</b>";
				echo "<a class='collectionItemEditDelete' href='profile.php?user=".$row["username"]."'>".$row["username"]."</a>";
				echo "</span>";
				echo "<span class='collectionItemSideBar'></span>";
		
					// Item Content
					echo "<a class='collectionItemContent' href='view_collection.php?id=".$row["id"]."'>";
					// Item Picture
					echo "<img class='collectionPicture' src='".$row["img"]."'>";			
					// Item Name, edition, and shared with				
					echo "<div class='collectionText'><b>".$row["name"]."<br>".$row["game"]."</b><br>";
					if($sharedusernames){
						echo "<br>Shared With: ";
						$userkeys = array_keys($sharedusernames);
						for($i = 0; $i < sizeof($sharedusernames)-1; $i++){
							echo htmlspecialchars($sharedusernames[$userkeys[$i]], ENT_QUOTES, "UTF-8").", ";
						}
						echo htmlspecialchars($sharedusernames[$userkeys[$i]], ENT_QUOTES, "UTF-8");
					}
					echo "</div>";
					// Num of Items in Collections
					echo "<div class='collectionNumItems'><b>".$row["size"]." Items</b></div>";
					echo "</a>";
				echo "</div>";
			}
		}else{
			echo "<b style='margin: 10px; display: inline-block'>Noone has shared their stuff with you yet.</b>";
		}
		echo "</div>";

	
		

		$mysql->commit();
	}catch(Exception $e){
		$mysql->rollback();
	}

	}else{
		echo "You must be logged in to see this page";
	}

?>

</div>

	<div id="collection_modal_area" style="display: none" title="New Collection..."></div>
</body>
</html>

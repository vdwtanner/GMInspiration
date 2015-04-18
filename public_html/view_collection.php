<?php
	session_start();
	include "header.php";
?>
<DOCTYPE html>
<html>
<head>
        <link rel="stylesheet" href="css/example/global.css" media="all">
        <link rel="stylesheet" href="css/example/layout.css" media="all and (min-width: 33.236em)">
	<script type='text/javascript'>

		function deleteItemFromCollection(contriID, collectionID){
			var div = document.createElement("div");
			$(div).html("<b>Remove this contribution from your collection?</b>");
			$(div).dialog({
				height: 200,
				width: 400,
				title: "Are you sure?",
				dialogClass: "ui-state-error",
				modal: true,
				position: {my: "center top", at: "center top", of: window },
				buttons: ({
					"Yes": function(){
						$.ajax({
							url: "scripts/deleteCollectionItem.php",
							type: "POST",
							data: {
								contriID: contriID,
								collectionID: collectionID,
							},
							success: function(html){
								$(div).html(html);
								$(div).dialog("option", "buttons", [{
									text: "Close",
									click: function(){
										$(this).dialog("close");
										window.location.href="view_collection.php?id="+collectionID;
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



		$.fn.stars = function() {
		return $(this).each(function() {
			// Get the value
			var val = parseFloat($(this).html());
			// Make sure that the value is in 0 - 5 range, multiply to get width
			var size = Math.max(0, (Math.min(5, val))) * 16;
			// Create stars holder
			var $span = $('<span />').width(size);
			// Replace the numerical value with stars
			$(this).html($span);
		});
		}
		
		$(function() {
			$('span.stars').stars();
		});

	</script>

</head>
<body>
<div id="container">

<?php
	$id = $_GET["id"];

	if($_SESSION["username"]){
		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if ($mysql->connect_error) {
			die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}

		try{
			$stmt = $mysql->prepare("SELECT username, name, size, sharedusers_json, contribution_ids_json, game FROM collections WHERE id=?");
			$stmt->bind_param("i", $id);
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}
			$creator = null; $name = null; $size = null; $sharedusersJson = null; $contriJson = null; $game = null;
			$stmt->bind_result($creator, $name, $size, $sharedusersJson, $contriJson, $game);
			$stmt->fetch();
			$stmt->close();

			// Make all the display items safe
			$name = htmlspecialchars($name, ENT_QUOTES, "UTF-8");
			$creator = htmlspecialchars($creator, ENT_QUOTES, "UTF-8");

			// Decode the contribution_ids_json
			if($contriJson)
				$cjson = json_decode($contriJson, true);
			else
				$cjson = array();

			// Decode the sharedusers_json
			if($sharedusersJson)
				$ujson = json_decode($sharedusersJson, true);
			else
				$ujson = array();

			unset($sharedusersJson);
			unset($contriJson);

			if(in_array($_SESSION["username"],$ujson) || $_SESSION["username"] == $creator){

				echo "<div class='collectionHeader'>";
				echo "<h2 style='display:inline; margin-right:10px;'>".$name."</h2>";
				echo "<span style='float: right;'>";
				echo "<b>".$size." Items</b>";
				echo "</span>";
				echo "</div>";
				echo "<hr>";

				unset($name);
				unset($size);


				echo "<div class='collectionList'>";
				foreach($cjson as $key => $contriID){
					$stmt = $mysql->prepare("SELECT username, img, name, `type`, sub_type, game, `desc`, json, avg_fun, avg_balance, privacy FROM contributions WHERE id=?");
					$stmt->bind_param("i", $contriID);
					if(!$stmt->execute()){
						echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
					}
					$user=null; $img=null; $name=null; $type=null; $s_type=null; $game=null; $desc=null; $json=null; $avgFun=null; $avgBalance=null; $privacy=null;
					$stmt->bind_result($user, $img, $name, $type, $s_type, $game, $desc, $json, $avgFun, $avgBalance, $privacy);
					$stmt->fetch();
					$stmt->close();

					echo "<div class='contributionListItem'>";

					// Item Title and Sidebar decoration
					echo "<span class='collectionItemTitle'>";
					echo "<b class='collectionItemEditDelete'>Created by:</b>";
					echo "<a class='collectionItemEditDelete' href='profile.php?user=".$user."'>".$user."</a>";
					if($_SESSION["username"] == $creator){
						echo "<span style='float: right;'>";
						echo "<a class='collectionItemEditDelete' onclick='deleteItemFromCollection(".$contriID.",".$_GET["id"].")'>remove</a>";
						echo "</span>";
					}
					echo "</span>";
					echo "<span class='collectionItemSideBar'></span>";
						if($privacy == 0 || $privacy == 2){ // if item is public or protected
	
							// Item Content
							echo "<a class='collectionItemContent' href='view_contribution_updateable.php?contid=".$contriID."'>";
							// Item Picture
							echo "<img class='collectionPicture' src='".$img."'>";			
							// Item Name, game, and rating			
							echo "<div class='collectionText'><b>".$name."<br>".$game."</b><br>";
							if($avgBalance >= 0 && $avgFun >= 0)
								echo "<table><tr><td><b style='font-size:12'>Fun</b></td><td><span class='stars'>".$avgFun."</span></td></tr><tr><td><b style='font-size:12'>Balance</b></td><td><span class='stars'>".$avgBalance."</span></td></tr></table>";
							else
								echo "Not yet rated";
							echo "</div>";

							echo "</a>";
						}else{
							// Item Content
							echo "<a class='collectionItemContent' href='view_contribution_updateable.php?contid=".$contriID."'>";
							// Item Picture
							echo "<img class='collectionPicture' src='http://upload.wikimedia.org/wikipedia/commons/3/33/White_square_with_question_mark.png'>";			
							// Item Name, game, and rating			
							echo "<div class='collectionText'><b>Unidentified Item<br>".$game."</b><br>";
							echo "Undetermined Rating";
							echo "</div>";

							echo "</a>";

						}
					echo "</div>";
				}
			
				echo "</div>";
			}else{
				echo "<b>You dont have permission to view this page</b>";
			}
		
			$mysql->commit();
		}catch(Exception $e){
			$mysql->rollback();
		}
	}else{

		echo "<b>You need to be logged in to access this part of the site.</b>";
	}

?>
</div>
</body>

</html>
	

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

	function deleteCollection(){
		alert("Delete Collection");
	}

	function shareCollection(){
		alert("Share Collection");
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


<body>
<div id='container'>
<?php
	if($_SESSION["username"]){
		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if ($mysql->connect_error) {
			die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}


	try{
		$stmt = $mysql->prepare("SELECT name, img, size, sharedusers_json, game FROM collections WHERE username=?");
		$stmt->bind_param("s", $_SESSION["username"]);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$n = null; $img = null; $s = null; $suj = null; $g = null;
		$stmt->bind_result($n, $img, $s, $suj, $g);
		while($stmt->fetch()){
			$row["name"] = $n;
			$row["img"] = $img;
			$row["size"] = $s;
			$row["sharedusers_json"] = $suj;
			$row["game"] = $g;
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
				echo "<a class='collectionItemEditDelete' href='edit_collection.php'>view</a><a class='collectionItemEditDelete' onclick='shareCollection()'>share</a><a class='collectionItemEditDelete' onClick='deleteCollection()'>delete</a>";
				echo "</span>";
				echo "</span>";
				echo "<span class='collectionItemSideBar'></span>";
		
					// Item Content
					echo "<div class='collectionItemContent'>";
					// Item Picture
					echo "<img class='collectionPicture' src='".$row["img"]."'>";			
					// Item Name, edition, and shared with				
					echo "<div class='collectionText'><b>".$row["name"]."<br>".$row["game"]."</b><br>";
					if($sharedusernames){
						echo "<br>Shared With: ";
						$userkeys = array_keys($sharedusernames);
						for($i = 0; $i < sizeof($sharedusernames)-1; $i++){
							echo $sharedusernames[$userkeys[$i]].", ";
						}
						echo $sharedusernames[$userkeys[$i]];
					}
					echo "</div>";
					// Num of Items in Collections
					echo "<div class='collectionNumItems'><b>".$row["size"]." Items</b></div>";
					echo "</div>";
				echo "</div>";
			}
		}else{
			echo "<b style='margin: 10px; display: inline-block'>You dont have any Collections!</b>";
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

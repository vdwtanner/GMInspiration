<?php
	include "header.php";
    session_start();
	require dirname(__FILE__)."/scripts/parser.php";
?>
<DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="initial-scale=1">
	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all">
	
	<style>
	
		a {
			cursor: pointer;
		}
		a.button {
			margin: .3em;
			padding: 0 .3em 0 .3em;
			vertical-align: top;
			background: #FFAD33;
			border-radius: 10px;
		}
		a.button:hover {
			margin: .3em;
			padding: 0 .3em 0 .3em;
			vertical-align: top;
			background: #EC9C2E;
			border-radius: 10px;
		}
		a.button:active {
			margin: .3em;
			padding: 0 .3em 0 .3em;
			vertical-align: top;
			background: #E8A643;
			border-radius: 10px;
		}
		div.comment {
			background-color: rgba(150,100,56,.5);
			border: 2px;
			border-color: #F59032;
			padding: 1em;
			border-radius: 10px;
			margin: .5em;
		}
		div.namedate {
			padding-left: 1em;
		}
		span.stars, span.stars span {
			display: inline-block;
			background: url(img/dice64x64.png) 0 -16px repeat-x;
			background-size: 16px 32px;
			width: 80px;
			height: 16px;
		}
		span.stars span {
			background-position: 0 0;
		}
		
		p {
			margin-top: .1em;
		}
		
		div.textarea {
			background-color: #FFFFFF;
			box-shadow: 1px 1px 3px #888888;
			line-height: 1.5;
			height: 50px;
			border-radius: 4px;
			padding: 0px;
			margin: 0px;
		}
	</style>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
	<script src="http://cdn.jsdelivr.net/jquery.validation/1.13.1/jquery.validate.min.js"></script><!--Including some JQuery because yes-->
	<script src="scripts/ckeditor/ckeditor.js"></script>
	<script src="scripts/js/utils.js"></script>
	<link href="scripts/ckeditor/samples/sample.css" rel="stylesheet">
	<script type="text/javascript" language="javascript">

		function addContributionToCollection(contriID){
			var div = document.createElement("div");
			var collectionID = $("#collection_select").val();
			var collectionName = $("#collection_select_option_"+collectionID).html();
			$(div).html("<b>Add this contribution to \""+collectionName+"\"?</b>");
			$(div).dialog({
				height: 200,
				width: 400,
				title: "Add to Collection",
				modal: true,
				position: {my: "center top", at: "center top", of: window },
				buttons: ({
					"Yes": function(){
						$.ajax({
							url: "scripts/addCollectionItem.php",
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
										window.location.href="view_contribution_updateable.php?contid="+contriID;
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

		function comment(){
			var cid = $("#contribution_id").text();
			var username = $("#user").text();
			var form = $("#make_comment");
			var rich = $("#isRichText").is(":checked");
			var com = (rich)?$("#comment").html():$("#comment").val();
			console.log(rich);
			if(!com.length>25){
				alert("Please enter a comment more than 25 characters long");
			}else{
				$.ajax({
					url: "ajax_comment.php",
					type: "POST",
					data: ({
						c_id: cid,
						user: username,
						comment: com,
						rich: rich,
					}),
					//Expect to receive HTML back
					success: function(html){
						try{
							//$("#comments").prepend(html);
							$(html).insertAfter("#submit_comment");
							$("#comment").val("");
							//$("#comment").html("");
							//comments.insertBefore(html, comments.firstChild);
							//alert(1);
						}catch(e){
							//comments.appendChild(newdiv);
							alert("error");
						}
					},
					error: function(xhr, status, error){
						alert("Well. Looks like something went wrong. :/" +"\nstatus");
					}
				})
			}
		}
		
		function update(){
			var id=$("#contribution_id").text();
			var privacy=$("#privacy").val();
			var name=$("#name").text();
			var game=$("#game").text();
			var type=$("#type").text();
			var subtype=$("#subtype").text();
			var desc=$("#desc").html();
			var img=$("#img").attr("src");
			var labels=[];
			$("[name*='label ']").each(function() {labels.push($(this).html())});
			var texts=[];
			$("[name*='text ']").each(function() {texts.push($(this).html())});
			var json=new Array();
			for(var x=0; x < labels.length;x++){
				//store elements as objects within the JSON.
				var element = new Object();
				element.label=labels[x];
				element.text=texts[x];
				json[x]=element;
			}
			json=JSON.stringify(json);
			console.log("id=" + id);
			console.log("Privacy: " + privacy);
			console.log(name);
			console.log(game);
			console.log(type);
			console.log(subtype);
			console.log(desc);
			console.log(img);
			console.log(labels);
			console.log(texts);
			console.log("JSON: "+json);
			$.ajax({
				url: "update_contribution.php",
				type: "POST",
				data: ({
					id: id,
					privacy: privacy,
					name: name,
					game: game,
					type: type,
					subtype: subtype,
					desc: desc,
					img: img,
					json: json,
				}),
				success: function(html){
					var div=document.createElement("div");
					$(div).html(html);
					$(div).dialog({
						height: 250,
						width: 400,
						modal: true,
						position: {my: "center top", at: "center top", of: window},
						buttons: {
							"Awwww yeeeah, nat 20!": function(){$(this).dialog("close");}
						}
					});
				},
				error: function(xhr, status, html){
					var div=document.createElement("div");
					$(div).html(html);
					$(div).dialog({
						height: 250,
						width: 350,
						modal: true,
						position: {my: "center top", at: "center top", of: window},
						buttons: {
							"Crap... looks like a crit fail...": function(){$(this).dialog("close");}
						}
					});
				}
			});
		}
		
		function submitRating(form){
			var fun = $("#fun_rating").val();
			var balance = $("#balance_rating").val();
			var comment = $("#rating_comment").val();
			console.log(comment);
			$.ajax({
				url: "submit_rating.php",
				type: "POST",
				data: ({
					id: $("#contribution_id").text(),
					fun: fun,
					bal: balance,
					comment: comment,
				}),
				success: function(html){
					$(form).html(html);
					$(form).dialog("option", "buttons", [{
						text: "Close",
						click: function(){
							$(this).dialog("close");
							location.reload();
						}
					}]);
					//setTimeout(function(){location.reload();},1000);
				},
				error: function(xhr, status, error){
					$(form).html(error);
					$(form).dialog("option", "buttons", [{
						text: "Close",
						click: function(){
							$(this).dialog("close");
						}
					}]);
				}
			});
		}
		
		function rate(){
			var form = document.createElement("div");
			$(form).load("rating_form.html");
			$(form).dialog({
				height: 350,
				width: 400,
				modal: true,
				position: {my: "center top", at: "center top", of: window},
				buttons: {
					"Submit": function(){
						submitRating(form);
					},
					"Cancel": function(){
						$(this).dialog("close");
					}
				}
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
	
	function showComments(){
		$("#comments").show();
		$("#ratings").hide();
		$("#ratings_tab").prop("disabled", false);
		$("#comments_tab").prop("disabled", true);
	}
	
	function showRatings(){
		$("#comments").hide();
		$("#ratings").show();
		$("#ratings_tab").prop("disabled", true);
		$("#comments_tab").prop("disabled", false);
	}
	
	var num = $("#num_extras");
	function addField(){
		var label=document.createElement("label");
		
		var div=document.createElement("div");
		$(div).html('<h4 id="label '+num+'" name="label '+num+'" style="margin-bottom: .1em; padding-bottom: 0em" contenteditable="true">New Title</h4>' +
		'<div id="text '+num+'" name="text '+num+'" style="margin-top: .1em" contenteditable="true">Enter yee some words of wonder</div>');
		$("#body").append(div);
		CKEDITOR.inline("label "+num);
		CKEDITOR.inline("text "+num);
		num++;
	}
	
	function switchCommentType(cbox){
		if(cbox.checked){
			$("#comment").replaceWith("<div id='comment'></div>");
			replaceWithEditor(document.getElementById("comment"));
			editor.on( 'configLoaded', function() {

					// Remove unnecessary plugins to make the editor simpler.
					editor.config.removePlugins = "format";
					editor.config.removeButtons = "Source";
				});
		}else{
			$("#comment").replaceWith("<textarea id='comment' rows='5' cols='40' placeholder='Enter comment here'></textarea>");
			destroyEditor();
		}
	}
	
	function deleteContribution(){
		var id=$("#contribution_id").text();
		var div = document.createElement("div");
		$(div).html("<b>This cannot be undone.</b>");
		$(div).dialog({
			height: 175,
			width: 400,
			title: "Are you sure?",
			dialogClass: "ui-state-error",
			modal: true,
			position: { my: "left top", at: "left bottom", of: $("#delete_button") },
			buttons: ({
				"Yes": function(){
					$.ajax({
						url: "scripts/deleteContribution.php",
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
									window.location.href="home.php";
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
		//$(div).dialog("option","title", "<img src='http://png-3.findicons.com/files/icons/1951/iconza/32/warning.png' />Are you sure?");
	}
	</script>
</head>
<body>
<div id="container" class="cf">
<?php
    //echo "Hello ".$_SESSION("username");
    $mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
    if ($mysql->connect_error) {
        die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
    }
    // Tanner might wanna change this, and just use GET in the end. (we need to use GET in order to link contributions)
    if($_GET["contid"]){
		$id=$_GET["contid"];
    }else{
		$id=$_POST["id"];
	}
	echo "<p id='contribution_id' style='display: none;' >".$id."</p>";
	echo "<p id='user' style='display: none;'>".$_SESSION["username"]."</p>";
	$isCreator=false;
	
    try{
	/*********************************
		Avg Ratings Code
	**********************************/
        $mysql->query("START TRANSACTION");
		//$ratings=$mysql->query("SELECT COUNT(*), SUM(fun), SUM(balance) FROM ratings WHERE contribution_id=".$id);
		$stmt = $mysql->prepare("SELECT COUNT(*), SUM(fun), SUM(balance) FROM ratings WHERE contribution_id=?");
		$stmt->bind_param("i", $id);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$c=null; $s=null; $b=null;
		$stmt->bind_result($c, $s, $b);
		$stmt->fetch();
		$stmt->close();
		$num_ratings=$c;
		if($c>0){
			$avgFun=$s/$c;
			$avgBalance=$b/$c;
		}
        //$result = $mysql->query("SELECT * from contributions where id='".$id."'");
	/*********************************
		Get Contribution Data
	**********************************/
	$stmt = $mysql->prepare("SELECT username, img, name, `type`, sub_type, game, `desc`, json, privacy FROM contributions WHERE id=?");
	$stmt->bind_param("i", $id);
	if(!$stmt->execute()){
		echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
	}
	$user=null; $img=null; $name=null; $type=null; $s_type=null; $game=null; $desc=null; $json=null; $privacy=null;
	$stmt->bind_result($user, $img, $name, $type, $s_type, $game, $desc, $json, $privacy);
	$stmt->fetch();
	$stmt->close();
	if($privacy==1 && $user!=$_SESSION["username"]){
		exit("The contributor has currently set the privacy to \"private,\" so you cannot view it at this time.");
	}
	// make it safe to display
	$user = htmlspecialchars($user, ENT_QUOTES, "UTF-8");
	$name = htmlspecialchars($name, ENT_QUOTES, "UTF-8");
	$type = htmlspecialchars($type, ENT_QUOTES, "UTF-8");
	$s_type = htmlspecialchars($s_type, ENT_QUOTES, "UTF-8");
	$game = htmlspecialchars($game, ENT_QUOTES, "UTF-8");

        $fields = json_decode(($json));    //create associative array from json
	/********************************
		Privacy Drop Down
	*********************************/
		echo "<a style='float:right;' href='view_contribution_printable.php?contid=".$id."'>view printable version</a>";
		echo "<span style='display:none; clear:both;'></span>";
		if($user==$_SESSION["username"]){
			echo "<a id='update_button' class='button' onclick='update()'>Save Changes</a> <a id='delete_button' class='button' onclick='deleteContribution()'>Delete Contribution</a></br>";
			echo '<div id="privacy_settings" style="display: inline-block">
					<select id="privacy" title="Select a privacy option" required>
						<option'.(($privacy==0)?" selected='selected'":"").' value="0">Public</option>
						<option'.(($privacy==1)?" selected='selected'":"").' value="1">Private</option>
						<option'.(($privacy==2)?" selected='selected'":"").' value="2">Protected</option>
					</select></div>';
			$isCreator=true;
		}
		if($_SESSION["username"]){
	/********************************
		Collection Drop Down
	*********************************/
			$stmt = $mysql->prepare("SELECT id, name, contribution_ids_json FROM collections WHERE username=?");
			$stmt->bind_param("s", $_SESSION["username"]);
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}
			$collectionID = null; $n = null; $contriIDs = null;
			$stmt->bind_result($collectionID, $n, $contriIDs);
			while($stmt->fetch()){
				$row["id"] = $collectionID;
				$row["name"] = htmlspecialchars($n, ENT_QUOTES, "UTF-8");
				$row["contribution_ids_json"] = $contriIDs; 
				$rowarr[] = $row;
			}
			echo "<span style='float: right;'>";
			echo "<div id='collection_add'>";
			echo "<select id='collection_select' title='Select a collection to add this contribution to.' required>";
				foreach($rowarr as $key => $row){
					if($contriIDs)
						$cjson = json_decode($row["contribution_ids_json"], true);
					else
						$cjson = array();
				
					if(in_array($id, $cjson))
						echo "<option disabled value=".$row["id"].">".$row["name"]."</option>";
					else{
						echo "<option id='collection_select_option_".$row["id"]."' value=".$row["id"].">".$row["name"]."</option>";
						$enable_button = true;
					}
				}
			echo "</select>";
			if($enable_button)
				echo "<button onclick='addContributionToCollection(".$id.")'>Add to Collection</button>";
			else
				echo "<button onclick='addContributionToCollection(".$id.")' disabled>Already Added</button>";
			echo "</div>";
			echo "</span>";

			unset($row);
			unset($rowarr);
		}
		
	/********************************
		Display Collection
	*********************************/	
		echo "<div id='contribution'><div class='profile_img'><img id='img' src='".$img."' alt='An image depicting ".$name."' width='175' height='175' ".($isCreator? "onclick='editImgSrc(this)'":"")."/></div>";
		echo "<div class='name_user_game' ><h2><span id='name' ".($isCreator?"contenteditable='true'":"").">".$name."</span> - <span id='type' ".($isCreator?"contenteditable='true'":"").">".stripslashes($type).(stripslashes(($s_type))? " </span>(<span id='subtype' title='Sub Type' ".($isCreator?"contenteditable='true'":"").">".$s_type."</span>)":"")."</h2>";
		echo "<h3>submitted by <a href=profile.php?user=".$user.">".$user."</a></h3><h3 id='game'>".stripslashes($game)."</h3></div>";
		if($num_ratings>0){
			echo "<table class='rating_table'><tr><td><b>Fun</b></td><td><span class='stars'>".$avgFun."</span></td></tr><tr><td><b>Balance</b></td><td><span class='stars'>".$avgBalance."</span></td></tr></table>";
		}else{
			echo "<b>Not yet rated</b>";
		}
		echo "<div id='body' style='display: block; clear: both;'><h4 style='margin-bottom: .1em; padding-bottom: 0em'>Description</h4>";
		echo "<div id='desc' style='margin-top: .1em' ".($isCreator?"contenteditable='true'":"").">".stripslashes($desc)."</div>";
		$num=1;
		foreach($fields as $key => $value){
			echo "<h4 id='label ".$num."' name='label ".$num."' style='margin-bottom: .1em; padding-bottom: 0em' ".($isCreator?"contenteditable='true'":"").">".stripslashes($value->label)."</h4>";
			echo "<div id='text ".$num."' name='text ".$num."' style='margin-top: .1em' ".($isCreator?"contenteditable='true'":"").">".stripslashes($value->text)."</div>";
			$num++;
		}
		echo "</div></div>";
        echo "<h6>Contribution ID: <span  id='contid'>".$id."</span></h6>";
		echo "<span id='num_extras' style='display: none'>".$num."</span>";
    }catch(Exception $e)
    {
		echo "We appear to have rolled a natural 1... *sigh* Copy the following error message and submit it to us <a href=''>here</a>:</br>".$e;
    }
	
	if($isCreator){
		echo "<button onclick='addField()'>Add Field</button></br>";
	}
?>

	<span><button id="comments_tab" onclick="showComments()" disabled="true">Comments</button><button id="ratings_tab" onclick="showRatings()">Ratings</button></span>
	</br></br>
	<div id='comments'>
		<?php
	/********************************
		Display Comments
	*********************************/
			if($_SESSION["username"]){
				/*echo "<form id='make_comment'>
					<textarea id='comment' contenteditable='true' rows='5' cols='50' placeholder='Enter comment here.' required ></textarea>
					</form><script>CKEDITOR.replace( 'comment' );</script>*/
				echo "<label for='isRichText'>Use rich text</label><input id='isRichText' type='checkbox' onChange='switchCommentType(this)'/></br>";
				echo "<textarea id='comment' rows='5' cols='40' placeholder='Enter comment here'></textarea></br>
					<a class='button' onclick='comment();' id='submit_comment'>Submit</a>";
			}else{
				echo "You must login before you can comment.";
			}
			//$result=$mysql->query("SELECT * from contribution_comments WHERE contribution_id =".$id." ORDER BY timestamp DESC");
			$stmt = $mysql->prepare("SELECT username, timestamp, comment FROM contribution_comments WHERE contribution_id = ? ORDER BY timestamp DESC");
			$stmt->bind_param("i", $id);
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}
			$u=null; $t=null; $c=null;
			$stmt->bind_result($u, $t, $c);
			while($stmt->fetch()){
				$row["username"] = htmlspecialchars($u, ENT_QUOTES, "UTF-8");
				$row["timestamp"] = $t;
				$row["comment"] = htmlspecialchars($c, ENT_QUOTES, "UTF-8");
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

					echo "<div class='comment'><a href='profile.php?user=".$row["username"]."'><img src='".$img."' alt='".$row["username"]."&#39s profile picture' width='50' height='50' style='float: left;'><div id='namedate'><h4 style='margin-top:.4em; margin-bottom: .2em;'>".$row["username"]."</h4></a>";
					echo "<h5 style='margin-top: .2em; margin-bottom: .4em;'>".date('F j, Y g:i A',strtotime($row["timestamp"]))."</h5></div></br>";
					echo "<p style=' margin: 0em;'>".$row["comment"]."</p>";
					//echo "<a class='button' onclick=''>Delete</a>";
					echo "</div>";
				}
			}
			unset($row);
			unset($rowarr);
			
		?>
	</div>
	<div id="ratings" style="display:none;">
		<?php
	/********************************
		Display Ratings
	*********************************/
			if($_SESSION["username"]){
				//$rating_check=$mysql->query("SELECT * FROM ratings WHERE contribution_id=".$id." AND username='".$_SESSION["username"]."'");
				$stmt = $mysql->prepare("SELECT id FROM ratings WHERE contribution_id=? AND username=?");
				$stmt->bind_param("is", $id, $_SESSION["username"]);
				if(!$stmt->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}
				$i=null;
				$stmt->bind_result($i);
				$stmt->fetch();
				$stmt->close();
				if($i){
					echo "<b>You have already rated this contribution</b>";
				}else{
					echo "<a class='button' onclick='rate()'>Rate!</a></br>";
				}
			}
			//$result=$mysql->query("SELECT * FROM ratings WHERE contribution_id =".$id." ORDER BY timestamp DESC");
			$stmt = $mysql->prepare("SELECT username, timestamp, comment, fun, balance FROM ratings WHERE contribution_id=? ORDER BY timestamp DESC");
			$stmt->bind_param("i", $id);
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}
			$u=null; $t=null; $c=null; $f=0; $b=0;
			$stmt->bind_result($u, $t, $c, $f, $b);
			while($stmt->fetch()){
				$row["username"] = htmlspecialchars($u, ENT_QUOTES, "UTF-8");
				$row["timestamp"] = $t;
				$row["comment"] = htmlspecialchars($c, ENT_QUOTES, "UTF-8");
				$row["fun"] = $f;
				$row["balance"] = $b;
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
					echo "<div class='comment'><a href='profile.php?user=".$row["username"]."'><img src='".$img."' alt='".$row["username"]."&#39s profile picture' width='50' height='50' style='float: left;'><div id='namedate'><h4 style='margin-top:.4em; margin-bottom: .2em;'>".$row["username"]."</h4></a>";
					echo "<h5 style='margin-top: .2em; margin-bottom: .4em;'>".date('F j, Y g:i A',strtotime($row["timestamp"]))."</h5></div></br>";
					echo "<table class='rating_table'><tr><td><b>Fun</b></td><td><span class='stars'>".$row["fun"]."</span></td></tr>
						<tr><td><b>Balance</b></td><td><span class='stars'>".$row["balance"]."</span></td></tr></table>";
					echo "<p style=' margin: 0em;'>".$row["comment"]."</p></div>";
				}
			}

			unset($row);
			unset($rowarr);
		?>
	</div>
</div>
</body>
</html>

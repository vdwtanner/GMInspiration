<?php
    session_start();
?>
<DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="initial-scale=1">
	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all">
	<?php include "header.php";?>
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
			background-color: #EDF556;
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
	</style>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">

	<!--Begin jquery scripts-->
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
	<script src="http://cdn.jsdelivr.net/jquery.validation/1.13.1/jquery.validate.min.js"></script><!--Including some JQuery because yes-->
	<!--End jquery scripts-->


	<!--Begin our scripts-->
	<script src="scripts/ajaxMsgFunctions.js"></script>
	<script type="text/javascript" language="javascript">
		function comment(){
			var cid = $("#contribution_id").text();
			var username = $("#user").text();
			var com = $("#comment").val();
			var form = $("#make_comment");
			var contributer = $("#contributer").text();
			var contribution_name = $("#contribution_name").text();
			if(!com){
				form.submit();
			}else{
				$.ajax({
					url: "ajax_comment.php",
					type: "POST",
					data: ({
						c_id: cid,
						user: username,
						comment: com,
					}),
					//Expect to receive HTML back
					success: function(html){
						//alert(data);
						//data is whatever is returned from php
						//alert("comment added");
						//Come back later and make this add the new comment
						//var newdiv = document.createElement("div");
						//newdiv.innerHTML=data;
						//var comments = $("#comments");
						try{
							//$("#comments").prepend(html);
							$(html).insertAfter("#submit_comment");
							$("#comment").val("");
							//comments.insertBefore(html, comments.firstChild);
							//alert(1);
							if(username != contributer)
								sendMsg("DungeonCrawlers",
									username+" commented on one of your contributions!",
									username+"commented on your "+contribution_name+"\n\n\""+com+"\"\n",
									contributer);
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
		
		/*function submitUpdate(){
			var id=$("#contid").text();
			var game=$("#name").val();
			var type=$("#type").val();
			var subtype=$("#desc").val();
			var img=$("#").attr("src");
			
		}*/
		
		function update(){
			var name=$("#name").text();
			var game=$("#game").text();
			var type=$("#type").text();
			var subtype=$("#subtype").text();
			var desc=$("#desc").text();
			var img=$("#img").attr("src");
			var labels=[];
			$("[name*='label ']").each(function() {labels.push($(this).text())});
			var texts=[];
			$("[name*='text ']").each(function() {texts.push($(this).text())});
			console.log(name);
			console.log(game);
			console.log(type);
			console.log(subtype);
			console.log(desc);
			console.log(img);
			console.log(labels);
			console.log(texts);
			$.ajax({
				url: "contribute.php",
				success: function(html){
					var p = document.createElement("p");
					p.innerHTML="<b>Select a contribution type. For some reason that does not load right now.</b>"
					$("#update_button").replaceWith(p);
					var h2 = document.createElement("h2");
					h2.innerHTML="Update Contribution"
					$("#page_title").replaceWith(h2);
					$("#contribution").html(html);
					$("#name").val(name);
					$("#game").val(game);
					$("#type").filter(function() {
						//may want to use $.trim in here
						return $(this).text() == type; 
					}).prop('selected', true);
					$("#type").val(type);
					$("#Sub_type").val(subtype);
					$("#desc").val(desc);
					$("#img").val(img);
					$("#contribute").attr("action", "update_contribution.php");
					$("#contribute").append("<input id='id' name='id' style='display: none;' value='"+$("#contid").text()+"' />");
					$("#submit_contribution").text("Update");
					$("#lore").remove();
					$("#how").remove();
					$("#effect").remove();
					$("#attack").remove();
					var extras=0;
					labels.forEach(function(label){
						$("#add_button").click();
						var div = document.getElementById("extra");
						var newdiv = document.createElement("div");
						extras++;
						newdiv.id=extras;
						newdiv.innerHTML = "<input id='label_"+extras+"' name='label_"+extras+"' type='text' style='vertical-align: top' placeholder='Enter label here' /><textarea id='text_"+extras+"' name='text_"+extras+"' placeholder='Enter extra info here' rows='5' cols='50'></textarea><a class='button' onclick='removeField(this.parentNode)'>Delete</a></br>";
						div.appendChild(newdiv);
						$("#label_"+extras).val(labels[extras-1]);
						$("#text_"+extras).val(texts[extras-1]);
					});
					
				},
				error: function(xhr, status, error){
					alert(error);
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
	</script>

	<!--End our scripts-->
</head>
<body>
<div id="container" class="cf">
<?php
    //echo "Hello ".$_SESSION("username");
    $mysql = new mysqli("mysql14.000webhost.com","a9044814_crawler","d&d4days", "a9044814_dungeon");
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

    try{
        $mysql->query("START TRANSACTION");
		//$ratings=$mysql->query("SELECT COUNT(*), SUM(fun), SUM(balance) FROM ratings WHERE contribution_id=".$id);
		$stmt = $mysql->prepare("SELECT COUNT(*), SUM(fun), SUM(balance) FROM ratings WHERE contribution_id=?");
		$stmt->bind_param("i", $id);
		if(!$stmt->execute()){
			echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
		}
		$row0 = 0; $row1 = 0; $row2 = 0;
		$stmt->bind_result($row0, $row1, $row2);
		$stmt->fetch();
		$stmt->close();

		$num_ratings=$row0;
		if($row0>0){
			$fun=$row1/$row0;
			$balance=$row2/$row0;
		}
        //$result = $mysql->query("SELECT * from contributions where id='".$id."'");
	$stmt = $mysql->prepare("SELECT username, name, `type`, sub_type, game, `desc`, img, json FROM contributions WHERE id=?");
	$stmt->bind_param("i", $id);
	if(!$stmt->execute()){
		echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
	}
	$username=null; $name=null; $json=null; $img=null; $type=null; $sub_type=null; $game=null; $desc=null;	
	$stmt->bind_result($username, $name, $type, $sub_type, $game, $desc, $img, $json);
	$stmt->fetch();
	$stmt->close();

	echo "<p id='contributer' style='display: none;'>".$username."</p>";
	echo "<p id='contribution_name' style='display: none;'>".$name."</p>";
        $fields = json_decode($json);    //create associative array from json
	//echo print_r($row);
	if($username==$_SESSION["username"]){
		echo "<a id='update_button' class='button' onclick='update()'>Update</a></br>";
	}	
	echo "<div id='contribution'><div class='profile_img'><img id='img' src='".$img."' alt='An image depicting ".$name."' width='175' height='175' /></div>";
	echo "<div class='name_user_game' ><h2><span id='name'>".$name."</span> - <span id='type'>".$type.(($sub_type)? " </span>(<span id='subtype' title='Sub Type'>".$sub_type."</span>)":"")."</h2>";
	echo "<h3>submitted by <a href=profile.php?user=".$username.">".$username."</a></h3><h3 id='game'>".$game."</h3></div>";
	if($num_ratings>0){
		echo "<table class='rating_table'><tr><td><b>Fun</b></td><td><span class='stars'>".$fun."</span></td></tr><tr><td><b>Balance</b></td><td><span class='stars'>".$balance."</span></td></tr></table>";
	}else{
		echo "<b>Not yet rated</b>";
	}
	echo "<div style='display: block; clear: both;'><h4 style='margin-bottom: .1em; padding-bottom: 0em'>Description</h4>";
	echo "<p id='desc' style='margin-top: .1em'>".$desc."</p>";
	$num=1;
	foreach($fields as $key => $value){
		echo "<h4 id='label ".$num."' name='label ".$num."' style='margin-bottom: .1em; padding-bottom: 0em'>".$key."</h4>";
		echo "<p id='text ".$num."' name='text ".$num."' style='margin-top: .1em'>".$value."</p>";
		$num++;
	}
	echo "</div></div>";
        echo "<h6>Contribution ID: <span  id='contid'>".$id."</span></h6>";
    }catch(Exception $e)
    {
	$mysql->rollback();
	echo "We appear to have rolled a natural 1... *sigh* Copy the following error message and submit it to us <a href=''>here</a>:</br>".$e;
    }
	
?>
	<span><button id="comments_tab" onclick="showComments()" disabled="true">Comments</button><button id="ratings_tab" onclick="showRatings()">Ratings</button></span>
	<div id='comments'>
		<?php
			if($_SESSION["username"]){
				echo "<form id='make_comment'>
					<textarea id='comment' rows='5' cols='50' placeholder='Enter comment here.' required ></textarea>
					</form>
					<a class='button' onclick='comment();' id='submit_comment'>Submit</a>";
			}else{
				echo "You must <a href='login.html'>login</a> before you can comment.";
			}
			//$result=$mysql->query("SELECT * from contribution_comments WHERE contribution_id =".$id." ORDER BY timestamp DESC");
			$stmt = $mysql->prepare("SELECT username, comment, timestamp FROM contribution_comments WHERE contribution_id=? ORDER BY timestamp DESC");
			$stmt->bind_param("i", $id);
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}
			$username = null; $comment = null; $timestamp = null;
			$stmt->bind_result($username, $comment, $timestamp);
			$stmt->fetch();

			while($stmt->fetch()){
				//$result2 = $mysql->query("SELECT picture from users WHERE username='".$row["username"]."'");
				$stmt2 = $mysql->prepare("SELECT picture FROM users WHERE username=?");
				$stmt2->bind_param("s", $username);
				if(!$stmt2->execute()){
					echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
				}
				$picture=null;
				$stmt2->bind_result($picture);
				$stmt2->fetch();
				$stmt2->close();
				echo "<div class='comment'><a href='profile.php?user=".$username."'><img src='".$picture."' alt='".$username."&#39s profile picture' width='50' height='50' style='float: left;'><div id='namedate'><h4 style='margin-top:.4em; margin-bottom: .2em;'>".$username."</h4></a>";
				echo "<h5 style='margin-top: .2em; margin-bottom: .4em;'>".date('F j, Y g:i A',strtotime($timestamp))."</h5></div></br>";
				echo "<p style=' margin: 0em;'>".$comment."</p></div>";
			}
			$stmt->close();
		?>
	</div>
	<div id="ratings" style="display:none;">
		<?php
			if($_SESSION["username"]){
				$rating_check=$mysql->query("SELECT * FROM ratings WHERE contribution_id=".$id." AND username='".$_SESSION["username"]."'");	// START HERE
				if(count($rating_check->fetch_array(MYSQL_BOTH))>0){
					echo "<b>You have already rated this contribution</b>";
				}else{
					echo "<a class='button' onclick='rate()'>Rate!</a></br>";
				}
			}
			$result=$mysql->query("SELECT * FROM ratings WHERE contribution_id =".$id." ORDER BY timestamp DESC");
			while($row=$result->fetch_array(MYSQL_BOTH)){
				$result2 = $mysql->query("SELECT picture from users WHERE username='".$row["username"]."'");
				$img=$result2->fetch_array(MYSQL_BOTH);
				echo "<div class='comment'><a href='profile.php?user=".$row["username"]."'><img src='".$img["picture"]."' alt='".$row["username"]."&#39s profile picture' width='50' height='50' style='float: left;'><div id='namedate'><h4 style='margin-top:.4em; margin-bottom: .2em;'>".$row["username"]."</h4></a>";
				echo "<h5 style='margin-top: .2em; margin-bottom: .4em;'>".date('F j, Y g:i A',strtotime($row["timestamp"]))."</h5></div></br>";
				echo "<table class='rating_table'><tr><td><b>Fun</b></td><td><span class='stars'>".$fun."</span></td></tr>
					<tr><td><b>Balance</b></td><td><span class='stars'>".$balance."</span></td></tr></table>";
				echo "<p style=' margin: 0em;'>".$row["comment"]."</p></div>";
			}
		?>
	</div>
</div>
</body>
</html>

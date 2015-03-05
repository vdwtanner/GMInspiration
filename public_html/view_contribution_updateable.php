<?php
    session_start();
	require dirname(__FILE__)."/scripts/parser.php";
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
	<link href="scripts/ckeditor/samples/sample.css" rel="stylesheet">
	<script type="text/javascript" language="javascript">
		function comment(){
			var cid = $("#contribution_id").text();
			var username = $("#user").text();
			var com = $("#comment").html();
			var form = $("#make_comment");
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
			var id=$("#contribution_id").text();
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
			var json='{';
			for(var x=0; x<labels.length;x++){
				if(x>0){
					json+=',';
				}
				json+='"'+labels[x]+'":"'+texts[x]+'"';
			}
			json+='}';
			console.log("id=" + id);
			console.log(name);
			console.log(game);
			console.log(type);
			console.log(subtype);
			console.log(desc);
			console.log(img);
			console.log(labels);
			console.log(texts);
			console.log(json);
			$.ajax({
				url: "update_contribution2.php",
				type: "POST",
				data: ({
					id: id,
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
	</script>
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
	$isCreator=false;
	
    try{
        $mysql->query("START TRANSACTION");
		$ratings=$mysql->query("SELECT COUNT(*), SUM(fun), SUM(balance) FROM ratings WHERE contribution_id=".$id);
		$row=$ratings->fetch_array(MYSQL_BOTH);
		//print_r($row);
		$num_ratings=$row[0];
		if($row[0]>0){
			$fun=$row[1]/$row[0];
			$balance=$row[2]/$row[0];
		}
        $result = $mysql->query("SELECT * from contributions where id='".$id."'");
        $row = $result->fetch_array(MYSQL_BOTH);
        $fields = json_decode(stripslashes($row["json"]));    //create associative array from json
		//echo print_r($row);
		if($row["username"]==$_SESSION["username"]){
			echo "<a id='update_button' class='button' onclick='update()'>Save Changes</a></br>";
			$isCreator=true;
		}	
		echo "<div id='contribution'><div class='profile_img'><img id='img' src='".$row["img"]."' alt='An image depicting ".$row["name"]."' width='175' height='175' /></div>";
		echo "<div class='name_user_game' ><h2><span id='name' ".($isCreator?"contenteditable='true'":"").">".$row["name"]."</span> - <span id='type' ".($isCreator?"contenteditable='true'":"").">".stripslashes($row["type"]).(stripslashes(($row["sub_type"]))? " </span>(<span id='subtype' title='Sub Type' ".($isCreator?"contenteditable='true'":"").">".$row["sub_type"]."</span>)":"")."</h2>";
		echo "<h3>submitted by <a href=profile.php?user=".$row["username"].">".$row["username"]."</a></h3><h3 id='game'>".stripslashes($row["game"])."</h3></div>";
		if($num_ratings>0){
			echo "<table class='rating_table'><tr><td><b>Fun</b></td><td><span class='stars'>".$fun."</span></td></tr><tr><td><b>Balance</b></td><td><span class='stars'>".$balance."</span></td></tr></table>";
		}else{
			echo "<b>Not yet rated</b>";
		}
		echo "<div id='body' style='display: block; clear: both;'><h4 style='margin-bottom: .1em; padding-bottom: 0em'>Description</h4>";
		echo "<div id='desc' style='margin-top: .1em' ".($isCreator?"contenteditable='true'":"").">".stripslashes($row["desc"])."</div>";
		$num=1;
		foreach($fields as $key => $value){
			echo "<h4 id='label ".$num."' name='label ".$num."' style='margin-bottom: .1em; padding-bottom: 0em' ".($isCreator?"contenteditable='true'":"").">".stripslashes($key)."</h4>";
			echo "<div id='text ".$num."' name='text ".$num."' style='margin-top: .1em' ".($isCreator?"contenteditable='true'":"").">".stripslashes($value)."</div>";
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
			if($_SESSION["username"]){
				/*echo "<form id='make_comment'>
					<textarea id='comment' contenteditable='true' rows='5' cols='50' placeholder='Enter comment here.' required ></textarea>
					</form><script>CKEDITOR.replace( 'comment' );</script>*/
					
				echo "<div id='comment' contenteditable='true'>Enter comment here</div>
					<script>
						var editor=CKEDITOR.replace( 'comment' );
						editor.on('change', function(event){
							console.log('Total bytes: '+event.editor.getData().length);
							$('#comment').html(event.editor.getData());
						});
					</script></br>
					<a class='button' onclick='comment();' id='submit_comment'>Submit</a>";
			}else{
				echo "You must <a href='login.html'>login</a> before you can comment.";
			}
			$result->free();
			$result=$mysql->query("SELECT * from contribution_comments WHERE contribution_id =".$id." ORDER BY timestamp DESC");
			while($row=$result->fetch_array(MYSQL_BOTH)){
				$result2 = $mysql->query("SELECT picture from users WHERE username='".$row["username"]."'");
				$img=$result2->fetch_array(MYSQL_BOTH);
				echo "<div class='comment'><a href='profile.php?user=".$row["username"]."'><img src='".$img["picture"]."' alt='".$row["username"]."&#39s profile picture' width='50' height='50' style='float: left;'><div id='namedate'><h4 style='margin-top:.4em; margin-bottom: .2em;'>".$row["username"]."</h4></a>";
				echo "<h5 style='margin-top: .2em; margin-bottom: .4em;'>".date('F j, Y g:i A',strtotime($row["timestamp"]))."</h5></div></br>";
				echo "<p style=' margin: 0em;'>".stripslashes($row["comment"])."</p></div>";
			}
		?>
	</div>
	<div id="ratings" style="display:none;">
		<?php
			if($_SESSION["username"]){
				$rating_check=$mysql->query("SELECT * FROM ratings WHERE contribution_id=".$id." AND username='".$_SESSION["username"]."'");
				if(count($rating_check->fetch_array(MYSQL_BOTH))>0){
					echo "<b>You have already rated this contribution</b>";
				}else{
					echo "<a class='button' onclick='rate()'>Rate!</a></br>";
				}
			}
			$result->free();
			$result=$mysql->query("SELECT * FROM ratings WHERE contribution_id =".$id." ORDER BY timestamp DESC");
			while($row=$result->fetch_array(MYSQL_BOTH)){
				$result2 = $mysql->query("SELECT picture from users WHERE username='".$row["username"]."'");
				$img=$result2->fetch_array(MYSQL_BOTH);
				echo "<div class='comment'><a href='profile.php?user=".$row["username"]."'><img src='".$img["picture"]."' alt='".$row["username"]."&#39s profile picture' width='50' height='50' style='float: left;'><div id='namedate'><h4 style='margin-top:.4em; margin-bottom: .2em;'>".$row["username"]."</h4></a>";
				echo "<h5 style='margin-top: .2em; margin-bottom: .4em;'>".date('F j, Y g:i A',strtotime($row["timestamp"]))."</h5></div></br>";
				echo "<table class='rating_table'><tr><td><b>Fun</b></td><td><span class='stars'>".$fun."</span></td></tr>
					<tr><td><b>Balance</b></td><td><span class='stars'>".$balance."</span></td></tr></table>";
				echo "<p style=' margin: 0em;'>".stripslashes($row["comment"])."</p></div>";
			}
		?>
	</div>
</div>
</body>
</html>

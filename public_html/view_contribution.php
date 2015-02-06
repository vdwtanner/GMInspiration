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
	</style>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
	<script src="http://cdn.jsdelivr.net/jquery.validation/1.13.1/jquery.validate.min.js"></script><!--Including some JQuery because yes-->
	<script type="text/javascript" language="javascript">
		function comment(){
			var cid = $("#contribution_id").text();
			var username = $("#user").text();
			var com = $("#comment").val();
			var form = $("#make_comment");
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
							$("#comments").prepend(html);
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
    try{
        $mysql->query("START TRANSACTION");
        $result = $mysql->query("SELECT * from contributions where id='".$id."'");
        $row = $result->fetch_array(MYSQL_BOTH);
        $fields = json_decode($row["json"]);    //create associative array from json
		//echo print_r($row);
		if($row["username"]==$_SESSION["username"]){
			echo "<a id='update_button' class='button' onclick='update()'>Update</a></br>";
		}
			echo "<div id='contribution'><div class='profile_img'><img id='img' src='".$row["img"]."' alt='An image depicting ".$row["name"]."' width='175' height='175' /></div>";
			echo "<div class='name_user_game' ><h2><span id='name'>".$row["name"]."</span> - <span id='type'>".$row["type"].(($row["sub_type"])? " </span>(<span id='subtype' title='Sub Type'>".$row["sub_type"]."</span>)":"")."</h2>";
			echo "<h3>submitted by <a href=profile.php?user=".$row["username"].">".$row["username"]."</a></h3><h3 id='game'>".$row["game"]."</h3></div>";
			echo "<div style='display: block; clear: both;'><h4 style='margin-bottom: .1em; padding-bottom: 0em'>Description</h4>";
			echo "<p id='desc' style='margin-top: .1em'>".$row["desc"]."</p>";
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
		echo "We appear to have rolled a natural 1... *sigh* Copy the following error message and submit it to us <a href=''>here</a>:</br>".$e;
    }
?>
	<h3>Comment</h3>
	<?php
		if($_SESSION["username"]){
			echo "<form id='make_comment'>
				<textarea id='comment' rows='5' cols='50' placeholder='Enter comment here.' required ></textarea>
				</form>
				<a class='button' onclick='comment();'>Submit</a>";
		}else{
			echo "You must <a href='login.html'>login</a> before you can comment.";
		}
	?>
	<div id='comments'>
		<?php
			$result->free();
			$result=$mysql->query("SELECT * from contribution_comments WHERE contribution_id =".$id." ORDER BY timestamp DESC");
			while($row=$result->fetch_array(MYSQL_BOTH)){
				$result2 = $mysql->query("SELECT picture from users WHERE username='".$row["username"]."'");
				$img=$result2->fetch_array(MYSQL_BOTH);
				echo "<div class='comment'><a href='profile.php?user=".$row["username"]."'><img src='".$img["picture"]."' alt='".$row["username"]."&#39s profile picture' width='50' height='50' style='float: left;'><div id='namedate'><h4 style='margin-top:.4em; margin-bottom: .2em;'>".$row["username"]."</h4></a>";
				echo "<h5 style='margin-top: .2em; margin-bottom: .4em;'>".date('F j, Y g:i A',strtotime($row["timestamp"]))."</h5></div></br>";
				echo "<p style=' margin: 0em;'>".$row["comment"]."</p></div>";
			}
		?>
	</div>
</div>
</body>
</html>

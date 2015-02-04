<?php
    session_start();
?>
<DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all and (min-width: 33.236em)">
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
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script><!--Including some JQuery because yes-->
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
	</script>
</head>
<body>
<div id="container">
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
		if($row["username"]!=$_SESSION["username"]){
			echo "<div class='img' style='float: left'><img href='".$row["img"]."' alt='An image depicting ".$row["name"]."' /></div>";
			echo "<h2>".$row["name"]." - ".$row["type"].(($row["sub_type"])? " <span title='Sub Type'>(".$row["sub_type"].")":"")."</span></h2>";
			echo "<h3>submitted by ".$row["username"]." for ".$row["game"]."</h3>";
			echo "<h4 style='margin-bottom: .1em; padding-bottom: 0em'>Description</h4>";
			echo "<p style='margin-top: .1em'>".$row["desc"]."</p>";
			foreach($fields as $key => $value){
				echo "<h4 style='margin-bottom: .1em; padding-bottom: 0em'>".$key."</h4>";
				echo "<p style='margin-top: .1em'>".$value."</p>";
			}
		}else{
			echo "<h2>This page is still under construction. Submit updates at your own risk.</h2>";
			echo "<p id='cont_type' style='display: none;'>".$row["type"]."</p>";
			echo "<script type='text/javascript' language='javascript'>
				$(document).ready(function(){
					$('$type').value=$('#cont_type').text();
				});</script>";
			echo "<form method='POST' action='update_contribution.php'>";
			echo "<div class='img' style='float: left'><img href='".$row["img"]."' alt='An image depicting ".$row["name"]."' /></div></br>";
			echo "<label for='name'>Name: </label><input id='name' name='name' type='text' value='".$row['name']."' maxlength='75' title='Name of contribution'/></br>";
			echo "<label for='type'>Contribution Type:</label><select id='type' name='type' required value='".$row["type"]."'>
				<option value='Weapon'>Weapon</option>
				<option value='Spell'>Spell</option>
				<option value='Consumable'>Consumable</option>
				<option value='Crafting'>Crafting</option>
				<option value='Feat'>Feat</option>
				<option value='Artifact'>Artifact</option>
				<option value='Tool'>Tool</option>
				</select></br>";
			echo "<label for='sub_type'>Sub Type: </label><input id='sub_type' name='sub_type' value='".$row["sub_type"]."' />";
			echo "<h4 style='margin-bottom: .1em; padding-bottom: 0em' >Description</h4>";
			echo "<textarea id='desc' name='desc' rows='5' cols='50' >".$row["desc"]."</textarea>";
			foreach($fields as $key => $value){
				echo "<h4 style='margin-bottom: .1em; padding-bottom: 0em'>".$key."</h4>";
				echo "<textarea id='".$key."' name='".$key."' style='margin-top: .1em' rows='5' cols='50'>".$value."</textarea>";
			}
			echo "</br><input id='id' name='id' type='text' readonly style='display: none;' value='".$row["id"]."' size='".strlen($row["id"])."'/></br>";
			echo "<input type='submit' value='Update contribution' />";
			echo "</form>";
		}
        echo "<h6>Contribution ID: ".$id."</h6>";
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
				</form>;
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
				echo "<div class='comment'><img src='".$img["picture"]."' alt='".$row["username"]."&#39s profile picture' width='50' height='50' style='float: left;'><div id='namedate'><h4 style='margin-top:.4em; margin-bottom: .2em;'>".$row["username"]."</h4>";
				echo "<h5 style='margin-top: .2em; margin-bottom: .4em;'>".date('F j, Y g:i A',strtotime($row["timestamp"]))."</h5></div></br>";
				echo "<p style=' margin: 0em;'>".$row["comment"]."</p></div>";
			}
		?>
	</div>
</div>
</body>
</html>

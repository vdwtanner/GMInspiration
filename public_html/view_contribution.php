<?php
    session_start();
?>
<DOCTYPE html>
<html>
<head>
</head>
<body>
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
			echo "<form method='POST' action='update_contribution.php'>";
			echo "<div class='img' style='float: left'><img href='".$row["img"]."' alt='An image depicting ".$row["name"]."' /></br></div>";
			echo "<label for='name'>Name: </label><input id='name' name='name' type='text' value='".$row['name']."' maxlength='75' title='Name of contribution'/></br>";
			echo "<label for='sub_type'>Sub Type: </label><input id='sub_type' name='sub_type' value='".$row["sub_type"]."' />";
			echo "<h4 style='margin-bottom: .1em; padding-bottom: 0em>Description</h4>";
			echo "<textarea id='desc' name='desc' rows='5' cols='50' >".$row["desc"]."</textarea>";
			foreach($fields as $key => $value){
				echo "<h4 style='margin-bottom: .1em; padding-bottom: 0em'>".$key."</h4>";
				echo "<textarea id='".$key."' name='".$key."' style='margin-top: .1em' rows='5' cols='50'>".$value."</textarea>";
			}
			echo "</br><input id='id' name='id' type='text' readonly value='".$row["id"]."' size='".strlen($row["id"])."'/></br>";
			echo "<input type='submit' value='Update contribution' />";
			echo "</form>";
		}
        echo "<h6>Contribution ID: ".$id."</h6>";
    }catch(Exception $e)
    {
		echo "We appear to have rolled a natural 1... *sigh* Copy the following error message and submit it to us <a href=''>here</a>:</br>".$e;
    }
?>
</body>
</html>

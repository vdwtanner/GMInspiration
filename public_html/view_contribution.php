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
    if($_GET["contid"])
	$id=$_GET["contid"];
    else
	$id=$_POST["id"];

    try{
        $mysql->query("START TRANSACTION");
        $result = $mysql->query("SELECT * from contributions where id='".$id."'");
        $row = $result->fetch_array(MYSQL_BOTH);
        $fields = json_decode($row["json"]);    //create associative array from json
        echo "<div class='img' style='float: left'><img href='".$row["img"]."' alt='An image depicting ".$row["name"]."' /></div>";
        echo "<h2>".$row["name"]."</h2>";
        echo "<h3>submitted by ".$row["username"]." for ".$row["game"]."</h3>";
        echo "<h4>Description</h4>";
        echo "<p>".$row["desc"]."</p>";
        foreach($fields as $key => $value){
			echo "<h4>".$key."</h4>";
			echo "<p>".$value."</p>";
		}
        echo "<h6>Contribution ID: ".$id."</h6>";
    }catch(Exception $e)
    {
    
    }
?>
</body>
</html>

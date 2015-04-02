<?php include "header.php";?>
<DOCTYPE html>
<html>
<head>
	<title>The GM's Inspiration</title>
	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all and (min-width: 33.236em)">
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
	<script src="http://cdn.jsdelivr.net/jquery.validation/1.13.1/jquery.validate.min.js"></script>
  	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

	<script type="text/javascript">
		
		var step = 0;

		function slideForwards(numSlides){
			if(!document.images)
				return;

			$("#slide"+step).fadeOut(2000, function(){
				step++;
				if(step >= parseInt(numSlides))
					step = 0;		
				$("#slide"+step).fadeIn(2000).css("display", "inline-block");
			});

		}
		function slideBackwards(numSlides){
			if(!document.images)
				return;

			$("#slide"+step).fadeOut(2000, function(){
				step--;
				if(step == 0)
					step = parseInt(numSlides);		
				$("#slide"+step).fadeIn(2000).css("display", "inline-block");
			});

		}

	</script>
</head>
<body>
<div id="container" class="cf">

	<div id="slideshow">
	<?php
		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if($mysql->connect_error){
			die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
		}
		try{
			$stmt = $mysql->prepare("SELECT id, username, name, type, sub_type, game, img, avg_fun, avg_balance FROM contributions");
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}
			$id = null; $u = null; $n = null; $t = null; $st = null; $g = null; $i = null; $af = null; $ab = null;
			$stmt->bind_result($id, $u, $n, $t, $st, $g, $i, $af, $ab);
			while($stmt->fetch()){
				$row["id"] = $id;
				$row["username"] = $u;
				$row["name"] = $n;
				$row["type"] = $t;
				$row["sub_type"] = $st;
				$row["game"] = $g;
				$row["img"] = $i;
				$row["avg_fun"] = $af;
				$row["avg_balance"] = $ab;

				$rowarr[] = $row;
			}

			$count = 0;
			if($rowarr)
				foreach($rowarr as $key => $row){
					if($count == 0)
						echo "<a id='slide".$count."' style='display: inline-block' href='view_contribution_updateable.php?contid=".$row["id"]."'>";
					else
						echo "<a id='slide".$count."' style='display: none' href='view_contribution_updateable.php?contid=".$row["id"]."'>";
					echo "<div style='float:left;'>";
					echo "<img border='0' alt='".$name."' src='".$row["img"]."' width='200' height='200' style='inline-block'>";
					echo "<div style='float:right; display:inline-block'>";
					echo "<h2>".$row["name"]." - ".$row["type"]." (".$row["sub_type"].")</h2>";
					echo "<h3>For ".$row["game"]."</h3>";
					echo "<h3>By ".$row["username"]."</h3>";
					echo "</div>";
					echo "</div>";
					echo "</a>";
					$count++;
				}

			echo "<script type='text/javascript'> setInterval('slideForwards(".$count.")', 10000); </script>";

			$stmt->close();
			$mysql->commit();
		}catch(Exception $e){
			$mysql->rollback();
		}
	?>
	</div>
</div>
</body>
</html>

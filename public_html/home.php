<?php include "header.php";?>
<DOCTYPE html>
<html>
<head>
	<title>The GM's Inspiration</title>
	<link rel="stylesheet" href="css/example/global.css" media="all">
	<link rel="stylesheet" href="css/example/layout.css" media="all and (min-width: 33.236em)">
	<style>
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
<div id="container" class="cf">
	<!--<a href="contribute.php"><button style="border-radius: 10px;">Contribute!</button></a>-->

	<!--<div class="featureTitle">
		<p style='margin: 0em; padding: 0em'>Top Contributions</p>
	</div>-->
	<!--<div class="hotContributions">-->
	<ul id="quick_search" class="quick_search">
		<li id="classes"><a href="http://gminspiration.com/tanner/search_results.php?keywords=class&usort=relevance&csort=relevance&searchSubmit=Search">classes</a></li>
		<li id="feats"><a href="http://gminspiration.com/tanner/search_results.php?keywords=feat&usort=relevance&csort=relevance&searchSubmit=Search">feats</a></li>
		<li id="items"><a href="http://gminspiration.com/tanner/search_results.php?keywords=item&usort=relevance&csort=relevance&searchSubmit=Search">items</a></li>
		<li id="monsters"><a href="http://gminspiration.com/tanner/search_results.php?keywords=monster&usort=relevance&csort=relevance&searchSubmit=Search">monsters</a></li>
		<li id="races"><a href="http://gminspiration.com/tanner/search_results.php?keywords=race&usort=relevance&csort=relevance&searchSubmit=Search">races</a></li>
		<li id="spells"><a href="http://gminspiration.com/tanner/search_results.php?keywords=spell&usort=relevance&csort=relevance&searchSubmit=Search">spells</a></li>
		<li id="weapons"><a href="http://gminspiration.com/tanner/search_results.php?keywords=weapon&usort=relevance&csort=relevance&searchSubmit=Search">weapons</a></li>
	</ul>
	<?php
		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if($mysql->connect_error){
			die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
		}
		try{
			$stmt = $mysql->prepare("SELECT id, username, name, type, sub_type, game, img, avg_fun, avg_balance FROM contributions WHERE privacy=0 OR username=?");
			$stmt->bind_param("s", $_SESSION["username"]);
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}
			$id = null; $u = null; $n = null; $t = null; $st = null; $g = null; $i = null; $af = null; $ab = null;
			$stmt->bind_result($id, $u, $n, $t, $st, $g, $i, $af, $ab);
			for($x = 0; $x < 6; $x++){
				if($stmt->fetch()){
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
			}

			$count = 0;
			if($rowarr){
				foreach($rowarr as $key => $row){

					if($count == 0){
						echo "<div class='row'>";
					}else if($count == 3){
						echo "</div>";
						echo "<div class='row'>";
					}else if($count==sizeof($count)-1){
						echo "</div>";
					}

					echo "<a class='col-1-3' href='view_contribution_updateable.php?contid=".$row["id"]."'>";
					if($key == 0)
						echo "<img class='gridImage' border='0' alt='".$name."' src='".$row["img"]."'>";
					else
						echo "<img class='gridImage' border='0' alt='".$name."' src='".$row["img"]."'>";
					echo "<div class='blockTextBackground'>";
					//echo "<h2 class='blockText ellipsis'>".$row["name"]." - ".$row["type"]." ".(!empty($row["sub_type"])?("(".$row["sub_type"].")"):"")."</h2>";
					echo "<h2 class='blockText ellipsis'>".$row["name"]."<br>".$row["type"]." ".(!empty($row["sub_type"])?("(".$row["sub_type"].")"):"")."</h2>";
					if($row["avg_fun"] < 0 || $row["avg_balance"] < 0)
						echo "<b class='ratingText'>Not yet rated</b>";
					else
						echo "<table class='rating_table'><tr><td><b class='ratingText'>Fun</b></td><td><span class='stars'>".$row["avg_fun"]."</span></td></tr><tr><td><b class='ratingText'>Balance</b></td><td><span class='stars'>".$row["avg_balance"]."</span></td></tr></table>";
					echo "<h2 class='blockDescription'>By ".$row["username"]."<br>For ".$row["game"]."</h2>";
					echo "</div>";			
					echo "</a>";		
					$count++;
				}
			}

			$stmt->close();
			$mysql->commit();
		}catch(Exception $e){
			$mysql->rollback();
		}
	?>

	<!--</div>-->
</div>
</body>
</html>

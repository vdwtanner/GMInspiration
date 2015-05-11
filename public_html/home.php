<?php
	// Define variables for SEO
	$pageTitle = "The GM's Inspiration";
	$pageDescription = "The GM's Inspiration is a repository designed to facilitate the sharing of homebrew content for tabletop RPGs.";
	include "header.php";
	include dirname(__FILE__)."/scripts/homeScripts.php";
?>
<DOCTYPE html>
<html>
<head>
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
	
	<script type="application/ld+json">
	{
		"@context": "http://schema.org",
		"@type": "WebSite",
		"url": "https://www.gminspiration.com/",
		"potentialAction": {
			"@type": "SearchAction",
			"target": "https://www.gminspiration.com/search_results.php?keywords={search_term_string}&csort=relevance",
			"query-input": "required name=search_term_string"
		}
	}
	</script>

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
	
	$(document).ready(function(){
		//console.log("test");
		$("p.blockDescription").each(function(){
			var divh=$(this).parent().height();
			console.log("divh: "+divh+"  ::  this.outerHeight(): "+$(this).outerHeight());
			while ($(this).outerHeight()>divh) {
				$(this).text(function (index, text) {
					return text.replace(/\W*\s(\S)*$/, '...');
				});
			}
		})
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
	<ul id="quick_search" class="quick_search" style="padding-left: 0em">
		<li id="armor">
				
				<a href="search_results.php?keywords=armor&csort=rating"><img src="img/Armor15.png">Armor</a></li>
		&nbsp <li id="classes">
				
				<a href="search_results.php?keywords=class&csort=rating"><img src="img/Classes15.png">Classes</a></li>
		&nbsp <li id="feats">
				
				<a href="search_results.php?keywords=feat&csort=rating"><img src="img/Feats15.png">Feats</a></li>
		&nbsp <li id="items">
				
				<a href="search_results.php?keywords=item&csort=rating"><img src="img/Items15.png">Items</a></li>
		&nbsp <li id="monsters">
				
				<a href="search_results.php?keywords=monster&csort=rating"><img src="img/Monsters15.png">Monsters</a></li>
		&nbsp <li id="races">
				
				<a href="search_results.php?keywords=race&csort=rating"><img src="img/Races15.png">Races</a></li>
		&nbsp <li id="spells">
				
				<a href="search_results.php?keywords=spell&csort=rating"><img src="img/Spells15.png">Spells</a></li>
		&nbsp <li id="weapons">
				
				<a href="search_results.php?keywords=weapon&csort=rating"><img src="img/Weapons15.png">Weapons</a></li>
	</ul>
	<h2>Up and Coming</h2>
	<?php
		$mysql = new mysqli("localhost", "ab68783_crawler", "El7[Pv~?.p(1", "ab68783_dungeon");
		if($mysql->connect_error){
			die('Connect Error ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
		}
		try{
			/*$stmt = $mysql->prepare("SELECT id, username, name, type, sub_type, game, img, avg_fun, avg_balance FROM contributions WHERE privacy=0 OR username=?");
			$stmt->bind_param("s", $_SESSION["username"]);
			if(!$stmt->execute()){
				echo "Failed to execute mysql command: (".$stmt->errno.") ".$stmt->error;
			}
			$id = null; $u = null; $n = null; $t = null; $st = null; $g = null; $i = null; $af = null; $ab = null;
			$stmt->bind_result($id, $u, $n, $t, $st, $g, $i, $af, $ab);
			for($x = 0; $x < 5; $x++){
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
			}*/
			$rowarr = json_decode(getUpAndComing(),true);
			$count = 0;
			if($rowarr){
				foreach($rowarr as $key => $row){

					if($count == 0){
						echo "<div class='row'>";
						echo "<a class='col-1-3' style='width:50%; height:400px;' href='view_contribution_updateable.php?contid=".$row["id"]."'>";
					}else if($count == 1){
						echo "</div>";
						echo "<div class='row'>";
						echo "<a class='col-1-3' href='view_contribution_updateable.php?contid=".$row["id"]."'>";
					}else if($count == 3){
						echo "</div>";
						echo "<div class='row'>";
						echo "<a class='col-1-3' href='view_contribution_updateable.php?contid=".$row["id"]."'>";
					}else if($count==sizeof($count)-1){
						echo "</div>";
						echo "<a class='col-1-3' href='view_contribution_updateable.php?contid=".$row["id"]."'>";
					}
					else{
					echo "<a class='col-1-3' href='view_contribution_updateable.php?contid=".$row["id"]."'>";
					}
					if($key == 0)
						echo "<img class='gridImage' border='0' alt='".$name."' src='".$row["img"]."'>";
					else
						echo "<img class='gridImage' border='0' alt='".$name."' src='".$row["img"]."'>";
					if($count == 0){echo "<div class='blockTextBackground' style='height:200px; top:200px;'>";}
					else{
					echo "<div class='blockTextBackground'>";}
					//echo "<h2 class='blockText ellipsis'>".$row["name"]." - ".$row["type"]." ".(!empty($row["sub_type"])?("(".$row["sub_type"].")"):"")."</h2>";
					echo "<h2 class='blockText ellipsis'>".$row["name"]."<br>".$row["type"]." ".(!empty($row["sub_type"])?("(".$row["sub_type"].")"):"")."</h2>";
					if($row["avg_fun"] < 0 || $row["avg_balance"] < 0)
						echo "<b class='ratingText'>Not yet rated</b>";
					else
						echo "<table class='rating_table'><tr><td><b class='ratingText'>Fun</b></td><td><span class='stars'>".$row["avg_fun"]."</span></td></tr><tr><td><b class='ratingText'>Balance</b></td><td><span class='stars'>".$row["avg_balance"]."</span></td></tr></table>";
					//echo "<h2 class='blockDescription'>By ".$row["username"]."<br>For ".$row["game"]."</h2>";
					echo "<h2 class='blockDescription ellipsis'>".$row["game"]."</h2>";
					echo "</div>";			
					echo "</a>";		
					$count++;
				}
			}

			//$stmt->close();
			//$mysql->commit();
		}catch(Exception $e){
			$mysql->rollback();
		}
		$topContributors=json_decode(getTopContributors(), true);// the 'true' parameter makes all arrays instead of classes
		echo "<br style='clear:both'>";
		echo "<h2 title='Public contributions only'>Top Contributors</h2><div id='topContributors' class='row'>";
		//print_r($topContributors);
		foreach($topContributors as $index => $row){
			echo "<a class='col-1-3' href='profile.php?user=".$row["username"]."'>";//Create link to user profile
			echo "<img class='gridImage' border='0' alt='".$row["username"]."' src='".$row["picture"]."'>";//show profile picture
			echo "<div class='blockTextBackground'>";//Create text block overlay
			echo "<h2 class='blockText ellipsis'>".$row["username"]."<br>Contributions: ".$row["contributions"]."</h2>";//Create text within overlay
			echo "<p class='blockDescription ellipses'>".$row["desc"]."</p>";
			echo "</div></a>";//close div and link
		}
		echo "</div>"
	?>

	</div>
</div>
<br>
<p style="clear:both;"></p>

</body>
</html>
<?php include 'footer.php';?>
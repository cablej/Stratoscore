<?php
$prefix = "../";

include $prefix . 'XMLTools.php';

?>
<html>
    <head>
    <title>Resting Chart</title>
    </head>
    <body>
		<center>
		<?php
			$currentPage = 1;
			include $prefix . 'header.php';
		?>
    	<?php
    	
    	$league = getXMLatURL($leagueFile, true);
    	
		echo("<br>");
    	echo("<table border='1'><tr><th>Player</th><th>Resting Games</th></tr>");
    	
    	foreach ($league->teams->team as $team) {
	        $name = $team['name'];
	        echo("<tr><th colspan='2'>$name</th></tr>");
	        foreach($team->players->player as $player) {
				if(isSet($player['inactive'])) continue;
	        	$pname = $player['first_name'] . ' ' . $player['last_name'];
	        	$rest = (int) $player->rest;
	        	if($rest != 0)
	        		echo("<tr><td>$pname</td><td>$rest</td>");
	        }
	    }
    
    	?>

<?php
	include $prefix . 'footerTools.php';
?>
	</center>
    </body>
</html>
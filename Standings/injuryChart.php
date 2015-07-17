<?php
$prefix = "../";

include $prefix . 'XMLTools.php';

?>
<html>
    <head>
    <title>Injury Chart</title>
    </head>
    <body>
		<center>
		<div class='buttonMenu'>
			<a href='..' style='border-left:0px;'>Home</a>
			<a href='index.php' class='currentPage'>Standings</a>
			<a href='../schedule.php'>Schedule</a>
			<a href='../Stats'>Stats</a>
		</div>
    	<?php
    	
    	$league = getXMLatURL($leagueFile, true);
    	
		echo("<br>");
	    echo("<p>Add injury</p><form method='POST' action='addInjury.php'><input name='first_name' placeholder='First name'></input><input name='last_name' placeholder='Last name'></input><input name='games' placeholder='Games'><input name='cause' placeholder='Cause'></input><input type='submit'></input></form><br>");
    
    	echo("<table border='1'><tr><th>Player</th><th>Games Injured</th><th>Cause</th></tr>");
    	
    	foreach ($league->teams->team as $team) {
	        $name = $team['name'];
	        echo("<tr><th colspan='3'>$name</th></tr>");
	        foreach($team->players->player as $player) {
				if(isSet($player['inactive'])) continue;
	        	$pname = $player['first_name'] . ' ' . $player['last_name'];
	        	$injury = (int) $player->injury;
	        	$cause = $player->injury['cause'];
	        	if($injury != 0)
	        		echo("<tr><td>$pname</td><td>$injury</td><td>$cause</td></tr>");
	        }
	    }
	
    	?>

<?php
	include $prefix . 'footerTools.php';
?>
	</center>
    </body>
</html>
<html>
<head>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>

<?php
    $prefix = "../../../";
    
    include $prefix . "XMLTools.php";
    
    $team_name = $_GET['team'];
    $id = $_GET['id'];
?>

<script>
<?php
    echo("var team_name = '$team_name';\r\n");
    echo("var id = $id;\r\n");
?>

function confirmDelete() {
	if(prompt("What is your password?") == password) {
		window.location = "playerAction.php?type=delete&id=" + id + "&team=" + team_name;
	}
}

</script>
</head>
<body>
<center>
<?php
	$currentPage = 1;
	include $prefix . 'header.php';
?>
</center>
<?php
    $league = simplexml_load_file($leagueFile);
    $schedule = simplexml_load_file($scheduleFile);
    $team = findTeam($team_name, $league);
    $password=$team['password'];
    echo("<script>password = '$password'</script>");
    if(!$team) {
        echo("<h1>Team not found.</h1>");
        echo("<p>Sorry but the team $team_name was not found.</p>");
        die("<p><a href='../../'><-- Back to standings</a></p>");
    }
    $player = findPlayerByID($id, $team_name, $league);
    if(!$player) {
        echo("<h1>Player not found.</h1>");
        echo("<p>Sorry but the player $player_name on the $team_name was not found.</p>");
        die("<p><a href='../displayTeam.php?name=$team_name'><-- Back to the $team_name team page.</a></p>");
    }
    $first_name = $player['first_name'];
    $last_name = $player['last_name'];
    $player_name = $first_name . ' ' . $last_name;
    echo("<title>$player_name -- $team_name</title>");
    echo("<p>Stats for $player_name (ID: $id).</p>");
    $restGames = (int) $player->rest;
    $quote = "\"";
    echo("<p>$player_name has to rest for $restGames games.");
    echo("<p><a href='../displayTeam.php?name=$team_name'><--Back to team</a></p>");
    echo("<p><a onmouseover='$(&quot#actions&quot).toggle()' style='cursor:pointer'><u>Actions</u></a></p>");
    echo("<div id='actions' style='display:none'>
        <p><a class='linkOnClick' onclick='confirmDelete()' >Delete Player</a></p>
        <p><a onclick='$(&quot#names&quot).toggle()' style='cursor:pointer'><u>Change Player Name</u></a></p>
        <div id='names' style='display:none'>
        <form action='playerAction.php' method='GET'>
            <input type='text' id='new_first' name='new_first' placeholder='New First Name'></input>
            <input type='text' id='new_last' name='new_last' placeholder='New Last Name'></input>
            <input type='hidden' id='team' name='team' value='$team_name'></input>
            <input type='hidden' id='id' name='id' value='$id'></input>
            <input type='hidden' id='type' name='type' value='change_name'></input>
            <input type='submit'></input>
        </form>
        </div>
        </div>");
    echo("<table border='1'><tr><th colspan='29'>Game log</th></tr>");
    
    $games = getPlayerGames($id, $schedule, $team_name);

    foreach($league->teams->team as $teamo) {
    	foreach($teamo->players->player as $playero) {
    		if($playero['id'] == $id && $playero['inactive'] == 'inactive') {
    			$otherGames = getPlayerGames($id, $schedule, $teamo['name']);
    			array_merge($games, $otherGames);
    		}
    	}
    }


	echo("<th>OPP</th><th>Result</th>");
	for($j=0; $j<count($stat_abbrs_withAdditionalStats); $j++) {
		for($h=0; $h<count($stat_abbrs_withAdditionalStats[$j]); $h++) {
			echo"<th class='" . $type . ($i+1) . "'>" . $stat_abbrs_withAdditionalStats[$j][$h] . "</th>";
		}
	}
	echo("</thead></tr><tbody>");

	foreach($games as $game) {
		$gameStats = prependAdditionalStats($game);
		$against = $game[2][1];
		$score = $game[2][2];
		$gid = $game[2][3];
		$link = "/Stratomatic/Stats/generateGameCard.php?game=$gid";
		echo("<tr><td>$against</td><td><a href='$link'>$score</a></td>");
		for($g=0; $g<2; $g++) {
			for($h=0; $h<count($gameStats[$g]); $h++) {
				echo("<td>" . $gameStats[$g][$h] . "</td>");
			}
		}
		echo("</tr>");
	}

	
	
    $stats = $player->stats;
    $i = 0;
    echo("<tr><td colspan='2'><b>Totals</b></td>");
    foreach ($stats->children() as $category) {
        $num = 0;
        echo("<td></td><td></td>");
        if($i == 0) echo("<td></td>");
        foreach ($category->children() as $stat) {
            $id = $stat_categories[$i][$num];
            echo("<td><b>$stat</b></td>");
            $num++;
        }
        $i++;
    }

	echo("</tr>");

	echo("</table></div></div>");
    
    
?>

<?php
	include $prefix . 'footerTools.php';
?>
</body>
</html>	
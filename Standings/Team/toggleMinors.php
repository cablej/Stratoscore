<html>
<head>
<title>Create Player</title>
</head>
<body>
<?php
    $prefix = "../../";
    
    include $prefix . "XMLTools.php";
    
    $league = getXMLatURL($leagueFile, true);
    $team_name = $_GET['team_name'];
    $id = $_GET['id'];
    $team = findTeam($team_name, $league);
    $player = findPlayerByIDWithTeam($id, $team, $league);
    if(!isSet($player["minors"]) || $player["minors"] == "false") {
    	$player["minors"] = "true";
    } else { //call up
		$numPlayers = 0;
		foreach ($team->players->player as $teamPlayer) if($teamPlayer["minors"] != "true") $numPlayers++;
    	if($numPlayers < 25 && (!isSet($team->callUpsUsed) || $team->callUpsUsed < 5)) {
    		$player["minors"] = "false";
    		$team->callUpsUsed = 0;//$team->callUpsUsed + 1;
    	} else {
    		die("<p>Sorry, but you either have exceeded your call up limit (5) or already have 25 players on your team. Try sending a player down first. <a href='displayTeam.php?name=$team_name'><--Back to team.</a></p>");
    	}
    }
    
    if($league->asXML($leagueFile)) {
    	if($player["minors"] == "true") {
    		header("Location:displayTeam.php?name=$team_name");
    	} else {
    		header("Location:displayMinors.php?name=$team_name");
    	}
        echo("<p>Success! <a href='displayTeam.php?name=$team_name'><--Back to team.</a></p>");
    } else {
        echo("<p>ERROR: COULD NOT SAVE. <a href='displayTeam.php?name=$team_name'><--Back to team.</a></p>");
	}
?>



<?php
	include $prefix . 'footerTools.php';
?>
</body>
</html>
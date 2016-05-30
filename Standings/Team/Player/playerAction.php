<?php
    $prefix = "../../../";
    
    include $prefix . "XMLTools.php";
    
    $ck_name = '@[^a-zA-Z0-9_:;\(\)\?\|\&=!<>+*/\%-]@';
    $type = $_GET['type'];
    $id = $_GET['id'];
    $team_name = $_GET['team'];
    $league = simplexml_load_file($leagueFile);
    $schedule = simplexml_load_file($scheduleFile);
    $team = findTeam($team_name, $league);
    $player = findPlayerByID($id, $team_name, $league);
    if($type == 'change_name') {
        $new_first = $_GET['new_first'];
        $new_last = $_GET['new_last'];
        $new_first = preg_replace($ck_name, '', $new_first);
        $new_last = preg_replace($ck_name, '', $new_last);
        $player['first_name'] = $new_first;
        $player['last_name'] = $new_last;
        if($league->asXML($leagueFile)) {
            echo("<p>Success! <a href='../displayTeam.php?name=$team_name'><--Back to team.</a></p>");
        } else {
            echo("<p>ERROR: COULD NOT SAVE. <a href='../displayTeam.php?name=$team_name'><--Back to team.</a></p>");
        }
    }
    else if($type == 'delete') {
    	//if(!inSeason($schedule, $team_name)) {
	    	$players = $team->players;
	    	$players = deletePlayer($players, $player);
	    	
	        if($league->asXML($leagueFile)) {
	            echo("<p>Success! <a href='../displayTeam.php?name=$team_name'><--Back to team.</a></p>");
	        } else {
	            echo("<p>ERROR: COULD NOT SAVE. <a href='../displayTeam.php?name=$team_name'><--Back to team.</a></p>");
	        }
    	/*} else {
    		echo("<p>Whoops! Your season has already started! <a href='../displayTeam.php?name=$team_name'><--Back to team.</a></p>");
    	}*/
    }
?>
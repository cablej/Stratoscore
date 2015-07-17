<?php

$prefix = "../";

include($prefix . 'XMLTools.php');
include($prefix . 'simple_html_dom.php');

echo("<p><a href='createTeam.php'><--Go back to 'Create a Team'.</a></p>");
if(isSet($_GET['team']) && isSet($_GET['division']) && isSet($_GET['owner'])) {
    echo("<p>Importing team.</p>");
    
    $team_name = $_GET['team'];
    $team_realname = $_GET['name'];
    $division = $_GET['division'];
    $owner = $_GET['owner'];
    $password = $_GET['password'];
    
    if(!isValidTeamName($team_name)) {
        die("Invalid team name.");
    }
    
    $url = "http://img.sports.yahoo.com/assets/i/us/sp/v/mlb/teams/83/70x70/$team_name.png";
    
    $league = getXMLatURL($leagueFile, true);
    $league = createTeam($league, $team_realname, $owner, $url, $division, $password);
    
    $html = file_get_html("http://sports.yahoo.com/mlb/teams/$team_name/roster/"); //Loads the team roster
    
    $players = [];
    foreach($html->find('td[class=player]') as $e) {
        $players[] = [$e->plaintext];
    }
    $index = 0;
    foreach($html->find('td[class=position]') as $e) {
        $players[$index][] = $e->plaintext;
        $index++;
    }
    
    foreach($players as $player) {
        if(isSet($player[1])) {
            $position = $player[1];
            $splitname = explode(' ', $player[0], 2);
            $first_name = $splitname[0];
            $last_name = $splitname[1];
            $league = createPlayer($league, $team_realname,  $first_name, $last_name, $position, $stat_categories);
        }
    }
    
    saveXMLAtURL($leagueFile, $league, true);
} else {
    echo("<p>No team selected.</p>");
}

function isValidTeamName($team_name) {
    $validTeams = ["bal", "bos", "nyy", "tb", "tor", "chw", "cle", "det", "kc", "min", "hou", "oak", "sea", "tex", "atl", "mia", "nym", "phi", "was", "chc", "cin", "mil", "pit", "stl", "ari", "col", "lad", "sd", "sf"];
    return in_array($team_name, $validTeams);
}

?>

<?php
	include $prefix . 'footerTools.php';
?>
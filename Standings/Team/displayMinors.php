<html>
<head>
<title>Team Stats</title>
<?php
$prefix = "../../";
    
include $prefix . "XMLTools.php";

$league = simplexml_load_file($leagueFile);
$schedule = simplexml_load_file($scheduleFile);
$team_name = $_GET['name'];
$team = findTeam($team_name, $league);
$owner = $team['owner'];
echo("<title>$team_name -- $owner</title>");
?>
</head>
<body>
<center>
<?php
	$currentPage = 1;
	include $prefix . 'header.php';
?>
<style>
</style>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<?php

if(!$team) {
	echo("<h1>Team not found.</h1>");
	die("<p>Sorry but the team $team_name was not found.</p>");
}

//Prints team stats
$img = $team['img'];
echo("<center><a href='../Standings/Team/displayTeam.php?name=$teamName'><img src='$img' style='width:75px; position:absolute; top:3;left:3'/></a><br>");
$idLabel = "team_playerStats";
echo("<div class='table-wrapper'><table border='1' id='$idLabel' class='tablesorter'>");

$numPlayers = 0;
foreach ($team->players->player as $player) {
	if($player["minors"] == "true") $numPlayers++;
}

if(!isSet($team->callUpsUsed)) {
	$team->addChild("callUpsUsed", 0);
	saveXMLAtURL($leagueFile, $league, true);
	$callUpsUsed = 0;
} else {
	$callUpsUsed = $team->callUpsUsed;
}

$callUpsRemaining = 5 - $callUpsUsed;

$starts = getStartsForPlayers($schedule, $team_name);
	
echo("<table border='1' style='width:40%'><tr><th colspan='5'>Minors - $numPlayers Player" . endingS($numPlayers) . " - $callUpsRemaining Call Up" . endingS($callUpsRemaining) . " Remaining</th></tr><tr><th colspan='5'><a href='displayTeam.php?name=$team_name'>View Main Roster</a></th></tr><tr><th>Name</th><th>Position</th><th>Starts</th><th>2015 Stats</th><th>Call up</th></tr>");
foreach ($team->players->player as $player) {
	if(isSet($player['inactive']) || !isSet($player["minors"]) || $player["minors"] == "false") continue;
	$id = $player['id'];
	$numStarts = $starts[(string) $id];
	if(!$numStarts) $numStarts = 0;
	$first_name = $player['first_name'];
	$last_name = $player['last_name'];
	$player_name = $first_name . ' ' . $last_name;
	$position = $player->position;
	$link = getPlayerBaseballReferenceLink($player);
	$player_link = "<a href='$link' target='_blank'>$player_name</a>";
	echo("<tr colspan='3'><td><a href='Player/displayPlayer.php?team=$team_name&id=$id'>$player_name</a></td><td>$position</td><td>$numStarts</td><td>$player_link</td><td><a href='toggleMinors.php?team_name=$team_name&id=$id'>Send</a></td></tr>");
}
echo("</table></div>");

?>

<?php
	include $prefix . 'footerTools.php';
?>
</center>
</body>
</html>
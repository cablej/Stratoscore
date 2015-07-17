<?php

$prefix = "../";

include $prefix . 'XMLTools.php';
    	    	
$league = getXMLatURL($leagueFile, true);

$ck_name = '@[^a-zA-Z0-9_:;\(\)\?\|\&=!<>+*/\%-]@';
	
$first_name = preg_replace($ck_name, '', toASCII($_POST['first_name']));
$last_name = preg_replace($ck_name, '', toASCII($_POST['last_name']));
$games = $_POST['games'];
$cause = $_POST['cause'];

$player = findPlayerByName($first_name, $last_name, $league);

$player->injury = $games;
$player->injury['cause'] = $cause;

saveXMLAtURL($leagueFile, $league);

header("Location:injuryChart.php");

?>
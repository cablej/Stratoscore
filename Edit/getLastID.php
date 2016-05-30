<?php

$prefix = "../";

include $prefix . "XMLTools.php";

$league = simplexml_load_file($leagueFile);

$schedule = simplexml_load_file($scheduleFile);

$games = $schedule->game;

$game_id = getLastID($schedule);
	
?>
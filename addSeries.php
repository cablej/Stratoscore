<?php

$prefix = "";

include $prefix . "XMLTools.php";

$league = simplexml_load_file($leagueFile);

$schedule = simplexml_load_file($scheduleFile);

$homeTeam = $_GET['homeTeam'];
$awayTeam = $_GET['awayTeam'];
$numGames = $_GET['numGames'];

$homeLeague = findTeam($homeTeam, $league);
$awayLeague = findTeam($awayTeam, $league);

if(!$homeLeague || !$awayLeague) die("Invalid team name");

$owner1 = $homeLeague['owner'];
$owner2 = $awayLeague['owner'];

$games = $schedule->game;

for($i=0; $i<$numGames; $i++) {

	$game_id = getLastID($schedule) + 1;

	if("$owner1" != "$owner2") {
		$game = $schedule->addChild('game');
		$game->addAttribute('id', $game_id + $i);
		$game->addAttribute('started', 'false');
		$game->addAttribute('finished', 'false');
		$teama = $game->addChild('team');
		$teama->addAttribute('name', $homeTeam);
		$teama->addChild('stats');
		$teama->addChild('score', 0);
		$teamb = $game->addChild('team');
		$teamb->addAttribute('name', $awayTeam);
		$teamb->addChild('stats');
		$teamb->addChild('score', 0);
		$game->addChild('innings');
	}
}

$schedule->asXML("Leagues/$username/schedule.xml");

header("Location:schedule.php");

?>
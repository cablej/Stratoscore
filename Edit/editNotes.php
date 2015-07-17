<?php
$prefix = "../";
include $prefix . '/XMLTools.php';

$GAME_ID = $_POST['game'];

$notes = htmlspecialchars($_POST['notes']);

$league = getXMLAtURL($leagueFile, true);
$schedule = getXMLAtURL($scheduleFile, true);

$game = getGame($GAME_ID, $schedule);

$game['notes'] = $notes;

saveXMLAtURL($scheduleFile, $schedule, true);

echo("true");

?>
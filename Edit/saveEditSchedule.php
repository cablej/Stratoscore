<?php

$prefix = "../";

include $prefix . "XMLTools.php";

$schedule = simplexml_load_file($scheduleFile);

$order = $_GET['ids'];

$scheduleText = "<schedule>";

for($i=0; $i<count($order); $i++) {
	$game = getGame($order[$i], $schedule);
	$scheduleText .= $game->asXML();
}

$scheduleText .= "</schedule>";

$schedule = simplexml_load_string($scheduleText);

saveXMLAtURL($scheduleFile, $schedule);

header('Location: ../schedule.php');

?>
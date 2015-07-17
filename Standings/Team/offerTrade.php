<?php

$prefix = "../../";

include $prefix . "XMLTools.php";

$league = getXMLatURL($leagueFile, true);

$offeringTeam = $_GET['offeringTeam'];
$receivingTeam = $_GET['receivingTeam'];
$offeringPlayers = $_GET['offeringPlayers'];
$receivingPlayers = $_GET['receivingPlayers'];

$league = createTradeOffer($league, $offeringTeam, $receivingTeam, $offeringPlayers, $receivingPlayers);

saveXMLAtURL($leagueFile, $league, true);

header("Location:displayTeam.php?name=$offeringTeam");

?>
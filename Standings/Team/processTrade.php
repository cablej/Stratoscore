<?php

$prefix = "../../";

include $prefix . "XMLTools.php";

$league = getXMLatURL($leagueFile, true);

$accepted = $_GET['accept'];
$team_name = $_GET['team'];
$index = intval($_GET['num']);

$team = findTeam($team_name, $league);

$trade = $team->trades->trade[$index];

if($accepted == "yes") {
	$from = $trade->from;
	$fromName = $from['team'];
	$to = $trade->to;
	$toName = $to['team'];
	$fromInLeague = findTeam($fromName, $league);
	$toInLeague = findTeam($toName, $league);
	foreach($from->players->player as $player) { //move player from from to to.
		$playerInLeague = findPlayerByID($player['id'], $fromName, $league);
		
		$newPlayer = $toInLeague->players->addChild('player');
        $newPlayer->addAttribute('id', $playerInLeague['id']);
        $newPlayer->addAttribute('first_name', $playerInLeague['first_name']);
        $newPlayer->addAttribute('last_name', $playerInLeague['last_name']);
        $newPlayer->addChild('position', $playerInLeague->position);
        $newPlayer->addChild('rest', $playerInLeague->rest);
        $stats = $newPlayer->addChild('stats');
        for($i = 0; $i<count($stat_categories); $i++) {
            $count = count($stat_categories[$i]);
            $category = $stats->addChild('category');
            for($g = 0; $g<$count; $g++) {
                
                $stat_id = $stat_categories[$i][$g];
                $stat = $category->addChild('stat', $playerInLeague->stats->category[$i]->stat[$g]);
            }
        }
		$playerInLeague->addAttribute('inactive', 'inactive');
	}
	foreach($to->players->player as $player) { //move player from to to from.
		$playerInLeague = findPlayerByID($player['id'], $toName, $league);
		
		$newPlayer = $fromInLeague->players->addChild('player');
        $newPlayer->addAttribute('id', $playerInLeague['id']);
        $newPlayer->addAttribute('first_name', $playerInLeague['first_name']);
        $newPlayer->addAttribute('last_name', $playerInLeague['last_name']);
        $newPlayer->addChild('position', $playerInLeague->position);
        $newPlayer->addChild('rest', $playerInLeague->rest);
        $stats = $newPlayer->addChild('stats');
        for($i = 0; $i<count($stat_categories); $i++) {
            $count = count($stat_categories[$i]);
            $category = $stats->addChild('category');
            for($g = 0; $g<$count; $g++) {
                
                $stat_id = $stat_categories[$i][$g];
                $stat = $category->addChild('stat', $playerInLeague->stats->category[$i]->stat[$g]);
            }
        }
		$playerInLeague->addAttribute('inactive', 'inactive');
	}
}

$tradesDom = dom_import_simplexml($team->trades);
$tradeDOM = dom_import_simplexml($trade);
$tradesDom->removeChild($tradeDOM);
$team->trades = simplexml_import_dom($tradesDom);

//$league = htmlspecialchars_decode($league->asXML());
//$league = simplexml_load_string($league); //YES!!! It works!

saveXMLAtURL($leagueFile, $league, true);

header("Location:displayTeam.php?name=$team_name");

?>
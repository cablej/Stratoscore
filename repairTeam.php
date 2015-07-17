<?php
$prefix = "";

include 'XMLTools.php';

$league = simplexml_load_file($leagueFile);

$schedule = simplexml_load_file($scheduleFile);

$teamToRepair = $_GET["team"];

$teamInLeague = findTeam($teamToRepair, $league);

$teamInLeague->record->wins = 0;
$teamInLeague->record->losses = 0;

//resets stats to 0
foreach($teamInLeague->players->player as $player) {
	$player->rest = 0;
    $i = 0;
    foreach($player->stats->category as $category) {
        for($j=0; $j<$category->count(); $j++) {
            $category->stat[$j] = 0;
        }
        $i++;
    }
}

foreach($schedule->game as $game) {
	foreach($game->team as $team) {
		if($team['name'] != $teamToRepair) continue;
		foreach($team->stats->player as $player_in_schedule) {	
			$i = 0;
		    foreach($player_in_schedule->category as $category_schedule) {
		        for($j=0; $j<$category_schedule->count(); $j++) {
		            //$category->stat[$j] = 0;
		            foreach($teamInLeague->players->player as $player) {
		            	if((int) $player['id'] != (int) $player_in_schedule['id']) continue;
		            	echo("got the id");
		            	$current_cat = $player->stats->category[$i];
		            	$statToAddTo = (int) $current_cat->stat[$j];
		            	$statToAdd = (int) $category_schedule->stat[$j];
		            	//echo($statToAddTo + $statToAdd);
                    	$current_cat->stat[$j] = $statToAddTo + $statToAdd;
					}
		        }
		        $i++;
		    }
		}
	}
}

saveXMLAtURL("Leagues/strato2014/league.xml", $league, true);

?>
<?php
	include 'footerTools.php';
?>
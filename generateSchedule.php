<?php

    $prefix = "";

    include 'XMLTools.php';

    $league = simplexml_load_file($leagueFile);
    $schedule = simplexml_load_file($scheduleFile);
    $teams = $league->teams;
    $game_num = 0;
    foreach($teams->team as $team1) {
        foreach($teams->team as $team2) {
            $owner1 = $team1['owner'];
            $owner2 = $team2['owner'];
            $name1 = $team1['name'];
            $name2 = $team2['name'];
            if("$owner1" != "$owner2") {
            	for($i=0; $i<3; $i++) {
	                $game_num++;
	                $game = $schedule->addChild('game');
	                $game->addAttribute('id', $game_num);
	                $game->addAttribute('started', 'false');
	                $game->addAttribute('finished', 'false');
	                $teama = $game->addChild('team');
	                $teama->addAttribute('name', $name1);
	                $teama->addChild('stats');
	                $teama->addChild('score', 0);
	                $teamb = $game->addChild('team');
	                $teamb->addAttribute('name', $name2);
	                $teamb->addChild('stats');
	                $teamb->addChild('score', 0);
	                $game->addChild('innings');
            	}
            }
        }
    }
    if($schedule->asXML($scheduleFile)) {
        echo("<p>Schedule generated. <a href='schedule.php'><-- Back to schedule</a></p>");
    } else {
        echo("<p>Unable to generate. <a href='schedule.php'><-- Back to schedule</a></p>");
    }
?>

<?php
	include $prefix . 'footerTools.php';
?>
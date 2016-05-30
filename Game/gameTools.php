<?php

    $prefix = "../";
    
    $css = false;
    
    include $prefix . "XMLTools.php";

    $league = simplexml_load_file($leagueFile);
    $schedule = simplexml_load_file($scheduleFile);
    $type = $_POST['type'];
    $id = $_POST['id'];
    $game = getGame($id, $schedule);
    if($type == 'gameInfo') {
        echo($game->asXML());
    } else if($type == 'getPlayers') {
        $team1_name = $game->team[0]['name'];
        $team1 = findTeam($team1_name, $league);
        $players1 = $team1->players->asXML();
        $team2_name = $game->team[1]['name'];
        $team2 = findTeam($team2_name, $league);
        $players2 = $team2->players->asXML();
        $str=$players1.'<|>'.$players2; 
        echo($str);
    } else if($type == 'submitStats') {
        $result = "";
        $xml_str = $_POST['xml'];
        $xml = simplexml_load_string($xml_str);
        foreach($xml->player as $player) {
            $player_id = $player['id'];
            $team_number = $player['team'];
            $teams = $game->team;
            $i = 0;
            foreach($teams as $team) {
                if($i == $team_number) {
                    break;
                }
                $i++;
            }
            $stats;
            $i = 0;
            foreach($player->stats->category as $category) {
                $j = 0;
                foreach($category->stat as $stat) {
                    $stats[$i][$j] = (int) $stat;
                    $j++;
                }
                $i++;
            }
            $found = false;
            foreach($team->stats->player as $player_sched) {
                if($player_sched["id"] == (string) $player_id) {
                    $found = true;
                    $i = 0;
                    foreach($player_sched->category as $category) {
                        for($j=0; $j<$category->count(); $j++) {
                            $category->stat[$j] = (int) $category->stat[$j] + $stats[$i][$j];
                        }
                        $i++;
                    }
                    break;
                }
            }
            
            if(!$found) {
                $xml_player = $team->stats->addChild("player");
                $xml_player->addAttribute("id", $player_id);
                for($i=0; $i<count($stats); $i++) {
                    $category = $xml_player->addChild("category");
                    for($j = 0; $j<count($stats[$i]); $j++) {
                        $stat = $category->addChild("stat", $stats[$i][$j]);
                    }
                }
            }
            
        }
        if($schedule->asXML($scheduleFile)) {
            $result = "true";
        }
			
		$ids = [];
        foreach($xml->player as $player) {
            $player_id = (int) $player['id'];
			$ids[] = $player_id;
            $team_number = (int) $player['team'];
            $team_name = $game->team[$team_number]['name'];
            $team = findTeam($team_name, $league);
            $stats;
            $i = 0;
            foreach($player->stats->category as $category) {
                $j = 0;
                foreach($category->stat as $stat) {
                    $stats[$i][$j] = (int) $stat;
                    $j++;
                }
                $i++;
            }
            $found = false;
            foreach($team->players->player as $player_league) {
                if($player_league["id"] == (string) $player_id) {
                    $found = true;
                    $i = 0;
                    foreach($player_league->stats->category as $category) {
                        for($j=0; $j<$category->count(); $j++) {
                            $category->stat[$j] = (int) $category->stat[$j] + $stats[$i][$j];
                        }
                        $i++;
                    }
		    		$outs = $stats[1][0];
		    		$rest = 0;
		    		if($outs != 0) {
		    			if($outs > 0 && $outs <= 3) {
		    				$rest = 1;
		    			} else if($outs > 3 && $outs <= 9) {
		    				$rest = 2;
		    			} else if($outs > 9 && $outs <= 15) {
		    				$rest = 3;
		    			} else if($outs > 15 && $outs <= 27) {
		    				$rest = 4;
		    			} else if($outs > 27) {
		    				$rest = 5;
		    			}
		    		}
		    		if($rest != 0) {
		    			$player_league->rest = (int) $player_league->rest + $rest; //does not rest
		    		}
                    break;
                }
            }
        }
											
		foreach($game->team as $team) {
			$team_league = findTeam($team['name'], $league);
			foreach($team_league->players->player as $player) {
				$id = (int) $player['id'];
				if(!in_array($id, $ids)) { //Player did not play, will rest
					if((int) $player->rest != 0) {
						$player->rest = (int) $player->rest - 1; //rests
					}
					if((int) $player->injury != 0) {
						$player->injury = (int) $player->injury - 1; //injurys
					}
				}
			}
		}
		
        if($league->asXML($leagueFile)) {
            $result .= "true";
        } else {
            $result .= "false";
        }
        echo($result);
    } else if($type == 'getStatCats') {
        $str = "";
    	for($i=0; $i<count($stat_categories); $i++) {
    	    for($g=0; $g<count($stat_categories[$i]); $g++) {
    	        $str .= $stat_categories[$i][$g];
    	        if($g != count($stat_categories[$i])-1) {
    	            $str .= '~';
    	        }
    	    }
            if($i != count($stat_categories)-1) {
                $str .= '<|>';
            }
    	}
    	echo($str);
    } else if($type == 'getStatAbbreviations') {
        $str = "";
    	for($i=0; $i<count($stat_abbrs); $i++) {
    	    for($g=0; $g<count($stat_abbrs[$i]); $g++) {
    	        $str .= $stat_abbrs[$i][$g];
    	        if($g != count($stat_abbrs[$i])-1) {
    	            $str .= '~';
    	        }
    	    }
            if($i != count($stat_abbrs)-1) {
                $str .= '<|>';
            }
    	}
    	echo($str);
    } else if($type == 'endGame') {
        $team1_score = (int) $_POST['team1'];
        $team2_score = (int) $_POST['team2'];
        $team1_chart = explode(",", $_POST['team1_chart']);
        $team2_chart = explode(",", $_POST['team2_chart']);
        $game->team[0]->score = $team1_score;
        $game->team[1]->score = $team2_score;
        if($team1_score > $team2_score) {
            $team1_name = $game->team[0]['name'];
            $team1 = findTeam($team1_name, $league);
            $team1->record->wins = (int) $team1->record->wins + 1;
            $team2_name = $game->team[1]['name'];
            $team2 = findTeam($team2_name, $league);
            $team2->record->losses = (int) $team2->record->losses + 1;
        } else {
            $team2_name = $game->team[1]['name'];
            $team2 = findTeam($team2_name, $league);
            $team2->record->wins = (int) $team2->record->wins + 1;
            $team1_name = $game->team[0]['name'];
            $team1 = findTeam($team1_name, $league);
            $team1->record->losses = (int) $team1->record->losses + 1;
        }
        for($i=0; $i<count($team2_chart); $i++) {
            $inning = $game->innings->addChild("inning");
            $inning->addChild("top", $team2_chart[$i]);
            if(count($team1_chart) < $i) {
                $inning->addChild("bottom", -1);
            }
            $inning->addChild("bottom", $team1_chart[$i]);
        }
        $game["started"] = "true";
        $game["finished"] = "true";
        if($schedule->asXML($scheduleFile)) {
            echo("true");
        }
        if($league->asXML($leagueFile)) {
            echo("true");
        }
    }
?>
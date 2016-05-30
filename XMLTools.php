<?php
include 'StratoTools.php';
if(!isSet($css)) {
	$css = true;
}
if($css) {
	if($prefix != "")
		echo ("<link rel='stylesheet' type='text/css' href='" . $prefix . "Stylesheets/classic.css' /><link rel='icon' type='image/png' href='" . $prefix . "Stylesheets/favicon.ico'/>");
	else
		echo ("<link rel='stylesheet' type='text/css' href='Stylesheets/classic.css' /><link rel='icon' type='image/png' href='Stylesheets/favicon.ico'/>");
}

header ('Content-type: text/html; charset=utf-8');

function prependAdditionalStats($playerStats) { //Batting: BA, OBP, Slugging Pitching: ERA, WHIP
	for($z=0; $z<count($playerStats); $z++) {
		if($z == 0) { //Batting
			$newBattingStats = [];
			$newBattingStats[] = $playerStats[0][0] == 0 ? '0.000' : number_format((float) $playerStats[0][2] / $playerStats[0][0], 3, '.', ''); //Batting Average
			$newBattingStats[] = $playerStats[0][0] + $playerStats[0][7] == 0 ? '0.000' : number_format((float) ($playerStats[0][2] + $playerStats[0][7]) / ($playerStats[0][0] + $playerStats[0][7]), 3, '.', ''); //On Base Average
			$totalBases = $playerStats[0][2] + $playerStats[0][4] + ($playerStats[0][5]*2) + ($playerStats[0][6]*3);
			$newBattingStats[] = $playerStats[0][0] == 0 ? '0.000' : number_format((float) $totalBases / $playerStats[0][0], 3, '.', ''); //Slugging
			$playerStats[0] = array_merge($newBattingStats, $playerStats[0]);
		} else if($z == 1) { //Pitching
			$newPitchingStats = [];
			$newPitchingStats[] = $playerStats[1][0] == 0 ? '0.00' : number_format(9*(float) $playerStats[1][4] / ($playerStats[1][0]/3), 2, '.', ''); //ERA
			$newPitchingStats[] = $playerStats[1][0] == 0 ? '0.000' : number_format((float) ($playerStats[1][1] + $playerStats[1][2]) / ($playerStats[1][0]/3), 3, '.', ''); //WHIP
			$playerStats[1] = array_merge($newPitchingStats, $playerStats[1]);
			$outsPitched = intval($playerStats[1][2]);
			$inningsPitched = strval(floor($outsPitched / 3)) . '.' . strval($outsPitched % 3);
			$playerStats[1][2] = $inningsPitched;
		}
	}
	return $playerStats;
}

function getStartsForPlayers($schedule, $team_name) {
	$starts = [];
	foreach($schedule->game as $game) {
		if($game["finished"] == false) continue;
		foreach($game->team as $team) {
			if($team["name"] != $team_name) continue;
			$playerNum = 0;
			$pitcherFound = false;
			foreach($team->stats->player as $player) {
				$isFirstPitcher = false;
				if(!$pitcherFound && $player->category[1]->stat[0] >= 1) {
					$pitcherFound = true;
					$isFirstPitcher = true;
				}
				if($playerNum < 9 || ($player->category[0]->stat[0] + $player->category[0]->stat[7]) >= 2 || $isFirstPitcher) {
					$id = (string) $player["id"];
					if(isSet($starts[$id])) $starts[$id] += 1;
					else $starts[$id] = 1;
				}
				$playerNum++;
			}
		}
	}
	return $starts;
}

function endingS($index) {
	return $index == 1 ? "" : "s";
}

function getPlayerBaseballReferenceLink($player) {
	$last5 = strtolower(substr($player['last_name'], 0, 5));
	$first2 = strtolower(substr($player['first_name'], 0, 2));
	$type = isPitcher($player->position) ? "pitching" : "batting";
	$first = strtolower(substr($player['last_name'], 0, 1));
	$link = "http://www.baseball-reference.com/players/$first/$last5" . "$first2" . "01.shtml#2015:$type" . "_standard";
	return $link;
}

function createTradeOffer($league, $offeringTeamName, $receivingTeamName, $offeringPlayers, $receivingPlayers) {
	$offeringTeam = findTeam($offeringTeamName, $league);
	$receivingTeam = findTeam($receivingTeamName, $league);
	if(!$offeringTeam || !$receivingTeam) die("Invalid team name.");
	if(!isSet($receivingTeam->trades)) $receivingTeam->addChild("trades");
	$trade = $receivingTeam->trades->addChild("trade");
	$from = $trade->addChild("from");
	$from->addAttribute("team", $offeringTeamName);
	$fromPlayers = $from->addChild("players");
	foreach($offeringPlayers as $player) {
		if(!findPlayerByID($player, $offeringTeamName, $league)) die("Player not found: $player");
		$playerInTrade = $fromPlayers->addChild("player");
		$playerInTrade->addAttribute("id", $player);
	}
	$to = $trade->addChild("to");
	$to->addAttribute("team", $receivingTeamName);
	$toPlayers = $to->addChild("players");
	foreach($receivingPlayers as $player) {
		if(!findPlayerByID($player, $receivingTeamName, $league)) die("Player not found: $player");
		$playerInTrade = $toPlayers->addChild("player");
		$playerInTrade->addAttribute("id", $player);
	}
	return $league;
}

function createTeam($league, $new_team_name, $owner, $img_url, $division, $password) { //Creates the team, does not save league
	if(findTeam($new_team_name, $league)) { //If team name exists, don't create team
		die("<p>That team already exists.</p>");
	}
	if($division != "NL" && $division != "AL") { //Currently division can only be NL or AL
		die("<p>Division is not NL or AL");
	}
	$ck_name = '@[^ a-zA-Z0-9_:;\(\)\?\|\&=!<>+*/\%-]@';
	$valid_url = '/^https?:\/\/(?:[a-z\-]+\.)+[a-z]{2,6}(?:\/[^\/#?]+)+\.(?:jpe?g|gif|png)$/';
	
	$new_team_name = preg_replace($ck_name, '', toASCII($new_team_name));
	$owner = preg_replace($ck_name, '', toASCII($owner));
	$new_team_name = toASCII($new_team_name);
	if(!preg_match($valid_url, $img_url) && $img_url != "") {
		die("Not a valid url: $img_url");
	}
	echo("<p>Creating team $new_team_name.</p>");
	$team = $league->teams->addChild('team');
	$team->addAttribute('name',"$new_team_name");
	$team->addAttribute('division',"$division");
	$team->addAttribute('owner',"$owner");
	$team->addAttribute('img',"$img_url");
	$team->addAttribute('password', "$password");
	$record = $team->addChild('record');
	$record->addChild('wins', 0);
	$record->addChild('losses', 0);
	$team->addChild('players');
	$team->addChild('trades');
	
	return $league;
}

function createPlayer($league, $team_name, $first_name, $last_name, $position, $stat_categories) {
	$ck_name = '@[^a-zA-Z0-9_:;\(\)\?\|\&=!<>+*/\%-]@';
	
	$first_name = preg_replace($ck_name, '', toASCII($first_name));
	$last_name = preg_replace($ck_name, '', toASCII($last_name));
	$position = preg_replace($ck_name, '', toASCII($position));
	$team = findTeam($team_name, $league);
	$id = lastId($league)+1;
	if($first_name != "" && $last_name != "" && $position != "" && $team) {
		echo("Creating player $first_name $last_name.");
		$player = $team->players->addChild('player');
		$player->addAttribute('id', $id);
		$player->addAttribute('first_name', $first_name);
		$player->addAttribute('last_name', $last_name);
		$player->addChild('position', $position);
		$player->addChild('rest', 0);
		$stats = $player->addChild('stats');
		for($i = 0; $i<count($stat_categories); $i++) {
			$count = count($stat_categories[$i]);
			$category = $stats->addChild('category');
			for($g = 0; $g<$count; $g++) {
				
				$stat_id = $stat_categories[$i][$g];
				$stat = $category->addChild('stat', '0');
			}
		}
	} else {
		echo("<p>Error in creating player $first_name $last_name $position $team.</p>");
	}
	
	return $league;
}

function deletePlayer($simpleXMLPlayers, $player) {
	$playersDOM = dom_import_simplexml($simpleXMLPlayers);
	$playerDOM = dom_import_simplexml($player);
	$playersDOM->removeChild($playerDOM);
	return(simplexml_import_dom($playersDOM));
}

function toASCII( $str ) {
	return strtr(utf8_decode($str), 
		utf8_decode(
		'ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ'),
		'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy');
}

function getXMLAtURL($url, $simpleXML) {
	if($simpleXML) {
		$xml = simplexml_load_file($url);
		if(isSet($xml)) {
			return $xml;
		} else {
			die("Unable to load xml at url: $url , XML: $xml");
		}
	} else {
		$doc = new DOMDocument(); 
		 
		$doc->preserveWhiteSpace = false; 
		 
		$doc->load($url); 
		
		$root = $doc->firstChild;
		
		return $root;
	}
}

function saveXMLAtURL($url, $xml, $simpleXML = true) {
	if($simpleXML) {
		if($xml->asXml($url)) {
			return true;
		} else {
			die("Unable to save xml at url: $url");
		}
	}
}

function findTeam($team_name, $league) {
	if(true) {
		$teams = $league->teams;
		foreach ($teams->team as $team) {
			$name = $team['name'];
			if($name == (string) $team_name) {
				return $team;
			}
		}
		return false;
	}
}

function findPlayer($first_name, $last_name, $team_name, $league) {
	foreach (findTeam($team_name, $league)->players->player as $player) {
		$first = $player['first_name'];
		$last = $player['last_name'];
		if($first == $first_name && $last == $last_name) {
			return $player;
		}
	}
	return false;
}

function lastId($league) {
	$max = 0;
	foreach ($league->teams->team as $team) {
		foreach ($team->players->player as $player) {
			$id = $player['id'];
			if((int) $id > (int) $max) {
				$max = (int) $id;
			}
		}
	}
	return $max;
}

function findPlayerByID($id, $team_name, $league) {
	foreach (findTeam($team_name, $league)->players->player as $player) {
		$newid = $player['id'];
		if(intval($id) == $newid) {
			return $player;
		}
	}
	return false;
}

function findPlayerByIDWithTeam($id, $team, $league) {
	foreach ($team->players->player as $player) {
		$newid = $player['id'];
		if(intval($id) == $newid) {
			return $player;
		}
	}
	return false;
}

function findPlayerByIDNoTeam($id, $league) {
	foreach ($league->teams->team as $team) {
		foreach ($team->players->player as $player) {
			$newid = $player['id'];
			if($id == $newid) {
				return $player;
			}
		}
	}
	return false;
}

function getTeamName($playerId, $league) {
	foreach ($league->teams->team as $team) {
		foreach ($team->players->player as $player) {
			if(isSet($player['inactive'])) continue;
			$newid = $player['id'];
			if($playerId == $newid) {
				return $team['name'];
			}
		}
	}
	foreach ($league->teams->team as $team) {
		foreach ($team->players->player as $player) {
			$newid = $player['id'];
			if($playerId == $newid) {
				return $team['name'];
			}
		}
	}
}

function findPlayerByName($first_name, $last_name, $league) {
	foreach ($league->teams->team as $team) {
		foreach ($team->players->player as $player) {
			$first = $player['first_name'];
			$last = $player['last_name'];
			if($first == $first_name && $last == $last_name) {
				return $player;
			}
		}
	}
	return false;
}

function getGame($game_id, $schedule) {
	foreach($schedule->game as $game) {
		if($game['id'] == $game_id) return $game;
	}
	return false;
}

function getGameInfo($league, $schedule, $gameId, $additionalStats) {
	$gameArr = array(array(), array(), array()); //Teams: (0) name, (1) score, (2) inning chart, (3) stats, misc game stats: (0) winner (1) loser (2) maxLength of inningChart (3) notes
	$game = getGame($gameId, $schedule);
	
	if($game['finished'] != 'true') {
		die("Game is not finished.");
	}
	
	for($s = 0; $s<2; $s++) { // inserts names and scores
		$team = $game->team[$s];
		$gameArr[$s][0] = $team['name'];
		$gameArr[$s][1] = (int) $team->score;
		$gameArr[$s][3] = getStatsArray($team, $league, "Game", $additionalStats);
	}
	
	foreach($game->innings->inning as $inning) {
		$gameArr[0][2][] = (int) $inning->bottom;
		$gameArr[1][2][] = (int) $inning->top;
	}
	
	$gameArr[2][0] = $gameArr[0][1] > $gameArr[1][1] ? 0 : 1;
	$gameArr[2][1] = $gameArr[0][1] > $gameArr[1][1] ? 1 : 0;
	$gameArr[2][2] = max(count($gameArr[0][2]), count($gameArr[1][2])); //number of innings
	$gameArr[2][3] = $game['notes'];
	
	return $gameArr;
}

function getInningChart($gameInfo) {

	$teamNames = [$gameInfo[0][0], $gameInfo[1][0]];
	$inningChart = [$gameInfo[0][2], $gameInfo[1][2]];
	$teamScores = [$gameInfo[0][1], $gameInfo[1][1]];

	$inningChartEcho = "";

	//Prints inning chart
	$inningChartEcho .= "<table class='inningChart' border='1' ><tr><th></th>";

	for($i=0; $i<$gameInfo[2][2]; $i++) {
		$inningChartEcho .= "<th>" . ($i+1) . "</th>";
	}
	$inningChartEcho .= "<th>Score</th></tr>";
	for($i=count($inningChart) - 1; $i>=0; $i--) {
		$inningChartEcho .= "<tr><th>" . $teamNames[$i] . "</th>";
		for($j=0; $j<count($inningChart[$i]); $j++) {
			$inningChartEcho .= "<td>" . $inningChart[$i][$j] . "</td>";
		}
		$inningChartEcho .= "<td>" . $teamScores[$i] . "</td></tr>";
	}
	$inningChartEcho .= "</table>";
	
	return $inningChartEcho;
}

function getGames($schedule, $finished = "both", $sort = "asc", $num = "all") {
	
	$games;
	
	foreach($schedule->game as $game) {
		if($game['finished'] == "true" && ($finished == "both" || $finished == "t")) {
			$games[] = $game;
		} else if($game['finished'] == "false" && ($finished == "both" || $finished == "f")) {
			$games[] = $game;
		}
	}
	
	if($sort == "desc") $games = array_reverse($games);
	
	if($num != "all") $games = array_slice($games, 0, $num);
	
	return $games;
}

function getStatsArray($team, $league, $type, $additionalStats) { //Player Stats, Totals

	if($type == "Game") $playerXML = $team->stats->player;
	else if($type == "Team") $playerXML = $team->players->player;

	$teamName = $team['name'];

	$players = [];
	$totals = [];
	
	foreach($playerXML as $player) { //id, stats: category, category: stat
		if(isSet($player['inactive'])) continue;
		$player_id = (int) $player['id'];
		$players[$player_id] = [];
		$players[$player_id]["stats"] = [];
		$f = 0;
		if($type == "Game") $playerCategoryXML = $player->category;
		else if($type == "Team") $playerCategoryXML = $player->stats->category;
		foreach($playerCategoryXML as $category) {
			$players[$player_id]["stats"][] = [];
			$x = 0;
			foreach($category->stat as $stat) {
				$players[$player_id]["stats"][$f][] = $stat;
				if(isSet($totals[$f][$x])) $totals[$f][$x] += $stat;
				else $totals[$f][$x] = $stat;
				$x++;
			}
			$f++;
		}
		if($additionalStats) $players[$player_id]["stats"] = prependAdditionalStats($players[$player_id]["stats"]);
	}
	if($additionalStats) $totals = prependAdditionalStats($totals);
	
	foreach(findTeam($teamName, $league)->players->player as $player) {
		$player_id = (int) $player['id'];
		if(isSet($players[$player_id])) {
			$players[$player_id]['id'] = $player_id;
			$players[$player_id]["firstname"] = (string) $player['first_name'];
			$players[$player_id]["lastname"] = (string) $player['last_name'];
			$players[$player_id]["position"] = (string) $player->position;
		}
	}
	return [$players, $totals];
}

function getMVP($team) {
	$mvp;
	$numBases = 0;
	foreach($team->stats->player as $player) {
		$tb = getTotalBases($player);
		if($tb > $numBases) {
			$mvp = $player;
			$numBases = $tb;
		}
	}
	return $mvp;
}

function getStarter($team) {
	foreach($team->stats->player as $player) {
		$outs = $player->category[1]->stat[0];
		if($outs > 0) return $player;
	}
	return false;
}

function getRandomPlayer($team, $except_ids = []) {
	$players = [];
	foreach($team->stats->player as $player) {
		if(!in_array($player['id'], $except_ids)) $players[] = $player;
	}
	return $players[array_rand($players)];
}

function getLastID($schedule) {
	$ids = array();
	foreach($schedule->game as $game) {
		$ids[] = (int) $game['id'];
	}
	sort($ids); //sorts low to high
	$lastID = 0;
	for($i = 0; $i<count($ids); $i++) {
		echo($ids[$i] . " ");
		if($ids[$i] == $lastID + 1) {
			$lastID = $ids[$i];
		 } else {
			break;
		}
	}
	echo("<p>$lastID</p>");
	return $lastID;
}

function getPlayerGames($id, $schedule, $team_name) {
	$games = [];
	foreach($schedule->game as $game) {
		$teamnum = 0;
		foreach($game->team as $team) {
			if($team['name'] == $team_name) {
				$other_team_name = $teamnum == 0 ? $game->team[1]['name'] : $game->team[0]['name'];
				foreach($team->stats->player as $player) {
					if($player['id'] == $id) {
						$game_log = [[], [], []];
						for($i = 0; $i<2; $i++) {
							foreach($player->category[$i]->stat as $stat) {
								$game_log[$i][] = $stat;
							}
						}
						$type = $teamnum == 0 ? "vs" : "@";
						$playerScore = $teamnum == 0 ? $game->team[0]->score : $game->team[1]->score;
						$otherScore = $teamnum == 0 ? $game->team[1]->score : $game->team[0]->score;
						$result = (int) $playerScore > (int) $otherScore ? "W" : "L";
						$game_log[2] = ["$team_name", "$type $other_team_name", "$result $playerScore-$otherScore", $game['id']];
						$games[] = $game_log;
					}
				}
			}
			$teamnum++;
		}
	}
	return $games;
}

function getFullName($player) {
	return $player['first_name'] . " " . $player['last_name'];
}

function getInningsPitched($player) {
	$outsPitched = $player->category[1]->stat[0];
	return strval(floor($outsPitched / 3)) . '.' . strval($outsPitched % 3);
}

function getTotalBases($player) {
	return $player->category[0]->stat[2] + $player->category[0]->stat[4] + ($player->category[0]->stat[5]*2) + ($player->category[0]->stat[6]*3);
}

function getLeagueStatsArray($league) {
	$stats = [];
	foreach($league->teams->team as $team) {
		$stats[] = getStatsArray($team, $league, "Team", true);
	}
	return $stats;
}

function isPitcher($position) {
	if(strpos($position, "P") != false || strpos($position, "CL") != false) {
		return true;
	}
	return false;
}

function statsHaveValue($statCat) {
	foreach($statCat as $stat) {
		if($stat != 0) return true;
	}
	return false;
}

function getScorecardHTML($league, $schedule, $GAME_ID, $prefix) {
	

	$gameInfo = getGameInfo($league, $schedule, $GAME_ID, false);
	$winningTeam = $gameInfo[2][0];
	$losingTeam = $gameInfo[2][1];
	$teamNames = [$gameInfo[0][0], $gameInfo[1][0]];
	$teamScores = [$gameInfo[0][1], $gameInfo[1][1]];
	$inningChart = [$gameInfo[0][2], $gameInfo[1][2]];
	$notes = $gameInfo[2][3];

	//Game Stats
	
	$scorecardHTML = "";
	
	$scorecardHTML .= "<table class='typed' id='battingStats'>";
		//Header
	$scorecardHTML .= "<thead><tr><th></th><th>P</th><th>AB</th><th>R</th><th>H</th><th>RBI</th><th>2B</th><th>3B</th><th>HR</th><th>BB</th><th>SO</th><th>E</th><th>SB</th><th class='handwritten'>CS</th><th></th><th></th><th></th></tr></thead>";
	$scorecardHTML .= "<tbody class='handwritten'>";
	for($i=0; $i<count($teamNames); $i++) {
		$stats = $gameInfo[$i][3];
		
		$players = $stats[0];
		foreach($players as &$player) {
			if(!isPitcher($player["position"]) || statsHaveValue($player['stats']['0'])) { //is a batter
				$scorecardHTML .= "<tr><td>" . $player["firstname"] . " " . $player["lastname"] . "</td><td>" . substr($player["position"], 0, 2) . "</td>";
				for($h=0; $h<count($player['stats'][0]); $h++) {
					$imgStr = "";
					for($p=0; $p<=$player['stats'][0][$h] / 5 - 1; $p++) {
						if($p != 0) $imgStr .= " ";
						$imgStr .= "<img class='tally' id='i5' src='http://stratomatic.byethost7.com/Stratomatic/Images/Tallies/5.png' />";
					}
					$modFive = $player['stats'][0][$h] % 5;
					if($modFive != 0) {
						if($imgStr != "") $imgStr .= " ";
						$imgStr .= "<img class='tally' id='i$modFive' src='http://stratomatic.byethost7.com/Stratomatic/Images/Tallies/$modFive.png' />";
					}
					$scorecardHTML .= "<td>" . $imgStr . "</td>";
				}
				$scorecardHTML .= "<td></td><td></td><td></td></tr>";
			}
		}
	}
	$scorecardHTML .= "</tbody></table></center>";
	
	$scorecardHTML .= "<br />";
	
	//Other Data
	$scorecardHTML .= "<div class='left'>";
	$scorecardHTML .= "<h3 class='typed'>OTHER DATA:</h3>";
	$scorecardHTML .= "<p class='typed'>doubleplays:</p>";
	$scorecardHTML .= "<p class='typed'>sacrifice flies:</p>";
	$scorecardHTML .= "<p class='typed'>sacrifice hits:</p>";
	$scorecardHTML .= "<p class='typed'>passed balls:</p>";
	$scorecardHTML .= "<p class='typed'>wild pitches:</p>";
	$scorecardHTML .= "<h4 class='typed'>COMMENTS: <span class='handwritten'>" . nl2br($notes) . "</span></h3>";
	$scorecardHTML .= "</div>";
	
	//Pitcher Stats
	$scorecardHTML .= "<div class='right'>";
	$scorecardHTML .= "<table class='typed' id='pitchingStats'>";
		//Header
	$scorecardHTML .= "<thead><tr><th>PITCHER</th><th>IP</th><th>H</th><th>BB</th><th>SO</th><th>ER</th></tr></thead>";
	$scorecardHTML .= "<tbody class='handwritten'>";
	for($i=0; $i<count($teamNames); $i++) {
		$stats = $gameInfo[$i][3];
		
		$players = $stats[0];
		foreach($players as &$player) {
			if(isPitcher($player["position"])) { //is a pitcher
				$scorecardHTML .= "<tr><td>" . $player["firstname"] . " " . $player["lastname"] . "</td>";
				for($h=0; $h<5; $h++) {
					$statVal = $player['stats'][1][$h];
					if($h == 0) {
						$modThree = $statVal % 3;
						$statVal /= 3;
						$statVal = (int) $statVal . "." . $modThree;
					}
					$scorecardHTML .= "<td>" . $statVal . "</td>";
				}
				$scorecardHTML .= "</tr>";
			}
		}
	}
	$scorecardHTML .= "</tbody></table></div>";
	$scorecardHTML .= "<br />";
	$scorecardHTML .= "<div style='clear:both'></div>";
	//Inning Chart
	
	$scorecardHTML .= "<center><table class='typed' id='inningChart'";
		//Header
	$scorecardHTML .= "<thead><tr><th>TEAMS</th>";
	for($i=1; $i<=$gameInfo[2][2]; $i++) {
		$scorecardHTML .= "<th>$i</th>";
	}
	for($j=0; $j<15-$gameInfo[2][2]; $j++) {
		$scorecardHTML .= "<th></th>";
	}
	$scorecardHTML .= "<th>R</th><th>H</th><th>E</th></tr></thead><tbody class='handwritten'>";
	for($i=count($inningChart)-1; $i>=0; $i--) {
		$teamName = $teamNames[$i];
		$scorecardHTML .= "<tr><td>$teamName</td>";
		for($j=0; $j<$gameInfo[2][2]; $j++) {
			$score = $inningChart[$i][$j];
			$scorecardHTML .= "<td>$score</td>";
		}
		for($j=0; $j<15-$gameInfo[2][2]; $j++) {
			$scorecardHTML .= "<td></td>";
		}
		$scorecardHTML .= "<td>" . $teamScores[$i] . "</td><td>" . $gameInfo[$i][3][1][0][2] /*trust me, it works*/ . "</td><td>" . $gameInfo[$i][3][1][0][9] . "</td><tr>";
	}
	$scorecardHTML .= "</tbody><table></center>";
	

	//Game Number
	//echo("<div class='left'><table class='typed' id='gameNumber'><tr><th>GAME NUMBER</th></tr><tr class='handwritten'><td>1</td></tr></table></div>")

	return $scorecardHTML;
}

function curPageName() {
	return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
}

function curPageURL() {
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
	$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
	$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

function inSeason($schedule, $team_name) {
	foreach($schedule->game as $game) {
		foreach($game->team as $team) {
			if($team['name'] == $team_name && $game['started'] == 'true') return true;
		}
	}
	return false;
}

function getStandingTable($league, $schedule, $createATeam = false) {

	$table = "";

	$table = "<table border='1' id='standings'><tr><th colspan='7'>Standings</th></tr><tr><th>Name</th><th>Owner</th><th>Wins</th><th>Losses</th><th>PCT</th><th>STRK</th><th>GB</th></tr>";
	$NL = [];
	$AL = [];
	$NL_Wins = [];
	$NL_Losses = [];
	$NL_WPC = [];
	$AL_Wins = [];
	$AL_Losses = [];
	$AL_WPC = [];
	foreach ($league->teams->team as $team) {
		$name = $team['name'];
		$wins = $team->record->wins;
		$losses = $team->record->losses;
		$division = $team['division'];
		$owner = $team['owner'];
		$img = $team['img'];
		$wpc;
		if($wins + $losses != 0) $wpc = $wins / ($wins + $losses);
		else $wpc = 0;
		$wpc_form = number_format((float) $wpc, 3, '.', '');
		$game_arr = [];
		foreach ($schedule->game as $game) {
			$team1 = (string) $game->team[0]['name'];
			$team2 = (string) $game->team[1]['name'];
			$started = $game['started'];
			if($team1 != (string) $name && $team2 != (string) $name) continue;
			$isHomeTeam = $team1 == (string) $name;
			$otherTeam = $isHomeTeam ? $team2 : $team1;
			$home_score = intval($game->team[0]->score);
			$away_score = intval($game->team[1]->score);
			$score1 = $isHomeTeam ? $home_score : $away_score;
			$score2 = $isHomeTeam ? $away_score : $home_score;
		
			$game_status;
			if($game['finished'] == 'false') $game_status = "-";
			else if(($isHomeTeam && intval($home_score) > intval($away_score)) || (!$isHomeTeam && intval($away_score) > intval($home_score))) $game_status = "W";
			else $game_status = "L";
			$game_arr[] = $game_status;
		}
		$game_arr = array_reverse($game_arr);
		$streakType = "";
		$count = 0;
		for($m = 0; $m<count($game_arr); $m++) {
			if($game_arr[$m] == "-") continue;
			if($streakType == "") {
				$streakType = $game_arr[$m];
				$count++;
			} else if($streakType == $game_arr[$m]) $count++;
			else break;
		}
		$string = $streakType . $count;
		$code = "<tr><td><a href='Team/displayTeam.php?name=$name'><img src='$img' style='width:20px' /> $name</a></td><td>$owner</td><td>$wins</td><td>$losses</td><td>$wpc_form</td><td>$string</td>";
		if($division == 'NL') {
			$NL_Wins[] = $wins;
			$NL_Losses[] = $losses;
			$NL_WPC[] = $wins - $losses;
			$NL[] = $code;
		} else if($division == 'AL') {
			$AL_Wins[] = $wins;
			$AL_Losses[] = $losses;
			$AL_WPC[] = $wins - $losses;
			$AL[] = $code;
		}
	}
	array_multisort($NL_WPC,SORT_NUMERIC, SORT_DESC,$NL, $NL_Wins, $NL_Losses);
	array_multisort($AL_WPC,SORT_NUMERIC, SORT_DESC,$AL, $AL_Wins, $AL_Losses);
	$table .= "<tr><td colspan='7'>NL</td></tr>";
	for($i=0; $i<count($NL); $i++) {
		$w = $NL_Wins[$i];
		$l = $NL_Losses[$i];
		$W = $NL_Wins[0];
		$L = $NL_Losses[0];
		$gb = (($W - $w) + ($l - $L))/2;
		if($gb == 0) $gb = "-";
		$table .= $NL[$i] . "<td>$gb</td>" . "</tr>";
	}
	$table .= "<tr><td colspan='7'>AL</td></tr>";
	for($i=0; $i<count($AL); $i++) {
		$w = $AL_Wins[$i];
		$l = $AL_Losses[$i];
		$W = $AL_Wins[0];
		$L = $AL_Losses[0];
		$gb = (($W - $w) + ($l - $L))/2;
		if($gb == 0) $gb = "-";
		$table .= $AL[$i] . "<td>$gb</td>" . "</tr>";
	}
	if($createATeam) $table .= "<tr><td colspan='7'><center><button><a href='createTeam.php' style='text-decoration:none'>Create a team</a></button></center></td></tr>";
	$table .= "</table>";
	
	return $table;
}

$stat_categories = array(array('At Bats', 'Runs', 'Hits', 'RBIs', 'Doubles', 'Triples', 'Home Runs', 'BBs', 'Strikeouts', 'Errors', 'Stolen Bases', 'Caught Stealing'), array('Outs Pitched', 'Hits Allowed', 'Walks', 'Ks', 'Earned Runs', 'Runs Allowed', 'Home Runs Allowed', 'Wins', 'Losses', 'Saves'));
$stat_abbrs = array(array('AB', 'R', 'H', 'RBI', '2B', '3B', 'HR', 'BB', 'K', 'E', 'SB', 'CS'), array('O', 'H', 'BB', 'K', 'ER', 'R', 'HR', 'W', 'L', 'S'));
$stat_abbrs_withAdditionalStats = array(array('BA', 'OBP', 'SLU','AB', 'R', 'H', 'RBI', '2B', '3B', 'HR', 'BB', 'K', 'E', 'SB', 'CS'), array('ERA', 'WHIP','IP', 'H', 'BB', 'K', 'ER', 'R', 'HR', 'W', 'L', 'S'));
?>	
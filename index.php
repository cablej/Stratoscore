<?php
$prefix = "";

include 'XMLTools.php';

?>
<html>
    <head>
    <title>Home</title>
    <style>
    #owners {
    	position:absolute;
    	right:0;
    	top:0;
    	bottom:0;
    	margin:auto;
    	z-index:999;
    }
    
    #schedule {
    	position:absolute;
    	left:0;
    	top:200px;
    	bottom:0;
    	z-index:999;
    }
    
    #interview {
    	width:45%;
    	background-color:rgba(0,105,14,0.6);
    }

    </style>
    </head>
    <body>
    	<center>
		<?php
			include $prefix . 'header.php'
		?>
        <?php
        
        	echo("<br>");
        	
        	
    		$league = getXMLatURL($leagueFile, true);
   			$schedule = getXMLatURL($scheduleFile, true);
        
        
			$league = simplexml_load_file($leagueFile);
			
			$schedule = simplexml_load_file($scheduleFile);
			
			//last game
			
			$game = getGames($schedule, "t", "desc", 1)[0];

                        if(isSet($_GET["game"])) $game = getGame($_GET["game"], $schedule);
			
			if($game != null) {
				
				$gameInfo = getGameInfo($league, $schedule, $game['id'], true);
				$inningChart = [$gameInfo[0][2], $gameInfo[1][2]];
				
				$team1 = $game->team[0]['name'];
				$team2 = $game->team[1]['name'];
				$home_score = $game->team[0]->score;
				$away_score = $game->team[1]->score;
				
				$wt;
				$lt;
				$ws;
				$ls;
				
				$winningTeam;
				
				if((int) $home_score > (int) $away_score) {
					$wt = $team1;
					$lt = $team2;
					$ws = $home_score;
					$ls = $away_score;
					$winningTeam = $game->team[0];
				} else {
					$wt = $team2;
					$lt = $team1;
					$ws = $away_score;
					$ls = $home_score;
					$winningTeam = $game->team[1];
				}
				
				$mvp = getMVP($winningTeam);
				$starter = getStarter($winningTeam);
				$player = getRandomPlayer($winningTeam, [$mvp['id'], $starter['id']]);
				
				$mvp_league = findPlayerByID($mvp['id'], $wt, $league);
				$starter_league = findPlayerByID($starter['id'], $wt, $league);
				$player_league = findPlayerByID($player['id'], $wt, $league);
				
				$mvp_name = getFullName($mvp_league);
				$starter_name = getFullName($starter_league);
				$player_name = getFullName($player_league);
				
				$close = ["$wt manage to beat $lt $ws-$ls", "The $wt scrape past the $lt, win $ws-$ls", "$wt barely win against $lt $ws-$ls", "$mvp_name helps $wt to win $ws-$ls in close game over $lt"];
				$mid = ["$wt comfortably win $ws-$ls against the $lt", "$wt $ws, $lt $ls", "$lt are no match for $wt, lost $ws-$ls", "$wt beat $lt with help of $mvp_name $ws-$ls"];
				$far = ["$wt kill $lt $ws-$ls", "A huge failure for the $lt, the $wt win $ws-$ls", "BLOWOUT: $wt win $ws-$ls over $lt", "Is this it for the $lt? Lost $ws-$ls to the $wt", "The start of a dynasty? $wt beat $lt $ws-$ls", "Is it possible? $wt beat $lt $ws-$ls", "Another game, another win: $wt win $ws-$ls over $lt", "$wt win $ws-$ls over the $lt in huge upset", "$mvp_name takes $wt to $ws-$ls victory over $lt"];
				
				$header;
				
				if($ws - $ls <= 1) $header = $close[array_rand($close)];
				else if($ws - $ls <= 4) $header = $mid[array_rand($mid)];
				else $header = $far[array_rand($far)];
				
				echo("<h1 style='background-color:rgba(0,105,14,0.6);color: white;'>$header</h1>");
				
				echo("<center>" . getInningChart($gameInfo) . "</center>");
				
				echo(sprintf("<div id='interview'><p>MVP: $mvp_name, %s-%s, %s RBI's, and %s home run%s.</p>", $mvp->category[0]->stat[2], $mvp->category[0]->stat[0], $mvp->category[0]->stat[3], $mvp->category[0]->stat[6], $mvp->category[0]->stat[6] == 1 ? "" : " "));
				
				$players_list = [$mvp_name, $starter_name, $player_name];
				$interviewee = $players_list[array_rand($players_list)];
				
				$news = ["ESPN", "Yahoo", "CBS Sports"];
				$new = $news[array_rand($news)];
				$verbs = ["exclusive", "post-game"];
				$verb = $verbs[array_rand($verbs)];
				$nouns = ["interview", "talk", "meeting", "chat"];
				$noun = $nouns[array_rand($nouns)];
				
				echo("<p>$new's $verb $noun with $interviewee</p>");
				
				$questions = ["What do you have to say about your performance today?" => ["I did good.", "Well, I played good enough to get a win, and that's all that really matters.", "Horrible. We won, but I played nowhere near to where I should be performing."], "How did you feel your teammates played today?" => ["Great. We won, they got us the win.", "Horribly. I carried the whole team.", "Great, as always. They're a great team and I love playing with them.", "Our team stinks, so nothing new. Can't believe we won, I guess miracles do happen."], "What do you feel helped you to win today?" => ["It's all because of my mom out there in the crowd cheering me on. Love you mom.", "It's all because of my mom out there in the crowd cheering me on. Just kidding, she doesn't do a thing.", "I have a lot of m and m's, it really helps me calm down.", "I just set my mind to it and go from there!", "Honestly, I don't need anything to support me. I support myself.", "No comment."], "What was your greatest weakness in the game?" => ["Well, I hit a lot of home runs. Is that a weakness?", "It seems like this is an interview or something... oh wait, it is!", "I'm a bit of a perfetionist when it comes to baseball.", "I am not weak. Do you say I am weak? Because I am not weak.", "Everything. That's what my mom says, at least."], "When did you start playing baseball?" => ["Actually, well, I'm just filling in for this guy. What's baseball again?", "Ah, around 1 or 2. A bit of a child prodigy, I guess.", "Yesterday. ... I'm serious.", "Yes. Wait, what'd you say?"]];
				
				if($interviewee == $starter_name) { //starter only
					$ip = getInningsPitched($starter);
					$er = $starter->category[1]->stat[4];
					$hits = $starter->category[1]->stat[1];
					$ks = $starter->category[1]->stat[3];
					
					if($ks >= 6) $questions += ["You had an impressive $ks strikeouts this game. What do you have to say about that?" => ["They always said I had a golden arm. I guess I do, hah.", "Same old, same old. I really need to step my game up.", "Best game of my life, for sure. My dad would be so proud of me.", "Hah, that'll boost up my contract for next year, I hope.", "I AM THE BEST PITCHER IN THE WORLD!"]];
					
					$questions += ["You went for $ip innings this game. Does it ever tire you out?" => ["Nah, I'm not the type of pitcher to get tired easily.", "Yeah, I'm getting pretty old for this game.", "That's what this coffee's for! Never need to sleep.", "Yeah... zzzzzZZzzzzz", "Of course, but you get used to it.", "It does, this game will put me up on the resting chart for a while, I bet!"], "How do you feel your start turned out today? You only allowed $er runs." => ["Eh. $hits hits is decent, I guess. Who knows.", "I am pretty good, I gotta say.", "Wow, only $er? I better start paying attention!", "Great game, great stats."]];
				} else if($interviewee == $player_name) { //random player
					$questions += ["How do you feel you teammate $mvp_name played today?" => ["He played great, as always. I really look up to him as a player.", "Decent. Not as good as me, of course.", "Great. He's a good player, deserves to be the MVP."]];
				} else if($interviewee == $mvp_name) { //mvp
					$questions += ["MVP. What does that word mean to you?" => ["According to the Oxford English Dictionary, MVP means 'Most Valuable Player'. So that's what it means.", "I'm really proud of it, I worked hard and got the award."]];
				}
				
				
				$numQuestions = 3;
				for($i=0; $i<$numQuestions; $i++) {
					shuffle_assoc($questions);	
					$value = end($questions);
					$answer = $value[array_rand($value)];
					$question = key($questions);
					unset($questions[$question]);
					echo("<p>$question $answer</p>");
				}
				
				echo("</div>");
				
			}
			
			//recent and upcoming games
			
			$last5Games = getGames($schedule, "t", "desc", 5);
			$next5Games = getGames($schedule, "f", "asc", 5);
			
			echo("<table id='schedule' border='1'><tr><th colspan='4'>Recent Games</th></tr>");
			
			foreach($last5Games as $game)
				echo(sprintf("<tr><td><img src='%s' style='height:20px; width:20px'/> %s</td><td><img src='%s' style='height:20px; width:20px'/> %s</td><td>%s-%s</td><td><a href='Stats/generateGameCard.php?game=%s'>Box Score</a></td></tr>", findTeam($game->team[0]['name'], $league)['img'], $game->team[0]['name'], findTeam($game->team[1]['name'], $league)['img'], $game->team[1]['name'], $game->team[0]->score, $game->team[1]->score, $game['id']));
			
			echo("<tr><th colspan='4'>Upcoming Games</th></tr>");
			
			foreach($next5Games as $game)
				echo(sprintf("<tr><td><img src='%s' style='height:20px; width:20px'/> %s</td><td><img src='%s' style='height:20px; width:20px'/> %s</td><td></td><td></td></tr>", findTeam($game->team[0]['name'], $league)['img'], $game->team[0]['name'], findTeam($game->team[1]['name'], $league)['img'], $game->team[1]['name']));
			
			
			echo("</table>");
			
			//owner leaders
			
			echo("<table border='1' id='owners'><tr><th colspan='3'>Owner Standings</th></tr>");
			
			$owners = [];
			
			foreach($league->teams->team as $team) {
				$owner = (string) $team['owner'];
				if(array_key_exists($owner, $owners)) {
					$owners[$owner]['wins'] += $team->record->wins;
					$owners[$owner]['losses'] += $team->record->losses;
				} else {
					$owners[$owner] = [];
					$owners[$owner]['wins'] = $team->record->wins;
					$owners[$owner]['losses'] = $team->record->losses;
				}
			}
			
			foreach($owners as $owner => $record) {
				echo(sprintf("<tr><td>$owner</td><td>%s-%s</td><td>%s</td></tr>", $record['wins'], $record['losses'], $record['losses'] + $record['wins'] == 0 ? '0.00' : number_format((float)$record['wins']/($record['losses'] + $record['wins']), 3, '.', '')));
			}
			
			echo("</table>");
			
			//league leaders
			
			/*echo("<table border='1'><tr><th colspan='2'>League Leaders</th></tr><tr colspan='2'>AVG</tr>");
			
			$leagueStats = getLeagueStatsArray($league);
			
			
			echo("</table>");*/
        
        
        	function shuffle_assoc(&$array) {
				$keys = array_keys($array);

				shuffle($keys);

				foreach($keys as $key) {
					$new[$key] = $array[$key];
				}

				$array = $new;

				return true;
			}
        
        ?>
        <?php
			include 'footerTools.php';
        ?>
        </center>
    </body>
</html>			
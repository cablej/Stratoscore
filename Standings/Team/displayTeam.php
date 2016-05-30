<html>
<head>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<?php
    $prefix = "../../";
    
    include $prefix . "XMLTools.php";
    
    $league = simplexml_load_file($leagueFile);
    $schedule = simplexml_load_file($scheduleFile);
    $team_name = $_GET['name'];
    $team = findTeam($team_name, $league);
    $owner = $team['owner'];
    echo("<title>$team_name -- $owner</title>");
    
    
?>
</head>
<body>
<style>
.sideTable {
     position: absolute;
     margin: auto;
     right: 0;
     bottom: 20;
     height:100px;
     width:375px;
}

table th {
    padding:3px;
}

#footer {
   position:fixed;
   left:0px;
   bottom:0px;
   height:30px;
   width:100%;
   background:#999;
}

</style>
<center>
<?php
    $currentPage = 1;
    include $prefix . 'header.php';
?>
</center>
<?php
    if(!$team) {
        echo("<h1>Team not found.</h1>");
        die("<p>Sorry but the team $team_name was not found.</p>");
    }
    $password = $team['password'];
    echo("<script>password = '$password'</script>");
    $wins = $team->record->wins;
    $losses = $team->record->losses;
    $division = $team['division'];
    $src = $team['img'];
    
    $numPlayers = 0;
    foreach ($team->players->player as $player) {
        if($player["minors"] != "true") $numPlayers++;
    }
    
    $starts = getStartsForPlayers($schedule, $team_name);

    echo("<img src='$src' style='width:75px; position:absolute; top:3;left:3'/><h1 style=' position:absolute; top:-10;left:84'>$team_name</h1><a href='changePassword.php?name=$team_name'>Change Password</a>");
    echo("<h2 style=' position:absolute; top:30;left:84'>$wins-$losses ($division)</h2>");
    echo("<br>");
    echo("<div style='height:80%; overflow:scroll; position:relative;left:20%;'>");
    echo("<table border='1' style='width:35%'><tr><th colspan='5'>Roster - $numPlayers Players</th></tr><tr><th colspan='5'><a href='displayMinors.php?name=$team_name'>View Minors</a></th></tr><tr><th>Name</th><th>Position</th><th>Starts</th><th>2015 Stats</th><th>Send to Minors</th></tr>");
    foreach ($team->players->player as $player) {
        if(isSet($player['inactive']) || $player['minors'] == "true") continue;
        $id = $player['id'];
        $numStarts = $starts[(string) $id];
        if(!$numStarts) $numStarts = 0;
        $first_name = $player['first_name'];
        $last_name = $player['last_name'];
        $player_name = $first_name . ' ' . $last_name;
        $position = $player->position;
        $link = getPlayerBaseballReferenceLink($player);
        $player_link = "<a href='$link' target='_blank'>$player_name</a>";
        echo("<tr colspan='3'><td><a href='Player/displayPlayer.php?team=$team_name&id=$id'>$player_name</a></td><td>$position</td><td>$numStarts</td><td>$player_link</td><td><a href='toggleMinors.php?team_name=$team_name&id=$id'>Send</a></td></tr>");
    }
    echo("</table></div>");
    echo("<div style='position:absolute; left:59%; top: 13.5%;height:40%; overflow:scroll;'>");
    echo("<table border='1' id='schedule'><tr><td colspan='4' style='text-align:center;'>Schedule</td>");
    foreach ($schedule->game as $game) {
        $team1 = $game->team[0]['name'];
        $team2 = $game->team[1]['name'];$started = $game['started'];
        if($team1 != $team_name && $team2 != $team_name) continue;
        $isHomeTeam = $team1 == $team_name;
        $otherTeam = $isHomeTeam ? $team2 : $team1;
        $finished = $game['finished'];
        $id = $game['id'];
        $status;
        if($finished == 'true') {
            $status = "Finished";
        } else if($started == 'true') {
            $status = "In Play";
        } else {
            $status = "Play";
        }
        $home_score = $game->team[0]->score;
        $away_score = $game->team[1]->score;
        $home_ind = $isHomeTeam ? "vs." : "@";
        $other_icon = findTeam($otherTeam, $league)['img'];
        $score1 = $isHomeTeam ? $home_score : $away_score;
        $score2 = $isHomeTeam ? $away_score : $home_score;
        
        $game_status;
        if($finished == 'false') $game_status = "-";
        else if(($isHomeTeam && intval($home_score) > intval($away_score)) || (!$isHomeTeam && intval($away_score) > intval($home_score))) $game_status = "W";
        else $game_status = "L";
        
        $scoreString = $finished == 'true' ? "<b>$game_status</b> $score1 - $score2" : "";
        echo("<tr><td>$home_ind</td><td><img src='$other_icon' style='height:20px; width:20px'/></td><td><a href='displayTeam.php?name=$otherTeam'>$otherTeam</a></td><td><a href='../../Stats/generateGameCard.php?game=$id' target='_blank'>$scoreString</a></td></tr>");
        //echo("<tr><td><a href='Standings/Team/displayTeam.php?name=$team1'>$team1</a></td><td><a href='Standings/Team/displayTeam.php?name=$team2'>$team2</a></td><td><a href='Game/playGame.html?id=$id'>$status</a></td><td>$home_score-$away_score</td><td><a href='enterGameScorecard.php?id=$id'>Enter</a></td></tr>");
    }
    echo("</table></div>");
    echo("<div style='position:absolute; left:59%; bottom:15%;height:30%; overflow:scroll;'>");
    echo("<table border='1' id='teamLeaders'><tr><th colspan='3'>Team Leaders</th></tr>");
    $statLeaders = $stat_categories;
    $statLeadersNames = $stat_categories;
    $battingAverageLeader = "";
    $battingAverage;
    $eraLeader = "";
    $era;
    foreach ($team->players->player as $player) {
        $id = $player['id'];
        $player_name = $player['first_name'] . ' ' . $player['last_name'];
        $stats = $player->stats;
        $statArray = $stat_categories;
        $i = 0;
        foreach ($stats->children() as $category) {
            $num = 0;
            foreach ($category->children() as $stat) {
                $statArray[$i][$num] = $stat;
                if(!preg_match('/^[0-9]{1,}$/', $statLeaders[$i][$num])) {
                    $statLeaders[$i][$num] = $stat;
                    $statLeadersNames[$i][$num] = $player_name;
                } else if(intval($stat) > intval($statLeaders[$i][$num])) {
                    $statLeaders[$i][$num] = $stat;
                    $statLeadersNames[$i][$num] = $player_name;
                }
                $num++;
            }
            echo('</tbody>');
            $i++;
        }
        $statArray = prependAdditionalStats($statArray);
        if($battingAverageLeader == "" || $statArray[0][0] > $battingAverage) {
            if($statArray[0][3] >= 4) {
                $battingAverageLeader = $player_name;
                $battingAverage = $statArray[0][0];
            }
        }
        
        if($eraLeader == "" || $statArray[1][0] < $era) {
            if($statArray[1][2] != 0) {
                
                $eraLeader = $player_name;
                $era = $statArray[1][0];
            }
        }
        
    }
    $homerun_name = $statLeadersNames[0][6];
    $homerun = $statLeaders[0][6];
    $rbi_name = $statLeadersNames[0][3];
    $rbi = $statLeaders[0][3];
    if(!isSet($era)) $era = "0.00";
    if(!isSet($battingAverage)) $battingAverage = "0.00";
    echo("<tr><td>Home Runs</td><td>$homerun_name</td><td>$homerun</td></tr>");
    echo("<tr><td>RBI</td><td>$rbi_name</td><td>$rbi</td></tr>");
    echo("<tr><td>Batting Average</td><td>$battingAverageLeader</td><td>$battingAverage</td></tr>");
    echo("<tr><td>ERA</td><td>$eraLeader</td><td>$era</td></tr>");
    echo("<tr><td colspan='3' style='text-align:center'><a href='../../Stats/displayTeamStats.php?name=$team_name'>View complete stats</a></td></tr>");
    echo("</table>");
    echo("</div>");
    echo("
            <form method='GET' action='createPlayer.php' style='position:absolute; left:28%; bottom:0px'>
            Create Player: <input type='text' id='first_name' name='first_name' placeholder='First Name'></input>
            <input type='text' id='last_name' name='last_name' placeholder='Last Name'></input>
            <input type='text' id='position' name='position' placeholder='Position'></input>
            <input type='hidden' id='team' name='team' value='$team_name'></input>
            <input type='submit'></input>
            </form>
        ");
    echo("<div class='sideTable'>");
    
    echo("<table border='1'><tr><th colspan='5'>Trades</th></tr><tr><th>Team</th><th>Players Sending</th><th>Players Receiving</th><th>Accept</th><th>Decline</th></tr>");
    
    if(isSet($team->trades)) {
        $i = 0;
        foreach($team->trades->trade as $trade) {
            $from = $trade->from['team'];
            echo("<tr><td>$from</td><td>");
            $players = [];
            foreach($trade->from->players->player as $playerID) {
                $id = $playerID['id'];
                $player = findPlayerByID($id, $from, $league);
                $player_name = $player['first_name'] . " " . $player['last_name'];
                $link = getPlayerBaseballReferenceLink($player);
                $players[] = "<a href='$link' target='_blank'>$player_name</a>";
            }
            echo(implode(', ', $players));
            echo("</td><td>");
            $players = [];
            foreach($trade->to->players->player as $playerID) {
                $id = $playerID['id'];
                $player = findPlayerByID($id, $team_name, $league);
                $player_name = $player['first_name'] . " " . $player['last_name'];
                $link = getPlayerBaseballReferenceLink($player);
                $players[] = "<a href='$link' target='_blank'>$player_name</a>";
            }
            echo(implode(', ', $players));
            echo("</td><td><a onclick='checkPassword(\"yes\", \"$team_name\", $i)' style='cursor:pointer'>Accept</a></td><td><a onclick='checkPassword(\"no\", \"$team_name\", $i)' style='cursor:pointer'>Decline</a></td></tr>");
            $i++;
        }
    }
    $dropdownInput = "<select name='to' id='to'>";
    foreach($league->teams->team as $team) {
        $tname = $team['name'];
        if($tname == $team_name) continue;
        $dropdownInput .= "<option value='" . $tname ."'>" . $tname ."</option>";
    }
    $dropdownInput .= "</select>";
    echo("<tr><td colspan=5 style='text-align:center'><form method='GET' action='proposeTrade.php' onsubmit='return validatePassword()'>$dropdownInput<input type='hidden' name='from' id='from' value='$team_name'></input><input type='submit' value='Propose trade'></input></td></tr></table></div>");
?>

<?php
    include $prefix . 'footerTools.php';
?>

<script>
var text_input = document.getElementById ('first_name');
text_input.focus ();
text_input.select ();

function checkPassword(result, team_name, i) {
    newlocation = 'processTrade.php?accept=' + result + '&team=' + team_name + '&num=' + i;
    if(prompt("What is your password?") == password) {
        window.location = newlocation;
    } else {
        alert("Password is incorrect.");
    }
}

function validatePassword() {
    if(prompt("What is your password?") == password) {
        return true;
    } else {
        alert("Password is incorrect.");
        return false;
    }
}
</script>
</body>
</html>
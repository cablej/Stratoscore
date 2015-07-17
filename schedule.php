<html>
<head>
<title>Schedule</title>
<?php

    $prefix = "";
    
    include $prefix . "XMLTools.php";
?>
</head>
<body>
<center>
<div class='buttonMenu'>
	<a href='index.php' style='border-left:0px;'>Home</a>
	<a href='Standings'>Standings</a>
	<a href='#' class='currentPage'>Schedule</a>
	<a href='Stats'>Stats</a>
</div>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<?php

    $league = simplexml_load_file($leagueFile);
    $league_name = $league['name'];
    echo("<br>");
    $schedule = simplexml_load_file($scheduleFile);
    if(count($schedule->game) == 0) {
        echo("<p>No schedule. <a href='generateSchedule.php'>Make me one!</a></p>
        ");
    } else {
        echo("<div style='height:80%; overflow:scroll'><table border='1'><tr><th colspan='6'>Schedule</th></tr><tr><th>Home Team</th><th>Away Team</th><th>Score</th><th>Status</th><th>Box Score</th><th>Import</th></tr>");
        foreach ($schedule->game as $game) {
            $team1 = $game->team[0]['name'];
            $team2 = $game->team[1]['name'];
            $t1i = findTeam($game->team[0]['name'], $league)['img'];
            $t2i = findTeam($game->team[1]['name'], $league)['img'];
            $started = $game['started'];
            $finished = $game['finished'];
            $id = $game['id'];
            if($id == 50) { //rest day
            	echo("<tr><th colspan='6'>All Star Break</th></tr>");
            }
            $status;
            if($finished == 'true') {
                $status = "View Recap";
            } else if($started == 'true') {
                $status = "In Play";
            } else {
                $status = "Play";
            }
            $home_score = $game->team[0]->score;
            $away_score = $game->team[1]->score;
            if($finished != 'true') {
            	echo("<tr><td><img src='$t1i' style='height:20px; width:20px'/> <a href='Standings/Team/displayTeam.php?name=$team1'>$team1</a></td><td><img src='$t2i' style='height:20px; width:20px'/> <a href='Standings/Team/displayTeam.php?name=$team2'>$team2</a></td><td>$home_score-$away_score</td><td><a href='Game/playGame.html?id=$id'>$status</a></td><td><a href='Stats/generateGameCard.php?game=$id'>Box Score</a></td><td><a href='enterGameScorecard.php?id=$id'>Enter</a></td></tr>");
            }
            else {
            	echo("<tr><td><img src='$t1i' style='height:20px; width:20px'/> <a href='Standings/Team/displayTeam.php?name=$team1'>$team1</a></td><td><img src='$t2i' style='height:20px; width:20px'/> <a href='Standings/Team/displayTeam.php?name=$team2'>$team2</a></td><td>$home_score-$away_score</td><td><a href='Stats/displayGameStats.php?game=$id'>$status</a></td><td><a href='Stats/generateGameCard.php?game=$id'>Box Score</a></td><td><a href='enterGameScorecard.php?id=$id'>Enter</a></td></tr>");
            }
            	
            }
        echo("</table></div>");
	    echo("<div style='display:inline-block;'><a href='Edit/editSchedule.php' style='display:inline-block;'><button class='linkOnClick'>Edit schedule</button></a>
	            <form method='GET' action='addSeries.php' style=' left:28%; bottom:0px;display:inline-block;'>
	            <input type='text' id='homeTeam' name='homeTeam' placeholder='Home Team'></input>
	            <input type='text' id='awayTeam' name='awayTeam' placeholder='Away Team'></input>
	            <input type='text' id='numGames' name='numGames' placeholder='Games'></input>
	            <input type='submit' value='Create' class='linkOnClick'></input>
	            </form></div>
	        ");
    }
?>

<?php
	include $prefix . 'footerTools.php';
?>
</center>
</body>
</html>
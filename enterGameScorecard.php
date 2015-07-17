<script>

var playerIDs = []; var playerNames = [];

</script>
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">

<?php
$prefix = "";
include 'XMLTools.php';

if(isSet($_POST["game"])) { //can process info
	
} else {
	
	
    $schedule = getXMLAtURL($scheduleFile, true);
    $league = getXMLAtURL($leagueFile, true);
    
    
    $id = $_GET['id'];
    $game = $schedule->game[$id-1];
    
    echo("<div style='float:right'><p>Once you have created a player, enter its name and ID here. Team is 0 for home and 1 for away.</p><input type='text' id='new_player_name' placeholder='New player name'></input><input type='text' id='new_player_ID' placeholder='New player ID'></input><input type='text' id='new_player_team' placeholder='New player team (0 or 1)'></input><button class='linkOnClick' onclick='addNewPlayer()'>Add player</button></div>");
	
	for($i=1; $i>=0; $i--) {
		
		$currentTeam = $game->team[$i];
		$currentTeamName = $currentTeam['name'];
		$team_league = findTeam($currentTeamName, $league);
		
		$teamPlayers = $currentTeam->players;
		
		$quote = '"';
		
		$teamNum = $quote . "team$i" . $quote;
		
		echo("<br><table border='1' class='Battingteam$i' id='Battingteam$i'><thead><tr><th colspan='13'>$currentTeamName Batters</th></tr><tr><th>Player Name</th><th>AB</th><th>R</th><th>H</th><th>RBI</th><th>2B</th><th>3B</th><th>HR</th><th>SB</th><th>BB</th><th>SO</th><th>E</th><th>CS</th></tr><thead><tbody></tbody><tfoot><tr><th colspan='13'><button onclick='addPlayer$i()'>Add</button></tfoot></table>");
		
		
		$row_code = "<tr class='player'><td><input class='playerName_team$i' name='name'></td>";
		for($j=0; $j<12; $j++) {
			$row_code .= "<td><input size='1' value='0' class='stat' onclick='this.focus();this.select()'></td>";
		}
		
		$row_code .= "</tr>";
		
		$playerNames = array();
		$playerIDs = array();
		foreach($team_league->players->player as $p) {
			$playerNames[] = $p['first_name'] . " " . $p['last_name'];
			$playerIDs[] = $p['id'] . "";
		}
		
		
		$js_array = json_encode((array)$playerNames);
		$js_array2 = json_encode((array)$playerIDs);
		
		echo("
		<script>
		var players$i = $js_array;
		playerNames[$i] = $js_array;
		playerIDs[$i] = $js_array2;
		var GAME_ID = $id;
		 </script>");
		 
		if($i == 0) {
			echo(
			"<script>function addPlayer0() {
				$('.Battingteam0 tbody').append($quote $row_code $quote);
				
				$( '.playerName_team0' ).autocomplete({
		      		source: players0,
        autoFocus: true
		    		});
			}
			for(i=0; i<9; i++) addPlayer0();
			</script>"
			
			);
		} else {
			echo(
			"<script>function addPlayer1() {
				$('.Battingteam1 tbody').append($quote $row_code $quote);
				
				$( '.playerName_team1' ).autocomplete({
		      		source: players1,
        autoFocus: true
		    		});
			}
			for(i=0; i<9; i++) addPlayer1();</script>"
			);
		}
		
		
	}
	
	echo("<br>");
	
	for($i=1; $i>=0; $i--) {
		
		$currentTeam = $game->team[$i];
		$currentTeamName = $currentTeam['name'];
		$team_league = findTeam($currentTeamName, $league);
		
		$teamPlayers = $currentTeam->players;
		
		$quote = '"';
		
		$teamNum = $quote . "team$i" . $quote;
		
		echo("<br><table border='1' class='Pitchingteam$i' id='Pitchingteam$i'><thead><tr><th colspan='9'>$currentTeamName Pitchers</th></tr><tr><th>Player Name</th><th>IP</th><th>H</th><th>BB</th><th>SO</th><th>ER</th><th>R</th><th>W</th><th>L</th><th>S</th></tr><thead><tbody></tbody><tfoot><tr><th colspan='9'><button onclick='addPPlayer$i()'>Add</button></tfoot></table>");
		
		
		$row_code = "<tr class='player'><td><input class='playerName_team$i' name='name'></td>";
		for($j=0; $j<9; $j++) {
			$row_code .= "<td><input size='1' value='0' class='stat' onclick='this.focus();this.select()'></td>";
		}
		
		$row_code .= "</tr>";
		
		$playerNames = array();
		foreach($team_league->players->player as $p) {
			$playerNames[] = $p['first_name'] . " " . $p['last_name'];
		}
		
		
		$js_array = json_encode((array)$playerNames);
		
		echo("
		<script>
		var players$i = $js_array;
		 </script>");
		 
		if($i == 0) {
			echo(
			"<script>function addPPlayer0() {
				$('.Pitchingteam0 tbody').append($quote $row_code $quote);
				
				$( '.playerName_team0' ).autocomplete({
		      		source: players0,
        autoFocus: true
		    		});
			}
			for(i=0; i<2; i++) addPPlayer0();
			</script>"
			
			);
		} else {
			echo(
			"<script>function addPPlayer1() {
				$('.Pitchingteam1 tbody').append($quote $row_code $quote);
				
				$( '.playerName_team1' ).autocomplete({
		      		source: players1,
        autoFocus: true
		    		});
			}
			for(i=0; i<2; i++) addPPlayer1();</script>"
			);
		}
		
		
	}
	
	echo("<br><table border='1' id='inningChart'><tr><th>Team</th><th>1</th><th>2</th><th>3</th><th>4</th><th>5</th><th>6</th><th>7</th><th>8</th><th>9</th><th class='addInning'><button onclick='addInning()'>Add</button></th></tr>");
	
	for($i=1; $i>=0; $i--) {
		$currentTeam = $game->team[$i];
		$currentTeamName = $currentTeam['name'];
		echo("<tr><td>$currentTeamName</td>");
		for($j=0;$j<9;$j++) {
			echo("<td><input value='0' onclick='this.focus();this.select()' size='1'></td>");
		}
		echo("</tr>");
	}
	echo("</table>");
	
	echo("<br><br><button style='cursor:pointer' onclick='submitButton()'>Submit</button>");
}

?>

<script>

Array.prototype.move = function (old_index, new_index) {
    if (new_index >= this.length) {
        var k = new_index - this.length;
        while ((k--) + 1) {
            this.push(undefined);
        }
    }
    this.splice(new_index, 0, this.splice(old_index, 1)[0]);
    return this; // for testing purposes
};

function addInning() {
	$('#inningChart tr').each(function()
    {
        $(this).find( "td:nth-last-child(2)" ).after("<td><input value='0' onclick='this.focus();this.select()' size='1'></td>");
    });
}


function finishGame() {
	xml_update = "<statupdate>";
	for (x = 0; x < playerStats.length; x++) {
		for (y = 0; y < playerStats[x].length; y++) {
			player = playerStats[x][y];
			stats = makeStats(player.stats);
			xml_stats = generatePlayerStats(player.id, player.team, stats);
			xml_update += xml_stats;
		}
	}
	xml_update += "</statupdate>";
	submitStats(xml_update);
	submitEndGame();
}

function submitButton() {

	xml_update = "<statupdate>";
	//Batting
	for(var c=0; c<2; c++) {
		var teamTableID = "Battingteam" + c;
		var teamData = gatherData(teamTableID);
		for(var i=0; i<teamData.length; i++) { //each player
			var stats = fixBattingStats(teamData[i].stats); //move stolen bases
			var playerName = teamData[i].name;
			var indexOfName = playerNames[c].indexOf(playerName);
			if(indexOfName == -1) {
				if(playerName == "") continue;
				else {
					alert("Error: player name not found on team. Please try again.");
					return;
				}
			}
			var id = playerIDs[c][indexOfName]; //good to go!
			var statXML = generatePlayerStats(id, c, stats);
			xml_update += statXML;
		}
	}
	//Pitching
	for(var c=0; c<2; c++) {
		var teamTableID = "Pitchingteam" + c;
		var teamData = gatherData(teamTableID);
		for(var i=0; i<teamData.length; i++) { //each player
			var stats = fixPitchingStats(teamData[i].stats); //change to outs pitched
			var playerName = teamData[i].name;
			var indexOfName = playerNames[c].indexOf(playerName);
			if(indexOfName == -1) {
				if(playerName == "") continue;
				else {
					alert("Error: player name not found on team. Please try again.");
					return;
				}
			}
			var id = playerIDs[c][indexOfName]; //good to go!
			var statXML = generatePlayerStats(id, c, stats);
			xml_update += statXML;
		}
	}
	xml_update += "</statupdate>";
	console.log(xml_update);
	
	//inning chart
	var scores = [];
	var inningchart = gatherDataInning('inningChart');
	var temp = inningchart[0];
	inningchart[0] = inningchart[1];
	inningchart[1] = temp;
	for(var i=0; i<inningchart.length; i++) {
		var sum = 0;
		for(var x=0; x<inningchart[i].length; x++) {
			sum += parseInt(inningchart[i][x]);
		}
		scores[i] = sum;
	}
	
	if(scores[0] == scores[1]) {
		alert("The game is a tie! (fix!)");
		return;
	}
	
	submitStats(xml_update);
	submitEndGame(scores, inningchart);
}

function fixPitchingStats(stats) {
	var inningsPitched = stats[0].split(".");
	var innings = parseInt(inningsPitched[0])*3;
	var extra = inningsPitched.length > 1 ? parseInt(inningsPitched[1]) : 0;
	var outsPitched =  innings + extra;
	stats.splice(6, 0, 0);
	stats[0] = outsPitched;
	var batStats = [0,0,0,0,0,0,0,0,0,0,0,0];
	var newStats = [batStats, stats];
	return newStats;
}

function fixBattingStats(stats) {
	stats.move(7, 10); //move stolen bases
	var pitchStats = [0,0,0,0,0,0,0,0,0,0];
	var newStats = [stats, pitchStats];
	return newStats;
}


function generatePlayerStats(id, team, stats) { //the goal
	stats_xml = "<player id='" + id + "' team='" + team + "'><stats>";
	for (var i = 0; i < stats.length; i++) {
		stats_xml += "<category>";
		for (j = 0; j < stats[i].length; j++) {
			stats_xml += "<stat>";
			stats_xml += stats[i][j];
			stats_xml += "</stat>";
		}
		stats_xml += "</category>";
	}
	stats_xml += "</stats></player>"
	return stats_xml;
}

function gatherData(tableID) {
	 var data = [];
	
     var table = document.getElementById(tableID);
     for (r = 2; r < table.rows.length - 1; r++) { //skip header and footer

         var row = table.rows[r];
         var cells = row.cells;
         
         var rowData = {};
         rowData.stats = [];

         for (c = 0; c < cells.length; c++) {
             var cell = cells[c];
             var inputElem = cell.children[0];

             var value = inputElem.value;
             
             if(c == 0) {
             	rowData.name = value;
             } else {
             	rowData.stats.push(value);
             }

         }
         
         data.push(rowData);
     }
     
     return data;
 }
 
 function gatherDataInning(tableID) {
	 var data = [];
	
     var table = document.getElementById(tableID);
     for (r = 1; r < table.rows.length; r++) { //skip header

         var row = table.rows[r];
         var cells = row.cells;
         
         var rowData = [];

         for (c = 1; c < cells.length; c++) {
             var cell = cells[c];
             var inputElem = cell.children[0];

             var value = inputElem.value;
             
             rowData.push(value);

         }
         
         data.push(rowData);
     }
     
     return data;
 }

function submitStats(statXML) {
	$.ajax({
		type: "POST",
		url: "Game/gameTools.php",
		data: "id=" + GAME_ID + "&type=submitStats" + "&xml="+statXML,
		success: function(submitted) {
			return true;
		},
		error: function(){
			alert("[ERROR]: Could not send data to server. Please try again.");
		}
	});
}

function submitEndGame(scores, inningChart) {
	$.ajax({
		type: "POST",
		url: "Game/gameTools.php",
		data: "id=" + GAME_ID + "&type=endGame" + "&team1="+scores[0] + "&team2="+scores[1] + "&team1_chart="+inningChart[0].join()+"&team2_chart="+inningChart[1].join(),
		success: function(state) {
			alert("Success. Make sure that the stats were uploaded before closing this tab.");
			return true;
		},
		error: function(state){
			alert("[ERROR]: Could not send data to server. Please try again.");
			return false;
		}
	});
}

function addNewPlayer() {
	var player_name = $("#new_player_name").val();
	var player_id = $("#new_player_ID").val();
	var player_team = $("#new_player_team").val();
	
	playerNames[player_team].push(player_name);
	playerIDs[player_team].push(player_id);
	
	alert("Added " + player_name + ".");
}

</script>

<?php
include 'footerTools.php';
?>
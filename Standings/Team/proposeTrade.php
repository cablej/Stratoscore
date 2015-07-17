<html>
<head>
<script>var playerIDs = []; var playerNames = [];</script>
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
<?php
    $prefix = "../../";
    
    include $prefix . "XMLTools.php";
    
    $league = simplexml_load_file($leagueFile);
    $schedule = simplexml_load_file($scheduleFile);
    
    $from = $_GET["from"];
    $to = $_GET["to"];
    echo("<script>var From = '$from'; var To = '$to';</script>");
    
    echo("<title>Propose Trade -- $from</title>");
    
    
?>
<style>
h1 {
	background-color:rgba(0,105,14,0.6);
	color: white;
}
</style>
</head>
<body>
<center>
<div class='buttonMenu'>
	<a href='../..' style='border-left:0px;'>Home</a>
	<a href='..' class='currentPage'>Standings</a>
	<a href='../../schedule.php'>Schedule</a>
	<a href='../../Stats'>Stats</a>
</div>

<?php
    if($from == $to) die("Same teams");
  	if(!findTeam($from, $league) || !findTeam($to, $league)) die("Teams do not exist");
	for($i=0; $i<2; $i++) {
		$currentTeamName = $i == 0 ? $from : $to;
		$type = $i == 0 ? "From" : "To";
		echo("<h1>$type : $currentTeamName</h1>");
		$team_league = findTeam($currentTeamName, $league);
		$teamPlayers = $team_league->players;
		$quote = '"';
		$teamNum = $quote . "team$i" . $quote;
		echo("<br><table border='1' class='team$i' id='team$i'><thead><tr><th colspan='1'>$currentTeamName Players</th></tr><tr><th>Player Name</th></thead><tbody></tbody><tfoot><tr><th colspan='1'><button onclick='addPlayer$i()'>Add</button></tfoot></table>");
		$row_code = "<tr class='player'><td><input class='playerName_team$i' name='name'></td></tr>";
		
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
		 </script>");
		if($i == 0) {
			echo(
			"<script>function addPlayer0() {
				$('.team0 tbody').append($quote $row_code $quote);
				
				$( '.playerName_team0' ).autocomplete({
		      		source: players0,
        autoFocus: true
		    		});
			}
			for(i=0; i<1; i++) addPlayer0();
			</script>"
			
			);
		} else {
			echo(
			"<script>function addPlayer1() {
				$('.team1 tbody').append($quote $row_code $quote);
				
				$( '.playerName_team1' ).autocomplete({
		      		source: players1,
        autoFocus: true
		    		});
			}
			for(i=0; i<1; i++) addPlayer1();</script>"
			);
		}
		
		
	}
	echo("<br><br><button style='cursor:pointer' onclick='submitButton()'>Submit</button>");
?>

<script>

function submitButton() {
	var url = "offerTrade.php?offeringTeam=" + From + "&receivingTeam=" + To;
	for(var c=0; c<2; c++) {
		var teamTableID = "team" + c;
		var teamData = gatherData(teamTableID);
		for(var i=0; i<teamData.length; i++) { //each player
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
			if(c == 0) {
				url += "&offeringPlayers[]=" + id;
			} else {
				url += "&receivingPlayers[]=" + id;
			}
		}
	}
	window.location = url;
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
             }

         }
         
         data.push(rowData);
     }
     
     return data;
 }
</script>

<?php
	include $prefix . 'footerTools.php';
?>
</center>
</body>
</html>
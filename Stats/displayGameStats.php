<html>
<head>
<title>Game Results!</title>
<?php
$prefix = "../";

include $prefix . '/XMLTools.php';

echo("<script>username = '$username'</script>");

$GAME_ID = $_GET['game'];
echo("<script>GAME_ID=$GAME_ID</script>");
?>
</head>
<body>
<style>
.inningChart td{
	padding: 3px;
	text-align:center;
}

td {
	text-align:center;
}

#editnotes, #saveButton, #cancelButton {
	display:none;
}
</style>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<!--<script src="../Scripts/colResizable-1.3/jquery.js"></script>-->
<script type="text/javascript" src="../Scripts/__jquery.tablesorter/jquery.tablesorter.js"></script>
<!--<script type="text/javascript" src="../Scripts/colResizable-1.3/colResizable-1.3.min.js"></script>-->
<script>

function toggleColumn(type, team) {
	var classColumn = "." + type + team;
	$(classColumn).toggle();
}

function openGameCard() {
	popupWindow = window.open(
		'generateGameCard.php?game='+GAME_ID,'popUpWindow','height=1000,width=800,resizable=yes,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no,status=no')
}

function hide() {
	$("#notestext").hide();
	$("#notesButton").hide();
   $("#editnotes").val(
       $("#notes").text()
   ).show();
    $("#notes").hide();
   $("#saveButton").show();
   $("#cancelButton").show();
}

function cancel() {
	returnToOriginal();
}

function save() {
	submitNotes($("#editnotes").val())
	returnToOriginal();
}

function submitNotes(notes) {
	$.ajax({
		type: "POST",
		url: "../Edit/editNotes.php",
		data: "game=" + GAME_ID + "&notes="+notes,
		success: function(submitted) {
			return true;
		},
		error: function(){
			alert("[ERROR]: Could not send data to server. Please try again.");
		}
	});
}

function returnToOriginal() {
   $("#notes").text(
    	$("#editnotes").val()
   ).show();
	$("#notestext").show();
	$("#notesButton").show();
   $("#editnotes").hide();
   $("#saveButton").hide();
   $("#cancelButton").hide();
}

</script>
<?php
$league = getXMLAtURL($leagueFile, true);
$schedule = getXMLAtURL($scheduleFile, true);

$gameInfo = getGameInfo($league, $schedule, $GAME_ID, true);
$winningTeam = $gameInfo[2][0];
$losingTeam = $gameInfo[2][1];
$teamNames = [$gameInfo[0][0], $gameInfo[1][0]];
$teamScores = [$gameInfo[0][1], $gameInfo[1][1]];
$inningChart = [$gameInfo[0][2], $gameInfo[1][2]];

//Prints head of results
echo("<h1 class='centeredP'>" . $teamNames[$winningTeam] . " " . $teamScores[$winningTeam] . ", " . $teamNames[$losingTeam] . " " . $teamScores[$losingTeam] . "</h1>");


echo("<center><p><a href='../schedule.php'><-- Back to schedule</a></p></center>");
echo("<center><p><a onclick='openGameCard()' class='linkOnclick'>Generate game scorecard</a></p></center>");

//Prints inning chart
echo"<center><table class='inningChart' border='1' ><tr><th></th>";

for($i=0; $i<$gameInfo[2][2]; $i++) {
	echo"<th>" . ($i+1) . "</th>";
}
echo"<th>Score</th></tr>";
for($i=count($inningChart) - 1; $i>=0; $i--) {
	echo"<tr><th>" . $teamNames[$i] . "</th>";
	for($j=0; $j<count($inningChart[$i]); $j++) {
		echo"<td>" . $inningChart[$i][$j] . "</td>";
	}
	echo"<td>" . $teamScores[$i] . "</td></tr>";
}
echo("</table></center>");

//notes

echo("<center><p id='notesText'>Notes:<br><textarea id='editnotes'></textarea><span id='notes'>" . $gameInfo[2][3] . "</span><br><button id='cancelButton' onclick='cancel()' class='linkOnClick'>Cancel</button><button id='saveButton' class='linkOnClick' onclick='save()'>Save</button><a id='notesButton' class='linkOnClick' onclick='hide()'>Edit</a></p></center>");

//Prints game stats
for($i=0; $i<count($teamNames); $i++) {
    $idLabel = "team" . ($i+1) . "_playerStats";
    $orient = $i==0 ? 'left' : 'right';
    //Table 
    $teamName = $teamNames[$i];
    echo("<div style='float:" . $orient . "'>");
    echo("<h2 class='backgroundGreen'>$teamName</h2>");
    echo("<div class='table-wrapper'><table border='1' id='$idLabel' class='tablesorter'>");
    echo"<thead><tr><th>Name</th><th>Position</th>";
	for($j=0; $j<count($stat_abbrs_withAdditionalStats); $j++) {
		$type = $j == 0 ? "Batting" : "Pitching";
		$quote = "'";
		echo"<th onclick=\"toggleColumn($quote". $type ."$quote, " . ($i+1) . ")\">" . $type . "</th>";
		for($h=0; $h<count($stat_abbrs_withAdditionalStats[$j]); $h++) {
			//echo"<th style='display:none' class='" . $type . ($i+1) . "'>" . $stat_abbrs_withAdditionalStats[$j][$h] . "</th>";
			echo"<th class='" . $type . ($i+1) . "'>" . $stat_abbrs_withAdditionalStats[$j][$h] . "</th>";
		}
	}
	echo("</thead></tr><tbody>");
	
	$stats = $gameInfo[$i][3];
	
	$players = $stats[0];
	$totals = $stats[1];
	
	foreach($players as &$player) {
		$teamName = $teamNames[$i];
		$id = $player['id'];
		echo("<tr><th><a href='../Standings/Team/Player/displayPlayer.php?team=$teamName&id=$id' target='_blank'>" . $player["firstname"] . " " . $player["lastname"] . "</th><td>" . substr($player["position"], 0, 2) . "</a></td>");
		for($g=0; $g<count($player['stats']); $g++) {
			echo("<td></td>");
			$type = $g == 0 ? "Batting" : "Pitching";
			for($h=0; $h<count($player['stats'][$g]); $h++) {
				//echo("<td style='display:none' class='" . $type . ($i+1) . "'>" . $player['stats'][$g][$h] . "</td>");
				echo("<td class='" . $type . ($i+1) . "'>" . $player['stats'][$g][$h] . "</td>");
			}
		}
		echo("</tr>");
	}
	
	echo("</tbody><tr><th>Totals</th><td></td>");
	
	for($j=0; $j<count($totals); $j++) {
		echo("<td></td>");
		$type = $j == 0 ? "Batting" : "Pitching";
		for($g=0; $g<count($totals[$j]); $g++) {
			//echo("<th style='display:none' class='" . $type . ($i+1) . "'>" . $totals[$j][$g] . "</th>");
			echo("<th class='" . $type . ($i+1) . "'>" . $totals[$j][$g] . "</th>");
		}
	}
	
	echo("</tr>");
	
	echo("</table></div></div>");
    echo("<script>\$('#$idLabel').tablesorter({sortInitialOrder: 'desc'});</script>");//\$('#$idLabel').colResizable();</script>");
}
?>

<?php
	include $prefix . 'footerTools.php';
?>
</body>
</html>
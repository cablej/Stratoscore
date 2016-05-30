<html>
<head>
<title>Team Stats</title>
<?php
$prefix = "../";

include $prefix . '/XMLTools.php';
?>
</head>
<body>
<center>
<?php
	$currentPage = 3;
	include $prefix . 'header.php';
?>
<style>
.table-wrapper {
	position:relative;
    height:90%;
    overflow:scroll;
}

td {
	text-align:center;
}
</style>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script type="text/javascript" src="../Scripts/__jquery.tablesorter/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="../Scripts/jquery.fixedheadertable.min.js"></script>
<script type="text/javascript" src="../Scripts/DataTables-1.9.4/DataTables-1.9.4/media/js/jquery.dataTables.min.js"></script>
<script>

function toggleColumn(type) {
	var classColumn = "." + type;
	$(classColumn).toggle();
}


</script>
<?php

$league = getXMLAtURL($leagueFile, true);

//Prints team stats
if(!isSet($_GET['name'])) {
	header("Location: ../Standings");
} else {
	$teamName = $_GET['name'];
	$team = findTeam($teamName, $league);
	$img = $team['img'];
	echo("<center><a href='../Standings/Team/displayTeam.php?name=$teamName'><img src='$img' style='width:75px; position:absolute; top:3;left:3'/></a><br>");
	$idLabel = "team_playerStats";
	echo("<div class='table-wrapper'><table border='1' id='$idLabel' class='tablesorter'>");
	echo"<thead><tr><th>Name</th><th>Position</th>";
	for($j=0; $j<count($stat_abbrs_withAdditionalStats); $j++) {
		$type = $j == 0 ? "Batting" : "Pitching";
		$quote = "'";
		echo"<th onclick=\"toggleColumn($quote". $type ."$quote)\">" . $type . "</th>";
		for($h=0; $h<count($stat_abbrs_withAdditionalStats[$j]); $h++) {
			echo"<th style='' class='" . $type . "'>" . $stat_abbrs_withAdditionalStats[$j][$h] . "</th>";
		}
	}
	echo("</thead></tr><tbody>");
	
	$stats = getStatsArray($team, $league, "Team", true);
	
	$players = $stats[0];
	$totals = $stats[1];
	
	foreach($players as &$player) {
		$id = $player['id'];
		echo("<tr><th><a href='../Standings/Team/Player/displayPlayer.php?team=$teamName&id=$id' target='_blank'>" . $player["firstname"] . " " . $player["lastname"] . "</th><td>" .  substr($player["position"], 0, 2) . "</a></td>");
		for($g=0; $g<count($player['stats']); $g++) {
			echo("<td></td>");
			$type = $g == 0 ? "Batting" : "Pitching";
			for($h=0; $h<count($player['stats'][$g]); $h++) {
				echo("<td style='' class='" . $type . "'>" . $player['stats'][$g][$h] . "</td>");
			}
		}
		echo("</tr>");
	}
	
	echo("</tbody><tfoot><tr><th>Totals</th><td></td>");
	
	for($j=0; $j<count($totals); $j++) {
		echo("<td></td>");
		$type = $j == 0 ? "Batting" : "Pitching";
		for($g=0; $g<count($totals[$j]); $g++) {
			echo("<th style='' class='" . $type . "'>" . $totals[$j][$g] . "</th>");
		}
	}
	
	echo("</tr></tfoot>");
	
	echo("</table></div></center>");
	echo("<script>\$('#$idLabel').tablesorter({sortInitialOrder: 'desc'});</script>");
	echo("<script>
	\$('#$idLabel').dataTable( {
        'sScrollY': '80%',
        'bPaginate': false,
        'bScrollCollapse': true
    } );</script>");
}
?>

<?php
	include $prefix . 'footerTools.php';
?>
</center>
</body>
</html>
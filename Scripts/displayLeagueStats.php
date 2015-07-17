<html>
<head>
<title>League Stats</title>
</head>
<body>
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
<!--<script src="../Scripts/colResizable-1.3/jquery.js"></script>-->
<script type="text/javascript" src="../Scripts/__jquery.tablesorter/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="../Scripts/jquery.fixedheadertable.min.js"></script>
<script type="text/javascript" src="../Scripts/DataTables-1.9.4/DataTables-1.9.4/media/js/jquery.dataTables.min.js"></script>
<!--<script type="text/javascript" src="../Scripts/colResizable-1.3/colResizable-1.3.min.js"></script>-->
<script>

function toggleColumn(type) {
	var classColumn = "." + type;
	$(classColumn).toggle();
}


</script>
<?php

$prefix = "../";

include $prefix . '/XMLTools.php';

$league = getXMLAtURL($leagueFile, true);

//Prints league stats
echo("<center><h1>League Stats</h1>");
echo("<p><a href='index.php'><-- Back to stats</a></p>");
$idLabel = "team_playerStats";
echo("<div class='table-wrapper'><table border='1' id='$idLabel' class='tablesorter'>");
echo"<thead><tr><th>Name</th><th>Position</th><th>Team</th>";
for($j=0; $j<count($stat_abbrs_withAdditionalStats); $j++) {
	$type = $j == 0 ? "Batting" : "Pitching";
	$quote = "'";
	echo"<th onclick=\"toggleColumn($quote". $type ."$quote)\">" . $type . "</th>";
	for($h=0; $h<count($stat_abbrs_withAdditionalStats[$j]); $h++) {
		echo"<th style='' class='" . $type . "'>" . $stat_abbrs_withAdditionalStats[$j][$h] . "</th>";
	}
}
echo("</thead></tr><tbody>");

$leagueStats = getLeagueStatsArray($league);

for($m=0; $m<count($leagueStats); $m++) {

	$players = $leagueStats[$m][0];
	
	foreach($players as &$player) {
		$id = $player['id'];
		$teamName = getTeamName($id, $league);
		echo("<tr><th><a href='../Standings/Team/Player/displayPlayer.php?team=$teamName&id=$id' target='_blank'>" . $player["firstname"] . " " . $player["lastname"] . "</th><td>" .  substr($player["position"], 0, 2) . "</a></td><td>$teamName</td>");
		for($g=0; $g<count($player['stats']); $g++) {
			echo("<td></td>");
			$type = $g == 0 ? "Batting" : "Pitching";
			for($h=0; $h<count($player['stats'][$g]); $h++) {
				echo("<td style='' class='" . $type . "'>" . $player['stats'][$g][$h] . "</td>");
			}
		}
		echo("</tr>");
	}
}
echo("</tbody>");


echo("</table></div></center>");
echo("<script>\$('#$idLabel').tablesorter({sortInitialOrder: 'desc'});</script>");
echo("<script>
\$('#$idLabel').dataTable( {
    'sScrollY': '80%',
    'bPaginate': false,
    'bScrollCollapse': true
} );</script>");
?>

<?php
	include $prefix . 'footerTools.php';
?>
</body>
</html>
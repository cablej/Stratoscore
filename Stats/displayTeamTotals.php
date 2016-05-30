<?php
$prefix = "../";

include $prefix . 'XMLTools.php';

?>
<html>
    <head>
    <style>
    th {
    	cursor:pointer;
    }
    </style>
    <title>Team Totals</title>
	    
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
	<script type="text/javascript" src="../Scripts/__jquery.tablesorter/jquery.tablesorter.min.js"></script>
	<script type="text/javascript" src="../Scripts/DataTables-1.9.4/DataTables-1.9.4/media/js/jquery.dataTables.min.js"></script>
    </head>
    <body>
		<center>
		<?php
			$currentPage = 3;
			include $prefix . 'header.php';
		?>
		
		<?php
		
			echo("<br><table border='1' id='teamStats'><thead><tr><th></th><th>Team</th><th>BA</th><th>OBP</th><th>SLU</th><th>H</th><th>HR</th><th>R</th><th>ERA</th><th>WHIP</th><th>RA</th></thead></tr>");
		
    		$league = getXMLatURL($leagueFile, true);
    		$schedule = getXMLatURL($scheduleFile, true);
		
			$teamTotals = getLeagueStatsArray($league);
			
			for($i=0; $i<count($teamTotals); $i++) {
				$totals = $teamTotals[$i][1];
		    	$teamName = $league->teams->team[$i]['name'];
		    	$teamLogo = $league->teams->team[$i]['img'];
		    	$BA = $totals[0][0];
		    	$OBP = $totals[0][1];
		    	$SLU = $totals[0][2];
		    	$H = $totals[0][5];
		    	$HR = $totals[0][9];
		    	$ERA = $totals[1][0];
		    	$WHIP = $totals[1][1];
		    	$R = $totals[1][7];
		    	$Rr = $totals[0][4];
		    	echo("<tr><td><img src='$teamLogo' style='height:25px'/></td><td><a href='../Standings/Team/displayTeam.php?name=$teamName' target='_blank'>$teamName</a></td><td>$BA</td><td>$OBP</td><td>$SLU</td><td>$H</td><td>$HR</td><td>$Rr</td><td>$ERA</td><td>$WHIP</td><td>$R</td></tr>");
			}
			
			echo("</table>");
			
			$idLabel = "teamStats";
			echo("<script>\$('#$idLabel').tablesorter({sortInitialOrder: 'desc'});</script>");
			
		?>
		
<?php
	include $prefix . 'footerTools.php';
?>
	</center>
    </body>
</html>
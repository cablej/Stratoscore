<html>
<head>
<title>Standings</title>
<?php


    $prefix = "../";
    
    include $prefix . "XMLTools.php";
?>
</head>
<body>
<style>
</style>
<center>
<?php
	$currentPage = 1;
	include $prefix . 'header.php';
?>
<?php


    $league = getXMLatURL($leagueFile, true);
    $schedule = getXMLatURL($scheduleFile, true);
    //echo("<p>Welcome to the league: $LEAGUE_NAME</p>");
    echo("<br>");
    
    echo(getStandingTable($league, $schedule, true));
    
    echo("<p><a href='restChart.php'>View Resting Chart</a></p>");
    echo("<p><a href='injuryChart.php'>View Injury Chart</a></p>");
?>

<?php
	include $prefix . 'footerTools.php';
?>
</body>
</center>
</html>
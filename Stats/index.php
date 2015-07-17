<?php
$prefix = "../";

include $prefix . 'XMLTools.php';

?>
<html>
    <head>
    <title>Stats Home</title>
    </head>
    <body>
		<center>
		<div class='buttonMenu'>
			<a href='../index.php' style='border-left:0px;'>Home</a>
			<a href='../Standings'>Standings</a>
			<a href='../schedule.php'>Schedule</a>
			<a href='index.php' class='currentPage'>Stats</a>
		</div>
        <?php
            //echo("<p>Welcome to $LEAGUE_NAME</p>");
        ?>
        <p><a href='../schedule.php'>View Game Stats</a></p>
        <p><a href='displayTeamTotals.php'>View Team Stats</a></p>
        <p><a href='displayLeagueStats.php'>View League Stats</a></p>

<?php
	include $prefix . 'footerTools.php';
?>
	</center>
    </body>
</html>
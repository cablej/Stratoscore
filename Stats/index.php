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
		<?php
			$currentPage = 3;
			include $prefix . 'header.php';
		?>
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
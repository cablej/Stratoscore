<html>
<head>
<title>Create Player</title>
</head>
<body>
<?php
    $prefix = "../../";
    
    include $prefix . "XMLTools.php";
    
    $league = getXMLatURL($leagueFile, true);
    $team = $_GET['team'];
    $league = createPlayer($league, $_GET['team'],  $_GET['first_name'], $_GET['last_name'], $_GET['position'], $stat_categories);
    saveXMLAtURL($leagueFile, $league, true);
    
    header("Location:displayTeam.php?name=$team");
    
    echo("<p>Success!<a href='displayTeam.php?name=$team'> <--Back to team page</a></p>")
?>



<?php
	include $prefix . 'footerTools.php';
?>
</body>
</html>
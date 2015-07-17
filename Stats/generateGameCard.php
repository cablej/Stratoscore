<html>
<head>
<title>Game Card!</title>
</head>
<body><link href='http://fonts.googleapis.com/css?family=Indie+Flower' rel='stylesheet' type='text/css'>

<script src="http://code.jquery.com/jquery-1.9.1.js"></script>

<div class='left'>
<a href='../Share/emailScorecard.php?game=<?php echo $_GET['game'] ?>' target='_blank'><img src="../Images/Share/iconShareGreenSmall.png" /></a>
</div>
<center><p class='typed'>Form A</p>
</a>
<?php

$prefix = "../";
$css = false;

if(isSet($_GET['game'])) {
	$GAME_ID = $_GET['game'];
} else if(isSet($_GET['key'])) {
	$password = false;
	
	if(!preg_match('/^[a-zA-Z\d]+$/', $_GET['key'])) {
		die("Key contains invalid characters.");
	}

	$key = $_GET['key'];
	
	
	$mysqli = new mysqli("localhost", "root", "91079oak", "Stratomatic");
	if($mysqli === false) {
		die("ERROR: COULD NOT CONNECT. " . mysqli_connect_error() . " [ERROR: GG01]");
	}
	$sql = "SELECT `Type`, `AdditionalParams` FROM `AuthorizationKeys` WHERE KeyVal = '$key'";
	if($result = $mysqli->query($sql)) {
	    if($result->num_rows == 1) {
	        $row = $result->fetch_row();
	        $type = $row[0];
	        $params = $row[1];
	        
	        parse_str($params);
	        
	        if(!isSet($GAME_ID)) $GAME_ID = $GAME_ID;
	        
			$leagueFile = $prefix . "Leagues/$USERNAME/league.xml";
			$scheduleFile = $prefix . "Leagues/$USERNAME/schedule.xml";
	        
		} else {
			die("<p>Invalid key.</p>");
		}
	} else {
	    die("<p>Sorry. Something went wrong. Please try again. [ERROR: GG02]</p>");
	}
	
	$mysqli->close();
	
} else {
	die("Improper parameters.");
}

echo("<link href='$prefix/Stylesheets/scorecard.css' rel='stylesheet' type='text/css' />");

include $prefix . '/XMLTools.php';

$league = getXMLAtURL($leagueFile, true);
$schedule = getXMLAtURL($scheduleFile, true);

echo(getScorecardHTML($league, $schedule, $GAME_ID, $prefix));

?>

<?php
	include $prefix . 'footerTools.php';
?>
</body>
</html>
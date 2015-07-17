<html>
<head>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<?php
    $prefix = "../../";
    
    include $prefix . "XMLTools.php";
    
    $league = simplexml_load_file($leagueFile);
    $schedule = simplexml_load_file($scheduleFile);
    $team_name = $_GET['name'];
    $team = findTeam($team_name, $league);
    $owner = $team['owner'];
    echo("<title>$team_name -- $owner</title>");
    
?>
</head>
<body>
<style>
.sideTable {
     position: absolute;
     margin: auto;
     right: 0;
     bottom: 20;
     height:100px;
     width:375px;
}

table th {
	padding:3px;
}
</style>
<center>
<div class='buttonMenu'>
	<a href='../..' style='border-left:0px;'>Home</a>
	<a href='..' class='currentPage'>Standings</a>
	<a href='../../schedule.php'>Schedule</a>
	<a href='../../Stats'>Stats</a>
</div>
<br><br>
<form method='POST' action=''>
<p>Old Password:</p> <input type='text' id='old' name='old' placeholder='Old Password'/><br>
<p>New Password:</p> <input type='text' id='new' name='new' placeholder='New Password' /><br>
<p>Confirm Password:</p> <input type='text' id='con' name='con' placeholder='Confirm Password' /><br><br>
<input type='submit'>
</form>
<?php

if(isSet($_POST['old']) && isSet($_POST['new']) && isSet($_POST['con'])) {
	$real_old_password = $team['password'];
	$old = $_POST['old'];
	$new = $_POST['new'];
	$con = $_POST['con'];
	if($real_old_password == $old) {
		if($new == $con) {
			$team['password'] = $new;
			echo("Success!");
		} else {
			die("Passwords don't match.");
		}
	} else {
		die("That wasn't your password!");
	}
} else {
	die("Field missing");
}

saveXMLAtURL($leagueFile, $league, true);

?>
</center>

<?php
	include $prefix . 'footerTools.php';
?>
</body>
</html>
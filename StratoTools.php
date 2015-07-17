<?php
if(!isSet($password)) {
	$password = true;
}
if($password) {
	session_start();
	$_SESSION["loggedIn"] = 1; //for auto login
	$_SESSION["Username"] = "strato2014"; //for auto login
	if(!isSet($_SESSION["loggedIn"])) {
	    session_destroy();
	    
	    if(curPageName() == 'index.php') {
	    	header('Location: /Stratomatic/SignIn/signup.php');
	    }
	    else {
	    	header('Location: /Stratomatic/SignIn/signin.php?page=' . curPageName() . '&url=' . curPageURL());
	    }
	}
	$username = $_SESSION['Username'];
	$leagueFile = "Leagues/$username/league.xml";
	$scheduleFile = "Leagues/$username/schedule.xml";
	
	$sessionUsername = $_SESSION['Username'];
	$sql = "SELECT `Name`, `Username`, `Email`, `League Name` FROM `Users` WHERE Username = '$sessionUsername'";
	
	
	$mysqli = new mysqli("sql108.byethost7.com", "b7_15607535", "Password changed to protect the innocent", "b7_15607535_Users");
	if($mysqli === false) {
		die("ERROR: COULD NOT CONNECT. " . mysqli_connect_error() . " [ERROR: SU00]");
	}
	
	if($result = $mysqli->query($sql)) {
	    if($result->num_rows == 1) {
	        $row = $result->fetch_row();
	        $USER_NAME = $row[0];
	        $USERNAME = $row[1];
	        $USER_EMAIL = $row[2];
	        $LEAGUE_NAME = $row[3];
	    }
	}
	
	$mysqli->close();
	
	$leagueFile = $prefix . $leagueFile;
	$scheduleFile = $prefix . $scheduleFile;
}
?>
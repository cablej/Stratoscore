<?php
$prefix = "../";

include $prefix . 'XMLTools.php';

?>
<html>
    <head>
    <title>Email Scorecard</title>
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    </head>
    <body>
        <form action="emailScorecard.php?game=<?php echo $_GET['game'] ?>" method="POST" id='emailForm'>
		<p>E-mail to send from:</p>
		<input type="text" id='emailFrom' name="emailFrom" placeholder="your email"><br>
		<p>E-mail to send to:</p>
		<input type="text" id='emailTo' name="emailTo" placeholder="their email"><br>
		<p>Comments:</p>
		<textarea rows="16" cols="50" name="comments" id="comments">Hello! This is a game scorecard from the Stratomatic Scorekeeper. Hope you enjoy!</textarea><br><br>
		<input type="submit" value="Send">
		<input type="reset" value="Reset">
		</form>
        <?php
            if(isSet($_POST['emailFrom'])) {
    			$ck_email = '/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i';
            	echo("<script>$('#emailForm').hide()</script>");
            	if(!preg_match($ck_email, $_POST['emailFrom']) || !preg_match($ck_email, $_POST['emailTo'])) {
            		echo("<script>alert('Invalid email')</script>");
            		header("Location:emailScorecard.php");
            	}
            	
            	$GAME_ID = $_GET['game'];
				$league = getXMLAtURL($leagueFile, true);
				$schedule = getXMLAtURL($scheduleFile, true);
				
				$gameHTML = getScorecardHTML($league, $schedule, $GAME_ID, $prefix);
				$css = file_get_contents($prefix . "Stylesheets/scorecard.css");
            	
            	$key = uniqid();
            	
            	$mysqli = new mysqli("localhost", "root", "91079oak", "Stratomatic");
				if($mysqli === false) {
					die("ERROR: COULD NOT CONNECT. " . mysqli_connect_error() . " [ERROR: ES00]");
				}
            	
            	$sql = "INSERT INTO `AuthorizationKeys`(`KeyVal`, `Type`, `AdditionalParams`) VALUES ('$key', '0', 'USERNAME=$username&GAME_ID=$GAME_ID')";
				if(!$mysqli->query($sql)) {
				    echo("<p>Sorry. Something went wrong. Please try again. [ERROR: ES01]</p>");
				}
            	
            	//Now we can send the email
            	$to      = $_POST['emailTo'];
				$subject = 'Stratomatic Scorekeeper Scorecard';
				$comments = $_POST['comments'];
				$from = $_POST['emailFrom'];
				$message = "<html>
					<head>
					<style type='text/css'>
					$css
					</style>
					<title>HTML email</title>
					</head>
					<body>
					<p>$from wrote : $comments</p>
					<p>Email not displaying correctly? <a href='http://cablej.kd.io/Stratomatic/Stats/generateGameCard.php?key=$key'>Open it in your browser</a></p>
					<p><a href='http://cablej.kd.io'>Be sure to visit the Stratomatic Scorekeeper!</a></p>
					$gameHTML
					</body>
					</html>";
				
				$headers = "From: $from" . "\r\n";
				$headers .= "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
				
				$mail_sent = @mail( $to, $subject, $message, $headers ); 
				//if the message is sent successfully print "Mail sent". Otherwise print "Mail failed" 
				echo $mail_sent ? "Mail sent" : "Mail failed"; 
            } else {
            }
        ?>

        <?php
			include $prefix . 'footerTools.php';
        ?>
    </body>
</html>
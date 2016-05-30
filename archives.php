<html>
<head>
<title>Archives</title>
<?php

    $prefix = "";
    
    include $prefix . "XMLTools.php";
?>
</head>
<body>
<center>
<?php
	$currentPage = 2;
	include $prefix . 'header.php';
?>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<?php

	session_start();

if(isSet($_GET["league"])) {
$_SESSION["loggedIn"] = 1;
$_SESSION["Username"] = $_GET["league"];
}
    echo("<br>");
   echo("Current league: " . $_SESSION["Username"]);
    $leagues  = scandir($prefix."Leagues");
echo("<table border=1><tr><th>League Name</th><th>Activate</th></tr>");
    foreach($leagues as $league) {
     if($league == "." || $league == "..") continue;
         echo("<tr><td>$league</td><td><a href='?league=$league'>Activate</a></td></tr>");
    }
?>

<?php
	include $prefix . 'footerTools.php';
?>
</center>
</body>
</html>	
<html>
<head>
<title>Schedule</title>
<?php

    $prefix = "../";
    
    include $prefix . "XMLTools.php";
?>
<style>
  #schedule { list-style-type: none; margin: 0; padding: 0; width: 350px; }
  #schedule li { margin: 0 5px 5px 5px; padding: 5px; font-size: 1.2em; height: 1.5em; }
  html>body #schedule li { height: 1.5em; line-height: 1.2em; }
  .ui-state-highlight { height: 1.5em; line-height: 1.2em; }
  
  li
  {
    background:#398028;
	background: linear-gradient(top, #13990E 0%, #047800 100%);  
	background: -moz-linear-gradient(top, #13990E 0%, #047800 100%); 
	background: -webkit-linear-gradient(top, #13990E 0%,#047800 100%);
    border-radius:5px;
    color:White;
    border: 1px solid 003200;
}
  </style>
</head>
<body>
<center>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<?php

    $league = simplexml_load_file($leagueFile);
    $schedule = simplexml_load_file($scheduleFile);
    if(count($schedule->game) == 0) {
        echo("<p>No schedule.</p>");
    } else {
        echo("<center><button class='linkOnClick' onclick='submit()'>Save</button><ul id='schedule'>");
        foreach ($schedule->game as $game) {
            $team1 = $game->team[0]['name'];
            $team2 = $game->team[1]['name'];
            $id = $game['id'];
            echo("<li id='$id'>$id $team1-$team2 <a class='linkOnClick' onclick='removeGame($id)'>[delete]</a></li>");
            	
            }
        echo("</ul></center>");
    }
?>

<script>

$(function() {
	$( "#schedule" ).sortable({
	  placeholder: "ui-state-highlight"
	});
	$( "#schedule" ).disableSelection();
});

function removeGame(id) {
	$("#schedule #" + id).remove();
}

function submit() {
	
	$url = "saveEditSchedule.php?";

	$("#schedule li").each(function( index ) {
	  $url += "&ids[]=" + $(this).text().split(" ")[0];
	});
	
	window.location.replace($url);
}

</script>

<?php
	include $prefix . 'footerTools.php';
?>
</center>
</body>
</html>
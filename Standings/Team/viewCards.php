<html>
<head>
<title>View Cards</title>
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
<?php


    $prefix = "../../";
    
    include $prefix . "XMLTools.php";
?>
<style>
.back {
	width: 600px;
}
.front {
	width: 600px;
}

.ftable {
    display:none;

}

#options {
	float:left;
	background-color:#00690E;
	padding:5px;
	border-radius:3px;
}


body {
	background-image:url('http://upload.wikimedia.org/wikipedia/commons/9/9f/Wrigley_field_720.jpg');
	background-size:contain;
}
</style>
</head>
<body>
<style>
</style>
<center>
<div id="options">
<button class="linkOnClick" onclick="$('.ftable').show();$('.btable').hide();">View fronts</button>
<button class="linkOnClick" onclick="$('.ftable').hide();$('.btable').show();">View backs</button>
<table border="1" style="margin-top:10px" id='playerTable'>
<thead><tr><th>Display players:</p></th></tr></thead>
<tbody>
</tbody>
<tfoot><tr><td><button class="linkOnClick" onclick="addPlayer()">Add player</button>
<button class="linkOnClick" onclick="submitButton()">Submit</button></td></tr></tfoot>
</table>
</div>
<?php


    $league = getXMLatURL($leagueFile, true);
    $schedule = getXMLatURL($scheduleFile, true);
    
    $team_name = $_GET["name"];
    
    $playersList = $_GET["player"];
    
    $team = findTeam($team_name, $league);
    
    
		$row_code = "<tr class='player'><td><input type='text' class='playerName' name='name'></input></td></tr>";
		
		$quote = '"';
		$playerNames = array();
		$playerIDs = array();
		foreach($team->players->player as $p) {
			$playerNames[] = $p['first_name'] . " " . $p['last_name'];
			$playerIDs[] = $p['id'] . "";
		}
		
		
		$js_array = json_encode((array)$playerNames);
		$js_array2 = json_encode((array)$playerIDs);
		echo("
		<script>
		playerNames = $js_array;
		playerIDs = $js_array2;
		 </script>");
	 
		echo(
		"<script>function addPlayer() {
			$('#playerTable tbody').append($quote $row_code $quote);
			
			$( '.playerName' ).autocomplete({
				source: playerNames,
	autoFocus: true
				});
		}
		for(i=0; i<3; i++) addPlayer();
		</script>"
		
		);
    
    echo("<table>");
    
    if(count($playersList) == 0) {
		foreach($team->players->player as $player) {
			$playersList[] = $player["id"];
		}
    }
    
    foreach($playersList as $id) {
    	$front = $prefix . "Images/Cards/Fronts/$id.png";
    	$back = $prefix . "Images/Cards/Backs/$id.png";
    	$idfront = $id . "front";
    	$idback = $id . "back";
    	echo("<tr><td class='ftable' id='$idfront'><img class='front' src='$front' /></td><td class='btable' id='$idback'><img class='back' src='$back' /></td></tr>");
    	echo("<script>
    	
    	$( '#$idfront' ).click(function() {
 			$( '#$idfront' ).hide();
 			$( '#$idback' ).show();
		});
			
    	$( '#$idback' ).click(function() {
 			$( '#$idfront' ).show();
 			$( '#$idback' ).hide();
		});
    	</script>");
    }
    
    echo("</table>");
    
?>

<script>

function submitButton() {
	$url = "?name=Giants";
	var tableID = "playerTable";
	var teamData = gatherData(tableID);
	for(var i=0; i<teamData.length; i++) { //each player
		var playerName = teamData[i].name;
		var indexOfName = playerNames.indexOf(playerName);
		if(indexOfName == -1) {
			if(playerName == "") continue;
			else {
				alert("Error: player name '" + playerName + "' not found on team. Please try again.");
				return;
			}
		}
		var id = playerIDs[indexOfName]; //good to go!
		
		$url += "&player[]=" + id;
	}
	
	window.location.replace($url);
	
}


function gatherData(tableID) {
	 var data = [];
	
     var table = document.getElementById(tableID);
     for (r = 1; r < table.rows.length - 1; r++) { //skip header and footer

         var row = table.rows[r];
         var cells = row.cells;
         
         var rowData = {};
         rowData.stats = [];

         for (c = 0; c < cells.length; c++) {
             var cell = cells[c];
             var inputElem = cell.children[0];

             var value = inputElem.value;
             
             if(c == 0) {
             	rowData.name = value;
             } else {
             	rowData.stats.push(value);
             }

         }
         
         data.push(rowData);
     }
     
     return data;
 }

</script>

<?php
	include $prefix . 'footerTools.php';
?>
</body>
</center>
</html>
<html>
<head>
<title>League Stats</title>
<?php
$prefix = "../";

include $prefix . '/XMLTools.php';
?>
</head>
<body>
<center>
<?php
	$currentPage = 3;
	include $prefix . 'header.php';
?>
<style>
.table-wrapper {
	position:relative;
    height:90%;
    overflow:scroll;
}

td {
	text-align:center;
}
</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
<script src="http://code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
<!--<script src="../Scripts/colResizable-1.3/jquery.js"></script>-->
<script type="text/javascript" src="../Scripts/__jquery.tablesorter/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="../Scripts/jquery.fixedheadertable.min.js"></script>
<script type="text/javascript" src="../Scripts/DataTables-1.9.4/DataTables-1.9.4/media/js/jquery.dataTables.min.js"></script>
<!--<script type="text/javascript" src="../Scripts/colResizable-1.3/colResizable-1.3.min.js"></script>-->
<script>

function toggleColumn(type) {
	var classColumn = "." + type;
	$(classColumn).toggle();
}


</script>

<!--<div id="slider"></div>

  <script>$(function() {
    $( "#slider" ).slider({
      range: true,
      min: 0,
      max: 500,
      values: [ 75, 300 ],
      slide: function( event, ui ) {
        $( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
      }
    });
    $( "#amount" ).val( "$" + $( "#slider" ).slider( "values", 0 ) +
      " - $" + $( "#slider" ).slider( "values", 1 ) );
  });
  </script>-->
<?php
$league = getXMLAtURL($leagueFile, true);

//Prints league stats
echo("<br>");
$idLabel = "team_playerStats";
echo("<div class='table-wrapper'><table border='1' id='$idLabel' class='tablesorter'>");
echo"<thead><tr><th>Name</th><th>P</th><th>Team</th>";
for($j=0; $j<count($stat_abbrs_withAdditionalStats); $j++) {
	$type = $j == 0 ? "Batting" : "Pitching";
	$quote = "'";
	echo"<th onclick=\"toggleColumn($quote". $type ."$quote)\">" . $type . "</th>";
	for($h=0; $h<count($stat_abbrs_withAdditionalStats[$j]); $h++) {
		echo"<th style='' class='" . $type . "'>" . $stat_abbrs_withAdditionalStats[$j][$h] . "</th>";
	}
}
echo("</thead></tr><tbody>");

$leagueStats = getLeagueStatsArray($league);

for($m=0; $m<count($leagueStats); $m++) {

	$players = $leagueStats[$m][0];
	
	foreach($players as &$player) {
		$id = $player['id'];
		$teamName = getTeamName($id, $league);
		echo("<tr><th><a href='../Standings/Team/Player/displayPlayer.php?team=$teamName&id=$id' target='_blank'>" . $player["firstname"] . " " . $player["lastname"] . "</th><td>" .  substr($player["position"], 0, 2) . "</a></td><td>$teamName</td>");
		for($g=0; $g<count($player['stats']); $g++) {
			echo("<td></td>");
			$type = $g == 0 ? "Batting" : "Pitching";
			for($h=0; $h<count($player['stats'][$g]); $h++) {
				echo("<td style='' class='" . $type . "'>" . $player['stats'][$g][$h] . "</td>");
			}
		}
		echo("</tr>");
	}
}
echo("</tbody>");


echo("</table></div></center>");
//echo("<script>\$('#$idLabel').tablesorter({sortInitialOrder: 'desc'});</script>");

echo("
<div style='float:left;top:0px;position:absolute; height:100px; overflow:scroll'>
<select id='selectFilterType'><option value='name'>Name</option><option value='position'>Position</option><option value='team'>Team</option>");


for($j=0; $j<count($stat_abbrs_withAdditionalStats); $j++) {
	for($h=0; $h<count($stat_abbrs_withAdditionalStats[$j]); $h++) {
		echo "<option value='$j" . '_' . "$h'>" . $stat_abbrs_withAdditionalStats[$j][$h] . "</option>";
	}
}

echo("
</select>
<button onclick='appendFilter()'>Add Filter</button>
<div id='filterDiv'>
</div>
</div>
");
?>

<script>

	var currentFilters = [];
	var table;

	function appendFilter() {
		var value = $("#selectFilterType option:selected").val();
		var code = "<div id='"+ value + "_filter'><br>" + value + ": <input id='"+ value + "_greaterThan' placeholder='>' size='4'></input><input id='"+ value + "_lessThan' placeholder='<' size='4'></input><input id='"+ value + "_contains' placeholder='contains' size='8'></input><button onclick='applyFilter(\""+ value +"\")'>Apply</button></div>";
		$("#filterDiv").append(code);
	}
	
	function applyFilter(filterID) {
	
		var filter = {};
		var column = 0;
		if(filterID == "name") column = 0;
		else if(filterID == "position") column = 1;
		else if(filterID == "team") column = 2;
		else if(filterID.charAt(0) == '0') column = filterID.charAt(2) * 1 + 4;
		else if(filterID.charAt(0) == '1') column = filterID.charAt(2) * 1 + 20;
		var min = $("#" + filterID + "_greaterThan").val();
		var max = $("#" + filterID + "_lessThan").val();
		var contains = $("#" + filterID + "_contains").val();
		
		
		filter.column = column;
		filter.min = min;
		filter.max = max;
		filter.contains = contains;
		
		currentFilters.push(filter);
		
        table.fnDraw();
	}

    $.fn.dataTable.ext.afnFiltering.push(
    function( settings, data, dataIndex ) {
    
    	for(var i=0; i<currentFilters.length; i++) {
    		
    		var column = currentFilters[i].column;
    		var min = currentFilters[i].min * 1;
    		var max = currentFilters[i].max * 1;
    		var contains = currentFilters[i].contains;
    		
    		
	        var val = data[column];
	        if(val.indexOf(">") > -1) val = val.substring(val.lastIndexOf(">")+1,val.lastIndexOf("<"));
	        
        	if(contains != "" && val.indexOf(contains) > -1) {
        		return true;
        	}
	        
	        if(/^\d*\.?\d*$/.test(val)) {
	        	val = parseFloat(val);
	        	console.log(val);
	        	
		        if ( ( min == '' && max == '' && contains == '') ||
		             ( min == '' && val <= max ) ||
		             ( min <= val && '' == max ) ||
		             ( min <= val && val <= max ) )
		        {
		            return true;
		        }
	        
	        }
	        
    	}
    	
    	if(currentFilters.length == 0) {
    		return true;
    	}
    	
        return false;
    });

	$(document).ready(function() {
	    table = $('#team_playerStats').DataTable({
    'sScrollY': '80%',
    'bPaginate': false,
    'bScrollCollapse': true
	});
	     
	     
	     
	    // Event listener to the two range filtering inputs to redraw on input
	    $('#min, #max').keyup( function() {
	        table.draw();
	    } );
	    
	    //$('#team_playerStats_info').remove();
	} );

	/*$('#team_playerStats').dataTable( {
    'sScrollY': '80%',
    'bPaginate': false,
    'bScrollCollapse': true
	} )*/

</script>

<?php
	include $prefix . 'footerTools.php';
?>
</center>
</body>
</html>
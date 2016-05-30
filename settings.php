<?php
$prefix = "";

include 'XMLTools.php';

?>
<html>
    <head>
    <title>Settings</title>
    </head>
    <body>
    	<center>
		<?php
			$currentPage = 4;
			include $prefix . 'header.php';
		?>
		<h1>Printable Downloads</h1>
		<p><a href="Images/Uploads/X Chart.pdf">X Fielding Chart</a></p>
		<p><a href="Images/Uploads/resting.pdf">Injury/Resting Chart</a></p>
		<p><a href="Images/Uploads/pitcher.pdf">Pitcher Batting Cards</a></p>
		<p><a href="Images/Uploads/scorecard.pdf">Scorecard</a></p>
		<p><a href="Images/Uploads/rules.pdf">League Rules</a></p>
		<h1> Misc Settings</h1>
		<p><a href="archives.php">League archives</a></p>
		<!--<script>
		
		function getCookie(cname)
		{
			var name = cname + "=";
			var ca = document.cookie.split(';');
			for(var i=0; i<ca.length; i++) 
			  {
			  var c = ca[i].trim();
			  if (c.indexOf(name)==0) return c.substring(name.length,c.length);
			  }
			return "";
		}
		
		function setCookie(cname,cvalue,exdays)
		{
			var d = new Date();
			d.setTime(d.getTime()+(exdays*24*60*60*1000));
			var expires = "expires="+d.toGMTString();
			document.cookie = cname + "=" + cvalue + "; " + expires;
		}
		
		$(document).ready(function() {
			if(getCookie("instant") == 'true') $('#instant').prop('checked', true);
			else if(getCookie("instant") != 'false') setCookie("instant", "false", "7");
			
			
			$("#instant").change(function() {
			    if($("#instant").is(":checked")) {
			    	setCookie("instant", "true", "7");
			    } else {
			    	setCookie("instant", "false", "7");
			    }
			});
		})
		
		</script>
        <p>Load pages instantly (WARNING: BUGGY): <input type='checkbox' id='instant' /></p>-->
        <?php
			include 'footerTools.php';
        ?>
        </center>
    </body>
</html>
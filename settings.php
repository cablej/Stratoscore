<?php
$prefix = "";

include 'XMLTools.php';

?>
<html>
    <head>
    <title>Settings</title>
    </head>
    <body>
        <?php
            echo("<p>Welcome to $LEAGUE_NAME</p>");
        ?>
        <p><a href="index.php"><--Back to home</a></p>
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<script>
		
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
        <p>Load pages instantly (WARNING: BUGGY): <input type='checkbox' id='instant' /></p>
        <?php
			include 'footerTools.php';
        ?>
    </body>
</html>
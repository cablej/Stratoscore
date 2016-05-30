<?php

	$pagesArray = ["Home" => "/Stratomatic/index.php", "Standings" => "/Stratomatic/Standings", "Schedule" => "/Stratomatic/schedule.php", "Stats" => "/Stratomatic/Stats", "Settings" => "/Stratomatic/settings.php"];
	echo("<div class='buttonMenu'>");
	
	$count = 0;
	foreach($pagesArray as $title => $index) {
		echo("<a href='$index' style='border-left:0px;' class='" . ($currentPage == $count ? "currentPage" : "") . "'>$title</a>");
		$count++;	
	}
	echo("</div>");

?>
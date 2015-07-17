<?php

	if(isSet($_COOKIE['instant']) && $_COOKIE['instant'] == 'true') {
	    if($css) {
	    	if($prefix != "")
	    		echo ("<script type='text/javascript' src='" . $prefix . "Scripts/instantclick.min.js' data-no-instant></script><script data-no-instant>InstantClick.init('mousedown');</script>");
	    	else
	    		echo ("<script type='text/javascript' src='Scripts/instantclick.min.js' data-no-instant></script><script data-no-instant>InstantClick.init(50);</script>");
	    }
	}
?>
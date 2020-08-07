<?php

function addLinkstwitter() {
	echo "bk<div class=\"icon\">\n";
		echo "<a href=\"".DIR."admin/manage-add-ons/twitter\"><img src=\"".DIR."assets/plugins/twitter/twitter.png\" alt=\"Twitter\" title=\"Manage Twitter\" class=\"tooltip-top\" /></a>\n";
    	echo "<p><a href=\"".DIR."admin/manage-add-ons/twitter\" title=\"Manage Twitter\" class=\"tooltip-top\">Twitter</a></p>\n";
    echo "</div>\n";	
}

//add hook, where to execute a function
add_hook('admin_modules','addLinkstwitter');
?>
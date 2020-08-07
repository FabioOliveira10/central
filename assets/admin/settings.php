<?php
/*-------------------------------------------------------+
| The One Point - Content Management System
| http://www.theonepoint.co.uk/
+--------------------------------------------------------+
| Author: David Carr  Email: d.carr@theonepoint.co.uk
+--------------------------------------------------------+*/

if (!defined('included')){
die('You cannot access this file directly!');
}

if (isglobaladmin() || isadmin() || iseditor()){
	
echo "<div class=\"content-box-header\"><h3>Settings</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > Settings</p></div>";

messages();

//if is global admint show manage users
if (isglobaladmin()){

echo "<div class=\"icon\">\n";
echo "<a href=\"".DIRADMIN."manage-users\"><img src=\"".DIR."assets/images/icons/users.png\" alt=\"Manage Users\" title=\"Manage Users\" class=\"tooltip-top\" /></a>\n";
echo "<p><a href=\"".DIRADMIN."manage-users\" title=\"Manage Users\" class=\"tooltip-top\">Manage Users</a></p>\n";
echo "</div>\n";

echo "<div class=\"icon\">\n";
echo "<a href=\"".DIRADMIN."site-settings\"><img src=\"".DIR."assets/images/icons/config.png\" alt=\"Site Settings\" title=\"Site Settings\" class=\"tooltip-top\" /></a>\n";
echo "<p><a href=\"".DIRADMIN."site-settings\" title=\"Site Settings\" class=\"tooltip-top\">Site Settings</a></p>\n";
echo "</div>\n";

echo "<div class=\"icon\">\n";
echo "<a href=\"".DIRADMIN."themes\"><img src=\"".DIR."assets/images/icons/themes.png\" alt=\"Themes\" title=\"Templates\" class=\"tooltip-top\" /></a>\n";
echo "<p><a href=\"".DIRADMIN."themes\" title=\"Manage Templates\" class=\"tooltip-top\">Templates</a></p>\n";
echo "</div>\n";
	
echo "<div class=\"icon\">\n";
echo "<a href=\"".DIR."assets/includes/download.php?xdb\"><img src=\"".DIR."assets/images/icons/database.png\" alt=\"Backup Database\" title=\"Backup Database\" class=\"tooltip-top\" /></a>\n";
echo "<p><a href=\"".DIR."assets/includes/download.php?xdb\" title=\"Backup Database\" class=\"tooltip-top\">Backup Database</a></p>\n";
echo "</div>\n";

}

echo "<div class=\"icon\">\n";
echo "<a href=\"".DIRADMIN."admin-details\"><img src=\"".DIR."assets/images/icons/homepage.png\" alt=\"Admin Details\" title=\"Admin Details\" class=\"tooltip-top\" /></a>\n";
echo "<p><a href=\"".DIRADMIN."admin-details\" title=\"Admin Details\" class=\"tooltip-top\">Admin Details</a></p>\n";
echo "</div>\n";

echo "<div class=\"icon\">\n";
echo "<a href=\"".DIRADMIN."change-pass\"><img src=\"".DIR."assets/images/icons/password.png\" alt=\"Change Password\" title=\"Change Password\" class=\"tooltip-top\" /></a>\n";
echo "<p><a href=\"".DIRADMIN."change-pass\" title=\"Change Password\" class=\"tooltip-top\">Change Password</a></p>\n";
echo "</div>\n";

echo "</div>";

} else {
header('Location: '.DIRADMIN);
exit;
}?>
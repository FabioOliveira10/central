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
	
echo "<div class=\"content-box-header\"><h3>Manage Add-ons</h3></div> 			
<div class=\"content-box-content\">";

	echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > Manage Add-ons</p></div>";

	if ($SYS->hooks_exist('admin_modules')) 
	{ 
			ob_start();
			$SYS->execute_hooks('admin_modules',DIRADMIN);
			$mod = ob_get_clean();
			$mod = explode("bk",$mod);
			sort($mod);
			foreach ($mod as $mod){
				echo $mod; 	
			}	
	
	} else {
	echo "<p>No add-ons yet</p>\n";
	}

echo "</div>";

} else {
header('Location: '.DIRADMIN);
exit;
}?>
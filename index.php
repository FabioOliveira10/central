<?php
/*-------------------------------------------------------+
| The One Point - Content Management System
| http://www.theonepoint.co.uk/
+--------------------------------------------------------+
| Author: David Carr  Email: d.carr@theonepoint.co.uk
+--------------------------------------------------------+*/

require ('assets/includes/config.php');
require ('assets/includes/settings.php');
require ('assets/includes/functions.php');

//if system not installed call installer
if (!defined('installed')){
header('Location: install/index.php');
} 

get_theme();

ob_flush(); 
?>
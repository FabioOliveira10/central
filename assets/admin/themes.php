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

if (isglobaladmin() || isadmin()){
	
echo "<div class=\"content-box-header\"><h3>Templates</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."settings\">Settings</a> > Templates</p></div>";

echo messages();

$result = mysql_query("SELECT * FROM ".PREFIX."styles ORDER BY styleID")or die(mysql_error());
?>
<table class="stripeMe">
<tr align="center">
<td width="41%" align="left"><strong>Template</strong></td>
<td width="59%"><strong>Action</strong></td>
</tr>
<?php
while ($row = mysql_fetch_object($result)) {
?>
<tr>
<td><?php echo $row->themeTitle;?></td>
<td><a href="<?php echo DIRADMIN;?>themes/edit-theme-<?php echo $row->styleID;?>" class="tooltip-top" title="Edit"><img src="<?php echo DIR;?>assets/images/icons/action-edit.png" alt="Edit" /></a> <a href="#" id="<?php echo $row->styleID;?>" rel="deltheme" title="<?php echo $row->themeTitle;?>" class="delete_button"><img src="<?php echo DIR;?>assets/images/icons/action-del.png" alt="Delete" /></a></td>
</tr>
<?php
}
?>
</table>
</div>
<?php
} else {
header('Location: '.DIRADMIN);
exit;
}?>
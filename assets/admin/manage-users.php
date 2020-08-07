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

if (isglobaladmin()){
	
echo "<div class=\"content-box-header\"><h3>Manage Users</h3></div> 			
<div class=\"content-box-content\">";

messages();

print_r($_SESSION['success']);

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."settings\">Settings</a> > Manage Users</p></div>";

$result = mysql_query("SELECT * FROM ".PREFIX."members")or die(mysql_error());

?>
<table class="stripeMe">
<tr>
<th><strong>Users</strong></th>
<th><strong>Level</strong></th>
<th><strong>Action</strong></th>
</tr>
<?php
while ($row = mysql_fetch_object($result)){
?>
<tr>
<td><?php echo $row->username;?></td>
<td><?php
if($row->level == 0){ echo "Global Admin"; }
if($row->level == 1){ echo "Admin"; }
if($row->level == 2){ echo "Editor"; }
?></td>
<td valign="top"><a href="<?php echo DIRADMIN;?>manage-users/edit-user-<?php echo $row->memberID;?>" class="tooltip-top" title="Edit"><img src="<?php echo DIR;?>assets/images/icons/action-edit.png" alt="Edit" /></a> 

<a href="#" id="<?php echo $row->memberID;?>" rel="deluser" title="<?php echo $row->username;?>" class="delete_button"><img src="<?php echo DIR;?>assets/images/icons/action-del.png" alt="Delete" /></a> </td>
</tr>
<?php }	?>
</table>

<p><a href="<?php echo DIRADMIN;?>manage-users/add-user" class="button tooltip-right" title="Create a new user">Add User</a></p>
</div>
<?php
} else {
header('Location: '.DIRADMINs);
exit;
}?>
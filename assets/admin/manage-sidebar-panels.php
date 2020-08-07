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

echo "<div class=\"content-box-header\"><h3>Manage Sidebar Panels</h3></div> 			
<div class=\"content-box-content\">";

if($_POST["Submit"])
{
	$i = 0;
    foreach($_POST['id'] as $value) 
	{
        $sql1 = mysql_query("UPDATE ".PREFIX."sidebars SET sidebarOrder='".$_POST['sidebarOrder'][$i]."' WHERE sidebarID='$value' ")or die(mysql_error());
    $i++;}
	
//redirect user 
$_SESSION['success'] = 'Order Updated';
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
}


echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > Manage Sidebar Panels</p></div>";

echo messages();

$result = mysql_query("SELECT * FROM ".PREFIX."sidebars ORDER BY sidebarOrder")or die(mysql_error());
$Rows = mysql_num_rows($result);
echo '<form name="form1" method="post" action="">';
?>
<table class="stripeMe">
<tr>
<th><strong>Panel</strong></th>
<th><strong>Action</strong></th>
<th><strong>Order</strong></th>
</tr>
<?php
while ($row = mysql_fetch_object($result)){
?>
<tr>
<td><?php echo $row->sidebarTitle;?></td>
<td valign="top"><a href="<?php echo DIRADMIN;?>manage-sidebar-panels/edit-sidebar-panel-<?php echo $row->sidebarID;?>" class="tooltip-top" title="Edit"><img src="<?php echo DIR;?>assets/images/icons/action-edit.png" alt="Edit" /></a>

<a href="#" id="<?php echo $row->sidebarID;?>" rel="delpanel" title="<?php echo $row->sidebarTitle;?>" class="delete_button"><img src="<?php echo DIR;?>assets/images/icons/action-del.png" alt="Delete" /></a></td>

<td align="center" valign="top">
<input type="hidden" name="id[]" value="<?php echo $row->sidebarID;?>" readonly><input name="sidebarOrder[]" class="tooltip-right" title="Set Sidebar Order" type="text" value="<?php echo $row->sidebarOrder;?>">
</td>

</tr>
<?php }	?>
</table>
<p><a href="<?php echo DIRADMIN;?>manage-sidebar-panels/add-sidebar-panel" class="button tooltip-top" title="Create a Sidebar ">Add Sidebar Panel</a>
<input type="submit" name="Submit" value="Update Order" class="button tooltip-right" title="Update Sidebar Order" /></p>
</div>
<?php
} else {
header('Location: '.DIRADMIN);
exit;
}?>
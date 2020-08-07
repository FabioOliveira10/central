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

echo "<div class=\"content-box-header\"><h3>Manage Pages</h3></div> 			
<div class=\"content-box-content\">";

if($_POST["Submit"])
{
	$i = 0;
    foreach($_POST['id'] as $value) 
	{
        $sql1 = mysql_query("UPDATE ".PREFIX."pages SET pageOrder='".$_POST['pageOrder'][$i]."' WHERE pageID='$value' ")or die(mysql_error());
    $i++;}
	
//redirect user 
$_SESSION['success'] = 'Order Updated';
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
}

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > Manage Pages</p></div>";

echo messages();
echo '<form name="form1" method="post" action="">';
?>
<table class="stripeMe">
<tr>
<th><strong>Pages</strong></th>
<th><strong>Action</strong></th>
<th><strong>View Level</strong></th>
<th><strong>Active</strong></th>
<th><strong>Order</strong></th>
</tr>
<tr>
<td><b>Home</b></td>
<td><a href="manage-pages/edit-page-1" class="tooltip-top" title="Edit"><img src="<?php echo DIR;?>assets/images/icons/action-edit.png" alt="Edit" /></a></td>
<td><b>Public</b></td>
<td></td>
<?php
$result = mysql_query("SELECT * FROM ".PREFIX."pages WHERE pageParent='0' AND pageStandAlone='0' AND isRoot='1' ORDER BY pageOrder");
$Rows = mysql_num_rows($result);
$i = 0;
while ($row = mysql_fetch_object($result))
{
		if($row->pageVis == 0){ $row->pageVis = 'Admins'; }
		if($row->pageVis == 3){ $row->pageVis = 'Public'; }
		if($row->pageActive == 1){ $row->pageActive = 'Yes'; } else { $row->pageActive = 'No';}
		$parent1 = $row->pageID;
		$result1 = mysql_query("SELECT * FROM ".PREFIX."pages WHERE pageParent='$parent1' AND pageStandAlone='0' ORDER BY pageOrder");
		
		$sub = "";
		while ($row1 = mysql_fetch_object($result1))
		{
			if($row1->pageVis == 0){ $row1->pageVis = 'Admins'; }
		    if($row1->pageVis == 3){ $row1->pageVis = 'Public'; }
			if($row1->pageActive == 1){ $row1->pageActive = 'Yes'; } else { $row1->pageActive = 'No';}
			$parent = $row1->pageParent;
			global $parent;
			$sub.= "<tr>\n";
			
			$sub.= "<td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; $row1->pageTitle</td>\n";		
			$sub.= "<td><a href=\"manage-pages/edit-page-$row1->pageID\" class=\"tooltip-top\" title=\"Edit\"><img src=\"".DIR."assets/images/icons/action-edit.png\" alt=\"Edit\" /></a> <a href=\"#\" id=\"$row1->pageID\" rel=\"delpage\" title=\"$row1->pageSlug\" class=\"delete_button\"><img src=\"".DIR."assets/images/icons/action-del.png\" alt=\"Delete\" /></a></td>\n";
			$sub.= "<td><b>$row1->pageVis</b></td>";
			$sub.= "<td><b>$row1->pageActive</b></td>";
			
			$sub.= "<td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <input type=\"hidden\" name=\"id[]\" value=\"$row1->pageID\" readonly><input name=\"pageOrder[]\" class=\"tooltip-right\" title=\"Set Page Order\" type=\"text\" value=\"$row1->pageOrder\"></td>\n";
			
			$sub.= "</tr>\n";
		}
	?>
	<tr>
	<td><b><?php echo $row->pageTitle;?></b></td>	
	
	<td align="center" valign="top">
	<a href="<?php echo DIRADMIN;?>manage-pages/edit-page-<?php echo $row->pageID;?>" class="tooltip-top" title="Edit"><img src="<?php echo DIR;?>assets/images/icons/action-edit.png" alt="Edit" /></a> 

	<?php if ($row->pageID != '1') {?> <a href="#" id="<?php echo $row->pageID;?>" rel="delpage" title="<?php echo $row->pageSlug;?>" class="delete_button"><img src="<?php echo DIR;?>assets/images/icons/action-del.png" alt="Delete" /></a> <?php } ?>
	
	</td>
    <td><b><?php echo $row->pageVis;?></b></td>	
    <td><b><?php echo $row->pageActive;?></b></td>	
	
<td align="center" valign="top">
<input type="hidden" name="id[]" value="<?php echo $row->pageID;?>" readonly><input name="pageOrder[]" class="tooltip-right" title="Set Page Order" type="text" value="<?php echo $row->pageOrder;?>">
</td>




	
	</tr> 
	<?php
	if($row->pageID == $parent){ echo $sub; } else { /*echo "</li>\n";*/ } 
$i ++;
}
?>
</table>
<p><input type="submit" name="Submit" value="Update Order" class="button tooltip-right" title="Update Page Order" /></p>

<p>&nbsp;</p>

<?php
$result = mysql_query("SELECT * FROM ".PREFIX."pages WHERE pageStandAlone='1'");
$Rows = mysql_num_rows($result);
$i = 0;
?>
<table class="stripeMe">
<tr align="center" >
<th><strong>Stand Alone Pages</strong></th>
<th><strong>Action</strong></th>
<th><strong>View Level</strong></th>
<th><strong>Active</strong></th>
</tr>
<tr>
<?php
while ($i <$Rows)

	{
	//Add resulting tablerow to relvant variable
	$pageID = mysql_result($result, $i, "pageID");
	$pageTitle = mysql_result($result, $i, "pageTitle");
	$pageSlug = mysql_result($result, $i, "pageSlug");
	$pageVis = mysql_result($result, $i, "pageVis");
	$pageActive = mysql_result($result, $i, "pageActive");
	
	if($pageVis == 0){ $pageVis = 'Admins'; }
	if($pageVis == 3){ $pageVis = 'Public'; }
	if($pageActive == 1){ $pageActive = 'Yes'; } else { $pageActive = 'No';}
	
	
?>
<tr>
<td><?php echo $pageTitle;?></td>

<td align="center" valign="top"> <a href="<?php echo DIRADMIN;?>manage-pages/edit-page-<?php echo $pageID;?>" class="tooltip-top" title="Edit"><img src="<?php echo DIR;?>assets/images/icons/action-edit.png" alt="Edit" /></a>

 <?php if ($pageID != '1') {?> <a href="#" id="<?php echo $pageID;?>" rel="delpage" title="<?php echo $pageSlug;?>" class="delete_button"><img src="<?php echo DIR;?>assets/images/icons/action-del.png" alt="Delete" /></a> <?php } ?></td>
 <td><b><?php echo $pageVis;?></b></td>	
 <td><b><?php echo $pageActive;?></b></td>	
</tr>
<?php $i ++;} ?>
</table>


<p><a href="<?php echo DIRADMIN;?>manage-pages/add-page" class="button tooltip-top" title="Create a new page">Add Page</a></p>
</div>
<?php
} else {
header('Location: '.DIRADMIN);
exit;
}?>
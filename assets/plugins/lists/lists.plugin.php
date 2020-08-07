<?php
/*-------------------------------------------------------+
| The One Point - Content Management System
| http://www.theonepoint.co.uk/
+--------------------------------------------------------+
| Author: David Carr  Email: d.carr@theonepoint.co.uk
| Plugin version 3.0
+--------------------------------------------------------+*/

/* hooks

above_doctype - code above doctype
header_css - code for including css files
header_js_script - code for including js files
header_js_jquery - code for jquery
header_slim_editor - code for stripped down editor
cont - code for plugins in main content
page_requester - code to request addtitional pages
del - delete section
admin_modules  - add link to manage add-ons

*/

$cfile = ".htaccess";
$fo = fopen($cfile, 'r');
//get file contents and work out the file content size in bytes
$data = fread($fo, filesize($cfile));
//close the file
fclose($fo);

if (preg_match('/lists/', $data))
{
} else { 
$newData = "
RewriteRule ^admin/manage-add-ons/lists$ 			                admin.php?lists=$1 [L]
RewriteRule ^admin/manage-add-ons/lists/$			                admin.php?lists=$1 [L]

RewriteRule ^admin/manage-add-ons/lists/add-list$ 			    admin.php?add-list=$1 [L]
RewriteRule ^admin/manage-add-ons/lists/add-list/$			    admin.php?add-list=$1 [L]

RewriteRule ^admin/manage-add-ons/lists/edit-list-([^/]+)$ 		admin.php?edit-list=$1 [L]
RewriteRule ^admin/manage-add-ons/lists/edit-list-([^/]+)/$		admin.php?edit-list=$1 [L]

RewriteRule ^admin/manage-add-ons/lists/items-([^/]+)$ 		    admin.php?items=$1 [L]
RewriteRule ^admin/manage-add-ons/lists/items-([^/]+)/$  	    admin.php?items=$1 [L]

RewriteRule ^admin/manage-add-ons/lists/items/add-item-([^/]+)$   admin.php?add-item=$1 [L]
RewriteRule ^admin/manage-add-ons/lists/items/add-item-([^/]+)/$  admin.php?add-item=$1 [L]

RewriteRule ^admin/manage-add-ons/lists/items/edit-item-([^/]+)$  admin.php?edit-item=$1 [L]
RewriteRule ^admin/manage-add-ons/lists/items/edit-item-([^/]+)/$ admin.php?edit-item=$1 [L]\n###\n";

$data = str_replace("###",$newData,$data);

$myFile = $cfile;
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $data);
fclose($fh);

mysql_query("CREATE TABLE IF NOT EXISTS `".PREFIX."lists` (
  `listID` int(11) NOT NULL auto_increment,
  `listTitle` varchar(255) NOT NULL,
  `listType` varchar(255) NOT NULL,
  `limitNum` int(11) NOT NULL default '25',
  PRIMARY KEY  (`listID`)
) ENGINE=MyISAM")or die('cannot make table lists due to: '.mysql_error());

mysql_query("CREATE TABLE IF NOT EXISTS `".PREFIX."list_items` (
  `itemID` int(11) NOT NULL auto_increment,
  `itemTitle` varchar(255) NOT NULL,
  `itemSlug` varchar(255) NOT NULL,
  `itemImg` text NOT NULL,
  `listImgExtra` text NOT NULL,
  `itemDesc` text NOT NULL,
  `itemCont` text NOT NULL,
  `listID` int(11) NOT NULL,
  `listOrder` int(11) NOT NULL,
  PRIMARY KEY  (`itemID`)
) ENGINE=MyISAM")or die('cannot make table list_items due to: '.mysql_error());


}


function managelists()
{
	$curpage = true;	
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Lists</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIR."admin/manage-add-ons\">Manage Add-ons</a> > Lists</p></div>";

echo messages();

echo "<p>To implement the list insert the list title in brackets like [mylist] into any page you want the list to be displayed.</p>\n";

$result = mysql_query("SELECT * FROM ".PREFIX."lists")or die(mysql_error());
$Rows = mysql_num_rows($result);
?>
<table class="stripeMe">
<tr align="center">
<td width="42%" align="left"><strong>Lists</strong></td>
<td width="42%" align="left"><strong>Type</strong></td>
<td width="45%"><strong>Action</strong></td>
</tr>
<?php
while ($row = mysql_fetch_object($result)) {
?>
<tr>
<td><a href="<?php echo DIRADMIN;?>manage-add-ons/lists/items-<?php echo $row->listID;?>" class="tooltip-top" title="Manage items in this category"><?php echo $row->listTitle;?></a></td>
<td><?php echo $row->listType;?></td>
<td align="center" valign="top">

<a href="<?php echo DIRADMIN;?>manage-add-ons/lists/edit-list-<?php echo $row->listID;?>" class="tooltip-top" title="Edit"><img src="<?php echo DIR;?>assets/images/icons/action-edit.png" alt="Edit" /></a> 

<a href="#" id="<?php echo $row->listID;?>" rel="dellist" title="<?php echo $row->listTitle;?>" class="delete_button"><img src="<?php echo DIR;?>assets/images/icons/action-del.png" alt="Delete" /></a></td>
</tr>
<?php
}
?>
</table>
<p><a href="<?php echo DIRADMIN;?>manage-add-ons/lists/add-list" class="button tooltip-right" title="Create new List">Add List</a></p>

</div>

<?php
} else {
url(DIR);

}
}


function addList()
{
	$curpage = true;
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Add List</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/lists\">Lists</a> > Add List</p></div>";

echo messages();

if(isset($_POST['cancel'])){
$_SESSION['info'] = "List was not created";
url('manage-add-ons/lists');
}

// if form submitted then process form
if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$listTitle = trim($_POST['listTitle']);
if (strlen($listTitle) < 1 || strlen($listTitle) > 255) {
$error[] = 'Title must be at between 1 and 255 charactors.';
}

$limitNum = trim($_POST['limitNum']);
if (strlen($limitNum) < 1) {
$error[] = 'Please input number of images to show per page';
}

// if valadation is okay then carry on
if (!$error) {
	
	// post form data 
   $listTitle     = $_POST['listTitle'];
   $limitNum       = $_POST['limitNum'];
   $listType      = $_POST['listType'];
   
   //strip any tags from input
   $listTitle   = strip_tags($listTitle);   
   
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$listTitle   = addslashes($listTitle);
	 }
   
   // remove any harhful code and stop sql injection
   $listTitle = mysql_real_escape_string($listTitle);
   $limitNum = mysql_real_escape_string($limitNum); 
   
 
// insert data into images table
$query = "INSERT INTO ".PREFIX."lists (listTitle,limitNum,listType) VALUES ('$listTitle','$limitNum','$listType')";
$result  = mysql_query($query) or die ('list'.mysql_error());

pingSE(DIR."sitemap.php","bing");
pingSE(DIR."sitemap.php","ask");
pingSE(DIR."sitemap.php","google");

// show a message to confirm results	
if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'List Added';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'List Added';
url('manage-add-ons/lists');	
}	
		
 
}
}

	
//dispaly any errors
errors($error);

?>

<form enctype="multipart/form-data" action="" method="post">
<p>Title:<br /><input type="text" class="box-medium tooltip-right" title="Enter the list title" name="listTitle" <?php if (isset($error)){ echo "value=\"$listTitle\""; }?>/></p>

<p>Number of images to show per page<br /><input class="box-small tooltip-right" title="Enter number of items to show per page" name="limitNum" type="text" value="<?php if (isset($error)){ echo $limitNum; } else { echo "25";} ?>" size="3" /></p>

<p>List Type<br  /><select name="listType" class="box-medium tooltip-right" title="Select the list type">
<option value="1col" <?php if($_POST['listType']=='1col'){echo "selected=selected";}?>>1 Column</option>
<option value="2col" <?php if($_POST['listType']=='2col'){echo "selected=selected";}?>>2 Column</option>
<option value="grid" <?php if($_POST['listType']=='grid'){echo "selected=selected";}?>>Grid</option>
</select></p>

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save list and return to lists">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save list and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save list and return to lists">
</form>
</div>
<?php

} else {
url(DIR);
}		
}

function editList()
{
	$curpage = true;	
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Edit List</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/lists\">Lists</a> > Edit List</p></div>";

echo messages();

if(isset($_POST['cancel'])){
$_SESSION['info'] = "List was not created";
url('manage-add-ons/lists');
}

// if form submitted then process form
if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$listTitle = trim($_POST['listTitle']);
if (strlen($listTitle) < 1 || strlen($listTitle) > 255) {
$error[] = 'Title must be at between 1 and 255 charactors.';
}

$limitNum = trim($_POST['limitNum']);
if (strlen($limitNum) < 1) {
$error[] = 'Please input number of images to show per page';
}

// if valadation is okay then carry on
if (!$error) {
	
	// post form data 
   $listID     = $_POST['listID'];
   $listTitle     = $_POST['listTitle'];
   $limitNum       = $_POST['limitNum'];
   $listType   = $_POST['listType'];
   
   //strip any tags from input
   $listTitle   = strip_tags($listTitle);   
   
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$listTitle   = addslashes($listTitle);
	 }
   
   // remove any harhful code and stop sql injection
   $listTitle = mysql_real_escape_string($listTitle); 
   $limitNum = mysql_real_escape_string($limitNum);   
  

// insert data into images table
$query = "UPDATE ".PREFIX."lists SET listTitle = '$listTitle', limitNum = '$limitNum', listType='$listType' WHERE listID='$listID'";
$result  = mysql_query($query) or die (mysql_error());

pingSE(DIR."sitemap.php","bing");
pingSE(DIR."sitemap.php","ask");
pingSE(DIR."sitemap.php","google");

// show a message to confirm results	
if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'List Updated';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'List Updated';
url('manage-add-ons/lists');	
}		
 
}
}

	
//dispaly any errors
errors($error);

$result = mysql_query("SELECT * FROM ".PREFIX."lists WHERE listID='{$_GET['edit-list']}'");
while($row = mysql_fetch_object($result)){

// Define the number of results per allclientspages
$limitNum = $row->limitNum;
?>

<form action="" method="post">
<input type="hidden" name="listID" value="<?=$row->listID;?>" />
<p>Image Name:<br /><input type="text" class="box-medium tooltip-right" title="Enter the list title" name="listTitle" value="<?php echo $row->listTitle;?>"/></p>

<p>Number of images to show per page<br /><input class="box-small tooltip-right" title="Enter number of items to show per page" name="limitNum" type="text" value="<?php echo $row->limitNum; ?>" size="3" /></p>

<p>List Type<br  /><select name="listType" class="box-medium tooltip-right" title="Select the list type">
<option value="1col" <?php if($row->listType=='1col'){echo "selected=selected";}?>>1 Column</option>
<option value="2col" <?php if($row->listType=='2col'){echo "selected=selected";}?>>2 Column</option>
<option value="grid" <?php if($row->listType=='grid'){echo "selected=selected";}?>>Grid</option>
</select></p>

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save list and return to lists">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save list and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save list and return to lists">
</form>	
</div>
<?php }

} else {
url(DIR);

}	
}

function manageItems()
{
	$curpage = true;;
	if (isglobaladmin() || isadmin()){
	
	
if($_POST["Submit"])
{
	$i = 0;
    foreach($_POST['id'] as $value) 
	{
        $sql1 = mysql_query("UPDATE ".PREFIX."list_items SET listOrder='".$_POST['listOrder'][$i]."' WHERE itemID='$value' ")or die(mysql_error());
    $i++;}
	
//redirect user 
$_SESSION['success'] = 'Order Updated';
header('Location:'.$_SERVER['HTTP_REFERER']);
}	
	
		
$result = mysql_query("SELECT * FROM ".PREFIX."list_items WHERE listID='{$_GET['items']}' ORDER BY listOrder")or die(mysql_error());
$Rows = mysql_num_rows($result);

$sql = mysql_query("SELECT * FROM ".PREFIX."lists WHERE listID='{$_GET['items']}'")or die(mysql_error());
$row = mysql_fetch_object($sql);
$listTitle = $row->listTitle;
$listID = $row->listID;

echo "<div class=\"content-box-header\"><h3>$listTitle</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/lists\">lists</a> > <a href=\"".DIRADMIN."manage-add-ons/lists/item-$listID\">$listTitle</a></p></div>";

echo messages();
echo '<form name="form1" method="post" action="">';
?>
<p><a href="<?php echo DIRADMIN;?>manage-add-ons/lists/items/add-item-<?php echo $listID;?>" class="button tooltip-top" title="Add item to the current list">Add Item</a></p>
<table class="stripeMe">
<tr align="center">
<td width="42%" align="left"><strong>lists</strong></td>
<td width="45%"><strong>Order</strong></td>
<td width="45%"><strong>Action</strong></td>
</tr>
<?php
while ($row = mysql_fetch_object($result)) {
?>
<tr>
<td><?php echo $row->itemTitle;?></td>
<td><input type="hidden" name="id[]" value="<?php echo $row->itemID;?>" readonly><input name="listOrder[]" type="text" value="<?php echo $row->listOrder;?>"></td>

<td align="center" valign="top">
<a href="<?php echo DIRADMIN;?>manage-add-ons/lists/items/edit-item-<?php echo $row->itemID;?>" class="tooltip-top" title="Edit"><img src="<?php echo DIR;?>assets/images/icons/action-edit.png" alt="Edit" /></a> 

<a href="#" id="<?php echo $row->itemID;?>" rel="delitem" title="<?php echo $row->itemTitle;?>" class="delete_button"><img src="<?php echo DIR;?>assets/images/icons/action-del.png" alt="Delete" /></a>
</td>
</tr>
<?php
}
?>
</table>
<p><input type="submit" name="Submit" value="Update Order" title="Save order of items" class="button tooltip-top" />
<a href="<?php echo DIRADMIN;?>manage-add-ons/lists/items/add-item-<?php echo $listID;?>" class="button tooltip-top" title="Add item to the current list">Add Item</a></p>
</div>
<?php
} else {
url(DIR);

}		
}


function additem()
{
	$curpage = true;	
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Add Image</h3></div> 			
<div class=\"content-box-content\">";
	
$sql = mysql_query("SELECT * FROM ".PREFIX."lists WHERE listID='{$_GET['add-item']}'")or die(mysql_error());
$row = mysql_fetch_object($sql);
$listTitle = $row->listTitle;
$listID = $row->listID;
$listType = $row->listType;

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/lists\">lists</a> > <a href=\"".DIRADMIN."manage-add-ons/lists/items-$listID\">$listTitle</a> > Add item  $row->listType</p></div>";

$listID = $_GET['add-item'];

echo messages();

if(isset($_POST['cancel'])){
$_SESSION['info'] = "Item was not created";
url('manage-add-ons/lists/items-'.$listID);
}

// if form submitted then process form
if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$itemTitle = trim($_POST['itemTitle']);
if (strlen($itemTitle) < 1 || strlen($itemTitle) > 255) {
$error[] = 'Title must be at between 1 and 255 charactors.';
}

// if valadation is okay then carry on
if (!$error) {
	
	// post form data 
   $itemTitle     = $_POST['itemTitle'];
   $itemDesc       = $_POST['itemDesc'];
   $itemCont       = $_POST['itemCont'];
   $itemImg       = $_POST['itemImg'];
   $listOrder     = $_POST['listOrder'];
   $listImgExtra  = $_POST['listImgExtra'];
   
   //strip any tags from input
   $itemTitle   = strip_tags($itemTitle);
 
   
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$imageTitle   = addslashes($imageTitle);
	 }
   
   // remove any harhful code and stop sql injection
   $imageTitle = mysql_real_escape_string($imageTitle); 
   
   
   	   $itemSlug  = strtolower(str_replace(" ", '-', $itemTitle));
	   $itemSlug  = strtolower(str_replace("'", '', $itemSlug));
	   $itemSlug  = strtolower(str_replace("?", '', $itemSlug));
	   $itemSlug  = strtolower(str_replace("/", '', $itemSlug));
	   $itemSlug  = strtolower(str_replace("!", '', $itemSlug));
	   $itemSlug  = strtolower(str_replace(".", '', $itemSlug));
	   $itemSlug  = strtolower(str_replace(",", '', $itemSlug));
	   $itemSlug  = strtolower(str_replace("@", '', $itemSlug));
	   $itemSlug  = strtolower(str_replace("_", '', $itemSlug));
	   $itemSlug  = strtolower(str_replace("--", '-', $itemSlug));   

// insert data into images table
$query = "INSERT INTO ".PREFIX."list_items (itemTitle, itemDesc, itemCont,itemImg,listImgExtra, listID,listOrder) VALUES
  ('$itemTitle', '$itemDesc', '$itemCont','$itemImg','$listImgExtra','$listID','$listOrder')";
  $result  = mysql_query($query) or die ('Cannot add image because: '. mysql_error());
  
$getID = mysql_insert_id();

$itemSlug = $itemSlug.'-'.$getID;

$result = mysql_query("UPDATE ".PREFIX."list_items SET itemSlug='$itemSlug'WHERE itemID='$getID'");
 	
pingSE(DIR."sitemap.php","bing");
pingSE(DIR."sitemap.php","ask");
pingSE(DIR."sitemap.php","google");


if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'Item Added';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'Item Added';
url('manage-add-ons/lists/items-'.$listID);
} 	
 
}
}

	
//dispaly any errors
errors($error);

?>

<form action="" method="post">
<p><label>Title:</label> <input type="text" class="box-medium tooltip-right" title="Enter the title"  name="itemTitle" <?php if (isset($error)){ echo "value=\"$itemTitle\""; }?>/></p>
<?php if($row->listType == '1col' || $row->listType == '2col'){?>
<p><label>Description</label><textarea name="itemDesc" id="edit1" cols="60" rows="10"><?php if (isset($error)){ echo "$itemDesc"; }?></textarea></p>
<?php } ?>
<?php if($row->listType == 'grid'){?>
<p><label>Content</label><textarea name="itemCont" id="edit2" cols="60" rows="10"><?php if (isset($error)){ echo "$itemCont"; }?></textarea></p>
<?php } ?>
<p><label>Image</label><textarea name="itemImg" id="edit3" cols="60" rows="10"><?php if (isset($error)){ echo "$itemImg"; }?></textarea></p>
<?php if($row->listType == 'grid'){?>
<p><label>Extra Images</label><textarea name="listImgExtra" id="edit4" cols="60" rows="10"><?php if (isset($error)){ echo "$listImgExtra"; }?></textarea></p>
<?php } ?>

<p><label>Order:</label> <input type="text" class="box-medium tooltip-right" title="Enter the order"  name="listOrder" <?php if (isset($error)){ echo "value=\"$listOrder\""; }?>/></p>
<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save item and return to list">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save item and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save item and return to list">
</form>
</div>
<?php




} else {
url(DIR);

}	
}

function edititem()
{
	$curpage = true;
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Edit Image</h3></div> 			
<div class=\"content-box-content\">";
	
$sql = mysql_query("SELECT * FROM ".PREFIX."list_items WHERE itemID='{$_GET['edit-item']}'");	
$ob = mysql_fetch_object($sql);
	
$sql = mysql_query("SELECT * FROM ".PREFIX."lists WHERE listID='$ob->listID'")or die(mysql_error());
$row = mysql_fetch_object($sql);
$listTitle = $row->listTitle;
$listID = $row->listID;
$listType = $row->listType;

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/lists\">lists</a> > <a href=\"".DIRADMIN."manage-add-ons/lists/items-$listID\">$listTitle</a> > Edit Item</p></div>";

echo messages();

if(isset($_POST['cancel'])){
$_SESSION['info'] = "Item was not updated";
url('manage-add-ons/lists/items-'.$listID);
}

// if form submitted then process form
if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$itemTitle = trim($_POST['itemTitle']);
if (strlen($itemTitle) < 1 || strlen($itemTitle) > 255) {
$error[] = 'Title must be at between 1 and 255 charactors.';
}

// if valadation is okay then carry on
if (!$error) {
	
	// post form data 
   $itemID         = $_POST['itemID'];
   $itemTitle     = $_POST['itemTitle'];
   $itemDesc       = $_POST['itemDesc'];
   $itemCont       = $_POST['itemCont'];
   $itemImg       = $_POST['itemImg'];
   $listImgExtra  = $_POST['listImgExtra'];
   $listOrder      = $_POST['listOrder'];
   $template          = $_POST['template'];
   
   //strip any tags from input
   $itemTitle   = strip_tags($itemTitle);
  
   
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$imageTitle   = addslashes($imageTitle);
	 }
   
   // remove any harhful code and stop sql injection
   $imageTitle = mysql_real_escape_string($imageTitle);  
   
   $itemSlug  = strtolower(str_replace(" ", '-', $itemTitle));
	   $itemSlug  = strtolower(str_replace("'", '', $itemSlug));
	   $itemSlug  = strtolower(str_replace("?", '', $itemSlug));
	   $itemSlug  = strtolower(str_replace("/", '', $itemSlug));
	   $itemSlug  = strtolower(str_replace("!", '', $itemSlug));
	   $itemSlug  = strtolower(str_replace(".", '', $itemSlug));
	   $itemSlug  = strtolower(str_replace(",", '', $itemSlug));
	   $itemSlug  = strtolower(str_replace("@", '', $itemSlug));
	   $itemSlug  = strtolower(str_replace("_", '', $itemSlug));
	   $itemSlug  = strtolower(str_replace("--", '-', $itemSlug));  
	   
	   $itemSlug = $itemSlug.'-'.$itemID; 
  
// insert data into images table
$query = "UPDATE ".PREFIX."list_items SET itemTitle = '$itemTitle', itemSlug='$itemSlug', itemDesc = '$itemDesc', itemCont = '$itemCont', itemImg = '$itemImg', listImgExtra='$listImgExtra', listOrder='$listOrder', template='$template' WHERE itemID='$itemID'";
$result  = mysql_query($query) or die ('Cannot Update image because: '. mysql_error());
 	

pingSE(DIR."sitemap.php","bing");
pingSE(DIR."sitemap.php","ask");
pingSE(DIR."sitemap.php","google");


if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'Item Updated';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'Item Updated';
url('manage-add-ons/lists/items-'.$listID);
} 			
 
}
}

	
//dispaly any errors
errors($error);
$result = mysql_query("SELECT * FROM ".PREFIX."list_items WHERE itemID='{$_GET['edit-item']}'");
while($row = mysql_fetch_object($result)){
?>

<form action="" method="post">
<input type="hidden" name="itemID" value="<?=$row->itemID;?>" />

<p><label>Title:</label> <input type="text" class="box-medium tooltip-right" title="Enter the title" name="itemTitle" <?php echo "value=\"$row->itemTitle\"";?>/></p>
<?php if($listType == '1col' || $listType == '2col'){?>
<p><label>Description</label><textarea name="itemDesc" id="edit1" cols="60" rows="10"><?php echo "$row->itemDesc";?></textarea></p>
<?php }?>
<?php if($row->listType == 'grid'){?>
<p><label>Content</label><textarea name="itemCont" id="edit2" cols="60" rows="10"><?php echo "$row->itemCont"; ?></textarea></p>
<?php }?>
<p><label>Image</label><textarea name="itemImg" id="edit3" cols="60" rows="10"><?php echo "$row->itemImg"; ?></textarea></p>

<?php if($listType == 'grid'){?>
<p><label>Extra Images</label><textarea name="listImgExtra" id="edit4" cols="60" rows="10"><?php echo "$row->listImgExtra"; ?></textarea></p>
<?php } ?>

<p><label>Order:</label> <input type="text" class="box-medium tooltip-right" title="Enter the order" name="listOrder" <?php echo "value=\"$row->listOrder\"";?>/></p>

<p><label>Template:</label>
 <?php
$result2 = mysql_query("SELECT * FROM ".PREFIX."styles")or die(mysql_error());
echo "<select name='template' class='box-medium tooltip-right' title='Select page Template'>\n";
while ($row2 = mysql_fetch_object($result2)) { 
	
	$row2->themeTitle = RemoveExtension($row2->themeTitle);
	$row2->themeTitle = str_replace("-"," ",$row2->themeTitle);
	$row2->themeTitle = ucwords($row2->themeTitle);

	echo "<option value='$row2->styleID' ";	
	if ($_POST['template'] == $row2->styleID){
	echo "selected='selected'";
	}
	if ($row->template == $row2->styleID){
	echo "selected='selected'";
	}
	
	echo ">$row2->themeTitle</option>\n"; 
}
	echo "</select>\n";
?> </p>

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save item and return to list">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save item and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save item and return to list">

</form>	
<?php }
echo "</div>";
} else {
url(DIR);

}	
}


function listsRequest()
{
	if(isset($_GET['lists'])){
	managelists();
	$curpage = true;
	}
	
	if(isset($_GET['add-list'])){
	addlist();
	$curpage = true;
	}
	
	if(isset($_GET['edit-list'])){
	editlist();
	$curpage = true;
	}
	
	if(isset($_GET['items'])){
	manageItems();
	$curpage = true;
	}
	
	
	if(isset($_GET['add-item'])){
	additem();
	$curpage = true;
	}
	
	if(isset($_GET['edit-item'])){
	edititem();
	$curpage = true;
	}
	
	if(isset($_GET['dellist'])){
	dellist();
	$curpage = true;
	}
	
	if(isset($_GET['delitem'])){
	delitem();
	$curpage = true;
	}
	
	$isql = mysql_query("SELECT * FROM ".PREFIX."list_items WHERE itemSlug='".PAGE."' LIMIT 1")or die(mysql_error());
	$in = mysql_num_rows($isql);
	$irow = mysql_fetch_object($isql);
	
	if($in >0 && PAGE != ''){	
		global $curpage;
		$curpage = true;
		
		$irow->itemImg = str_replace("../../../../","",$irow->itemImg);
		
		$wi = '915';
		$img =  $irow->itemImg;
		$str = preg_replace('#<p>.+?src=[\'"]([^\'"]+)[\'"].+</p>#i', "$1", $img);		
		$inews="<img src=\"img.php?src=$str&w=$wi&zc=0\" alt=\"\" />";
		
		$plugcont.= "<h1>$irow->itemTitle</h1>";
		$plugcont.= "<div class=\"itemGridImage\">$inews</div>";
		$plugcont.= "<div style=\"clear:both;\" class=\"itemGridImage\">$irow->listImgExtra</div>";		
		$plugcont.= "<div style=\"clear:both;\">$irow->itemCont</div>";		
		
		$_SESSION['plugcont'] = $plugcont;
		
		$themeSql = mysql_query("SELECT * FROM ".PREFIX."styles WHERE styleID='".$irow->template."'")or die(mysql_error());
		$tnum = mysql_num_rows($themeSql);
		$thRow = mysql_fetch_object($themeSql);
		if($tnum != 0){
		define('THEME',$thRow->themeTitle);
		define('THEMEPATH','assets/templates/');
		} else {
		define('THEME','404.php');
		define('THEMEPATH','assets/templates/');
		}	
	}	
	
}

function listTitle()
{
	global $navTitle,$isp;
	$navTitle = $_GET['ispage'];
	$result = mysql_query("SELECT * FROM ".PREFIX."list_items WHERE itemSlug='".PAGE."' LIMIT 1"); 
	while ($row = mysql_fetch_object($result)){
	$navTitle = 'hello'.$row->itemTitle;	
	$isp = true;
	}
}

function jslist() {
	echo "\nfunction dellist(listID, listTitle)
{
   if (confirm(\"Are you sure you want to delete '\" + listTitle + \"'\"))
   {
      window.location.href = '".DIR."admin.php?dellist=' + listID;
   }
}\n";
}

function jsitem() {
	echo "\nfunction delitem(itemID, itemTitle)
{
   if (confirm(\"Are you sure you want to delete '\" + itemTitle + \"'\"))
   {
      window.location.href = '".DIR."admin.php?delitem=' + itemID;
   }
}\n";
}

function dellist() {
if(isset($_GET['dellist']))
{
  ///global $prefix;	

$query = "SELECT * FROM  ".PREFIX."lists  WHERE listID = '{$_GET['dellist']}'";
$result = mysql_query($query) or die('problem: ' . mysql_error());

while ( $row = mysql_fetch_array ($result)) {
//if($row['imageThumb'] !== '' && $row['imageFull'] !== ''){
unlink ($row['imageThumb']);
unlink ($row['imageFull']);
//}	
}

  $query = "DELETE FROM ".PREFIX."lists WHERE listID = '{$_GET['dellist']}'";
  mysql_query($query) or die('Error : ' . mysql_error());
  
  $query = "DELETE FROM ".PREFIX."lists WHERE listID = '{$_GET['dellist']}'";
  mysql_query($query) or die('Error : ' . mysql_error());

   $_SESSION['success'] = 'Deleted';
	url('admin/manage-add-ons/lists');	
}




function delitem() {
if(isset($_GET['delitem']))
{


$query = "SELECT * FROM  ".PREFIX."list_items  WHERE itemID = '{$_GET['delitem']}'";
$result = mysql_query($query) or die('problem: ' . mysql_error());

while ( $row = mysql_fetch_array ($result)) {
global $alb;
$alb = $row['listID'];
if($row['imageThumb'] !== '' && $row['imageFull'] !== ''){
unlink ($row['imageThumb']);
unlink ($row['imageFull']);
}	
}

global $alb;

  $query = "DELETE FROM ".PREFIX."list_items WHERE itemID = '{$_GET['delitem']}'";
  mysql_query($query) or die('Error : ' . mysql_error());
  
  $_SESSION['success'] = 'Item Deleted';
  url('admin/manage-add-ons/lists/items-'.$alb);	

}

}
}


function lists($string) 
{	

  //plugin for lists
$albsql = mysql_query("SELECT * FROM ".PREFIX."lists");
while ($albRow = mysql_fetch_object($albsql))
{ 
	$mystring = $string;
	$findme   = "[$albRow->listTitle]";
	$pos = strpos($mystring, $findme);
	
	if ($pos !== false) {

	  $albMatch = "[$albRow->listTitle]";
	   
	   $asql = mysql_query("SELECT * FROM ".PREFIX."list_items WHERE listID='$albRow->listID'")or die(mysql_error());

$arow = mysql_fetch_object($asql);


$listID = $arow->listID;
	
if(!isset($_GET['listspage'])){
	$listspage = 1;
} else {
	$listspage = $_GET['listspage'];
}

// Define the number of results per allclientspages
$max_results = $albRow->limitNum;

// Figure out the limit for the query based
$from = (($listspage * $max_results) - $max_results); 

//LIMIT $from, $max_results
$result = mysql_query("SELECT * FROM ".PREFIX."list_items WHERE listID='$listID' ORDER BY listOrder LIMIT $from, $max_results")or die(mysql_error());

$albOutput= "<div id=\"lists\">\n";

while ($grow = mysql_fetch_object($result)){

		$grow->itemTitle = str_replace("../../../../","",$grow->itemTitle);
		$grow->itemImg = str_replace("../../../../","",$grow->itemImg);
		$grow->itemDesc = str_replace("../../../../","",$grow->itemDesc);

		if($albRow->listType == '1col'){		
		
			$albOutput.= "<div class=\"item1colholder\">\n";
			$albOutput.= "<div class=\"item1colImage\">$grow->itemImg</div>\n";
			$albOutput.= "<div class=\"item1colDesc\">$grow->itemDesc</div>\n";
			$albOutput.= "</div>\n";
		}
		
		if($albRow->listType == '2col'){
				
			$albOutput.= "<div class=\"item2colholder\">\n";
			$albOutput.= "<div class=\"item2colImage\">$grow->itemImg</div>\n";
			$albOutput.= "<div class=\"item2colDesc\">$grow->itemDesc</div>\n";
			$albOutput.= "</div>\n";		
		}
		
		if($albRow->listType == 'grid'){
		
			$wi = '220';
			$hi = '220';
			$img =  $grow->itemImg;
			$str = preg_replace('#<p>.+?src=[\'"]([^\'"]+)[\'"].+</p>#i', "$1", $img);		
			$inews="<img src=\"img.php?src=$str&w=$wi&zc=0\" alt=\"\" />";
		
			$albOutput.= "<div class=\"itemGridholder\">\n";
			$albOutput.= "<div class=\"itemGridImage\"><a href=\"".DIR."$grow->itemSlug\">$inews</a></div>\n";
			$albOutput.= "<div class=\"itemGridTitle\"><a href=\"".DIR."$grow->itemSlug\">$grow->itemTitle</a></div>\n";
			$albOutput.= "</div>\n";
		}
 
    
	}
 
$albOutput.= "</div>\n";	
// Figure out the total number of results in DB:
$total_results = mysql_result(mysql_query("SELECT COUNT(*) as Num FROM ".PREFIX."list_items WHERE listID='$listID'"),0);

// Figure out the total number of clientspages. Always round up using ceil()
$total_listspage = ceil($total_results / $max_results);

// Build clientspage Number Hyperlinks

$albOutput.= "<div class=\"pagination\">\n";


// Build Previous Link
			if($listspage > 1){
				$prev = ($listspage - 1);
				$albOutput.=  "<a href=\"?listspage=$prev\">Previous</a>\n ";
			}
			
			for($i = 1; $i <= $total_listspage; $i++){
				if($total_listspage > 1){
				if(($listspage) == $i){
					$albOutput.=  "<span class=\"current\">$i</span>\n ";
					} else {
						$albOutput.=  "<a href=\"?listspage=$i\">$i</a>\n ";
				}
				}
			}
			
			// Build Next Link
			if($listspage < $total_listspage){
				$next = ($listspage + 1);
				$albOutput.=  "<a href=\"?listspage=$next\">Next</a>\n";
			}
			$albOutput.=  "</div>\n"; 

//}//close while
}//close if
	
	//$searchalb
	$string = str_replace("$albMatch", $albOutput, $string);
} //close first while  
  
  return $string;
}

function addLinkslists() {
	echo "bk<div class=\"icon\">\n";
		echo "<a href=\"".DIR."admin/manage-add-ons/lists\"><img src=\"".DIR."assets/plugins/lists/lists.png\" alt=\"Lists\" title=\"Manage Lists\" class=\"tooltip-top\" /></a>\n";
    	echo "<p><a href=\"".DIR."admin/manage-add-ons/lists\" title=\"Manage Lists\" class=\"tooltip-top\">Lists</a></p>\n";
    echo "</div>\n";	
}

//add hook, where to execute a function
add_hook('admin_modules','addLinkslists');
add_hook('cont','lists');
add_hook('js_popup', 'jslist');
add_hook('js_popup', 'jsitem');
add_hook('del', 'dellist');
add_hook('del', 'delitem');
add_hook('page_requester','listsRequest');
add_hook('above_doctype_title','listTitle');

?>
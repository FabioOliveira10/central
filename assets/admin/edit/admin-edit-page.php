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
	
echo "<div class=\"content-box-header\"><h3>Edit Page</h3></div> 			
<div class=\"content-box-content\">";

echo "<p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-pages\">Manage Pages</a> > Edit Pages</p>";

messages();

if(isset($_POST['cancel'])){
$_SESSION['info'] = $_POST['pageName']." was not updated";
url('manage-pages');
}

if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty

$pageName = trim($_POST['pageName']);
if (strlen($pageName) < 3 || strlen($pageName) > 255) {
$error[] = 'Browser Title must be between 3 and 255 characters.';
}

if($_POST['pageID'] != '1'){
	$pageTitle = trim($_POST['pageTitle']);
	if (strlen($pageTitle) < 3 || strlen($pageTitle) > 255) {
	$error[] = 'Menu Title must be between 3 and 255 characters.';
	}
	
	$pageLink = trim($_POST['pageLink']);
	if (strlen($pageLink) < 1 || strlen($pageLink) > 255) {
	$error[] = 'Menu Link must be between 3 and 255 characters.';
	}
}

// if valadation is okay then carry on
if (!$error) {

	// post form data
   $pageID            = $_POST['pageID'];
   $pageName 	      = $_POST['pageName'];
   $pageTitle 	      = $_POST['pageTitle'];
   $pageLink 	      = $_POST['pageLink'];
   $pageMetaKeywords  = $_POST['pageMetaKeywords'];
   $pageMetaDescription = $_POST['pageMetaDescription'];
   $pageCont          = $_POST['pageCont'];
   $level             = $_POST['level'];
   $parent            = $_POST['parent'];
   $active            = $_POST['active'];
   $template          = $_POST['template'];
   $pageVis           = $_POST['viewlevel'];
   
   // page 1
	$box1Text    = $_POST['box1Text'];
	$box2Text    = $_POST['box2Text'];
	$box3Text    = $_POST['box3Text'];

   
   
   $pageStandAlone = 0;
   
   if($level == 0)
   {
   		$parent = 0;
   }
   
    if($level == 1)
   {
   		$pageStandAlone = 0;
   }
   
   if($level == 2)
   {
   		$parent = 0;
		$pageStandAlone = 1;
   }
  
    
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$pageTitle        = addslashes($pageTitle);
	$pageName         = addslashes($pageName);
	$pageMetaKeywords = addslashes($pageMetaKeywords);
	$pageMetaDescription     = addslashes($pageMetaDescription);
	$pageCont         = addslashes($pageCont);
	 }
	 
	 //$pageMetaKeywords = strtolower($pageMetaKeywords);
	 //$pageMetaDescription = strtolower($pageMetaDescription);
	 
	   $pageSlug  = strtolower(str_replace(" ", '-', $pageLink));
	  //$pageSlug  = strtolower(str_replace("/", '', $pageSlug));
	   $pageSlug  = strtolower(str_replace("?", '', $pageSlug));
	   $pageSlug  = strtolower(str_replace("&", 'and', $pageSlug));
	   $pageSlug  = strtolower(str_replace("!", '', $pageSlug));
	  // $pageSlug  = strtolower(str_replace(".", '', $pageSlug));
	   $pageSlug  = strtolower(str_replace(",", '', $pageSlug));
	   $pageSlug  = strtolower(str_replace("@", '', $pageSlug));
	   $pageSlug  = strtolower(str_replace("_", '', $pageSlug));
	   $pageSlug  = strtolower(str_replace("--", '-', $pageSlug));
	   $pageSlug  = strtolower(str_replace("'", '', $pageSlug));
	   $pageSlug  = strtolower(str_replace('"', '', $pageSlug));
	   
	    $pageSlug = stripslashes($pageSlug);
		
		if(isset($_POST['sidebar']))
		foreach($_POST['sidebar'] as $side)
		{
		$sides.=$side.',';						
		}
		
		if($pageID == '1'){
			$pageTitle = $pageName;
			$pageLink = '';
   			$pageStandAlone = 0;
			$parent = 0;
			$pageSlug = '';
			$active = 1;
			$pageCont = $pageCont;
			$template = 1;
			$pageOrder = 0;
			$sides = '';
			$pageVis = 3;
			
			$sql = "UPDATE ".PREFIX."pages SET pageName ='$pageName', pageTitle ='$pageTitle', pageLink='$pageLink', pageSlug ='$pageSlug',  pageMetaKeywords ='$pageMetaKeywords', pageMetaDescription ='$pageMetaDescription', pageCont ='$pageCont', pageStandAlone = '$pageStandAlone', 
pageParent = '$parent', pageActive = '$active', template='$template', pageVis = '$pageVis', box1Text='$box1Text', box2Text='$box2Text', box3Text='$box3Text' WHERE pageID='$pageID'";
$resultupdate = mysql_query($sql)or die(mysql_error());

  		 }	else {
		 	$sql = "UPDATE ".PREFIX."pages SET pageName ='$pageName', pageTitle ='$pageTitle', pageLink='$pageLink', pageSlug ='$pageSlug',  pageMetaKeywords ='$pageMetaKeywords', pageMetaDescription ='$pageMetaDescription', pageCont ='$pageCont', pageStandAlone = '$pageStandAlone', 
pageParent = '$parent', pageActive = '$active', template='$template', pageVis = '$pageVis' WHERE pageID='$pageID'";
$resultupdate = mysql_query($sql)or die(mysql_error());
		 }

$update = mysql_query("UPDATE ".PREFIX."pages SET sidebars='$sides' WHERE pageID='$pageID' ")or die(mysql_error());

pingSE(DIR."sitemap.php","bing");
pingSE(DIR."sitemap.php","ask");
pingSE(DIR."sitemap.php","google");
pingSE(DIR."sitemap.php","moreover");


if(isset($_POST['backsubmit'])){
$_SESSION['success'] = $_POST['pageName']." Updated";
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = $_POST['pageName']." Updated";
url('manage-pages');
}



}// close errors
}// close if form sent

//dispaly any errors
errors($error);

$result = mysql_query("SELECT * FROM ".PREFIX."pages WHERE pageID='{$_GET['edit-page']}'")or die(mysql_error());
$Rows = mysql_num_rows($result);
while ($row = mysql_fetch_object($result)) {

$row->box1Text = str_replace("../../../../","../../../",$row->box1Text);
$row->box2Text = str_replace("../../../../","../../../",$row->box2Text);
$row->box3Text = str_replace("../../../../","../../../",$row->box3Text);
?>

<form action="" method="post">
<input type="hidden" name="pageID" value="<?php echo $row->pageID;?>" />

<p><label>Browser Title</label> <input class="box-medium tooltip-right" title="Set a title that will appear at the very top of the page" name="pageName" type="text" value="<?php echo $row->pageName;?>" size="40" maxlength="255"  /></p>
  
<?php if($row->pageID != '1'){?>  
<p><label>Menu Title</label> <input class="box-medium tooltip-right" title="Set title of menu item" name="pageTitle" type="text" value="<?php echo $row->pageTitle;?>" size="40" maxlength="255"  /></p>

<p><label>Menu Link</label> <input class="box-medium tooltip-right" title="Set the link of menu item" name="pageLink" type="text" value="<?php echo $row->pageLink;?>" size="40" maxlength="255" /></p>
<?php } ?>

<p><label>Meta Keywords</label><br /> <textarea class="ta-default tooltip-top" title="Place keywords here coma seperated, to help search engines find this page" name="pageMetaKeywords" cols="60" rows="5"><?php echo $row->pageMetaKeywords;?></textarea></p>

<p><label>Meta Description</label><br /> <textarea class="ta-default tooltip-top" title="Set the description that will be shown in search engines results" name="pageMetaDescription" cols="60" rows="5"><?php echo $row->pageMetaDescription;?></textarea></p>

<p><label>Content</label><textarea name="pageCont" class="ta-default" cols="60" rows="20"><?php echo $row->pageCont;?></textarea></p>

<?php if($row->pageID != '1'){?>

<p><label>Template:</label>
 <?php
$result2 = mysql_query("SELECT * FROM ".PREFIX."styles WHERE themeTitle!='404.php'")or die(mysql_error());
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


<p><label>Page Level</label>
<select name="level" class="box-medium tooltip-right" title="Select page, Main page and Sub page appear in the site's menu Standalone pages do not">
	<option value="0" <?php if($row->pageParent == 0) { echo "selected=selected"; } ?>>Main Page</option>
	<option value="1" <?php if($row->pageParent != 0) { echo "selected=selected"; } ?>>Sub Page</option>
	<option value="2" <?php if($row->pageStandAlone != 0) { echo "selected=selected"; } ?>>Standalone Page</option>
</select></p>

<p><label>Page Parent:</label>
 <?php
$result2 = mysql_query("SELECT * FROM ".PREFIX."pages WHERE pageParent='0' AND isRoot='1' AND pageStandAlone='0'")or die(mysql_error());
echo "<select name='parent' class='box-medium tooltip-right' title='Only change if Page Level is Sub Page'>\n";
echo "<option value='none'>None</option>'\n";
while ($row2 = mysql_fetch_object($result2)) { 
	echo "<option value='$row2->pageID' ";	
	if ($_POST['parent'] == $row2->pageID){
	echo "selected='selected'";
	}
	if ($row->pageParent == $row2->pageID){
	echo "selected='selected'";
	}
	
	echo ">$row2->pageTitle</option>\n"; 
}
	echo "</select>\n";
?> </p>

<p><label>Page View Level</label>
<select name="viewlevel" class='box-medium tooltip-right' title='Select who can see page'>
	<option value="0" <?php if($row->pageVis == 0) { echo "selected=selected"; } ?>>Admins</option>
	<option value="3" <?php if($row->pageVis == 3) { echo "selected=selected"; } ?>>Public</option>
</select></p>

<p><label>Active</label>
<select name="active" class="box-medium tooltip-right" title="Select if page is visable or not">
	<option value="1" <?php if($row->pageActive == 1) { echo "selected=selected"; } ?>>Yes</option>
	<option value="0" <?php if($row->pageActive == 0) { echo "selected=selected"; } ?>>No</option>
</select></p>

<fieldset><legend>Select Sidebars</legend>
<?php

$insides = explode(",",$row->sidebars);

$sql = mysql_query("SELECT * FROM ".PREFIX."sidebars ORDER BY sidebarOrder")or die(mysql_error());
$num = mysql_num_rows($sql);
while ($r = mysql_fetch_object($sql)){
	if (in_array("$r->sidebarID", $insides)) {
	$checked = 'checked=checked';    
} else {
	$checked = ''; 
}
	echo "<p><label>$r->sidebarTitle</label><input name=\"sidebar[]\" type=\"checkbox\" value=\"$r->sidebarID\" $checked /></p>\n";
}
if($num == 0){ echo "<p>No Sidebar exists yet</p>"; }
?>
</fieldset>

<?php } else {

	
?>


<p><label>Box 1</label><textarea name="box1Text" class="ta-default" id="edit3" cols="60" rows="20"><?php echo $row->box1Text;?></textarea></p>

<p><label>Box 2</label><textarea name="box2Text" class="ta-default" id="edit4" cols="60" rows="20"><?php echo $row->box2Text;?></textarea></p>

<p><label>Box 3</label><textarea name="box3Text" class="ta-default" id="edit5" cols="60" rows="20"><?php echo $row->box3Text;?></textarea></p>

<?php } ?>

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save page and return to manage pages">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save page and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save page and return to manage pages">
</form>
</div>

<?php } 

} else {
header('Location: '.DIRADMIN);
exit;
}?>
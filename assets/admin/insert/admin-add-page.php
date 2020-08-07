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
	
echo "<div class=\"content-box-header\"><h3>Add Page</h3></div> 			
<div class=\"content-box-content\">";

echo "<p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-pages\">Manage Pages</a> > Add Pages</p>";

if(isset($_POST['cancel'])){
$_SESSION['info'] = "Page was not created";
url('manage-pages');
}

if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$pageName = trim($_POST['pageName']);
if (strlen($pageName) < 3 || strlen($pageName) > 255) {
$error[] = 'Browser Title must be between 3 and 255 characters.';
}

$pageTitle = trim($_POST['pageTitle']);
if (strlen($pageTitle) < 3 || strlen($pageTitle) > 255) {
$error[] = 'Menu Title must be between 3 and 255 characters.';
}

$pageLink = trim($_POST['pageLink']);
if (strlen($pageLink) < 1 || strlen($pageLink) > 255) {
$error[] = 'Menu Link must be between 1 and 255 characters.';
}


// if valadation is okay then carry on
if (!$error) {

	// post form data
   $pageTitle 	      = $_POST['pageTitle'];
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
   $pageVis           = $_POST['pageVis'];

   
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
	$pageName        = addslashes($pageName);
	$pageMetaKeywords = addslashes($pageMetaKeywords);
	$pageMetaDescription     = addslashes($pageMetaDescription);
	$pageCont         = addslashes($pageCont);
	 }
	 
	 //$pageMetaKeywords = strtolower($pageMetaKeywords);
	 //$pageMetaDesc     = strtolower($pageMetaDesc);
	 
       $pageSlug  = strtolower(str_replace(" ", '-', $pageLink));
	  // $pageSlug  = strtolower(str_replace("/", '', $pageSlug));
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
		
		if(isset($_POST['sidebar'])){
		foreach($_POST['sidebar'] as $side)
		{
		$sides.=$side.',';						
		}}
		
		$osql = mysql_query("SELECT pageOrder FROM ".PREFIX."pages ORDER BY pageOrder DESC LIMIT 1");
		$or = mysql_fetch_object($osql);
	    $order = $or->pageOrder + 1;
	      
$sql = "INSERT INTO ".PREFIX."pages (pageTitle, pageSlug, pageName, pageLink, pageMetaKeywords, pageMetaDescription, pageCont, pageStandAlone,	pageParent, pageOrder, pageActive,sidebars,template,pageVis) VALUES ('$pageTitle', '$pageSlug',  '$pageName', '$pageLink', '$pageMetaKeywords', '$pageMetaDescription', '$pageCont', '$pageStandAlone', '$parent', '$order', '$active','$sides','$template','$pageVis')"; 
$resultupdate = mysql_query($sql)or die(mysql_error());

pingSE(DIR."sitemap.php","bing");
pingSE(DIR."sitemap.php","ask");
pingSE(DIR."sitemap.php","google");
pingSE(DIR."sitemap.php","moreover");


if(isset($_POST['backsubmit'])){
$_SESSION['success'] = "Page added";
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = "Page added";
url('manage-pages');
}
 
}// close errors
}// close if form sent

//dispaly any errors
errors($error);

?>

<form action="" method="post">

<p><label>Browser Title</label> <input class="box-medium tooltip-right" title="Set a title that will appear at the very top of the page" name="pageName" type="text" value="<?php if(isset($error)){ echo $_POST['pageName']; } ?>" size="40" maxlength="255"  /></p>
  
<p><label>Menu Title</label> <input class="box-medium tooltip-right" title="Set title of menu item" name="pageTitle" type="text" value="<?php if(isset($error)){ echo $_POST['pageTitle']; } ?>" size="40" maxlength="255"  /></p>

<p><label>Menu Link</label> <input class="box-medium tooltip-right" title="Set the link of menu item" name="pageLink" type="text" value="<?php if(isset($error)){ echo $_POST['pageLink']; } ?>" size="40" maxlength="255" /></p>

<p><label>Meta Keywords</label><br /> <textarea class="ta-default tooltip-top" title="Place keywords here coma seperated, to help search engines find this page" name="pageMetaKeywords" cols="60" rows="5"><?php if(isset($error)){  echo $_POST['pageMetaKeywords']; }?></textarea>
</p>

<p><label>Meta Description</label><br /> <textarea class="ta-default tooltip-top" title="Set the description that will be shown in search engines results" name="pageMetaDescription" cols="60" rows="5"><?php if(isset($error)){  echo $_POST['pageMetaDescription']; }?></textarea>
</p>

<p><label>Content</label>
<br />
<textarea class="ta-default" id="pageCont" name="pageCont" cols="60" rows="20"><?php if(isset($error)){  echo $pageCont; }?></textarea>
</p>

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
	<option value="0" <?php if($_POST['active'] == 0) { echo "selected=selected"; } ?>>Main Page</option>
	<option value="1" <?php if($_POST['active'] == 1) { echo "selected=selected"; } ?>>Sub Page</option>
	<option value="2" <?php if($_POST['active'] == 2) { echo "selected=selected"; } ?>>Standalone Page</option>
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
	echo ">$row2->pageTitle</option>\n"; 
}
	echo "</select>\n";
?> </p>

<p><label>Page View Level</label>
<select name="pageVis" class='box-medium tooltip-right' title='Select who can see page'>
	<option value="0" <?php if($_POST['pageVis'] == 0) { echo "selected=selected"; } ?>>Admins</option>
	<option value="3" <?php if($_POST['pageVis'] == 3) { echo "selected=selected"; } ?>>Public</option>
</select></p>

<p><label>Active</label>
<select name="active" class="box-medium tooltip-right" title="Select if page is visable or not">
	<option value="1" <?php if($_POST['active'] == 1) { echo "selected=selected"; } ?>>Yes</option>
	<option value="0" <?php if($_POST['active'] == 0) { echo "selected=selected"; } ?>>No</option>
</select></p>

<fieldset><legend>Select Sidebars</legend>
<?php

$sql = mysql_query("SELECT * FROM ".PREFIX."sidebars ORDER BY sidebarOrder")or die(mysql_error());
$num = mysql_num_rows($sql);
while ($r = mysql_fetch_object($sql)){	
	echo "<p><label>$r->sidebarTitle</label><input name=\"sidebar[]\" type=\"checkbox\" value=\"$r->sidebarID\" /></p>\n";
}
if($num == 0){ echo "<p>No Sidebar exists yet</p>"; }
?>
</fieldset>

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save page and return to manage pages">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save page and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save page and return to manage pages">
</form>
</div>
<?php
} else {
header('Location: '.DIRADMIN);
exit;
}?>
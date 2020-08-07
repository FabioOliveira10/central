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

global $curpage;

$cfile = ".htaccess";
$fo = fopen($cfile, 'r');
//get file contents and work out the file content size in bytes
$data = fread($fo, filesize($cfile));
//close the file
fclose($fo);

if (preg_match('/recipes/', $data))
{
} else { 

$newData = "
RewriteRule ^admin/manage-add-ons/recipessnippit$                    admin.php?recipessnippit=$1 [L]
RewriteRule ^admin/manage-add-ons/recipessnippit/$                   admin.php?recipessnippit=$1 [L]

RewriteRule ^admin/manage-add-ons/recipes$                    admin.php?recipes=$1 [L]
RewriteRule ^admin/manage-add-ons/recipes/$                   admin.php?recipes=$1 [L]

RewriteRule ^admin/manage-add-ons/recipes/add-recipes$            admin.php?add-recipes=$1 [L]
RewriteRule ^admin/manage-add-ons/recipes/add-recipes/$           admin.php?add-recipes=$1 [L]

RewriteRule ^admin/manage-add-ons/recipes/edit-recipes-([^/]+)$   admin.php?edit-recipes=$1 [L]
RewriteRule ^admin/manage-add-ons/recipes/edit-recipes-([^/]+)/$  admin.php?edit-recipes=$1 [L]

RewriteRule ^recipesp-([^/]+)$                  	              index.php?recipespage=$1 [L]
RewriteRule ^recipesp-([^/]+)/$                 	              index.php?recipespage=$1 [L]
###";

$data = str_replace("###",$newData,$data);

$myFile = $cfile;
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $data);
fclose($fh);


mysql_query("CREATE TABLE IF NOT EXISTS `".PREFIX."recipes` (
  `recipesID` int(11) NOT NULL auto_increment,
  `recipesTitle` varchar(255) NOT NULL,
  `recipesSlug` varchar(255) NOT NULL,
  `recipesMetaKeywords` text NOT NULL,
  `recipesMetaDescription` text NOT NULL,
  `recipesDesc` text NOT NULL,
  `recipesCont` text NOT NULL,
  `recipesDate` datetime NOT NULL,
  PRIMARY KEY  (`recipesID`)
) ENGINE=MyISAM");

mysql_query("CREATE TABLE IF NOT EXISTS `".PREFIX."recipes_limit` (
  `recipeslimit` int(11) NOT NULL default '5',
  `snippetlimit` int(11) NOT NULL default '5',
  `thumbWidth` int(3) NOT NULL default '100',
  `thumbHeight` int(3) NOT NULL default '100',
   PRIMARY KEY  (`recipeslimit`)
) ENGINE=MyISAM");

mysql_query("INSERT INTO `".PREFIX."recipes_limit` (`recipeslimit`,`snippetlimit`) VALUES
( '5','5')");

}

function managerecipes()
{
global $curpage;
$curpage = true;
if (isglobaladmin() || isadmin()){
	
echo "<div class=\"content-box-header\"><h3>Recipes</h3></div> 			
<div class=\"content-box-content\">";	


echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > Recipes</p></div>";

echo messages();

echo "<p>To implement the recipes insert [recipes] into any page you want the recipes to be displayed.</p>\n";

$result = mysql_query("SELECT * FROM ".PREFIX."recipes ORDER BY recipesID DESC")or die(mysql_error());


if(isset($_POST['recipesub']))
{
$lim = $_POST['recipeslimit'];
$thumbWidth = $_POST['thumbWidth'];
$thumbHeight = $_POST['thumbHeight'];

$lim = mysql_real_escape_string($lim);
$thumbWidth = mysql_real_escape_string($thumbWidth);
$thumbHeight = mysql_real_escape_string($thumbHeight);

$sql = mysql_query("UPDATE ".PREFIX."recipes_limit SET recipeslimit='$lim', thumbWidth='$thumbWidth', thumbHeight='$thumbHeight'")or die(mysql_error());
$_SESSION['success'] = 'Updated';
  url('manage-add-ons/recipes');
}

$sql = mysql_query("SELECT * FROM ".PREFIX."recipes_limit")or die(mysql_error());
$limit = mysql_fetch_object($sql);


?>

<form action="" method="post">
<p><label>Number per page</label><input name="recipeslimit" type="text" class="box-small tooltip-right" title="Enter number of recipes items to be shown per page" value="<?php echo $limit->recipeslimit;?>" size="3" /></p>

<p><label>Thumbnail Width</label><input name="thumbWidth" type="text" class="box-small tooltip-right" title="Set the width of thumbnails in pixels" value="<?php echo $limit->thumbWidth;?>" size="3" /></p>

<p><label>Thumbnail Height</label><input name="thumbHeight" type="text" class="box-small tooltip-right" title="Set the height of thumbnails in pixels" value="<?php echo $limit->thumbHeight;?>" size="3" /></p>

<p><input type="submit" class="button tooltip-right" title="Save Changes" name="recipesub" value="submit" /></p>
</form>

<table class="stripeMe">
<tr align="center">
<td width="41%" align="left"><strong>Recipes</strong></td>
<td width="59%"><strong>Action</strong></td>
</tr>
<?php
while ($row = mysql_fetch_object($result)) {
?>
<tr>
<td><?php echo $row->recipesTitle;?></td><td align="center" valign="top"><a href="<?php echo DIRADMIN;?>manage-add-ons/recipes/edit-recipes-<?php echo $row->recipesID;?>" class="tooltip-top" title="Edit"><img src="<?php echo DIR;?>assets/images/icons/action-edit.png" alt="Edit" /></a> <a href="#" id="<?php echo $row->recipesID;?>" rel="delrecipes" title="<?php echo $row->recipesSlug;?>" class="delete_button"><img src="<?php echo DIR;?>assets/images/icons/action-del.png" alt="Delete" /></a></td>
</tr>
<?php
}
?>
</table>
<p><a href="<?php echo DIRADMIN;?>manage-add-ons/recipes/add-recipes" class="button tooltip-right" title="Add New Recipe">Add Recipe</a></p>
<?php


echo "<p>Get a recipes snippet from the last recipes items</p>
<p>To implement the recipes snippit insert [recipessnippit] into any page or sidebar panel you want the recipes to be displayed.</p>\n";

if(isset($_POST['snipsub']))
{
$lim = $_POST['snippetlimit'];
$lim = mysql_real_escape_string($lim);

$sql = mysql_query("UPDATE ".PREFIX."recipes_limit SET snippetlimit='$lim'")or die(mysql_error());
$_SESSION['success'] = 'Snippet Updated Added';
  url('manage-add-ons/recipes');
}

$sql = mysql_query("SELECT * FROM ".PREFIX."recipes_limit")or die(mysql_error());
$limit = mysql_fetch_object($sql);


?>

<form action="" method="post">
<p>Number of recipe items to show</p>
<p><input name="snippetlimit" type="text" class="box-small tooltip-right" title="Enter number of recipe items to show" value="<?php echo $limit->snippetlimit;?>" size="3" />
</p>
<p><input type="submit" name="snipsub" class="button tooltip-right" title="Save Changes" value="Submit" /></p>
</form>
</div>
<?php

} else {
url(DIRADMIN);

}		
}


function addrecipes()
{
	global $curpage;
$curpage = true;
	if (isglobaladmin() || isadmin() || iseditor()){
		
echo "<div class=\"content-box-header\"><h3>Add Recipe</h3></div> 			
<div class=\"content-box-content\">";			
		
	echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/recipes\">Recipes</a> > Add Recipe</p></div>";
	
echo messages();

if(isset($_POST['cancel'])){
$_SESSION['info'] = "recipe was not created";
url('manage-add-ons/recipes');
}

// if form submitted then process form
if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$recipesTitle = trim($_POST['recipesTitle']);
if (strlen($recipesTitle) < 3 || strlen($recipesTitle) > 255) {
$error[] = 'Title Must be between 3 and 255 characters.';
}

$recipesDesc = trim($_POST['recipesDesc']);
if (strlen($recipesDesc) < 3 ) {
$error[] = 'Description Must be more then 3 characters.';
}


$recipesCont = trim($_POST['recipesCont']);
if (strlen($recipesCont) < 3 ) {
$error[] = 'Content Must be more then 3 characters.';
}

$recipesTitle 	      = $_POST['recipesTitle'];
$recipesMetaKeywords  = $_POST['recipesMetaKeywords'];
$recipesMetaDescription  = $_POST['recipesMetaDescription'];


// print_r($error);
// if valadation is okay then carry on
if (!$error) {

	// post form data
   $recipesTitle 	      = $_POST['recipesTitle'];
   $recipesMetaKeywords  = $_POST['recipesMetaKeywords'];
   $recipesMetaDescription  = $_POST['recipesMetaDescription'];
   $recipesDesc           = $_POST['recipesDesc'];
   $recipesCont           = $_POST['recipesCont'];
   $recipesImg           = $_POST['recipesImg'];
 
   
   // add slashes if needed
	   if(!get_magic_quotes_gpc())
		{ 
		$recipesTitle        = addslashes($recipesTitle);
		$recipesCont        = addslashes($recipesCont);
		}

	
		// escape any harmfull code and prevent sql injection
		$recipesTitle = mysql_real_escape_string($recipesTitle);
		$recipesCont   = mysql_real_escape_string($recipesCont );		
 
	   
	 $recipesTitle  = str_replace("/", '', $recipesTitle);
	 $recipesTitle  = str_replace(".", '', $recipesTitle);
	 $recipesTitle  = str_replace(",", '', $recipesTitle);
	 $recipesTitle  = str_replace("@", '', $recipesTitle);
	 $recipesTitle  = str_replace("'", '', $recipesTitle);
		 
	   $recipesSlug  = strtolower(str_replace(" ", '-', $recipesTitle));
	   $recipesSlug  = strtolower(str_replace("'", '', $recipesSlug));
	   $recipesSlug  = strtolower(str_replace("&", 'and', $recipesSlug));
	   $recipesSlug  = strtolower(str_replace("?", '', $recipesSlug));
	   $recipesSlug  = strtolower(str_replace("/", '', $recipesSlug));
	   $recipesSlug  = strtolower(str_replace("!", '', $recipesSlug));
	   $recipesSlug  = strtolower(str_replace(".", '', $recipesSlug));
	   $recipesSlug  = strtolower(str_replace(",", '', $recipesSlug));
	   $recipesSlug  = strtolower(str_replace("@", '', $recipesSlug));
	   $recipesSlug  = strtolower(str_replace("_", '', $recipesSlug));
	   $recipesSlug  = strtolower(str_replace("--", '-', $recipesSlug));
	   
	   $recipesSlug = $recipesSlug.'-'.$recipesID;
	   
	   	 $row->recipesCont = stripslashes($row->recipesCont);
		 $recipesTitle = stripslashes($recipesTitle); 
		 
		 if(isset($_POST['sidebar'])){
		foreach($_POST['sidebar'] as $side)
		{
		$sides.=$side.',';						
		}}
	   	      
$sql = "INSERT INTO ".PREFIX."recipes (recipesTitle, recipesSlug, recipesMetaKeywords, recipesMetaDescription, recipesDesc, recipesCont, recipesDate, recipesImg, sidebars) VALUES ('$recipesTitle', '$recipesSlug', '$recipesMetaKeywords', '$recipesMetaDescription', '$recipesDesc', '$recipesCont',  CURDATE(), '$recipesImg','$sides')";
$resultupdate = mysql_query($sql)or die(mysql_error());
$getID = mysql_insert_id();

$recipesSlug  = strtolower(str_replace('"', '-', $recipesSlug));
$recipesSlug = $recipesSlug.'-'.$getID;
$recipesSlug  = strtolower(str_replace('--', '', $recipesSlug));


$result = mysql_query("UPDATE ".PREFIX."recipes SET recipesSlug='$recipesSlug'WHERE recipesID='$getID'");

pingSE(DIR."sitemap.php","bing");
pingSE(DIR."sitemap.php","ask");
pingSE(DIR."sitemap.php","google");
pingSE(DIR."sitemap.php","moreover");

if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'Recipe Added';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'Recipe Added';
url('manage-add-ons/recipes');	
}

 
}// close errors
}// close if form sent

//dispaly any errors
errors($error);

?>

<form action="" method="post">

<p><label>Recipe Title</label> <input name="recipesTitle" class="box-medium tooltip-right" title="Enter the Recipes Title" type="text" value="<?php if (isset($error)){ echo $recipesTitle; }?>" size="40" maxlength="255"  />
</p>

<p><label>Meta Keywords</label> <textarea class="ta-default tooltip-top" title="Place keywords here coma seperated, to help search engines find this page" name="recipesMetaKeywords" cols="60" rows="5"><?php if (isset($error)){ echo $recipesMetaKeywords; }?></textarea></p>

<p><label>Meta Description</label> <textarea class="ta-default tooltip-top" title="Set the description that will be shown in search engines results" name="recipesMetaDescription" cols="60" rows="5"><?php if (isset($error)){ echo $recipesMetaDescription; }?></textarea></p>

<p><label>Recipe Description</label><textarea name="recipesDesc" id="edit1" cols="60" rows="20"><?php if (isset($error)){ echo $recipesDesc; }?></textarea>
</p>

<p><label>Recipe Content</label><textarea name="recipesCont" id="edit2" cols="60" rows="20"><?php if (isset($error)){ echo $recipesCont ; }?></textarea>
</p>

<p><label>Recipe Image</label><textarea name="recipesImg" id="edit3" cols="60" rows="20"><?php if (isset($error)){ echo $recipesImg; }?></textarea>
</p>

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


<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save page and return to recipes">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save page and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save page and return to recipes">
</form>
</div>
<?php
} else {
url(DIRADMIN);

}		
}

function editrecipes()
{
	global $curpage;
$curpage = true;
	if (isglobaladmin() || isadmin() || iseditor()){
		
echo "<div class=\"content-box-header\"><h3>Edit Recipe</h3></div> 			
<div class=\"content-box-content\">";	

	echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/recipes\">Recipes</a> > Edit Recipe</p></div>";

if(isset($_POST['cancel'])){
$_SESSION['info'] = "recipe was not updated";
url('manage-add-ons/recipes');
}

// if form submitted then process form
if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$recipesTitle = trim($_POST['recipesTitle']);
if (strlen($recipesTitle) < 3 || strlen($recipesTitle) > 255) {
$error[] = 'Title Must be between 3 and 255 characters.';
}

$recipesDesc = trim($_POST['recipesDesc']);
if (strlen($recipesDesc) < 3 ) {
$error[] = 'Description Must be more then 3 characters.';
}

$recipesCont = trim($_POST['ncont']);
if (strlen($recipesCont) < 3 ) {
$error[] = 'Content Must be more then 3 characters.';
}

$recipesTitle 	      = $_POST['recipesTitle'];
$recipesMetaKeywords  = $_POST['recipesMetaKeywords'];
$recipesMetaDescription  = $_POST['recipesMetaDescription'];
$recipesImg = $_POST['recipesImg'];

// print_r($error);
// if valadation is okay then carry on
if (!$error) {

	// post form data
   $recipesID 	          = $_POST['recipesID'];
   $recipesTitle 	      = $_POST['recipesTitle'];
   $recipesMetaKeywords  = $_POST['recipesMetaKeywords'];
   $recipesMetaDescription  = $_POST['recipesMetaDescription'];
   $recipesDesc          = $_POST['recipesDesc'];
   $recipesCont          = $_POST['ncont'];
   $recipesImg           = $_POST['recipesImg'];
 
	// escape any harmfull code and prevent sql injection
	$recipesTitle = mysql_real_escape_string($recipesTitle);
	$recipesCont   = mysql_real_escape_string($recipesCont );		
				   
	 $recipesTitle  = str_replace("/", '', $recipesTitle);
	 $recipesTitle  = str_replace(".", '', $recipesTitle);
	 $recipesTitle  = str_replace(",", '', $recipesTitle);
	 $recipesTitle  = str_replace("@", '', $recipesTitle);
	 $recipesTitle  = str_replace("'", '', $recipesTitle);
		 
	   $recipesSlug  = strtolower(str_replace(" ", '-', $recipesTitle));
	   $recipesSlug  = strtolower(str_replace("&", 'and', $recipesSlug));
	   $recipesSlug  = strtolower(str_replace("'", '', $recipesSlug));
	   $recipesSlug  = strtolower(str_replace("?", '', $recipesSlug));
	   $recipesSlug  = strtolower(str_replace("/", '', $recipesSlug));
	   $recipesSlug  = strtolower(str_replace("!", '', $recipesSlug));
	   $recipesSlug  = strtolower(str_replace(".", '', $recipesSlug));
	   $recipesSlug  = strtolower(str_replace(",", '', $recipesSlug));
	   $recipesSlug  = strtolower(str_replace("@", '', $recipesSlug));
	   $recipesSlug  = strtolower(str_replace("_", '', $recipesSlug));
	   $recipesSlug  = strtolower(str_replace("--", '-', $recipesSlug));
	   
	   $recipesSlug = $recipesSlug.'-'.$recipesID;
		 
		$recipesSlug = stripslashes($recipesSlug); 
		$recipesTitle = stripslashes($recipesTitle);
		
		foreach($_POST['sidebar'] as $side)
		{
		$sides.=$side.',';						
		} 
	   	      
$sql = mysql_query("UPDATE ".PREFIX."recipes SET recipesTitle='$recipesTitle', recipesSlug='$recipesSlug', recipesMetaKeywords='$recipesMetaKeywords', recipesMetaDescription='$recipesMetaDescription', recipesDesc='$recipesDesc', recipesCont='$recipesCont', recipesImg='$recipesImg', sidebars='$sides' WHERE recipesID='$recipesID'"); 

if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'Recipe Updated';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'Recipe Updated';
url('manage-add-ons/recipes');	
}  
  
  
 
}// close errors
}// close if form sent

//dispaly any errors
errors($error);

$result = mysql_query("SELECT * FROM ".PREFIX."recipes WHERE recipesID='{$_GET['edit-recipes']}'");
while ($row = mysql_fetch_object($result)){
$row->recipesCont = stripslashes($row->recipesCont);
?>

<form action="" method="post">
<input type="hidden" name="recipesID" value="<?php echo $row->recipesID;?>">

<p><label>Recipe Title</label> <input name="recipesTitle" class="box-medium tooltip-right" title="Enter the Recipes Title" type="text" value="<?php echo $row->recipesTitle; ?>" size="40" maxlength="255"  /></p>

<p><label>Meta Keywords</label><br /> <textarea class="ta-default tooltip-top" title="Place keywords here coma seperated, to help search engines find this page" name="recipesMetaKeywords" cols="60" rows="5"><?php echo $row->recipesMetaKeywords;?></textarea></p>

<p><label>Meta Description</label><br /> <textarea class="ta-default tooltip-top" title="Set the description that will be shown in search engines results" name="recipesMetaDescription" cols="60" rows="5"><?php echo $row->recipesMetaDescription;?></textarea></p>

<p><label>Recipe Description</label><textarea name="recipesDesc" id="edit1" cols="60" rows="20"><?php echo $row->recipesDesc;?></textarea>
</p>

<p><label>Recipe Content</label><textarea name="ncont" id="edit2" cols="60" rows="20"><?php echo $row->recipesCont;?></textarea>
</p>

<p><label>Recipe Image</label><textarea name="recipesImg" id="edit3" cols="60" rows="20"><?php echo $row->recipesImg; ?></textarea>
</p>

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

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save page and return to recipes">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save page and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save page and return to recipes">
</form>
</div>
<?php }
} else {
url(DIRADMIN);

}	
}




function recipesRequest()
{
	if(isset($_GET['recipes'])){
	managerecipes();	
	}
	
	if(isset($_GET['add-recipes'])){
	addrecipes();
	}
	
	if(isset($_GET['edit-recipes'])){
	editrecipes();
	}
	
	if(isset($_GET['delrecipes'])){
	delrecipes();	
	}
	
	$result = mysql_query("SELECT * FROM ".PREFIX."recipes WHERE recipesSlug='".PAGE."'")or die(mysql_error());
	$n = mysql_num_rows($result);
	while ($nrow = mysql_fetch_object($result))
	{
	global $curpage,$s,$page,$breadcrumb;
    $curpage = true;
	
	$breadcrumb.= "<a href=\"".DIR."recipes\">Recipes</a> > $nrow->recipesTitle";

	
	 $up = mysql_query("UPDATE ".PREFIX."recipes SET recipesViews=recipesViews+1 WHERE recipesSlug='".PAGE."'")or die(mysql_error());
	 

		
	$precipes.="<div class=\"post\">";  
		
	$isql = mysql_query("SELECT * FROM ".PREFIX."recipes_limit")or die(mysql_error());
	$sm = mysql_fetch_object($isql);	
	
	$precipes.="<div class=\"postcontent\">\n";
	$precipes.="<h1 class=\"recipesTitle\" title=\"$nrow->recipesTitle\">$nrow->recipesTitle</h1>";
	
	$wi = $sm->thumbWidth;
	$hi = $sm->thumbHeight;
	$img =  $nrow->recipesImg;
	$img = str_replace("../../../..","",$img);	
	$str = preg_replace('#<p>.+?src=[\'"]([^\'"]+)[\'"].+</p>#i', "$1", $img);
	if($nrow->recipesImg !=''){
	$str = str_replace("../../../","",$str);
	$precipes.="<a href=\"".DIR."$str\" rel=\"prettyPhoto\" title=\"$nrow->recipesTitle\" style=\"float:right;\"><img src=\"img.php?src=$str&w=$wi&h=$hi&zc=1\" alt=\"$nrow->recipesTitle\" title=\"$nrow->recipesTitle\" class=\"thumb\" /></a>\n";
	}
	
	$precipes.=stripslashes($nrow->recipesCont);
	$precipes.="</div><!-- /postcontent -->\n";
	$precipes.="</div><!-- close post -->"; 	
	 
	$s.=$nrow->sidebars; 
	$_SESSION['plugcont'] = $precipes;
	define('THEME','inner-page.php');
	define('THEMEPATH','assets/templates/');
	define('ISPLUGPAGE','Yes');
	}

	
}

function delrecipes() {

$delType = $_GET['delType'];
$delID   = $_GET['delID'];
$delID = mysql_real_escape_string($delID);

if($delType == 'delrecipes')
{
   $query = mysql_query("DELETE FROM ".PREFIX."recipes WHERE recipesID = '$delID'")or die('Error : ' . mysql_error());  
   $_SESSION['success'] = 'Recipes Deleted';
   url('manage-add-ons/recipes');
}
}



function recipes($string) 
{

//global $nep;
$nep = $_GET['np'];

if(!isset($nep)){
	$recipespage = 1;
} else {
	$recipespage = $nep;
}



	$sql = mysql_query("SELECT * FROM ".PREFIX."recipes_limit");
$row = mysql_fetch_object($sql);

// Define the number of results per allclientspages
$max_results = $row->recipeslimit;

// Figure out the limit for the query based
$from = (($recipespage * $max_results) - $max_results);

$result = mysql_query("SELECT * FROM ".PREFIX."recipes WHERE recipesID ORDER BY recipesID DESC LIMIT $from, $max_results")or die(mysql_error());

while ($nrow = mysql_fetch_object($result)){

	$recipesOutput.="<div class=\"post\">";  
		
	$isql = mysql_query("SELECT * FROM ".PREFIX."recipes_limit")or die(mysql_error());
	$s = mysql_fetch_object($isql);
	
	$wi = $s->thumbWidth;
	$hi = $s->thumbHeight;
	$img =  $nrow->recipesImg;//'assets/templates/images/minipostthumb.jpg';
	$img = str_replace("../../../..","",$img);
	$str = preg_replace('#<p>.+?src=[\'"]([^\'"]+)[\'"].+</p>#i', "$1", $img);
	if($nrow->recipesImg !=''){
	$recipesOutput.="<a href=\"".DIR."$nrow->recipesSlug\" title=\"$nrow->recipesTitle\"><img src=\"img.php?src=$str&w=$wi&h=$hi&zc=1\" alt=\"$nrow->recipesTitle\" title=\"$nrow->recipesTitle\" class=\"thumb\" /></a>\n";
	}
	
	$recipesOutput.="<div class=\"postcontent\">\n";
	$recipesOutput.="<h1 class=\"recipesTitle\" title=\"$nrow->recipesTitle\"><a href=\"".DIR."$nrow->recipesSlug\" title=\"$nrow->recipesTitle\">$nrow->recipesTitle</a></h1>";
	
	$recipesOutput.=$nrow->recipesDesc;
	$recipesOutput.="<p><a href=\"".DIR."$nrow->recipesSlug\" title=\"$nrow->recipesTitle\">View Recipe ></a></p>\n";
	$recipesOutput.="</div><!-- /postcontent -->\n";
	$recipesOutput.="</div><!-- close post -->"; 
}
 
	
// Figure out the total number of results in DB:
$total_results = mysql_result(mysql_query("SELECT COUNT(*) as Num FROM ".PREFIX."recipes"),0);

// Figure out the total number of clientspages. Always round up using ceil()
$total_recipespage = ceil($total_results / $max_results);

// Build clientspage Number Hyperlinks

$recipesOutput.= "<div class=\"pagination\">";


// Build Previous Link
			if($recipespage > 1){
				$prev = ($recipespage - 1);
				$recipesOutput.=  "<a href=\"?np=$prev\">Previous</a> ";
			}
			
			for($i = 1; $i <= $total_recipespage; $i++){
				if($total_recipespage > 1){
					if(($recipespage) == $i){
						$recipesOutput.=  "<span class=\"current\">$i</span>";
						} else {
							$recipesOutput.=  "<a href=\"?np=$i\">$i</a> ";
					}
				}
			}
			
			// Build Next Link
			if($recipespage < $total_recipespage){
				$next = ($recipespage + 1);
				$recipesOutput.=  "<a href=\"?np=$next\">Next</a>";
			}
			$recipesOutput.=  "</div>"; 
			
			$string = str_replace("[recipes]", $recipesOutput, $string);
		return $string;
}


function recipessnippitmain($string) {

	
$sql = mysql_query("SELECT * FROM ".PREFIX."recipes_limit");
$row = mysql_fetch_object($sql);

// Define the number of results per allclientspages
$max_results = $row->snippetlimit;
	
	$result = mysql_query("SELECT SUBSTRING(recipesTitle,1,35) as recipesTitle, recipesSlug, recipesImg, recipesDate FROM ".PREFIX."recipes WHERE recipesID ORDER BY recipesID DESC LIMIT $max_results ")or die(mysql_error());
	$newSnippitOutput.="<ul class=\"single-col\">\n";
	while ($r = mysql_fetch_object($result))
	{
		  $wi = '60';
		  $hi = '40';
		  $img =  $r->recipesImg;//'assets/templates/images/minipostthumb.jpg';
		  $img = str_replace("../../../..","",$img);
		  $str = preg_replace('#<p>.+?src=[\'"]([^\'"]+)[\'"].+</p>#i', "$1", $img);
		  
		  $date = date('l jS \of F Y', strtotime($r->recipesDate)); 
		  
		  if($r->recipesImg !=''){
			  $newSnippitOutput.="<li>
			  <div class=\"tiny-thumb\"><a title=\"$r->recipesTitle\" href=\"".DIR."$r->recipesSlug\"><img alt=\"$r->postTitle\" src=\"img.php?src=$str&w=$wi&h=$hi&zc=0\"></a></div>
			  <div class=\"block\"><a title=\"$r->recipesTitle\" href=\"".DIR."$r->recipesSlug\" class=\"bold\">$r->recipesTitle</a> <a title=\"$r->recipesTitle\" href=\"".DIR."$r->recipesSlug\" style=\"color:#999;\">View Recipe ></a></div></li>";
		  } else {
			  $newSnippitOutput.="<li>
			  <div class=\"tiny-thumb\"><a title=\"$r->recipesTitle\" href=\"".DIR."$r->recipesSlug\"><img alt=\"$r->recipesTitle\" src=\"img.php?src=assets/templates/images/minipostthumb.jpg&w=$wi&zc=0\"></a></div>
			  <div class=\"block\"><a title=\"$r->recipesTitle\" href=\"".DIR."$r->recipesSlug\" class=\"bold\">$r->recipesTitle</a> <a title=\"$r->recipesTitle\" href=\"".DIR."$r->recipesSlug\" style=\"color:#999;\">View Recipe ></a></div></li>";
		  }  
		
		
	}
	$newSnippitOutput.="</ul>";
	//$newSnippitOutput.="<p style=\"text-align:right; font-size:14px; margin-bottom:0px;\"><a href=\"".DIR."recipes\">See All Recipes</a></p>";
	
	$string = str_replace("[recipessnippit]", $newSnippitOutput, $string);
	return $string;
}

function addLinksrecipes() {
	echo "bk<div class=\"icon\">\n";
		echo "<a href=\"".DIR."admin/manage-add-ons/recipes\"><img src=\"".DIR."assets/plugins/recipes/recipes.png\" alt=\"Recipes\" title=\"Manage Recipes\" class=\"tooltip-top\" /></a>\n";
    	echo "<p><a href=\"".DIR."admin/manage-add-ons/recipes\" title=\"Manage Recipes\" class=\"tooltip-top\">Recipes</a></p>\n";
    echo "</div>\n";	
}


//add hook, where to execute a function
add_hook('admin_modules','addLinksrecipes');
add_hook('cont','recipes');
add_hook('cont','recipessnippitmain');
add_hook('del', 'delrecipes');
add_hook('page_requester','recipesRequest');
?>
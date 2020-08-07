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

if (preg_match('/news/', $data))
{
} else { 

$newData = "
RewriteRule ^admin/manage-add-ons/newssnippit$                    admin.php?newssnippit=$1 [L]
RewriteRule ^admin/manage-add-ons/newssnippit/$                   admin.php?newssnippit=$1 [L]

RewriteRule ^admin/manage-add-ons/news$                    admin.php?news=$1 [L]
RewriteRule ^admin/manage-add-ons/news/$                   admin.php?news=$1 [L]

RewriteRule ^admin/manage-add-ons/news/add-news$            admin.php?add-news=$1 [L]
RewriteRule ^admin/manage-add-ons/news/add-news/$           admin.php?add-news=$1 [L]

RewriteRule ^admin/manage-add-ons/news/edit-news-([^/]+)$   admin.php?edit-news=$1 [L]
RewriteRule ^admin/manage-add-ons/news/edit-news-([^/]+)/$  admin.php?edit-news=$1 [L]

RewriteRule ^newsp-([^/]+)$                  	              index.php?newspage=$1 [L]
RewriteRule ^newsp-([^/]+)/$                 	              index.php?newspage=$1 [L]
###";

$data = str_replace("###",$newData,$data);

$myFile = $cfile;
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $data);
fclose($fh);


mysql_query("CREATE TABLE IF NOT EXISTS `".PREFIX."news` (
  `newsID` int(11) NOT NULL auto_increment,
  `newsTitle` varchar(255) NOT NULL,
  `newsSlug` varchar(255) NOT NULL,
  `newsMetaKeywords` text NOT NULL,
  `newsMetaDescription` text NOT NULL,
  `newsDesc` text NOT NULL,
  `newsCont` text NOT NULL,
  `newsDate` datetime NOT NULL,
  PRIMARY KEY  (`newsID`)
) ENGINE=MyISAM");

mysql_query("CREATE TABLE IF NOT EXISTS `".PREFIX."news_limit` (
  `newslimit` int(11) NOT NULL default '5',
  `snippetlimit` int(11) NOT NULL default '5',
  `thumbWidth` int(3) NOT NULL default '100',
  `thumbHeight` int(3) NOT NULL default '100',
   PRIMARY KEY  (`newslimit`)
) ENGINE=MyISAM");

mysql_query("INSERT INTO `".PREFIX."news_limit` (`newslimit`) VALUES
( '5')");

mysql_query("INSERT INTO `".PREFIX."news_limit` (`snippetlimit`) VALUES
( '5')");


}




function managenews()
{
global $curpage;
$curpage = true;
if (isglobaladmin() || isadmin()){
	
echo "<div class=\"content-box-header\"><h3>News</h3></div> 			
<div class=\"content-box-content\">";	


echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > News</p></div>";

echo messages();

echo "<p>To implement the news insert [news] into any page you want the news to be displayed.</p>\n";

$result = mysql_query("SELECT * FROM ".PREFIX."news ORDER BY newsID DESC")or die(mysql_error());


if(isset($_POST['newsub']))
{
$lim = $_POST['newslimit'];
$thumbWidth = $_POST['thumbWidth'];
$thumbHeight = $_POST['thumbHeight'];

$lim = mysql_real_escape_string($lim);
$thumbWidth = mysql_real_escape_string($thumbWidth);
$thumbHeight = mysql_real_escape_string($thumbHeight);

$sql = mysql_query("UPDATE ".PREFIX."news_limit SET newslimit='$lim', thumbWidth='$thumbWidth', thumbHeight='$thumbHeight'")or die(mysql_error());
$_SESSION['success'] = 'Updated';
  url('manage-add-ons/news');
}

$sql = mysql_query("SELECT * FROM ".PREFIX."news_limit")or die(mysql_error());
$limit = mysql_fetch_object($sql);


?>

<form action="" method="post">
<p><label>Number per page</label><input name="newslimit" type="text" class="box-small tooltip-right" title="Enter number of news items to be shown per page" value="<?php echo $limit->newslimit;?>" size="3" /></p>

<p><label>Thumbnail Width</label><input name="thumbWidth" type="text" class="box-small tooltip-right" title="Set the width of thumbnails in pixels" value="<?php echo $limit->thumbWidth;?>" size="3" /></p>

<p><label>Thumbnail Height</label><input name="thumbHeight" type="text" class="box-small tooltip-right" title="Set the height of thumbnails in pixels" value="<?php echo $limit->thumbHeight;?>" size="3" /></p>

<p><input type="submit" class="button tooltip-right" title="Save Changes" name="newsub" value="submit" /></p>
</form>

<table class="stripeMe">
<tr align="center">
<td width="41%" align="left"><strong>News</strong></td>
<td width="59%"><strong>Action</strong></td>
</tr>
<?php
while ($row = mysql_fetch_object($result)) {
?>
<tr>
<td><?php echo $row->newsTitle;?></td><td align="center" valign="top"><a href="<?php echo DIRADMIN;?>manage-add-ons/news/edit-news-<?php echo $row->newsID;?>" class="tooltip-top" title="Edit"><img src="<?php echo DIR;?>assets/images/icons/action-edit.png" alt="Edit" /></a> <a href="#" id="<?php echo $row->newsID;?>" rel="delnews" title="<?php echo $row->newsSlug;?>" class="delete_button"><img src="<?php echo DIR;?>assets/images/icons/action-del.png" alt="Delete" /></a></td>
</tr>
<?php
}
?>
</table>
<p><a href="<?php echo DIRADMIN;?>manage-add-ons/news/add-news" class="button tooltip-right" title="Add New Story">Add News</a></p>
<?php


echo "<p>Get a news snippet from the last news items</p><p>To implement the news snippit insert [newssnippit] into any page or sidebar panel you want the news to be displayed.</p>\n";

if(isset($_POST['snipsub']))
{
$lim = $_POST['snippetlimit'];
$lim = mysql_real_escape_string($lim);

$sql = mysql_query("UPDATE ".PREFIX."news_limit SET snippetlimit='$lim'")or die(mysql_error());
$_SESSION['success'] = 'Snippet Updated Added';
  url('manage-add-ons/news');
}

$sql = mysql_query("SELECT * FROM ".PREFIX."news_limit")or die(mysql_error());
$limit = mysql_fetch_object($sql);


?>

<form action="" method="post">
<p>Number of news items to show</p>
<p><input name="snippetlimit" type="text" class="box-small tooltip-right" title="Enter number of news items to show" value="<?php echo $limit->snippetlimit;?>" size="3" />
</p>
<p><input type="submit" name="snipsub" class="button tooltip-right" title="Save Changes" value="Submit" /></p>
</form>
</div>
<?php

} else {
url(DIRADMIN);

}		
}


function addnews()
{
	global $curpage;
$curpage = true;
	if (isglobaladmin() || isadmin() || iseditor()){
		
echo "<div class=\"content-box-header\"><h3>Add News</h3></div> 			
<div class=\"content-box-content\">";			
		
	echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/news\">News</a> > Add News</p></div>";
	
echo messages();

if(isset($_POST['cancel'])){
$_SESSION['info'] = "news was not created";
url('manage-add-ons/news');
}

// if form submitted then process form
if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$newsTitle = trim($_POST['newsTitle']);
if (strlen($newsTitle) < 3 || strlen($newsTitle) > 255) {
$error[] = 'Title Must be between 3 and 255 characters.';
}

$newsDesc = trim($_POST['newsDesc']);
if (strlen($newsDesc) < 3 ) {
$error[] = 'Description Must be more then 3 characters.';
}


$newsCont = trim($_POST['newsCont']);
if (strlen($newsCont) < 3 ) {
$error[] = 'Content Must be more then 3 characters.';
}

$newsTitle 	      = $_POST['newsTitle'];
$newsMetaKeywords  = $_POST['newsMetaKeywords'];
$newsMetaDescription  = $_POST['newsMetaDescription'];


// print_r($error);
// if valadation is okay then carry on
if (!$error) {

	// post form data
   $newsTitle 	      = $_POST['newsTitle'];
   $newsMetaKeywords  = $_POST['newsMetaKeywords'];
   $newsMetaDescription  = $_POST['newsMetaDescription'];
   $newsDesc           = $_POST['newsDesc'];
   $newsCont           = $_POST['newsCont'];
   $newsImg           = $_POST['newsImg'];
 
   
   // add slashes if needed
	   if(!get_magic_quotes_gpc())
		{ 
		$newsTitle        = addslashes($newsTitle);
		$newsCont        = addslashes($newsCont);
		}

	
		// escape any harmfull code and prevent sql injection
		$newsTitle = mysql_real_escape_string($newsTitle);
		$newsCont   = mysql_real_escape_string($newsCont );		
 
	   
	 $newsTitle  = str_replace("/", '', $newsTitle);
	 $newsTitle  = str_replace(".", '', $newsTitle);
	 $newsTitle  = str_replace(",", '', $newsTitle);
	 $newsTitle  = str_replace("@", '', $newsTitle);
	 $newsTitle  = str_replace("'", '', $newsTitle);
		 
	   $newsSlug  = strtolower(str_replace(" ", '-', $newsTitle));
	   $newsSlug  = strtolower(str_replace("'", '', $newsSlug));
	   $newsSlug  = strtolower(str_replace("?", '', $newsSlug));
	   $newsSlug  = strtolower(str_replace("/", '', $newsSlug));
	   $newsSlug  = strtolower(str_replace("!", '', $newsSlug));
	   $newsSlug  = strtolower(str_replace(".", '', $newsSlug));
	   $newsSlug  = strtolower(str_replace(",", '', $newsSlug));
	   $newsSlug  = strtolower(str_replace("@", '', $newsSlug));
	   $newsSlug  = strtolower(str_replace("_", '', $newsSlug));
	   $newsSlug  = strtolower(str_replace("--", '-', $newsSlug));
	   
	   $newsSlug = $newsSlug.'-'.$newsID;
	   
	   	 $row->newsCont = stripslashes($row->newsCont);
		 $newsTitle = stripslashes($newsTitle); 
		 
		 if(isset($_POST['sidebar'])){
		foreach($_POST['sidebar'] as $side)
		{
		$sides.=$side.',';						
		}}
	   	      
$sql = "INSERT INTO ".PREFIX."news (newsTitle, newsSlug, newsMetaKeywords, newsMetaDescription, newsDesc, newsCont, newsDate, newsImg, sidebars) VALUES ('$newsTitle', '$newsSlug', '$newsMetaKeywords', '$newsMetaDescription', '$newsDesc', '$newsCont',  CURDATE(), '$newsImg','$sides')";
$resultupdate = mysql_query($sql)or die(mysql_error());
$getID = mysql_insert_id();

$newsSlug  = strtolower(str_replace('"', '-', $newsSlug));
$newsSlug = $newsSlug.'-'.$getID;
$newsSlug  = strtolower(str_replace('--', '', $newsSlug));


$result = mysql_query("UPDATE ".PREFIX."news SET newsSlug='$newsSlug'WHERE newsID='$getID'");

pingSE(DIR."sitemap.php","bing");
pingSE(DIR."sitemap.php","ask");
pingSE(DIR."sitemap.php","google");
pingSE(DIR."sitemap.php","moreover");

if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'News Added';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'News Added';
url('manage-add-ons/news');	
}

 
}// close errors
}// close if form sent

//dispaly any errors
errors($error);

?>

<form action="" method="post">

<p><label>News Title</label> <input name="newsTitle" class="box-medium tooltip-right" title="Enter the News Title" type="text" value="<?php if (isset($error)){ echo $newsTitle; }?>" size="40" maxlength="255"  />
</p>

<p><label>Meta Keywords</label> <textarea class="ta-default tooltip-top" title="Place keywords here coma seperated, to help search engines find this page" name="newsMetaKeywords" cols="60" rows="5"><?php if (isset($error)){ echo $newsMetaKeywords; }?></textarea></p>

<p><label>Meta Description</label> <textarea class="ta-default tooltip-top" title="Set the description that will be shown in search engines results" name="newsMetaDescription" cols="60" rows="5"><?php if (isset($error)){ echo $newsMetaDescription; }?></textarea></p>

<p><label>News Description</label><textarea name="newsDesc" id="newsDesc" cols="60" rows="20"><?php if (isset($error)){ echo $newsDesc; }?></textarea>
</p>

<p><label>News Content</label><textarea name="newsCont" id="newsCont" cols="60" rows="20"><?php if (isset($error)){ echo $newsCont ; }?></textarea>
</p>

<p><label>News Image</label><textarea name="newsImg" id="newsImg" cols="60" rows="20"><?php if (isset($error)){ echo $newsImg; }?></textarea>
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


<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save page and return to news">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save page and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save page and return to news">
</form>
</div>
<?php
} else {
url(DIRADMIN);

}		
}

function editnews()
{
	global $curpage;
$curpage = true;
	if (isglobaladmin() || isadmin() || iseditor()){
		
echo "<div class=\"content-box-header\"><h3>Edit News</h3></div> 			
<div class=\"content-box-content\">";	

	echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/news\">News</a> > Edit News</p></div>";

if(isset($_POST['cancel'])){
$_SESSION['info'] = "news was not created";
url('manage-add-ons/news');
}

// if form submitted then process form
if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$newsTitle = trim($_POST['newsTitle']);
if (strlen($newsTitle) < 3 || strlen($newsTitle) > 255) {
$error[] = 'Title Must be between 3 and 255 characters.';
}

$newsDesc = trim($_POST['newsDesc']);
if (strlen($newsDesc) < 3 ) {
$error[] = 'Description Must be more then 3 characters.';
}

$newsCont = trim($_POST['ncont']);
if (strlen($newsCont) < 3 ) {
$error[] = 'Content Must be more then 3 characters.';
}

$newsTitle 	      = $_POST['newsTitle'];
$newsMetaKeywords  = $_POST['newsMetaKeywords'];
$newsMetaDescription  = $_POST['newsMetaDescription'];
$newsImg = $_POST['newsImg'];

// print_r($error);
// if valadation is okay then carry on
if (!$error) {

	// post form data
   $newsID 	          = $_POST['newsID'];
   $newsTitle 	      = $_POST['newsTitle'];
   $newsMetaKeywords  = $_POST['newsMetaKeywords'];
   $newsMetaDescription  = $_POST['newsMetaDescription'];
   $newsDesc          = $_POST['newsDesc'];
   $newsCont          = $_POST['ncont'];
   $newsImg           = $_POST['newsImg'];
 
	// escape any harmfull code and prevent sql injection
	$newsTitle = mysql_real_escape_string($newsTitle);
	$newsCont   = mysql_real_escape_string($newsCont );		
				   
	 $newsTitle  = str_replace("/", '', $newsTitle);
	 $newsTitle  = str_replace(".", '', $newsTitle);
	 $newsTitle  = str_replace(",", '', $newsTitle);
	 $newsTitle  = str_replace("@", '', $newsTitle);
	 $newsTitle  = str_replace("'", '', $newsTitle);
		 
	   $newsSlug  = strtolower(str_replace(" ", '-', $newsTitle));
	   $newsSlug  = strtolower(str_replace("'", '', $newsSlug));
	   $newsSlug  = strtolower(str_replace("?", '', $newsSlug));
	   $newsSlug  = strtolower(str_replace("/", '', $newsSlug));
	   $newsSlug  = strtolower(str_replace("!", '', $newsSlug));
	   $newsSlug  = strtolower(str_replace(".", '', $newsSlug));
	   $newsSlug  = strtolower(str_replace(",", '', $newsSlug));
	   $newsSlug  = strtolower(str_replace("@", '', $newsSlug));
	   $newsSlug  = strtolower(str_replace("_", '', $newsSlug));
	   $newsSlug  = strtolower(str_replace("--", '-', $newsSlug));
	   
	   $newsSlug = $newsSlug.'-'.$newsID;
		 
		$newsSlug = stripslashes($newsSlug); 
		$newsTitle = stripslashes($newsTitle);
		
		foreach($_POST['sidebar'] as $side)
		{
		$sides.=$side.',';						
		} 
	   	      
$sql = mysql_query("UPDATE ".PREFIX."news SET newsTitle='$newsTitle', newsSlug='$newsSlug', newsMetaKeywords='$newsMetaKeywords', newsMetaDescription='$newsMetaDescription', newsDesc='$newsDesc', newsCont='$newsCont', newsImg='$newsImg', sidebars='$sides' WHERE newsID='$newsID'"); 

if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'News Updated';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'News Updated';
url('manage-add-ons/news');	
}  
  
  
 
}// close errors
}// close if form sent

//dispaly any errors
errors($error);

$result = mysql_query("SELECT * FROM ".PREFIX."news WHERE newsID='{$_GET['edit-news']}'");
while ($row = mysql_fetch_object($result)){
$row->newsCont = stripslashes($row->newsCont);
?>

<form action="" method="post">
<input type="hidden" name="newsID" value="<?php echo $row->newsID;?>">

<p><label>News Title</label> <input name="newsTitle" class="box-medium tooltip-right" title="Enter the News Title" type="text" value="<?php echo $row->newsTitle; ?>" size="40" maxlength="255"  /></p>

<p><label>Meta Keywords</label><br /> <textarea class="ta-default tooltip-top" title="Place keywords here coma seperated, to help search engines find this page" name="newsMetaKeywords" cols="60" rows="5"><?php echo $row->newsMetaKeywords;?></textarea></p>

<p><label>Meta Description</label><br /> <textarea class="ta-default tooltip-top" title="Set the description that will be shown in search engines results" name="newsMetaDescription" cols="60" rows="5"><?php echo $row->newsMetaDescription;?></textarea></p>

<p><label>News Description</label><textarea name="newsDesc" id="newsDesc" cols="60" rows="20"><?php echo $row->newsDesc;?></textarea>
</p>

<p><label>News Content</label><textarea name="ncont" id="newsCont" cols="60" rows="20"><?php echo $row->newsCont;?></textarea>
</p>

<p><label>News Image</label><textarea name="newsImg" id="newsImg" cols="60" rows="20"><?php echo $row->newsImg; ?></textarea>
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

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save page and return to news">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save page and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save page and return to news">
</form>
</div>
<?php }
} else {
url(DIRADMIN);

}	
}




function newsRequest()
{
	if(isset($_GET['news'])){
	managenews();	
	}
	
	if(isset($_GET['add-news'])){
	addnews();
	}
	
	if(isset($_GET['edit-news'])){
	editnews();
	}
	
	if(isset($_GET['delnews'])){
	delnews();	
	}
	
	$result = mysql_query("SELECT * FROM ".PREFIX."news WHERE newsSlug='".PAGE."'")or die(mysql_error());
	$n = mysql_num_rows($result);
	while ($nrow = mysql_fetch_object($result))
	{
	global $curpage,$s,$page,$breadcrumb;
    $curpage = true;
	
	$breadcrumb.= "<a href=\"".DIR."news\">News</a> > $nrow->newsTitle";

	
	 $up = mysql_query("UPDATE ".PREFIX."news SET newsViews=newsViews+1 WHERE newsSlug='".PAGE."'")or die(mysql_error());
	 
	$date = date('l jS \of F Y', strtotime($nrow->newsDate)); 
		
	$pnews.="<div class=\"post\">";  
		
	$isql = mysql_query("SELECT * FROM ".PREFIX."news_limit")or die(mysql_error());
	$sm = mysql_fetch_object($isql);
	
	$wi = $sm->thumbWidth;
	$hi = $sm->thumbHeight;
	$img =  $nrow->newsImg;//'assets/templates/images/minipostthumb.jpg';
	$img = str_replace("../../../..","",$img);
	$str = preg_replace('#<p>.+?src=[\'"]([^\'"]+)[\'"].+</p>#i', "$1", $img);
	if($nrow->newsImg !=''){
	$pnews.="<a href=\"".DIR."$str\" rel=\"prettyPhoto\" title=\"$nrow->newsTitle\"><img src=\"img.php?src=$str&w=$wi&h=$hi&zc=1\" alt=\"$nrow->newsTitle\" title=\"$nrow->newsTitle\" class=\"thumb\" /></a>\n";
	}
	
	$pnews.="<div class=\"postcontent\">\n";
	$pnews.="<h1 class=\"newsTitle\" title=\"$nrow->newsTitle\">$nrow->newsTitle</h1>";
	if($nrow->newsDate !='0000-00-00 00:00:00'){
	$pnews.="<p class=\"meta\"><span>$date </span></p>\n";
	}
	$pnews.=stripslashes($nrow->newsCont);
	$pnews.="</div><!-- /postcontent -->\n";
	$pnews.="</div><!-- close post -->"; 	
	 
	$s.=$nrow->sidebars; 
	$_SESSION['plugcont'] = $pnews;
	define('THEME','inner-page.php');
	define('THEMEPATH','assets/templates/');
	define('ISPLUGPAGE','Yes');
	}

	
}

function delnews() {

$delType = $_GET['delType'];
$delID   = $_GET['delID'];
$delID = mysql_real_escape_string($delID);

if($delType == 'delnews')
{
   $query = mysql_query("DELETE FROM ".PREFIX."news WHERE newsID = '$delID'")or die('Error : ' . mysql_error());  
   $_SESSION['success'] = 'News Deleted';
   url('manage-add-ons/news');
}
}



function news($string) 
{

//global $nep;
$nep = $_GET['np'];

if(!isset($nep)){
	$newspage = 1;
} else {
	$newspage = $nep;
}



	$sql = mysql_query("SELECT * FROM ".PREFIX."news_limit");
$row = mysql_fetch_object($sql);

// Define the number of results per allclientspages
$max_results = $row->newslimit;

// Figure out the limit for the query based
$from = (($newspage * $max_results) - $max_results);

$result = mysql_query("SELECT * FROM ".PREFIX."news WHERE newsID ORDER BY newsID DESC LIMIT $from, $max_results")or die(mysql_error());

while ($nrow = mysql_fetch_object($result)){

	$date = date('l jS \of F Y', strtotime($nrow->newsDate)); 
	
	$newsOutput.="<div class=\"post\">";  
		
	$isql = mysql_query("SELECT * FROM ".PREFIX."news_limit")or die(mysql_error());
	$s = mysql_fetch_object($isql);
	
	$wi = $s->thumbWidth;
	$hi = $s->thumbHeight;
	$img =  $nrow->newsImg;//'assets/templates/images/minipostthumb.jpg';
	$img = str_replace("../../../..","",$img);
	$str = preg_replace('#<p>.+?src=[\'"]([^\'"]+)[\'"].+</p>#i', "$1", $img);
	if($nrow->newsImg !=''){
	$newsOutput.="<a href=\"".DIR."$nrow->newsSlug\" title=\"$nrow->newsTitle\"><img src=\"img.php?src=$str&w=$wi&h=$hi&zc=1\" alt=\"$nrow->newsTitle\" title=\"$nrow->newsTitle\" class=\"thumb\" /></a>\n";
	}
	
	$newsOutput.="<div class=\"postcontent\">\n";
	$newsOutput.="<h1 class=\"newsTitle\" title=\"$nrow->newsTitle\"><a href=\"".DIR."$nrow->newsSlug\" title=\"$nrow->newsTitle\">$nrow->newsTitle</a></h1>";
	if($nrow->newsDate !='0000-00-00 00:00:00'){
	$newsOutput.="<p class=\"meta\"><span>$date </span></p>\n";
	}
	$newsOutput.=$nrow->newsDesc;
	$newsOutput.="<p><a href=\"".DIR."$nrow->newsSlug\" title=\"$nrow->newsTitle\">Read More</a></p>\n";
	$newsOutput.="</div><!-- /postcontent -->\n";
	$newsOutput.="</div><!-- close post -->"; 
}
 
	
// Figure out the total number of results in DB:
$total_results = mysql_result(mysql_query("SELECT COUNT(*) as Num FROM ".PREFIX."news"),0);

// Figure out the total number of clientspages. Always round up using ceil()
$total_newspage = ceil($total_results / $max_results);

// Build clientspage Number Hyperlinks

$newsOutput.= "<div class=\"pagination\">";


// Build Previous Link
			if($newspage > 1){
				$prev = ($newspage - 1);
				$newsOutput.=  "<a href=\"?np=$prev\">Previous</a> ";
			}
			
			for($i = 1; $i <= $total_newspage; $i++){
				if($total_newspage > 1){
					if(($newspage) == $i){
						$newsOutput.=  "<span class=\"current\">$i</span>";
						} else {
							$newsOutput.=  "<a href=\"?np=$i\">$i</a> ";
					}
				}
			}
			
			// Build Next Link
			if($newspage < $total_newspage){
				$next = ($newspage + 1);
				$newsOutput.=  "<a href=\"?np=$next\">Next</a>";
			}
			$newsOutput.=  "</div>"; 
			
			$string = str_replace("[news]", $newsOutput, $string);
		return $string;
}


function newssnippitmain($string) {

	
$sql = mysql_query("SELECT * FROM ".PREFIX."news_limit");
$row = mysql_fetch_object($sql);

// Define the number of results per allclientspages
$max_results = $row->snippetlimit;
	
	$result = mysql_query("SELECT SUBSTRING(newsTitle,1,35) as newsTitle, newsSlug, newsImg, newsDate FROM ".PREFIX."news WHERE newsID ORDER BY newsID DESC LIMIT $max_results ")or die(mysql_error());
	$newSnippitOutput.="<ul class=\"single-col\">\n";
	while ($r = mysql_fetch_object($result))
	{
		  $wi = '60';
		  $hi = '40';
		  $img =  $r->newsImg;//'assets/templates/images/minipostthumb.jpg';
		  $img = str_replace("../../../..","",$img);
		  $str = preg_replace('#<p>.+?src=[\'"]([^\'"]+)[\'"].+</p>#i', "$1", $img);
		  
		  $date = date('l jS \of F Y', strtotime($r->newsDate)); 
		  
		  if($r->newsImg !=''){
			  $newSnippitOutput.="<li>
			  <div class=\"tiny-thumb\"><a title=\"$r->newsTitle\" href=\"".DIR."$r->newsSlug\"><img alt=\"$r->postTitle\" src=\"img.php?src=$str&w=$wi&h=$hi&zc=0\"></a></div>
			  <div class=\"block\"><a title=\"$r->newsTitle\" href=\"".DIR."$r->newsSlug\" class=\"bold\">$r->newsTitle</a><small>Posted on $date</small></div></li>";
		  } else {
			  $newSnippitOutput.="<li>
			  <div class=\"tiny-thumb\"><a title=\"$r->newsTitle\" href=\"".DIR."$r->newsSlug\"><img alt=\"$r->newsTitle\" src=\"img.php?src=assets/templates/images/minipostthumb.jpg&w=$wi&zc=0\"></a></div>
			  <div class=\"block\"><a title=\"$r->newsTitle\" href=\"".DIR."$r->newsSlug\" class=\"bold\">$r->newsTitle</a><small>Posted on $date</small></div></li>";
		  }  
		
		
	}
	$newSnippitOutput.="</ul>";
	$newSnippitOutput.="<p style=\"text-align:right; font-size:14px; margin-bottom:0px;\"><a href=\"".DIR."news\">See All News</a></p>";
	
	$string = str_replace("[newssnippit]", $newSnippitOutput, $string);
	return $string;
}

function addLinksnews() {
	echo "bk<div class=\"icon\">\n";
		echo "<a href=\"".DIR."admin/manage-add-ons/news\"><img src=\"".DIR."assets/plugins/news/news.png\" alt=\"News\" title=\"Manage News\" class=\"tooltip-top\" /></a>\n";
    	echo "<p><a href=\"".DIR."admin/manage-add-ons/news\" title=\"Manage News\" class=\"tooltip-top\">News</a></p>\n";
    echo "</div>\n";	
}


//add hook, where to execute a function
add_hook('admin_modules','addLinksnews');
add_hook('cont','news');
add_hook('cont','newssnippitmain');
add_hook('del', 'delnews');
add_hook('page_requester','newsRequest');
?>
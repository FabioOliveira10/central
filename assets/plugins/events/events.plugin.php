<?php
/* hooks

above_doctype - code above doctype
header_css - code for including css files
header_js_script - code for including js files
header_js_jquery - code for jquery
header_slim_editor - code for stripped down editor
main - code for modules in main content
page_requester - code to request addtitional pages
js_popup - confirm deletion
del - delete section
admin_modules  - add link to manage modules

*/
global $curpage;

$cfile = ".htaccess";
$fo = fopen($cfile, 'r');
//get file contents and work out the file content size in bytes
$data = fread($fo, filesize($cfile));
//close the file
fclose($fo);

if (preg_match('/events/', $data))
{
} else { 

$newData = "
RewriteRule ^admin/manage-add-ons/events$ 			                admin.php?events=$1 [L]
RewriteRule ^admin/manage-add-ons/events/$			                admin.php?events=$1 [L]

RewriteRule ^admin/manage-add-ons/events/add-events-cat$ 			    admin.php?add-events-cat=$1 [L]
RewriteRule ^admin/manage-add-ons/events/add-events-cat/$			    admin.php?add-events-cat=$1 [L]

RewriteRule ^admin/manage-add-ons/events/edit-events-cat-([^/]+)$ 		admin.php?edit-events-cat=$1 [L]
RewriteRule ^admin/manage-add-ons/events/edit-events-cat-([^/]+)/$		admin.php?edit-events-cat=$1 [L]

RewriteRule ^admin/manage-add-ons/events/posts-([^/]+)$ 		    admin.php?posts=$1 [L]
RewriteRule ^admin/manage-add-ons/events/posts-([^/]+)/$  	    admin.php?posts=$1 [L]

RewriteRule ^admin/manage-add-ons/events/posts/add-post-([^/]+)$   admin.php?add-post=$1 [L]
RewriteRule ^admin/manage-add-ons/events/posts/add-post-([^/]+)/$  admin.php?add-post=$1 [L]

RewriteRule ^admin/manage-add-ons/events/posts/edit-post-([^/]+)$  admin.php?edit-post=$1 [L]
RewriteRule ^admin/manage-add-ons/events/posts/edit-post-([^/]+)/$ admin.php?edit-post=$1 [L]

RewriteRule ^c-([^/]+)$                  	        			 index.php?eventscat=$1 [L]
RewriteRule ^c-([^/]+)/$                 						 index.php?eventscat=$1 [L]

RewriteRule ^p-([^/]+)$                  	        			 index.php?eventspost=$1 [L]
RewriteRule ^p-([^/]+)/$                 						 index.php?eventspost=$1 [L]

RewriteRule ^pa-([^/]+)$                  	        			 index.php?eventspage=$1 [L]
RewriteRule ^pa-([^/]+)/$                 						 index.php?eventspage=$1 [L]###\n";

$data = str_replace("###",$newData,$data);

$myFile = $cfile;
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $data);
fclose($fh);


mysql_query("CREATE TABLE IF NOT EXISTS `".PREFIX."events_cats` (
  `catID` int(11) NOT NULL auto_increment,
  `catTitle` varchar(255) NOT NULL,
  `catSlug` varchar(255) NOT NULL,
  `sidebars` varchar(255) NOT NULL,
  PRIMARY KEY  (`catID`)
) ENGINE=MyISAM");

mysql_query("CREATE TABLE IF NOT EXISTS `".PREFIX."events_posts` (
  `postID` int(11) NOT NULL auto_increment,
  `postTitle` varchar(255) NOT NULL,
  `postSlug` varchar(255) NOT NULL,
  `postMetaKeywords` text NOT NULL,
  `postMetaDescription` text NOT NULL,
  `postDesc` text NOT NULL,
  `postCont` text NOT NULL,
  `postDate` date NOT NULL,
  `catID` int(11) NOT NULL,
  `postImg` int(11) NOT NULL,
  `postViews` int(11) NOT NULL,
  `memberID` int(11) NOT NULL,  
  PRIMARY KEY  (`postID`)
) ENGINE=MyISAM");

mysql_query("CREATE TABLE IF NOT EXISTS `".PREFIX."events_limit` (
  `postlimit` int(11) NOT NULL default '10',
  `postsnippetlimit` int(11) NOT NULL default '5',
  `thumbWidth` int(3) NOT NULL default '100',
  `thumbHeight` int(3) NOT NULL default '100',
   PRIMARY KEY  (`postlimit`)
) ENGINE=MyISAM");

mysql_query("INSERT INTO `".PREFIX."events_limit` (`postsnippetlimit`) VALUES
( '10')");

}







function manageevents()
{
	$curpage = true;	
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Events</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add Ons</a> > Events</p></div>";

echo messages();

echo "<p>To implement the events insert [events] into any page you want the events to be displayed.</p>\n";

if(isset($_POST['postsub']))
{
$lim = $_POST['postlimit'];
$postsnippetlimit = $_POST['postsnippetlimit'];
$lim = mysql_real_escape_string($lim);;

$sql = mysql_query("UPDATE ".PREFIX."events_limit SET postlimit='$lim', postsnippetlimit='$postsnippetlimit'")or die(mysql_error());
$_SESSION['success'] = 'Updated';
  url('manage-add-ons/events');
}

$sql = mysql_query("SELECT * FROM ".PREFIX."events_limit")or die(mysql_error());
$limit = mysql_fetch_object($sql);


?>

<form action="" method="post">
<p><label>Number per page</label><input name="postlimit" type="text" class="box-small tooltip-right" title="Enter number of items to be shown per page" value="<?php echo $limit->postlimit;?>" size="3" /></p>

<p><label>Limit Snippit</label><input name="postsnippetlimit" type="text" class="box-small tooltip-right" title="Enter number of items to be shown for sidebar snippits" value="<?php echo $limit->postsnippetlimit;?>" size="3" /></p>

<p><input type="submit" class="button tooltip-right" title="Save Changes" name="postsub" value="submit" /></p>
</form>

<?php

$result = mysql_query("SELECT * FROM ".PREFIX."events_cats")or die(mysql_error());
$Rows = mysql_num_rows($result);
?>
<table class="stripeMe">
<tr align="center">
<td width="42%" align="left"><strong>Categories</strong></td>
<td width="45%"><strong>Action</strong></td>
</tr>
<?php
while ($row = mysql_fetch_object($result)) {
?>
<tr>
<td><a href="<?php echo DIRADMIN;?>manage-add-ons/events/posts-<?php echo $row->catID;?>" class="tooltip-top" title="Manage Posts in this category"><?php echo $row->catTitle;?></a></td>
<td align="center" valign="top"><a href="<?php echo DIRADMIN;?>manage-add-ons/events/edit-events-cat-<?php echo $row->catID;?>" class="tooltip-top" title="Edit"><img src="<?php echo DIR;?>assets/images/icons/action-edit.png" alt="Edit" /></a> <a href="#" id="<?php echo $row->catID;?>" rel="deleventscat" title="<?php echo $row->catTitle;?>" class="delete_button"><img src="<?php echo DIR;?>assets/images/icons/action-del.png" alt="Delete" /></a></td>
</tr>
<?php
}
?>
</table>
<p><a href="<?php echo DIRADMIN;?>manage-add-ons/events/add-events-cat" class="button tooltip-right" title="Add events category">Add Category</a></p>

</div>
<?php
} else {
url(DIRADMIN);

}
}


function addcat()
{
	$curpage = true;
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Add Cat</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/events\">events</a> > Add Category</p></div>";

echo messages();

if(isset($_POST['cancel'])){
$_SESSION['info'] = "events category was not created";
url('manage-add-ons/events');
}

// if form submitted then process form
if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$catTitle = trim($_POST['catTitle']);
if (strlen($catTitle) < 1 || strlen(catTitle) > 255) {
$error[] = 'Title must be at between 1 and 255 charactors.';
}

// if valadation is okay then carry on
if (!$error) {
	
	// post form data 
   $catTitle     = $_POST['catTitle'];
   
   //strip any tags from input
   $catTitle   = safestrip($catTitle);   
   
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$catTitle   = addslashes($catTitle);
	 }
	 
	   $catSlug  = strtolower(str_replace(" ", '-', $catTitle));
	   $catSlug  = strtolower(str_replace("/", '', $catSlug));
	   $catSlug  = strtolower(str_replace("?", '', $catSlug));
	   $catSlug  = strtolower(str_replace("&", 'and', $catSlug));
	   $catSlug  = strtolower(str_replace("!", '', $catSlug));
	   $catSlug  = strtolower(str_replace(".", '', $catSlug));
	   $catSlug  = strtolower(str_replace(",", '', $catSlug));
	   $catSlug  = strtolower(str_replace("@", '', $catSlug));
	   $catSlug  = strtolower(str_replace("_", '', $catSlug));
	   $catSlug  = strtolower(str_replace("--", '-', $catSlug));
	   $catSlug  = strtolower(str_replace("'", '', $catSlug));
	   
	    $catSlug = stripslashes($catSlug);
		
		if(isset($_POST['sidebar'])){
		foreach($_POST['sidebar'] as $side)
		{
		$sides.=$side.',';						
		}}
   
 
// insert data into images table
$query = "INSERT INTO ".PREFIX."events_cats (catTitle,catSlug,sidebars) VALUES ('$catTitle','$catSlug','$sides')";
$result  = mysql_query($query) or die ('album'.mysql_error());
$getID = mysql_insert_id();

pingSE(DIR."sitemap.php","bing");
pingSE(DIR."sitemap.php","ask");
pingSE(DIR."sitemap.php","google");

// show a message to confirm results	
if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'events Category Added';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'events Category Added';
url('manage-add-ons/events');	
}		
 
}
}

	
//dispaly any errors
errors($error);

?>

<form enctype="multipart/form-data" action="" method="post">
<p><label>Title:</label><input type="text" class="box-medium tooltip-right" title="Enter the category title" name="catTitle" <?php if (isset($error)){ echo "value=\"$catTitle\""; }?>/></p>

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

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save category and return to events">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save category and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save category and return to events">
</form>
</div>
<?php

} else {
url(DIRADMIN);
}		
}

function editcat()
{
	$curpage = true;	
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Edit Cat</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/events\">events</a> > Edit Category</p></div>";

echo messages();

if(isset($_POST['cancel'])){
$_SESSION['info'] = "events category was not created";
url('manage-add-ons/events');
}

// if form submitted then process form
if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$catTitle = trim($_POST['catTitle']);
if (strlen($catTitle) < 1 || strlen($catTitle) > 255) {
$error[] = 'Title must be at between 1 and 255 charactors.';
}

// if valadation is okay then carry on
if (!$error) {
	
	// post form data 
   $catID        = $_POST['catID'];
   $catTitle     = $_POST['catTitle'];
   
   //strip any tags from input
   $catTitle   = safestrip($catTitle);   
   
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$catTitle   = addslashes($catTitle);
	 }
 
  	   $catSlug  = strtolower(str_replace(" ", '-', $catTitle));
	   $catSlug  = strtolower(str_replace("/", '', $catSlug));
	   $catSlug  = strtolower(str_replace("?", '', $catSlug));
	   $catSlug  = strtolower(str_replace("&", 'and', $catSlug));
	   $catSlug  = strtolower(str_replace("!", '', $catSlug));
	   $catSlug  = strtolower(str_replace(".", '', $catSlug));
	   $catSlug  = strtolower(str_replace(",", '', $catSlug));
	   $catSlug  = strtolower(str_replace("@", '', $catSlug));
	   $catSlug  = strtolower(str_replace("_", '', $catSlug));
	   $catSlug  = strtolower(str_replace("--", '-', $catSlug));
	   $catSlug  = strtolower(str_replace("'", '', $catSlug));
	   
	    $catSlug = stripslashes($catSlug);
		
		foreach($_POST['sidebar'] as $side)
		{
		$sides.=$side.',';						
		} 
		
		//die($sides);

// insert data into images table
$query = "UPDATE ".PREFIX."events_cats SET catTitle = '$catTitle', catSlug='$catSlug', sidebars='$sides' WHERE catID='$catID'";
$result  = mysql_query($query) or die (mysql_error());

pingSE(DIR."sitemap.php","bing");
pingSE(DIR."sitemap.php","ask");
pingSE(DIR."sitemap.php","google");

// show a message to confirm results	
if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'events Category Updated';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'events Category Updated';
url('manage-add-ons/events');	
}		
 
}
}

	
//dispaly any errors
errors($error);

$sql = mysql_query("SELECT * FROM ".PREFIX."events_cats WHERE catID='{$_GET['edit-events-cat']}'");
while($row = mysql_fetch_object($sql)){
?>

<form action="" method="post">
<input type="hidden" name="catID" value="<?=$row->catID;?>" />
<p><label>Title:</label><input type="text" class="box-medium tooltip-right" title="Enter the category title" name="catTitle" value="<?php echo $row->catTitle;?>"/></p>

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

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save category and return to events">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save category and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save category and return to events">
</form>	
</div>
<?php }

} else {
url(DIRADMIN);

}	
}

function manageposts()
{
	$curpage = true;;
	if (isglobaladmin() || isadmin()){
		
$sql = mysql_query("SELECT * FROM ".PREFIX."events_cats WHERE catID='{$_GET['eventposts']}'")or die(mysql_error());
$row = mysql_fetch_object($sql);
$catTitle = $row->catTitle;
$catID = $row->catID;

echo "<div class=\"content-box-header\"><h3>$catTitle</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/events\">events</a> > <a href=\"".DIRADMIN."manage-add-ons/events/posts-$catID\">$catTitle</a></p></div>";

$result = mysql_query("SELECT * FROM ".PREFIX."events_posts WHERE catID='{$_GET['eventposts']}' ORDER BY postDate")or die(mysql_error());

echo messages();
?>
<table>
<tr align="center">
<td width="42%" align="left"><strong>Posts</strong></td>
<td width="42%" align="left"><strong>Date</strong></td>
<td width="45%"><strong>Action</strong></td>
</tr>
<?php
while ($row = mysql_fetch_object($result)) {
?>
<tr>
<td><?php echo $row->postTitle;?></td>
<td><?php echo $row->postDate;?></td>
<td align="center" valign="top">
<a href="<?php echo DIRADMIN;?>manage-add-ons/events/posts/edit-post-<?php echo $row->postID;?>" class="tooltip-top" title="Edit"><img src="<?php echo DIR;?>assets/images/icons/action-edit.png" alt="Edit" /></a> <a href="#" id="<?php echo $row->postID;?>" rel="deleventspost" title="<?php echo $row->postTitle;?>" class="delete_button"><img src="<?php echo DIR;?>assets/images/icons/action-del.png" alt="Delete" /></a>
<?php
}
?>
</table>
<p><a href="<?php echo DIRADMIN;?>manage-add-ons/events/posts/add-post-<?php echo $catID;?>" class="button tooltip-top" title="Add Post">Add Post</a></p>
</div>
<?php
} else {
url(DIRADMIN);

}		
}


function addpost()
{
	$curpage = true;	
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Add Post</h3></div> 			
<div class=\"content-box-content\">";

$sql = mysql_query("SELECT * FROM ".PREFIX."events_cats WHERE catID='{$_GET['add-eventpost']}'")or die(mysql_error());
$row = mysql_fetch_object($sql);
$catTitle = $row->catTitle;
$catID = $row->catID;

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/events\">events</a> > <a href=\"".DIRADMIN."manage-add-ons/events/posts-$catID\">$catTitle</a> > Add Post</p></div>";

$catID = $_GET['add-post'];

echo messages();

if(isset($_POST['cancel'])){
$catID  = $_POST['catID'];
$_SESSION['info'] = "events category was not created";
url('manage-add-ons/events/posts-'.$catID);
}

// if form submitted then process form
if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$postTitle = trim($_POST['postTitle']);
if (strlen($postTitle) < 1 || strlen($postTitle) > 255) {
$error[] = 'Title must be at between 1 and 255 charactors.';
}

// if valadation is okay then carry on
if (!$error) {
	
	// post form data 
   $postTitle     = $_POST['postTitle'];
   $postURL       = $_POST['postURL'];
   $postDate      = $_POST['postDate'];
   $catID         = $_POST['catID'];
      
   //strip any tags from input
   $postTitle   = safestrip($postTitle); 
   //$bpostDesc   = safe($bpostDesc); 
   //$postCont   = safe($postCont);   
   
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$postTitle   = addslashes($postTitle);
	 }
   

// insert data into images table
$query = "INSERT INTO ".PREFIX."events_posts (postTitle, postURL,postDate, catID, memberID) VALUES
  ('$postTitle', '$postURL', '$postDate', '$catID', '".get_memberID()."')";
  $result  = mysql_query($query) or die ('Cannot add because: '. mysql_error());

//$msg = "New Post: ".strtolower(ucwords($postTitle))." - ".DIR."p-$postSlug";
//twitter ($msg);

pingSE(DIR."sitemap.php","bing");
pingSE(DIR."sitemap.php","ask");
pingSE(DIR."sitemap.php","google");
pingSE(DIR."sitemap.php","moreover");

if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'events Post Added';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'events Post Added';
url('manage-add-ons/events/posts-'.$catID);
} 
 
}
}

	
//dispaly any errors
echo errors($error);

?>

<form action="" method="post">
<input type="hidden" name="catID" value="<?php echo $_GET['add-eventpost'];?>" />
<p><label>Title:</label> <input name="postTitle" type="text" class="box-medium tooltip-right" title="Enter the title" size="30" <?php if (isset($error)){ echo "value=\"$postTitle\""; }?>/></p>

<p><label>URL:</label> <input name="postURL" type="text" class="box-medium tooltip-right" title="Enter the event address from the calendar" size="30" <?php if (isset($error)){ echo "value=\"$postURL\""; }?>/></p>

<p><label>Date:</label> <input name="postDate" type="text" class="box-medium tooltip-right datepicker" title="Enter the date of the event" value="<?php echo $postDate;?>" size="30"/> (Used for ordering the events by date.)</p>

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save post and return to events">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save post and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save post and return to events">
</form>
</div>
<?php

} else {
url(DIRADMIN);

}	
}

function edipost()
{
	$curpage = true;
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Edit Post</h3></div> 			
<div class=\"content-box-content\">";


$sql = mysql_query("SELECT * FROM ".PREFIX."events_posts WHERE postID='{$_GET['edit-eventpost']}'")or die(mysql_error());
$row = mysql_fetch_object($sql);


$sql = mysql_query("SELECT * FROM ".PREFIX."events_cats WHERE catID='$row->catID'")or die(mysql_error());
$row = mysql_fetch_object($sql);
$catTitle = $row->catTitle;
$catID = $row->catID;

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/events\">events</a> > <a href=\"".DIRADMIN."manage-add-ons/events/posts-$catID\">$catTitle</a> > Edit Post</p></div>";


echo messages();

if(isset($_POST['cancel'])){
$_SESSION['info'] = "events category was not created";
url('manage-add-ons/events/posts-'.$catID);
}

// if form submitted then process form
if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$postTitle = trim($_POST['postTitle']);
if (strlen($postTitle) < 1 || strlen($postTitle) > 255) {
$error[] = 'Title must be at between 1 and 255 charactors.';
}

$catID = $_POST['catID'];
if ($catID == ''){
$error[] = 'Please select a section';
}

// if valadation is okay then carry on
if (!$error) {
	
	// post form data 
   $postID        = $_POST['postID'];
   $postTitle     = $_POST['postTitle'];
   $postURL      = $_POST['postURL'];
   $catID         = $_POST['catID'];
   $postDate      = $_POST['postDate'];
   
   //strip any tags from input
   $postTitle   = safestrip($postTitle); 
   //$postDesc   = safe($postDesc); 
   //$postCont   = safe($postCont);   
   
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$postTitle   = addslashes($postTitle);
	 }
		$postTitle = stripslashes($postTitle); 

  

// insert data into images table
$query = "UPDATE ".PREFIX."events_posts SET postTitle = '$postTitle', postURL = '$postURL', catID = '$catID', postDate='$postDate' WHERE postID='$postID'";
$result  = mysql_query($query) or die ('Cannot Update because: '. mysql_error());
 	
	
pingSE(DIR."sitemap.php","bing");
pingSE(DIR."sitemap.php","ask");
pingSE(DIR."sitemap.php","google");
pingSE(DIR."sitemap.php","moreover");	

if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'events Post Updated';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'events Post Updated';
url('manage-add-ons/events/posts-'.$catID);
}  
 
}
}

	
//dispaly any errors
errors($error);
$result = mysql_query("SELECT * FROM ".PREFIX."events_posts WHERE postID='{$_GET['edit-eventpost']}'");
while($row = mysql_fetch_object($result)){
?>

<form action="" method="post">
<input type="hidden" name="postID" value="<?=$row->postID;?>" />

<p><label>Title:</label> <input name="postTitle" type="text" class="box-medium tooltip-right" title="Enter the title" value="<?php echo $row->postTitle;?>" size="30"/></p>

<p><label>URL:</label> <input name="postURL" type="text" class="box-medium tooltip-right" title="Enter the event address from the calendar" size="30" <?php echo "value=\"$row->postURL \"";?>/></p>

<p><label>Date:</label> <input name="postDate" type="text" class="box-medium tooltip-right datepicker" title="Enter the date of the event" value="<?php echo $row->postDate;?>" size="30"/> (Used for ordering the events by date.)</p>


<p><label>Select Section:</label>
 <?php
$cat = $row->catID; 
$result2 = mysql_query("SELECT * FROM ".PREFIX."events_cats")or die(mysql_error());
echo "<select name='catID'>\n";
echo "<option value=''>Please select a section</option>\n";
while ($row2 = mysql_fetch_object($result2)) { 
	echo "<option value='$row2->catID'";	
	if ($_POST['catID'] == $row2->catID){
	echo "selected='selected'";
	}elseif($cat == $row2->catID){
	echo "selected='selected'";		
	}
	echo ">$row2->catTitle</option>\n"; 
}
	echo "</select>\n";
?> </p>

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save post and return to events">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save post and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save post and return to events">
</form>	
<?php }
echo "</div>";
} else {
url(DIRADMIN);

}	
}


function eventsRequest()
{
	if(isset($_GET['events'])){
	manageevents();
	$curpage = true;
	}
	
	if(isset($_GET['add-events-cat'])){
	addcat();
	$curpage = true;
	}
	
	if(isset($_GET['edit-events-cat'])){
	editcat();
	$curpage = true;
	}
	
	if(isset($_GET['eventposts'])){
	manageposts();
	$curpage = true;
	}
	
	
	if(isset($_GET['add-eventpost'])){
	addpost();
	$curpage = true;
	}
	
	if(isset($_GET['edit-eventpost'])){
	edipost();
	$curpage = true;
	}
	
	if(isset($_GET['deleventscat'])){
	pdeleventscat();
	$curpage = true;
	}
	
	if(isset($_GET['deleventspost'])){
	pdeleventspost();
	$curpage = true;
	}

}



function jsdeleventscomment() {
	return "\nfunction deleventscomment(ID, Title)
{
   if (confirm(\"Are you sure you want to delete '\" + Title + \"'\"))
   {
      window.location.href = '".DIR."index.php?deleventscomment=' + ID;
   }
}\n";
}


function deleventscomment() {
if(isset($_GET['deleventscomment']))
{
  $query = "DELETE FROM ".PREFIX."events_comments WHERE commentID = '{$_GET['deleventscomment']}'";
  mysql_query($query) or die('Error : ' . mysql_error());
  header('Location: ' . $_SERVER['HTTP_REFERER']);
}

}

function deleventscat() {

$delType = $_GET['delType'];
$delID   = $_GET['delID'];
$delID = mysql_real_escape_string($delID);

if($delType == 'deleventscat')
{

    $query = "SELECT * FROM ".PREFIX."events_posts  WHERE catID = '$delID'";
	$result = mysql_query($query) or die('problem: ' . mysql_error());
	while ( $row = mysql_fetch_array ($result)) {

	$query = "DELETE FROM ".PREFIX."events_posts WHERE catID = '$row->catID'";
  	mysql_query($query) or die('Error : ' . mysql_error());
	}
	
	$query = "DELETE FROM ".PREFIX."events_cats WHERE catID = '$delID'";
  	mysql_query($query) or die('Error : ' . mysql_error());

   	$_SESSION['success'] = 'Cat Deleted';
	url('manage-add-ons/events');
}  	
}


function deleventspost() {

$delType = $_GET['delType'];
$delID   = $_GET['delID'];
$delID = mysql_real_escape_string($delID);

if($delType == 'deleventspost')
{
    $query = "SELECT * FROM ".PREFIX."events_posts WHERE postID = '$delID'";
	$result = mysql_query($query) or die('problem: ' . mysql_error());
	$row = mysql_fetch_object ($result);

    $query = "DELETE FROM ".PREFIX."events_posts WHERE postID = '$delID'";
    mysql_query($query) or die('Error : ' . mysql_error());
  
    $_SESSION['success'] = 'Post Deleted';
    url('manage-add-ons/events/posts-'.$row->catID);
}
}


function eventsposts($string) {

	
$sql = mysql_query("SELECT * FROM ".PREFIX."events_limit");
$row = mysql_fetch_object($sql);

// Define the number of results per allclientspages
$max_results = $row->postsnippetlimit;

	$now = date('Y-m-d'); 	
	$result = mysql_query("SELECT postTitle, postURL, postDate FROM ".PREFIX."events_posts WHERE postDate >= '$now' ORDER BY postDate LIMIT $max_results ")or die(mysql_error());

	while ($r = mysql_fetch_object($result))
	{		
		$m = date('M', strtotime($r->postDate));
		$d = date('d', strtotime($r->postDate));
		$newSnippitOutput.="
		
		<div style=\"clear: both; height: 40px; padding-top: 5px;\">
			<div style=\"background:#FFFFFF; border-radius: 5px 5px 5px 5px; color: #000000; float: left; padding: 5px; text-align: center; text-transform: uppercase; width: 30px; font-weight:bold;\">
				$m <br />$d
			</div>
			<div style=\"float:left; font-weight: bold; margin-left: 10px; width: 160px;\">
				<a title=\"$r->postTitle\" href=\"$r->postURL\" target=\"_blank\"> $r->postTitle</a>
			</div>
		</div>";		
	}
	
	if(mysql_num_rows($result) == 0){ $newSnippitOutput.="No upcoming events."; }
	
	$string = str_replace("[eventsposts]", $newSnippitOutput, $string);
	return $string;
}


function addLinksevents() {
	echo "bk<div class=\"icon\">\n";
		echo "<a href=\"".DIR."admin/manage-add-ons/events\"><img src=\"".DIR."assets/plugins/events/events.png\" alt=\"events\" title=\"Manage events\" class=\"tooltip-top\" /></a>\n";
    	echo "<p><a href=\"".DIR."admin/manage-add-ons/events\" title=\"Manage events\" class=\"tooltip-top\">Events</a></p>\n";
    echo "</div>\n";	
}


//add hook, where to execute a function
add_hook('admin_modules','addLinksevents');
add_hook('cont','events');
add_hook('cont','eventsposts');
add_hook('js_inner_popup', 'jsdeleventscomment');
add_hook('del', 'deleventspost');
add_hook('del', 'deleventscat');
add_hook('del_inner', 'deleventscomment');
add_hook('page_requester','eventsRequest');
?>
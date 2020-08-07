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

if (preg_match('/blog/', $data))
{
} else { 

$newData = "
RewriteRule ^admin/manage-add-ons/blog$ 			                admin.php?blog=$1 [L]
RewriteRule ^admin/manage-add-ons/blog/$			                admin.php?blog=$1 [L]

RewriteRule ^admin/manage-add-ons/blog/add-blog-cat$ 			    admin.php?add-blog-cat=$1 [L]
RewriteRule ^admin/manage-add-ons/blog/add-blog-cat/$			    admin.php?add-blog-cat=$1 [L]

RewriteRule ^admin/manage-add-ons/blog/edit-blog-cat-([^/]+)$ 		admin.php?edit-blog-cat=$1 [L]
RewriteRule ^admin/manage-add-ons/blog/edit-blog-cat-([^/]+)/$		admin.php?edit-blog-cat=$1 [L]

RewriteRule ^admin/manage-add-ons/blog/posts-([^/]+)$ 		    admin.php?posts=$1 [L]
RewriteRule ^admin/manage-add-ons/blog/posts-([^/]+)/$  	    admin.php?posts=$1 [L]

RewriteRule ^admin/manage-add-ons/blog/posts/add-post-([^/]+)$   admin.php?add-post=$1 [L]
RewriteRule ^admin/manage-add-ons/blog/posts/add-post-([^/]+)/$  admin.php?add-post=$1 [L]

RewriteRule ^admin/manage-add-ons/blog/posts/edit-post-([^/]+)$  admin.php?edit-post=$1 [L]
RewriteRule ^admin/manage-add-ons/blog/posts/edit-post-([^/]+)/$ admin.php?edit-post=$1 [L]

RewriteRule ^c-([^/]+)$                  	        			 index.php?blogcat=$1 [L]
RewriteRule ^c-([^/]+)/$                 						 index.php?blogcat=$1 [L]

RewriteRule ^p-([^/]+)$                  	        			 index.php?blogpost=$1 [L]
RewriteRule ^p-([^/]+)/$                 						 index.php?blogpost=$1 [L]

RewriteRule ^pa-([^/]+)$                  	        			 index.php?blogpage=$1 [L]
RewriteRule ^pa-([^/]+)/$                 						 index.php?blogpage=$1 [L]\n###\n";

$data = str_replace("###",$newData,$data);

$myFile = $cfile;
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $data);
fclose($fh);


mysql_query("CREATE TABLE IF NOT EXISTS `".PREFIX."blog_cats` (
  `catID` int(11) NOT NULL auto_increment,
  `catTitle` varchar(255) NOT NULL,
  `catSlug` varchar(255) NOT NULL,
  PRIMARY KEY  (`catID`)
) ENGINE=MyISAM");

mysql_query("CREATE TABLE IF NOT EXISTS `".PREFIX."blog_posts` (
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

mysql_query("CREATE TABLE IF NOT EXISTS `".PREFIX."blog_limit` (
  `postlimit` int(11) NOT NULL default '10',
  `postsnippetlimit` int(11) NOT NULL default '5',
  `thumbWidth` int(3) NOT NULL default '100',
  `thumbHeight` int(3) NOT NULL default '100',
   PRIMARY KEY  (`postlimit`)
) ENGINE=MyISAM");

mysql_query("INSERT INTO `".PREFIX."blog_limit` (`newslimit`) VALUES
( '10')");

}

function manageblog()
{
	$curpage = true;	
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Blog</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add Ons</a> > Blog</p></div>";

echo messages();

echo "<p>To implement the blog insert [blog] into any page you want the blog to be displayed.</p>\n";

if(isset($_POST['postsub']))
{
$lim = $_POST['postlimit'];
$postsnippetlimit = $_POST['postsnippetlimit'];
$thumbWidth = $_POST['thumbWidth'];
$thumbHeight = $_POST['thumbHeight'];

$lim = mysql_real_escape_string($lim);
$thumbWidth = mysql_real_escape_string($thumbWidth);
$thumbHeight = mysql_real_escape_string($thumbHeight);

$sql = mysql_query("UPDATE ".PREFIX."blog_limit SET postlimit='$lim', postsnippetlimit='$postsnippetlimit', thumbWidth='$thumbWidth', thumbHeight='$thumbHeight'")or die(mysql_error());
$_SESSION['success'] = 'Updated';
  url('manage-add-ons/blog');
}

$sql = mysql_query("SELECT * FROM ".PREFIX."blog_limit")or die(mysql_error());
$limit = mysql_fetch_object($sql);


?>

<form action="" method="post">
<p><label>Number per page</label><input name="postlimit" type="text" class="box-small tooltip-right" title="Enter number of items to be shown per page" value="<?php echo $limit->postlimit;?>" size="3" /></p>

<p><label>Limit Snippit</label><input name="postsnippetlimit" type="text" class="box-small tooltip-right" title="Enter number of items to be shown for sidebar snippits" value="<?php echo $limit->postsnippetlimit;?>" size="3" /></p>

<p><label>Thumbnail Width</label><input name="thumbWidth" type="text" class="box-small tooltip-right" title="Set the width of thumbnails in pixels" value="<?php echo $limit->thumbWidth;?>" size="3" /></p>

<p><label>Thumbnail Height</label><input name="thumbHeight" type="text" class="box-small tooltip-right" title="Set the height of thumbnails in pixels" value="<?php echo $limit->thumbHeight;?>" size="3" /></p>

<p><input type="submit" class="button tooltip-right" title="Save Changes" name="postsub" value="submit" /></p>
</form>

<?php

$result = mysql_query("SELECT * FROM ".PREFIX."blog_cats")or die(mysql_error());
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
<td><a href="<?php echo DIRADMIN;?>manage-add-ons/blog/posts-<?php echo $row->catID;?>" class="tooltip-top" title="Manage Posts in this category"><?php echo $row->catTitle;?></a></td>
<td align="center" valign="top"><a href="<?php echo DIRADMIN;?>manage-add-ons/blog/edit-blog-cat-<?php echo $row->catID;?>" class="tooltip-top" title="Edit"><img src="<?php echo DIR;?>assets/images/icons/action-edit.png" alt="Edit" /></a> <a href="#" id="<?php echo $row->catID;?>" rel="delblogcat" title="<?php echo $row->catTitle;?>" class="delete_button"><img src="<?php echo DIR;?>assets/images/icons/action-del.png" alt="Delete" /></a></td>
</tr>
<?php
}
?>
</table>
<p><a href="<?php echo DIRADMIN;?>manage-add-ons/blog/add-blog-cat" class="button tooltip-right" title="Add Blog category">Add Category</a></p>

</div>
<?php
} else {
url(DIRADMIN);

}
}


function blogaddcat()
{
	$curpage = true;
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Add Cat</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/blog\">Blog</a> > Add Category</p></div>";

echo messages();

if(isset($_POST['cancel'])){
$_SESSION['info'] = "Blog category was not created";
url('manage-add-ons/blog');
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
$query = "INSERT INTO ".PREFIX."blog_cats (catTitle,catSlug,sidebars) VALUES ('$catTitle','$catSlug','$sides')";
$result  = mysql_query($query) or die ('245 - '.mysql_error());
$getID = mysql_insert_id();

pingSE(DIR."sitemap.php","bing");
pingSE(DIR."sitemap.php","ask");
pingSE(DIR."sitemap.php","google");

// show a message to confirm results	
if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'Blog Category Added';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'Blog Category Added';
url('manage-add-ons/blog');	
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

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save category and return to blog">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save category and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save category and return to blog">
</form>
</div>
<?php

} else {
url(DIRADMIN);
}		
}

function blogeditcat()
{
	$curpage = true;	
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Edit Cat</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/blog\">Blog</a> > Edit Category</p></div>";

echo messages();

if(isset($_POST['cancel'])){
$_SESSION['info'] = "Blog category was not created";
url('manage-add-ons/blog');
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
$query = "UPDATE ".PREFIX."blog_cats SET catTitle = '$catTitle', catSlug='$catSlug', sidebars='$sides' WHERE catID='$catID'";
$result  = mysql_query($query) or die (mysql_error());

pingSE(DIR."sitemap.php","bing");
pingSE(DIR."sitemap.php","ask");
pingSE(DIR."sitemap.php","google");

// show a message to confirm results	
if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'Blog Category Updated';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'Blog Category Updated';
url('manage-add-ons/blog');	
}		
 
}
}

	
//dispaly any errors
errors($error);

$sql = mysql_query("SELECT * FROM ".PREFIX."blog_cats WHERE catID='{$_GET['edit-blog-cat']}'");
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

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save category and return to blog">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save category and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save category and return to blog">
</form>	
</div>
<?php }

} else {
url(DIRADMIN);

}	
}

function blogmanageposts()
{
	$curpage = true;;
	if (isglobaladmin() || isadmin()){
		
$sql = mysql_query("SELECT * FROM ".PREFIX."blog_cats WHERE catID='{$_GET['posts']}'")or die(mysql_error());
$row = mysql_fetch_object($sql);
$catTitle = $row->catTitle;
$catID = $row->catID;

echo "<div class=\"content-box-header\"><h3>$catTitle</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/blog\">Blog</a> > <a href=\"".DIRADMIN."manage-add-ons/blog/posts-$catID\">$catTitle</a></p></div>";

$result = mysql_query("SELECT * FROM ".PREFIX."blog_posts WHERE catID='{$_GET['posts']}' ORDER BY postID DESC")or die(mysql_error());

echo messages();
?>
<table>
<tr align="center">
<td width="42%" align="left"><strong>Posts</strong></td>
<td width="42%" align="left"><strong>Views</strong></td>
<td width="45%"><strong>Action</strong></td>
</tr>
<?php
while ($row = mysql_fetch_object($result)) {
?>
<tr>
<td><?php echo $row->postTitle;?></td>
<td><?php echo $row->postViews;?></td>
<td align="center" valign="top">
<a href="<?php echo DIRADMIN;?>manage-add-ons/blog/posts/edit-post-<?php echo $row->postID;?>" class="tooltip-top" title="Edit"><img src="<?php echo DIR;?>assets/images/icons/action-edit.png" alt="Edit" /></a> <a href="#" id="<?php echo $row->postID;?>" rel="delblogpost" title="<?php echo $row->postTitle;?>" class="delete_button"><img src="<?php echo DIR;?>assets/images/icons/action-del.png" alt="Delete" /></a>
<?php
}
?>
</table>
<p><a href="<?php echo DIRADMIN;?>manage-add-ons/blog/posts/add-post-<?php echo $catID;?>" class="button tooltip-top" title="Add Post">Add Post</a></p>
</div>
<?php
} else {
url(DIRADMIN);

}		
}


function blogaddpost()
{
	$curpage = true;	
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Add Post</h3></div> 			
<div class=\"content-box-content\">";

$sql = mysql_query("SELECT * FROM ".PREFIX."blog_cats WHERE catID='{$_GET['add-post']}'")or die(mysql_error());
$row = mysql_fetch_object($sql);
$catTitle = $row->catTitle;
$catID = $row->catID;

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/blog\">Blog</a> > <a href=\"".DIRADMIN."manage-add-ons/blog/posts-$catID\">$catTitle</a> > Add Post</p></div>";

$catID = $_GET['add-post'];

echo messages();

if(isset($_POST['cancel'])){
$_SESSION['info'] = "Blog category was not created";
url('manage-add-ons/blog/posts-'.$catID);
}

// if form submitted then process form
if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$postTitle = trim($_POST['postTitle']);
if (strlen($postTitle) < 1 || strlen($postTitle) > 255) {
$error[] = 'Title must be at between 1 and 255 charactors.';
}

$postDesc = trim($_POST['postDesc']);
if (strlen($postDesc) < 3 ) {
$error[] = 'Description Must be more then 3 characters.';
}


$postCont = trim($_POST['postCont']);
if (strlen($postCont) < 3 ) {
$error[] = 'Content Must be more then 3 characters.';
}

// if valadation is okay then carry on
if (!$error) {
	
	// post form data 
   $postTitle     = $_POST['postTitle'];
   $postMetaKeywords = $_POST['postMetaKeywords'];
   $postMetaDescription = $_POST['postMetaDescription'];
   $postDesc     = $_POST['postDesc'];
   $postCont      = $_POST['postCont'];
   $postImg       = $_POST['postImg'];
   
   //strip any tags from input
   $postTitle   = safestrip($postTitle); 
   //$bpostDesc   = safe($bpostDesc); 
   //$postCont   = safe($postCont);   
   
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$postTitle   = addslashes($postTitle);
	$postDesc   = addslashes($postDesc);
	$postCont   = addslashes($postCont);
	 }
	 
	   $postSlug  = strtolower(str_replace(" ", '-', $postTitle));
	   $postSlug  = strtolower(str_replace("/", '', $postSlug));
	   $postSlug  = strtolower(str_replace("?", '', $postSlug));
	   $postSlug  = strtolower(str_replace("&", 'and', $postSlug));
	   $postSlug  = strtolower(str_replace("!", '', $postSlug));
	   $postSlug  = strtolower(str_replace(".", '', $postSlug));
	   $postSlug  = strtolower(str_replace("£", '', $postSlug));
	   $postSlug  = strtolower(str_replace(",", '', $postSlug));
	   $postSlug  = strtolower(str_replace("@", '', $postSlug));
	   $postSlug  = strtolower(str_replace("_", '', $postSlug));
	   $postSlug  = strtolower(str_replace("--", '-', $postSlug));
	   $postSlug  = strtolower(str_replace("'", '', $postSlug));
	   
	    $postSlug = stripslashes($postSlug);
		
		if(isset($_POST['sidebar'])){
		foreach($_POST['sidebar'] as $side)
		{
		$sides.=$side.',';						
		}}
   

// insert data into images table
$query = "INSERT INTO ".PREFIX."blog_posts (postTitle, postSlug, postMetaKeywords, postMetaDescription, postDesc, postCont, postDate, postImg, catID, memberID, sidebars) VALUES
  ('$postTitle', '$postSlug', '$postMetaKeywords', '$postMetaDescription', '$postDesc','$postCont', NOW(), '$postImg', '$catID', '".get_memberID()."','$sides')";
  $result  = mysql_query($query) or die ('Cannot add image because: '. mysql_error());

$msg = "New Post: ".strtolower(ucwords($postTitle))." - ".DIR."p-$postSlug";
twitter ($msg);

pingSE(DIR."sitemap.php","bing");
pingSE(DIR."sitemap.php","ask");
pingSE(DIR."sitemap.php","google");
pingSE(DIR."sitemap.php","moreover");

if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'Blog Post Added';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'Blog Post Added';
url('manage-add-ons/blog/posts-'.$catID);
} 
 
}
}

	
//dispaly any errors
echo errors($error);

?>

<form action="" method="post">
<p><label>Title:</label> <input name="postTitle" type="text" class="box-medium tooltip-right" title="Enter the title" size="30" <?php if (isset($error)){ echo "value=\"$postTitle\""; }?>/></p>

<p><label>Meta Keywords</label><br /> <textarea class="ta-default tooltip-top" title="Place keywords here coma seperated, to help search engines find this page" name="postMetaKeywords" cols="60" rows="5"><?php if (isset($error)){ echo $row->postMetaKeywords;}?></textarea></p>

<p><label>Meta Description</label><br /> <textarea class="ta-default tooltip-top" title="Set the description that will be shown in search engines results" name="postMetaDescription" cols="60" rows="5"><?php if (isset($error)){ echo $row->postMetaDescription;}?></textarea></p>

<p><label>Description:</label><textarea name="postDesc" id="postDesc" cols="60" rows="20"><?php if (isset($error)){ echo $postDesc; }?></textarea>
</p>

<p><label>Content:</label><textarea name="postCont" id="postCont" cols="60" rows="20"><?php if (isset($error)){ echo $postCont; }?></textarea>
</p>

<p><label>Image:</label><textarea name="postImg" id="postImg" cols="60" rows="20"><?php if (isset($error)){ echo $postImg; }?></textarea>
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

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save post and return to blog">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save post and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save post and return to blog">
</form>
</div>
<?php

} else {
url(DIRADMIN);

}	
}

function blogeditpost()
{
	$curpage = true;
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Edit Post</h3></div> 			
<div class=\"content-box-content\">";


$sql = mysql_query("SELECT * FROM ".PREFIX."blog_posts WHERE postID='{$_GET['edit-post']}'")or die(mysql_error());
$row = mysql_fetch_object($sql);


$sql = mysql_query("SELECT * FROM ".PREFIX."blog_cats WHERE catID='$row->catID'")or die(mysql_error());
$row = mysql_fetch_object($sql);
$catTitle = $row->catTitle;
$catID = $row->catID;

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/blog\">Blog</a> > <a href=\"".DIRADMIN."manage-add-ons/blog/posts-$catID\">$catTitle</a> > Edit Post</p></div>";


echo messages();

if(isset($_POST['cancel'])){
$_SESSION['info'] = "Blog category was not created";
url('manage-add-ons/blog/posts-'.$catID);
}

// if form submitted then process form
if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$postTitle = trim($_POST['postTitle']);
if (strlen($postTitle) < 1 || strlen($postTitle) > 255) {
$error[] = 'Title must be at between 1 and 255 charactors.';
}

$postDesc = trim($_POST['postDesc']);
if (strlen($postDesc) < 3 ) {
$error[] = 'Description Must be more then 3 characters.';
}


$postCont = trim($_POST['postCont']);
if (strlen($postCont) < 3 ) {
$error[] = 'Content Must be more then 3 characters.';
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
   $postMetaKeywords = $_POST['postMetaKeywords'];
   $postMetaDescription = $_POST['postMetaDescription'];
   $postDesc      = $_POST['postDesc'];
   $postCont      = $_POST['postCont'];
   $catID         = $_POST['catID'];
   $postImg       = $_POST['postImg'];
   
   //strip any tags from input
   $postTitle   = safestrip($postTitle); 
   //$postDesc   = safe($postDesc); 
   //$postCont   = safe($postCont);   
   
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$postTitle   = addslashes($postTitle);
	$postDesc   = addslashes($postDesc);
	$postCont   = addslashes($postCont);
	 }
	 
	   $postSlug  = strtolower(str_replace(" ", '-', $postTitle));
	   $postSlug  = strtolower(str_replace("/", '', $postSlug));
	   $postSlug  = strtolower(str_replace("?", '', $postSlug));
	   $postSlug  = strtolower(str_replace("&", 'and', $postSlug));
	   $postSlug  = strtolower(str_replace("!", '', $postSlug));
	   $postSlug  = strtolower(str_replace(".", '', $postSlug));
	   $postSlug  = strtolower(str_replace(",", '', $postSlug));
	   $postSlug  = strtolower(str_replace("£", '', $postSlug));
	   $postSlug  = strtolower(str_replace("@", '', $postSlug));
	   $postSlug  = strtolower(str_replace("_", '', $postSlug));
	   $postSlug  = strtolower(str_replace("--", '-', $postSlug));
	   $postSlug  = strtolower(str_replace("'", '', $postSlug));
	   
	    $postSlug = stripslashes($postSlug); 
		$postTitle = stripslashes($postTitle); 
		
		foreach($_POST['sidebar'] as $side)
		{
		$sides.=$side.',';						
		}  
  

// insert data into images table
$query = "UPDATE ".PREFIX."blog_posts SET postTitle = '$postTitle', postSlug = '$postSlug', postMetaKeywords='$postMetaKeywords', postMetaDescription='$postMetaDescription', postDesc = '$postDesc', postCont = '$postCont', postImg='$postImg', catID = '$catID', sidebars='$sides' WHERE postID='$postID'";
$result  = mysql_query($query) or die ('Cannot Update image because: '. mysql_error());
 	
	
pingSE(DIR."sitemap.php","bing");
pingSE(DIR."sitemap.php","ask");
pingSE(DIR."sitemap.php","google");
pingSE(DIR."sitemap.php","moreover");	

if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'Blog Post Updated';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'Blog Post Updated';
url('manage-add-ons/blog/posts-'.$catID);
}  
 
}
}

	
//dispaly any errors
errors($error);
$result = mysql_query("SELECT * FROM ".PREFIX."blog_posts WHERE postID='{$_GET['edit-post']}'");
while($row = mysql_fetch_object($result)){
?>

<form action="" method="post">
<input type="hidden" name="postID" value="<?=$row->postID;?>" />

<p><label>Title:</label> <input name="postTitle" type="text" class="box-medium tooltip-right" title="Enter the title" value="<?php echo $row->postTitle;?>" size="30"/>
</p>

<p><label>Meta Keywords</label><br /> <textarea class="ta-default tooltip-top" title="Place keywords here coma seperated, to help search engines find this page" name="postMetaKeywords" cols="60" rows="5"><?php echo $row->postMetaKeywords;?></textarea></p>

<p><label>Meta Description</label><br /> <textarea class="ta-default tooltip-top" title="Set the description that will be shown in search engines results" name="postMetaDescription" cols="60" rows="5"><?php echo $row->postMetaDescription;?></textarea></p>

<p><label>Description:</label><textarea name="postDesc" id="postDesc" cols="60" rows="20"><?php echo $row->postDesc;?></textarea>
</p>

<p><label>Content:</label><textarea name="postCont" id="postCont" cols="60" rows="20"><?php echo htmlspecialchars($row->postCont);?></textarea>
</p>

<p><label>Image:</label><textarea name="postImg" id="postImg" cols="60" rows="20"><?php echo $row->postImg;?></textarea></p>

<p><label>Select Section:</label>
 <?php
$cat = $row->catID; 
$result2 = mysql_query("SELECT * FROM ".PREFIX."blog_cats")or die(mysql_error());
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

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save post and return to blog">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save post and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save post and return to blog">
</form>	
<?php }
echo "</div>";
} else {
url(DIRADMIN);

}	
}


function blogRequest()
{
	if(isset($_GET['blog'])){
	manageblog();
	$curpage = true;
	}
	
	if(isset($_GET['add-blog-cat'])){
	blogaddcat();
	$curpage = true;
	}
	
	if(isset($_GET['edit-blog-cat'])){
	blogeditcat();
	$curpage = true;
	}
	
	if(isset($_GET['posts'])){
	blogmanageposts();
	$curpage = true;
	}
	
	
	if(isset($_GET['add-post'])){
	blogaddpost();
	$curpage = true;
	}
	
	if(isset($_GET['edit-post'])){
	blogeditpost();
	$curpage = true;
	}
	
	if(isset($_GET['delblogcat'])){
	pdelblogcat();
	$curpage = true;
	}
	
	if(isset($_GET['delblogpost'])){
	pdelblogpost();
	$curpage = true;
	}
	
	
if(isset($_GET['blogcat'])){

if(!isset($_GET['p'])){
	$blogpage = 1;
} else {
	$blogpage = $_GET['p'];
}

$isql = mysql_query("SELECT * FROM ".PREFIX."blog_limit")or die(mysql_error());
$si = mysql_fetch_object($isql);

// Define the number of results per allclientspages
$max_results = $si->postlimit;

// Figure out the limit for the query based
$from = (($blogpage * $max_results) - $max_results); 

	$id = $_GET['blogcat'];
	$id = mysql_real_escape_string($id);
	
	$sql = mysql_query("SELECT * FROM ".PREFIX."blog_cats WHERE catSlug='$id'")or die(mysql_error());
    $b = mysql_fetch_object($sql);
	
	//$plugcont.="<div id=\"bread\"><a href=\"".DIR."\">Home</a> > $b->catTitle</div>";
  
  $sql = mysql_query("SELECT * FROM ".PREFIX."blog_cats WHERE catSlug='$id'")or die(mysql_error());
  
  while($r = mysql_fetch_object($sql)){
  
  $c = mysql_query("SELECT * FROM ".PREFIX."blog_posts WHERE catID='$r->catID' ORDER BY postID DESC LIMIT $from, $max_results");
  $n = mysql_num_rows($c);
  
  if($n == 0){
  
  $plugcont.="<h1>No posts in <b>$b->catTitle</b> yet</h1>";
  
  } else {
  
  
  while($pr = mysql_fetch_object($c)){
  
  $cs = mysql_query("SELECT * FROM ".PREFIX."blog_comments WHERE postID='$pr->postID'");
  $num = mysql_num_rows($cs);
  
  $date = date('l jS \of F Y', strtotime($pr->postDate));
  
  $m = mysql_query("SELECT * FROM ".PREFIX."members WHERE memberID='$pr->memberID'")or die(mysql_error());
  $mr = mysql_fetch_object($m);
  
  $plugcont.="<div class=\"post\">";  
  $plugcont.="<h1 title=\"$pr->postTitle\"><a href=\"".DIR."p-$pr->postSlug\" title=\"$r->postTitle\">$pr->postTitle</a></h1>";
  $plugcont.="<div class=\"postcontent\">\n"; 
  $wi = '137';
  $hi = '137';
  $img =  $pr->postImg;//'assets/templates/images/minipostthumb.jpg';
  $img = str_replace("../../../..","",$img);
  $str = preg_replace('#<p>.+?src=[\'"]([^\'"]+)[\'"].+</p>#i', "$1", $img);
  if($pr->postImg !=''){
  $plugcont.="<a href=\"".DIR."p-$pr->postSlug\"><img src=\"".DIR."img.php?src=$str&w=$wi&zc=1\" alt=\"$pr->postTitle\" title=\"$pr->postTitle\" class=\"thumb\" /></a>\n";
  }
  $plugcont.="<p class=\"meta\"><span>$date in <a href=\"".DIR."c-$r->catSlug\" title=\"$r->catTitle\">$r->catTitle</a> by $mr->username</span></p>\n";
  $plugcont.=$pr->postDesc;
  $plugcont.="</div><!-- /postcontent -->\n";
			$plugcont.="<div class=\"postmeta\">\n";
			//$plugcont.="<p class=\"more\"><a href=\"".DIR."p-$pr->postSlug#cs\" title=\"$pr->postTitle\" class=\"button\"><span>$num</span> Comments</a>\n";
			$plugcont.="<p class=\"more\">\n";
			$plugcont.="<a href=\"".DIR."p-$pr->postSlug\" title=\"$pr->postTitle\" class=\"button\">Read More</a></p>\n";
			$plugcont.="</div><!-- close postmeta -->";
 $plugcont.="</div><!-- close post -->";
   
  }
  }

   // Figure out the total number of results in DB:
$total_results = mysql_result(mysql_query("SELECT COUNT(*) as Num FROM ".PREFIX."blog_posts WHERE catID='$r->catID'"),0);

// Figure out the total number of clientspages. Always round up using ceil()
$total_blogpage = ceil($total_results / $max_results);

// Build clientspage Number Hyperlinks

$plugcont.= "<div class=\"pagination\">\n";


// Build Previous Link
			if($blogpage > 1){
				$prev = ($blogpage - 1);
				$plugcont.=  "<a href=\"tc=$id&p-$prev\">Previous</a>\n ";
			}
			
			for($i = 1; $i <= $total_blogpage; $i++){
				if($total_blogpage > 1){
				if(($blogpage) == $i){
					$plugcont.=  "<span class=\"current\">$i</span>\n ";
					} else {
						$plugcont.=  "<a href=\"c-$id&p=$i\">$i</a>\n ";
				}
				}
			}
			
			// Build Next Link
			if($blogpage < $total_blogpage){
				$next = ($blogpage + 1);
				$plugcont.=  "<a href=\"c-$id&p=$next\">Next</a>\n";
			}
			$plugcont.=  "</div>\n";
  
  }

global $curpage,$s,$page;
$curpage = true;

$s.=$b->sidebars; 

$page = $plugcont;
//$_SESSION['plugcont'].= $plugcont;
define('THEME','inner-page.php');
define('THEMEPATH','assets/templates/');
define('PLUGPAGE','blog');
define('ISPLUGPAGE','Yes');
}
	
	if(isset($_GET['blogpost'])){
	
  $id = $_GET['blogpost'];
  $id = mysql_real_escape_string($id);
   
  $psql = mysql_query("SELECT * FROM ".PREFIX."blog_posts WHERE `postSlug` = '$id'")or die(mysql_error());
  $n = mysql_num_rows($psql);
  $r = mysql_fetch_object($psql);
  
  //update db
  $up = mysql_query("UPDATE ".PREFIX."blog_posts SET postViews=postViews+1 WHERE postID='$r->postID'")or die(mysql_error());
  
  $c = mysql_query("SELECT * FROM ".PREFIX."blog_cats WHERE catID='$r->catID'");
  $cr = mysql_fetch_object($c);
  
  $cs = mysql_query("SELECT * FROM ".PREFIX."blog_comments WHERE postID='$r->postID'");
  $num = mysql_num_rows($cs);
  
 $date = date('l jS \of F Y', strtotime($r->postDate));
 
 $m = mysql_query("SELECT * FROM ".PREFIX."members WHERE memberID='$r->memberID'")or die(mysql_error());
  $mr = mysql_fetch_object($m);
  
  if($n == 0){
  
  $plugcont.="<h1>Post not found</h1>\n<p>You've requested a post that does not exist.</p>\n";
  
  } else {
  

	$plugcont.= "\n";
	$plugcont.= "<h1 title=\"$r->postTitle\">$r->postTitle</h1>\n";
	$plugcont.= "<div class=\"post\">\n";
	$wi = '137';
	$hi = '137';
	$img =  $r->postImg;//'assets/templates/images/minipostthumb.jpg';
	$img = str_replace("../../../..","",$img);
	$str = preg_replace('#<p>.+?src=[\'"]([^\'"]+)[\'"].+</p>#i', "$1", $img);
	if($r->postImg !=''){
	$plugcont.="<a href=\"".DIR."$str\" rel=\"prettyPhoto\"><img src=\"".DIR."img.php?src=$str&w=$wi&zc=1\" alt=\"$r->postTitle\" title=\"$r->postTitle\" class=\"thumb\" /></a>\n";
 }

	$plugcont.= "<p class=\"meta\"><span>$date in <a href=\"".DIR."c-$cr->catSlug\" title=\"$cr->catTitle\">$cr->catTitle</a> by $mr->username</span></p>\n";
	$plugcont.=$r->postCont;
	$plugcont.= "</div>\n";
						
	}

global $curpage,$s,$page;
$curpage = true;

$page = $plugcont;

$s.=$r->sidebars; 


//$_SESSION['plugcont'].= $plugcont;
define('THEME','inner-page.php');
define('THEMEPATH','assets/templates/');
define('PLUGPAGE','blog');
define('ISPLUGPAGE','Yes');
}
	

}



function jsdelblogcomment() {
	return "\nfunction delblogcomment(ID, Title)
{
   if (confirm(\"Are you sure you want to delete '\" + Title + \"'\"))
   {
      window.location.href = '".DIR."index.php?delblogcomment=' + ID;
   }
}\n";
}


function delblogcomment() {
if(isset($_GET['delblogcomment']))
{
  $query = "DELETE FROM ".PREFIX."blog_comments WHERE commentID = '{$_GET['delblogcomment']}'";
  mysql_query($query) or die('Error : ' . mysql_error());
  header('Location: ' . $_SERVER['HTTP_REFERER']);
}

}

function delblogcat() {

$delType = $_GET['delType'];
$delID   = $_GET['delID'];
$delID = mysql_real_escape_string($delID);

if($delType == 'delblogcat')
{

    $query = "SELECT * FROM ".PREFIX."blog_posts  WHERE catID = '$delID'";
	$result = mysql_query($query) or die('problem: ' . mysql_error());
	while ( $row = mysql_fetch_array ($result)) {

	$query = "DELETE FROM ".PREFIX."blog_posts WHERE catID = '$row->catID'";
  	mysql_query($query) or die('Error : ' . mysql_error());
	}
	
	$query = "DELETE FROM ".PREFIX."blog_cats WHERE catID = '$delID'";
  	mysql_query($query) or die('Error : ' . mysql_error());

   	$_SESSION['success'] = 'Cat Deleted';
	url('manage-add-ons/blog');
}  	
}


function delblogpost() {

$delType = $_GET['delType'];
$delID   = $_GET['delID'];
$delID = mysql_real_escape_string($delID);

if($delType == 'delblogpost')
{
    $query = "SELECT * FROM ".PREFIX."blog_posts WHERE postID = '$delID'";
	$result = mysql_query($query) or die('problem: ' . mysql_error());
	$row = mysql_fetch_object ($result);

    $query = "DELETE FROM ".PREFIX."blog_posts WHERE postID = '$delID'";
    mysql_query($query) or die('Error : ' . mysql_error());
  
    $_SESSION['success'] = 'Post Deleted';
    url('manage-add-ons/blog/posts-'.$row->catID);
}
}


function blog($string) 
{	

if(!isset($_GET['p'])){
	$blogpage = 1;
} else {
	$blogpage = $_GET['p'];
}

// Define the number of results per allclientspages
$max_results = 10;

// Figure out the limit for the query based
$from = (($blogpage * $max_results) - $max_results); 
//LIMIT $from, $max_results

  $sql = mysql_query("SELECT * FROM ".PREFIX."blog_posts ORDER BY postID DESC LIMIT $from, $max_results")or die(mysql_error());
  while($r = mysql_fetch_object($sql)){
  
  $c = mysql_query("SELECT * FROM ".PREFIX."blog_cats WHERE catID='$r->catID'");
  $cr = mysql_fetch_object($c);
  
  $cs = mysql_query("SELECT * FROM ".PREFIX."blog_comments WHERE postID='$r->postID'");
  $num = mysql_num_rows($cs);
  
  $date = date('l jS \of F Y', strtotime($r->postDate));
  
  $m = mysql_query("SELECT * FROM ".PREFIX."members WHERE memberID='$r->memberID'")or die(mysql_error());
  $mr = mysql_fetch_object($m);
  
  $listOuput.="<div class=\"post\">";  
  $listOuput.="<h1 title=\"$r->postTitle\"><a href=\"".DIR."p-$r->postSlug\" title=\"$r->postTitle\">$r->postTitle</a></h1>";
  $listOuput.="<div class=\"postcontent\">\n";
  $wi = '137';
  $hi = '137';
  $img =  $r->postImg;//'assets/templates/images/minipostthumb.jpg';
  $img = str_replace("../../../..","",$img);
  $str = preg_replace('#<p>.+?src=[\'"]([^\'"]+)[\'"].+</p>#i', "$1", $img);
  if($r->postImg !=''){
  $listOuput.="<a href=\"".DIR."p-$r->postSlug\"><img src=\"".DIR."img.php?src=$str&w=$wi&zc=1\" alt=\"$r->postTitle\" title=\"$r->postTitle\" class=\"thumb\" /></a>\n";
  }
  $listOuput.="<p class=\"meta\"><span>$date in <a href=\"".DIR."c-$cr->catSlug\" title=\"$cr->catTitle\">$cr->catTitle</a> by $mr->username</span></p>\n";
  $listOuput.=$r->postDesc;
  $listOuput.="</div><!-- /postcontent -->\n";
			$listOuput.="<div class=\"postmeta\">\n";
			//$listOuput.="<p class=\"more\"><a href=\"".DIR."p-$r->postSlug#cs\" title=\"$r->postTitle\" class=\"button\"><span>$num</span> Comments</a>\n";
			$listOuput.="<p class=\"more\">\n";
			$listOuput.="<a href=\"".DIR."p-$r->postSlug\" title=\"$r->postTitle\" class=\"button\">Read More</a></p>\n";
			$listOuput.="</div><!-- close postmeta -->";
 $listOuput.="</div><!-- close post -->";
  
  }
  
  // Figure out the total number of results in DB:
$total_results = mysql_result(mysql_query("SELECT COUNT(*) as Num FROM ".PREFIX."blog_posts"),0);

// Figure out the total number of clientspages. Always round up using ceil()
$total_blogpage = ceil($total_results / $max_results);

// Build clientspage Number Hyperlinks

$listOuput.= "<div class=\"pagination\">\n";


// Build Previous Link
			if($blogpage > 1){
				$prev = ($blogpage - 1);
				$listOuput.=  "<a href=\"".DIR."news?p=$prev\">Previous</a>\n ";
			}
			
			for($i = 1; $i <= $total_blogpage; $i++){
			 if($total_blogpage > 1){
					if(($blogpage) == $i){
						$listOuput.=  "<span class=\"current\">$i</span>\n ";
						} else {
							$listOuput.=  "<a href=\"".DIR."news?p=$i\">$i</a>\n ";
					}
				}
			}
			
			// Build Next Link
			if($blogpage < $total_blogpage){
				$next = ($blogpage + 1);
				$listOuput.=  "<a href=\"".DIR."news?p=$next\">Next</a>\n";
			}
			$listOuput.=  "</div>\n";
  
  
  
  
 
 
$string = str_replace("[blog]", $listOuput, $string);
return $string;
}

function blogcats($string){

$ouput.="<ul class=\"list\">";

	$c = mysql_query("SELECT * FROM ".PREFIX."blog_cats ORDER BY catTitle")or die(mysql_error());
	while($r = mysql_fetch_object($c)){
	$ouput.="<li><a href=\"".DIR."c-$r->catSlug\" title=\"$r->catTitle\">$r->catTitle</a></li>";
	}
$ouput.="</ul>";
  
$string = str_replace("[blogcats]", $ouput, $string);
return $string;
}





function blogposts($string) {

	
$sql = mysql_query("SELECT * FROM ".PREFIX."blog_limit");
$row = mysql_fetch_object($sql);

// Define the number of results per allclientspages
$max_results = $row->postsnippetlimit;
	
	$result = mysql_query("SELECT SUBSTRING(postTitle,1,35) as postTitle, postSlug, postImg FROM ".PREFIX."blog_posts WHERE postID ORDER BY postID DESC LIMIT $max_results ")or die(mysql_error());
	$newSnippitOutput.="<ul class=\"widget\">";
	while ($nrow = mysql_fetch_object($result))
	{
		$wi = '40';
	    $hi = '40 ';
		$img =  $nrow->postImg;//'assets/templates/images/minipostthumb.jpg';
		$img = str_replace("../../../..","",$img);
		$str = preg_replace('#<p>.+?src=[\'"]([^\'"]+)[\'"].+</p>#i', "$1", $img);
		
		if($nrow->postImg ==''){
		$newSnippitOutput.= "<li><img src=\"\" alt=\"\" title=\"$nrow->postTitle\" />\n<h3><a href=\"".DIR."news\">$nrow->postTitle</a></h3>\n</li>\n";
		} else {
		$newSnippitOutput.= "<li><img src=\"".DIR."img.php?src=$str&w=$wi&&h=$hi&zc=1\" alt=\"$nrow->postTitle\" title=\"$nrow->postTitle\" />\n<h3><a href=\"".DIR."news\">$nrow->postTitle...</a></h3>\n</li>\n";
		}		
		
	}
	$newSnippitOutput.="</ul>";
	//$newSnippitOutput.="<p class=\"f-right\"><a href=\"".DIR."blog\">See All News</a></p>";
	
	$string = str_replace("[blogposts]", $newSnippitOutput, $string);
	return $string;
}

function latestBlog($string){

  $sql = mysql_query("SELECT * FROM ".PREFIX."blog_posts ORDER BY postID DESC LIMIT 1")or die(mysql_error());
  while($r = mysql_fetch_object($sql)){
  
  $c = mysql_query("SELECT * FROM ".PREFIX."blog_cats WHERE catID='$r->catID'")or die(mysql_error());
  $cr = mysql_fetch_object($c);
  
  $cs = mysql_query("SELECT * FROM ".PREFIX."blog_comments WHERE postID='$r->postID'");
  $num = mysql_num_rows($cs);
  
  $date = date('l jS \of F Y', strtotime($r->postDate));
  
  $m = mysql_query("SELECT * FROM ".PREFIX."members WHERE memberID='$r->memberID'")or die(mysql_error());
  $mr = mysql_fetch_object($m);
  
  $output.="<div class=\"post\">";  
  $output.="<h1 title=\"$r->postTitle\"><a href=\"".DIR."p-$r->postSlug\" title=\"$r->postTitle\">$r->postTitle</a></h1>";
  $output.="<div class=\"postcontent\">\n";
  $wi = '137';
  $hi = '137';
  $img =  $r->postImg;//'assets/templates/images/minipostthumb.jpg';
  $img = str_replace("../../../..","",$img);
  $str = preg_replace('#<p>.+?src=[\'"]([^\'"]+)[\'"].+</p>#i', "$1", $img);
  if($r->postImg !=''){
  $output.="<a href=\"".DIR."p-$r->postSlug\" title=\"$r->postTitle\"><img src=\"".DIR."img.php?src=$str&w=$wii&zc=1\" alt=\"$r->postTitle\" title=\"$r->postTitle\" class=\"thumb\" /></a>\n";
  }
  $output.="<p class=\"meta\"><span>$date in <a href=\"".DIR."c-$cr->catSlug\" title=\"$cr->catTitle\">$cr->catTitle</a> by $mr->username</span></p>\n";
  $output.=$r->postDesc;
  $output.="</div><!-- /postcontent -->\n";
			$output.="<div class=\"postmeta\">\n";
			$output.="<p class=\"more\"><a href=\"".DIR."p-$r->postSlug#cs\" title=\"$r->postTitle\" class=\"button\"><span>$num</span> Comments</a>\n";
			$output.="<a href=\"".DIR."p-$r->postSlug\" class=\"button\" title=\"$r->postTitle\">Read More</a></p>\n";
			$output.="</div><!-- close postmeta -->";
 $output.="</div><!-- close post -->";
  
  }

	$string = str_replace("[latest-blog]", $output, $string);
	return $string;
}

function addLinksblog() {
	echo "bk<div class=\"icon\">\n";
		echo "<a href=\"".DIR."admin/manage-add-ons/blog\"><img src=\"".DIR."assets/plugins/blog/blog.png\" alt=\"Blog\" title=\"Manage Blog\" class=\"tooltip-top\" /></a>\n";
    	echo "<p><a href=\"".DIR."admin/manage-add-ons/blog\" title=\"Manage Blog\" class=\"tooltip-top\">Blog</a></p>\n";
    echo "</div>\n";	
}


//add hook, where to execute a function
add_hook('admin_modules','addLinksblog');
add_hook('cont','blog');
add_hook('cont','latestBlog');
add_hook('cont','blogposts');
add_hook('cont','blogcats');
add_hook('js_inner_popup', 'jsdelblogcomment');
add_hook('del', 'delblogpost');
add_hook('del', 'delblogcat');
add_hook('del_inner', 'delblogcomment');
add_hook('page_requester','blogRequest');
?>
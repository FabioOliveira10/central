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

if (preg_match('/gallery/', $data))
{
} else { 
$newData = "
RewriteRule ^admin/manage-add-ons/gallery$ 			                admin.php?gallery=$1 [L]
RewriteRule ^admin/manage-add-ons/gallery/$			                admin.php?gallery=$1 [L]

RewriteRule ^admin/manage-add-ons/gallery/add-album$ 			    admin.php?add-album=$1 [L]
RewriteRule ^admin/manage-add-ons/gallery/add-album/$			    admin.php?add-album=$1 [L]

RewriteRule ^admin/manage-add-ons/gallery/edit-album-([^/]+)$ 		admin.php?edit-album=$1 [L]
RewriteRule ^admin/manage-add-ons/gallery/edit-album-([^/]+)/$		admin.php?edit-album=$1 [L]

RewriteRule ^admin/manage-add-ons/gallery/images-([^/]+)$ 		    admin.php?gimages=$1 [L]
RewriteRule ^admin/manage-add-ons/gallery/images-([^/]+)/$  	    admin.php?gimages=$1 [L]

RewriteRule ^admin/manage-add-ons/gallery/images/add-image-([^/]+)$   admin.php?add-image=$1 [L]
RewriteRule ^admin/manage-add-ons/gallery/images/add-image-([^/]+)/$  admin.php?add-image=$1 [L]

RewriteRule ^admin/manage-add-ons/gallery/images/edit-image-([^/]+)$  admin.php?edit-image=$1 [L]
RewriteRule ^admin/manage-add-ons/gallery/images/edit-image-([^/]+)/$ admin.php?edit-image=$1 [L]\n###\n";

$data = str_replace("###",$newData,$data);

$myFile = $cfile;
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $data);
fclose($fh);

mysql_query("CREATE TABLE IF NOT EXISTS `".PREFIX."albums` (
  `albumID` int(11) NOT NULL auto_increment,
  `albumTitle` varchar(255) NOT NULL,
  PRIMARY KEY  (`albumID`)
) ENGINE=MyISAM")or die('cannot make table albums due to: '.mysql_error());

mysql_query("CREATE TABLE IF NOT EXISTS `".PREFIX."gallery_limit` (
  `limitID` int(11) NOT NULL auto_increment,
  `albumID` int(11) NOT NULL,
  `limitNum` int(11) NOT NULL default '25',
  PRIMARY KEY  (`limitID`)
) ENGINE=MyISAM")or die('cannot make table gallery limit due to: '.mysql_error());

mysql_query("CREATE TABLE IF NOT EXISTS `".PREFIX."gallery` (
  `imageID` int(11) NOT NULL auto_increment,
  `imageTitle` varchar(255) NOT NULL,
  `imageThumb` varchar(255) NOT NULL,
  `imageFull` varchar(255) NOT NULL,
  `albumID` int(11) NOT NULL,
  `orderID` int(11) NOT NULL,
  PRIMARY KEY  (`imageID`)
) ENGINE=MyISAM")or die('cannot make table gallery due to: '.mysql_error());


}


function manageAlbums()
{
	$curpage = true;	
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Gallery</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIR."\">Home</a> > <a href=\"".DIR."admin\">Admin</a> > <a href=\"".DIR."admin/manage-add-ons\">Manage Add-ons</a> > Gallery</p></div>";

echo messages();

echo "<p>To implement the album insert the album title in brackets like [mygallery] into any page you want the album to be displayed.</p>\n";

$result = mysql_query("SELECT * FROM ".PREFIX."albums")or die(mysql_error());
$Rows = mysql_num_rows($result);
?>
<table class="stripeMe">
<tr align="center">
<td width="42%" align="left"><strong>Albums</strong></td>
<td width="45%"><strong>Action</strong></td>
</tr>
<?php
while ($row = mysql_fetch_object($result)) {
?>
<tr>
<td><a href="<?php echo DIR;?>admin/manage-add-ons/gallery/images-<?php echo $row->albumID;?>"><?php echo $row->albumTitle;?></a></td>
<td align="center" valign="top"><a href="<?php echo DIR;?>admin/manage-add-ons/gallery/edit-album-<?php echo $row->albumID;?>">Edit</a> <a href="#" id="<?php echo $row->albumID;?>" rel="delalbum" title="<?php echo $row->albumTitle;?>" class="delete_button">Delete</a></td>
</tr>
<?php
}
?>
</table>
<p align="center"><a href="<?php echo DIR;?>admin/manage-add-ons/gallery/add-album">Add Album</a></p>

</div>
<?php
} else {
url(DIR);

}
}


function addAlbum()
{
	$curpage = true;
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Add Album</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIR."\">Home</a> > <a href=\"".DIR."admin\">Admin</a> > <a href=\"".DIR."admin/manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIR."admin/manage-add-ons/gallery\">Gallery</a> > Add Album</p></div>";

echo messages();

// if form submitted then process form
if (isset($_POST['sub'])){

// check feilds are not empty
$albumTitle = trim($_POST['albumTitle']);
if (strlen($albumTitle) < 1 || strlen($albumTitle) > 255) {
$error[] = 'Album title must be at between 1 and 255 charactors.';
}

$limitNum = trim($_POST['limitNum']);
if (strlen($limitNum) < 1) {
$error[] = 'Please input number of images to show per page';
}

// if valadation is okay then carry on
if (!$error) {
	
	// post form data 
   $albumTitle     = $_POST['albumTitle'];
   $limitNum       = $_POST['limitNum'];
   
   //strip any tags from input
   $albumTitle   = strip_tags($albumTitle);   
   
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$albumTitle   = addslashes($albumTitle);
	 }
   
   // remove any harhful code and stop sql injection
   $albumTitle = mysql_real_escape_string($albumTitle);
   $limitNum = mysql_real_escape_string($limitNum); 
   
 
// insert data into images table
$query = "INSERT INTO ".PREFIX."albums (albumTitle) VALUES ('$albumTitle')";
$result  = mysql_query($query) or die ('album'.mysql_error());
$getID = mysql_insert_id();

$query = "INSERT INTO ".PREFIX."gallery_limit (albumID,limitNum) VALUES ('$getID','$limitNum')";
$result  = mysql_query($query) or die ('limit album'.mysql_error());
 	

// show a message to confirm results	
$_SESSION['success'] = 'Album Added';
url('manage-add-ons/gallery');	
		
 
}
}

	
//dispaly any errors
echo errors($error);

?>

<form enctype="multipart/form-data" action="<?php echo DIR."admin/manage-add-ons/gallery/add-album";?>" method="post">
<p>Album Title:<br /><input type="text" class="text-input" name="albumTitle" <?php if (isset($error)){ echo "value=\"$albumTitle\""; }?>/></p>

<p>Number of images to show per page<br /><input class="text-input" name="limitNum" type="text" value="<?php if (isset($error)){ echo $limitNum; } else { echo "25";} ?>" size="3" /></p>

<p><input type="submit" class="button" name="sub" value="Add Album"></p>
</form>
</div>
<?php

} else {
url(DIR);
}		
}

function editAlbum()
{
	$curpage = true;	
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Edit Album</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIR."\">Home</a> > <a href=\"".DIR."admin\">Admin</a> > <a href=\"".DIR."admin/manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIR."admin/manage-add-ons/gallery\">Gallery</a> > Edit Album</p></div>";

// if form submitted then process form
if (isset($_POST['sub'])){

// check feilds are not empty
$albumTitle = trim($_POST['albumTitle']);
if (strlen($albumTitle) < 1 || strlen($albumTitle) > 255) {
$error[] = 'Image name must be at between 1 and 255 charactors.';
}

$limitNum = trim($_POST['limitNum']);
if (strlen($limitNum) < 1) {
$error[] = 'Please input number of images to show per page';
}

// if valadation is okay then carry on
if (!$error) {
	
	// post form data 
   $albumID     = $_POST['albumID'];
   $albumTitle     = $_POST['albumTitle'];
   $limitNum       = $_POST['limitNum'];
   
   //strip any tags from input
   $albumTitle   = strip_tags($albumTitle);   
   
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$albumTitle   = addslashes($albumTitle);
	 }
   
   // remove any harhful code and stop sql injection
   $albumTitle = mysql_real_escape_string($albumTitle); 
   $limitNum = mysql_real_escape_string($limitNum);   
  

// insert data into images table
$query = "UPDATE ".PREFIX."albums SET albumTitle = '$albumTitle' WHERE albumID='$albumID'";
$result  = mysql_query($query) or die (mysql_error());

$query = "UPDATE ".PREFIX."gallery_limit SET limitNum = '$limitNum' WHERE albumID='$albumID'";
$result  = mysql_query($query) or die (mysql_error());
 	

// show a message to confirm results	
$_SESSION['success'] = 'Album Updated';
url('manage-add-ons/gallery');		
 
}
}

	
//dispaly any errors
echo errors($error);

$sql = mysql_query("SELECT * FROM ".PREFIX."gallery_limit WHERE albumID='{$_GET['edit-album']}'");
$lrow = mysql_fetch_object($sql);

// Define the number of results per allclientspages
$limitNum = $lrow->limitNum;

$result = mysql_query("SELECT * FROM ".PREFIX."albums WHERE albumID='{$_GET['edit-album']}'");
while($row = mysql_fetch_object($result)){
?>

<form action="<?php echo DIR."admin/manage-add-ons/gallery/edit-album-$row->albumID";?>" method="post">
<input type="hidden" name="albumID" value="<?=$row->albumID;?>" />
<p>Image Name:<br /><input type="text" class="text-input" name="albumTitle" value="<?php echo $row->albumTitle;?>"/></p>

<p>Number of images to show per page<br /><input class="text-input" name="limitNum" type="text" value="<?php echo $limitNum; ?>" size="3" /></p>

<p><input type="submit" name="sub" class="button" value="Update Album"></p>
</form>	
</div>
<?php }

} else {
url(DIR);

}	
}

function manageGallery()
{
	$curpage = true;;
	if (isglobaladmin() || isadmin()){
		
$result = mysql_query("SELECT * FROM ".PREFIX."gallery WHERE albumID='{$_GET['gimages']}'")or die(mysql_error());
$Rows = mysql_num_rows($result);

$sql = mysql_query("SELECT * FROM ".PREFIX."albums WHERE albumID='{$_GET['gimages']}'")or die(mysql_error());
$row = mysql_fetch_object($sql);
$albumTitle = $row->albumTitle;
$albumID = $row->albumID;

echo "<div class=\"content-box-header\"><h3>$albumTitle</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIR."\">Home</a> > <a href=\"".DIR."admin\">Admin</a> > <a href=\"".DIR."admin/manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIR."admin/manage-add-ons/gallery\">Gallery</a> > <a href=\"".DIR."admin/manage-add-ons/gallery/images-$albumID\">$albumTitle</a></p></div>";

echo messages();
?>
<table class="stripeMe">
<tr align="center">
<td width="42%" align="left"><strong>Gallery</strong></td>
<td width="45%"><strong>Action</strong></td>
</tr>
<?php
while ($row = mysql_fetch_object($result)) {
?>
<tr>
<td><?php echo $row->imageTitle;?></td>
<td align="center" valign="top"><a href="<?php echo DIR;?>admin/manage-add-ons/gallery/images/edit-image-<?php echo $row->imageID;?>">Edit</a> | <a href="#" id="<?php echo $row->imageID;?>" rel="delimage" title="<?php echo $row->imageTitle;?>" class="delete_button">Delete</a></td>
</tr>
<?php
}
?>
</table>
<p align="center"><a href="<?php echo DIR;?>admin/manage-add-ons/gallery/images/add-image-<?php echo $albumID;?>">Add Image</a></p>
</div>
<?php
} else {
url(DIR);

}		
}


function addGallery()
{
	$curpage = true;	
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Add Image</h3></div> 			
<div class=\"content-box-content\">";
	
$sql = mysql_query("SELECT * FROM ".PREFIX."albums WHERE albumID='{$_GET['add-image']}'")or die(mysql_error());
$row = mysql_fetch_object($sql);
$albumTitle = $row->albumTitle;
$albumID = $row->albumID;

echo "<div id=\"bread\"><p><a href=\"".DIR."\">Home</a> > <a href=\"".DIR."admin\">Admin</a> > <a href=\"".DIR."admin/manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIR."admin/manage-add-ons/gallery\">Gallery</a> > <a href=\"".DIR."admin/manage-add-ons/gallery/images-$albumID\">$albumTitle</a> > Add Image</p></div>";

$albumID = $_GET['add-image'];

// if form submitted then process form
if (isset($_POST['sub'])){

// check feilds are not empty
$imageTitle = trim($_POST['imageTitle']);
if (strlen($imageTitle) < 1 || strlen($imageTitle) > 255) {
$error[] = 'Image name must be at between 1 and 255 charactors.';
}

// location where inital upload will be moved to
$target = "assets/plugins/gallery/" . $_FILES['uploaded']['name'] ;

// find thevtype of image
switch ($_FILES["uploaded"]["type"]) {
case $_FILES["uploaded"]["type"] == "image/gif":
	move_uploaded_file($_FILES["uploaded"]["tmp_name"],$target);
    break;
case $_FILES["uploaded"]["type"] == "image/jpeg":
   	move_uploaded_file($_FILES["uploaded"]["tmp_name"],$target);		
    break;
case $_FILES["uploaded"]["type"] == "image/pjpeg":
   	move_uploaded_file($_FILES["uploaded"]["tmp_name"],$target);		
    break;	
case $_FILES["uploaded"]["type"] == "image/png":
    move_uploaded_file($_FILES["uploaded"]["tmp_name"],$target);		
    break;
case $_FILES["uploaded"]["type"] == "image/x-png":
    move_uploaded_file($_FILES["uploaded"]["tmp_name"],$target);		
    break;
	
default:
    $error[] = 'Wrong image type selected. Only JPG, PNG or GIF accepted!.';
}

// if valadation is okay then carry on
if (!$error) {
	
	// post form data 
   $imageTitle     = $_POST['imageTitle'];
   
   //strip any tags from input
   $imageTitle   = strip_tags($imageTitle);   
   
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$imageTitle   = addslashes($imageTitle);
	 }
   
   // remove any harhful code and stop sql injection
   $imageTitle = mysql_real_escape_string($imageTitle);    
  
	//add target location to varible $src_filename 
	$src_filename = $target;
	// define file locations for full sized and thumbnail images
	$dst_filename_full = 'assets/plugins/gallery/';
	$dst_filename_thumb = 'assets/plugins/gallery/';
	
// create the images
createthumbfull($src_filename, $dst_filename_full);	
createthumb($src_filename, $dst_filename_thumb);
// delete original image as its not needed any more.
unlink ($src_filename);

global $thumb_Add_thumb,$thumb_Add_full;
// insert data into images table
$query = "INSERT INTO ".PREFIX."gallery (imageTitle, imageThumb, imageFull, albumID) VALUES
  ('$imageTitle', '$thumb_Add_thumb', '$thumb_Add_full','$albumID')";
  $result  = mysql_query($query) or die ('Cannot add image because: '. mysql_error());
 	

// show a message to confirm results	
$_SESSION['success'] = 'Image Added';
url('manage-add-ons/gallery/images-'.$albumID);		
 
}
}

	
//dispaly any errors
echo errors($error);

?>

<form enctype="multipart/form-data" action="<?php echo DIR."admin/manage-add-ons/gallery/images/add-image-$albumID";?>" method="post">
<p><label>Image Name:</label> <input type="text" class="text-input" name="imageTitle" <?php if (isset($error)){ echo "value=\"$imageTitle\""; }?>/></p>
<p><label>Image:</label><input name="uploaded" class="text-input" type="file" maxlength="20" <?php if (isset($error)){ echo "value=\"$uploaded\""; }?>/></p>

</p>
<p><input type="submit" name="sub" class="button" value="Add Image"></p>
</form>
</div>
<?php

} else {
url(DIR);

}	
}

function editGallery()
{
	$curpage = true;
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Edit Image</h3></div> 			
<div class=\"content-box-content\">";
	
$sql = mysql_query("SELECT * FROM ".PREFIX."gallery WHERE imageID='{$_GET['edit-image']}'");	
$ob = mysql_fetch_object($sql);
	
$sql = mysql_query("SELECT * FROM ".PREFIX."albums WHERE albumID='$ob->albumID'")or die(mysql_error());
$row = mysql_fetch_object($sql);
$albumTitle = $row->albumTitle;
$albumID = $row->albumID;

echo "<div id=\"bread\"><p><a href=\"".DIR."\">Home</a> > <a href=\"".DIR."admin\">Admin</a> > <a href=\"".DIR."admin/manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIR."admin/manage-add-ons/gallery\">Gallery</a> > <a href=\"".DIR."admin/manage-add-ons/gallery/images-$albumID\">$albumTitle</a> > Edit Image</p></div>";

// if form submitted then process form
if (isset($_POST['sub'])){

// check feilds are not empty
$imageTitle = trim($_POST['imageTitle']);
if (strlen($imageTitle) < 1 || strlen($imageTitle) > 255) {
$error[] = 'Image name must be at between 1 and 255 charactors.';
}

$albumID = $_POST['albumID'];
if ($albumID == ''){
$error[] = 'Please select a section';
}

// if valadation is okay then carry on
if (!$error) {
	
	// post form data 
   $imageID     = $_POST['imageID'];
   $imageTitle     = $_POST['imageTitle'];
   $albumID             = $_POST['albumID'];
   
   //strip any tags from input
   $imageTitle   = strip_tags($imageTitle);   
   
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$imageTitle   = addslashes($imageTitle);
	 }
   
   // remove any harhful code and stop sql injection
   $imageTitle = mysql_real_escape_string($imageTitle);    
  

// insert data into images table
$query = "UPDATE ".PREFIX."gallery SET imageTitle = '$imageTitle',  albumID = '$albumID' WHERE imageID='$imageID'";
$result  = mysql_query($query) or die ('Cannot Update image because: '. mysql_error());
 	

// show a message to confirm results	
$_SESSION['success'] = 'Image updated';
url('manage-add-ons/gallery/images-'.$albumID);			
 
}
}

	
//dispaly any errors
echo errors($error);
$result = mysql_query("SELECT * FROM ".PREFIX."gallery WHERE imageID='{$_GET['edit-image']}'");
while($row = mysql_fetch_object($result)){
?>

<form action="<?php echo DIR."admin/manage-add-ons/gallery/images/edit-image-$row->imageID";?>" method="post">
<input type="hidden" name="imageID" value="<?=$row->imageID;?>" />
<p><label>Image Name:</label> <input class="text-input" type="text" name="imageTitle" value="<?php echo $row->imageTitle;?>"/></p>

<p><label>Select Section:</label>
 <?php
$cat = $row->albumID; 
$result2 = mysql_query("SELECT * FROM ".PREFIX."albums")or die(mysql_error());
echo "<select name='albumID'>\n";
echo "<option value=''>Please select a section</option>\n";
while ($row2 = mysql_fetch_object($result2)) { 
	echo "<option value='$row2->albumID'";	
	if ($_POST['albumID'] == $row2->albumID){
	echo "selected='selected'";
	}elseif($cat == $row2->albumID){
	echo "selected='selected'";		
	}
	echo ">$row2->albumTitle</option>\n"; 
}
	echo "</select>\n";
?> </p>

<p><input type="submit" name="sub" class="button" value="Update Picture"></p>
</form>	
<img src="<?=DIR.$row->imageFull;?>" alt="<?=$row->imageTitle;?>">
<?php }
echo "</div>";
} else {
url(DIR);

}	
}


function galleryRequest()
{
	if(isset($_GET['gallery'])){
	manageAlbums();
	$curpage = true;
	}
	
	if(isset($_GET['add-album'])){
	addAlbum();
	$curpage = true;
	}
	
	if(isset($_GET['edit-album'])){
	editAlbum();
	$curpage = true;
	}
	
	if(isset($_GET['gimages'])){
	manageGallery();
	$curpage = true;
	}
	
	
	if(isset($_GET['add-image'])){
	addGallery();
	$curpage = true;
	}
	
	if(isset($_GET['edit-image'])){
	editGallery();
	$curpage = true;
	}
	
	if(isset($_GET['delAlbum'])){
	delAlbum();
	$curpage = true;
	}
	
	if(isset($_GET['delimage'])){
	delimage();
	$curpage = true;
	}
}


function delalbum() {

$delType = $_GET['delType'];
$delID   = $_GET['delID'];
$delID = mysql_real_escape_string($delID);

if($delType == 'delalbum')
{
    $query = "SELECT * FROM  ".PREFIX."gallery  WHERE albumID = '$delID'";
	$result = mysql_query($query) or die('problem: ' . mysql_error());
	
	while ( $row = mysql_fetch_array ($result)) {
	if($row['imageThumb'] !== '' && $row['imageFull'] !== ''){
	unlink ($row['imageThumb']);
	unlink ($row['imageFull']);
	}	
	}

	$query = "DELETE FROM ".PREFIX."gallery WHERE albumID = '$delID'";
	mysql_query($query) or die('Error : ' . mysql_error());
	  
	$query = "DELETE FROM ".PREFIX."albums WHERE albumID = '$delID'";
	mysql_query($query) or die('Error : ' . mysql_error());

    $_SESSION['success'] = 'Deleted';
	url('admin/manage-add-ons/gallery');
}
}


function delimage() {

$delType = $_GET['delType'];
$delID   = $_GET['delID'];
$delID = mysql_real_escape_string($delID);

if($delType == 'delimage')
{
    $query = "SELECT * FROM  ".PREFIX."gallery  WHERE imageID = '$delID'";
	$result = mysql_query($query) or die('problem: ' . mysql_error());
	
	$row = mysql_fetch_array ($result);
	if($row['imageThumb'] !== '' && $row['imageFull'] !== ''){
	unlink ($row['imageThumb']);
	unlink ($row['imageFull']);
	}
	
	  $query = "DELETE FROM ".PREFIX."gallery WHERE imageID = '$delID'";
	  mysql_query($query) or die('Error : ' . mysql_error());
	  
	  $_SESSION['success'] = 'Image Deleted';
	  url('admin/manage-add-ons/gallery/images-'.$row['albumID']);
}
}









function gallery($string) 
{	

  //plugin for albums
$albsql = @mysql_query("SELECT * FROM ".PREFIX."albums");
while ($albRow = @mysql_fetch_object($albsql))
{ 
	$mystring = $string;
	$findme   = "[$albRow->albumTitle]";
	$pos = strpos($mystring, $findme);
	
	if ($pos !== false) { 
	  $albMatch = "[$albRow->albumTitle]";
	   
	   $asql = @mysql_query("SELECT * FROM ".PREFIX."gallery WHERE albumID='$albRow->albumID'")or die(mysql_error());
//while ($arow = mysql_fetch_object($asql))
$arow = @mysql_fetch_object($asql);
//{

$albumID = $arow->albumID;
	
if(!isset($_GET['gp'])){
	$gallerypage = 1;
} else {
	$gallerypage = $_GET['gp'];
}



$sql = @mysql_query("SELECT * FROM ".PREFIX."gallery_limit WHERE albumID='$albumID' ");
$lrow = @mysql_fetch_object($sql);

// Define the number of results per allclientspages
$max_results = $lrow->limitNum;


// Figure out the limit for the query based
$from = (($gallerypage * $max_results) - $max_results); 

//LIMIT $from, $max_results
$result = @mysql_query("SELECT * FROM ".PREFIX."gallery WHERE albumID='$albumID' ORDER BY imageID DESC LIMIT $from, $max_results");

$albOutput= "<div id=\"gallery\">\n";

while ($grow = @mysql_fetch_object($result)){
 
    $albOutput.= "<a href=\"".DIR."$grow->imageFull\" title=\"$grow->imageTitle\" rel=\"prettyPhoto[gallery]\"><img src=\"".DIR."$grow->imageThumb\" alt=\"\" title=\"$grow->imageTitle\" border=\"0\" class=\"thumbImage\" /></a>\n";
	}
 
$albOutput.= "</div>\n";	
// Figure out the total number of results in DB:
$total_results = @mysql_result(mysql_query("SELECT COUNT(*) as Num FROM ".PREFIX."gallery WHERE albumID='$albumID'"),0);

// Figure out the total number of clientspages. Always round up using ceil()
$total_gallerypage = @ceil($total_results / $max_results);

// Build clientspage Number Hyperlinks

$albOutput.= "<p style=\"clear:both;\" align=\"center\">\n";


// Build Previous Link
			if($gallerypage > 1){
				$prev = ($gallerypage - 1);
				$albOutput.=  "<a href=\"?gp=$prev&al=$albumID\">Previous</a>\n ";
			}
			
			for($i = 1; $i <= $total_gallerypage; $i++){
			if($total_gallerypage > 1){
				if(($gallerypage) == $i){
					$albOutput.=  "$i\n ";
					} else {
						$albOutput.=  "<a href=\"?gp=$i&al=$albumID\">$i</a>\n ";
				}
			}
			}
			
			// Build Next Link
			if($gallerypage < $total_gallerypage){
				$next = ($gallerypage + 1);
				$albOutput.=  "<a href=\"?gp=$next&al=$albumID\">Next</a>\n";
			}
			$albOutput.=  "</p>\n"; 

//}//close while
}//close if
	
	//$searchalb
	$string = str_replace("$albMatch", $albOutput, $string);
} //close first while  
  
  return $string;
}

function addLinksgallery() {
	echo "bk<div class=\"icon\">\n";
		echo "<a href=\"".DIR."admin/manage-add-ons/gallery\"><img src=\"".DIR."assets/plugins/gallery/gallery.png\" alt=\"Gallery\" title=\"Manage Gallery\" class=\"tooltip-top\" /></a>\n";
    	echo "<p><a href=\"".DIR."admin/manage-add-ons/gallery\" title=\"Manage Gallery\" class=\"tooltip-top\">Gallery</a></p>\n";
    echo "</div>\n";	
}


//add hook, where to execute a function
add_hook('admin_modules','addLinksgallery');
add_hook('cont','gallery');
add_hook('del', 'delimage');
add_hook('del', 'delalbum');
add_hook('page_requester','galleryRequest');

?>
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

if (preg_match('/slides/', $data))
{
} else { 

$newData = "
RewriteRule ^admin/manage-add-ons/slides$                    admin.php?slides=$1 [L]
RewriteRule ^admin/manage-add-ons/slides/$                   admin.php?slides=$1 [L]

RewriteRule ^admin/manage-add-ons/slides/add-slides$            admin.php?add-slides=$1 [L]
RewriteRule ^admin/manage-add-ons/slides/add-slides/$           admin.php?add-slides=$1 [L]

RewriteRule ^admin/manage-add-ons/slides/edit-slides-([^/]+)$   admin.php?edit-slides=$1 [L]
RewriteRule ^admin/manage-add-ons/slides/edit-slides-([^/]+)/$  admin.php?edit-slides=$1 [L]
###";

$data = str_replace("###",$newData,$data);

$myFile = $cfile;
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $data);
fclose($fh);


mysql_query("CREATE TABLE IF NOT EXISTS `".PREFIX."slides` (
  `slidesID` int(11) NOT NULL auto_increment,
  `slidesTitle` varchar(255) NOT NULL,
  `slidesImage` text NOT NULL,
  `slidesCont` text NOT NULL,
  PRIMARY KEY  (`slidesID`)
) ENGINE=MyISAM");

}

function manageslides()
{
global $curpage;
$curpage = true;
if (isglobaladmin() || isadmin()){
	
echo "<div class=\"content-box-header\"><h3>Slides</h3></div> 			
<div class=\"content-box-content\">";	


echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > Slides</p></div>";

echo messages();

echo "<p>To implement the slides insert [slides] into any page you want the slides to be displayed.</p>\n";

?>

<table class="stripeMe">
<tr align="center">
<td width="41%" align="left"><strong>Slides</strong></td>
<td width="59%"><strong>Action</strong></td>
</tr>
<?php
$result = mysql_query("SELECT * FROM ".PREFIX."slides ORDER BY slidesID DESC")or die(mysql_error());
while ($row = mysql_fetch_object($result)) {
?>
<tr>
<td><?php echo $row->slidesTitle;?></td><td align="center" valign="top"><a href="<?php echo DIRADMIN;?>manage-add-ons/slides/edit-slides-<?php echo $row->slidesID;?>" class="tooltip-top" title="Edit"><img src="<?php echo DIR;?>assets/images/icons/action-edit.png" alt="Edit" /></a> <a href="#" id="<?php echo $row->slidesID;?>" rel="delslides" title="<?php echo $row->slidesSlug;?>" class="delete_button"><img src="<?php echo DIR;?>assets/images/icons/action-del.png" alt="Delete" /></a></td>
</tr>
<?php
}
?>
</table>
<p><a href="<?php echo DIRADMIN;?>manage-add-ons/slides/add-slides" class="button tooltip-right" title="Add New Slide">Add Slide</a></p>
</div>
<?php

} else {
url(DIRADMIN);

}		
}


function addslides()
{
	global $curpage;
$curpage = true;
	if (isglobaladmin() || isadmin() || iseditor()){
		
echo "<div class=\"content-box-header\"><h3>Add Slide</h3></div> 			
<div class=\"content-box-content\">";			
		
	echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/slides\">Slides</a> > Add Slide</p></div>";
	
echo messages();

if(isset($_POST['cancel'])){
$_SESSION['info'] = "slide was not created";
url('manage-add-ons/slides');
}

// if form submitted then process form
if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$slidesTitle = trim($_POST['slidesTitle']);
if (strlen($slidesTitle) < 3 || strlen($slidesTitle) > 255) {
$error[] = 'Title Must be between 3 and 255 characters.';
}

$slidesTitle = $_POST['slidesTitle'];
$slidesImage = $_POST['slidesImage'];
$slidesCont  = $_POST['slidesCont'];

// if valadation is okay then carry on
if (!$error) {

		// escape any harmfull code and prevent sql injection
		$slidesTitle = mysql_real_escape_string($slidesTitle);
		$slidesCont   = mysql_real_escape_string($slidesCont);		
		 
	   	 $row->slidesCont = stripslashes($row->slidesCont);
		 $slidesTitle = stripslashes($slidesTitle); 
		 

	   	      
$sql = "INSERT INTO ".PREFIX."slides (slidesTitle, slidesImage, slidesCont) VALUES ('$slidesTitle', '$slidesImage', '$slidesCont')";
$resultupdate = mysql_query($sql)or die(mysql_error());


pingSE(DIR."sitemap.php","bing");
pingSE(DIR."sitemap.php","ask");
pingSE(DIR."sitemap.php","google");
pingSE(DIR."sitemap.php","moreover");

if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'Slide Added';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'Slide Added';
url('manage-add-ons/slides');	
}

 
}// close errors
}// close if form sent

//dispaly any errors
errors($error);

?>

<form action="" method="post">

<p><label>Slide Title</label> <input name="slidesTitle" class="box-medium tooltip-right" title="Enter the Slides Title" type="text" value="<?php if (isset($error)){ echo $slidesTitle; }?>" size="40" maxlength="255"  />
</p>

<p><label>Slide Content</label><textarea name="slidesCont" id="edit2" cols="60" rows="20"><?php if (isset($error)){ echo $slidesCont ; }?></textarea>
</p>

<p><label>Slide Image</label><textarea name="slidesImage" id="edit3" cols="60" rows="20"><?php if (isset($error)){ echo $slidesImage; }?></textarea>
</p>

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save page and return to slides">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save page and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save page and return to slides">
</form>
</div>
<?php
} else {
url(DIRADMIN);

}		
}

function editslides()
{
	global $curpage;
$curpage = true;
	if (isglobaladmin() || isadmin() || iseditor()){
		
echo "<div class=\"content-box-header\"><h3>Edit Slide</h3></div> 			
<div class=\"content-box-content\">";	

	echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/slides\">Slides</a> > Edit Slide</p></div>";

if(isset($_POST['cancel'])){
$_SESSION['info'] = "slide was not updated";
url('manage-add-ons/slides');
}

// if form submitted then process form
if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$slidesTitle = trim($_POST['slidesTitle']);
if (strlen($slidesTitle) < 3 || strlen($slidesTitle) > 255) {
$error[] = 'Title Must be between 3 and 255 characters.';
}

$slidesID 	  = $_POST['slidesID'];
$slidesImage  = $_POST['slidesImage'];
$slidesCont   = $_POST['slidesCont'];

// print_r($error);
// if valadation is okay then carry on
if (!$error) {

 
	// escape any harmfull code and prevent sql injection
	$slidesTitle = mysql_real_escape_string($slidesTitle);
	$slidesCont   = mysql_real_escape_string($slidesCont);		

		$slidesTitle = stripslashes($slidesTitle);
	
	   	      
$sql = mysql_query("UPDATE ".PREFIX."slides SET slidesTitle='$slidesTitle', slidesImage='$slidesImage',  slidesCont='$slidesCont' WHERE slidesID='$slidesID'"); 

if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'Slide Updated';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'Slide Updated';
url('manage-add-ons/slides');	
}  
  
  
 
}// close errors
}// close if form sent

//dispaly any errors
errors($error);

$result = mysql_query("SELECT * FROM ".PREFIX."slides WHERE slidesID='{$_GET['edit-slides']}'");
while ($row = mysql_fetch_object($result)){
$row->slidesCont = stripslashes($row->slidesCont);
?>

<form action="" method="post">
<input type="hidden" name="slidesID" value="<?php echo $row->slidesID;?>">

<p><label>Slide Title</label> <input name="slidesTitle" class="box-medium tooltip-right" title="Enter the Slides Title" type="text" value="<?php echo $row->slidesTitle; ?>" size="40" maxlength="255"  /></p>

<p><label>Slide Content</label><textarea name="slidesCont" id="edit2" cols="60" rows="20"><?php echo $row->slidesCont;?></textarea>
</p>

<p><label>Slide Image</label><textarea name="slidesImage" id="edit3" cols="60" rows="20"><?php echo $row->slidesImage; ?></textarea>
</p>

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save page and return to slides">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save page and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save page and return to slides">
</form>
</div>
<?php }
} else {
url(DIRADMIN);

}	
}




function slidesRequest()
{
	if(isset($_GET['slides'])){
	manageslides();	
	}
	
	if(isset($_GET['add-slides'])){
	addslides();
	}
	
	if(isset($_GET['edit-slides'])){
	editslides();
	}
	
	if(isset($_GET['delslides'])){
	delslides();	
	}

}

function delslides() {

$delType = $_GET['delType'];
$delID   = $_GET['delID'];
$delID = mysql_real_escape_string($delID);

if($delType == 'delslides')
{
   $query = mysql_query("DELETE FROM ".PREFIX."slides WHERE slidesID = '$delID'")or die('Error : ' . mysql_error());  
   $_SESSION['success'] = 'Slide Deleted';
   url('manage-add-ons/slides');
}
}



function slides($string) 
{
$result = mysql_query("SELECT * FROM ".PREFIX."slides")or die(mysql_error());
$slidesOutput.="<div class=\"slider\"><div class=\"wrapper\">";
if(mysql_num_rows($result) > 0){ $slidesOutput.="<ul>"; }
while ($r = mysql_fetch_object($result)){
	$r->slidesImage = str_replace("../","",$r->slidesImage);
	$str = preg_replace('#<p>.+?src=[\'"]([^\'"]+)[\'"].+</p>#i', "$1", $r->slidesImage);
	$slidesOutput.="<li style=\"background:url($str) no-repeat scroll 0 0 transparent;\">
				<div>
					<div class=\"slidet\">$r->slidesCont</div>												
				</div>
			</li>";	
}
if(mysql_num_rows($result) > 0){ $slidesOutput.="</ul>"; }
$slidesOutput.="</div></div>
<div class=\"sliderdots\">
    <div class=\"dots\"></div>
</div>";			
		$string = str_replace("[slides]", $slidesOutput, $string);
		return $string;
}


function addLinksslides() {
	echo "bk<div class=\"icon\">\n";
		echo "<a href=\"".DIR."admin/manage-add-ons/slides\"><img src=\"".DIR."assets/plugins/slides/slides.png\" alt=\"Slides\" title=\"Manage Slides\" class=\"tooltip-top\" /></a>\n";
    	echo "<p><a href=\"".DIR."admin/manage-add-ons/slides\" title=\"Manage Slides\" class=\"tooltip-top\">Slides</a></p>\n";
    echo "</div>\n";	
}


//add hook, where to execute a function
add_hook('admin_modules','addLinksslides');
add_hook('cont','slides');
add_hook('del', 'delslides');
add_hook('page_requester','slidesRequest');
?>
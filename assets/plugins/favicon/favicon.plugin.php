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

if (preg_match('/favicon/', $data))
{
} else { 
global $loc;
$newData = "
RewriteRule ^admin/manage-add-ons/favicon$ 	  					  admin.php?favicon=$1 [L]
RewriteRule ^admin/manage-add-ons/favicon/$                       admin.php?favicon=$1 [L]\n###\n";

$data = str_replace("###",$newData,$data);

$myFile = $cfile;
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $data);
fclose($fh);
}


function useFavicon()
{
global $curpage;
$curpage = true;
if (isglobaladmin($prefix) || isadmin($prefix)){
	
echo "<div class=\"content-box-header\"><h3>Favicon</h3></div> 			
<div class=\"content-box-content\">";	
	
echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > Favicon</p></div>";

// if form submitted then process form
if (isset($_POST['sub'])){

if($_FILES['uploaded']['size'] <= 0){
$error[] = 'No image selected.';
}

// location where inital upload will be moved to
$target = "assets/plugins/favicon/" . $_FILES['uploaded']['name'] ;

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

echo "fvsdvdvvvfdv";
	
	//add target location to varible $src_filename 
	$src_filename = $target;
	// define file locations for full sized and thumbnail images

	$dst_filename_thumb = 'assets/plugins/favicon/';
	
// create the images
createico($src_filename, $dst_filename_thumb);
// delete original image as its not needed any more.
unlink ($src_filename);

//out put favicon

$_SESSION['success'] = 'Favicon Updated';
url('manage-add-ons/favicon');



}
}
	
//dispaly any errors
echo errors($error);
echo messages();
?>

<h2>What is a Favicon?</h2>
<p>A Favicon is a small icon 16 x 16 pixels thats sits before the web site title in the address bar.</p>
<p>Favicons have a special extension which is .ico you can use the form below to generate a Favicon for you. Simply upload an image (JPG, GIF or PNG) then it will be converted into a Favicon. </p>


<form enctype="multipart/form-data" action="" method="post">
<p><label>Image:</label><input name="uploaded" class="box-medium tooltip-top" title="Select image" type="file" maxlength="20" /></p>

<p><input type="submit" name="sub" class="button tooltip-right" title="Create favicon" value="Generate Favicon" /></p>
</form>
</div>
<?php 
} else {
header('Location: '.DIR);
exit;
}	
}


function faviconRequest()
{
	if(isset($_GET['favicon'])){
	useFavicon();
	$curpage = true;
	}
}

function Headerfavicon()
{
	return "<link rel=\"shortcut icon\" href=\"".DIR."assets/plugins/favicon/favicon.ico\"/>\n";
}

function addLinksfavicon() {
	echo "bk<div class=\"icon\">\n";
		echo "<a href=\"".DIR."admin/manage-add-ons/favicon\"><img src=\"".DIR."assets/plugins/favicon/favicon.png\" alt=\"Favicon\" title=\"Manage Favicon\" class=\"tooltip-top\" /></a>\n";
    	echo "<p><a href=\"".DIR."admin/manage-add-ons/favicon\" title=\"Manage Favicon\" class=\"tooltip-top\">Favicon</a></p>\n";
    echo "</div>\n";	
}


//add hook, where to execute a function
add_hook('admin_modules','addLinksfavicon');
add_hook('header_css','Headerfavicon');
add_hook('page_requester','faviconRequest');
?>
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

if (preg_match('/code/', $data))
{
} else { 
$newData = "
RewriteRule ^admin/manage-add-ons/code$ 		                admin.php?code=$1 [L]
RewriteRule ^admin/manage-add-ons/code/$		                admin.php?code=$1 [L]

RewriteRule ^admin/manage-add-ons/code/add-code$ 			    admin.php?add-code=$1 [L]
RewriteRule ^admin/manage-add-ons/code/add-code/$			    admin.php?add-code=$1 [L]

RewriteRule ^admin/manage-add-ons/code/edit-code-([^/]+)$ 		admin.php?edit-code=$1 [L]
RewriteRule ^admin/manage-add-ons/code/edit-code-([^/]+)/$		admin.php?edit-code=$1 [L]\n###\n";

$data = str_replace("###",$newData,$data);

$myFile = $cfile;
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $data);
fclose($fh);

mysql_query("CREATE TABLE IF NOT EXISTS ".PREFIX."code (
  codeID int(11) NOT NULL auto_increment,
  codeTitle varchar(255) NOT NULL,
  codeTag varchar(255) NOT NULL,
  codeCont varchar(255) NOT NULL,
  PRIMARY KEY  (codeID)
) ENGINE=MyISAM")or die('cannot make table code due to: '.mysql_error());

}

function managecode()
{
	$curpage = true;	
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Polls</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > Code</p></div>";

echo messages();

echo "<p>To implement the code insert the code tag title in brackets like [news] into any page you want the code to be placed.</p>\n";

$result = mysql_query("SELECT * FROM ".PREFIX."code")or die(mysql_error());
$Rows = mysql_num_rows($result);
?>
<table class="stripeMe">
<tr align="center">
<td align="left"><strong>Title</strong></td>
<td align="left"><strong>code Tag</strong></td>
<td ><strong>Action</strong></td>
</tr>
<?php
while ($row = mysql_fetch_object($result)) {
?>
<tr>
<td><?php echo $row->codeTitle;?></td>
<td><?php echo $row->codeTag;?></td>

<td align="center" valign="top"><a href="<?php echo DIRADMIN;?>manage-add-ons/code/edit-code-<?php echo $row->codeID;?>" class="tooltip-top" title="Edit"><img src="<?php echo DIR;?>assets/images/icons/action-edit.png" alt="Edit" /></a> 

<a href="#" id="<?php echo $row->codeID;?>" rel="delcode" title="<?php echo $row->codeTitle;?>" class="delete_button"><img src="<?php echo DIR;?>assets/images/icons/action-del.png" alt="Delete" /></a></td>
</tr>
<?php
}
?>
</table>
<p><a href="<?php echo DIRADMIN;?>manage-add-ons/code/add-code" class="button tooltip-top" title="Add Code">Add Code</a></p>

</div>
<?php
} else {
url(DIR);

}
}

function addcode()
{
	$curpage = true;
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Add code </h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/code\">code</a> > Add code</p></div>";

echo messages();

if(isset($_POST['cancel'])){
$_SESSION['info'] = "code was not added";
url('manage-add-ons/code');
}

if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$codeTitle = trim($_POST['codeTitle']);
if (strlen($codeTitle) < 1 || strlen($codeTitle) > 255) {
$error[] = 'Title must be at between 1 and 255 charactors.';
}

// if valadation is okay then carry on
if (!$error) {
	
	// post form data 
   $codeTitle     = $_POST['codeTitle'];
   $codeCont       = $_POST['codeCont'];
   
   //strip any tags from input
   $codeTitle   = strip_tags($codeTitle); 
   
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$codeTitle   = addslashes($codeTitle);
	 }
   
   // remove any harhful code and stop sql injection
   $codeTitle = mysql_real_escape_string($codeTitle);
   
 
// insert data into images table
$query = mysql_query("INSERT INTO ".PREFIX."code (codeTitle, codeCont) VALUES ('$codeTitle','$codeCont')");
$getID = mysql_insert_id();
$codeTag = "code$getID";

$query = mysql_query("UPDATE ".PREFIX."code SET codeTag='$codeTag' WHERE codeID='$getID'");
 	
if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'code Added';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'code Added';
url('manage-add-ons/code');
}	
		
 
}
}

	
//dispaly any errors
errors($error);

?>

<form action="" method="post">
<p>Title:<br /><input type="text" class="box-medium tooltip-right" title="Enter code  Title" name="codeTitle" <?php if (isset($error)){ echo "value=\"$codeTitle\""; }?>/></p>

<p>Code:<br /><textarea name="codeCont" cols="60" rows="20" class="box-medium tooltip-right" style="width:90% !important;" title="Enter the code" type="text"><?php if (isset($error)){ echo $row->codeCont; }?></textarea></p>

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save page and return to Manage code ">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save and return to Manage code ">
</form>
</div>
<?php

} else {
url(DIR);
}		
}

function editcode()
{
	$curpage = true;	
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Edit Poll</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/code\">code </a> > Edit code </p></div>";

messages();

if(isset($_POST['cancel'])){
$_SESSION['info'] = "code  was not Updated";
url('manage-add-ons/code');
}

if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$codeTitle = trim($_POST['codeTitle']);
if (strlen($codeTitle) < 1 || strlen($codeTitle) > 255) {
$error[] = 'Title must be at between 1 and 255 charactors.';
}

// if valadation is okay then carry on
if (!$error) {
	
	// post form data 
   $codeID     = $_POST['codeID'];
   $codeTitle     = $_POST['codeTitle'];
   $codeCont       = $_POST['codeCont'];
   
   //strip any tags from input
   $codeTitle   = strip_tags($codeTitle);   
   
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$codeTitle   = addslashes($codeTitle);
	 }
   
   // remove any harhful code and stop sql injection
   $codeTitle = mysql_real_escape_string($codeTitle); 
   $codeID = mysql_real_escape_string($codeID);   
  

// insert data into images table
$query = "UPDATE ".PREFIX."code SET codeTitle = '$codeTitle', codeCont='$codeCont', codeTag='code$codeID' WHERE codeID='$codeID'";
$result  = mysql_query($query) or die (mysql_error());

if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'code  Updated';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'code  Updated';
url('manage-add-ons/code');
}		
 
}
}

	
//dispaly any errors
errors($error);

$result = mysql_query("SELECT * FROM ".PREFIX."code WHERE codeID='{$_GET['edit-code']}'");
while($row = mysql_fetch_object($result)){
?>

<form action="" method="post">
<input type="hidden" name="codeID" value="<?php echo $row->codeID;?>" />

<p>Title:<br /><input type="text" class="box-medium tooltip-right" title="Enter code  Title" name="codeTitle" value="<?php echo $row->codeTitle;?>" /></p>

<p>Code:<br /><textarea name="codeCont" cols="60" rows="20" class="box-medium tooltip-right" style="width:90% !important;" title="Enter the code" type="text"><?php echo $row->codeCont;?></textarea></p>

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save page and return to Manage code ">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save and return to Manage code ">
</form>	
</div>
<?php }

} else {
url(DIR);

}	
}


function delcode() {

$delType = $_GET['delType'];
$delID   = $_GET['delID'];
$delID = mysql_real_escape_string($delID);

if($delType == 'delcode')
{
    $query = "DELETE FROM ".PREFIX."code WHERE codeID = '$delID'";
  	mysql_query($query) or die('Error : ' . mysql_error());

	$_SESSION['success'] = 'Deleted';
	url('manage-add-ons/code');
}
}


function codeRequest()
{
	if(isset($_GET['code'])){
	managecode();
	$curpage = true;
	}
	
	if(isset($_GET['add-code'])){
	addcode();
	$curpage = true;
	}
	
	if(isset($_GET['edit-code'])){
	editcode();
	$curpage = true;
	}
	
	if(isset($_GET['delcode'])){
	delcode();
	$curpage = true;
	}
	
}

function rendercode($string) 
{	

$qsql = mysql_query("SELECT * FROM ".PREFIX."code")or die(mysql_error());
while ($qRow = mysql_fetch_object($qsql))
{ 
	$mystring = $string;
	$findme   = "[$qRow->codeTag]";
	$pos = strpos($mystring, $findme);
	
	if ($pos !== false) {
	
		$qMatch = "[$qRow->codeTag]"; //match against in return string
		$getTag = $qRow->codeTag;
	
		$sql = mysql_query("SELECT * FROM ".PREFIX."code WHERE codeTag='$getTag'");
		$r = mysql_fetch_object($sql);
		
		$codeOutput =$r->codeCont;
			
	}//close if
	
	$string = str_replace("$qMatch", $codeOutput, $string);
} //close first while 
  
  return $string;
}


function addLinkscode() {
	echo "bk<div class=\"icon\">\n";
		echo "<a href=\"".DIR."admin/manage-add-ons/code\"><img src=\"".DIR."assets/plugins/code/code.png\" alt=\"code\" title=\"Manage code s\" class=\"tooltip-top\" /></a>\n";
    	echo "<p><a href=\"".DIR."admin/manage-add-ons/code\" title=\"Manage code\" class=\"tooltip-top\">code </a></p>\n";
    echo "</div>\n";	
}

//add hook, where to execute a function
add_hook('admin_modules','addLinkscode');
add_hook('del', 'delcode');
add_hook('page_requester','codeRequest');
add_hook('cont','rendercode');
?>
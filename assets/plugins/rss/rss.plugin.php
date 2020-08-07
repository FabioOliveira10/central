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

if (preg_match('/rssreader/', $data))
{
} else { 
$newData = "
RewriteRule ^admin/manage-add-ons/rssreader$ 		                admin.php?rssreader=$1 [L]
RewriteRule ^admin/manage-add-ons/rssreader/$		                admin.php?rssreader=$1 [L]

RewriteRule ^admin/manage-add-ons/rssreader/add-rss$ 			    admin.php?add-rss=$1 [L]
RewriteRule ^admin/manage-add-ons/rssreader/add-rss/$			    admin.php?add-rss=$1 [L]

RewriteRule ^admin/manage-add-ons/rssreader/edit-rss-([^/]+)$ 		admin.php?edit-rss=$1 [L]
RewriteRule ^admin/manage-add-ons/rssreader/edit-rss-([^/]+)/$		admin.php?edit-rss=$1 [L]\n###\n";

$data = str_replace("###",$newData,$data);

$myFile = $cfile;
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $data);
fclose($fh);

mysql_query("CREATE TABLE IF NOT EXISTS ".PREFIX."rssreader (
  rssID int(11) NOT NULL auto_increment,
  rssTitle varchar(255) NOT NULL,
  rssTag varchar(255) NOT NULL,
  rssUrl varchar(255) NOT NULL,
  PRIMARY KEY  (rssID)
) ENGINE=MyISAM")or die('cannot make table rss due to: '.mysql_error());

}


function parseRSS($xml)
{
    echo "<h1>".$xml->channel->title."</h1>";
    $cnt = count($xml->channel->item);
    for($i=0; $i<$cnt; $i++)
    {
	$url 	= $xml->channel->item[$i]->link;
	$title 	= $xml->channel->item[$i]->title;
	$desc = $xml->channel->item[$i]->description;
 
	echo "<h3><a href=\"".$url."\" target=\"_blank\">$title</a></h3>";
	echo "<p>$desc</p>";
    }
}


function parseAtom($xml)
{
    echo "<h1>".$xml->author->name."</h1>";
    $cnt = count($xml->entry);
    for($i=0; $i<$cnt; $i++)
    {
	$urlAtt = $xml->entry->link[$i]->attributes();
	$url	= $urlAtt['href'];
	$title 	= $xml->entry->title;
	$desc	= strip_tags($xml->entry->content);
 
	echo "<h3><a href=\"".$url."\" target=\"_blank\">$title</a></h3>";
	echo "<p>$desc</p>";
    }
}

function managerss()
{
	$curpage = true;	
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Polls</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > RSS Reader</p></div>";

echo messages();

echo "<p>To implement the RSS Reader insert the rss tag title in brackets like [newsfeed] into any page you want the reader to be displayed.</p>\n";

$result = mysql_query("SELECT * FROM ".PREFIX."rssreader")or die(mysql_error());
$Rows = mysql_num_rows($result);
?>
<table class="stripeMe">
<tr align="center">
<td align="left"><strong>Title</strong></td>
<td align="left"><strong>RSS Tag</strong></td>
<td ><strong>Action</strong></td>
</tr>
<?php
while ($row = mysql_fetch_object($result)) {
?>
<tr>
<td><?php echo $row->rssTitle;?></td>
<td><?php echo $row->rssTag;?></td>

<td align="center" valign="top"><a href="<?php echo DIRADMIN;?>manage-add-ons/rssreader/edit-rss-<?php echo $row->rssID;?>" class="tooltip-top" title="Edit"><img src="<?php echo DIR;?>assets/images/icons/action-edit.png" alt="Edit" /></a> 

<a href="#" id="<?php echo $row->rssID;?>" rel="delrss" title="<?php echo $row->rssTitle;?>" class="delete_button"><img src="<?php echo DIR;?>assets/images/icons/action-del.png" alt="Delete" /></a></td>
</tr>
<?php
}
?>
</table>
<p><a href="<?php echo DIRADMIN;?>manage-add-ons/rssreader/add-rss" class="button tooltip-top" title="Add RSS Feed">Add RSS Feed</a></p>

</div>
<?php
} else {
url(DIR);

}
}

function addrss()
{
	$curpage = true;
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Add RSS Feed</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/rssreader\">RSS Reader</a> > Add RSS Feed</p></div>";

echo messages();

if(isset($_POST['cancel'])){
$_SESSION['info'] = "RSS Feed was not added";
url('manage-add-ons/rssreader');
}

if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$rssTitle = trim($_POST['rssTitle']);
if (strlen($rssTitle) < 1 || strlen($rssTitle) > 255) {
$error[] = 'Title must be at between 1 and 255 charactors.';
}

$rssUrl = trim($_POST['rssUrl']);
if (strlen($rssUrl) < 1 || strlen($rssUrl) > 255) {
$error[] = 'URL must be at between 1 and 255 charactors.';
}



// if valadation is okay then carry on
if (!$error) {
	
	// post form data 
   $rssTitle     = $_POST['rssTitle'];
   $rssUrl       = $_POST['rssUrl'];
   
   //strip any tags from input
   $rssTitle   = strip_tags($rssTitle); 
   $rssUrl     = strip_tags($rssUrl);  
   
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$rssTitle   = addslashes($rssTitle);
	 }
   
   // remove any harhful code and stop sql injection
   $rssTitle = mysql_real_escape_string($rssTitle);
   
 
// insert data into images table
$query = mysql_query("INSERT INTO ".PREFIX."rssreader (rssTitle, rssUrl) VALUES ('$rssTitle','$rssUrl')");
$getID = mysql_insert_id();
$rssTag = "rss$getID";

$query = mysql_query("UPDATE ".PREFIX."rssreader SET rssTag='$rssTag' WHERE rssID='$getID'");
 	
if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'RSS Feed Added';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'RSS Feed Added';
url('manage-add-ons/rssreader');
}	
		
 
}
}

	
//dispaly any errors
errors($error);

?>

<form action="" method="post">
<p>Title:<br /><input type="text" class="box-medium tooltip-right" title="Enter RSS Feed Title" name="rssTitle" <?php if (isset($error)){ echo "value=\"$rssTitle\""; }?>/></p>

<p>URL:<br /><input type="text" class="box-medium tooltip-right" title="Enter the RSS Feed URL suc has http://www.example.com/rss.xml" name="rssUrl" <?php if (isset($error)){ echo "value=\"$rssUrl\""; }?>/></p>

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save page and return to Manage RSS Reader">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save and return to Manage RSS Reader">
</form>
</div>
<?php

} else {
url(DIR);
}		
}

function editrss()
{
	$curpage = true;	
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Edit Poll</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/rssreader\">RSS Reader</a> > Edit RSS Feed</p></div>";

messages();

if(isset($_POST['cancel'])){
$_SESSION['info'] = "RSS Feed was not Updated";
url('manage-add-ons/polls');
}

if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$rssTitle = trim($_POST['rssTitle']);
if (strlen($rssTitle) < 1 || strlen($rssTitle) > 255) {
$error[] = 'Title must be at between 1 and 255 charactors.';
}

$rssUrl = trim($_POST['rssUrl']);
if (strlen($rssUrl) < 1 || strlen($rssUrl) > 255) {
$error[] = 'URL must be at between 1 and 255 charactors.';
}

// if valadation is okay then carry on
if (!$error) {
	
	// post form data 
   $rssID     = $_POST['rssID'];
   $rssTitle     = $_POST['rssTitle'];
   $rssUrl       = $_POST['rssUrl'];
   
   //strip any tags from input
   $rssTitle   = strip_tags($rssTitle); 
   $rssUrl     = strip_tags($rssUrl);   
   
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$rssTitle   = addslashes($rssTitle);
	 }
   
   // remove any harhful code and stop sql injection
   $rssTitle = mysql_real_escape_string($rssTitle); 
   $rssID = mysql_real_escape_string($rssID);   
  

// insert data into images table
$query = "UPDATE ".PREFIX."rssreader SET rssTitle = '$rssTitle', rssUrl='$rssUrl', rssTag='rss$rssID' WHERE rssID='$rssID'";
$result  = mysql_query($query) or die (mysql_error());

if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'RSS Feed Updated';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'RSS Feed Updated';
url('manage-add-ons/rssreader');
}		
 
}
}

	
//dispaly any errors
errors($error);

$result = mysql_query("SELECT * FROM ".PREFIX."rssreader WHERE rssID='{$_GET['edit-rss']}'");
while($row = mysql_fetch_object($result)){
?>

<form action="" method="post">
<input type="hidden" name="rssID" value="<?php echo $row->rssID;?>" />

<p>Title:<br /><input type="text" class="box-medium tooltip-right" title="Enter RSS Feed Title" name="rssTitle" value="<?php echo $row->rssTitle;?>" /></p>

<p>URL:<br /><input type="text" class="box-medium tooltip-right" title="Enter the RSS Feed URL suc has http://www.example.com/rss.xml" name="rssUrl" value="<?php echo $row->rssUrl;?>" /></p>

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save page and return to Manage RSS Reader">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save and return to Manage RSS Reader">
</form>	
</div>
<?php }

} else {
url(DIR);

}	
}


function delrss() {

$delType = $_GET['delType'];
$delID   = $_GET['delID'];
$delID = mysql_real_escape_string($delID);

if($delType == 'delrss')
{
    $query = "DELETE FROM ".PREFIX."rssreader WHERE rssID = '$delID'";
  	mysql_query($query) or die('Error : ' . mysql_error());

	$_SESSION['success'] = 'Deleted';
	url('manage-add-ons/rssreader');
}
}


function rssRequest()
{
	if(isset($_GET['rssreader'])){
	managerss();
	$curpage = true;
	}
	
	if(isset($_GET['add-rss'])){
	addrss();
	$curpage = true;
	}
	
	if(isset($_GET['edit-rss'])){
	editrss();
	$curpage = true;
	}
	
	if(isset($_GET['delrss'])){
	delrss();
	$curpage = true;
	}
	
}

function renderRSS($string) 
{	


$qsql = mysql_query("SELECT * FROM ".PREFIX."rssreader")or die(mysql_error());
while ($qRow = mysql_fetch_object($qsql))
{ 
	$mystring = $string;
	$findme   = "[$qRow->rssTag]";
	$pos = strpos($mystring, $findme);
	
	if ($pos !== false) {
	
		$qMatch = "[$qRow->rssTag]"; //match against in return string
		$getTag = $qRow->rssTag;
	
		$sql = mysql_query("SELECT * FROM ".PREFIX."rssreader WHERE rssTag='$getTag'");
		$r = mysql_fetch_object($sql);
		
		$rssOutput ="Feed: $r->rssTitle";
		
		$ch = curl_init($r->rssUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$data = curl_exec($ch);
		curl_close($ch);
		
		$doc = new SimpleXmlElement($data, LIBXML_NOCDATA);
		
		ob_start();
		if(isset($doc->channel))
		{
			parseRSS($doc);
		}
		if(isset($doc->entry))
		{
			parseAtom($doc);
		}
		$rssOutput.= ob_get_clean();	
	
	}//close if
	
	$string = str_replace("$qMatch", $rssOutput, $string);
} //close first while 
  
  return $string;
}


function addLinksrss() {
	echo "bk<div class=\"icon\">\n";
		echo "<a href=\"".DIR."admin/manage-add-ons/rssreader\"><img src=\"".DIR."assets/plugins/rss/rss.png\" alt=\"RSS\" title=\"Manage RSS Feeds\" class=\"tooltip-top\" /></a>\n";
    	echo "<p><a href=\"".DIR."admin/manage-add-ons/rssreader\" title=\"Manage RSS Feeds\" class=\"tooltip-top\">RSS Reader</a></p>\n";
    echo "</div>\n";	
}

//add hook, where to execute a function
add_hook('admin_modules','addLinksrss');
add_hook('del', 'delrss');
add_hook('page_requester','rssRequest');
add_hook('cont','renderRSS');
?>
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

if (preg_match('/mailing-list/', $data))
{
} else { 
$newData = "
RewriteRule ^admin/manage-add-ons/mailing-list$ 			            admin.php?mailing-list=$1 [L]
RewriteRule ^admin/manage-add-ons/mailing-list/$			            admin.php?mailing-list=$1 [L]
###";

$data = str_replace("###",$newData,$data);

$myFile = $cfile;
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $data);
fclose($fh);

mysql_query("CREATE TABLE IF NOT EXISTS `".PREFIX."mailinglist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1")or die('cannot make table mailing-list due to: '.mysql_error());

}


function managemailinglist()
{
	$curpage = true;	
	if (isglobaladmin() || isadmin()){
	
	
echo "<div class=\"content-box-header\"><h3>Mailing list</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > Mailing List</p></div>";

echo messages();

echo "<p>To implement the mailing list signup form insert [mailinglistform] into any page you want the form to be displayed.</p>\n";

$result = mysql_query("SELECT * FROM ".PREFIX."mailinglist")or die(mysql_error());
$Rows = mysql_num_rows($result);
?>
<table class="stripeMe">
<tr align="center">
<td align="left"><strong>name</strong></td>
<td align="left"><strong>email</strong></td>
<td ><strong>Action</strong></td>
</tr>
<?php
while ($row = mysql_fetch_object($result)) {
?>
<tr>
<td><?php echo $row->name;?></td>
<td><?php echo $row->email;?></td>

<td align="center" valign="top"><a href="<?php echo DIRADMIN;?>manage-add-ons/mailing-list/edit-poll-<?php echo $row->qID;?>" class="tooltip-top" title="Edit"><img src="<?php echo DIR;?>assets/images/icons/action-edit.png" alt="Edit" /></a> 

<a href="#" id="<?php echo $row->id;?>" rel="delmailinglistentry" title="<?php echo "$row->name ($row->email)";?>" class="delete_button"><img src="<?php echo DIR;?>assets/images/icons/action-del.png" alt="Delete" /></a></td>
</tr>
<?php
}
?>
</table>
<p><a href="<?php echo DIR;?>assets/includes/download.php?export" class="button tooltip-top" title="Download all contacts">Export all entries to Microsoft Excell</a></p>

</div>
<?php
} else {
url(DIR);

}
}


function mailinglistRequest()
{
	if(isset($_GET['mailing-list'])){
	managemailinglist();
	$curpage = true;
	}
	
}

function delmailinglistentry() {

$delType = $_GET['delType'];
$delID   = $_GET['delID'];
$delID = mysql_real_escape_string($delID);

if($delType == 'delmailinglistentry')
{
    $query = "DELETE FROM ".PREFIX."mailinglist WHERE id = '$delID'";
  	mysql_query($query) or die('Error : ' . mysql_error());

	$_SESSION['success'] = 'Entry Deleted';
	url('manage-add-ons/mailing-list');
}
}


function addLinksmailinglist() {
	echo "bk<div class=\"icon\">\n";
		echo "<a href=\"".DIR."admin/manage-add-ons/mailing-list\"><img src=\"".DIR."assets/plugins/mailing-list/mail.png\" alt=\"mailing-list\" title=\"Manage mailing-list\" class=\"tooltip-top\" /></a>\n";
    	echo "<p><a href=\"".DIR."admin/manage-add-ons/mailing-list\" title=\"Manage mailing-list\" class=\"tooltip-top\">Mailing List</a></p>\n";
    echo "</div>\n";	
}

//add hook, where to execute a function
add_hook('admin_modules','addLinksmailinglist');
add_hook('del', 'delmailinglistentry');
add_hook('page_requester','mailinglistRequest');
?>
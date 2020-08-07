<?php
/*-------------------------------------------------------+
| The One Point - Content Management System
| http://www.theonepoint.co.uk/
+--------------------------------------------------------+
| Author: David Carr  Email: d.carr@theonepoint.co.uk
+--------------------------------------------------------+*/

if (!defined('included')){
die('You cannot access this file directly!');
}

if (isglobaladmin() || isadmin() || iseditor()){
	
echo "<div class=\"content-box-header\"><h3>Site Settings</h3></div> 			
<div class=\"content-box-content\">";

echo "<p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."settings\">Settings</a> > Site Settings</p>";

if(isset($_POST['cancel'])){
$_SESSION['info'] = "Settings were not updated";
url('manage-sidebar-panels');
}

if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// if valadation is okay then carry on
if (!$error) {

	// post form data
  $siteTitle 	       = $_POST['siteTitle'];
  $siteAddress         = $_POST['siteAddress'];
  $siteEmail           = $_POST['siteEmail'];  
  $siteSettingsAddress = $_POST['siteSettingsAddress'];  
  $siteEditorAddress   = $_POST['siteEditorAddress'];
     
$sql = "UPDATE ".PREFIX."settings SET siteTitle='$siteTitle', siteAddress='$siteAddress', siteEmail='$siteEmail', siteSettingsAddress='$siteSettingsAddress', siteEditorAddress='$siteEditorAddress'"; 
$resultupdate = mysql_query($sql)or die(mysql_error());

if(isset($_POST['backsubmit'])){
$_SESSION['success'] = "Settings Updated";
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = "Settings Updated";
url('manage-sidebar-panels');
}
 
}// close errors

}// close if form sent

//dispaly any errors
errors($error);

$result = mysql_query("SELECT * FROM ".PREFIX."settings");
while($row = mysql_fetch_object($result)){
?>

<form action="" method="post">

<p><label>Site Title</label> <input class="box-medium tooltip-right" title="Set the title that will appear at the very top of the page" name="siteTitle" type="text" value="<?php echo $row->siteTitle;?>" size="40" maxlength="255"  /></p>

<p><label>Site Address</label> <input class="box-medium tooltip-right" title="Enter the site URL with a ending /" name="siteAddress" type="text" value="<?php echo $row->siteAddress;?>" size="40" maxlength="255"  /></p>

<p><label>Site Email</label> <input class="box-medium tooltip-right" title="Set the site's email address" name="siteEmail" type="text" value="<?php echo $row->siteEmail;?>" size="40" maxlength="255"  /></p>

<p><label>Settings Address</label> <input class="box-medium tooltip-right" title="Set the address for the admin settings" name="siteSettingsAddress" type="text" value="<?php echo $row->siteSettingsAddress;?>" size="40" maxlength="255"  /></p>

<p><label>Editor Address</label> <input class="box-medium tooltip-right" title="Set the address used for the editor" name="siteEditorAddress" type="text" value="<?php echo $row->siteEditorAddress;?>" size="40" maxlength="255"  /></p>

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save page and return to settings">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save page and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save page and return to settings">
</form>
</div>
<?php
}
} else {
header('Location: '.DIRADMIN);
exit;
}
?>
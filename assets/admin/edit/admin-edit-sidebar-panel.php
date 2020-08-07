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
	
echo "<div class=\"content-box-header\"><h3>Edit Sidebar Panel</h3></div> 			
<div class=\"content-box-content\">";

echo "<p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-sidebar-panels\">Manage Sidebar Panels</a> > Edit Sidebar Panel</p>";

if(isset($_POST['cancel'])){
$_SESSION['info'] = $_POST['sidebarTitle']." was not updated";
url('manage-sidebar-panels');
}

if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$sidebarTitle = trim($_POST['sidebarTitle']);
if (strlen($sidebarTitle) < 3 || strlen($sidebarTitle) > 255) {
$error[] = 'sidebarTitle Must be between 3 and 255 characters.';
}

$sidebarCont = trim($_POST['sidebarCont']);
if (strlen($sidebarCont) < 3) {
$error[] = 'Content must be more then 3 characters.';
}

// if valadation is okay then carry on
if (!$error) {

	// post form data
  $sidebarID 	= $_POST['sidebarID'];
  $sidebarTitle = $_POST['sidebarTitle'];
  $sidebarCont  = $_POST['sidebarCont'];  
  $sidebarPos   = $_POST['sidebarPos']; 
 
   
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$sidebarTitle   = addslashes($sidebarTitle);
	$sidebarCont    = addslashes($sidebarCont);
	 }
	 
		      
$sql = "UPDATE ".PREFIX."sidebars SET sidebarTitle='$sidebarTitle', sidebarCont='$sidebarCont' WHERE sidebarID='$sidebarID'"; 
$resultupdate = mysql_query($sql)or die(mysql_error());

if(isset($_POST['backsubmit'])){
$_SESSION['success'] = "$sidebarTitle Updated";
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = "$sidebarTitle Updated";
url('manage-sidebar-panels');
}
 
}// close errors

}// close if form sent

//dispaly any errors
errors($error);

$result = mysql_query("SELECT * FROM ".PREFIX."sidebars WHERE sidebarID='{$_GET['edit-sidebar-panel']}'");
while($row = mysql_fetch_object($result)){
?>

<form action="" method="post">
<input type="hidden" name="sidebarID" value="<?php echo $row->sidebarID;?>" />

<p><label>Title</label> <input class="box-medium tooltip-bottom" title="Provide a name for the sidebar" name="sidebarTitle" type="text" value="<?php echo $row->sidebarTitle; ?>" size="40" maxlength="255"  /></p>

<p><span>Content<br /></span><textarea name="sidebarCont" id="sidebarCont" cols="60" rows="5"><?php echo $row->sidebarCont;?></textarea></p>

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save page and return to manage sidebars">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save page and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save page and return to manage sidebars">
</form>
</div>
<?php
}
} else {
header('Location: '.DIRADMIN);
exit;
}
?>
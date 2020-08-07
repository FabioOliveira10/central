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
	
echo "<div class=\"content-box-header\"><h3>Edit Template</h3></div> 			
<div class=\"content-box-content\">";

echo "<p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."themes\">Templates</a> > Edit Template</p>";

if(isset($_POST['cancel'])){
$_SESSION['info'] = "Template was not updated";
url('themes');
}

if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$sidebarTitle = trim($_POST['sidebarTitle']);
if (strlen($sidebarTitle) < 3 || strlen($sidebarTitle) > 255) {
$error[] = 'sidebarTitle Must be between 3 and 255 characters.';
}

// if valadation is okay then carry on
if (!$error) {

	// post form data
  $sidebarTitle 	      = $_POST['sidebarTitle'];
  $sidebarCont      = $_POST['sidebarCont'];  
 


if(isset($_POST['backsubmit'])){
$_SESSION['success'] = "$sidebarTitle Updated";
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = "$sidebarTitle Updated";
url('themes');
}
 
}// close errors

}// close if form sent

//dispaly any errors
errors($error);
$id = mysql_real_escape_string($_GET['edit-theme']);
$q = mysql_query("SELECT * FROM ".PREFIX."styles WHERE styleID='$id'");
$r = mysql_fetch_object($q);
?>

<form action="" method="post">
<input type="hidden" name="styleID" value="<?php echo $row->styleID;?>" />

<p><label>Title</label> <input class="box-medium tooltip-right" title="Enter the name of the template, no spaces or special characters." name="themeTitle" type="text" value="<?php echo $r->themeTitle; ?>" size="40" maxlength="255"  />
</p>

<p><span>Content<br /></span><textarea name="themeCont" class="ta-default tooltip-top" title="Enter the HTML markup of the template" rows="20" style="width:98%;"><?php echo include("assets/templates/".$r->themeTitle);?></textarea>
</p>

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save page and return to manage sidebars">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save page and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save page and return to manage sidebars">
</form>
</div>
<?php
} else {
header('Location: '.DIRADMIN);
exit;
}
?>
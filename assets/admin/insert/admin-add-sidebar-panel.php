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
	
echo "<div class=\"content-box-header\"><h3>Add Sidebar Panel</h3></div> 			
<div class=\"content-box-content\">";

echo "<p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-sidebar-panels\">Manage Sidebar Panels</a> > Add Sidebar Panel</p>";

if(isset($_POST['cancel'])){
$_SESSION['info'] = $_POST['sidebarTitle']." was not created";
url('manage-sidebar-panels');
}

if (isset($_POST['ssubmit']) || isset($_POST['backsubmit'])){

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
   $sidebarTitle  = $_POST['sidebarTitle'];
  $sidebarCont    = $_POST['sidebarCont'];  
 
   
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$sidebarTitle    = addslashes($sidebarTitle);
	$sidebarCont     = addslashes($sidebarCont);
	 }
	 
		      
$sql = mysql_query("INSERT INTO ".PREFIX."sidebars (sidebarTitle, sidebarCont) VALUES ('$sidebarTitle', '$sidebarCont')"); 
$id = mysql_insert_id();
$num = mysql_result(mysql_query("SELECT COUNT(*) as Num FROM ".PREFIX."sidebars "),0);
$sql = mysql_query("UPDATE ".PREFIX."sidebars  SET sidebarOrder='$num' WHERE sidebarID='$id'"); 


if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'Sidebar Panel Added';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['ssubmit'])){
$_SESSION['success'] = 'Sidebar Panel Added';
url('manage-sidebar-panels');
}
 
}// close errors
}// close if form sent

//dispaly any errors
errors($error);

?>

<form action="" method="post">

<p><label>Title</label> <input class="box-medium tooltip-bottom" name="sidebarTitle" type="text" value="<?php if(isset($error)){ echo $sidebarTitle; } ?>" size="40" maxlength="255" title="Provide a name for the sidebar" />
</p>

<p><span>Content<br /></span> <textarea class="ta-default tooltip-top" name="sidebarCont" id="sidebarCont" cols="60" rows="5"><?php if(isset($error)){  echo $sidebarCont; }?></textarea>
</p>

<input type="submit" name="ssubmit" class="button tooltip-top" value="Submit" title="Save page and return to manage sidebars">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save page and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save page and return to manage sidebars">
</form>
</div>
<?php
} else {
header('Location: '.DIRADMIN);
exit;
}?>
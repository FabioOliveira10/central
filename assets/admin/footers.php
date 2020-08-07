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
	
echo "<div class=\"content-box-header\"><h3>Manage Footers</h3></div> 			
<div class=\"content-box-content\">";

echo "<p><a href=\"".DIRADMIN."\">Home</a> > Manage Footers</p>";

if(isset($_POST['cancel'])){
$_SESSION['info'] = "Footers were not updated";
url();
}

if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// if valadation is okay then carry on
if (!$error) {

	// post form data
  $id 	= mysql_real_escape_string($_POST['id']);
  $box1 = mysql_real_escape_string($_POST['box1']);
  $box2 = mysql_real_escape_string($_POST['box2']); 
     
$sql = "UPDATE ".PREFIX."footers SET box1='$box1', box2='$box2' WHERE id='$id'"; 
$resultupdate = mysql_query($sql)or die(mysql_error());

if(isset($_POST['backsubmit'])){
$_SESSION['success'] = "Footers Updated";
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = "Footers Updated";
url();
}
 
}// close errors

}// close if form sent

//dispaly any errors
errors($error);

$result = mysql_query("SELECT * FROM ".PREFIX."footers WHERE id='1'");
while($row = mysql_fetch_object($result)){
?>

<form action="" method="post">
<input type="hidden" name="id" value="<?php echo $row->id;?>" />

<p><span>Left Footer<br /></span><textarea name="box1" id="box1" cols="80" rows="15"><?php echo stripslashes($row->box1);?></textarea></p>
<p><span>Right Footer<br /></span><textarea name="box2" id="box2" cols="80" rows="15"><?php echo stripslashes($row->box2);?></textarea></p>


<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save page and return to admin">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save page and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save page and return to admin">
</form>
</div>
<?php
}
} else {
header('Location: '.DIRADMIN);
exit;
}
?>
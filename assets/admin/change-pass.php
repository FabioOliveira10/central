<div class="pwd">
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
	
echo "<div class=\"content-box-header\"><h3>Change Password</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."settings\">Settings</a> > Change Password</p></div>";

if(isset($_POST['updatepass']))
{

// check feilds are not empty
$password = trim($_POST['password']);
if (strlen($password) < 5) {
$error[] = 'Current password Must be between 5 and 20 charactors.';
}
if (strlen($password) > 20) {
$error[] = 'Current password Must be between 5 and 20 charactors.';
}

$password   = $_POST['password'];

$salt = 'fjsj560';	
$pass = md5($salt . md5($password . $salt));


 // check if the password exist in database
$query = "SELECT password FROM ".PREFIX."members WHERE memberID = '". $_SESSION['member'] ."' AND password = '$pass'";
$result = mysql_query($query) or die('Query failed. ' . mysql_error());

if (mysql_num_rows($result) !== 1) {
$error[] = 'Sorry wrong current password please try again.';
}
	  

// check feilds are not empty
$newpassword = trim($_POST['newpassword']);
if (strlen($newpassword) < 5) {
$error[] = 'New password Must be between 5 and 20 charactors.';
}
if (strlen($newpassword) > 20) {
$error[] = 'New password Must be between 5 and 20 charactors.';
}

// check feilds are not empty
$newpassword2 = trim($_POST['newpassword2']);
if (strlen($newpassword2) < 5) {
$error[] = 'confirm password Must be between 5 and 20 charactors.';
}
if (strlen($newpassword2) > 20) {
$error[] = 'confirm password Must be between 5 and 20 charactors.';
}

// this makes sure both passwords entered match
if ($_POST['newpassword'] != $_POST['newpassword2']) {
$error[] = 'Your new passwords did not match.';
}

if (!$error){

$newpassword   = $_POST['newpassword'];

	if(!get_magic_quotes_gpc())
	{
		$newpassword = addslashes($newpassword);				
	}	
	
  $salt = 'fjsj560';	
  $pass = md5($salt . md5($newpassword . $salt));
	
	// update the article in the database
	$query = "UPDATE ".PREFIX."members SET password = '$pass' WHERE memberID = '". $_SESSION['member'] ."' ";
	mysql_query($query) or die('Error : ' . mysql_error());

$_SESSION['success'] = 'Password updated';
url('settings');
	
	// now we will display $title & content
	// so strip out any slashes
		
		$password   = stripslashes($password);
} // end no error
} // end submit

//dispaly any errors
errors($error);

?>

<form action="" method="post">

<p><label>Current Password:</label>
<input name="password" class="box-medium tooltip-right" title="Provide your existing password" type="password" size="40" maxlength="40" />
</p>

<p><label>New Password:</label>
<input name="newpassword" class="box-medium tooltip-right" title="Create a new password" type="password" size="40" maxlength="40" />
</p>

<p><label>Confirm Password:</label>
  <input name="newpassword2" class="box-medium tooltip-right" title="Confirm the new password" type="password" size="40" maxlength="40" />
</p>


<p><input type="submit" class="button tooltip-right" title="Save Changes" name="updatepass" value="Change Password"></p>

</form>
</div>
</div>
<?php
} else {
header('Location: '.DIRADMIN);
exit;
}?>
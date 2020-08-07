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
	
echo "<div class=\"content-box-header\"><h3>Admin Details</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."settings\">Settings</a> > Admin Details</p></div>";
	
if(isset($_POST['updateprofile']))
{
// check feilds are not empty
$username = trim($_POST['username']);
if (strlen($username) < 3) {
$error[] = 'username Must be between 3 and 20 charactors.';
}
if (strlen($username) > 20) {
$error[] = 'username Must be between 3 and 20 charactors.';
}

$result2 = mysql_query("SELECT * FROM ".PREFIX."members WHERE memberID = '{$_GET['edit-user']}' ")or die(mysql_error());
while ($row2 = mysql_fetch_object($result2)) {

if($_POST['username'] !== $row2->username){

$sql = "SELECT * FROM ".PREFIX."members WHERE username = '$username' ";
$result = mysql_query($sql) or die('Query failed. ' . mysql_error());
   
   if (mysql_num_rows($result) == 1) {
      // the user id and password match,
$error[] = 'Username already exists please choose another username.';
}
}
}

$email = $_POST['email'];
$pattern = '/^[^@]+@[^\s\r\n\'";,@%]+$/';
if (!preg_match($pattern, trim($email))) {
  $error[] = 'Please enter a valid email address';
  }  

$result2 = mysql_query("SELECT * FROM ".PREFIX."members WHERE memberID = '{$_GET['edit-user']}' ")or die(mysql_error());
while ($row2 = mysql_fetch_object($result2)) {

if($_POST['email'] !== $row2->email){

$sql = "SELECT * FROM ".PREFIX."members WHERE email = '$email' ";
$result = mysql_query($sql) or die('Query failed. ' . mysql_error());
   
   if (mysql_num_rows($result) == 1) {
      // the user id and password match,
$error[] = 'Sorry, the email address <b>'.$_POST['email'].'</b> is already in use, Please choose another email address.';
}
}
}

// if valadation is okay then carry on
if (!isset($error)) {
    
    $username  = $_POST['username'];
	$email     = $_POST['email'];
		
	
	if(!get_magic_quotes_gpc())
	{
		$username   = addslashes($username);
		$email      = addslashes($email);
		
				
	}	
		// escape any harmfull code and prevent sql injection
		$username = mysql_real_escape_string($username);
		$email    = mysql_real_escape_string($email);
				
		// escape any harmfull code and prevent sql injection
		$username = strip_tags($username);
		$email    = strip_tags($email);		
		

	// update the article in the database
	$query = "UPDATE ".PREFIX."members SET username = '$username', email  = '$email' WHERE memberID = '". $_SESSION['member'] ."' ";
	mysql_query($query) or die('Error : ' . mysql_error());
	
$_SESSION['success'] = 'Details updated';
url('settings');
	
} // if not error
} // end if submitted	
	

//dispaly any errors
errors($error);

$result = mysql_query("SELECT * FROM ".PREFIX."members WHERE memberID = '". $_SESSION['member'] ."' ")or die(mysql_error());
$Rows = mysql_num_rows($result);
while ($row = mysql_fetch_object($result)) {
?>


<form action="" method="post">
<p><label>Username</label> <input class="box-medium tooltip-right" title="Update Username" name="username" type="text" value="<?php echo $row->username;?>" size="40" maxlength="40"  />
</p>

<p><label>email </label><input class="box-medium tooltip-right" title="Update Email Address" name="email" type="text" value="<?php echo $row->email;?>" size="40" maxlength="255"  />
</p>

<input type="submit" class="button tooltip-right" title="Save Changes" name="updateprofile" value="Update Details">
</div>
<?php } 
} else {
header('Location: '.DIRADMIN);
exit;
}?>
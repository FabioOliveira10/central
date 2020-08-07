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

if(isglobaladmin($prefix)){
	
echo "<div class=\"content-box-header\"><h3>Edit User</h3></div> 			
<div class=\"content-box-content\">";

echo "<p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."settings\">Settings</a> > <a href=\"".DIRADMIN."manage-users\">Manage Users</a> > Edit User</p>";

if(isset($_POST['cancel'])){
$_SESSION['info'] = "User was not updated";
url('manage-users');
}

if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

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

$userlevel = $_POST['userlevel'];
if ($userlevel == ''){
$error[] = 'Please select a user level';
}

// if valadation is okay then carry on
if (!isset($error)) {


    // post form data
   $memberID  = $_POST['memberID']; 	
   $username  = trim($_POST['username']);
   $email     = $_POST['email'];
   $userlevel = $_POST['userlevel'];
   $active    = $_POST['active'];
   
	   if(!get_magic_quotes_gpc())
   {
      $username  = addslashes($username);
	  $email     = addslashes($email);
   }
	
	// escape any harmfull code and prevent sql injection
		$username  = mysql_real_escape_string($username);
		$email     = mysql_real_escape_string($email);
				
		
		// escape any harmfull code and prevent sql injection
		$username  = strip_tags($username);
		$email     = strip_tags($email);				
		
	   $username  = str_replace("\"\"", '', $username);
	   $username  = str_replace("?", '', $username);
	   $username  = str_replace("/", '', $username);
	   $username  = str_replace("!", '', $username);
	   $username  = str_replace(".", '', $username);
	   $username  = str_replace(",", '', $username);
	   $username  = str_replace("@", '', $username);
	   $username  = str_replace("_", '', $username);
		
	     
	   
		
	$query = "UPDATE ".PREFIX."members  SET username = '$username', email = '$email', level = '$userlevel', active='$active' WHERE memberID ='$memberID'";
	mysql_query($query) or die('Error : ' . mysql_error());	
	
	
if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'User updated';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'User updated';
url('manage-users');
}

 
}// close errors
}// close if form sent

//dispaly any errors
errors($error);

$result = mysql_query("SELECT * FROM ".PREFIX."members WHERE memberID = '{$_GET['edit-user']}'")or die (mysql_error());
while ($row = mysql_fetch_object($result)){

?>

<form action="" method="post">
<input type="hidden" name="memberID" value="<?php echo $row->memberID;?>" />


<p><label>Username</label><input class="box-medium tooltip-right" title="Update Username" name="username" type="text" value="<?php echo $row->username;?>" size="50" maxlength="255">
</p>

<p><label>Email</label><input class="box-medium tooltip-right" title="Update Email Address" name="email" type="text" value="<?php echo $row->email;?>" size="50" maxlength="255">
</p>

<p><label>User Level:</label>
<select name="userlevel" class="box-medium tooltip-right" title="Select Access Level">
	<option value=''>Please select a user level</option>
    <option value="0"<?php if($row->level ==0) { echo "selected='selected'"; }?>>Global Admin</option>
    <option value="1"<?php if($row->level ==1) { echo "selected='selected'"; }?>>Admin</option> 
	<option value="2"<?php if($row->level ==2) { echo "selected='selected'"; }?>>Editor</option>    
</select>
</p>

<p><label>User Active:</label>
<select name="active" class="box-medium tooltip-right" title="If user is not active they cannot login">
	<option value="1"<?php if($row->active ==1) { echo "selected='selected'"; }?>>Yes</option> 
    <option value="0"<?php if($row->active ==0) { echo "selected='selected'"; }?>>No</option>   
</select>
</p>

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save page and return to manage users">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save page and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save page and return to manage users">
</form>
</div>
<?php } 
 } else {
header('Location: '.DIRADMIN);
exit;
}?>
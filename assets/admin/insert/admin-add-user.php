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

if(isglobaladmin()){
	
echo "<div class=\"content-box-header\"><h3>Add User</h3></div> 			
<div class=\"content-box-content\">";

echo "<p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."settings\">Settings</a> > <a href=\"".DIRADMIN."manage-users\">Manage Users</a> > Add User</p>";

if(isset($_POST['cancel'])){
$_SESSION['info'] = "A new user was not created";
url('manage-users');
}

if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$username = trim($_POST['username']);
if (strlen($username) < 3) {
$error[] = 'username Must be between 3 and 50 charactors.';
}
if (strlen($username) > 50) {
$error[] = 'username Must be between 3 and 50 charactors.';
}

// checks if the username is in use
if (!get_magic_quotes_gpc()) {
$_POST[] = addslashes($_POST['username']);
}
$usercheck = $_POST['username'];
$check = mysql_query("SELECT username FROM ".PREFIX."members WHERE username = '$usercheck'") 
or die(mysql_error());
$check2 = mysql_num_rows($check);

//if the name exists it gives an error
if ($check2 != 0) {
$error[] = 'Sorry, the username <b>'.$_POST['username'].'</b> is already in use.';
}

$password = trim($_POST['password']);
if (strlen($password) < 3) {
$error[] = 'password Must be between 3 and 20 charactors.';
}
if (strlen($password) > 20) {
$error[] = 'password Must be between 3 and 20 charactors.';
}

// check feilds are not empty
$password2 = trim($_POST['password2']);
if (strlen($password2) < 3) {
$error[] = 'confirm password Must be between 3 and 20 charactors.';
}
if (strlen($password2) > 20) {
$error[] = 'confirm password Must be between 5 and 20 charactors.';
}

// this makes sure both passwords entered match
if ($_POST['password'] != $_POST['password2']) {
$error[] = 'Your passwords did not match.';
}

$email = $_POST['email'];
$pattern = '/^[^@]+@[^\s\r\n\'";,@%]+$/';
if (!preg_match($pattern, trim($email))) {
  $error[] = 'Please enter a valid email address';
  }  

// checks if the email is in use
if (!get_magic_quotes_gpc()) {
$_POST[] = addslashes($_POST['email']);
}
$emailcheck = $_POST['email'];
$emailcheck1 = mysql_query("SELECT email FROM ".PREFIX."members WHERE email = '$emailcheck'") 
or die(mysql_error());
$emailcheck2 = mysql_num_rows($emailcheck1);

//if the name exists it gives an error
if ($emailcheck2 != 0) {
$error[] = 'Sorry, the email address <b>'.$_POST['email'].'</b> is already in use, Please choose another email address.';
}

$userlevel = $_POST['userlevel'];
if ($userlevel == ''){
$error[] = 'Please select a user level';
}

// if valadation is okay then carry on
if (!isset($error)) {


    // post form data
   $username = trim($_POST['username']);
   $password = $_POST['password'];
   $email    = $_POST['email'];
   $userlevel = $_POST['userlevel'];
   
	   if(!get_magic_quotes_gpc())
   {
      $username  = addslashes($username);
	  $password  = addslashes($password);
	  $email     = addslashes($email);	 
    }
	
	// escape any harmfull code and prevent sql injection
		$username  = mysql_real_escape_string($username);
		$password  = mysql_real_escape_string($password);
		$email     = mysql_real_escape_string($email);		
		
		
		// escape any harmfull code and prevent sql injection
		$username  = strip_tags($username);
		$password  = strip_tags($password);
		$email     = strip_tags($email);
					
				
		
	   $username  = strtolower(str_replace("\"\"", '', $username));
	   $username  = strtolower(str_replace("?", '', $username));
	   $username  = strtolower(str_replace("/", '', $username));
	   $username  = strtolower(str_replace("!", '', $username));
	   $username  = strtolower(str_replace(".", '', $username));
	   $username  = strtolower(str_replace(",", '', $username));
	   $username  = strtolower(str_replace("@", '', $username));
	   $username  = strtolower(str_replace("_", '', $username));
		
	     
$salt = 'fjsj560';	
$password = md5($salt . md5($password . $salt));	   
		
	$query = "INSERT INTO ".PREFIX."members (username, password, email, level) VALUES ('$username','$password', '$email', '$userlevel')";
	mysql_query($query) or die('Error : ' . mysql_error());	
	
	
if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'User added';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'User added';
url('manage-users');
}
 
}// close errors
}// close if form sent

//dispaly any errors
errors($error);


?>

<form action="" method="post">

<p><label>Username</label><input class="box-medium tooltip-right" title="Provide Username" name="username" type="text" value="<?php if(isset($error)){ echo $username;}?>" size="50" maxlength="255">
</p>

<p><label>Password</label><input class="box-medium tooltip-right" title="Provide password" name="password" type="password" value="" size="50" maxlength="255">
</p>

<p><label>Confirm Password:</label><input class="box-medium tooltip-right" title="Confirm password" name="password2" type="password" size="40" maxlength="255" />
</p>

<p><label>Email</label><input class="box-medium tooltip-right" title="Provide Email Address" name="email" type="text" value="<?php if(isset($error)){  echo $email;}?>" size="50" maxlength="255">
</p>

<p><label>User Level:</label>
<select name="userlevel" class="box-medium tooltip-right" title="Select Access Level">
	<option value=''>Please select a user level</option>
    <option value="0">Global Admin</option>
    <option value="1">Admin</option>
	<option value="2">Editor</option>    
</select>
</p>

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save page and return to manage users">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save page and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save page and return to manage users">
</form>
</div>
<?php } else {
header('Location: '.DIRADMIN);
exit;
}
?>
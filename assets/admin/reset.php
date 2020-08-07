<?php
require ('../includes/config.php');
require ('../includes/functions.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Admin Reset</title>
<link rel="stylesheet" type="text/css" href="<?php echo DIR;?>assets/style/login.css" />
<?php
echo "<script type=\"text/javascript\" src=\"".DIR."assets/includes/js/jquery.js\"></script>\n";
?>
<script type="text/javascript">
$(document).ready(function(){		
	$("div.hidethis").click(function(){$(this).slideUp("fast");});
});
</script>
</head>
<body>
<?php
if (isset($_POST['cancel'])){
	url('');
}

//This code runs if the form has been submitted
if (isset($_POST['submit'])) 
{

// check for valid email address
$email = $_POST['remail'];
$pattern = '/^[^@]+@[^\s\r\n\'";,@%]+$/';
if (!preg_match($pattern, trim($email))) {
  $error[] = 'Please enter a valid email address';
  } 
  
// checks if the username is in use
if (!get_magic_quotes_gpc()) {
$_POST[] = addslashes($_POST['remail']);
}
$usercheck = $_POST['remail'];
$check = mysql_query("SELECT email FROM ".PREFIX."members WHERE email = '$usercheck'") 
or die(mysql_error());
$check2 = mysql_num_rows($check);

//if the name exists it gives an error
if ($check2 == 0) {
$error[] = 'Sorry, we cannot find your account details please try another email address.';
}

// if no errors then carry on
if (!$error) {

$email  = $_POST['remail'];

$query = "SELECT username, password FROM ".PREFIX."members WHERE email = '$email' ";
$result = mysql_query($query) or die ('Can\t get requested info because : '. mysql_error());
$Rows = mysql_num_rows($result);

$i = 0;

while ($i < $Rows){

$username = mysql_result($result, $i, "username");
$password = mysql_result($result, $i, "password");

//create a new random password

$password = substr(md5(uniqid(rand(),1)),3,10);

$salt = 'fjsj560';	
$pass = md5($salt . md5($password . $salt));


//send email
$to = "$email";
$subject = "Account Details Recovery for $siteTitle";
$body = "Hi $username, \n\n you or someone else have requested your account details. \n\n Here is your account information please keep this as you may need this at a later stage. \n\nYour username is $username \n\n your password is $password \n\n Your password has been reset please login and change your password to something more rememberable.\n\n Regards Site Admin \n\n ".DIR."admin \n\n";
$additionalheaders = "From: <$siteTitle>\r\n";
$additionalheaders .= "Replt-To: $email";
if(mail($to, $subject, $body, $additionalheaders)){}

//update database
$sql = "UPDATE ".PREFIX."members SET password='$pass' WHERE email = '$email'";
$result2 = mysql_query($sql) or die ('Coult not reset password: '. mysql_error());

$_SESSION['success'] = "You have been sent an email with your account details to $email";
url();

$i++;}	

}// close errors
}// close if form sent
?>

<div id="main_container">
<?php errors($error); echo messages();?> 

  <div class="login_form">
  	<div class="logo"><img src="<?php echo DIR;?>assets/templates/images/logo.png" alt="" title="" border="0" /></div>
         <div style="float: left; width: 300px; margin-left: 30px;">
		 <h3>Admin Reset Password</h3> 	     
         <p style="clear:both;">Please enter your e-mail address. You will receive a new password via e-mail.</p>   
         <form action="" method="post"> 
		 <p class="clear"><label>Email:</label><input name="remail" type="text" class="box-medium" size="50" /></p>		 
		 <p><input type="submit" class="button" name="submit" value="Reset Password" title="Reset Password" /> <input type="submit" class="button" name="cancel" value="Cancel" title="Cancel" /></p>         </form>
		 </div>
    </div> 	
    
  <div class="footer_login">    
   	<div class="left_footer_login">&copy; Copyright <?php echo date('Y').' '.SITETITLE;?> | Version <?php echo CMSV;?> | Created by <a href="http://www.daveismyname.co.uk" target="_blank" title="visit David Carr's website">David Carr</a></div>    
  </div>
</div>		
</body>
</html>
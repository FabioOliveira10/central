<?php
require ('../includes/config.php');
require ('../includes/functions.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Admin Login</title>
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
if (isset($_POST['forgot'])){
	url('reset');
}

if (isset($_POST['slogin'])){
	login($_POST['user'], $_POST['pass']);
}
?>
<div id="main_container">
<?php echo messages();?> 

  <div class="login_form">
  	<div class="logo"><img src="<?php echo DIR;?>assets/templates/images/logo.png" alt="" title="" border="0" /></div>
         <div style="float: left; width: 300px; margin-left: 30px;">
		 <h3>Admin Login</h3> 	     
                 
         <form action="" method="post"> 
		 <p class="clear"><label>Username:</label><input name="user" type="text" class="box-medium" size="50" /></p>
		 <p><label>Password:</label><input name="pass" type="password" class="box-medium" size="50" /></p>
		 <p><input type="submit" class="button" name="slogin" value="Login" title="Login" /> <input type="submit" class="button" name="forgot" value="Forgot password" title="orgot password" /></p>         </form>
		 </div>
    </div> 	
    
  <div class="footer_login">    
   	<div class="left_footer_login">&copy; Copyright <?php echo date('Y').' '.SITETITLE;?> | Version <?php echo CMSV;?> | Created by <a href="http://www.daveismyname.co.uk" target="_blank" title="visit David Carr's website">David Carr</a></div>    
  </div>
</div>		
</body>
</html>
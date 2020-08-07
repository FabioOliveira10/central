<?php
/*-------------------------------------------------------+
| The One Point - Content Management System
| http://www.theonepoint.co.uk/
+--------------------------------------------------------+
| Author: David Carr  Email: d.carr@theonepoint.co.uk
+--------------------------------------------------------+*/

require ('assets/includes/config.php');

/* Consumer key from twitter */
//$consumer_key = 'FelDmE0aAFkNuUZBvYkh6g';

/* Consumer Secret from twitter */
//$consumer_secret = 'MGuBQZUWLuMKv0KrdQCINLROpXl6a8U4M0pS4nwSlI';

//$_SESSION['oauth_access_token'] = '21011659-7bz0h0dBRmh0q6FoXS4YEUPLm01cdLlsPmDrczs6U';
//$_SESSION['oauth_access_token_secret'] = 'LSCG50WSaH9Td2GhSt1dyr3p0kQUDY0e0XwG1HCjKA';

/* include the twitter OAuth library files */
//require_once('assets/includes/twitter/twitterOAuth.php');
//require_once('assets/includes/twitter/OAuth.php');

/* Connect to the Twitter API */
//$to = new TwitterOAuth($consumer_key, $consumer_secret, $_SESSION['oauth_access_token'], $_SESSION['oauth_access_token_secret']);

require ('assets/includes/settings.php');
require ('assets/includes/functions.php');
require ('assets/includes/del.php');

if (isset($_GET['logout'])) {logout(DIR);}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=8" />
<title>Admin</title>
<link href="<?php echo DIR;?>assets/style/reset.css" rel="stylesheet" type="text/css" />
<link href="<?php echo DIR;?>assets/style/alert.css" rel="stylesheet" type="text/css" />
<link href="<?php echo DIR;?>assets/style/admin.css" rel="stylesheet" type="text/css" />
<link href="<?php echo DIR;?>assets/style/tabs.css" rel="stylesheet" type="text/css" />
<link href="<?php echo DIR;?>assets/style/date.css" rel="stylesheet" type="text/css" />
<link href="<?php echo DIR;?>assets/style/addons.css" rel="stylesheet" type="text/css" />
<?php
echo "<script type=\"text/javascript\" src=\"".DIR."assets/includes/js/jquery.js\"></script>\n";
echo "<script type=\"text/javascript\" src=\"".DIR."assets/includes/js/alert.js\"></script>\n";
echo "<script type=\"text/javascript\" src=\"".DIR."assets/includes/js/tables/table.js\"></script>\n";
echo "<script type=\"text/javascript\" src=\"".DIR."assets/includes/js/tabs/tabs.js\"></script>\n";
echo "<script type=\"text/javascript\" src=\"".DIR."assets/includes/js/pop.js\"></script>\n";
echo "<script type=\"text/javascript\" src=\"".DIR."assets/includes/js/date.js\"></script>\n";
?>
<script src="<?php echo DIR;?>assets/includes/js/jquery.tipsy.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('.tooltip-top').tipsy({fade: false, gravity: 's'});
	$('.tooltip-right').tipsy({fade: false, gravity: 'w'});
	$('.tooltip-bottom').tipsy({fade: false, gravity: 'n'});
	$('.tooltip-left').tipsy({fade: false, gravity: 'e'}); 
					
	$("div.hidethis").click(function(){$(this).slideUp("fast");});
	//date picker
	$('.datepicker').datepick({dateFormat: 'yy-mm-dd'});	
	//tabs
 	$('#container-1').tabs(); 
	$('#container-2').tabs();
	
	$('#top').click(function() {
	$('html, body').animate({ scrollTop:0 }, 'fast');
	return false;
	})
	
	//Sidebar Accordion Menu:
		
		$("#main-nav li ul").hide(); // Hide all sub menus
		$("#main-nav li a.current").parent().find("ul").slideToggle("slow"); // Slide down the current menu item's sub menu
		
		$("#main-nav li a.nav-top-item").click( // When a top menu item is clicked...
			function () {
				$(this).parent().siblings().find("ul").slideUp("normal"); // Slide up all sub menus except the one clicked
				$(this).next().slideToggle("normal"); // Slide down the clicked sub menu
				return false;
			}
		);
		
		$("#main-nav li a.no-submenu").click( // When a menu item with no sub menu is clicked...
			function () {
				window.location.href=(this.href); // Just open the link instead of a sub menu
				return false;
			}
		); 

    // Sidebar Accordion Menu Hover Effect:
		
		$("#main-nav li .nav-top-item").hover(
			function () {
				$(this).stop().animate({ paddingRight: "25px" }, 200);
			}, 
			function () {
				$(this).stop().animate({ paddingRight: "15px" });
			}
		); 
 
	 
	 //alert box
	 $(".delete_button").click( function() 
	{
		var delID = $(this).attr("id");
		var delType = $(this).attr("rel");		
		var title = $(this).attr("title");
		jConfirm('Are you sure you want to delete ' + title + '?', 'Confirmation Dialog', 
		function(r) {
			if(r==true)
			{
				window.location.href = '<?php echo DIR;?>admin.php?delType=' + delType + '&delID='+delID;
			}
		});
		return false;
	});

//close
});
</script>
<?php require('assets/includes/editor-script.php');?>
<?php require('assets/includes/js.php');?>
<script type="text/javascript">
//Edit the counter/limiter value as your wish
var count = "140";   //Example: var count = "175";
function limiter(){
var tex = document.myform.comment.value;
var len = tex.length;
if(len > count){
        tex = tex.substring(0,count);
        document.myform.comment.value =tex;
        return false;
}
document.myform.limit.value = count-len;
}
</script>

</head>

<body onLoad="initialize()">

<div id="body-wrapper"> 		
<div id="sidebar">
<div id="sidebar-wrapper"> 
<img id="logo" src="<?php echo DIR;?>assets/templates/images/logo.png" border="0" width="200px">

<?php
if (isglobaladmin($prefix) || isadmin($prefix) || iseditor($prefix)){
?>

<div id="profile-links">


<h3>Hello, <?php echo get_username();?> <br />
<a href="<?php echo DIR;?>" class="tooltip-bottom" title="Launch Site" target="_blank">Visit Site</a> |
<a href="<?php echo DIR;?>logout" class="tooltip-right" title="Sign Out">Logout</a></h3>

</div>        
			
<ul id="main-nav">  
<li><a href="<?php echo DIR;?>admin" class="nav-top-item no-submenu">Dashboard</a></li>
<li><a href="<?php echo DIR;?>admin/manage-pages" class="nav-top-item no-submenu">Manage Pages</a></li>
<li><a href="<?php echo DIR;?>admin/manage-sidebar-panels" class="nav-top-item no-submenu">Manage Sidebar Panels</a></li>
<li><a href="<?php echo DIR;?>admin/manage-add-ons" class="nav-top-item no-submenu">Manage Add-ons</a></li>
<li><a href="<?php echo DIR;?>admin/manage-footers" class="nav-top-item no-submenu">Manage Footers</a></li>
<li><a href="<?php echo DIR;?>admin/settings" class="nav-top-item no-submenu">Settings</a></li>		
</ul> 
<?php } ?>			
</div>
</div> 
		
<div id="main-content"> 

<div class="clear"></div> 
			
<div class="content-box">

<?php

// admin options
if(isset($_GET['reset'])){
require('assets/admin/reset.php');
$curpage = true;
}

if(isset($_GET['admin'])){
$rowadmin = 1;
$curpage = true;

//if is logged in show admin page
if (isglobaladmin($prefix) || isadmin($prefix) || iseditor($prefix)){

echo messages();
	

echo "<div class=\"content-box-header\"><h3>Dashboard</h3></div> 			
<div class=\"content-box-content\">";
?>

<div class="icon">
<a href="<?php echo DIRADMIN?>manage-pages"><img src="<?php echo DIR;?>assets/images/icons/pages.png" alt="" /></a>
<p><a href="<?php echo DIRADMIN;?>manage-pages">Manage Pages</a></p>
</div>

<div class="icon">
<a href="<?php echo DIRADMIN;?>manage-sidebar-panels"><img src="<?php echo DIR;?>assets/images/icons/panals.png" alt="" /></a>
<p><a href="<?php echo DIRADMIN;?>manage-sidebar-panels">Manage Sidebars</a></p>
</div>

<div class="icon">
<a href="<?php echo DIRADMIN;?>manage-add-ons"><img src="<?php echo DIR;?>assets/images/icons/addons.png" alt="" /></a>
<p><a href="<?php echo DIRADMIN;?>manage-add-ons">Manage Add-ons</a></p>
</div>

<div class="icon">
<a href="<?php echo DIRADMIN;?>settings"><img src="<?php echo DIR;?>assets/images/icons/settings.png" alt="" /></a>
<p><a href="<?php echo DIRADMIN;?>settings">Settings</a></p>
</div>

<div class="icon">
<a href="<?php echo DIRADMIN?>manage-footers"><img src="<?php echo DIR;?>assets/images/icons/pages.png" alt="" /></a>
<p><a href="<?php echo DIRADMIN;?>manage-footers">Manage Footers</a></p>
</div>

</div>

<div class="content-box-header"><h3>Quick Links</h3></div> 			
<div class="content-box-content">


<div class="icon">
<a href="<?php echo DIRADMIN;?>manage-pages/edit-page-1"><img src="<?php echo DIR;?>assets/images/icons/homepage.png" alt="" /></a>
<p><a href="<?php echo DIRADMIN;?>manage-pages/edit-page-1">Edit Homepage</a></p>
</div>


<div class="icon">
<a href="<?php echo DIR;?>assets/editor/jscripts/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php" title="File Manager" class="example2demo" name="windowX"><img src="<?php echo DIR;?>assets/images/icons/filemanager.png" alt="" /></a>
<p><a href="<?php echo DIR;?>assets/editor/jscripts/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php" title="File Manager" class="example2demo" name="windowX">File Manager</a></p>
<script type="text/javascript"> 
$('.example2demo').popupWindow({ 
height:500, 
width:800,
centerBrowser:1 
}); 
</script>
</div>


</div>


<?php

} else { // not logged in show login form

url('login');


}
}
//The following sections get the needed pages based on what page has been requested.

//checkpoint for hook
if ($SYS->hooks_exist('page_requester')) { 
//load data from the hook page requester from plugins
$SYS->execute_hooks('page_requester');
}

if(isset($_GET['site-settings'])){
require('assets/admin/site-settings.php');
$curpage = true;
}

if(isset($_GET['manage-footers'])){
require('assets/admin/footers.php');
$curpage = true;
}

if(isset($_GET['manage-add-ons'])){
require('assets/admin/manage-add-ons.php');
$curpage = true;
}

if(isset($_GET['mtwitter'])){
require('assets/admin/twitter.php');
$curpage = true;
}

if(isset($_GET['ssettings'])){
require('assets/admin/settings.php');
$curpage = true;
}

if(isset($_GET['manage-sidebar-panels'])){
require('assets/admin/manage-sidebar-panels.php');
$curpage = true;
}

if(isset($_GET['add-sidebar-panel'])){
require('assets/admin/insert/admin-add-sidebar-panel.php');
$curpage = true;
}

if(isset($_GET['edit-sidebar-panel'])){
require('assets/admin/edit/admin-edit-sidebar-panel.php');
$curpage = true;
}

if(isset($_GET['manage-users'])){
require('assets/admin/manage-users.php');
$curpage = true;
}

if(isset($_GET['add-user'])){
require('assets/admin/insert/admin-add-user.php');
$curpage = true;
}

if(isset($_GET['edit-user'])){
require('assets/admin/edit/admin-edit-user.php');
$curpage = true;
}

if(isset($_GET['manage-pages'])){
require('assets/admin/manage-pages.php');
$curpage = true;
}

if(isset($_GET['add-page'])){
require('assets/admin/insert/admin-add-page.php');
$curpage = true;
}

if(isset($_GET['edit-page'])){
require('assets/admin/edit/admin-edit-page.php');
$curpage = true;
}

if(isset($_GET['change-pass'])){
require('assets/admin/change-pass.php');
$curpage = true;
}

if(isset($_GET['admin-details'])){
require('assets/admin/admin-details.php');
$curpage = true;
}

if(isset($_GET['themes'])){
require('assets/admin/themes.php');
$curpage = true;
}

if(isset($_GET['add-theme'])){
require('assets/admin/insert/admin-add-theme.php');
$curpage = true;
}

if(isset($_GET['edit-theme'])){
require('assets/admin/edit/admin-edit-theme.php');
$curpage = true;
}

?>
</div>
<div class="clear"></div>

<div id="footer">
<small>&copy; Copyright <?php echo date('Y').' '.SITETITLE;?> | Version <?php echo CMSV;?> | Created by <a href="http://www.daveismyname.co.uk" title="visit Web Design Hull">David Carr</a> | 
<a href="#" id="top" class="tooltip-top" title="Move Top of page" >Top</a></small></div>
</div>		
</body>
</html>
<?php
/*-------------------------------------------------------+
| The One Point - Content Management System
| http://www.theonepoint.co.uk/
+--------------------------------------------------------+
| Author: David Carr  Email: d.carr@theonepoint.co.uk
| Plugin version 3.0
+--------------------------------------------------------+*/

/* hooks

above_doctype - code above doctype
header_css - code for including css files
header_js_script - code for including js files
header_js_jquery - code for jquery
header_slim_editor - code for stripped down editor
cont - code for plugins in main content
page_requester - code to request addtitional pages
del - delete section
admin_modules  - add link to manage add-ons

*/

$cfile = ".htaccess";
$fo = fopen($cfile, 'r');
//get file contents and work out the file content size in bytes
$data = fread($fo, filesize($cfile));
//close the file
fclose($fo);

if (preg_match('/contact-form/', $data))
{
} else { 
$newData = "
RewriteRule ^admin/manage-add-ons/contact-form$                   admin.php?contact-form=$1 [L]
RewriteRule ^admin/manage-add-ons/contact-form/$                  admin.php?contact-form=$1 [L]\n###\n";

$data = str_replace("###",$newData,$data);

$myFile = $cfile;
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $data);
fclose($fh);
}

function showContact()
{
global $curpage;
$curpage = true;
if (isglobaladmin($prefix) || isadmin($prefix)){
	
echo "<div class=\"content-box-header\"><h3>Contact Form</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > Contact Form</p></div>";
?>

<p>To implement the contact form insert [contact] into any page you want the contact form to be displayed., for sidebars add [side-contact]</p>

<p>The form is shown below for demonstration purposes.</p>

<form action="" method="post">
<p>>Name:<br /><input name="name" type="text" class="text-input" maxlength="30" value="" disabled="disabled"/></p>
<p>Email:<br /><input name="email" type="text" class="text-input" maxlength="2550" value="" disabled="disabled"/></p>
<p> Where did you hear about us:<br /><input name="email" type="text" class="text-input" maxlength="2550" value="" disabled="disabled"/></p>
<p>Message:<br /><textarea name="message" cols="34" rows="5" disabled="disabled"></textarea></p>
<input type="submit" name="maincontsubmit" value="Send Message" class="button" disabled="disabled" /></form>
</div>
<?php
} else {
header('Location: '.DIRADMIN);
exit;
}	
}

function contactRequest()
{
	if(isset($_GET['contact-form'])){
	showContact();
	$curpage = true;
	}
}



function addLinksContactForm() {
	echo "bk<div class=\"icon\">\n";
		echo "<a href=\"".DIR."admin/manage-add-ons/contact-form\"><img src=\"".DIR."assets/plugins/contact/contact-form.png\" alt=\"Contact Form\" title=\"Manage Contact Form\" class=\"tooltip-top\" /></a>\n";
    	echo "<p><a href=\"".DIR."admin/manage-add-ons/contact-form\" title=\"Manage Contact Form\" class=\"tooltip-top\">Contact Form</a></p>\n";
    echo "</div>\n";	
}


//add hook, where to execute a function
add_hook('admin_modules','addLinksContactForm');
add_hook('cont','mainDoContact');
add_hook('cont','sideDoContact');
add_hook('page_requester','contactRequest');
?>
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

function twitter ($msg){

global $to;
$msg = stripslashes($msg);

//post to twitter
//$content = $to->OAuthRequest('https://twitter.com/statuses/update.xml', array('status' => $msg), 'POST');

}


//log user in ---------------------------------------------------
function login($user, $pass){

	$user = $_POST['user'];
	$pass = $_POST['pass'];
 
   //strip all tags from varible   
   $user = safestrip($user);
   $pass = safestrip($pass);


  $salt = 'fjsj560';	
  $pass = md5($salt . md5($pass . $salt));

   // check if the user id and password combination exist in database
   $sql = "SELECT * FROM ".PREFIX."members WHERE username = '$user' AND password = '$pass'";
   $result = mysql_query($sql) or die('Query failed. ' . mysql_error());
   
   
   if (mysql_num_rows($result) == 1) {
      // the username and password match,
      // set the session
	  
	  while($row = mysql_fetch_object($result))
	  {
	  
	  
	  		if($row->active == '0'){
					
				// define an error message
				$_SESSION['error'] = 'Sorry, your account is not active';
			} else {
				  $_SESSION['member'] = $row->memberID;
					  
				  // reload the page
				url();
			}
			
	}  
    } else {
	// define an error message
	$_SESSION['error'] = 'Sorry, wrong username or password';
   }
}

function get_memberID() {
	$sql = mysql_query("SELECT * FROM ".PREFIX."members WHERE memberID = '".$_SESSION['member']."'")or die(mysql_error());
	if (mysql_num_rows($sql) == 1) {
	$r=mysql_fetch_object($sql);
	return $r->memberID;
	}	
}

function get_username() {
	$sql = mysql_query("SELECT * FROM ".PREFIX."members WHERE memberID = '".$_SESSION['member']."'")or die(mysql_error());
	if (mysql_num_rows($sql) == 1) {
	$r=mysql_fetch_object($sql);
	return $r->username;
	}	
}

function get_userlevel() {
	$sql = mysql_query("SELECT * FROM ".PREFIX."members WHERE memberID = '".$_SESSION['member']."'")or die(mysql_error());
	if (mysql_num_rows($sql) == 1) {
	$r=mysql_fetch_object($sql);
	return $r->level;
	}	
}

function url($string){
	$string = str_replace(DIRADMIN,"",$string);
	header('Location: '.DIRADMIN."$string");
	exit();
}

// Render error messages
function messages() {
    $message = '';
    if($_SESSION['success'] != '') {
        $message = '<div class="msg-ok hidethis">'.$_SESSION['success'].'</div>';
        $_SESSION['success'] = '';
    }
    if($_SESSION['info'] != '') {
        $message = '<div class="msg-info hidethis">'.$_SESSION['info'].'</div>';
        $_SESSION['info'] = '';
    }
    if($_SESSION['error'] != '') {
        $message = '<div class="msg-error hidethis">'.$_SESSION['error'].'</div>';
        $_SESSION['error'] = '';
    }
	//print_r($_SESSION);
    echo "$message";
}

function errors($error){
if (!empty($error))
{
		$i = 0;
		while ($i < count($error)){
		$showError.= "<div class=\"msg-error hidethis\">".$error[$i]."</div>";
		$i ++;}
		echo $showError;
}// close if empty errors


} // close function

// Authentication
function logged_in() {
	if($_SESSION['authorized'] == true) {
		return true;
	} else {
		return false;
	}
	
}
function login_required() {
	if(logged_in()) {	
		return true;
	} else {
		header('Location: login');
	}
	
}

function safestrip($string){
	$string = strip_tags($string);
	$string = mysql_real_escape_string($string);
	return $string;
}

function safe($string){
	$string = mysql_real_escape_string($string);
	return $string;
}

// --------------------- logout user -------------------------------
function logout(){
unset($_SESSION['member']);
url('');
}
//---------------------is global admin ------------------------
function isglobaladmin()
{
	if(isset($_SESSION['member']))
	{
		
		$sql = "SELECT level FROM ".PREFIX."members WHERE memberID='". $_SESSION['member'] ."' ";
		$r = mysql_query($sql)or die(mysql_error());
		
		while ($row = mysql_fetch_array($r))
		{
		
			if($row['level'] == '0')
			{
			return true;	
			}
		}
	
	}	
}

//---------------------is admin ------------------------
function isadmin()
{
	if(isset($_SESSION['member']))
	{
		
		$sql = "SELECT level FROM ".PREFIX."members WHERE memberID='". $_SESSION['member'] ."' ";
		$r = mysql_query($sql)or die(mysql_error());
		
		while ($row = mysql_fetch_array($r))
		{
		
			if($row['level'] == '1')
			{
			return true;	
			}
		}
	
	}	
}


//---------------------is admin ------------------------
function iseditor()
{
	if(isset($_SESSION['member']))
	{
		
		$sql = "SELECT level FROM ".PREFIX."members WHERE memberID='". $_SESSION['member'] ."' ";
		$r = mysql_query($sql)or die(mysql_error());
		
		while ($row = mysql_fetch_array($r))
		{
		
			if($row['level'] == '2')
			{
			return true;	
			}
		}
	
	}	
}


/* documents */

function getFileType($extension)
{
	$images = array('jpg', 'gif', 'png', 'bmp');
	$docs 	= array('txt', 'rtf', 'doc');
	$apps 	= array('zip', 'rar', 'exe', 'html');
	$video 	= array('mpg', 'wmv', 'avi');
	$audio 	= array('wav', 'mp3');
	
	if(in_array($extension, $images)) return "Image";
	if(in_array($extension, $docs)) return "Document";
	if(in_array($extension, $apps)) return "Application";
	if(in_array($extension, $video)) return "Video";
	if(in_array($extension, $audio)) return "Audio";
	return "Other";
}

function allowedExt($extension)
{
	$file = end(explode('.',$extension));
	
	if(in_array($file, badExt())) return false;
	return true;
}

function badExt()
{
	$types = array('php','exe');
	return $types;
}

function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
   
    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
   
    $bytes /= pow(1024, $pow); 
   
    return round($bytes, $precision) . ' ' . $units[$pow]; 
} 

function format_sql_date($date)
{
    $ray = split('/', $date);
    return join('-', array_reverse($ray));
}

//---------------delete directory
function delete_directory($dirname) {
	if (is_dir($dirname)){
	$dir_handle = opendir($dirname);
	}
	if (!$dir_handle){
	return false;
	}
	while($file = readdir($dir_handle)) {
	if ($file != "." && $file != "..") {
	if (!is_dir($dirname."/".$file))
	unlink($dirname."/".$file);
	else
	delete_directory($dirname.'/'.$file);
	}
	}
	closedir($dir_handle);
	rmdir($dirname);
	return true;
} 

//-------------- count files in a directory
function countFiles($path){
	$d = opendir($path);
	$count = 0;
	while(($f = readdir($d)) !== false)
	  if(ereg('.jpg$', $f))
		 ++$count;
	closedir($d);
	return $count;
}


// database backup
function backup_tables($DBHOST, $DBUSER, $DBPASS,$DBNAME,$tables = '*')
{
	
	$link = mysql_connect($DBHOST, $DBUSER, $DBPASS);
	mysql_select_db($DBNAME,$link);
	
	//get all of the tables
	if($tables == '*')
	{
		$tables = array();
		$result = mysql_query('SHOW TABLES')or die(mysql_error());
		while($row = mysql_fetch_row($result))
		{
			$tables[] = $row[0];
		}
	}
	else
	{
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}
	
	//cycle through
	foreach($tables as $table)
	{
		$result = mysql_query('SELECT * FROM '.$table);
		$num_fields = mysql_num_fields($result);
		
		$return.= 'DROP TABLE IF EXISTS '.$table.';';
		$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) 
		{
			while($row = mysql_fetch_row($result))
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j<$num_fields; $j++) 
				{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = ereg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j<($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
		
	}

	$stamp = date('d-m-y-h-i-s');	
	
	//save file
	$file = 'db-backup-'.$stamp.'.sql';
	$handle = fopen($file,'w+');
	fwrite($handle,$return);
	fclose($handle);
	
	//offer file for download using headser
	header("Content-Disposition: attachment; filename=\"$file\"");
	header('Content-type: text/sql');
	//read the file or download will be empty
	readfile($file);

	//delete the new file
	unlink($file);	
}


function getdbs()
{

	// make a connection to mysql here
	$conn = mysql_connect (DBHOST, DBUSER, DBPASS);
 
     // list all databases
     $dbs = mysql_list_dbs($conn);

	// loop through all databases
	for($x=0; $x < mysql_num_rows($dbs); $x++)
	{
		// get name of database
		$db = mysql_db_name($dbs, $x);
		
		//for each db get a list of tables within it
		$tables = mysql_list_tables($db, $conn);
		//echo "<ul>\n";
 
			//get all rows
			for($y=0; $y < mysql_num_rows($tables); $y++)
			{
				if (preg_match("/".PREFIX."/i", mysql_tablename($tables, $y)))
				{
					$match[] = mysql_tablename($tables, $y);
				} 				
			}			
	}



	
	if (!empty($match))
	{
		$i = 0;	
		$t = 1;	
		while ($i < count($match))
		{
			if($t == count($match)){
			$mysqltables.= $match[$i];
			} else {
			$mysqltables.= $match[$i].',';
			}		
		$i ++;
		$t ++;
		}		
	}	
	
	return $mysqltables;

}

/* ------------ Theme Functions ------------------------------*/


function RemoveExtension($strName){
	$ext = strrchr($strName, '.');
	
	if($ext !== false)
	{
	$strName = substr($strName, 0, -strlen($ext));
	}
	return $strName;
} 


function get_theme()
{
	include(THEMEPATH.THEME);
}


function get_theme_path()
{
	return THEMEPATH;
}

function get_meta()
{
	global $header_css;
	
	//if page is empty use home page meta data
	if(PAGE == ''){
	
	 $psql = @mysql_query("SELECT * FROM ".PREFIX."news WHERE newsSlug='".PAGE."'"); 
	 $npn = mysql_num_rows($psql);	 
	 if($npn == 1){
	 		$row = mysql_fetch_object($psql);
			echo "<meta name=\"keywords\" content=\"$row->newsMetaKeywords\" />\n";
			echo "<meta name=\"description\" content=\"$row->newsMetaDescription\" />\n";
	 }
	 
	 $psqr = @mysql_query("SELECT * FROM ".PREFIX."recipes WHERE recipesSlug='".PAGE."'"); 
	 $npnr = mysql_num_rows($psqr);	 
	 if($npnr == 1){
	 		$row = mysql_fetch_object($psqr);
			echo "<meta name=\"keywords\" content=\"$row->recipesMetaKeywords\" />\n";
			echo "<meta name=\"description\" content=\"$row->recipesMetaDescription\" />\n";
	 }
	 
	 $id = $_GET['blogpost'];
	 $id = mysql_real_escape_string($id);   
	 $psql = @mysql_query("SELECT * FROM ".PREFIX."blog_posts WHERE `postSlug` = '$id'"); 
	 $bpn = mysql_num_rows($psql);
	 if($bpn == 1){
	 		$row = mysql_fetch_object($psql);
			echo "<meta name=\"keywords\" content=\"$row->postMetaKeywords\" />\n";
			echo "<meta name=\"description\" content=\"$row->postMetaDescription\" />\n";
	 }	
	 
	 $id = $_GET['blogcat'];
	 $id = mysql_real_escape_string($id);   
	 $psql = @mysql_query("SELECT * FROM ".PREFIX."blog_cats WHERE `catSlug` = '$id'"); 
	 $bcn = mysql_num_rows($psql);	
		
		if($npn == 0 && $bpn == 0 && $bcn == 0){
			$result = mysql_query("SELECT * FROM ".PREFIX."pages WHERE pageID='1'")or die(mysql_error());
			$row = mysql_fetch_object($result);
			echo "<meta name=\"keywords\" content=\"$row->pageMetaKeywords\" />\n";
			echo "<meta name=\"description\" content=\"$row->pageMetaDescription\" />\n";			
		}
	} 
	else 
	{ 	
			// or use from the current page
			$sql = mysql_query("SELECT * FROM ".PREFIX."pages WHERE pageSlug='".PAGE."'");
			$num = mysql_num_rows($sql);
			if($num >= 1)
			{
				$row = mysql_fetch_object($sql);
				echo "<meta name=\"keywords\" content=\"$row->pageMetaKeywords\" />\n";
				echo "<meta name=\"description\" content=\"$row->pageMetaDescription\" />\n";
			}			
	}
			echo "<meta name=\"author\" content=\"".SITETITLE."\"/>\n";
			echo "<meta name=\"publisher\" content=\"".DIR.PAGE."\"/>\n";
			echo "<meta name=\"robots\" content=\"all,index,follow\"/>\n";			
			echo "<meta name=\"optimised-by\" content=\"www.daveismyname.co.uk\"/>\n";
			echo "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=8\" />\n";
			echo "<meta name=\"viewport\" content=\"width=device-width, height=device-height, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no\">\n";
}






function get_title()
{
	//get vars from settings.php and plugins
	global $isp,$above_doctype_title;
	
	//get title of requested page
	$result = mysql_query("SELECT * FROM ".PREFIX."pages WHERE pageSlug='".PAGE."'");
	$n = mysql_num_rows($result); 
	while ($row = mysql_fetch_object($result)){ 
		$navTitle = $row->pageName; 
		$isp = true;
	}	
	
	$result = mysql_query("SELECT * FROM ".PREFIX."news WHERE newsSlug='".PAGE."'");
	$n2 = mysql_num_rows($result); 
	while ($row = mysql_fetch_object($result)){ 
		$navTitle = $row->newsTitle; 
		$isp = true;
	}
	
	$result = mysql_query("SELECT * FROM ".PREFIX."recipes WHERE recipesSlug='".PAGE."'");
	$n3 = mysql_num_rows($result); 
	while ($row = mysql_fetch_object($result)){ 
		$navTitle = $row->recipesTitle; 
		$isp = true;
	}
	
	$result = mysql_query("SELECT * FROM ".PREFIX."courses WHERE coursesSlug='".PAGE."'");
	$n4 = mysql_num_rows($result); 
	while ($row = mysql_fetch_object($result)){ 
		$navTitle = $row->coursesTitle; 
		$isp = true;
	}
	
	/*$result = mysql_query("SELECT * FROM ".PREFIX."portfolio WHERE portfolioSlug='".PAGE."'");
	$n3 = mysql_num_rows($result); 
	while ($row = mysql_fetch_object($result)){ 
		$navTitle = $row->portfolioTitle; 
		$isp = true;
	}	*/
	
	//if no results from previous query and isp is true then get title from plugin
	if($n == 0 && $n2 == 0 && $n3 == 0 && $n4 == 0){
		$id = $_GET['blogpost'];
		$id = mysql_real_escape_string($id);   
		$psql = @mysql_query("SELECT * FROM ".PREFIX."blog_posts WHERE `postSlug` = '$id'"); 
		$n = mysql_num_rows($psql);	
		while ($row = mysql_fetch_object($psql)){
		$navTitle = $row->postTitle;	
		$isp = true;
		}
		
		$id = $_GET['blogcat'];
		$id = mysql_real_escape_string($id);   
		$psql = @mysql_query("SELECT * FROM ".PREFIX."blog_cats WHERE `catSlug` = '$id'"); 
		$n = mysql_num_rows($psql);	
		while ($row = mysql_fetch_object($psql)){
		$navTitle = $row->catTitle;	
		$isp = true;
		}
	}
		
	//get title of home page
	if($isp != true)
	{
		$result = mysql_query("SELECT * FROM ".PREFIX."pages WHERE pageID='1'")or die(mysql_error());
		$row = mysql_fetch_object($result);
		$navTitle = $row->pageName;	
	}
	
	//print site title and page title
	echo  $navTitle.' - '.SITETITLE; 
}

function get_feat_Image()
{
	global $featImage;		
	$wi = '701';
	$hi = '306';	 
	$img = $featImage;	
	$img = str_replace("../../../","",$img);
	
	if($featImage !=''){	 
		  
	$doc = new DOMDocument();
	$doc->loadHTML("$featImage");
	$linkTags = $doc->getElementsByTagName('a');	
	foreach($linkTags as $tag) {
	$link = $tag->getAttribute('href');
	}	
	$imageTags = $doc->getElementsByTagName('img');	
	foreach($imageTags as $tag) {
	$title = $tag->getAttribute('title');
	$alt = $tag->getAttribute('alt');
	}	
	
	if (preg_match("/<p class=\"slideshow\">/i", $featImage)) {	
		echo $featImage;
	} else {
		$str = preg_replace('#<p>.+?src=[\'"]([^\'"]+)[\'"].+</p>#i', "$1", $img);	
		if($link !=''){
		  echo "<a href=\"$link\"><img src=\"".DIR."img.php?src=$str&w=$wi&h=$hi&zc=1\" alt=\"$alt\" title=\"$title\" /></a>\n";
		} else {
		  echo "<img src=\"".DIR."img.php?src=$str&w=$wi&h=$hi&zc=1\" alt=\"$alt\" title=\"$title\" />\n";
		}
	}
	
	}	
	
	if($featImage == ''){
		echo "<p><img src=\"".DIR."img.php?src=../../images/welcome-south-hunsley-school.png&w=701&h=306&zc=1\" alt=\"\" />\n";		
	}
}

function mailinglist($string) 
{	
	if(isset($_POST['submit'])){
	
	//collect data from form and remove any tags and make safe for database entry
	$email = strip_tags(mysql_real_escape_string($_POST['email']));
	
	
	// check for valid email address
	$pattern = '/^[^@]+@[^\s\r\n\'";,@%]+$/';
	if (!preg_match($pattern, trim($email))) {
		$error[] = 'Please enter a valid email address';
	}
	
	// if validation is okay then carry on
	if (!isset($error)) {
	
	//insert into database
	$q = mysql_query("INSERT INTO ".PREFIX."mailinglist (email) VALUES ('$email')")or die(mysql_error());
	
	//submission successful show a message
	$mOutput.= "<div class=\"msg-ok hidethis\">Thank you, submission was successful.</div>";
	
	} // end validation
	}// end submit
	
	
	//show any errors
	if (!empty($error))
	{
			$i = 0;
			while ($i < count($error)){
			$mOutput.= "<div class=\"msg-error hidethis\">".$error[$i]."</div>";
			$i ++;}
	}// close if empty errors
	ob_start();
	?>	
	<form action="" method="post" id="form-newsletter">
	<input name="email" onBlur="if(this.value=='')this.value='Enter Email Address';" onFocus="if(this.value=='Enter Email Address')this.value='';" type="text" id="input-newsletter" value="Enter Email Address" size="20">		
	<input id="submit-newsletter" type="image" src="<?php echo DIR.get_theme_path();?>images/go.png" alt="&nbsp;" title="Search" value="Search" name="submit" />
	</form>
	<?php
	$mOutput.= ob_get_clean();
	
	$string = str_replace("[mailinglist]", $mOutput, $string);
    return $string;
}


//Main Page plugin function
function mainDoContact($string) 
{

//This code runs if the contact form has been submitted
if (isset($_POST['maincontsubmit']))
{

// check feilds are not empty
$name = trim($_POST['name']);
if (strlen($name) < 3) {
$mainerror[] = 'Name Must be more then 3 charactors.';
}
if (strlen($name) > 20) {
$mainerror[] = 'Name Must be less then 20 charactors.';
}

// check for valid email address
$email = $_POST['email'];
$pattern = '/^[^@]+@[^\s\r\n\'";,@%]+$/';
if (!preg_match($pattern, trim($email))) {
$mainerror[] = 'Please enter a valid email address';
  }
 
// check feilds are not empty
$message = trim($_POST['message']);
if (strlen($message) < 3) {
$mainerror[] = 'Message Must be more then 3 charactors.';
}

if($_SESSION["captcha"]!==$_POST["captcha"])
{
$mainerror[] = 'Characters do not match the black characters on the image.';
}

$tel = $_POST['tel'];
$ip = $_SERVER["REMOTE_ADDR"];

// if valadation is okay then carry on
if (!$mainerror ) {

//send email
$to = SITEEMAIL;
$subject = "Contact for ".SITETITLE;
$body = "on ".date('M j, Y')." Information from contact form: \n\n Name: $name \n\n Email: $email \n\n Tel: $tel \n\n Message: \n\n $message \n\n IP Address: $ip";
$additionalheaders = "From: <$email>\r\n";
$additionalheaders .= "Replt-To: $email";

if(mail($to, $subject, $body, $additionalheaders))
{
	$output.= "<div class=\"msg-error hidethis\">Message sent successfully</div>";
}
 
 
} // end valadation
}// end submit

 
//show any errors
if (!empty($mainerror))
{
		$i = 0;
		while ($i < count($mainerror)){
		$showError.= "<div class=\"msg-error hidethis\">".$mainerror[$i]."</div>";
		$i ++;}
		$output.= $showError;
}// close if empty errors

$output.= "<form action=\"\" method=\"post\">\n";
$output.= "<p>Name:<br />\n";
$output.= "<input name=\"name\" class=\"text-input\" type=\"text\" maxlength=\"20\" size=\"40\" value=\"$name\"/></p>\n";

$output.= "<p> Email:<br />\n";
$output.= "<input name=\"email\" class=\"text-input\" type=\"text\" maxlength=\"255\" size=\"40\" value=\"$email\"/></p>\n";

$output.= "<p>Tel:<br />\n";
$output.= "<input name=\"tel\" class=\"text-input\" type=\"text\" maxlength=\"20\" size=\"40\" value=\"$tel\"/></p>\n";

$output.= "<p>Message:<br /><br />\n";
$output.= "<textarea name=\"message\" class=\"inputfeilds\" id=\"contTest\" cols=\"45\" rows=\"10\">$message</textarea></p>\n";

$output.= "<p>Only enter the 3 <b>black</b> characters:<br /><br /></p>\n";
$output.= "<p><img src=\"".DIR."assets/plugins/contact/captcha.php\" alt=\"captcha image\"><input type=\"text\" id=\"captcha\" name=\"captcha\" size=\"3\" maxlength=\"3\"></p>\n";

$output.= "<p><input type=\"submit\" name=\"maincontsubmit\" class=\"button\" value=\"Send Message\" /></p></form>\n";
	
	$string = str_replace("[contact]", $output, $string);
	return $string;
	
}

function get_breadcrumb()
{
	global $breadcrumb;
	echo "<p><a href=\"".DIR."\">Home</a> > $breadcrumb</p>";
}

function get_content()
{
	global $page;
	$page = mailinglist($page);
	$page = mainDoContact($page);
	echo $page;
	echo $_SESSION['plugcont'];
}

function get_sidebars_left()
{
	global $sidebarsleft;
	echo $sidebarsleft;
}

function get_sidebars_right()
{
	global $sidebarsright;
	echo $sidebarsright;
}




//-------------------- create thumbnail --------------------------------------------

function createthumb($src_filename, $dst_filename_thumb)
{
    $size = getimagesize($src_filename);
	$stype = $size['mime'];
	$w = $size[0];
	$h = $size[1];
	switch($stype) {
		case 'image/gif':
		$simg = imagecreatefromgif($src_filename);
		//header("Content-type: image/gif");
		break;
		case 'image/jpeg':
		$simg = imagecreatefromjpeg($src_filename);
		//header("Content-type: image/jpg");
		break;
		case 'image/png':
		$simg = imagecreatefrompng($src_filename);
		//header("Content-type: image/png");
		break;
		}

	// get image width
	$width = $w;
	// get image height
    $height = $h;
	// size to create thumnail width
	$thumb_width = 100;
	$thumb_height = 100;
	
	
	$dimg = imagecreatetruecolor($thumb_width, $thumb_height);
	$wm = $width/$thumb_width;
	$hm = $height/$thumb_height;
	$h_height = $thumb_height/2;
	$w_height = $thumb_width/2;
	if($w> $h) {
		$adjusted_width = $w / $hm;
		$half_width = $adjusted_width / 2;
		$int_width = $half_width - $w_height;
		imagecopyresampled($dimg,$simg,-$int_width,0,0,0,$adjusted_width,$thumb_height,$w,$h);
	} elseif(($w <$h) || ($w == $h)) {
		$adjusted_height = $h / $wm;
		$half_height = $adjusted_height / 2;
		$int_height = $half_height - $h_height;
		imagecopyresampled($dimg,$simg,0,-$int_height,0,0,$thumb_width,$adjusted_height,$w,$h);
	} else {
		imagecopyresampled($dimg,$simg,0,0,0,0,$thumb_width,$thumb_height,$w,$h);
	}
		
	$ran = "thumb_".rand () ;	
	$thumb2 = $ran.".jpg";
	
	global $thumb_Add_thumb;
	$thumb_Add_thumb = $dst_filename_thumb;
	$thumb_Add_thumb .= $thumb2;
		
	
	 imagejpeg($dimg, "" .$dst_filename_thumb. "$thumb2"); 	
 }
 
//-------------------- create full size image --------------------------------------------  
 
 function createthumbfull($src_filename, $dst_filename_full)
{
    $size = getimagesize($src_filename);
	$stype = $size['mime'];
	$w = $size[0];
	$h = $size[1];
	switch($stype) {
		case 'image/gif':
		$simg = imagecreatefromgif($src_filename);
		//header("Content-type: image/gif");
		break;
		case 'image/jpeg':
		$simg = imagecreatefromjpeg($src_filename);
		//header("Content-type: image/jpg");
		break;
		case 'image/png':
		$simg = imagecreatefrompng($src_filename);
		//header("Content-type: image/png");
		break;
		}

	// get image width
	$width = $w;
	// get image height
    $height = $h;
	// size to create thumnail width
	$thumb_width = 400;
	$thumb_height = 400;
	
	
	$dimg = imagecreatetruecolor($thumb_width, $thumb_height);
	$wm = $width/$thumb_width;
	$hm = $height/$thumb_height;
	$h_height = $thumb_height/2;
	$w_height = $thumb_width/2;
	if($w> $h) {
		$adjusted_width = $w / $hm;
		$half_width = $adjusted_width / 2;
		$int_width = $half_width - $w_height;
		imagecopyresampled($dimg,$simg,-$int_width,0,0,0,$adjusted_width,$thumb_height,$w,$h);
	} elseif(($w <$h) || ($w == $h)) {
		$adjusted_height = $h / $wm;
		$half_height = $adjusted_height / 2;
		$int_height = $half_height - $h_height;
		imagecopyresampled($dimg,$simg,0,-$int_height,0,0,$thumb_width,$adjusted_height,$w,$h);
	} else {
		imagecopyresampled($dimg,$simg,0,0,0,0,$thumb_width,$thumb_height,$w,$h);
	}
		
	$ran = 'full_'.rand () ;	
	$thumb1 = $ran.".jpg";
	
	global $thumb_Add_full;
	$thumb_Add_full = $dst_filename_full;	
	$thumb_Add_full .= $thumb1;	
		
	
	 imagejpeg($dimg, "" .$dst_filename_full. "$thumb1"); 	
 }
 
 
// ------------------- language changer -----------------------------
function translateTexts($src_texts = array(), $src_lang, $dest_lang){
  //setting language pair
  $lang_pair = $src_lang.'|'.$dest_lang;

  $src_texts_query = "";
  foreach ($src_texts as $src_text){
    $src_texts_query .= "&q=".urlencode($src_text);
  }

  $url = "http://ajax.googleapis.com/ajax/services/language/translate?v=1.0".$src_texts_query."&langpair=".urlencode($lang_pair);

  // sendRequest
  // note how referer is set manually

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_REFERER, $_SERVER['PHP_SELF']);
  $body = curl_exec($ch);
  curl_close($ch);

  // now, process the JSON string
  $json = json_decode($body, true);

  if ($json['responseStatus'] != 200){
    return false;
  }


  $results = $json['responseData'];
  
  $return_array = array();
  
  foreach ($results as $result){
    if ($result['responseStatus'] == 200){
       return $result['responseData']['translatedText'];
 
    } else {
      $return_array[] = false;
    }
  }
}

//-------------------- create favicon ------------------------------
function createico($src_filename, $dst_filename_thumb)
{
    $size = getimagesize($src_filename);
	$stype = $size['mime'];
	$w = $size[0];
	$h = $size[1];
	switch($stype) {
		case 'image/gif':
		$simg = imagecreatefromgif($src_filename);
		//header("Content-type: image/gif");
		break;
		case 'image/jpeg':
		$simg = imagecreatefromjpeg($src_filename);
		//header("Content-type: image/jpg");
		break;
		case 'image/png':
		$simg = imagecreatefrompng($src_filename);
		//header("Content-type: image/png");
		break;
		}

	// get image width
	$width = $w;
	// get image height
    $height = $h;
	// size to create thumnail width
	$thumb_width = 16;
	$thumb_height = 16;
	
	
	$dimg = imagecreatetruecolor($thumb_width, $thumb_height);
	$wm = $width/$thumb_width;
	$hm = $height/$thumb_height;
	$h_height = $thumb_height/2;
	$w_height = $thumb_width/2;
	if($w> $h) {
		$adjusted_width = $w / $hm;
		$half_width = $adjusted_width / 2;
		$int_width = $half_width - $w_height;
		imagecopyresampled($dimg,$simg,-$int_width,0,0,0,$adjusted_width,$thumb_height,$w,$h);
	} elseif(($w <$h) || ($w == $h)) {
		$adjusted_height = $h / $wm;
		$half_height = $adjusted_height / 2;
		$int_height = $half_height - $h_height;
		imagecopyresampled($dimg,$simg,0,-$int_height,0,0,$thumb_width,$adjusted_height,$w,$h);
	} else {
		imagecopyresampled($dimg,$simg,0,0,0,0,$thumb_width,$thumb_height,$w,$h);
	}
		
	$thumb2 = "favicon.ico";
	
	global $thumb_Add_thumb;
	$thumb_Add_thumb = $dst_filename_thumb;
	$thumb_Add_thumb .= $thumb2;
		
	
	 imagegif($dimg, "" .$dst_filename_thumb. "$thumb2"); 	
 }


//----------------- showq files --------------------------------------------------------
 function showFiles($desired_extension,$dirname,$filename)
{

  $dir = opendir($dirname);  
  $selected = 'selected=selected'; 
  
  echo "<select name='file'>\n";
  $i = 0;
  while(false != ($file = readdir($dir)))
  {  
    if(($file != ".") and ($file != ".."))
    {
      $fileChunks = explode(".", $file);
      if($fileChunks[1] == $desired_extension) //interested in second chunk only
      {     
	  
	    if($dirname."/".$file == $filename)
		{
		$current = $selected;
		} else {	
		$current = '';
		}	
        
		echo "<option value=\"$dirname/"."$file\" $current>$file</option>\n";	
		$i++;	
      }
    }
  }
  
  if($i == 0)
  {
  	echo "<option value=\"0\">No files to show</option>\n";
  }
  
  echo "</select>\n";
  closedir($dir); 
}


?>
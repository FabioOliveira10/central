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

if (preg_match('/polls/', $data))
{
} else { 
$newData = "
RewriteRule ^admin/manage-add-ons/polls$ 			                admin.php?polls=$1 [L]
RewriteRule ^admin/manage-add-ons/polls/$			                admin.php?polls=$1 [L]

RewriteRule ^admin/manage-add-ons/polls/add-poll$ 			    admin.php?add-poll=$1 [L]
RewriteRule ^admin/manage-add-ons/polls/add-poll/$			    admin.php?add-poll=$1 [L]

RewriteRule ^admin/manage-add-ons/polls/edit-poll-([^/]+)$ 		admin.php?edit-poll=$1 [L]
RewriteRule ^admin/manage-add-ons/polls/edit-poll-([^/]+)/$		admin.php?edit-poll=$1 [L]

RewriteRule ^admin/manage-add-ons/polls/poll-([^/]+)$ 		    admin.php?poll=$1 [L]
RewriteRule ^admin/manage-add-ons/polls/poll-([^/]+)/$  	    admin.php?poll=$1 [L]

RewriteRule ^admin/manage-add-ons/polls/add-option-([^/]+)$   admin.php?add-option=$1 [L]
RewriteRule ^admin/manage-add-ons/polls/add-option-([^/]+)/$  admin.php?add-option=$1 [L]

RewriteRule ^admin/manage-add-ons/polls/edit-option-([^/]+)$  admin.php?edit-option=$1 [L]
RewriteRule ^admin/manage-add-ons/polls/edit-option-([^/]+)/$ admin.php?edit-option=$1 [L]\n###\n";

$data = str_replace("###",$newData,$data);

$myFile = $cfile;
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $data);
fclose($fh);

mysql_query("CREATE TABLE IF NOT EXISTS `".PREFIX."questions` (
  `qID` int(11) NOT NULL auto_increment,
  `qTitle` varchar(255) NOT NULL,
  `qTag` varchar(255) NOT NULL,
  PRIMARY KEY  (`qID`)
) ENGINE=MyISAM")or die('cannot make table questions due to: '.mysql_error());

mysql_query("CREATE TABLE IF NOT EXISTS `".PREFIX."answers` (
  `aID` int(11) NOT NULL auto_increment,
  `qID` int(11) NOT NULL,
  `aTitle` varchar(255) NOT NULL,
  `aPoints` int(11) NOT NULL default '0',
  PRIMARY KEY  (`aID`)
) ENGINE=MyISAM")or die('cannot make table answers due to: '.mysql_error());

}


function managePolls()
{
	$curpage = true;	
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Polls</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > Polls</p></div>";

echo messages();

echo "<p>To implement the poll insert the poll tag title in brackets like [poll2] into any page you want the poll to be displayed.</p>\n";

$result = mysql_query("SELECT * FROM ".PREFIX."questions")or die(mysql_error());
$Rows = mysql_num_rows($result);
?>
<table class="stripeMe">
<tr align="center">
<td align="left"><strong>Poll</strong></td>
<td align="left"><strong>Tag</strong></td>
<td ><strong>Action</strong></td>
</tr>
<?php
while ($row = mysql_fetch_object($result)) {
?>
<tr>
<td><a href="<?php echo DIRADMIN;?>manage-add-ons/polls/poll-<?php echo $row->qID;?>" class="tooltip-top" title="Manage Poll Options"><?php echo $row->qTitle;?></a></td>
<td><?php echo $row->qTag;?></td>

<td align="center" valign="top"><a href="<?php echo DIRADMIN;?>manage-add-ons/polls/edit-poll-<?php echo $row->qID;?>" class="tooltip-top" title="Edit"><img src="<?php echo DIR;?>assets/images/icons/action-edit.png" alt="Edit" /></a> 

<a href="#" id="<?php echo $row->qID;?>" rel="delpoll" title="<?php echo $row->qTitle;?>" class="delete_button"><img src="<?php echo DIR;?>assets/images/icons/action-del.png" alt="Delete" /></a></td>
</tr>
<?php
}
?>
</table>
<p><a href="<?php echo DIRADMIN;?>manage-add-ons/polls/add-poll" class="button tooltip-top" title="Create a new poll">Add Poll</a></p>

</div>
<?php
} else {
url(DIR);

}
}


function addPoll()
{
	$curpage = true;
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Add Poll</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/polls\">Polls</a> > Add Poll</p></div>";

echo messages();

if(isset($_POST['cancel'])){
$_SESSION['info'] = "poll was not created";
url('manage-add-ons/polls');
}

if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$qTitle = trim($_POST['qTitle']);
if (strlen($qTitle) < 1 || strlen($qTitle) > 255) {
$error[] = 'Poll question must be at between 1 and 255 charactors.';
}

// if valadation is okay then carry on
if (!$error) {
	
	// post form data 
   $qTitle     = $_POST['qTitle'];
   
   //strip any tags from input
   $qTitle   = strip_tags($qTitle);   
   
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$qTitle   = addslashes($qTitle);
	 }
   
   // remove any harhful code and stop sql injection
   $qTitle = mysql_real_escape_string($qTitle);
   
 
// insert data into images table
$query = "INSERT INTO ".PREFIX."questions (qTitle) VALUES ('$qTitle')";
$result  = mysql_query($query) or die ('album'.mysql_error());
$getID = mysql_insert_id();

$qTag = "poll$getID";

$query = mysql_query("UPDATE ".PREFIX."questions SET qTag='$qTag' WHERE qID='$getID'");
 	
if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'Poll added';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'Poll added';
url('manage-add-ons/polls');
}	
		
 
}
}

	
//dispaly any errors
errors($error);

?>

<form action="" method="post">
<p>Poll Question:<br /><input type="text" class="box-medium tooltip-right" title="Enter poll question" name="qTitle" <?php if (isset($error)){ echo "value=\"$qTitle\""; }?>/></p>

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save page and return to poll">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save and return to poll">
</form>
</div>
<?php

} else {
url(DIR);
}		
}

function editPoll()
{
	$curpage = true;	
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Edit Poll</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/polls\">Polls</a> > Edit Poll</p></div>";

messages();

if(isset($_POST['cancel'])){
$_SESSION['info'] = "poll was not created";
url('manage-add-ons/polls');
}

if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$qTitle = trim($_POST['qTitle']);
if (strlen($qTitle) < 1 || strlen($qTitle) > 255) {
$error[] = 'Poll Question must be at between 1 and 255 charactors.';
}

// if valadation is okay then carry on
if (!$error) {
	
	// post form data 
   $qID     = $_POST['qID'];
   $qTitle     = $_POST['qTitle'];
   
   //strip any tags from input
   $qTitle   = strip_tags($qTitle);   
   
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$qTitle   = addslashes($qTitle);
	 }
   
   // remove any harhful code and stop sql injection
   $qTitle = mysql_real_escape_string($qTitle); 
   $qID = mysql_real_escape_string($qID);   
  

// insert data into images table
$query = "UPDATE ".PREFIX."questions SET qTitle = '$qTitle' WHERE qID='$qID'";
$result  = mysql_query($query) or die (mysql_error());

if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'Poll updated';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'Poll updated';
url('manage-add-ons/polls');
}		
 
}
}

	
//dispaly any errors
errors($error);

$result = mysql_query("SELECT * FROM ".PREFIX."questions WHERE qID='{$_GET['edit-poll']}'");
while($row = mysql_fetch_object($result)){
?>

<form action="" method="post">
<input type="hidden" name="qID" value="<?=$row->qID;?>" />
<p>Poll Question:<br /><input type="text" class="box-medium tooltip-right" title="Update poll question"  name="qTitle" value="<?php echo $row->qTitle;?>"/></p>

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save page and return to poll">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save and return to poll">
</form>	
</div>
<?php }

} else {
url(DIR);

}	
}

function managePollOption()
{
	$curpage = true;;
	if (isglobaladmin() || isadmin()){
		
$sql = mysql_query("SELECT * FROM ".PREFIX."questions WHERE qID='{$_GET['poll']}'")or die(mysql_error());
$row = mysql_fetch_object($sql);
$qTitle = $row->qTitle;
$qID = $row->qID;

echo "<div class=\"content-box-header\"><h3>$qTitle</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/polls\">Polls</a> > <a href=\"".DIRADMIN."manage-add-ons/pools/poll-$qID\">$qTitle</a></p></div>";

messages();

?>
<table class="stripeMe">
<tr align="center">
<td width="42%" align="left"><strong>Poll Answers</strong></td>
<td width="45%"><strong>Action</strong></td>
</tr>
<?php
$result = mysql_query("SELECT * FROM ".PREFIX."answers WHERE qID='{$_GET['poll']}'")or die(mysql_error());
$Rows = mysql_num_rows($result);
while ($row = mysql_fetch_object($result)) {
?>
<tr>
<td><?php echo $row->aTitle;?></td>
<td align="center" valign="top"><a href="<?php echo DIRADMIN;?>manage-add-ons/polls/edit-option-<?php echo $row->aID;?>" class="tooltip-top" title="Edit"><img src="<?php echo DIR;?>assets/images/icons/action-edit.png" alt="Edit" /></a> 

<a href="#" id="<?php echo $row->aID;?>" rel="delpolloption" title="<?php echo $row->aTitle;?>" class="delete_button"><img src="<?php echo DIR;?>assets/images/icons/action-del.png" alt="Delete" /></a></td>
</tr>

<?php
}
?>
</table>
<p><a href="<?php echo DIRADMIN;?>manage-add-ons/polls/add-option-<?php echo $qID;?>" class="button tooltip-top" title="Add poll option">Add Option</a></p>
</div>
<?php
} else {
url(DIRADMIN);

}		
}


function addPollOption()
{
	$curpage = true;	
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Add Poll Option</h3></div> 			
<div class=\"content-box-content\">";
	
$sql = mysql_query("SELECT * FROM ".PREFIX."questions WHERE qID='{$_GET['add-option']}'")or die(mysql_error());
$row = mysql_fetch_object($sql);
$qTitle = $row->qTitle;
$qID = $row->qID;

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Modules</a> > <a href=\"".DIRADMIN."manage-add-ons/polls\">Polls</a> > <a href=\"".DIRADMIN."manage-add-ons/polls/poll-$qID\">$qTitle</a> > Add Poll Option</p></div>";

messages();

$qID = $_GET['add-option'];

if(isset($_POST['cancel'])){
$_SESSION['info'] = "Option was not added";
url('manage-add-ons/polls/poll-'.$qID);	
}

if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$aTitle = trim($_POST['aTitle']);
if (strlen($aTitle) < 1 || strlen($aTitle) > 255) {
$error[] = 'Option name must be at between 1 and 255 charactors.';
}

// if valadation is okay then carry on
if (!$error) {
	
	// post form data 
   $aTitle     = $_POST['aTitle'];
   
   //strip any tags from input
   $aTitle   = strip_tags($aTitle);   
   
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$aTitle   = addslashes($aTitle);
	 }
   
   // remove any harhful code and stop sql injection
   $aTitle = mysql_real_escape_string($aTitle);    
  
// insert data into images table
$query = "INSERT INTO ".PREFIX."answers (qID, aTitle) VALUES ('$qID', '$aTitle')";
  $result  = mysql_query($query) or die ('Cannot add option because: '. mysql_error());

 
if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'Poll Option Added';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'Poll Option Added';
url('manage-add-ons/polls/poll-'.$qID);	
} 
 
 
}
}

	
//dispaly any errors
errors($error);

?>

<form action="" method="post">
<p><label>Option Name:</label> <input type="text" class="box-medium tooltip-right" title="Add Poll Option" name="aTitle" <?php if (isset($error)){ echo "value=\"$aTitle\""; }?>/></p>

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save page and return to poll options">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save and return to poll options">
</form>
</div>
<?php

} else {
url(DIR);

}	
}

function editPollOption()
{
	$curpage = true;
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Edit Poll Option</h3></div> 			
<div class=\"content-box-content\">";

$sql = mysql_query("SELECT * FROM ".PREFIX."answers WHERE aID='{$_GET['edit-option']}'")or die(mysql_error());
$row = mysql_fetch_object($sql);

$sql = mysql_query("SELECT * FROM ".PREFIX."questions WHERE qID='$row->qID'")or die(mysql_error());
$row = mysql_fetch_object($sql);
$qTitle = $row->qTitle;
$qID = $row->qID;
	
echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Modules</a> > <a href=\"".DIRADMIN."manage-add-ons/polls\">Polls</a> > <a href=\"".DIRADMIN."manage-add-ons/polls/poll-$qID\">$qTitle</a> > Edit Poll Option</p></div>";

messages();

if(isset($_POST['cancel'])){
$_SESSION['info'] = "Option was not updated";
url('manage-add-ons/polls/poll-'.$qID);	
}

if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// check feilds are not empty
$aTitle = trim($_POST['aTitle']);
if (strlen($aTitle) < 1 || strlen($aTitle) > 255) {
$error[] = 'Poll name must be at between 1 and 255 charactors.';
}

// if valadation is okay then carry on
if (!$error) {
	
	// post form data 
   $aID     = $_POST['aID'];
   $aTitle = $_POST['aTitle'];
   $qID    = $_POST['qID'];
   
   //strip any tags from input
   $aTitle   = strip_tags($aTitle);   
   
   // add slashes if needed
   if(!get_magic_quotes_gpc())
	{ 
	$aTitle   = addslashes($aTitle);
	 }
   
   // remove any harhful code and stop sql injection
   $aTitle = mysql_real_escape_string($aTitle);    
  

// insert data into images table
$query = "UPDATE ".PREFIX."answers SET aTitle = '$aTitle' WHERE aID='$aID'";
$result  = mysql_query($query) or die ('Cannot Update option because: '. mysql_error());
 	
if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'Poll Option Added';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'Poll Option Added';
url('manage-add-ons/polls/poll-'.$qID);	
} 
	
 
}
}

	
//dispaly any errors
errors($error);
$result = mysql_query("SELECT * FROM ".PREFIX."answers WHERE aID='{$_GET['edit-option']}'");
while($row = mysql_fetch_object($result)){
?>

<form action="" method="post">
<input type="hidden" name="aID" value="<?php echo $row->aID;?>" />
<input type="hidden" name="qID" value="<?php echo $row->qID;?>" />
<p><label>Option Name:</label> <input class="box-medium tooltip-right" title="Update Poll Option" type="text" name="aTitle" value="<?php echo stripslashes($row->aTitle);?>"/></p>

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save page and return to poll options">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save and return to poll options">
</form>	
<?php }
echo "</div>";
} else {
url(DIR);

}	
}


function pollRequest()
{
	if(isset($_GET['polls'])){
	managePolls();
	$curpage = true;
	}
	
	if(isset($_GET['add-poll'])){
	addPoll();
	$curpage = true;
	}
	
	if(isset($_GET['edit-poll'])){
	editPoll();
	$curpage = true;
	}
	
	if(isset($_GET['poll'])){
	managePollOption();
	$curpage = true;
	}
	
	
	if(isset($_GET['add-option'])){
	addPollOption();
	$curpage = true;
	}
	
	if(isset($_GET['edit-option'])){
	editPollOption();
	$curpage = true;
	}
	
	if(isset($_GET['delPoll'])){
	delPoll();
	$curpage = true;
	}
	
	if(isset($_GET['delpolloption'])){
	delpolloption();
	$curpage = true;
	}
}

function delPoll() {

$delType = $_GET['delType'];
$delID   = $_GET['delID'];
$delID = mysql_real_escape_string($delID);

if($delType == 'delpoll')
{
    $query = "DELETE FROM ".PREFIX."questions WHERE qID = '$delID'";
  	mysql_query($query) or die('Error : ' . mysql_error());

	$_SESSION['success'] = 'Deleted';
	url('manage-add-ons/polls');
}
}

function delOption() {

$delType = $_GET['delType'];
$delID   = $_GET['delID'];
$delID = mysql_real_escape_string($delID);

if($delType == 'delpolloption')
{
	$query = "SELECT * FROM  ".PREFIX."answers  WHERE aID = '$delID'";
	$result = mysql_query($query) or die('problem: ' . mysql_error());

	while ( $row = mysql_fetch_array ($result)) {
	global $alb;
	$qlb = $row['qID'];
	}

	global $alb;

  $query = "DELETE FROM ".PREFIX."answers WHERE aID = '$delID'";
  mysql_query($query) or die('Error : ' . mysql_error());
  
  $_SESSION['success'] = 'Deleted';
  url('manage-add-ons/polls/poll-'.$qlb);	

}
}



function poll($string) 
{	

  //plugin for albums
$qsql = mysql_query("SELECT * FROM ".PREFIX."questions");
while ($qRow = mysql_fetch_object($qsql))
{ 
	$mystring = $string;
	$findme   = "[$qRow->qTag]";
	$pos = strpos($mystring, $findme);
	
if ($pos !== false) {

if(isset($_POST['results'])){header("Location: ?pid=$qRow->qID");}

//stage 1
if(isset($_POST['submit'])){

if(count($_POST)-1 != 2){
$pollerror[] = 'Please answer a question';
}

if (isset($_COOKIE['polluser'])) {
    foreach ($_COOKIE['polluser'] as $name => $value) {
        if($value == $_POST['qID']){
		$pollerror[] = "You've already voted!!";
		}
    }
}



if(!$pollerror){

//get qID and answer ID 	
$qID = $_POST['qID'];
$aID = $_POST['a'][0];

$sql = mysql_query("UPDATE ".PREFIX."answers SET aPoints=aPoints+1 WHERE qID='$qID' AND aID='$aID'")or die(mysql_error()); 
$expire=time()+60*60*24*30;//last 30 days
setcookie("polluser[$qID]", "$qID", $expire);

$page = $_GET['ispage'];

header("Location: ".DIR.$page."?pid=$qID");
	
}
}



$qMatch = "[$qRow->qTag]"; //match against in return string


if(!$_GET['pid']){

if (!empty($pollerror))
{
		$i = 0;
		while ($i < count($pollerror)){
		$qOutput.= "<div class=\"msg-error hidethis\">".$pollerror[$i]."</div>";
		$i ++;}
}// close if empty errors

$getTag = $qRow->qTag;

$qsql2 = mysql_query("SELECT * FROM ".PREFIX."questions WHERE qTag='$getTag'");
if(mysql_num_rows($qsql2) > 0){


$qOutput.="<form action=\"\" method=\"post\">\n";

while($qr = mysql_fetch_object($qsql2)){
$qOutput.="<input type=\"hidden\" name=\"qID\" value=\"$qr->qID\"/>";
$qOutput.="<p><b>$qr->qTitle</b></p>\n";

	$a = mysql_query("SELECT * FROM ".PREFIX."answers WHERE qID='$qr->qID'");
	while($ar = mysql_fetch_object($a)){
	
		$qOutput.="<p><label>".stripslashes($ar->aTitle)."</label><input type=\"radio\" name=\"a[]\" value=\"$ar->aID\" /></p>\n";
	}
	global $qID;
	$qID = $qr->qID;
}

$qOutput.="<p><input type=\"submit\" name=\"submit\" class=\"button\" value=\"Submit\">&nbsp;<input type=\"submit\" name=\"results\" class=\"button\" value=\"Results\"></p></form>\n";

}
} 

//stage 2

if($_GET['pid']){

$resget = $_GET['pid'];

//get total votes
$sql = mysql_query("SELECT SUM(aPoints) as aPoints FROM ".PREFIX."answers WHERE qID='$resget'");
$t = mysql_fetch_object($sql);
$total = $t->aPoints;

if($total == ''){
$qOutput.= "<p>Not a valid poll!</p>";
} else {

//$qOutput.= "<p>Total Votes: $total</p>";

	$a = mysql_query("SELECT * FROM ".PREFIX."answers WHERE qID='$resget' ORDER BY APoints DESC");
	
	
	$qOutput.= "<p><strong>$qRow->qTitle</strong></p>";
    $qOutput.= "<div class=poll>";
	
	while($ar = mysql_fetch_object($a))
	{
		//work out percentage			
		if($ar->aPoints != 0) {	$VotePercent = round(($ar->aPoints / $total) * 100)."%";} else { $VotePercent = 0 ."%";}	
		
		$qOutput.= "".stripslashes($ar->aTitle)."
 		<strong>$ar->aPoints</strong><br />
		<div class=\"pollbar\">
			<div class=\"pollpercent\" style=\"width: $VotePercent;\">$VotePercent</div>
		</div>";		
	}
	
	$qOutput.="<p>Total votes: <strong>$total</strong></p></div>";
	
	/*
	
	$qOutput.= "<ul>";
	
	while($ar = mysql_fetch_object($a))
	{
		//work out percentage			
		if($ar->aPoints != 0) {	$VotePercent = round(($ar->aPoints / $total) * 100)."%";} else { $VotePercent = 0 ."%";}
		
		
		$qOutput.= "<li>
				<span class=\"total-votes\">$ar->aPoints</span>".stripslashes($ar->aTitle)."
				<br />
				<div class=\"results-bar\" style=\"width:$VotePercent;\">$VotePercent</div>
			</li>";

				
		//$qOutput.= "<p>".stripslashes($ar->aTitle)."<br /><span class=\"resultBar\" style=\"width:20%\">$ar->aPoints ($VotePercent)</span></p>\n";
	}
	$qOutput.= "</ul>";
	*/
$qOutput.= "<p><a href=\"?vpid=$resget\" class=\"button\">Back to Poll</a></p>";
}
}


}//close if
	
	$string = str_replace("$qMatch", $qOutput, $string);
} //close first while  
  
  return $string;
}


function addLinkspoll() {
	echo "bk<div class=\"icon\">\n";
		echo "<a href=\"".DIR."admin/manage-add-ons/polls\"><img src=\"".DIR."assets/plugins/polls/poll.png\" alt=\"Polls\" title=\"Manage Polls\" class=\"tooltip-top\" /></a>\n";
    	echo "<p><a href=\"".DIR."admin/manage-add-ons/polls\" title=\"Manage Polls\" class=\"tooltip-top\">Polls</a></p>\n";
    echo "</div>\n";	
}

//add hook, where to execute a function
add_hook('admin_modules','addLinkspoll');
add_hook('cont','poll');
add_hook('del', 'delPoll');
add_hook('del', 'delOption');
add_hook('page_requester','pollRequest');
?>
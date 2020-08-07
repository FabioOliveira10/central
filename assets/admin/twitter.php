<?php

//$user = $to->OAuthRequest('https://twitter.com/account/verify_credentials.xml', array(), 'GET');

//echo '<pre>';
//print_r($user);
//echo '</pre>';

//echo "token " . $_SESSION['oauth_access_token'];
//echo " secret " . $_SESSION['oauth_access_token_secret'];


if (isglobaladmin() || isadmin()){

echo "<div class=\"content-box-header\"><h3>Twitter</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > Twitter</p></div>";

messages();

// if form submitted then process form
if (isset($_POST['tsub'])){

$msg = trim($_POST['comment']);
if (strlen($msg) < 1 ) {
$error[] = 'Please enter a message';
}

// if valadation is okay then carry on
if (!$error) {

$msg = stripslashes($msg);
$msg = strtolower($msg);
	
//post to twitter
$content = $to->OAuthRequest('https://twitter.com/statuses/update.xml', array('status' => $msg), 'POST');

if ($content->error!='') {
echo '<h2>ERROR: '.$content->error.'</h2>';
}

$_SESSION['success'] = "Message posted to Twitter.";
header('Location: '.DIRADMIN.'manage-add-ons/twitter');
exit();

}
}
	
//dispaly any errors
echo errors($error);


?>

<form name="myform" METHOD=POST>
<textarea name=comment wrap=physical rows=3 cols=50 onkeyup=limiter()></textarea><br>
<sub>(Maximum characters: 140)<br />
You have 
<script type="text/javascript">
document.write("<input type=text name=limit size=4 readonly value="+count+">");
</script>
characters left.</sub></p>
<p><input type="submit" name="tsub" class="button" value="Submit to Twitter" /></p>
</form>

<?php

$usertimeline = $to->OAuthRequest('http://twitter.com/statuses/user_timeline.xml', array(), 'GET');

$tweeters = new SimpleXMLElement($usertimeline);

//print_r($tweeters);

foreach ($tweeters->status as $twit1) {


//This finds any links in $description
$description = $twit1->text;

$description = preg_replace("#(^|[\n ])@([^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://www.twitter.com/\\2\" >@\\2</a>'", $description);  
$description = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t<]*)#ise", "'\\1<a href=\"\\2\" >\\2</a>'", $description);
$description = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://\\2\" >\\2</a>'", $description);

echo "<div class='user'><a href=\"http://www.twitter.com/", $twit1->user->screen_name,"\" target=\"_blank\"><img border=\"0\" class=\"twitter_followers\" src=\"", $twit1->user->profile_image_url, "\" title=\"", $twit1->name, "\" /></a>\n";
echo "<div class='name'>", $twit1->user->name,"</div>";
echo "<div class='followers'>", $twit1->user->location,"</div>";
echo "<div class='location'>", $twit1->user->url,"</div>";
echo "<div class='text'>".$description." <div class='description'>From ", $twit1->source,"</div></div></div>";}

echo "</div>";

} else {
header('Location: '.DIR);
exit;
}	
?>
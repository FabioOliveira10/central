<?php
/*-------------------------------------------------------+
| The One Point - Content Management System
| http://www.theonepoint.co.uk/
+--------------------------------------------------------+
| Author: David Carr  Email: d.carr@theonepoint.co.uk
+--------------------------------------------------------+*/

ob_start();
session_start();
error_reporting(E_ALL ^ E_NOTICE); 
//define installed
define('installed', 1);

// db properties
define('DBHOST','cust-mysql-123-03');
define('DBUSER','umii_592141_0001');
define('DBPASS','yGjwuvb4');
define('DBNAME','miianducouk_592141_db1');

date_default_timezone_set('Europe/London');


// make a connection to mysql here
$conn = mysql_connect (DBHOST, DBUSER, DBPASS);
$conn = mysql_select_db (DBNAME);
if(!$conn){
	die( "Sorry! There seems to be a problem connecting to our database.");
}

//define table prefix
define('PREFIX','cent_');

//define include checker
define('included', 1);

//define version
define('CMSV', '4.0');

$q = mysql_query("SELECT * FROM ".PREFIX."settings");
$r = mysql_fetch_object($q);

// define site path
define('DIR',$r->siteAddress);

// define admin site path
define('DIRADMIN',$r->siteAddress.'admin/');

// define site title for top of the browser
define('SITETITLE',$r->siteTitle);

//define site email
define('SITEEMAIL',$r->siteAddress);

//define site email
define('SETTINGSADDRESS',$r->siteSettingsAddress);

//define site email
define('EDITORSETTINGS',$r->siteEditorAddress);

function pingSE($sitemap,$service){

	switch ($service) {
		case 'bing':
			$ping = "http://www.bing.com/webmaster/ping.aspx?siteMap=$sitemap";
			break;
		case 'ask':
			$ping = "http://submissions.ask.com/ping?sitemap=$sitemap";
			break;
		case 'google':
			$ping = "http://www.google.com/webmasters/sitemaps/ping?sitemap=$sitemap";
			break;		
		default:
      		 return false;
	}

	$curl_handle=curl_init();
	curl_setopt($curl_handle,CURLOPT_URL,$ping);
	curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
	curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
	$buffer = curl_exec($curl_handle);
	curl_close($curl_handle);
}
?>
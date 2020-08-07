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

if (preg_match('/google-map/', $data))
{
} else { 
$newData = "
RewriteRule ^admin/manage-add-ons/google-map$                    admin.php?google-map=$1 [L]
RewriteRule ^admin/manage-add-ons/google-map/$                   admin.php?google-map=$1 [L]

RewriteRule ^admin/manage-add-ons/google-map/settings$           admin.php?settings=$1 [L]
RewriteRule ^admin/manage-add-ons/google-map/settings/$          admin.php?settings=$1 [L]\n###\n";

$data = str_replace("###",$newData,$data);

$myFile = $cfile;
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $data);
fclose($fh);
global $prefix;
mysql_query("CREATE TABLE IF NOT EXISTS `".PREFIX."map` (
  `mapID` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `postcode` varchar(255) NOT NULL,
  `tel` varchar(255) NOT NULL,
  `mainwidth` varchar(255) NOT NULL,
  `mainheight` varchar(255) NOT NULL,
  `sidebarwidth` varchar(255) NOT NULL,
  `sidebarheight` varchar(255) NOT NULL,
  `longatude varchar(255) NOT NULL,
  `latitude varchar(255) NOT NULL,
  PRIMARY KEY  (`mapID`)
) ENGINE=MyISAM");


mysql_query("INSERT INTO `".PREFIX."map` (`mapID`, `title`, `address`, `city`, `postcode`, `tel`, `mainwidth`, `mainheight`, `sidebarwidth`, `sidebarheight`, `longatude`, `longatude`) VALUES
(1, '', '', '', '', '', '', '100%', '500px', '250px', '300px', '','')");

}

function mapauth()
{
$msql = mysql_query("SELECT * FROM ".PREFIX."map")or die(mysql_error());
$mr = mysql_fetch_object($msql);

$houtput.="
<script type=\"text/javascript\" src=\"http://maps.google.com/maps/api/js?sensor=false\"></script>
<script type=\"text/javascript\">
function initialize() {
	var geocoder;
	var map;
	var address = '$mr->address $mr->city ';\n";
	
	if($mr->longatude != '' && $mr->latitude != ''){
		$houtput.="var latlng = new google.maps.LatLng($mr->longatude, $mr->latitude);";
	} else {
		 $houtput.="geocoder = new google.maps.Geocoder();\n
    	var latlng = new google.maps.LatLng(-34.397, 150.644);";
	}	
	
   $houtput.="
    var myOptions = {
      zoom: 15,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById(\"map\"), myOptions);

    var contentString = '<div id=\"infoContent\">'+
        '$mr->title'+
        '<div id=\"infoBody\">'+
        '<p>$mr->address <br /> $mr->city <br /> $mr->postcode <br /> $mr->tel</p>'+
		'<p><a href=\"http://maps.google.com/maps?saddr=&daddr=$mr->address $mr->city\" target =\"_blank\">Get Directions</a></p>'+
        '</div>'+
        '</div>';";
		
   $houtput.="		    
   if (geocoder) {
      geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          map.setCenter(results[0].geometry.location);
          var marker = new google.maps.Marker({
              map: map, 
              position: results[0].geometry.location,
			  title: '$row->title'
        });
		  
		var infowindow = new google.maps.InfoWindow({
        content: contentString,
        maxWidth: 700
   });

      google.maps.event.addListener(marker, 'click', function() {
      infowindow.open(map,marker);
      });
		  
   } else {
      alert(\"Geocode was not successful for the following reason: \" + status);
   }";    

     $houtput.="});
    }
}//close initialize
</script>";
	return $houtput;

}



function managemap()
{
	global $curpage;
$curpage = true;
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Google Map</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > Google Map</p></div>";

echo messages();
?>

<p><a href="<?php echo DIRADMIN;?>manage-add-ons/google-map/settings" class="button tooltip-right" title="Change Google Map Settings">Google Map Settings</a></p>

<p>To implement the Google Map insert [map] into any page you want the Google Map to be displayed. For sidebars use [side-map]</p>

<?php echo mapauth(); 

$sql = mysql_query("SELECT * FROM ".PREFIX."map LIMIT 1")or die(mysql_error());
  $r = mysql_fetch_object($sql);  
  
  echo "<div id=\"map\" style=\"color:#000; width:$r->mainwidth; height:$r->mainheight\"></div>";

?>

</div>
<?php

} else {
url(DIRADMIN);
}
}

function mapsettings()
{
	global $curpage;
    $curpage = true;
	if (isglobaladmin() || isadmin()){
		
echo "<div class=\"content-box-header\"><h3>Google Map Settings</h3></div> 			
<div class=\"content-box-content\">";

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > <a href=\"".DIRADMIN."manage-add-ons/google-map\">Google Map</a> > Settings</p></div>";

messages();

if(isset($_POST['cancel'])){
$_SESSION['info'] = "Google Map was not updated";
url('manage-add-ons/google-map');
}

if (isset($_POST['submit']) || isset($_POST['backsubmit'])){

// if valadation is okay then carry on
if (!$error) {
	
	// post form data 
   $title     = $_POST['title'];
   $address   = $_POST['address'];
   $city      = $_POST['city'];
   $postcode  = $_POST['postcode'];
   $tel       = $_POST['tel'];
   $mainwidth     = $_POST['mainwidth'];
   $mainheight    = $_POST['mainheight'];
   $sidebarwidth  = $_POST['sidebarwidth'];
   $sidebarheight = $_POST['sidebarheight'];
   $longatude     = $_POST['longatude'];
   $latitude      = $_POST['latitude'];
	
   
   //strip any tags from input
   $title     = strip_tags($title);
   $address   = strip_tags($address);
   $city      = strip_tags($city);
   $postcode  = strip_tags($postcode);
   $tel       = strip_tags($tel); 
   $mainwidth     = strip_tags($mainwidth); 
   $mainheight    = strip_tags($mainheight); 
   $sidebarwidth  = strip_tags($sidebarwidth); 
   $sidebarheight = strip_tags($sidebarheight);   
   

   // remove any harhful code and stop sql injection
   $title     = mysql_real_escape_string($title);
   $address   = mysql_real_escape_string($address);
   $city      = mysql_real_escape_string($city);
   $postcode  = mysql_real_escape_string($postcode);
   $tel       = mysql_real_escape_string($tel);
   $mainwidth     = mysql_real_escape_string($mainwidth); 
   $mainheight    = mysql_real_escape_string($mainheight); 
   $sidebarwidth  = mysql_real_escape_string($sidebarwidth); 
   $sidebarheight = mysql_real_escape_string($sidebarheight);
   $longatude     = mysql_real_escape_string($longatude);
   $latitude      = mysql_real_escape_string($latitude);     
  

// insert data into images table
$query = "UPDATE ".PREFIX."map SET title = '$title', address='$address', city='$city', postcode='$postcode', tel='$tel', mainwidth='$mainwidth', mainheight='$mainheight', sidebarwidth='$sidebarwidth', sidebarheight='$sidebarheight', longatude='$longatude', latitude='$latitude' WHERE mapID='1'";
$result  = mysql_query($query);
 	
if(isset($_POST['backsubmit'])){
$_SESSION['success'] = 'Map updated';
header('Location:' . $_SERVER['HTTP_REFERER'].'');
exit();
}

if(isset($_POST['submit'])){
$_SESSION['success'] = 'Map updated';
url('manage-add-ons/google-map');
}	
 
}
}

	
//dispaly any errors
errors($error);
$result = mysql_query("SELECT * FROM ".PREFIX."map WHERE mapID='1'")or die(mysql_error());
while($row = mysql_fetch_object($result)){
?>

<form action="" method="post">

<p><label>Title:</label> <input class="box-medium tooltip-right" title="Location Name" name="title" type="text" value="<?php echo $row->title;?>" size="50"/>
</p>
<p><label>Address:</label> <input class="box-medium tooltip-right" title="Building number, street name" name="address" type="text" value="<?php echo $row->address;?>" size="50"/>
</p>
<p><label>City:</label> <input class="box-medium tooltip-right" title="Enter City" name="city" type="text" value="<?php echo $row->city;?>" size="50"/>
</p>
<p><label>Post Code:</label> <input class="box-medium tooltip-right" title="Location Post Code" name="postcode" type="text" value="<?php echo $row->postcode;?>" size="50"/>
</p>
<p><label>Tel:</label> <input class="box-medium tooltip-right" title="Telephone Number" name="tel" type="text" value="<?php echo $row->tel;?>" size="50"/></p>

<p><label>Longatude: (optional)</label> <input class="box-medium tooltip-right" title="Set longatude coordinates if needed" name="longatude" type="text" value="<?php echo $row->longatude;?>" size="50"/>
<a href="http://www.satsig.net/maps/lat-long-finder.htm" title="Latitude and Longitude Finder" class="example2demo" name="windowX">Latitude and Longitude Finder</a>
<script type="text/javascript"> 
$('.example2demo').popupWindow({ 
height:500, 
width:800,
centerBrowser:1 
}); 
</script>
</p>

<p><label>Latitude: (optional)</label> <input class="box-medium tooltip-right" title="Set latitude coordinates if needed" name="latitude" type="text" value="<?php echo $row->latitude;?>" size="50"/></p>

<p><label>Main Width:</label> <input class="box-medium tooltip-right" title="Width of main map in px or %" name="mainwidth" type="text" value="<?php echo $row->mainwidth;?>" size="50"/></p>

<p><label>Main Height:</label> <input class="box-medium tooltip-right" title="Height of main map in px or %" name="mainheight" type="text" value="<?php echo $row->mainheight;?>" size="50"/></p>

<p><label>Sidbar Width:</label> <input class="box-medium tooltip-right" title="Width of main map in px or %" name="sidebarwidth" type="text" value="<?php echo $row->sidebarwidth;?>" size="50"/></p>

<p><label>Sidebar Height:</label> <input class="box-medium tooltip-right" title="Height of sidebar map in px or %" name="sidebarheight" type="text" value="<?php echo $row->sidebarheight;?>" size="50"/></p>

<input type="submit" name="submit" class="button tooltip-top" value="Submit" title="Save page and return to Google Map">
<input type="submit" name="backsubmit" class="button tooltip-top" value="Apply" title="Save page and stay on this page">
<input type="submit" name="cancel" class="button tooltip-top" value="Cancel" title="Don't save page and return to Google Map">
</form>	
</div>

<?php }

} else {
url(DIRADMIN);
}	
}


function mapRequest()
{
	if(isset($_GET['google-map'])){
	managemap();
	$curpage = true;
	}
	
	if(isset($_GET['settings'])){
	mapsettings();
	$curpage = true;
	}
}


function map($string) 
{	
  $sql = mysql_query("SELECT * FROM ".PREFIX."map LIMIT 1")or die(mysql_error());
  $r = mysql_fetch_object($sql);  
  
  $mapOutput.="<div id=\"map\" style=\"color:#000; width:$r->mainwidth; height:$r->mainheight\"></div>";
  $mapOutput.='
  <script type="text/javascript" src="'.DIR.'assets/templates/js/jquery-1.4.2.min.js"></script>
  <script type="text/javascript" charset="utf-8">  
	  $(document).ready(function(){
		initialize();
	  });
  </script>';
  
  $string = str_replace("[map]", $mapOutput, $string);
  return $string;
}

function mapsidebar($string) 
{	
  $sql = mysql_query("SELECT * FROM ".PREFIX."map LIMIT 1")or die(mysql_error());
  $r = mysql_fetch_object($sql); 
  
  $mapOutput.="<div id=\"map\" style=\"color:#000; width:$r->sidebarwidth; height:$r->sidebarheight\"></div>";
  $mapOutput.='
  <script type="text/javascript" src="'.DIR.'assets/templates/js/jquery-1.4.2.min.js"></script>
  <script type="text/javascript" charset="utf-8">  
	  $(document).ready(function(){
		initialize();
	  });
  </script>';
  
  $string = str_replace("[side-map]", $mapOutput, $string);
  return $string;
}

function addLinksmap() {
	echo "bk<div class=\"icon\">\n";
		echo "<a href=\"".DIR."admin/manage-add-ons/google-map\"><img src=\"".DIR."assets/plugins/google-map/google-map.png\" alt=\"Google Map\" title=\"Manage Google Map\" class=\"tooltip-top\" /></a>\n";
    	echo "<p><a href=\"".DIR."admin/manage-add-ons/google-map\" title=\"Manage Google Map\" class=\"tooltip-top\">Google Map</a></p>\n";
    echo "</div>\n";	
}


//add hook, where to execute a function
add_hook('admin_modules','addLinksmap');
add_hook('cont','map');
add_hook('cont','mapsidebar');
add_hook('page_requester','mapRequest');
add_hook('header_js_script','mapauth');
?>
<?php
/*-------------------------------------------------------+
| The One Point - Content Management System
| http://www.theonepoint.co.uk/
+--------------------------------------------------------+
| Author: David Carr  Email: d.carr@theonepoint.co.uk
+--------------------------------------------------------+*/
header('Content-type: application/xml'); 

require ('../includes/config.php');

echo "<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\n";
echo "<channel>\n";

echo "<title>".SITETITLE." News RSS Feed</title>\n";
echo "<description>News RSS Feeds</description>\n";
echo "<link>".DIR."</link>\n";

$result = mysql_query("SELECT * FROM ".PREFIX."news WHERE newsID ORDER BY newsID DESC LIMIT 10")or die(mysql_error());
while ($row = mysql_fetch_object($result)) {	
	
	$row->newsTitle = str_replace("&","&amp;",$row->newsTitle);
	$row->newsDesc = str_replace("&rdquo;","”",$row->newsDesc);
	$row->newsDesc = str_replace("&ldquo;","“",$row->newsDesc);	
	//$row->newsDesc = str_replace("&nbsp;","",$row->newsDesc);
	//$row->newsSlug = str_replace("&","&amp;",$row->newsSlug);
	$row->newsTitle = stripslashes($row->newsTitle);
	$row->newsDesc = stripslashes($row->newsDesc);	
	$row->newsSlug = stripslashes($row->newsSlug);
	$row->newsSlug = "$row->newsSlug";		
	 
	 echo "<item>\n";
	 echo "<title>$row->newsTitle</title>\n";
	 echo "<description><![CDATA[$row->newsDesc]]></description>\n";
	 echo "<pubDate>".date('D, d M Y',strtotime($row->newsDate))." $row->newsTime 0000</pubDate>\n";	 
	 echo "<link>".DIR."$row->newsSlug</link>\n";
	 echo "<guid>".DIR."$row->newsSlug</guid>\n";
	 echo "<atom:link href=\"".DIR."$row->newsSlug\" rel=\"self\" type=\"application/rss+xml\"/>\n";
	 echo "</item>\n";

}
  
echo "</channel>\n";

echo "</rss>\n";
?>
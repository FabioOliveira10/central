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

echo "<title>".SITETITLE." Blog RSS Feed</title>\n";
echo "<description>Blog RSS Feeds</description>\n";
echo "<link>".DIR."</link>\n";

$result = mysql_query("SELECT * FROM ".PREFIX."blog_posts WHERE postID ORDER BY postID DESC LIMIT 10")or die(mysql_error());
while ($row = mysql_fetch_object($result)) {	
	
	$row->postTitle = str_replace("&","&amp;",$row->postTitle);
	$row->postDesc = str_replace("&rdquo;","”",$row->postDesc);
	$row->postDesc = str_replace("&ldquo;","“",$row->postDesc);	
	//$row->postDesc = str_replace("&nbsp;","",$row->postDesc);
	//$row->postSlug = str_replace("&","&amp;",$row->postSlug);
	$row->postTitle = stripslashes($row->postTitle);
	$row->postDesc = stripslashes($row->postDesc);	
	$row->postSlug = stripslashes($row->postSlug);
	$row->postSlug = "$row->postSlug";		
	 
	 echo "<item>\n";
	 echo "<title>$row->postTitle</title>\n";
	 echo "<description><![CDATA[$row->postDesc]]></description>\n";
	 echo "<pubDate>".date('D, d M Y',strtotime($row->postDate))." $row->postTime 0000</pubDate>\n";	 
	 echo "<link>".DIR."p-$row->postSlug</link>\n";
	 echo "<guid>".DIR."$row->newsSlug</guid>\n";
	 echo "<atom:link href=\"".DIR."p-$row->postSlug\" rel=\"self\" type=\"application/rss+xml\"/>\n";
	 echo "</item>\n";

}
  
echo "</channel>\n";

echo "</rss>\n";
?>
<?php
/*-------------------------------------------------------+
| The One Point - Content Management System
| http://www.theonepoint.co.uk/
+--------------------------------------------------------+
| Author: David Carr  Email: d.carr@theonepoint.co.uk
+--------------------------------------------------------+*/

require ('assets/includes/config.php');
header('Content-type: text/xml');
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<?xml-stylesheet type=\"text/xsl\" href=\"sitemapxsl.php\"?>\n";
?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

<?php


$sql = mysql_query("SELECT * FROM ".PREFIX."pages WHERE pageActive='1' ORDER BY pageParent")or die(mysql_error());

while($row = mysql_fetch_object($sql))
{
	echo "<url>\n";
	echo "<loc>".DIR."$row->pageSlug</loc>\n";
	echo "<changefreq>weekly</changefreq>\n";
	echo "<priority>1.0</priority>\n";
	echo "</url>\n";
}	


$sql = mysql_query("SELECT * FROM ".PREFIX."blog_posts");
while($row = @mysql_fetch_object($sql))
{
	echo "<url>\n";
	echo "<loc>".DIR."p-$row->postSlug</loc>\n";
	echo "<changefreq>weekly</changefreq>\n";
	echo "<priority>1.0</priority>\n";
	echo "</url>\n";
}	


$sql = mysql_query("SELECT * FROM ".PREFIX."news");
while($row = @mysql_fetch_object($sql))
{
	echo "<url>\n";
	echo "<loc>".DIR."$row->postSlug</loc>\n";
	echo "<changefreq>weekly</changefreq>\n";
	echo "<priority>1.0</priority>\n";
	echo "</url>\n";
}	




	
echo "</urlset>\n";

?>
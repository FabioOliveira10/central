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

if (preg_match('/search/', $data))
{
} else { 
global $loc;
$newData = "
RewriteRule ^admin/manage-add-ons/search$                    admin.php?manage-search=$1 [L]
RewriteRule ^admin/manage-add-ons/search/$                   admin.php?manage-search=$1 [L]\n###";

$data = str_replace("###",$newData,$data);

$myFile = $cfile;
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $data);
fclose($fh);
}

function managesearch()
{
global $curpage;
$curpage = true;
if (isglobaladmin($prefix) || isadmin($prefix)){

echo "<div class=\"content-box-header\"><h3>Search</h3></div> 			
<div class=\"content-box-content\">";	

echo "<div id=\"bread\"><p><a href=\"".DIRADMIN."\">Home</a> > <a href=\"".DIRADMIN."manage-add-ons\">Manage Add-ons</a> > Search</p></div>";
?>

<p>To implement the search form insert [search] into any sidebar panel you want the search form to be displayed.<br  />Then create a standalone page with [search-results] in the content to display the search results.</p>

<p>The form is shown below for demonstration purposes.</p>


<form action="" method="get">
<p><input name="search" type="text" value="" size="15" disabled="disabled" />
<input type="submit" name="submit" value="Search" class="searhcbtn" disabled="disabled"/></p>
</form>
</div>
<?php
} else {
header('Location: '.DIRADMIN);
exit;
}	
}

function searchRequest()
{
	if(isset($_GET['manage-search'])){
	managesearch();
	$curpage = true;
	}
	
}

function search($string) 
{	
	
	$output.= "<form id=\"form-search\" method=\"get\" action=\"".DIR."search\">
  <input type=\"text\" name=\"search\" id=\"input-keywords\" value=\"\" /><input name=\"submit\" id=\"submit-search\" type=\"image\" src=\"".DIR."assets/templates/images/btn_go.jpg\" alt=\"Search\" title=\"Search\" value=\"submit\" />
	</form>";
	
	
	
	$string = str_replace("[search]", $output, $string);  
    return $string;
}

function dosearch($string)
{

//Post search words
$search = $_GET['search'];
$search = strip_tags($search);
$search = mysql_real_escape_string($search);

//No keywords entered.
if ($search == "" || $search == "Site Search...")
{
 $plugcont.= "<h1>Opps! You forgot to enter a search word</h1>\n";
  } else {
 

	
	//----------------- pages results
	
	 $sql = "SELECT * FROM ".PREFIX."pages WHERE MATCH(pageTitle,pageMetaKeywords,pageMetaDescription) AGAINST('$search*' IN BOOLEAN MODE)";
	$result = mysql_query($sql) or die("Problem, with Query:".mysql_error());
	$count1 = mysql_num_rows($result);
	if($count1 !==0){
		$plugcont.= "<h1>Pages Search Results</h1>\n";
		if($count1 > 1){
		$matches = "are $count1 matches";
		} else {
		$matches = "is $count1 match";
		}
		$plugcont.= "<p>you searched for <b>$search</b> there $matches.</p>\n"; 
	
		while ($row = mysql_fetch_object($result))
		{
			$plugcont.="<p><a href=\"".DIR."$row->pageSlug\">$row->pageTitle</a></p>\n";
		}
	}	
			
			
			
			
	//---------------- blog results            
	
	
	$result = mysql_query("SELECT * FROM ".PREFIX."blog_posts WHERE MATCH(postTitle,postMetaKeywords,postMetaDescription) AGAINST('$search*' IN BOOLEAN MODE)");
	$count2 = mysql_num_rows($result);
	if($count2 !==0){
		$plugcont.= "<h1>Blog Search Results</h1>\n";
		if($count2 > 1){
		$matches = "are $count2 matches";
		} else {
		$matches = "is $count2 match";
		}
		$plugcont.= "<p>you searched for <b>$search</b> there $matches.</p>\n";
		
		while ($row = mysql_fetch_object($result))
		{
			$plugcont.="<p><a href=\"".DIR."p-$row->postSlug\">$row->postTitle</a></p>\n";
		}
	}
	
	if($count1 == 0 && $count2 == 0){	
		$plugcont.= "<h1>Search Results</h1>\n";
		$plugcont.= "<p>you searched for <b>$search</b> there are 0 matches.</p>\n";	
	}

}

	$string = str_replace("[search-results]", $plugcont, $string);  
    return $string;
}

function addLinkssearch() {
	echo "bk<div class=\"icon\">\n";
		echo "<a href=\"".DIR."admin/manage-add-ons/search\"><img src=\"".DIR."assets/plugins/search/search.png\" alt=\"Search\" title=\"Manage Search\" class=\"tooltip-top\" /></a>\n";
    	echo "<p><a href=\"".DIR."admin/manage-add-ons/search\" title=\"Manage Search\" class=\"tooltip-top\">Search</a></p>\n";
    echo "</div>\n";	
}

//add hook, where to execute a function
add_hook('admin_modules','addLinkssearch');
add_hook('cont','search');
add_hook('cont','dosearch');
add_hook('page_requester','searchRequest');

?>
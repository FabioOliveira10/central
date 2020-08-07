<!-- NAV -->
<div id="navigation">
<ul class="sf-menu">
<?php
$result = mysql_query("SELECT * FROM ".PREFIX."pages WHERE pageID='1'");
$row = mysql_fetch_object($result);
?>
<li class="first"><a href="<?php echo DIR;?>" title="<?php echo $row->pageName;?>" <?php if ($_GET['ispage'] == '') { echo 'id="current"'; } ?>>Home</a></li> 

<?php
if(isglobaladmin() || isadmin()){
	$result = mysql_query("SELECT * FROM ".PREFIX."pages WHERE pageParent='0' AND pageActive='1' AND pageStandAlone='0' AND isRoot='1' ORDER BY pageOrder");
} else {
	$result = mysql_query("SELECT * FROM ".PREFIX."pages WHERE pageParent='0' AND pageActive='1' AND pageStandAlone='0' AND pageVis='3' AND isRoot='1' ORDER BY pageOrder");
}
$n = mysql_num_rows($result);
$i = 1;
while ($row = mysql_fetch_object($result))
{
		$parent1 = $row->pageID;
		if(isglobaladmin() || isadmin()){
			$result1 = mysql_query("SELECT * FROM ".PREFIX."pages WHERE pageParent='$parent1' AND pageActive='1' AND pageStandAlone='0' ORDER BY pageOrder");
		} else {
			$result1 = mysql_query("SELECT * FROM ".PREFIX."pages WHERE pageParent='$parent1' AND pageActive='1' AND pageStandAlone='0' AND pageVis='3' ORDER BY pageOrder");
		}
		
		
		$sub = "<ul>\n";
		while ($row1 = mysql_fetch_object($result1))
		{
			global $parent;
			$parent = $row1->pageParent;
			
			if (PAGE == $row1->pageSlug) { $cu = ' id="current"'; } elseif (PLUGPAGE == $row1->pageSlug) { $cu = ' id="current"'; }else { $cu='';} 
			
			if (preg_match("/http/i", $row1->pageSlug)) { $sub.= "		<li><a href=\"$row1->pageSlug\"$cu target=\"_blank\">$row1->pageTitle</a></li>\n"; } else { $sub.= "		<li><a href=\"".DIR."$row1->pageSlug\"$cu>$row1->pageTitle</a></li>\n";
			}
		}
		$sub.= "		</ul></li>\n";
	if (PAGE == $row->pageSlug) { $cu = ' id="current"'; }elseif (PLUGPAGE == $row1->pageSlug) { $cu = ' id="current"'; }else { $cu='';}
	if($n == $i){
	if (preg_match("/http/i", $row->pageSlug)) { echo "<li class=\"last\"><a href=\"$row->pageSlug\"$cu target=\"_blank\">$row->pageTitle</a>"; } else { echo "<li class=\"last\"><a href=\"".DIR."$row->pageSlug\"$cu>$row->pageTitle</a>";	
	} 
	
	} else {
	if($row->pageID==1){
		echo "<li><a href=\"".DIR."all-news\"$cu>$row->pageTitle</a>";
	} else {
	
	if (preg_match("/http/i", $row->pageSlug)) { echo "<li><a href=\"$row->pageSlug\"$cu target=\"_blank\">$row->pageTitle</a>";  } else {echo "<li><a href=\"".DIR."$row->pageSlug\"$cu>$row->pageTitle</a>"; 	
	} 
	}
	}
	if($row->pageID == $parent){ echo $sub; } else { echo "</li>\n"; } 

$i++;}
?>
</ul>
</div>
<!-- END NAV -->
<?php
/*-------------------------------------------------------+
| The One Point - Content Management System
| http://www.theonepoint.co.uk/
+--------------------------------------------------------+
| Author: David Carr  Email: d.carr@theonepoint.co.uk
+--------------------------------------------------------+*/

function get_file_extension($file_name)
{
	return substr(strrchr($file_name,'.'),1); 
}

//find all template files
$th = mysql_query("SELECT * FROM ".PREFIX."styles");
while($t = mysql_fetch_array($th))
{
	//make array of templates
	$the[] = $t['themeTitle'];
}


//if template is not in array add the template to the db
if ($handle = opendir('assets/templates')) {
    while (false !== ($file = readdir($handle))) {
        if (get_file_extension($file) == 'php' )
		{
			if(@!in_array($file,$the))
			{
				$sql = mysql_query("INSERT INTO ".PREFIX."styles (themeTitle)VALUES('$file')");  			
			}        
        }
    }
    closedir($handle);
}


global $navtitle;
define('PAGE', $_GET['ispage']);
$navTitle = $_GET['ispage'];

require ('init.php');

//checkpoint for hook
//global $header_css;
$above_doctype_title  = $SYS->execute_hooks('above_doctype_title');
$above_doctype      = $SYS->execute_hooks('above_doctype');
$header_css       	= $SYS->execute_hooks('header_css');
$header_js_script 	= $SYS->execute_hooks('header_js_script');
$header_js_jquery 	= $SYS->execute_hooks('header_js_jquery');
$header_slim_editor = $SYS->execute_hooks('header_slim_editor');
$js_inner_popup 	= $SYS->execute_hooks('js_inner_popup');

$SYS->execute_hooks('del_inner');

$jsi = "<script type=\"text/javascript\">$js_inner_popup</script>";

//check pages against requested page
$sql = mysql_query("SELECT * FROM ".PREFIX."pages WHERE pageSlug='".PAGE."' AND pageStandAlone='0' AND pageActive='1'");
$Rows1 = mysql_num_rows($sql);
while ($row = mysql_fetch_object($sql))
{
	define('TMP',$row->template);
	//send page content to plugins and request data from the hook cont
	$page = $SYS->execute_hooks('cont', $row->pageCont);
	$page = str_replace("../../","",$page);
	$curpage = true;
	$s = $row->sidebars;
	$s = str_replace("../../","",$s);
	$breadcrumb.= "<a href=\"".DIR."$row->pageSlug\">$row->pageName</a>";
}


//check pages against requested page where standalone
$sql = mysql_query("SELECT * FROM ".PREFIX."pages WHERE pageSlug='".PAGE."' AND pageStandAlone='1' AND pageActive='1'");
$Rows2 = mysql_num_rows($sql);
while ($row = mysql_fetch_object($sql))
{	
	define('TMP',$row->template);
	//send page content to plugins and request data from the hook cont
	$page = $SYS->execute_hooks('cont', $row->pageCont);
	$page = str_replace("../../","",$page);
	$curpage = true;
	$s = $row->sidebars;
	$s = str_replace("../../","",$s);
	$breadcrumb.= "<a href=\"".DIR."$row->pageSlug\">$row->pageName</a>";
}

$root = $_SERVER['DOCUMENT_ROOT'];
$file = $_SERVER['SCRIPT_FILENAME'];
$checker = str_replace($root,'',$file);
$checker = str_replace(LOC,'',$checker);

if($file != SETTINGSADDRESS){
//load data from the hook page requester from plugins
$SYS->execute_hooks('page_requester');
}

global $curpage,$page,$box1Text,$box2,$box3;

//load home page
if($curpage == false)
{
	if(PAGE == '')
	{
		$sql = mysql_query("SELECT * FROM ".PREFIX."pages WHERE pageID='1'");
		while ($row = mysql_fetch_object($sql))
		{
			define('TMP',$row->template);
			//send page content to plugins and request data from the hook cont
			$page = $SYS->execute_hooks('cont', $row->pageCont);
			$page = str_replace("../../","",$page);
			$s = $row->sidebars;
			$s = str_replace("../../","",$s);
		}
	}	
}



$themeSql = mysql_query("SELECT * FROM ".PREFIX."styles WHERE styleID='".TMP."'")or die(mysql_error());
	$tnum = mysql_num_rows($themeSql);
	$thRow = mysql_fetch_object($themeSql);
	if($tnum != 0){
	define('THEME',$thRow->themeTitle);
	define('THEMEPATH','assets/templates/');
	} else {
	$s.= '4';
	define('THEME','404.php');
	define('THEMEPATH','assets/templates/');
	}



global $s;
$s = "$s";
$insides = explode(",",$s);
//load sidebar panels
$result = mysql_query("SELECT * FROM ".PREFIX."sidebars WHERE sidebarPos='left' ORDER BY sidebarOrder")or die(mysql_error());
while ($r = mysql_fetch_object($result))
{
	if (in_array("$r->sidebarID", $insides)) {
	$sidebarsleft.="<div class=\"sidebox sidebox-left\">\n";
	$r->sidebarCont = str_replace("../../","",$r->sidebarCont);
	$r->sidebarCont = str_replace("../../../","",$r->sidebarCont);
	$sidebarsleft.=$SYS->execute_hooks('cont',$r->sidebarCont);	 
	$sidebarsleft.="</div>\n"; 
	} 	
}


$result = mysql_query("SELECT * FROM ".PREFIX."sidebars WHERE sidebarPos='right' ORDER BY sidebarOrder")or die(mysql_error());
while ($r = mysql_fetch_object($result))
{
	if (in_array("$r->sidebarID", $insides)) {
	$sidebarsright.="<div class=\"sidebox $r->class\">\n";
	$r->sidebarCont = str_replace("../../","",$r->sidebarCont);
	$r->sidebarCont = str_replace("../../../","",$r->sidebarCont);
	$sidebarsright.=$SYS->execute_hooks('cont',$r->sidebarCont);	 
	$sidebarsright.="</div>\n"; 
	} 	
}

if(ISPLUGPAGE != 'Yes'){
	$_SESSION['plugcont'] = '';
}

?>
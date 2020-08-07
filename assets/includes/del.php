<?php
/*-------------------------------------------------------+
| The One Point - Content Management System
| http://www.theonepoint.co.uk/
+--------------------------------------------------------+
| Author: David Carr  Email: d.carr@theonepoint.co.uk
+--------------------------------------------------------+*/

if (!defined('included')){
die('You cannot access this file directly!');
}

$delType = $_GET['delType'];
$delID   = $_GET['delID'];
$delID = mysql_real_escape_string($delID);


if($delType == 'delpage')
{
   $query = mysql_query("DELETE FROM ".PREFIX."pages WHERE pageID = '$delID'")or die('Error : ' . mysql_error());  
   $_SESSION['success'] = 'Page Deleted';
   url('manage-pages');
}

if($delType == 'delpanel')
{
   $query = mysql_query("DELETE FROM ".PREFIX."sidebars WHERE sidebarID = '$delID'")or die('Error : ' . mysql_error());  
   $_SESSION['success'] = 'Panel Deleted';
   url('manage-sidebar-panels');
}

if($delType == 'deltheme')
{
   $query = mysql_query("DELETE FROM ".PREFIX."styles WHERE styleID = '$delID'")or die('Error : ' . mysql_error());  
   $_SESSION['success'] = 'Template Deleted';
   url('themes');
}


if($delType == 'deluser')
{
   $query = mysql_query("DELETE FROM ".PREFIX."members WHERE memberID = '$delID'")or die('Error : ' . mysql_error());  
   $_SESSION['success'] = 'User Deleted';
   url('manage-users');
}

//checkpoint for hook
$SYS->execute_hooks('del');
?>
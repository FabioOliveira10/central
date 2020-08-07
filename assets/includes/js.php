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
?>
<script language="JavaScript" type="text/javascript">
function delpage(pageID, pageName)
{
   if (confirm("Are you sure you want to delete '" + pageName + "'"))
   {
      window.location.href = '<?php echo DIR;?>admin.php?delpage=' + pageID;
   }
}
function deluser(memberID, username)
{
   if (confirm("Are you sure you want to delete '" + username + "'"))
   {
      window.location.href = '<?php echo DIR;?>admin.php?deluser=' + memberID;
   }
}
function delpanel(panelID, panelTitle)
{
   if (confirm("Are you sure you want to delete '" + panelTitle + "'"))
   {
      window.location.href = '<?php echo DIR;?>admin.php?delpanel=' + panelID;
   }
}

function deltheme(themeID, themeTitle)
{
   if (confirm("Are you sure you want to delete '" + themeTitle + "'"))
   {
      window.location.href = '<?php echo DIR;?>admin.php?deltheme=' + themeID;
   }
}
<?php $SYS->execute_hooks('js_popup');?>
</script>
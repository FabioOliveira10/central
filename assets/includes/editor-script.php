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
<script language="javascript" type="text/javascript" src="<?php echo DIR;?>assets/editor/jscripts/tiny_mce/tiny_mce.js"></script>	
	<script language="javascript" type="text/javascript">
	tinyMCE.init({
	mode : "exact",
	elements : "pageCont,sidebarCont,newsDesc,newsCont,newsImg,postDesc,postCont,postImg,imageFull,imageDetails,tab1,tab2,tab3,tab4,edit1,edit2,edit3,edit4,edit5,edit6,edit7,edit8,box1,box2,box3,box4,box5,box6",
	theme : "advanced",			
	file_browser_callback : "ajaxfilemanager",
	relative_urls : true,
	apply_source_formatting : true,
	plugins : "style,table,advhr,advimage,advlink,preview,media,paste,xhtmlxtras",
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,hr,removeformat,link,unlink,anchor,cleanup,code",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,image,media,preview",
	theme_advanced_buttons3 : "tablecontrols",
	theme_advanced_buttons4 : "formatselect,forecolor,backcolor,styleselect",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	content_css : "<?php echo DIR;?>assets/templates/css/tiny.css",
	extended_valid_elements : "a[name|href|target|title|onclick|class|rel|style],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|area|usemap|style],hr[class|width|size|noshade|style],font[face|size|color|style],span[class|align|style]"
});
		function ajaxfilemanager(field_name, url, type, win) {
			var ajaxfilemanagerurl = "<?php echo DIR;?>assets/editor/jscripts/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php";
			switch (type) {
				case "image":
					break;
				case "media":
					break;
				case "flash": 
					break;
				case "file":
					break;
				default:
					return false;
			}
            tinyMCE.activeEditor.windowManager.open({
                url: "<?php  echo DIR;?>assets/editor/jscripts/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php",
                width: 782,
                height: 440,
                inline : "yes",
                close_previous : "no"
            },{
                window : win,
                input : field_name
            });           

		}
	</script>

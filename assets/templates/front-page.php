<?php include(get_theme_path().'includes/header.php'); ?>	     
 
 	<?php get_content();?>
	
	<?php
		global $SYS;
		$sql = mysql_query("SELECT * FROM ".PREFIX."pages WHERE pageID='1'");
		while ($row = mysql_fetch_object($sql))
		{
			define('TMP',$row->template);
			//send page content to plugins and request data from the hook cont
			$box1 = $SYS->execute_hooks('cont', $row->box1Text);
			$box2 = $SYS->execute_hooks('cont', $row->box2Text);
			$box3 = $SYS->execute_hooks('cont', $row->box3Text);
			$box1 = str_replace("../../","",$box1);
			$box2 = str_replace("../../","",$box2);
			$box3 = str_replace("../../","",$box3);	
			$box3 = mailinglist($box3);		
		}
	?>
	
	<div id="boxContainer">
		<div id="box1" class="column"><?php echo $box1;?></div> 
		<div id="box2" class="column"><?php echo $box2;?></div> 
		<div id="box3" class="column"><?php echo $box3;?></div>        
	</div>	
	
<?php include(get_theme_path().'includes/footer.php'); ?>		
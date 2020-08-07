<?php include(get_theme_path().'includes/header.php'); ?>	

<div id="contbd">
	<div id="contwrapper">
		<div id="breadcrumb"><?php get_breadcrumb();?></div>
		<div id="content">			
			<div id="content-left"><?php get_content();?></div>		
			<div id="content-right"><?php get_sidebars_right();?></div>
		</div>	
	</div>
</div>
<?php include(get_theme_path().'includes/footer.php'); ?>		
<?php include(get_theme_path().'includes/header.php'); ?>
	
	<div id="content">	
		<div id="content-left">
        
        <h1>OOOPS...</h1>
        <h3>The page you were looking for could not be found</h3>
        <p>This could be the result of the page being removed, the name being changed or the page being temporarily unavailable</p>
        <h3>Troubleshooting</h3>
        <ul>
          <li>If you spelled the URL manually, double check the spelling</li>
          <li>Go to our website's home page, and navigate to the content in question</li>
          <li>Alternatively, you can search our website below</li>
        </ul>
            
        <form action="<?php echo DIR;?>search" method="get" id="form-search">
        <p><input name="search" type="text" id="input-keywords" value="" size="40">
        <input type="submit" value="submit" title="Search" alt="Search" name="submit" class="seabtn"></p>
        </form>        
        
        </div>		
		<div id="content-right"><?php get_sidebars_right();?></div>
	</div>
		
<?php include(get_theme_path().'includes/footer.php'); ?>	
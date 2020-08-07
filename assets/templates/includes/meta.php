<?php get_meta(); //load meta tags?>
<meta name="generator" content="CMS built by David Carr" />
<link href="<?php echo DIR.get_theme_path();?>css/reset.css" rel="stylesheet" type="text/css" />
<?php 
if($_SESSION['printset'] == true){
	echo "<link href=\"".DIR.get_theme_path()."css/print.css\" rel=\"stylesheet\" type=\"text/css\" />";
} else {
	echo "<link href=\"".DIR.get_theme_path()."css/style.css\" rel=\"stylesheet\" type=\"text/css\" />";
} ?>
<link href="<?php echo DIR.get_theme_path();?>css/superfish.css" rel="stylesheet" type="text/css" />
<link href="<?php echo DIR.get_theme_path();?>css/addons.css" rel="stylesheet" type="text/css" />
<link href="<?php echo DIR.get_theme_path();?>css/prettyPhoto.css" rel="stylesheet" type="text/css" />
<?php global $header_css; 
echo $header_css; //load any css files from plugins ?>
<!--[if lte IE 6]>
    <style type=\"text/css\">
    img, div, h1 { behavior: url(<?php echo DIR.get_theme_path();?>iepngfix.htc) }
    </style>
<![endif]-->
<?php global $header_js_script; echo $header_js_script; //load any javascript files from plugins ?>
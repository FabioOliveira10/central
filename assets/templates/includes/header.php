<?php global $above_doctype; echo $above_doctype; ?>
<!DOCTYPE HTML>
<!--
	+********************************************************+
	| Thanks for your interest in the source code!           |
	| David Carr's - Content Management System              |
	| http://www.daveismyname.co.uk/                          |
	+********************************************************+
	| Author: David Carr  Email: dave@daveismyname.co.uk    |
	+********************************************************+
-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php get_title();?></title>
<?php include(get_theme_path().'includes/meta.php');?>
</head>
<body>

<div id="wrapper">
	<div id="header">	
		<div id="logo">
			<a href="<?php echo DIR;?>"><img src="<?php echo DIR.get_theme_path();?>images/logo.png" alt="<?php echo SITETITLE;?>" title="<?php echo SITETITLE;?>" border="0" /></a>
		</div>
		
		<div id="search">
			<form action="<?php echo DIR;?>search" method="get" id="form-search">
			<p><input name="search" onBlur="if(this.value=='')this.value='Site Search...';" onFocus="if(this.value=='Site Search...')this.value='';" type="text" id="input-keywords" value="Site Search..." size="20">		
			<input id="submit-search" type="image" src="<?php echo DIR.get_theme_path();?>images/btn_go.png" alt="&nbsp;" title="Search" value="Search" name="submit" /></p>
			</form>
		</div>	
		<?php include(get_theme_path().'includes/nav.php');?>       
    </div>	
</div>

<?php 
$fq = mysql_query("SELECT * FROM ".PREFIX."footers WHERE id='1'")or die(mysql_error());
$fr = mysql_fetch_object($fq);
$fr->box1 = str_replace("../","",$fr->box1);
$fr->box2 = str_replace("../","",$fr->box2);
?>

<div id="footerbar"></div>
<div id="footer-wrapper">
	<div id="footer">
		<div id="copyl"><?php echo stripslashes($fr->box1);?></div>	
		<div id="copyr"><?php echo stripslashes($fr->box2);?></div>
	</div>
</div>


<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.3/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo DIR.get_theme_path();?>js/superfish/hoverIntent.js"></script>
<script type="text/javascript" src="<?php echo DIR.get_theme_path();?>js/superfish/superfish.js"></script>
<script type="text/javascript" src="<?php echo DIR.get_theme_path();?>js/superfish/supersubs.js"></script>
<script type="text/javascript" src="<?php echo DIR.get_theme_path();?>js/jquery.cycle.js"></script>
<script type="text/javascript" src="<?php echo DIR.get_theme_path();?>js/prettyPhoto.js"></script>
<script type="text/javascript" src="<?php echo DIR.get_theme_path();?>js/slider.js"></script>
<script type="text/javascript" charset="utf-8">
function equalHeight(group) {
	tallest = 0;
	group.each(function() {
		thisHeight = $(this).height();
		if(thisHeight > tallest) {
			tallest = thisHeight;
		}
	});
	group.height(tallest);
}
$(document).ready(function(){
	$("div.hidethis").click(function(){$(this).slideUp("fast");});
	$("a[rel^='prettyPhoto']").prettyPhoto({animationSpeed:'fast',slideshow:10000});
	equalHeight($(".column"));
	
	$("ul.sf-menu").supersubs({ 
    minWidth:    12,   
    maxWidth:    27,  
    extraWidth:  1     
    }).superfish(); 
			
	 $('.slideshow').cycle({
		fx: 'fade' // choose your transition type, ex: fade, scrollUp, shuffle, etc...
	}); 
	
	$('div.slider').sliders({	
			cycle : true, // true or false
			slideWidth : 930, // width per slide
			cycleInterval : 8000 // pause in milleseconds between animation		
	});

		
	<?php
	 global $header_js_jquery,$header_js_script;
	 echo $header_js_jquery; //load any jquery from plugins 
	 ?>
	
});	

function popUp(URL) {
day = new Date();
id = day.getTime();
eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=584,height=746,left = 150,top = 150');");
}
</script>
</body>
</html>
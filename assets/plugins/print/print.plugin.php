<?php

function printer($string) {

	if(isset($_GET['print'])){
		$_SESSION['printset'] = true;
	}
	
	if(isset($_GET['web'])){
		$_SESSION['printset'] = false;
	} 		

	if($_SESSION['printset'] == true){
		$print = "<a href=\"?web\"><img src=\"".DIR."images/web.png\" border=\"0\" /></a>";
	} else {
		$print = "<a href=\"?print\"><img src=\"".DIR."images/printer.png\" border=\"0\" /></a>";
	} 
	
	$string = str_replace("[print]", $print, $string);
	return $string;
}

add_hook('cont','printer')
?>
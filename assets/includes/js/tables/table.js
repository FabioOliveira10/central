// JavaScript Document

// start table js
$(document).ready(function(){
//JQuery ready is quicker than onload
$(".stripeMe tr").mouseover(function() {$(this).addClass("over");}).mouseout(function() {$(this).removeClass("over");});
$(".stripeMe tr:even").addClass("alt");
});
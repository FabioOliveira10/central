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

//include Simple Hooks Plugin Class
require "SYS.class.php";

//create instance of class
$SYS = new SYS();

//set hook to which plugin developers can assign functions
$SYS->developer_set_hook('main');

//set multiple hooks to which plugin developers can assign functions
$SYS->developer_set_hooks(array('above_doctype', 'above_doctype_title', 'header_css', 'header_js_script', 'header_js_jquery', 'header_slim_editor', 'main', 'cont', 'sidebar', 'page_requester', 'js_popup', 'js_inner_popup', 'del_inner', 'del', 'admin_modules', 'admin_links', 'editorElements'));

//load plugins from folder, if no argument is supplied, a './plugins/' constant will be used
//trailing slash at the end is REQUIRED!
//this method will load all *.plugin.php files from given directory, INCLUDING subdirectories
$SYS->load_plugins();

//now, this is a workaround because plugins, when included, can't access $SYS variable, so we
//as developers have to basically redefine functions which can be called from plugin files
function add_hook($where, $function) {
	global $SYS;
	$SYS->add_hook($where, $function);
}

//same as above
function register_plugin($plugin_id, $data) {
	global $SYS;
	$SYS->register_plugin($plugin_id, $data);
}

?>
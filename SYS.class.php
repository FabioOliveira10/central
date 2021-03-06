<?php
/**
 * @author Nemanja Avramovic
 * @version 0.1
 * @category Plugins
 * 
 * LGPL license
 */

class SYS {

	var $plugins = array();
	var $hooks = array();

	//adds hook to hook list, so plugin developers can attach functions to hooks
	function developer_set_hook($where) {
		$this->hooks[$where] = '';
	}
	
	//add multiple hooks	
	function developer_set_hooks($wheres) {
		foreach ($wheres as $where) {
			$this->developer_set_hook($where);
		}
	}
	
	//unset hook
	function developer_unset_hook($where) {
		unset($this->hooks[$where]);
	}
	
	//unset multiple hooks
	function developer_unset_hooks($wheres) {
		foreach ($wheres as $where) {
			$this->developer_unset_hook($where);
		}
	}

	//load plugins from specific folder, includes all *.plugin.php files
	function load_plugins($from_folder = './assets/plugins/') {
	
		if ($handle = @opendir($from_folder)) {
	
			while (false !== ($file = readdir($handle))) {
				if (is_file($from_folder . $file)) {
					if (strpos($from_folder . $file,'.plugin.php') != false) {
						require_once $from_folder . $file;
						$this->plugins[$file]['file'] = $file;
					}
				}
				else if ((is_dir($from_folder . $file)) && ($file != '.') && ($file != '..')) {
					$this->load_plugins($from_folder . $file . '/');
				}
			}
	
			closedir($handle);
		}
		
	}
	
	//attach custom function to hook
	function add_hook($where, $function) {
		if (!isset($this->hooks[$where])) { 
			die("There is no such place ($where) for hooks."); 
		}
		else {
			$these_hooks = explode('|', $this->hooks[$where]);
			$these_hooks[] = $function;
			$this->hooks[$where] = implode('|', $these_hooks);
		}
	}
	
	//alias for add_hook
	function attach_hook($where, $function) {
		$this->add_hook($where, $function);
	}
	
	//print out plugin data
	function display_plugin_data($plugin_id) {
		print_r($this->plugins[$plugin_id]);
	}
	
	//check whether any function is attached to hook
	function hooks_exist($where) {
		return (trim($this->hooks[$where]) == "") ? false : true ;
	}
	
	//execute all functions which are attached to hook, you can provide argument (or arguments via array) as second parameter
	function execute_hooks($where, $args = '') {
		if (isset($this->hooks[$where])) {
			$these_hooks = explode('|', $this->hooks[$where]);
			$result = $args;
			foreach ($these_hooks as $hook) {
				if (function_exists($hook)) { $result = call_user_func($hook, $result); }
			}
			
			return $result;
		}
		else {
			die("There is no such place ($where) for hooks."); 
		}
	}
	
	//get all functions attached to hook as array (so you can foreach them)
	function get_hooks_array($where) {
		if (isset($this->hooks[$where])) {
			return explode('|', $this->hooks[$where]);
		}
		else {
			return false;
		}
	}
	
	//register plugin data in $this->plugin
	function register_plugin($plugin_id, $data) {
		foreach ($data as $key=>$value) {
			$this->plugins[$plugin_id][$key] = $value;
		}
	}
	
	//get data of plugin as array
	function get_plugin_data($plugin_id) {
		return $this->plugins[$plugin_id];
	}

}

?>
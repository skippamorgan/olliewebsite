<?php
/**
 * @package SjCore
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2009-2012 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */

defined('_JEXEC') or die;

define('_CORE', dirname(__FILE__));

function _core_autoload($classname){
	// stop if class exist
	if (class_exists($classname)){
		return;
	}
	// because file name is lower case
	$classname_lower = strtolower($classname);
	if (substr($classname_lower, 0, 5) === '_core'){
		// load in 'class' folder
		$classname_lower = '_class'.substr($classname_lower, 5).'.php';
	} else {
		// is not _core classes
		return;
	}
	
	// source file path
	$pieces = explode('_', $classname_lower);
	$class_path  = implode(DS, $pieces);
	
	// loading
	if ( file_exists(_CORE.$class_path) ){
		ob_start();
		include_once _CORE.$class_path;
		$errs = ob_get_contents();
		ob_end_clean();
		if ($errs){
			var_dump($class_path);
		}
	}
}

// register internal autoload
spl_autoload_register('_core_autoload');
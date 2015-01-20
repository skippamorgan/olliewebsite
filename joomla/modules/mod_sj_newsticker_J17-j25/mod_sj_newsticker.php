<?php
/**
 * @package Sj News Ticker
 * @version 2.5
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2012 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 * 
 */
defined('_JEXEC') or die;
defined('_YTOOLS') or include_once 'core' . DS . 'sjimport.php';

// set current module for working
YTools::setModule($module);
// import jQuery
if (!defined('SMART_JQUERY') && (int)$params->get('include_jquery', '1')){
	YTools::script('jquery-1.5.min.js');
	define('SMART_JQUERY', 1);
}

if (!defined('SMART_NOCONFLICT')){
	YTools::script('jsmart.noconflict.js');
	define('SMART_NOCONFLICT', 1);
}
YTools::script('newsticker.js');
YTools::stylesheet('style.css');

include_once   'core' . DS . 'newsticker.php';

$params->def('reader', 'Reader');
$layout_name = 'default';
$cacheid = md5(serialize(array ($layout_name, $module->module)));
$cacheparams = new stdClass;
$cacheparams->cachemode = 'id';
$cacheparams->class = 'NewsTicker';
$cacheparams->method = 'getList';
$cacheparams->methodparams = $params;
$cacheparams->modeparams = $cacheid;
$items = JModuleHelper::moduleCache ($module, $params, $cacheparams);
include JModuleHelper::getLayoutPath($module->module);

?>
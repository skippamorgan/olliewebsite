<?php
/**
 * Horizontal scrolling slideshow
 *
 * @package 	Horizontal scrolling slideshow
 * @subpackage 	Horizontal scrolling slideshow
 * @version   	3.3
 * @author    	Gopi Ramasamy
 * @copyright 	Copyright (C) 2010 - 2014 www.gopiplus.com, LLC
 * @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * http://www.gopiplus.com/extensions/2011/07/horizontal-scrolling-slideshow-joomla-module/
 */

// no direct access
defined('_JEXEC') or die;

// Include the helper functions only once
require_once __DIR__.'/helper.php';

$folder	= modHorizontalScrollingSlideshowHelper::getFolder($params);
$images	= modHorizontalScrollingSlideshowHelper::getImages($params, $folder);

if (!count($images)) 
{
	echo JText::_('NO IMAGES ' . $folder . '<br><br>');
	return;
}

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
require JModuleHelper::getLayoutPath('mod_horizontal_scrolling_slideshow', $params->get('layout', 'default'));

?>
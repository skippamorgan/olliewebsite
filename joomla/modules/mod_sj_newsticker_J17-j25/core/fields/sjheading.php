<?php
/**
 * @package SjCore
 * @subpackage Fields
 * @version 1.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2012 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */
defined('_JEXEC') or die;
defined('_CORE') or include_once dirname(dirname(__FILE__)).DS.'core.php';
if (!class_exists('JFormFieldSjHeading')){
	
	class JFormFieldSjHeading extends _Core_Field_Heading{};
	
}
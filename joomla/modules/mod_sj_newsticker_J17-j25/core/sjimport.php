<?php
/**
 * @package SjCore
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2009-2012 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */

defined('_JEXEC') or die;

define('_YTOOLS', 1);
define('_YTOOLS_VERSION', '1.0.0.20111207');
define('_YTOOLS_BASE', dirname(__FILE__) . DS . 'ytools');
define('_SJ_CLASSES', dirname(__FILE__) . DS . 'sjclass');
define('_SJ_FIELDS', dirname(__FILE__) . DS . 'fields');

include_once _YTOOLS_BASE .DS . 'ytools.php';
include_once _SJ_CLASSES . DS . 'sjmodule.php';
include_once _SJ_CLASSES . DS . 'sjreader.php';
include_once dirname(__FILE__).'/core.php';
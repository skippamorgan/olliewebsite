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

class modHorizontalScrollingSlideshowHelper
{
	public static function loadScripts(&$params)
	{
		$doc = JFactory::getDocument();
		$doc->addScript(JURI::Root(true).'/modules/mod_horizontal_scrolling_slideshow/tmpl/mod_horizontal_scrolling_slideshow.js');
	}
	
	public static function getImages(&$params, $folder)
	{
		$type = $params->get('slideshow_type', 'jpg');
		$imagetype	= array();
		$imagetype = explode(",", $type);
					
		$files	= array();
		$images	= array();

		if (substr(JPATH_BASE, -1) == '\\')
		{
			$japth = JPATH_BASE;
		}
		else
		{
			$japth = JPATH_BASE . "\\";
		}

		$dir = $japth . $folder;
		$dir = str_replace('/', '\\', $dir); // Line 45
		//if your getting no directory error message. Please comment line 45 and uncomment line 47.
		//$dir = str_replace('\\', '/', $dir);  // Line 47

		// check if directory exists
		if (is_dir($dir))
		{
			if ($handle = opendir($dir)) {
				while (false !== ($file = readdir($handle))) {
					if ($file != '.' && $file != '..' && $file != 'CVS' && $file != 'index.html') {
						$files[] = $file;
					}
				}
			}
			closedir($handle);

			$i = 0;
			$loadthis = false;
			foreach ($files as $img)
			{			
				if (!is_dir($dir . "\\". $img))
				{
					$exts = modHorizontalScrollingSlideshowHelper::findexts($img);
					
					foreach($imagetype as $imgtype) 
					{
						if (trim($exts) == trim($imgtype))
						{
							$loadthis = true;
						}
					}
					
					if($loadthis)
					{
						$images[$i] = new stdClass;
						$images[$i]->name	= $img;
						$images[$i]->folder	= $folder;
						$i++;
					}
					$loadthis = false;
				}
			}
		}
		else
		{
			echo JText::_('No directory ' . $dir . '<br>');
		}
		return $images;
	}

	public static function getFolder(&$params)
	{
		$folder	= $params->get('slideshow_folder');
		$LiveSite = JURI::base();

		// if folder includes livesite info, remove
		if (JString::strpos($folder, $LiveSite) === 0) {
			$folder = str_replace($LiveSite, '', $folder);
		}
		// if folder includes absolute path, remove
		if (JString::strpos($folder, JPATH_SITE) === 0) {
			$folder= str_replace(JPATH_BASE, '', $folder);
		}

		return $folder;
	}
	
	public static function findexts($filename) 
	{ 
		$filename = strtolower($filename) ; 
		$exts = explode(".", $filename) ; 
		$n = count($exts)-1; 
		$exts = $exts[$n]; 
		return $exts; 
	}
}
?>
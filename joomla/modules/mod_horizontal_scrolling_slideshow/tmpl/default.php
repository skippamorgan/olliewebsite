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

if ( ! empty($images) ) 
{
	$slideshow_link	= $params->get('slideshow_link');
	
	$slideshow_width = $params->get('slideshow_width');
	$slideshow_height = $params->get('slideshow_height');
	$slideshow_speed = $params->get('slideshow_speed');
	
	$slideshow_bgcolor = $params->get('slideshow_bgcolor');
	$slideshow_gap = $params->get('slideshow_gap');
	$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
	
	if(!is_numeric($slideshow_width)) { $slideshow_width = 200; }
	if(!is_numeric($slideshow_height)) { $slideshow_height = 150; }
	if(!is_numeric($slideshow_speed)) { $slideshow_speed = 1; }
	
	$Ihrss_count = 0;
	$Ihrss_package = "";
	$Ihrss_path = "";
	$Ihrss_link = "";
	foreach ( $images as $images ) 
	{	
		$Ihrss_path = JURI::base().$folder ."/". $images->name;
		$Ihrss_path = str_replace('\\', '/', $Ihrss_path);
		
		if($Ihrss_link == "" )
		{
			$Ihrss_link =  '#';
		}
		$Ihrss_package = $Ihrss_package ."IHRSS_SLIDESRARRAY[$Ihrss_count]='<a href=\"$Ihrss_link\"><img src=\"$Ihrss_path\" /></a>';	";
		$Ihrss_count++;
	}
}
?>
<script language="JavaScript1.2">
var IHRSS_WIDTH = "<?php echo $slideshow_width."px"; ?>";
var IHRSS_HEIGHT = "<?php echo $slideshow_height."px"; ?>";
var IHRSS_SPEED = <?php echo $slideshow_speed; ?>;
var IHRSS_BGCOLOR = "<?php echo $slideshow_bgcolor; ?>";
var IHRSS_SLIDESRARRAY=new Array();
var IHRSS_FINALSLIDE ='';
<?php echo $Ihrss_package; ?>
var IHRSS_IMGGAP = " ";
var IHRSS_PIXELGAP = <?php echo $slideshow_gap; ?>;
</script>
<script src="<?php echo JURI::Root(true); ?>/modules/mod_horizontal_scrolling_slideshow/tmpl/mod_horizontal_scrolling_slideshow.js" type="text/javascript"></script>
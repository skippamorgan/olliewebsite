<?php
/**
 * @package VerticalScroll JT1 Module for Joomla! 2.5
 * @version $Id: 1.0 
 * @author muratyil
 * @Copyright (C) 2012- muratyil
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined( '_JEXEC' ) or die( 'Restricted access' );	
$target = $params->get( 'target', "" );
$loadjquery=$params->get( 'loadjquery', "1" );
$speed = $params->get( 'speed', "500" );
$pause= $params->get( 'pause', "500" );
$mousePause= $params->get( 'mousePause', "false" );
$height= $params->get( 'height', "150" );
$background=$params->get( 'background', "#f1f1f1" );
$padding=$params->get( 'padding', "10" );
//
$url[]	= "!";
$urltext[]= $params->get( 'urltext0', "" );
for ($jtn=1; $jtn<=40; $jtn++)
	{
	$urltext[]= $params->get( 'urltext'.$jtn , "" );
	$url[]	= $params->get( 'url'.$jtn , "" );
	}
	//
if ( $loadjquery==1) {
$document = JFactory::getDocument();
$document->addScript('modules/mod_verticalscroll_jt1/js/jquery-1.7.1.min.js');
$document->addScript('modules/mod_verticalscroll_jt1/js/jquery.vertical.js');
}
elseif ( $loadjquery==0) {
$document = JFactory::getDocument();
$document->addScript('modules/mod_verticalscroll_jt1/js/jquery.vertical.js');
}
//
//
JHTML::_('stylesheet','style.css','modules/mod_verticalscroll_jt1/css/');

?>
<script>
jQuery.noConflict();
		jQuery(function() {
			jQuery('#scroller').vTicker({ speed: <?php echo $speed; ?>, pause: <?php echo $pause; ?>, height: <?php echo $height; ?>, animation: 'fade', mousePause:  <?php echo $mousePause; ?> 
			});		
		});
	</script>
<div style="width:auto; background:<?php echo $background; ?>; padding:<?php echo $padding; ?>px">
			<div id="scroller">
					<ul><?php 
	for ($i=0; $i<=40; $i++)
	{if ($urltext[$i] != null) { echo "<li class='selectItem'><a class='selectLink' href='$url[$i]' target='$target'><span class='selectName'>$urltext[$i]</span></a></li>"; }}
	?>
</ul>
</div></div>
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
$options=$params->toObject();
$image_item_config=array(
  'output_width'  => $params->get('item_image_width'),
  'output_height' => $params->get('item_image_height'),
  'function'		=> $params->get('item_image_function'),
  'background'	=> $params->get('item_image_background')
);
?>
<script language="javascript">	
	$jsmart(document).ready(function() {			
		newsTicker("ticker");		
	});
</script>
<?php 
    if(!empty($items)){
?>
<div id="tickerContainer">
  <dl id="ticker" style="width:<?php echo $options->module_width == 0 ? "100%" : $options->module_width.'px'?>;height:<?php echo $options->module_height;?>px" class="ticker">
    <?php
			foreach($items as $item) {
		?>
    <dt class="heading">
      <a href="<?php echo $item['link']?>" <?php echo YTools::parseTarget($options->item_link_target);?>>
          <?php echo Ytools::truncate($item['title'],$options->item_title_max_characs);?>
      </a>
    </dt>
    <dd class="text">	  
	  <?php if($options->item_image_display == 1 && !empty($item['image'])) :?>
	   <img src="<?php echo YTools::resize($item['image'],$image_item_config); ?>" <?php echo $options->item_desc_display == 1 ? "align='left' style='padding-right: 5px;'" : ''?> />
	   <?php endif;?>
	   <?php if($options->item_desc_display == 1) :?>
	  <p><?php echo Ytools::truncate($item['desc'],$options->item_desc_max_characs);?></p>
	  <?php endif;?>
	  <?php if($options->item_readmore_display == 1) :?>
	  <p class="read-more">
    	  <a href="<?php echo $item['link']?>" <?php echo YTools::parseTarget($options->item_link_target);?> class="more">
    	       <?php echo $options->item_readmore_text;?>
    	  </a> 
	  </p>
	  <?php endif;?>	 
    </dd>
    <!-- /detail -->
    <?php
		}
		?>
  </dl>
</div>
<?php }else { echo JText::_('Has no content to show!');}?>

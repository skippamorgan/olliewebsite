<?php
/**
 * @package SjCore
 * @subpackage Elements
 * @version 1.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2012 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */
defined('_JEXEC') or die;

class _Core_Field_Custom extends JFormField{
	
	public function  getInput(){
		$this->default = $this->parseAttributes();
		$keyfield = null;
		foreach($this->default as $f0 => $val){
			$keyfield = $f0;
			break;
		}
		$hasData = is_array($this->value) && count($this->value);
		$html = array();
		$html[] = '<div class="sj-custom" style="border:1px solid #c0c0c0; clear: both;padding: 5px 0;">';
		if ($hasData){
			$this->_i = 0;
			foreach ($this->value as $item) {
				if (is_array($item)){
					$item = JArrayHelper::toObject($item);
				}
				if (!$this->isValidate($item)){
					continue;
				}
					
				$item_css = '';
				if (isset($item->$keyfield)){
					$item_css .= 'item-' . $item->$keyfield;
				}
				if ($this->_i % 2 == 0){
					$item_css .= ' even';
				} else {
					$item_css .= ' odd';
				}
				
				$html[] = $this->getItemBlock($item, $item_css);
				$this->_i++;
			}
		} else {
			$this->_i = 0;
			$html[] = $this->getItemBlock($this->default);
			$this->_i++;
		}
			$html[] = '<div class="item last">';
				$html[] = '<div class="item-field">';
					$html[] = '<div class="item-field-label">&nbsp;</div>';
					$html[] = '<div class="item-field-input">';
						$html[] = '<div class="item-add-button">';
							$html[] = '<a href="#" class="next-' . $this->_i . '"><span>ADD ITEM</span></a>';
						$html[] = '</div>';
					$html[] = '</div>';
				$html[] = '</div>';
				$html[] = '<div class="item-separator"></div>';
			$html[] = '</div>';
			
			$this->_i = 'NEXTINDEX';
			$html[] = $this->getItemBlock($this->default, 'default');
			
		$html[] = '</div>';
		$this->addStylesheet();
		$this->addJavascript();
		return implode("\n", $html);
	}
	
	protected function parseAttributes(){
		$sampleObj = new stdClass();
		if (isset($this->element['fields']) && !empty($this->element['fields'])){
			$fields = explode(',', $this->element['fields']);
			
			if (count($fields)){
				foreach ($fields as $f){
					$f = trim($f);
					$f = strtolower($f);
					if (!isset($sampleObj->$f)){
						$sampleObj->$f = null;
					}
				}
			} else {
				$sampleObj->id			= null;
				$sampleObj->title		= null;
				$sampleObj->image		= null;
				$sampleObj->url			= null;
				$sampleObj->description	= null;
			}
		} else {
			$sampleObj->id			= null;
			$sampleObj->title		= null;
			$sampleObj->image		= null;
			$sampleObj->url			= null;
			$sampleObj->description	= null;
		}
		return $sampleObj;
	}
	
	protected function isValidate($item){
		$keyfield = false;
		$i = 0;
		foreach ($this->default as $f => $null){
			++$i;
			if ($i==1){
				if (!property_exists($item, $f) || empty($item->$f)){
					return false;
				}
			}
			if (!property_exists($item, $f)){
				$item->$f = '';
			}
		}
		return true;
	}
	
	protected function getItemBlock($item, $class_suffix=''){
		$html = array();
		$html[] = '<div class="item ' . $class_suffix . '">';
			foreach ($item as $f => $val){
				$f = strtolower($f);
				if (!property_exists($this->default, $f)){
					continue;
				}
				$html[] = '<div class="item-field">';
					$html[] = '<div class="item-field-label">' . JText::_( ucfirst($f) ) . '</div>';
					$html[] = '<div class="item-field-input item-field-' . $f . '">';
					if ($f=='description'){
						//$html[] = '<input value="' . $value . '" name="' . $this->name . "[{$this->_index}][$field]" . '" />';
						$html[] = '<textarea rows="3" name="' . $this->name . "[{$this->_i}][$f]" . '">' . $val . '</textarea>';
					} else {
						$html[] = '<input value="' . $val . '" name="' . $this->name . "[{$this->_i}][$f]" . '" />';
					}
					$html[] = '</div>';
				$html[] = '</div>';
			}
			$html[] = '<div class="item-separator"></div>';
		$html[] = '</div>';
		return implode("\n", $html);
	}

	protected function addStylesheet(){
		$document = &JFactory::getDocument();
		$document->addStyleDeclaration("
				.sj-custom .item{
					padding:0 5px;
				}
				.sj-custom .item.default{
					display: none;
				}
				.sj-custom .item-field{
					clear:both;
					padding: 0 0 5px 0;
				}
				.sj-custom .item-field-label{
					width: 25%;
					float: left;
				}
				.sj-custom .item-field-input{
					width: 74%;
					float: left;
				}
				.sj-custom .item-field-input input,
				.sj-custom .item-field-input textarea{
					margin: 0;
					width: 90%;
				}
				.sj-custom .item.recently .item-field-input input,
				.sj-custom .item.recently .item-field-input textarea{
					background: #FFEEDD;
				}
				.sj-custom .item-separator{
					clear:both;
					height: 5px;
					border-top: dashed 1px #ddd;
				}
				.sj-custom .item-add-button{
					text-align: right;
					width: 90%;
				}
				.sj-custom .item-add-button a{
					font-weight: 700;
					
				}
				.sj-custom .item-field:after {
					content: '.';
					display: block;
					height: 0;
					clear: both;
					visibility: hidden;
					overflow: hidden;
				}
				.sj-custom .item-field {
					display: block;
				}
		");
		return true;
	}
	
	protected function addJavascript(){
		$document = &JFactory::getDocument();
		$document->addScriptDeclaration("
				window.addEvent('domready', function(){
					try{
						var customDiv = $(document.body).getElement('.sj-custom');
						customDiv.getElement('.item-add-button a').addEvent('click', function(){
							var nextid		= $(this).get('class').replace('next-', '');
							var template	= customDiv.getElement('.default');
							var newTemplate	= template.clone(true, true);
							var newHtml		= $(newTemplate).get('html').replace(/NEXTINDEX/g, nextid);
		
							$(this).set('class', 'next-' + (nextid-(-1)));
							$(newTemplate).removeClass('default').addClass('recently');
							$(newTemplate).set('html', newHtml);
							$(newTemplate).inject( customDiv.getElement('.last'), 'before');
							return false;
						});
					} catch(e){
						console.log(e);
					}
		
				});
		");
		return true;
	}
	
}

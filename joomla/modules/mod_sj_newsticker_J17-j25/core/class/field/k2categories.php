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
defined('_CORE') or die;

class _Core_Field_K2Categories extends JFormField{

	public function getInput(){
		$html = array();
		if ($this->com_k2_installed()){
			$html[] = $this->getInputHtml();
		} else {
			$db = &JFactory::getDbo();
			$html[] = "<div style='clear:both; padding: 0 0 0 2em;'>";
			$html[] = "Cannot find table {$db->getPrefix()}k2_categories.<br>";
			$html[] = "If you have K2 component installed.<br>";
			$html[] = "Please contact us on <a href=\"http://www.smartaddons.com\" target=\"_blank\">http://www.smartaddons.com</a><br>";
			$html[] = "Thank you";
			$html[] = "</div>";
		}
		return implode("\n", $html);
	}
	
	public function getInputHtml(){
		$html = array();
		$attr = $this->getFieldAttributes();

		// Get the field options.
		$db = JFactory::getDbo();
		$query="
			SELECT a.id, a.name AS title, a.parent as parent_id
			FROM #__k2_categories AS a
			WHERE a.trash = 0
				AND a.published IN (0,1)
			ORDER BY a.ordering
		";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if (count($rows)){
			
			$categories_tree = array();
			foreach ($rows as $cid => $category){
				$categories_tree[$category->id] = &$rows[$cid];
			}
			foreach ($categories_tree as $cid => $category) {
				if (isset($categories_tree[$category->parent_id])){
					if( !isset($categories_tree[$category->parent_id]->child) ){
						$categories_tree[$category->parent_id]->child = array();
					}
					$categories_tree[$category->parent_id]->child[$cid] = &$categories_tree[$cid];
				}
			}
			
			$categories_flat = array();
			foreach ($categories_tree as $cid => $category) {
				if (!isset($categories_tree[$category->parent_id])){
					$category->level=1;
					$stack = array($categories_tree[$cid]);
					while (count($stack)){
						$top = array_pop($stack);
						$categories_flat[$top->id] = $top;
						
						// push child
						if (isset($top->child)){
							foreach (array_reverse($top->child) as $ccid => $child) {
								$child->level = $top->level+1;
								array_push($stack, $child);
							}
						}
					}
				}
			}
			//YTools::dump($categories_flat);
			$options = array();
			foreach ($categories_flat as $cid => $category) {
				$category_title = (($category->level) ? str_repeat('- ', $category->level-1): '') . $category->title;
				$options[] = JHtml::_('select.option', $category->id, $category_title);
			}
	
			// Create a read-only list (no name) with a hidden input to store the value.
			if ((string) $this->element['readonly'] == 'true') {
				$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
				$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
			}
			// Create a regular list.
			else {
				$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
			}
		} else {
			$html[] = "<div style='clear:both; padding: 0 0 0 2em;'>";
			$html[] = "Problem on reading <b>{$db->getPrefix()}k2_categories</b><br>";
			$html[] = "Please contact us on <a href=\"http://www.smartaddons.com\" target=\"_blank\">http://www.smartaddons.com</a><br>";
			$html[] = "Thank you";
			$html[] = "</div>";
		}
		
		return implode($html);
	}
	
	protected function getFieldAttributes(){
		$attr = '';
		
		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		
		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
			$attr .= ' disabled="disabled"';
		}
		
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';
		
		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
		
		return $attr;
	}
	
	protected function com_k2_installed(){
		$db = &JFactory::getDbo();
		$prefix = $db->getPrefix();
		$tables = $db->getTableList();
		return in_array($prefix.'k2_categories', $tables);
	}
}
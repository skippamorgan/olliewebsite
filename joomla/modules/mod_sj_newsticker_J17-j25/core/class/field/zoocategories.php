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

if (!class_exists('_Core_Field_ZooCategories') && class_exists('JFormField')){
	class _Core_Field_ZooCategories extends JFormField {

		public function getInput(){
			$html = array();
			if ($this->com_zoo_installed()){
				$html[] = $this->getInputHtml();
			} else {
				$db = &JFactory::getDbo();
				$html[] = "<div style='clear:both; padding: 0 0 0 2em;'>";
				$html[] = "There are no data table for Zoo.<br>";
				$html[] = "If you have Zoo component installed.<br>";
				$html[] = "Please contact us on <a href=\"http://www.smartaddons.com\" target=\"_blank\">http://www.smartaddons.com</a><br>";
				$html[] = "Thank you";
				$html[] = "</div>";
			}
			return implode("\n", $html);
		}

		protected function getInputHtml(){
			// $currentApplication = &JFormFieldSjZooApplication::$currentApplication;
				
			$db = &JFactory::getDBO();
			$query = "
			SELECT c.id, c.name AS title, c.parent, c.application_id, a.name AS application_title
			FROM #__zoo_category c
			LEFT JOIN #__zoo_application a ON a.id=c.application_id
			ORDER BY c.ordering
			";
			// echo htmlentities( str_replace('#__', 'jos_', $query) );
			$db->setQuery($query);
			$categories = $db->loadObjectList();
				
			$list = array();
			$select_all = isset($this->element['selectall']) && $this->element['selectall']=='true';
			$is_multiple = isset($this->element['multiple']) && $this->element['multiple']=='multiple';
				
			$sections = array();
			if (count($categories)>0){

				foreach ($categories as $i => $category){
					$sections[$category->application_id][$category->id] = &$categories[$i];
				}
				foreach ($sections as $j => $section){
					if (!isset($sections[$j][0]) && !$is_multiple && $select_all){
						$root = new stdClass();
						$root->id = 0;
						$root->title = JText::_('All Categories');
						$root->parent = -1;
						$root->application_id = $j;
						$sections[$j][0] = $root;
					}
					$sections[$category->application_id][$category->id] =& $categories[$i];
				}

				foreach ($sections as $i => $section){
					foreach($section as $j => $category){
						if (isset($section[$category->parent])){
							$section[$category->parent]->child[$category->id] =& $section[$j];
						}
					}
				}

				if (!is_array($this->value)){
					$this->value = array($this->value);
				}

				$select_attr = "";
				if (isset($this->element['multiple'])){
					$select_attr .= " multiple=\"multiple\"";
					$size = 15;
					$select_attr .= " size=\"$size\"";
				}
				if (isset($this->element['css'])){
					$select_attr .= ' class="' . trim($this->element['css']) . '"';;
				} else {
					$select_attr .= ' class="inputbox"';;
				}
					
				$html = "<select $select_attr id=\"" . $this->id . '" name="' . $this->name . '">';
				foreach ($sections as $i => $section){
					foreach($section as $j => $category){
						if (!isset($section[$category->parent])){
							$section[$j]->level = 1;
							$stack = array($section[$j]);
							while( count($stack)>0 ){
								$opt = array_pop($stack);
								$option = array(
										'label' => ($opt->level>1 ? str_repeat('- - ', $opt->level-1) : '') . $opt->title ,
										'value' => $opt->id
								);
								$selected = in_array($opt->id, $this->value) ? 'selected="selected"' : '';
								$html .= '<option class="app' . $opt->application_id . '" value="' . $option['value'] . '" ' . $selected . '>' . $option['label'] . '</option>';

								if (isset($opt->child) && count($opt->child)){
									foreach(array_reverse($opt->child) as $child){
										$child->level = $opt->level+1;
										array_push($stack, $child);
									}
								}
							}
						}
					}
				}
				$html .= '<select>';
				// dynamic change
				$on_change_application = "
				window.addEvent('domready', function(){
				var in_array = function(needle, haystack){
				try{
				for (var j=0; j<haystack.length; j++){
				if (needle==haystack[j]){
				return true;
			}
			}
			} catch(e){
			return false;
			}
			return false;
			}
			var options = $('$this->id').getElements('option');
			var original_app = false;
			var original_categories = [" . implode(',', $this->value) . "];

			var showOptions = function(){
			var current_app = this.value;
			if (!original_app){
			original_app = current_app;
			}
			if (typeof current_app != 'undefined'){
			var class_on = 'app' + parseInt(current_app);
			var currentOptions = new Array();
			for(var i=0; i<options.length; i++){
			if (options[i].hasClass(class_on)){
			options[i].setStyle('display', 'block');
			options[i].setProperty('disabled', false);
			currentOptions.push(options[i]);

			} else {
			options[i].setStyle('display', 'none');
			options[i].setProperty('disabled', 'disabled');
			}
			}
			for(var i=0; i<currentOptions.length; i++){
			if (current_app==original_app){
			var optval = currentOptions[i].getProperty('value');
			var optsta = currentOptions[i].getProperty('selected');
			if (!optsta && in_array(optval, original_categories)){
			currentOptions[i].setProperty('selected', 'selected');
			}
			} else if ( !$('$this->id').get('multiple') ){
			currentOptions[i].setProperty('selected', 'selected');
			break;
			}
			}

			}
			};
			$('jform_params_zoo_app').addEvent('change', showOptions);
			$('jform_params_zoo_app').fireEvent('change');
			});
			";

				$document =& JFactory::getDocument();
				$document->addScriptDeclaration($on_change_application);
			} else {
				$html = "<div style='clear:both; padding: 0 0 0 2em;'>";
				$html .= "Problem on reading <b>{$db->getPrefix()}zoo_category</b><br>";
				$html .= "Please contact us on <a href=\"http://www.smartaddons.com\" target=\"_blank\">http://www.smartaddons.com</a><br>";
				$html .= "Thank you";
				$html .= "</div>";
			}
			return $html;
		}

		protected function com_zoo_installed(){
			$db = &JFactory::getDbo();
			$prefix = $db->getPrefix();
			$tables = $db->getTableList();
			return in_array($prefix.'zoo_application', $tables) && in_array($prefix.'zoo_category', $tables);
		}
	}
}
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

if (!class_exists('_Core_Field_ZooApplication') && class_exists('JFormField')){
	class _Core_Field_ZooApplication extends JFormField {
		public static $currentApplication = null;
		protected function getInput(){
			self::$currentApplication = $this->value;
			$db = &JFactory::getDbo();
			$query = "
			SELECT a.id, a.name as title, a.application_group
			FROM #__zoo_application a
			ORDER BY a.id
			";
			$db->setQuery($query);
			$applications = $db->loadObjectList();
			if (!$applications){
				if (file_exists(JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_zoo' .DS. 'config.php')){
					return JText::_('Loading zoo application error.');
				} else {
					return JText::_('Zoo component is not installed?');
				}
			} else {
				$html = '<select class="inputbox" style="min-width: 200px;" id="' . $this->id . '" name="' . $this->name . '">';
				foreach ($applications as $app){
					$selected = ($app->id == $this->value) ? 'selected="selected"' : '';
					$html .= '<option value="' . $app->id . '" ' . $selected . '>' . $app->title . '</option>';
				}
				$html .= '<select>';
				return $html;
			}
		}
	}
}
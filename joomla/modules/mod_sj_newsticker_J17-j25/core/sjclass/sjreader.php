<?php
/**
 * @package SjClass
 * @subpackage SjReader
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2009-2011 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */

defined('_YTOOLS') or die;

if (!class_exists('SjReader')){
	abstract class SjReader {
		protected static $components = array();
		abstract function getList(&$params);
		public function __get($component){
			if (!isset(self::$components[$component])){
				include_once dirname(__FILE__) . DS . 'components' . DS . 'abstract.php';
				$sj_components_class = dirname(__FILE__) . DS . 'components' . DS . $component . '.php';
				if (file_exists($sj_components_class)){
					include_once $sj_components_class;
					// create new instance
					$class_name = 'Sj' . ucfirst($component) . 'Reader';
					self::$components[$component] = new $class_name();
					// return self::$components[$component];
				} else {
					// echo "<pre>$component reader does not exists!</pre>";
				}
			}
			return self::$components[$component];
		}

		public function getItem($id){
			$item = null;
			if ( !isset($this->_items[$id]) ){
				$this->_getItemsFromDb($id);
			}
			$item = &$this->_items[$id];
			return $item;
		}

		public function getItems($ids){
			$list = array();
			if ( is_string($ids) ){
				$ids = explode(',', $ids);
			}
			if ( is_array($ids) ){
				array_map('intval', $ids);
				$ids = array_unique($ids);
				$missing = array();
				foreach ($ids as $id) {
					if (!isset($this->_items[$id])){
						$missing[$id] = $id;
					}
				}
				$this->_getItemsFromDb($missing);
				foreach ($ids as $id){
					$list[$id] = &$this->_items[$id];
				}
			}
			return $list;
		}

		public function getCategory($cid){
			$category = null;
			if ( !isset($this->_categories[$cid]) ){
				$this->_getCategoriesFromDb($cid);
			}
			$category = &$this->_categories[$cid];
			return $category;
		}

		public function getCategories($cids){
			$list = array();
			if ( is_string($cids) ){
				$cids = explode(',', $cids);
			}
			if ( is_array($cids) ){
				array_map('intval', $cids);
				$cids = array_unique($cids);
				$missing = array();
				foreach ($cids as $cid) {
					if (!isset($this->_categories[$cid])){
						$missing[$cid] = $cid;
					}
				}
				$this->_getCategoriesFromDb($missing);
				foreach ($cids as $cid){
					$list[$cid] = &$this->_categories[$cid];
				}
			}
			return $list;
		}
	}
}
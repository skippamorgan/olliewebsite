<?php
/**
 * @package SjClass
 * @subpackage SjContentReader
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2009-2011 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */

defined('_YTOOLS') or die;

if (!class_exists('SjZooReader')){
	class SjZooReader extends SjAbstractReader{

		protected $app = null;

		public function __construct(){

			$this->addItemFieldToSelect('id');
			$this->addItemFieldToSelect('application_id');
			$this->addItemFieldToSelect(array('name'=>'title'));
			$this->addItemFieldToSelect('type');
			$this->addItemFieldToSelect('alias');
			$this->addItemFieldToSelect('elements');
			$this->addItemFieldToSelect('created');
			$this->addItemFieldToSelect('modified');
			$this->addItemFieldToSelect('hits');
			$this->addItemFieldToSelect(array('created_by'=>'author_id'));
			$this->addItemFieldToSelect(array('EXISTS (SELECT TRUE FROM #__zoo_category_item WHERE item_id=e.id AND category_id = 0)'=>'frontpage'));
			$this->addItemFieldToSelect('params');
            $this->addItemFieldToSelect(array('(SELECT count(cm.id) FROM #__zoo_comment AS cm WHERE cm.item_id=e.id AND cm.state=1)' => 'comments'));

			$this->addCategoryFieldToSelect('id');
			$this->addCategoryFieldToSelect('application_id');
			$this->addCategoryFieldToSelect(array('name'=>'title'));
			$this->addCategoryFieldToSelect('alias');
			$this->addCategoryFieldToSelect('description');
			$this->addCategoryFieldToSelect('parent');
			$this->addCategoryFieldToSelect('ordering');
			$this->addCategoryFieldToSelect('params');
			
			$this->_getZooApplication();
		}

		public function getItemsFromDb($ids, $overload = false){
			if (!is_array($ids)){
				$ids = array((int)$ids);
			}

			$db = &JFactory::getDbo();
			$query = "SELECT " . $this->getItemFields() . " FROM #__zoo_item AS e WHERE e.id IN (" . implode(',', $ids)  . ") GROUP BY e.id;";
			// YTools::dump($query);
			$db->setQuery($query);
			if (!class_exists('Item')){
				require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_zoo' . DS .'classes' . DS . 'item.php');
			}
			$rows = $db->loadObjectList('id', 'Item');
			$item_count = 0;
			if ( !is_null($rows) ){
				foreach($rows as $item){
					if ($overload || !isset($this->_items[$item->id])){
						// decorate data as object
						$item->params = $this->app->parameter->create($item->params);
						
						// decorate data as object
						$item->elements = $this->app->data->create($item->elements);
						
						$this->_items[$item->id] = $item;
						$item_count++;
					}
				}
			}
			return $item_count;
		}

		public function getItemsIn($cids, $params){
			$db = &JFactory::getDbo();
			$now = JFactory::getDate()->toMySQL();
			$nulldate = $db->getNullDate();

			if (is_array($cids)){
				$category_filter_set = implode(',', $cids);
			}
				
			$query = "
			SELECT e.id, EXISTS (SELECT TRUE FROM #__zoo_category_item WHERE item_id=e.id AND category_id=0) AS frontpage
			FROM #__zoo_item as e
			INNER JOIN #__zoo_category_item AS ci ON ci.item_id=e.id AND ci.category_id IN ($category_filter_set)
			WHERE
			e.state IN(1)
			" . ($this->_getContentAccessFilter() ? 'AND '.$this->_getContentAccessFilter() : '') . "
			AND (e.publish_up   = {$db->quote($nulldate)} OR e.publish_up   <= {$db->quote($now)})
			AND (e.publish_down = {$db->quote($nulldate)} OR e.publish_down >= {$db->quote($now)})
			GROUP BY e.id
			{$this->_itemFilter($params)}
			ORDER BY {$this->_itemOrders($params)}
			{$this->_queryLimit($params)}
			";
				
			$db->setQuery($query);
			$items = $db->loadObjectList();
			$ids = array();
			if (isset($items) && count($items)){
				foreach ($items as $i => $item) {
					array_push($ids, $item->id);
				}
			}
			return $ids;
		}

		public function getCategoriesFromDb(){
			if (is_null($this->_categories)){
				$db = &JFactory::getDbo();
				$query = "
				SELECT " . $this->getCategoryFields() . "
				FROM #__zoo_category AS e
				WHERE
				e.published IN (1)
				AND e.parent >= 0
				GROUP BY e.id
				";
				$db->setQuery($query);
				$rows = $db->loadObjectList();
				if ( !is_null($rows) ){
					foreach($rows as $category){
						$category->child_category = array();
						$this->_categories[$category->id] = $category;
					}
					$this->buildCategoriesTree();
				}
			}
			return $this->_categories;
		}

		public function buildCategoriesTree(){
			if(count($this->_categories)){
				foreach ($this->_categories as $cid => $category) {
					if (isset($this->_categories[$category->parent])){
						$parent_category = &$this->_categories[$category->parent];
						if (!isset($parent_category->child_category[$category->id])){
							$parent_category->child_category[$category->id] = $category;
						}
					}
				}
			}
		}

		protected function _itemFilter($params, $alias='e', $catit='ci'){
			$join_filter="";
			if ( isset($params['source_filter']) ){
				// frontpage filter.
				switch ($params['source_filter']){
					default:
					case '0':
						$join_filter = "";
					break;
					case '1':
						$join_filter = "HAVING frontpage=0";
						break;
					case '2':
						$join_filter = "HAVING frontpage=1";
						break;
				}
			}
			return $join_filter;
		}

		protected function _itemOrders($params, $alias='e'){
			// set order by default
			$item_order_by = "$alias.priority";

			if ( isset($params['source_order_by']) ){
				$string_order_by = trim($params['source_order_by']);
				switch ($string_order_by){
					default:
					case 'ordering':
						$item_order_by = "$alias.priority";
					break;
					case 'mostview':
					case 'hits':
						$item_order_by = "$alias.hits DESC";
						break;
					case 'recently_add':
					case 'created':
						$item_order_by = "$alias.created DESC";
						break;
					case 'recently_mod':
					case 'modified':
						$item_order_by = "$alias.modified DESC";
						break;
					case 'title':
						$item_order_by = "$alias.name";
						break;
					case 'random':
						$item_order_by = 'rand()';
						break;
				}
			}
			return $item_order_by;
		}

		protected function _queryLimit($params){
			$source_limit = '';
			$source_limit_start = 0;
			if (isset($params['source_limit']) && (int)$params['source_limit']){
				//$source_limit_start = 0;
				if (isset($params['source_limit_start']) && (int)$params['source_limit_start']){
					$source_limit_start = (int)$params['source_limit_start'];
				}
				$source_limit_total = (int)$params['source_limit'];
				$source_limit = "LIMIT $source_limit_start, $source_limit_total";
			}
			return $source_limit;
		}

		protected function _getContentAccessFilter($alias='e'){
			$condition = false;
			$app  = &JFactory::getApplication();
			$params = $app->getParams();
			if ($params instanceof JRegistry && !$params->get('show_noauth', 0)){
				$user = &JFactory::getUser();
				$condition = $alias . '.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')';
			}
			return $condition;
		}

		protected function _getZooApplication(){
			if (is_null($this->app)){
				// load config
				require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

				// get zoo instance
				$this->app = &App::getInstance('zoo');
			}
			return $this->app;
		}

		public function getCategoryImage(&$category){
			$image = false;
			if (is_int($category)){
				$category = &$this->getCategory($category);
			}
			if (is_object($category) && isset($category->params) && !empty($category->params)){
				$cparams = json_decode($category->params, true);
				if (isset($cparams['content.image'])){
					$image = $cparams['content.image'];
				}
			}
			return $image;
		}

		protected $_image4=array();
		public function getItemImage(&$item, $params){
			if (is_int($item)){
				$item = &$this->getItem($item);
			}
			$elements = isset($params['media_elements']) ? trim($params['media_elements']) : 'image';
			
			if (!isset($this->_image4[$item->id][$elements])){
				if (!isset($item->app)){
					// set application refer
					$item->app = &$this->_getZooApplication();
					is_string($item->params) && ($item->params = $item->app->parameter->create($item->params));
					is_string($item->elements) && ($item->elements = $item->app->data->create($item->elements));
				}
				$media_elements = $this->_parseValues($elements);
				$media_elements['_current'] = count($media_elements);
				$item_elements = $item->getElements();
				if ($item_elements)
					foreach ($item_elements as $element){
					$element_class = get_class($element);
					$eclass_suffix = substr($element_class, 7);
					$eclass_suffix = strtolower($eclass_suffix);
					if ( array_key_exists($eclass_suffix, $media_elements) ){
						// image
						if ($media_elements[$eclass_suffix]<$media_elements['_current']){
							$item->image = $element->hasValue('file') ? $element->get('file') : false;
							$media_elements['_current'] = $media_elements[$eclass_suffix];
						}
					}
				}
				if ($this->getItemDescription($item, $params)){
					$inline_images = YTools::extractImages($item->description);
					if(!isset($item->image) or $item->image === false) {
						$item->image = count($inline_images) ? array_shift($inline_images) : null;
					}
				}
				$this->_image4[$item->id][$elements] = isset($item->image);
			}
			return $this->_image4[$item->id][$elements];
		}
		
		public function getItemMultiImage(&$item, $params){
			
			if (is_int($item)){
				$item = &$this->getItem($item);
			}
			$elements = isset($params['media_elements']) ? trim($params['media_elements']) : 'image';
			
			if (!isset($this->_image4[$item->id][$elements])){
				if (!isset($item->app)){
					// set application refer
					$item->app = &$this->_getZooApplication();
					is_string($item->params) && ($item->params = $item->app->parameter->create($item->params));
					is_string($item->elements) && ($item->elements = $item->app->data->create($item->elements));
				}
				$media_elements = $this->_parseValues($elements);
				$media_elements['_current'] = count($media_elements);
				$item_elements = $item->getElements();
				if ($item_elements){
					$item->images	= array();
					foreach ($item_elements as $element){
						$element_class 	= get_class($element);
						$eclass_suffix 	= substr($element_class, 7);
						$eclass_suffix 	= strtolower($eclass_suffix);
						if ( array_key_exists($eclass_suffix, $media_elements) ){
							// image
							if ($media_elements[$eclass_suffix]<$media_elements['_current']){
								if ($element->hasValue('file')){
									$item->images[] = $element->get('file');
									$media_elements['_current'] = $media_elements[$eclass_suffix];
								}
							}
						}
					}
				}

				if ($this->getItemDescription($item, $params)){
					$inline_images = YTools::extractImages($item->description);
					if (!empty($inline_images)){
						$item->images	= array_merge($item->images,$inline_images);
					}
				}
				// $this->_image4[$item->id][$elements] = !empty($item->images);
			}
			return !empty($item->images);
		}

		public function getItemDescription(&$item, $params){
			if (is_int($item)){
				$item = &$this->getItem($item);
			}
			$elements = isset($params['description_elements']) ? trim($params['description_elements']) : 'description,text,textarea';
			if (!isset($this->_desc4[$item->id][$elements])){
				if (!isset($item->app)){
					// set application refer
					$item->app = &$this->_getZooApplication();
					is_string($item->params) && ($item->params = $item->app->parameter->create($item->params));
					is_string($item->elements) && ($item->elements = $item->app->data->create($item->elements));
				}
				$description_elements = $this->_parseValues($elements);
				$description_elements['_current'] = count($description_elements);
				$item_elements = $item->getElements();
				if ($item_elements)
					foreach ($item_elements as $element){
					$element_class = get_class($element);
					$eclass_suffix = substr($element_class, 7);
					$eclass_suffix = strtolower($eclass_suffix);
						
					if ( array_key_exists($eclass_suffix, $description_elements) ){
						// image
						if ($description_elements[$eclass_suffix]<$description_elements['_current']){
							$item->description = $element->hasValue('value') ? $element->get('value') : '';
							$description_elements['_current'] = $description_elements[$eclass_suffix];
						}
					}
				}
					
					
				$this->_desc4[$item->id][$elements] = isset($item->description);
			}
			return $this->_desc4[$item->id][$elements];
		}

		public function getItemUrl(&$item){
			if (is_int($item)){
				$item = &$this->getItem($item);
			}
			if (!isset($item->url)){
				//$item->url = JRoute::_( 'index.php?option=com_zoo&task=item&item_id='. $item->id);
				$item->url = $this->app->route->item($item);
			}
			return $item->url;
		}

		public function _parseValues($paramString=null){
			$array = array();
			if ( isset($paramString) && is_string($paramString)){
				$keys = explode(',', $paramString);
				$keys = array_map('trim', $keys);
				$i=0;
				foreach ($keys as $key){
					$array[$key] = $i++;
				}
			}
			return $array;
		}

	}
}
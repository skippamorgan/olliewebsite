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
class NewsTicker extends SjModule{
	protected static $_instance = null;
	
	public function __construct($params=array()){
		$this->setParams($params);
	}
	
	public function getReader(){
		if (!isset($this->_reader)){
			$classReader = $this->getParams('reader');
			
			$class_name = __CLASS__;			
			$class_name .= (null !== $classReader) ? $classReader : 'Reader';
			
			if (!class_exists($class_name)){
				// load reader class file
				$filename = dirname(__FILE__) . DS . strtolower($class_name) . '.php';
				if (file_exists($filename)){
					include_once $filename;
				} else {
					throw new Exception("File not found: " . $filename);
				}
			}
			$this->_reader = new $class_name();
		}
		return $this->_reader;
	}
	
	public static function getInstance($params=null){
		if (self::$_instance===null){
			$class_name = __CLASS__;
			self::$_instance = new $class_name($params);		
		} else {
			self::$_instance->setParams($params);
		}
		return self::$_instance;
	}
	
	public static function getList($params=array()){
		return self::getInstance($params)->getItems();
	}
}
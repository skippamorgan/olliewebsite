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
class NewsTickerReader extends SjReader{		
	public function getList(&$params){	
            $list =array();
            $items=array();		
            if (!isset($params['source']) || empty($params['source'])){
        		$this->errors = "No selected or all selected is unpublished.";
        		return array();
    		}
			$items = $this->content->getCategoryItems($params['source'], $params);
			if(!empty($items)) {
				include_once JPATH_SITE . DS . 'components' . DS . 'com_content' . DS . 'helpers' . DS . 'route.php';
				$arrcustom_url=array();
				$arrcustom_url = $this->_getCustomUrl($params['custom_url']);
				foreach($items as $key => $item){				    
					$temp = array();
					if (!$this->content->getItemImage($item)){
                        $image_url = "";
						$item->image = $image_url;
					}

					$temp['id'] = $item->id;
					$temp['title'] = $item->title;
					$temp['image'] = $item->image;
					
					if(array_key_exists($item->id, $arrcustom_url)){
						$temp['link'] = $arrcustom_url[$item->id];
					}else{
					    $this->content->getItemUrl($item);
						$temp['link'] = $item->url;
					}
					
					if ((int)$params['item_description_striptags'] == 0){
						$temp['desc'] = strip_tags($item->description);
					} else {
						$temp['desc'] = ($item->description);
					}
					$list[] = $temp;
				}
		 }
         return $list;
    }
    
	private function _getCustomUrl($custom_url) {     
    	$arrUrl = array();
        $tmp = explode("\n", trim($custom_url));            
        foreach( $tmp as $strTmp){
        	$pos = strpos($strTmp, ":");
            if($pos >=0){
            	$tmpKey = substr($strTmp, 0, $pos);
                $key = trim($tmpKey);
                $tmpLink = substr($strTmp, $pos+1, strlen($strTmp)-$pos);
                $haveHttp =  strpos(trim($tmpLink), "http://");
                if($haveHttp<0 || ($haveHttp !== false) ){
                    $link = trim($tmpLink);
                }else{
                    $link = "http://" . trim($tmpLink);
                }
                $arrUrl[$key] = $link;
            }  
        }            
        return $arrUrl;
	}
}
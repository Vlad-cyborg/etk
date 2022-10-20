<?php
/**
* @version      4.11.0 01.09.2015
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

class JshopHelpersSelectOptions{
	
	public static function getCountrys($option_select = 1){
		$app = JFactory::getApplication();
		$option = array();
		$country = JSFactory::getTable('country', 'jshop');
		if ($option_select){
			$option[] = JHTML::_('select.option',  '0', _JSHOP_REG_SELECT, 'country_id', 'name' );
		}
		if ($app->getName() != 'site'){
			$list = $country->getAllCountries(0);
		}else{
			$list = $country->getAllCountries();
		}
        $option = array_merge($option, $list);
	return $option;
	}
	
	public static function getTitles(){
		$jshopConfig = JSFactory::getConfig();
		$option = array();
		foreach($jshopConfig->user_field_title as $key => $value) {
            $option[] = JHTML::_('select.option', $key, $value, 'id', 'name');
        }
		return $option;
	}
	
	public static function getClientTypes(){
		$jshopConfig = JSFactory::getConfig();
		$option = array();
        foreach($jshopConfig->user_field_client_type as $key => $value){
            $option[] = JHTML::_('select.option', $key, $value, 'id', 'name');
        }
		return $option;
	}
	
	public static function getProductsOrdering($typelist = 0){
		$jshopConfig = JSFactory::getConfig();
		$option = array();
		if ($typelist==1){
			$list = $jshopConfig->sorting_products_name_select;
		}else{
			$list = $jshopConfig->sorting_products_name_s_select;
		}
		foreach($list as $key=>$value){
            $option[] = JHTML::_('select.option', $key, $value, 'id', 'name' );
        }
		return $option;
	}
	
	public static function getProductsCount($extended_value = null){
		$jshopConfig = JSFactory::getConfig();
		$list = $jshopConfig->count_product_select;
		if (!is_null($extended_value)){
			insertValueInArray($extended_value, $list);
		}
		$option = array();
        foreach($list as $key => $value){
            $option[] = JHTML::_('select.option',$key, $value, 'id', 'name' );
        }
		return $option;
	}
	
}
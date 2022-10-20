<?php
/**
* @version      4.11.0 01.09.2015
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

class JshopHelpersRequest{
	
	public static function getQuantity($key = 'quantity', $fix = 0){
		$jshopConfig = JSFactory::getConfig();
		if ($jshopConfig->use_decimal_qty){
            $quantity = floatval(str_replace(",", ".", JRequest::getVar($key, 1)));
            $quantity = round($quantity, $jshopConfig->cart_decimal_qty_precision);
        }else{
            $quantity = JRequest::getInt($key, 1);
        }
		if ($fix && $quantity < 0){
			$quantity = 0;
		}
		return $quantity;
	}
	
	public static function getAttribute($key = 'jshop_attr_id'){
		$attribut = JRequest::getVar($key);
        if (!is_array($attribut)) $attribut = array();
        foreach($attribut as $k=>$v){
			$attribut[intval($k)] = intval($v);
		}
		return $attribut;
	}
	
	public static function getFreeAttribute($key = 'freeattribut'){
		$attribut = JRequest::getVar($key);
        if (!is_array($attribut)) $attribut = array();
		return $attribut;
	}
    
    public static function getCartTo(){
        $to = JRequest::getVar('to', "cart");
        if ($to!="cart" && $to!="wishlist") $to = "cart"; 
        return $to;
    }
	
}
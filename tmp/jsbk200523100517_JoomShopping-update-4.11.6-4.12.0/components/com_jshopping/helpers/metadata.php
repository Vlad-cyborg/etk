<?php
/**
* @version      4.11.0 01.09.2015
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

class JshopHelpersMetadata{
	
	public static function metaData($alias, $loadParams = 1, $default_title = '', $path_way = '', $external_params = null){
		if ($path_way!=''){
			appendPathWay($path_way);
		}
		if ($loadParams && is_null($external_params)){
			$params = JFactory::getApplication()->getParams();
		}else{
			$params = null;
		}
		if ($external_params){
			$params = $external_params;
		}
		$seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData($alias);
		if ($seodata->title==""){
            $seodata->title = $default_title;
        }
        setMetaData($seodata->title, $seodata->keyword, $seodata->description, $params);
	}
	
	public static function mainCategory($category, $params){
		setMetaData($category->meta_title, $category->meta_keyword, $category->meta_description, $params);
	}
	
	public static function category($category){
		if (getShopMainPageItemid()==JRequest::getInt('Itemid')){
            appendExtendPathWay($category->getTreeChild(), 'category');
        }
        if ($category->meta_title=="") $category->meta_title = $category->name;
        setMetaData($category->meta_title, $category->meta_keyword, $category->meta_description);
	}
	
	public static function cart(){
		$params = JFactory::getApplication()->getParams();
		$seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("cart");
        if (getThisURLMainPageShop()){            
            appendPathWay(_JSHOP_CART);
            if ($seodata->title==""){
                $seodata->title = _JSHOP_CART;
            }
            setMetaData($seodata->title, $seodata->keyword, $seodata->description);
        }else{            
            setMetaData($seodata->title, $seodata->keyword, $seodata->description, $params);
        }
	}
	
	public static function checkoutAddress(){
		self::metaData("checkout-address", 0, _JSHOP_CHECKOUT_ADDRESS, _JSHOP_CHECKOUT_ADDRESS);
	}
	
	public static function checkoutPayment(){		
		self::metaData("checkout-payment", 0, _JSHOP_CHECKOUT_PAYMENT, _JSHOP_CHECKOUT_PAYMENT);
	}
	
	public static function checkoutShipping(){		
		self::metaData("checkout-shipping", 0, _JSHOP_CHECKOUT_SHIPPING, _JSHOP_CHECKOUT_SHIPPING);
	}
	
	public static function checkoutPreview(){		
		self::metaData("checkout-preview", 0, _JSHOP_CHECKOUT_PREVIEW, _JSHOP_CHECKOUT_PREVIEW);
	}
	
	public static function checkoutFinish(){
		$document = JFactory::getDocument();
        $document->setTitle(_JSHOP_CHECKOUT_FINISH);
        appendPathWay(_JSHOP_CHECKOUT_FINISH);
	}
	
	public static function content($page){
		switch($page){
            case 'agb':
                $pathway = _JSHOP_AGB;
            break;
            case 'return_policy':
                $pathway = _JSHOP_RETURN_POLICY;
            break;
            case 'shipping':
                $pathway = _JSHOP_SHIPPING;
            break;
            case 'privacy_statement':
                $pathway = _JSHOP_PRIVACY_STATEMENT;
            break;
        }
		appendPathWay($pathway);

        $seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("content-".$page);
        if ($seodata->title==""){
            $seodata->title = $pathway;
        }
        setMetaData($seodata->title, $seodata->keyword, $seodata->description);
		return $seodata;
	}
	
	public static function listManufacturers($params){
		self::metaData("manufacturers", 0, '', '', $params);
	}
	
	public static function manufacturer($manufacturer){
		if (getShopManufacturerPageItemid()==JRequest::getInt('Itemid')){
            appendPathWay($manufacturer->name);
        }
        if ($manufacturer->meta_title=="") $manufacturer->meta_title = $manufacturer->name;
        setMetaData($manufacturer->meta_title, $manufacturer->meta_keyword, $manufacturer->meta_description);
	}
	
	public static function search(){
		$params = JFactory::getApplication()->getParams();
		$seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("search");
        if (getThisURLMainPageShop()){
            appendPathWay(_JSHOP_SEARCH);
            if ($seodata->title==""){
                $seodata->title = _JSHOP_SEARCH;
            }
            setMetaData($seodata->title, $seodata->keyword, $seodata->description);
        }else{
            setMetaData($seodata->title, $seodata->keyword, $seodata->description, $params);
        }
	}
	
	public static function searchResult(){
		$params = JFactory::getApplication()->getParams();
		$seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("search-result");
        if (getThisURLMainPageShop()){
            appendPathWay(_JSHOP_SEARCH);
            if ($seodata->title==""){
                $seodata->title = _JSHOP_SEARCH;
            }
            setMetaData($seodata->title, $seodata->keyword, $seodata->description);
        }else{
            setMetaData($seodata->title, $seodata->keyword, $seodata->description, $params);
        }
	}
	
	public static function userLogin(){
		$params = JFactory::getApplication()->getParams();
		$seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("login");
        if (getThisURLMainPageShop()){
            appendPathWay(_JSHOP_LOGIN);
            if ($seodata->title==""){
                $seodata->title = _JSHOP_LOGIN;
            }
            setMetaData($seodata->title, $seodata->keyword, $seodata->description);
        }else{
            setMetaData($seodata->title, $seodata->keyword, $seodata->description, $params);
        }
	}
	
	public static function userRegister(){
		$params = JFactory::getApplication()->getParams();
		$seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("register");
        if (getThisURLMainPageShop()){
            appendPathWay(_JSHOP_REGISTRATION);
            if ($seodata->title==""){
                $seodata->title = _JSHOP_REGISTRATION;
            }
            setMetaData($seodata->title, $seodata->keyword, $seodata->description);
        }else{
            setMetaData($seodata->title, $seodata->keyword, $seodata->description, $params);
        }
	}
	
	public static function userEditaccount(){
		self::metaData("editaccount", 0, _JSHOP_EDIT_DATA, _JSHOP_EDIT_DATA);
	}
	
	public static function userOrders(){
		$shim = shopItemMenu::getInstance();
		if ($shim->getOrders()!=JRequest::getInt('Itemid')){
			$path_way = _JSHOP_MY_ORDERS;
		}else{
			$path_way = '';
		}
		self::metaData("myorders", 0, _JSHOP_MY_ORDERS, $path_way);
	}
	
	public static function userOrder($order){
		$jshopConfig = JSFactory::getConfig();        
		self::metaData("myorder-detail", 0, _JSHOP_MY_ORDERS);
		$shim = shopItemMenu::getInstance();
		if ($shim->getOrders()!=JRequest::getInt('Itemid')){
			appendPathWay(_JSHOP_MY_ORDERS, SEFLink('index.php?option=com_jshopping&controller=user&task=orders', 0, 0, $jshopConfig->use_ssl));
		}
        appendPathWay(_JSHOP_ORDER_NUMBER.": ".$order->order_number);
	}
	
	public static function userMyaccount(){
		self::metaData("myaccount", 0, _JSHOP_MY_ACCOUNT, _JSHOP_MY_ACCOUNT);
	}
	
	public static function userGroupsinfo(){
		setMetaData(_JSHOP_USER_GROUPS_INFO, "", "");
	}
	
	public static function listVendors(){
		self::metaData("vendors");
	}
	
	public static function vendorInfo($vendor){
		$title =  $vendor->shop_name;        
        appendPathWay($title);
        
        $seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("vendor-info-".$vendor->id);
        if (!isset($seodata)) {
            $seodata = new stdClass();
            $seodata->title = '';
            $seodata->keyword = '';
            $seodata->description = '';
        }
        if ($seodata->title==""){
            $seodata->title = $title;
        }
        setMetaData($seodata->title, $seodata->keyword, $seodata->description);
	}
	
	public static function vendorProducts($vendor){
		appendPathWay($vendor->shop_name);
        $seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("vendor-product-".$vendor->id);
        if (!isset($seodata->title) || $seodata->title==""){
            $seodata = new stdClass();
            $seodata->title = $vendor->shop_name;
            $seodata->keyword = $vendor->shop_name;;
            $seodata->description = $vendor->shop_name;;
        }
        setMetaData($seodata->title, $seodata->keyword, $seodata->description);
	}
	
	public static function wishlist(){
		$params = JFactory::getApplication()->getParams();
		$seo = JSFactory::getTable("seo", "jshop");
        $seodata = $seo->loadData("wishlist");
        if (getThisURLMainPageShop()){            
            appendPathWay(_JSHOP_WISHLIST);
            if ($seodata->title==""){
                $seodata->title = _JSHOP_WISHLIST;
            }
            setMetaData($seodata->title, $seodata->keyword, $seodata->description);
        }else{
            setMetaData($seodata->title, $seodata->keyword, $seodata->description, $params);
        }
	}
	
	public static function product($category, $product){
		if (getShopMainPageItemid()==JRequest::getInt('Itemid')){
            appendExtendPathway($category->getTreeChild(), 'product');
        }
        appendPathWay($product->name);
        if ($product->meta_title=="") $product->meta_title = $product->name;
        setMetaData($product->meta_title, $product->meta_keyword, $product->meta_description);
	}
	
	public static function allProducts(){
		self::metaData("all-products");
	}
	
	public static function productsTophits(){		
		self::metaData("tophitsproducts");
	}
	
	public static function productsToprating(){		
		self::metaData("topratingproducts");
	}
	
	public static function productsLabel(){		
		self::metaData("labelproducts");
	}
	
	public static function productsBestseller(){		
		self::metaData("bestsellerproducts");
	}
	
	public static function productsRandom(){		
		self::metaData("randomproducts");
	}
	
	public static function productsLast(){		
		self::metaData("lastproducts");
	}

}
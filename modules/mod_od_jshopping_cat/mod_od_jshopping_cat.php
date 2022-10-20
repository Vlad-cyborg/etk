<?php
/**
* @version      1.0.0 27/02/2015
* @author       Odlord
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/ 	
    defined('_JEXEC') or die('Restricted access');
    error_reporting(error_reporting() & ~E_NOTICE);    
    if (!file_exists(JPATH_SITE.'/components/com_jshopping/jshopping.php')){
        JError::raiseError(500,"Please install component \"joomshopping\"");
    } 

    require_once (dirname(__FILE__).'/helper.php');

    $document = JFactory::getDocument();

    require_once (JPATH_SITE.'/components/com_jshopping/lib/factory.php'); 
    require_once (JPATH_SITE.'/components/com_jshopping/lib/jtableauto.php');
    require_once (JPATH_SITE.'/components/com_jshopping/tables/config.php'); 
    require_once (JPATH_SITE.'/components/com_jshopping/lib/functions.php');
    require_once (JPATH_SITE.'/components/com_jshopping/lib/multilangfield.php');

    $lang = JFactory::getLanguage();
    if(file_exists(JPATH_SITE.'/components/com_jshopping/lang/' . $lang->getTag() . '.php')) 
        require_once (JPATH_SITE.'/components/com_jshopping/lang/'.  $lang->getTag() . '.php'); 
    else 
        require_once (JPATH_SITE.'/components/com_jshopping/lang/en-GB.php'); 
    
    JTable::addIncludePath(JPATH_SITE.'/components/com_jshopping/tables'); 

    $field_sort = $params->get('category_sort', 'id');
    $ordering = $params->get('sort_order', 'asc');
    $display_img = $params->get('display_img', '1');

    $count = $params->get('counter', 0);
    $class = $params->get('moduleclass_sfx', '');

// Get params     
    $category_id = JRequest::getInt('category_id');
    $category = JTable::getInstance('category', 'jshop');

if($params->get('cssstyle') == 1){
    $tpl = explode(":", $params->get('layout'));
    //$tpl        = explode(":", $params->def('template'));
    if($tpl[0] == '_') {
        $jtpl   = $app->getTemplate();
    } else {
        $jtpl   = $tpl[0];
    }

    if (is_file(JPATH_SITE . '/modules/mod_od_jshopping_cat/tmpl/'. $tpl[1] .'/css/style.css')) {
        $css = 'modules/mod_od_jshopping_cat/tmpl/'. $tpl[1] .'/css/style.css';
        $document->addStylesheet(JURI::base() . $css);
    }
    if (is_file(JPATH_SITE . '/templates/'. $jtpl .'/html/mod_od_jshopping_cat/'. $tpl[1] .'/css/style.css')) {
        $css = 'templates/'. $jtpl .'/html/mod_od_jshopping_cat/'. $tpl[1] .'/css/style.css';
        $document->addStylesheet(JURI::base() . $css);
    }                                                  
}

$arResult = modODJShoppingCategoryHelper::getCatsArray($params, $category_id, $category);

$jshopConfig = JSFactory::getConfig();

require JModuleHelper::getLayoutPath('mod_od_jshopping_cat', $params->get('layout', 'default'));

?>
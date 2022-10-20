<?php
/**
* @version 4.1.5 06/06/2021
* @author       Brooksus
* @package      JoomShopping
* @copyright    Copyright (C) 2016 Brooksite.ru. All rights reserved.
* @license      2016. Brooksite.ru (http://brooksite.ru/litsenzionnoe-soglashenie.html).
**/

defined('_JEXEC') or die('Restricted access');
error_reporting(E_ALL & ~E_NOTICE); 

if (!file_exists(JPATH_SITE.'/components/com_jshopping/jshopping.php')){
	JError::raiseError(500,"Please install component \"joomshopping\"");
}

jimport('joomla.application.component.model');
JLoader::register('mod_jshopping_compareHelper', __DIR__ . '/helper.php');
//require_once __DIR__ . '/helper.php'; //for J2.5
require_once (JPATH_SITE.'/components/com_jshopping/lib/factory.php'); 
require_once (JPATH_SITE.'/components/com_jshopping/lib/functions.php');

JSFactory::loadCssFiles();
JSFactory::loadLanguageFile();
$jshopConfig = JSFactory::getConfig();

//
$layout = $params->get('layout', 'default');
$bs_version=$params->get('bs_version', 0);
$compare_name = $params->get('compare_name');
$text_link_add_to_compare = $params->get('text_link_add_to_compare');
$check_compare_quantity = $params->get('check_compare_quantity',1);
$compare_quantity = $params->get('compare_quantity',4);
$compare_content_show = $params->get('compare_content_show',1);
$compare_position = $params->get('compare_position',1);
$compare_modal = $params->get('compare_modal',0);
$compare_link_text = $params->get('compare_link_text','');
$empty_text = $params->get('empty_text','Ваш текст в модуле, если товары к сравнению не выбраны');
$color = $params->get('compare_color', 1);
if ($bs_version=="0"){
	$bsv="bs2";
} elseif ($bs_version=="1") {
	$bsv="bs3";
} else {
	$bsv="bs4";	
}
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base().'modules/mod_jshopping_compare/assets/css/default.min.v3.css');
mod_jshopping_compareHelper::ajaxDataInCompare($jshopConfig, $document, $module, $bsv);
$document->addScript(JURI::base().'modules/mod_jshopping_compare/assets/js/compare_ajax.min.js','text/javascript', true, false);

if (!trim($compare_link_text)){
	$seflink=SEFLink('index.php?option=com_jshopping&controller=compare&task=view', 1);
} else {
	$seflink=trim($compare_link_text);
}

session_start();
if (is_array($_SESSION['comparep'])){
	$compare_arr = array_values($_SESSION['comparep']);
} else {
	$compare_arr = $_SESSION['comparep'];
}
$product = JTable::getInstance('product', 'jshop');
$lang = JSFactory::getLang();

require(JModuleHelper::getLayoutPath('mod_jshopping_compare',$layout)); 
?>
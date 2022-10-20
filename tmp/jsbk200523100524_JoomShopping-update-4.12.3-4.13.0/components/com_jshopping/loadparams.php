<?php
/**
* @version      4.11.0 18.12.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$session = JFactory::getSession();
$ajax = JRequest::getInt('ajax');

initLoadJoomshoppingLanguageFile();
$jshopConfig = JSFactory::getConfig();
setPrevSelLang($jshopConfig->getLang());
if (JRequest::getInt('id_currency')){
	reloadPriceJoomshoppingNewCurrency(JRequest::getVar('back'));
}

$user = JFactory::getUser();

$js_update_all_price = $session->get('js_update_all_price');
$js_prev_user_id = $session->get('js_prev_user_id');
if ($js_update_all_price || ($js_prev_user_id!=$user->id)){
    updateAllprices();
    $session->set('js_update_all_price', 0);
}
$session->set("js_prev_user_id", $user->id);

if (!$ajax){
    installNewLanguages();
    $document = JFactory::getDocument();
    $viewType = $document->getType();
    if ($viewType=="html"){
        JSFactory::loadCssFiles();
        JSFactory::loadJsFiles();
    }
}else{
    //header for ajax
    header('Content-Type: text/html;charset=UTF-8');
}
JDispatcher::getInstance()->trigger('onAfterLoadShopParams', array());
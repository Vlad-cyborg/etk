<?php
/**
* @version 4.0.6 02/10/2017
* @author       Brooksus
* @package      JoomShopping
* @copyright    Copyright (C) 2016 Brooksite.ru. All rights reserved.
* @license      2016. Brooksite.ru (http://brooksite.ru/litsenzionnoe-soglashenie.html).
**/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class plgJshoppingProductsCompare extends JPlugin{
	
	public function __construct(&$subject, $config = array()) {
        parent::__construct($subject, $config);
		JPlugin::loadLanguage('plg_jshoppingproducts_compare',JPATH_ADMINISTRATOR); 
		}
		
	function onBeforeDisplayProductListView(&$view){
		include_once dirname(__FILE__) . '/helper_list_compare.php'; 
	}


	function onBeforeDisplayProductView(&$view){
		include_once dirname(__FILE__) . '/helper_compare.php';
	}


	function onBeforeDisplayCompareView(&$view){
		$document = JFactory::getDocument();
		$compare_code = $this->params->get('compare_code', 1);
		$compare_manufacturer = $this->params->get('compare_manufacturer', 1);
		$compare_show_description = $this->params->get('compare_show_description', 1);
		$compare_raiting = $this->params->get('compare_raiting', 1);
		$compare_weight = $this->params->get('compare_weight', 1);
		$compare_attr = $this->params->get('compare_attr', 1);
		$add_style = $this->params->get('add_style', 1);
		
		if ($compare_code=='2'){
			$view->_tmp_var_compare_code='compare_dnone';
		}
		if ($compare_manufacturer=='2'){
			$view->_tmp_var_compare_manufacturer='compare_dnone';
		}
		if ($compare_show_description=='2'){
			$view->_tmp_var_compare_shortdescr='compare_dnone';	
		}
		if ($compare_raiting=='2'){
			$view->_tmp_var_compare_rat='compare_dnone';
		}
		if ($compare_weight=='2'){
			$view->_tmp_var_compare_weight='compare_dnone';
		}
		if ($compare_attr=='1'){
			$document->addScript(JURI::base().'plugins/jshoppingproducts/compare/js/compare_attr.js');
		}
		if ($compare_attr=='2'){
			$view->_tmp_var_compare_attr='compare_dnone';
		}
		if ($add_style=='1'){
			$document->addStyleSheet(JURI::base().'plugins/jshoppingproducts/compare/css/compare.css');
		}
	}

}
?>
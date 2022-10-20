<?php
/**
* @version      4.11.0 24.07.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

class JshoppingControllerContent extends JshoppingControllerBase{
    
    function __construct($config = array()){
        parent::__construct($config);
        JPluginHelper::importPlugin('content');
        JDispatcher::getInstance()->trigger('onConstructJshoppingControllerContent', array(&$this));
    }
    
    function display($cachable = false, $urlparams = false){
        JError::raiseError(404, _JSHOP_PAGE_NOT_FOUND);
    }

    function view(){        
		$model = JSFactory::getModel('contentPage', 'jshop');

        $page = JRequest::getVar('page');
		$order_id = JRequest::getInt('order_id');
        $cartp = JRequest::getInt('cart');
        
		$seodata = JshopHelpersMetadata::content($page);
		$model->setSeodata($seodata);
        
        $text = $model->load($page, $order_id, $cartp);
		if ($text===false){
			JError::raiseError(404, $model->getError());
			return 0;
		}		

        $view = $this->getView("content");
        $view->setLayout("content");
        $view->assign('text', $text);
        JDispatcher::getInstance()->trigger('onBeforeDisplayContentView', array(&$view));
        $view->display();
    }
}
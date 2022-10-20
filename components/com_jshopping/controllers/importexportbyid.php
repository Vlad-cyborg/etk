<?php
/**
* @version      4.13.0 23.09.2020
* @author       iMaud Studio
* @package      Jshopping
* @copyright    Copyright (C) 2020 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die();

if(version_compare(JVERSION, '3.0.0', 'lt')) {
	jimport('joomla.application.component.controller');
} else {
	JModelLegacy::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/models');
}

include_once(JPATH_COMPONENT_ADMINISTRATOR."/importexport/iecontroller.php");

if(class_exists('JshoppingControllerBase')) {
	// Joomshopping 4.x and newest
	
	class JshoppingControllerImportExportById extends JshoppingControllerBase{
    
    function display($cachable = false, $urlparams = false){
			JError::raiseError(404, _JSHOP_PAGE_NOT_FOUND);
    }

    function start(){

			$_GET['noredirect'] = 1;
			$_POST['noredirect'] = 1;
			$_REQUEST['noredirect'] = 1;
			
			$key = $this->input->getVar("key");
			$alias = $this->input->getWord("alias");
			$ie_id = $this->input->getInt("ie_id");
			
			$model = JSFactory::getModel('importExportStart', 'jshop');
			if ($model->checkKey($key)){
				if($alias && $ie_id) {
					$model->execute($alias, $ie_id);
				} else {
					$model->executeList(null, 1, $alias);
				}
			}
			die();
    }
	}

} else {
	// Joomshopping 3.x
	
	class JshoppingControllerImportExportById extends JController {
    
    function display($cachable = false, $urlparams = false){
        JError::raiseError(404, _JSHOP_PAGE_NOT_FOUND);
    }
    
    function start() {
			$jshopConfig = JSFactory::getConfig();
			
			$key = JRequest::getVar("key");
			if ($key!=$jshopConfig->securitykey) die();
			
 			$ie_alias = JRequest::getWord('alias');
			$ie_id = JRequest::getInt('ie_id');
       
			$_GET['noredirect'] = 1;
			$_POST['noredirect'] = 1;
			$_REQUEST['noredirect'] = 1;

			$db = JFactory::getDBO();
			
			$query = $db->getQuery(true);
			
			$query->select('*');
			
			$query->from('#__jshopping_import_export');
			
			$query->where('steptime > 0 AND (endstart + steptime < '.$db->quote(time()).')');
			
			if($ie_alias)
				$query->where("alias = ".$db->quote($ie_alias));
			
			if($ie_id)
				$query->where("id = ".$db->quote($ie_id));
			
			$query->order('id');
			
			$db->setQuery($query);
			$list = $db->loadObjectList();
			
			if(!$list) {
				print sprintf('There are no one selected Joomshopping Import/Export plugins according to the requested data!');
				die();
			}

			foreach($list as $ie){
				$alias = $ie->alias;
				if (!file_exists(JPATH_COMPONENT_ADMINISTRATOR."/importexport/".$alias."/".$alias.".php")){
					print sprintf(_JSHOP_ERROR_FILE_NOT_EXIST, "/importexport/".$alias."/".$alias.".php");
					return 0;
				}
				include_once(JPATH_COMPONENT_ADMINISTRATOR."/importexport/".$alias."/".$alias.".php");
				$classname  = 'Ie'.$alias;
				$controller = new $classname($ie->id);
				$controller->set('ie_id', $ie->id);
				$controller->set('alias', $alias);
				$controller->save();
				print $alias."\n";
			}
			die();
		}
	}        
}
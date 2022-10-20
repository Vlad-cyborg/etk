<?php
/**
* @package Joomla.JoomShopping
* @author Brooksus
* @website http://brooksite.ru/
* @email admin@brooksite.ru
* @copyright Copyright by Brooksus. All rights reserved.
* @version 4.0.6
* @license The MIT License (MIT); See \plugins\jshoppingproducts\compare\license.txt
**/
defined('_JEXEC') or die;
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
	$VersionAddon	= "4.0.6";
	$VersionJSh		= "3.14";
	$AddonName		= "Jshopping - Compare Ajax";
	$AddonAlias		= "compare";
	$PluginDir		= "jshoppingproducts";

	$DataBase = JFactory::getDBO();
		$DataBase->setQuery("DELETE FROM `#__extensions` WHERE `element` = \"".$AddonAlias."\" AND `folder` = \"".$PluginDir."\"");
		$DataBase->query();
		JFolder::Delete(JPATH_ROOT."/plugins/".$PluginDir."/".$AddonAlias);
	
	JFile::Delete(JPATH_ROOT."/administrator/components/com_jshopping/install/addon_".$AddonAlias."_uninstall.php");
?>
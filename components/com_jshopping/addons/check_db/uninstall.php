<?php
	defined('_JEXEC') or die('Restricted access');
	$db = JFactory::getDbo();
	
	$name = "Addon check the availability of the database tables and fields";
	$element = "check_db";
	
	//delete plygin
	$db->setQuery("DELETE FROM `#__extensions` WHERE `element` = '".$element."' AND `folder` = 'jshoppingmenu' AND `type` = 'plugin'");
	$db->query();
	
	// delete folder
	jimport('joomla.filesystem.folder');
	foreach(array(
		'administrator/components/com_jshopping/views/'.$element.'/',
		'administrator/components/com_jshopping/lang/'.$element.'/',
		'components/com_jshopping/addons/'.$element.'/',
		'components/com_jshopping/files/'.$element.'/',
		'plugins/jshoppingmenu/'.$element.'/'
	) as $folder){JFolder::delete(JPATH_ROOT.'/'.$folder);}
	
	// delete file
	jimport('joomla.filesystem.file');
	foreach(array(
		'administrator/components/com_jshopping/controllers/'.$element.'.php',
		'administrator/components/com_jshopping/models/'.$element.'.php'
	) as $file){JFile::delete(JPATH_ROOT.'/'.$file);}
?>

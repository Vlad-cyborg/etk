<?php
    /*
    * @version      1.0.0 07.12.2015
    * @author       MAXXmarketing GmbH
    * @package      addon_second_short_description_for_category
    * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
    * @license      GNU/GPL
    */
    defined('_JEXEC') or die('Restricted access');
    
	$db = JFactory::getDbo();
	
	$db->setQuery("DELETE FROM `#__extensions` WHERE element = 'second_short_description_for_category' AND folder = 'jshoppingproducts' AND `type` = 'plugin'");
	$db->query();
	
	$db->setQuery("DELETE FROM `#__extensions` WHERE element = 'second_short_description_for_category' AND folder = 'jshoppingcheckout' AND `type` = 'plugin'");
	$db->query();
	
	jimport('joomla.filesystem.folder');
	foreach(array(
        'components/com_jshopping/addons/second_short_description_for_category/',
        'components/com_jshopping/lang/addon_second_short_description_for_category/',
        'plugins/jshoppingadmin/second_short_description_for_category/',
        'plugins/jshoppingproducts/second_short_description_for_category/'
    ) as $folder){JFolder::delete(JPATH_ROOT.'/'.$folder);}
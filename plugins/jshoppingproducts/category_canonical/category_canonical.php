<?php
defined('_JEXEC') or die('Restricted access');

class plgJshoppingProductsCategory_canonical extends JPlugin {

    function onAfterLoadCategory(&$category, &$user) {
        $document = JFactory::getDocument();
		$uri = JURI::getInstance();        
        $liveurlhost = $uri->toString(array("scheme",'host', 'port'));
        $url = $liveurlhost.SEFLink('index.php?option=com_jshopping&controller=category&task=view&category_id='.$category->category_id);
        $document->addCustomTag('<link rel="canonical" href="'.$url.'"/>');        
    }
    
}
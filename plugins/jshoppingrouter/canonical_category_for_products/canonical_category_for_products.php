<?php
/**
* @package Joomla
* @subpackage JoomShopping
* @author Dmitry Stashenko
* @website http://nevigen.com/
* @email support@nevigen.com
* @copyright Copyright © Nevigen.com. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @license agreement http://nevigen.com/license-agreement.html
**/

defined( '_JEXEC' ) or die;

class plgJshoppingRouterCanonical_Category_For_Products extends JPlugin {

	function onBeforeBuildRoute(&$query, &$segments) {
		if (isset($query['controller'])){
			$controller = $query['controller'];
		}else{
			$controller = '';
		}
		if ($controller=="product" && $query['task']=="view" && $query['category_id'] && $query['product_id']){
			$product_id = $query['product_id'];
			$product = JTable::getInstance('product', 'jshop');
			$product->load($product_id);
			if ($product->product_id) {
				$category_id = $product->getCategory();
				if ($category_id) {
					$query['category_id'] = $category_id;
				}
			}
		}
	}

	// function onBeforeParseRoute(&$vars, &$segments) {
		// if ($segments[0] == 'checkout') return;
		// $menu = JFactory::getApplication()->getMenu();
		// $menuItem = $menu->getActive();
		// if ($menuItem->query['controller']=="quickcheckout"){
			// $segments[1] = $segments[0];
			// $segments[0] = "quickcheckout";
		// }
	// }

}
<?php
/**
* @package Joomla
* @subpackage JoomShopping
* @author Nevigen.com
* @website https://nevigen.com/
* @email support@nevigen.com
* @copyright Copyright © Nevigen.com. All rights reserved.
* @license Proprietary. Copyrighted Commercial Software
* @license agreement https://nevigen.com/license-agreement.html
**/

defined('_JEXEC') or die;

class ComielHelperFilter {

    public static function setFilter(JDatabaseQuery $query, $categories = [], $logicCondition = 'OR', $manufacturers = []) {
        $db = JFactory::getDbo();
        $whereFilter = null;
        $categories = array_filter($categories);
        $manufacturers = array_filter($manufacturers);
        if ($categories) {
            $query->leftJoin($db->qn('#__jshopping_products_to_categories', 'p2c') . ' USING(' . $db->qn('product_id') . ')');
            $whereFilter = $db->qn('p2c.category_id') . ' IN(' . implode(',', $categories) . ')';
        }
        if ($categories && $manufacturers) {
            $whereFilter .= ' ' . $logicCondition . ' ';
        }
        if ($manufacturers) {
            $whereFilter .= $db->qn('product_manufacturer_id') . ' IN(' . implode(',', $manufacturers) . ')';
        }
        if ($whereFilter) {
            $query->where('(' . $whereFilter . ')');
        }
    }

}
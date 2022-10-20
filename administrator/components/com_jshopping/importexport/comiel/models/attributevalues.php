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

class ComielModelAttributeValues extends ComielModels {

    public function getTable($name = 'attributValue', $prefix = 'jshop', $options = []) {
        return parent::getTable($name, $prefix, $options);
    }

    protected function beforeCreate($jshopTable) {
        $jshopTable->set('value_ordering', $jshopTable->getNextOrder($jshopTable->getDbo()->qn('attr_id') . ' = ' . $jshopTable->getDbo()->q($jshopTable->get('attr_id'))));
        parent::beforeCreate($jshopTable);
    }

}
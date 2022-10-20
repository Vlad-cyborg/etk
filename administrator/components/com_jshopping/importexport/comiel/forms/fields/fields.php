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

class JFormFieldFields extends JFormFieldList {

    protected function getOptions() {
        $options = [];

        foreach (IEComiel::FIELDS as $fieldAlias => $fieldName) {
            $options[] = ['value' => $fieldAlias, 'text' => JText::_($fieldName),];
        }
        $tableAddon = JTable::getInstance('addon', 'jshop');
        $tableAddon->loadAlias('addon_bonus_system');
        if ($tableAddon->get($tableAddon->getKeyName())) {
            $options[] = ['bonus_add' => 'Bonus +', 'bonus_sub' => 'Bonus -',];
        }

        $options = array_merge(parent::getOptions(), $options);
        return $options;
    }

}
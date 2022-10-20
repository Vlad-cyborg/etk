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

class JFormFieldManufacturers extends JFormFieldList {

    protected function getOptions() {
        $options = [];
        $manufacturers = JSFactory::getModel('manufacturers');
        foreach ($manufacturers->getAllManufacturers(0, 'ordering', 'asc') as $manufacturer) {
            $options[] = ['value' => $manufacturer->manufacturer_id, 'text' => $manufacturer->name,];
        }
        $options = array_merge(parent::getOptions(), $options);
        return $options;
    }

}
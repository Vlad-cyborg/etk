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

class JFormFieldCategories extends JFormFieldList {

    protected function getOptions() {
        $options = [];
        foreach (buildTreeCategory(0, 1) as $category) {
            $options[] = ['value' => $category->category_id, 'text' => $category->name,];
        }
        $options = array_merge(parent::getOptions(), $options);
        return $options;
    }

}
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

class ComielModelAttributes extends ComielModels {

    public function addImages($productId, array $images = []) {
        foreach ($images as $image) {
            $jshopImage = $this->getTable('image');
            $jshopImage->bind([
                'product_id' => $productId,
                'image_name' => $image,
                'ordering' => $jshopImage->getNextOrder($jshopImage->getDbo()->qn('product_id') . ' = ' . $jshopImage->getDbo()->q($productId)),
            ]);
            if (!$jshopImage->store()) {
                continue;
            }
        }
    }

    public function getTable($name = 'attribut', $prefix = 'jshop', $options = []) {
        return parent::getTable($name, $prefix, $options);
    }

    protected function afterCreate($jshopTable) {
        $jshopTable->addNewFieldProductsAttr();
        parent::afterCreate($jshopTable);
    }

    protected function beforeCreate($jshopTable) {
        $jshopTable->set('attr_ordering', $jshopTable->getNextOrder($jshopTable->getDbo()->qn('group') . ' = ' . $jshopTable->getDbo()->q(0)));
        parent::beforeCreate($jshopTable);
    }

}
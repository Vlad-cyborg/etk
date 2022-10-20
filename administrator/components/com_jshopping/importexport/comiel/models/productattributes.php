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

class ComielModelProductAttributes extends ComielModels {

    protected $images;

    public function addProductAttribute(array $attributeData, $productId = null) {
        if ($productId !== null) {
            $attributeData['product_id'] = $productId;
        }
        $jshopProductAttribute = $this->getTable();
        end($attributeData);
        if ($jshopProductAttribute->get(key($attributeData), false) === false) {
            $jshopProductAttribute->getFields(true);
            $jshopProductAttribute = $this->getTable();
        }
        $jshopProductAttribute->bind($attributeData);
        if (!$jshopProductAttribute->store()) {
            throw new RuntimeException(_JSHOP_ERROR_SAVE_DATABASE);
        }

        return $jshopProductAttribute;
    }

    public function getTable($name = 'productAttribut', $prefix = 'jshop', $options = []) {
        return parent::getTable($name, $prefix, $options);
    }

    public function images() {
        if ($this->images === null || !is_array($this->images)) {
            $this->images = [];
        }

        return $this->images;
    }

    public function parseAttributeDependence(array $attributeArray = [], $productId) {
        if (!$attributeArray) {
            return array();
        }
        $data = [
            'product_id' => $productId,
            'buy_price' => 0,
            'price' => 0,
            'old_price' => 0,
            'count' => 0,
            'ean' => '',
            'manufacturer_code' => '',
            'weight' => 0,
            'weight_volume_units' => 0,
            'ext_attribute_product_id' => 0,
        ];
        $comielAttributes = ComielModels::getInstance('attributes');
        $comielAttributeValues = ComielModels::getInstance('attributeValues');
        $combinations = [];
        $this->images = [];
		$updateAttributes = array();
        foreach ($attributeArray as $attributePairs) {
            list($attributeAlias, $attributeValues) = ComielHelper::parseLine(str_replace('\;', ';', $attributePairs), ':');
            if ($attributeAlias === null || $attributeAlias === '' || $attributeValues === null || $attributeValues === '') {
                continue;
            }
			$attributeAlias = str_replace(array('\:', '\,'), array(':', ','), $attributeAlias);
			$attributeValues = str_replace('\:', ':', $attributeValues);
            switch ($attributeAlias) {
                case _JSHOP_PRODUCT_BUY_PRICE:
                case 'buy_price':
                    $data['buy_price'] = (float)str_replace(',', '.', $attributeValues);
                    break;
                case _JSHOP_PRICE:
                case 'price':
                    $data['price'] = (float)str_replace(',', '.', $attributeValues);
                    break;
                case _JSHOP_OLD_PRICE:
                case 'old_price':
                    $data['old_price'] = (float)str_replace(',', '.', $attributeValues);
                    break;
                case _JSHOP_QUANTITY:
                case 'qty':
                case 'count':
                    $data['count'] = (float)str_replace(',', '.', $attributeValues);
                    break;
                case _JSHOP_EAN:
                case 'ean':
                case (JText::_('COMIEL_PRODUCT_EAN')):
                    $data['ean'] = (string)$attributeValues;
                    break;
                case _JSHOP_MANUFACTURER_CODE:
                case 'manufacturer_code':
                    $data['manufacturer_code'] = (string)$attributeValues;
                    break;
                case _JSHOP_PRODUCT_WEIGHT:
                case _JSHOP_WEIGHT:
                case 'weight':
                    $data['weight'] = (float)str_replace(',', '.', $attributeValues);
                    break;
                case _JSHOP_BASIC_PRICE:
                case 'basic_price':
                    $data['weight_volume_units'] = (float)str_replace(',', '.', $attributeValues);
                    break;
                case _JSHOP_IMAGE:
                case 'image':
                    $this->images = ComielHelper::parseLine($attributeValues, ',');
					foreach ($this->images as $key=>$image) {
						$this->images[$key] = str_replace('\,', ',', $image);
					}
                    break;
                default:
                    if (!$jshopAttribute = $comielAttributes->findOne([self::$lang->get('name') => $attributeAlias], null, ['independent' => 0, 'allcats' => 1, 'cats' => serialize([])])) {
                        continue;
                    }
                    $attributeId = (int)$jshopAttribute->get($jshopAttribute->getKeyName());
					$updateAttributes[] = $attributeId;
                    $combination = [];
                    foreach (ComielHelper::parseLine($attributeValues, ',') as $attributeValue) {
						$attributeValue = str_replace('\,', ',', $attributeValue);
                        if (!$jshopAttributeValue = $comielAttributeValues->findOne([self::$lang->get('name') => $attributeValue, 'attr_id' => $attributeId])) {
                            continue;
                        }
                        $attributeValueId = (int)$jshopAttributeValue->get($jshopAttributeValue->getKeyName());
                        $combination[] = ['attributeId' => $attributeId, 'attributeValueId' => $attributeValueId];
                    }
                    $combinations[] = $combination;
                    unset($combination);
                    break;
            }
        }
        unset($jshopAttribute, $jshopAttributeValue, $attributeArray, $attributePairs, $attributeAlias, $attributeValues, $attributeValue, $attributeId, $attributeValueId, $combination);
        $combinations = $this->generateVariation($combinations);
        $count = 0;
        foreach ($combinations as $combination) {
            $extAttributeProductId = 0;
            if ($this->images) {
				reset($this->images);
                $jshopProduct = $this->getTable('product');
                $jshopProduct->bind([
                    'parent_id' => $productId,
                    'image' => current($this->images),
                ]);
                if ($jshopProduct->store()) {
                    $extAttributeProductId = $jshopProduct->get($jshopProduct->getKeyName());
                    $comielAttributes->addImages($extAttributeProductId, $this->images);
                }
                unset($jshopProduct);
            }
            $attributeData = $data;
            $attributeData['ext_attribute_product_id'] = $extAttributeProductId;
            foreach ($combination as $value) {
                $attributeData['attr_' . $value['attributeId']] = $value['attributeValueId'];
            }
            try {
                $jshopProductAttribute = $this->addProductAttribute($attributeData);
                $count += (int)$jshopProductAttribute->get('count');
            } catch (Exception $e) {

            }
        }

        return $updateAttributes;
    }

    protected function generateVariation(array $attributes, $i = 0) {
        $result = [];
        if ($i < count($attributes)) {
            $variations = $this->generateVariation($attributes, $i + 1);
            for ($j = 0; $j < count($attributes[$i]); $j++) {
                if ($variations) {
                    foreach ($variations as $variation) {
                        $result[] = array_merge([$attributes[$i][$j]], $variation);
                    }
                } else {
                    $result[] = [$attributes[$i][$j]];
                }
            }
        }

        return $result;
    }

}
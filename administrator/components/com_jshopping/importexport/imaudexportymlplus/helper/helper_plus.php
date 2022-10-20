<?php
defined( '_JEXEC' ) or die();

class ProductData {
	var $catId = 0;
	var $name = '';
	var $price = 0;
	var $qty = 0;
}

class ImaudExportYMLPlusHelper extends IeController {


	/* Get unit title from attribute name
	** Example: Размер (EU)
	*/
	public static function getUnitFromAttrName($attrName, $attrValue = '') {
		global $unitsList;
		preg_match('/\((.+)\)/', $attrName, $m);
		if(isset($m[1]) && (in_array($m[1], $unitsList))) {
			$res = new stdClass();
			$res->unit = $m[1];
			$res->name = trim(str_replace('('.$m[1].')','',$attrName));		
		} elseif(!isset($m[1])) {
			$unit_pos = JString::strrpos($attrName, ',');
			if($unit_pos !== false) {
				$unit = trim(JString::substr($attrName, $unit_pos + 1));
				if(in_array($unit, $unitsList)) {
					$res = new stdClass();
					$res->unit = $unit;
					$res->name = trim(JString::substr($attrName, 0, $unit_pos));		
				}
			}
		} else {
			$res = false;
		}
		if(!$res && (JString::strtolower($attrName) == 'размер') && $attrValue) {
			$res = new stdClass();
			$res->unit = ($attrValue <= 12) ? 'EU' : ($attrValue < 80 ? 'RU' : 'Height');
			$res->name = trim($attrName);
		}
		return $res;
	}

	/* Get unit for attribute or customfield from custom table
	** Params:
	** 1) Attribute_ID
	** 2) Offer_Category
	** 3) Is attribute (A) / Is customfield (C)
	*/
	public static function getUnitFromTable($attrID, $offerCategory, $kind = '') {
		global $attr_name, $custom_units;
		
		if(!$custom_units) return false;
		
		$category = (array)$offerCategory;
		$categoryID = $category[0];
		$attrIdx = $kind . $attrID;
		if(isset($custom_units->$attrIdx)) {
			$attrOptions = $custom_units->$attrIdx;
			$unit = isset($attrOptions->$categoryID) ? $attrOptions->$categoryID : '';
			$name = isset($attr_name[$attrID]) ? $attr_name[$attrID] : '';
			if($unit && $name) {
				$res = new stdClass();
				$res->unit = $unit;
				$res->name = $name;
			}
		} else {
			$res = false;
		}
		return $res;
	}
	
	public static function getAllCombAttrs2($attributes = array()) {
		global $attr_name;
		if(empty($attributes) || empty($attr_name)) return false;
		$allAttrs = array();
		foreach($attr_name as $id => $value) {
			$i = 0;
			foreach($attributes as $pa) {
				if($pa->attr_id != $id) continue;
				$allAttrs[$id][$i] = $pa;
				$i++;
			}
		}
		if(empty($allAttrs)) return false;
		
		$res = array();
		
		// start all indexes (set to null)
		$cnt = array();
		foreach($allAttrs as $root => $row) {
			$cnt[$root] = count($row);
		}
		foreach($cnt as $k => $c) {
			$idx[$k] = 0;
		}

		// build array of index set for each attributes combinations
		while($idx) {
			$set = array();
			foreach($idx as $attr_id => $value_id) {
				$set[$attr_id] = $allAttrs[$attr_id][$value_id];
			}
			$res[] = $set;
			$idx = self::getNextSet($idx, $cnt);
		}		
		return $res;
	}

	// get next index set or false if it were last set
	static function getNextSet($currentIdx, $counters) {
		foreach($counters as $i => $c) {
			if($currentIdx[$i] < $c-1) {
				$currentIdx[$i] = self::getNextIdx($i, $currentIdx[$i], $counters);
				if($currentIdx[$i]) break;
			} else {
				$currentIdx[$i] = 0;
			}
		}
		$r = 0;
		foreach($currentIdx as $c) {
			$r += $c;
		}
		if($r) {
			return $currentIdx;
		} else {
			return false; // all indexes are null's - end index loop
		}
	}

	// get next counter index or null if it were last index
	static function getNextIdx($key, $value, $counters) {
		if($value < $counters[$key]-1) {
			$value++;
		} else {
			$value = 0;
		}
		return $value;
	}
	
	/* Get Unit title from parameter value
	** Example: 5 kg, 213 m
	*/
	public static function getUnit(&$value) {
		global $unitsList;

		$tmpvalue = trim($value);
		if(!preg_match('/^\d/', $tmpvalue)) return;

		$value_length = JString::strlen($tmpvalue);
		$tmpvalue = JString::strtolower($tmpvalue);
		foreach($unitsList as $unit) {
			$unit_length = JString::strlen($unit);
			$unit_envlength = $unit_length + 2;
			if($value_length < $unit_envlength) continue;
			$pos = JString::strpos($tmpvalue, ' '.$unit, 1);
			if($pos !== false) {
				$subvalue = JString::substr($tmpvalue, $pos+1);
				if(JString::strlen($subvalue) <= $unit_envlength) {
					$value = JString::substr(trim($value), 0, $pos);
					$res = new stdClass();
					$res->unit = $unit;
					return $res;
				}
			}
		}
		return false;
	}

	public static function getProductData($product) {

		global $ie_params, $ie_plugin_trigger, $db, $param_trigcat, $cat_list, $exist_only, $taxEnabled, $taxes,
			$jshopConfig, $lang, $router, $sefStatus, $shop_item_id, $acurrencies, $main_currency_code_iso, $notstock, $show_qty,
			$countries, $prods_included, $attr_name, $attr_values, $units, $unitsList, $weight_unit, $is_weight_kg, $is_weight_g,
			$delivtimes, $delivery_free, $deliv_options_global, $single_market_category, $market_category, $categories,
			$extrafields, $xml, $offers, $imagefield, $imliveurlhost, $base_url, $custom_units, $efv_separator;

		$productId = $product->product_id;
		if(!$productId) return false;
			
		$categories_table = "#__jshopping_categories";
		$products_table		= "#__jshopping_products";
		$products_cat		= "#__jshopping_products_to_categories";
		$extra_field_products	= "#__jshopping_products_to_extra_fields";
		$extra_field_values	= "#__jshopping_products_extra_field_values";

		$cpa_values_yes = explode(',' , str_replace(' ', '', _JSHOP_IMDEXPORTYML_CPAVALUES_YES));
		$cpa_values_no  = explode(',' , str_replace(' ', '', _JSHOP_IMDEXPORTYML_CPAVALUES_NO));
		
		$product_extrafields = array();
		if($extrafields) {
			$product_extrafields = $product->getExtraFields(0);
			foreach($product_extrafields as $id => $e) {
				if(!in_array($e['id'], $extrafields)) unset($product_extrafields[$id]); 
			}
		}
		
		if (in_array($product->hesh, $prods_included)) return false; // пропускаем товар, если уже обработан такой id

		// и пропускаем товар, если этот образец товара привязан не к той категории
		$query = $db->getQuery(true);
		$query->select('c.category_id AS cid')
					->from($products_cat.' AS pc')
					->leftJoin($categories_table.' AS c ON c.category_id = pc.category_id')
					->where('pc.product_id='.$db->Quote($productId))
					->where('c.category_publish='.$db->Quote('1'));
		if ($param_trigcat && $cat_list) $query->where('c.category_id ' . (($param_trigcat == 2) ? 'NOT ' : '') .'IN ('.$cat_list.')');
		$query->order('c.ordering');
		$db->setQuery($query);
		$row['cid'] = $db->loadColumn();
		if(!$row['cid']) return false;

		// все характеристики (extra_field_n)

		$extrafieldsvalues = array();	
		$customfieldsvalues = array();
		
		foreach($product_extrafields as $id => $info) {
			if (!$info['name']) continue; // не заполнено название характеристики товара!

			$prm = htmlspecialchars($info['value']);
		
			if ($prm) {
				$ef_name = JString::strtolower($info['name']);
				$ef_custom = false;
	
				foreach($ie_params['custom_xml_data'] as $i => $e) {
					if($id != $e->id) continue;
					$customfieldsvalues[$i] = $prm;
					$ef_custom = true;
				}
				
				if(!$ef_custom) $extrafieldsvalues[$ef_name] = $prm;
			}
		}
		
		$cpa_value = 0;
		
		if(in_array($ie_params['cpa'], array(3,4))) {
			
			// Set cpa default value: CPA ON = 0, CPA OFF = 1
			$cpa_value = (int)($ie_params['cpa'] == 4);
			
			if(isset($extrafieldsvalues['cpa'])) {
				$ef = JString::strtolower($extrafieldsvalues['cpa']);
				$ef = str_replace(' ', '', $ef);
				if(in_array($ef, $cpa_values_yes, true)) {
					$cpa_value = 1;
				} elseif(in_array($ef, $cpa_values_no, true)) {
					$cpa_value = 0;
				}
			}
			
			if(($ie_params['cpa'] == 4) && ($cpa_value == 0)) return false;
			if(($ie_params['cpa'] == 3) && ($cpa_value != 1)) return false;
		}
		
		unset($extrafieldsvalues['cpa']);
		
		// maybe use that?  $product->updateOtherPricesIncludeAllFactors();
		$prod_price = isset($product->product_price) ? $product->product_price : 0;
		$old_price =  isset($product->product_old_price) ? $product->product_old_price : 0;
		$min_price =  isset($product->min_price) ? $product->min_price : 0;
		$pricevalue = 0;
		
		$offer_id = $product->offer_id.$product->id_v;

		if($ie_params['clothes'] && $product->attrib_set) { // есть данные из зависимых атрибутов товара
			
			$attr_set = $product->attrib_set;
			$attr_set_values = $attr_set->values;
			$pricevalue = $attr_set->price;

			if($attr_set->old_price > 0) $old_price = $attr_set->old_price;
			if($ie_params['offerid'] == 1 && isset($attr_set->ean) && $attr_set->ean) $offer_id = $attr_set->ean;
			
		} else {
					
			if(class_exists('plgJshoppingProductsUser_group_product_price') && $product->userGroupId) {
				$query = $db->getQuery(true);
				$query->select('price')
					->from('#__jshopping_products_prices_group')
					->where('product_id = '.$db->quote($productId))
					->where('group_id = '.$db->quote($product->userGroupId))
					->where('price > 0');
				$db->setQuery($query);
				$pricevalue = $db->loadResult();
			}

			if(!$pricevalue) {
				if ($ie_params['price']) {
					$pricevalue = ($min_price>0) ? $min_price : $prod_price;
				} else {
					$pricevalue = ($prod_price>0) ? $prod_price : $min_price;
				}
			}
		}
		
		if ($ie_params['main_currency'] && ($acurrencies[$product->currency_id]->currency_value)) {
			$pricevalue = $pricevalue / ($acurrencies[$product->currency_id]->currency_value);
			$old_price = $old_price / ($acurrencies[$product->currency_id]->currency_value);
		}
		
		if ($product->usergroup_discount) $pricevalue = $pricevalue * $product->usergroup_discount;
		
		$pricevalue = round($pricevalue, $jshopConfig->decimal_count);

		if($pricevalue <= (float)$ie_params['min_price']) return false; // Исключить товар стоимостью меньше заданной

		$old_price = round($old_price, $jshopConfig->decimal_count);
			
		$prods_included[] = $product->hesh; // добавили хеш-код товара товар в список обработанных

		$offer_id = preg_replace('/[^a-z\d]/ui', '', $offer_id); // leave only a digits and letters

		if(isset($attr_set)) {
			$prod_qty = (int)$attr_set->count;
		} else {
			$qty_in_stock = getDataProductQtyInStock($product);
			$prod_qty = $qty_in_stock['unlimited'] ? 999999 : (int)$qty_in_stock['qty'];
		}
			
		if(isset($extrafieldsvalues['available'])) {
			$available = ImaudExportYMLHelper::getBooleanText($extrafieldsvalues['available']);
		} elseif($ie_params['preorder'] || ($delivtimes && $product->delivery_times_id)) {
			$available = 'false';
			// товар под заказ!
		} elseif($delivtimes && $product->delivery_times_id) {
			$available = 'false';
		} elseif($notstock || ($prod_qty >= (int)$ie_params['min_quantity']) || $qty_in_stock['unlimited']) {
			$available = 'true';
		} elseif(!$notstock && $ie_params['not_in_stock'] && ($prod_qty < (int)$ie_params['min_quantity'])) {
			// товара нет в наличии: особый случай - available без значения
			$available = '*';
		} else {
			// товар доступен на заказ
			$available = 'false';
		}
		unset($extrafieldsvalues['available']);

		if($exist_only && ($available == 'false')) return false;
		
		/* -----------P-L-U-G-I-N-S---------------------------------------- */
		if(isset($ie_plugin_trigger['onStartOffer'])) {
			foreach($ie_plugin_trigger['onStartOffer'] as $plugin) {
				require($plugin->file);
			}
		}
		/* -----end-of-P-L-U-G-I-N-S--------------------------------------- */
			
		$offer = $offers->appendChild($xml->createElement('offer'));
		$offer->setAttribute('id', $offer_id);
		// $offer->setAttribute('quantity', ceil($prod_qty));
				
		if(isset($product->groupId) && $product->groupId) {
			$product->groupId = preg_replace('/[^a-z\d]/ui', '', $product->groupId); // leave only a digits and letters
			$offer->setAttribute('group_id',$product->groupId);
		}
			
		if(strlen($available)) $offer->setAttribute('available', $available);

		$link = "index.php?option=com_jshopping&controller=product&task=view&category_id=".$row['cid'][0]."&product_id=".$productId.'&Itemid='.$shop_item_id;
		$prod_url = '';

		// Если в ссылках с мультиязычностью проблемы, см выгрузку для Google Multilanguage::isEnabled

		// Try build product page SEF-url from link:
		$prod_url = getFullUrlSefLink($link, 1);

		// Remove all path from URL, leave only the product alias?
		if(isset($ie_params['supersef']) && $ie_params['supersef']) {
			if(strstr($prod_url, '/product/view/') == false) $prod_url = $imliveurlhost.'/'.basename($prod_url);
		}

		$prod_url = str_replace($imliveurlhost, ($ie_params['nodomainurl'] ? '' : $base_url), $prod_url);

		// $prod_url = str_replace( '/component/jshopping/', '/', $prod_url);
		if(isset($ie_params['transcode']) && $ie_params['transcode']) $prod_url = str_replace('%2F', '/', rawurlencode($prod_url));

		// Create absolute url if SEF url cannot be retrieved:
		if (empty($prod_url) OR strlen($prod_url)<2) $prod_url = ($ie_params['nodomainurl'] ? '' : $base_url) . SEFLink('/'.$link.'&Itemid='.$shop_item_id,0,1);

		// And add product EAN if it's need
		// if(isset($attr_set) && $attr_set->ean && ($product->getEan() != $attr_set->ean)) $prod_url .= (stristr($prod_url, '?') ? '&' : '?') . 'ean='.rawurlencode($attr_set->ean);
		
		if($ie_params['attr_in_url']) {
			
			$prod_url_attr = array();
			
			// зависимые атрибуты товара в ссылку
			if ($product->attrib_set && is_array($product->attrib_set->values)) {
				foreach($product->attrib_set->values as $attr_id => $attr_value_id) {
					if($attr_value_id) $prod_url_attr[] = 'attr['.$attr_id.']='.$attr_value_id;
				}
			}
			
			// независимые атрибуты в ссылку
			if ($product->attrib_set2) {
				foreach($product->attrib_set2 as $attr_id => $attr_data) {
					if($attr_data->attr_value_id) $prod_url_attr[] = 'attr['.$attr_id.']='.$attr_data->attr_value_id;
				}
			}
			
			if($prod_url_attr) $prod_url .= (stristr($prod_url, '?') ? '&' : '?') . implode('&', $prod_url_attr);
		}

		if ($ie_params['urltracking']) {
			$utm_campaign = ImaudExportYMLHelper::translit($market_category ? $market_category[count($market_category)-1] : $categories[$row['cid'][0]]->name);
			$prod_url .= strpos($prod_url, '?') ? '&' : '?'; // add '?' if URI hasn't attributes yet else add '&'
			$prod_url .= 'utm_source='.$ie_params['utm'].'&utm_campaign='.$utm_campaign.'&utm_term='.$offer_id;
		}
		
		$childXML = $offer->appendChild($xml->createElement('url'));
		$childXML->appendChild($xml->createTextNode($prod_url));

		$old_price = round($old_price, $jshopConfig->decimal_count);
		$childXML = $offer->appendChild($xml->createElement('price'));
		$childXML->appendChild($xml->createTextNode($pricevalue));

		if($ie_params['add_price']) {
			$query = $db->getQuery(true);
			$query->select('DISTINCT discount, product_quantity_start')
						->from('#__jshopping_products_prices')
						->where('product_id = '.$db->quote($productId))
						->order('product_quantity_start');
			$db->setQuery($query);
			$product_add_price = $db->loadObjectList();
			if($product_add_price) {
				$prices = $offer->appendChild($xml->createElement('prices'));
				foreach($product_add_price as $pap) {
					$price_qty = round($pricevalue * (1 - $pap->discount/100), $jshopConfig->decimal_count);
					$xmlChild = $prices->appendChild($xml->createElement('price'));
					$xmlEle = $xmlChild->appendChild($xml->createElement('value'));
					$xmlEle->appendChild($xml->createTextNode($price_qty));
					$xmlEle = $xmlChild->appendChild($xml->createElement('quantity'));
					$xmlEle->appendChild($xml->createTextNode($pap->product_quantity_start));			
				}
			}
		}

		if($ie_params['oldprice'] && $old_price>0 && $pricevalue>0) {
			$sale = ($old_price-$pricevalue)/$old_price; // скидка от единицы (не меньше 5% и не больше 95%)
			if($sale >= 0.05 && $sale <= 0.95) {
				$childXML = $offer->appendChild($xml->createElement('oldprice'));
				$childXML->appendChild($xml->createTextNode($old_price));
			}
		}

		if($jshopConfig->admin_show_product_bay_price && $ie_params['buy_price']) {
			$buy_price = round((float)$product->product_buy_price, $jshopConfig->decimal_count);		
			if($buy_price) {
				$childXML = $offer->appendChild($xml->createElement('buy_price'));
				$childXML->appendChild($xml->createTextNode($buy_price));
			}
		}

		if($ie_params['vat']) {
			$prod_vat = 'NO_VAT';
			if($taxEnabled && isset($taxes[$product->product_tax_id])) {
				$prod_vat = 'VAT_' . (string)round($taxes[$product->product_tax_id],0);
			}
			$childXML = $offer->appendChild($xml->createElement('vat'));
			$childXML->appendChild($xml->createTextNode($prod_vat));
		}

		if ($ie_params['main_currency']) {
			$codeISO = $main_currency_code_iso;
		} else {
			$codeISO = $acurrencies[$product->currency_id]->currency_code_iso;
			if(!$codeISO) $codeISO = $main_currency_code_iso;
		}
		$childXML = $offer->appendChild($xml->createElement('currencyId'));
		$childXML->appendChild($xml->createTextNode($codeISO));

		if($single_market_category) {
			$childXML = $offer->appendChild($xml->createElement('categoryId'));
			$childXML->appendChild($xml->createTextNode($single_market_category));
		} else {
			foreach($row['cid'] as $cid) {
				$childXML = $offer->appendChild($xml->createElement('categoryId'));
				$childXML->appendChild($xml->createTextNode($cid));
			}
		}

		if (isset($extrafieldsvalues['market_category']) && !$single_market_category) {
			$childXML = $offer->appendChild($xml->createElement('market_category'));
			$childXML->appendChild($xml->createTextNode($extrafieldsvalues['market_category']));
			unset($extrafieldsvalues['market_category']);
		}

		$product_images = array();
		
		// First try get the product dependent attributes images
		if ($product->attrib_set && isset($jshopConfig->use_extend_attribute_data) && $jshopConfig->use_extend_attribute_data) $product_images = self::getProductAttrImages($productId, $product->attrib_set->values);

		// Or get product general images if is empty the product dependent attributes images
		if(!$product_images) $product_images = $product->getImages();
		
		$i = 0;
		foreach($product_images as $image) {
			if($image->image_full) {
				$img_url = $jshopConfig->image_product_live_path.'/'.rawurlencode($image->image_full);
				$img_url = str_replace($imliveurlhost, $base_url, $img_url);
				$childXML = $offer->appendChild($xml->createElement('picture'));
				$childXML->appendChild($xml->createTextNode($img_url));	
			}
			if($ie_params['pictures']) {
				$i++;
				if($i >= (int)$ie_params['pictures']) break;
			}
		}
		
		if(isset($extrafieldsvalues['enable_auto_discounts']) && in_array(JString::strtolower($extrafieldsvalues['enable_auto_discounts']), array('true','да','1'))) {
			$childXML = $offer->appendChild($xml->createElement('enable_auto_discounts'));
			$childXML->appendChild($xml->createTextNode('true'));
			unset($extrafieldsvalues['enable_auto_discounts']);
		}

		if($ie_params['store']) {		
			if(isset($extrafieldsvalues['store'])) {
				$value = ($available === 'false') ? 'false' : ImaudExportYMLHelper::getBooleanText($extrafieldsvalues['store']);
				unset($extrafieldsvalues['store']);
			} elseif($ie_params['store'] == 1) {
				$value = $available;
			} elseif($ie_params['store'] == 2) {
				$value = 'false';
			} else {
				$value = NULL;
			}
			
			if(strlen($value)) {
				$childXML = $offer->appendChild($xml->createElement('store'));
				$childXML->appendChild($xml->createTextNode($value));
			}
		}

		if($ie_params['pickup']) {
			if(isset($extrafieldsvalues['pickup'])) {
				$value = ImaudExportYMLHelper::getBooleanText($extrafieldsvalues['pickup']);
				unset($extrafieldsvalues['pickup']);
			} elseif($ie_params['pickup'] == 1) {
				$value = 'true';
			} elseif($ie_params['pickup'] == 2) {
				$value = 'false';
			} else {
				$value = NULL;
			}
			if(strlen($value)) {
				$childXML = $offer->appendChild($xml->createElement('pickup'));
				$childXML->appendChild($xml->createTextNode($value));
			}
		}
			
		// Информация о доставке
		switch($ie_params['delivery']) {
			
			case 1: // доставка есть
			
				$value = isset($extrafieldsvalues['delivery']) ? ImaudExportYMLHelper::getBooleanText($extrafieldsvalues['delivery']) : 'true';
				if(strlen($value)) {
					$childXML = $offer->appendChild($xml->createElement('delivery'));
					$childXML->appendChild($xml->createTextNode($value));
				}
				if(((int)$ie_params['delivery'] == 1) && isset($extrafieldsvalues['delivery_options_cost']))
				{
					$deliv_options = ImaudExportYMLHelper::getDeliveryOptions( $extrafieldsvalues['delivery_options_cost'], $extrafieldsvalues['delivery_options_days'], $extrafieldsvalues['delivery_order_before']);
					if($deliv_options) {
						$xmldeliv = $offer->appendChild($xml->createElement('delivery-options'));
						for ($i = 0; $i < count($deliv_options->cost); $i++) {
							$childXML = $xmldeliv->appendChild($xml->createElement('option'));
							$childXML->setAttribute('cost', $deliv_options->cost[$i]);
							$childXML->setAttribute('days', $deliv_options->days[$i]);
							if($deliv_options->before[$i]) $childXML->setAttribute('order-before', $deliv_options->before[$i]);
						}
					}
				} elseif($deliv_options_global) {
			
					$deliv_cost = ($delivery_free && ($pricevalue >= $delivery_free)) ? 0 : $deliv_options_global->cost[0];
			
					if($delivtimes && $product->delivery_times_id) {
						$xmldeliv = $offer->appendChild($xml->createElement('delivery-options'));
						$childXML = $xmldeliv->appendChild($xml->createElement('option'));
						$childXML->setAttribute('cost', $deliv_cost);
						$childXML->setAttribute('days', $product->delivery_times_id);
				
					} elseif($deliv_cost == 0) {	
						$xmldeliv = $offer->appendChild($xml->createElement('delivery-options'));
						for ($i = 0; $i < count($deliv_options_global->cost); $i++) {
							$childXML = $xmldeliv->appendChild($xml->createElement('option'));
							$childXML->setAttribute('cost', 0);
							$childXML->setAttribute('days', $deliv_options_global->days[$i]);
							if($deliv_options_global->before[$i]) $childXML->setAttribute('order-before', $deliv_options_global->before[$i]);
						}
					}
				}
				break;
				
			case 2: // доставки нет
				$childXML = $offer->appendChild($xml->createElement('delivery'));
				$childXML->appendChild($xml->createTextNode("false"));
				break;
		}
		
		if(isset($extrafieldsvalues['delivery'])) unset($extrafieldsvalues['delivery']);
		if(isset($extrafieldsvalues['local_delivery_cost'])) unset($extrafieldsvalues['local_delivery_cost']);

		
		$vendor_model = false;
		$vendor = '';
		
		$prod_vendor_info = $product->getManufacturerInfo();
		if($prod_vendor_info) {
			$vendor = $prod_vendor_info->name;
			$cofo = JString::ucwords(JString::strtolower($vendor));
			$country_of_origin = (in_array($cofo, $countries)) ? $cofo : false; // Название прозводителя - страна!
			// сокращаем название производителя до первой запятой
			$prod_vendor = strtok($vendor,",");
			// проверяем, нет ли здесь страны-производителя
			$cofo = trim(str_replace(',', ' ', str_replace($prod_vendor, '', $vendor)));
			$cofo = JString::ucwords(JString::strtolower($cofo));
			if(strlen($cofo)>=2 && in_array($cofo, $countries)) $country_of_origin = $cofo;
		}
		
		$name_field = $lang->get("name");
		
		if($show_qty && $prod_qty >= 0) {
			switch((int)$ie_params['qty']) {
				case 1:
					$ie_params['qty_name'] = str_replace(' ','-',$ie_params['qty_name']);
					$offer->setAttribute($ie_params['qty_name'], $prod_qty);
					break;
				case 2:
					$childXML = $offer->appendChild($xml->createElement($ie_params['qty_name']));
					$childXML->appendChild($xml->createTextNode($prod_qty));
					break;
				case 3:
					$extrafieldsvalues[$ie_params['qty_name']] = $prod_qty;
					break;	
				case 4:
					$childXML = $offer->appendChild($xml->createElement('outlets'));
					$xmlChild = $childXML->appendChild($xml->createElement('outlet'));
					$xmlChild->setAttribute('id', '1');
					$xmlChild->setAttribute($ie_params['qty_name'] ? $ie_params['qty_name'] : 'instock', $prod_qty);
					unset($xmlChild);
				break;	
			}
		}
		
		switch ($ie_params['description']) {
			case 0:
				$description_field = $lang->get("short_description");
				break;
			case 3:
				$description_field = $lang->get("meta_description");
				break;		
			default:
				$description_field = $lang->get("description");	
		}
		
		$prod_description = $product->$description_field;

		if($ie_params['content_prepare']) {
			$prod_description = ImaudExportYMLHelper::contentPrepare($prod_description);
			$prod_description = str_replace('{second_description}','',$prod_description);
		}
		
		if($ie_params['schema'] && $vendor) {
			// если есть производитель, то схема vendor.model
			$offer->setAttribute('type','vendor.model');
			$vendor_model = true;
			if(isset($extrafieldsvalues['typeprefix'])) {
				$childXML = $offer->appendChild($xml->createElement('typePrefix'));
				$childXML->appendChild($xml->createTextNode($extrafieldsvalues['typeprefix']));
			}
		} else {
			$prod_name = htmlspecialchars(ucfirst($product->$name_field));
			$prod_name = JHtml::_('string.truncate', $prod_name, 250);
			// $prod_name = JString::ucfirst(JString::strtolower($prod_name));
			$xmlname = $offer->appendChild($xml->createElement('name'));
			$xmlname->appendChild($xml->createTextNode($prod_name));
		}
		
		unset($extrafieldsvalues['typeprefix']);
		
		if(!empty($prod_vendor)) {
			$childXML = $offer->appendChild($xml->createElement('vendor'));
			$childXML->appendChild($xml->createTextNode(htmlspecialchars($prod_vendor)));
		}
		
		$ean = '';
		switch($ie_params['vendorcode']) {
		case 1:
			$ean = isset($attr_set) ? $attr_set->ean : $product->product_ean;
			break;
		case 2:
			if(isset($product->manufacturer_code)) $ean = $product->manufacturer_code;
			if(isset($product->attrib_set->manufacturer_code) && $product->attrib_set->manufacturer_code) {
				$ean = $product->attrib_set->manufacturer_code;
			}
			break;
		case 3:
			$sd_name = $lang->get('short_description');
			$ean = htmlspecialchars($product->$sd_name);
			break;
		default:
			if(isset($extrafieldsvalues['vendorcode'])) {
				$ean = $extrafieldsvalues['vendorcode'];
				unset($extrafieldsvalues['vendorcode']);
			}
		}
		if(!empty($ean)) {
			$childXML = $offer->appendChild($xml->createElement('vendorCode'));
			$childXML->appendChild($xml->createTextNode($ean));
		}
			
		$barcode = '';

		switch($ie_params['barcode']) {
		case 1:
			$barcode = $product->product_ean;
			if(isset($product->attrib_set->ean) && $product->attrib_set->ean) $barcode = $product->attrib_set->ean;
			break;
		case 2:
			if(isset($product->manufacturer_code)) $barcode = $product->manufacturer_code;
			if(isset($product->attrib_set->manufacturer_code) && $product->attrib_set->manufacturer_code) $barcode = $product->attrib_set->manufacturer_code;
			break;
		case 3:
			$sd_name = $lang->get('short_description');
			$barcode = htmlspecialchars($product->$sd_name);
			if(!preg_match('/^[\d]*$/', $barcode)) $barcode = '';
			break;
		default:
			if(isset($extrafieldsvalues['barcode'])) {
				$barcode = $extrafieldsvalues['barcode'];
				unset($extrafieldsvalues['barcode']);
			}
		}
		if(!empty($barcode)) {
			$childXML = $offer->appendChild($xml->createElement('barcode'));
			$childXML->appendChild($xml->createTextNode($barcode));
		}
		
		if($vendor_model) {
			$prod_model = ImaudExportYMLHelper::clearVendorModel($product->$name_field, $prod_vendor);
			// $prod_model = isset($attr_set) ? $attr_set->ean : $product->product_ean;
			if(isset($extrafieldsvalues['model']) && $extrafieldsvalues['model']) {
				$prod_model = $extrafieldsvalues['model'];
				unset($extrafieldsvalues['model']);
			}
			$xmlmodel = $offer->appendChild($xml->createElement('model'));
			$xmlmodel->appendChild($xml->createTextNode($prod_model));
		}
		if(!empty($prod_description)) {
			$childXML = $offer->appendChild($xml->createElement('description'));
			if ($ie_params['description']== 2) {
				// оставляем в описании все html теги
				$childXML->appendChild($xml->createCDATASection($prod_description));
			} else {
				// убираем ненужные символы в строке описания товара
				$childXML->appendChild($xml->createTextNode(ImaudExportYMLHelper::clearProductDescription($prod_description)));
			}
		}

		$notes_text = array();
		
		if(($available == 'false') && isset($ie_params['sales_notes2']) && $ie_params['sales_notes2']) {
			$notes_text[] = $ie_params['sales_notes2'];
		} else {
			if(isset($extrafieldsvalues['sales_notes'])) {
				$notes_text[] = trim($extrafieldsvalues['sales_notes'],'.');
				unset($extrafieldsvalues['sales_notes']);
			} elseif($product->sales_notes) {
				$notes_text[] = $product->sales_notes;
			}
		}

		$notes_fulltext = ltrim(implode('. ', $notes_text),'. ');
		// $notes_fulltext = JHtml::_('string.truncate', trim($notes_fulltext), 50);
		
		if($notes_fulltext) {
			$childXML = $offer->appendChild($xml->createElement('sales_notes'));
			$childXML->appendChild($xml->createTextNode($notes_fulltext));
		}

		// Официальная гарантия на все товары - тег <manufacturer_warranty>
		if(isset($extrafieldsvalues['manufacturer_warranty'])) {
			$value = ImaudExportYMLHelper::getBooleanText($extrafieldsvalues['manufacturer_warranty']);
			// unset($extrafieldsvalues['manufacturer_warranty']);
		} elseif($ie_params['warranty']=='1') {
			$value = 'true';
		} else {
			$value = NULL;
		}
		if(strlen($value)) {
			$childXML = $offer->appendChild($xml->createElement('manufacturer_warranty'));
			$childXML->appendChild($xml->createTextNode($value));
		}

		if (isset( $extrafieldsvalues['country_of_origin']) && strlen($extrafieldsvalues['country_of_origin'])>=3 ) {
			$country_of_origin = $extrafieldsvalues['country_of_origin'];
			unset($extrafieldsvalues['country_of_origin']);
		}
		if($country_of_origin) {
			$childXML = $offer->appendChild($xml->createElement('country_of_origin'));
			$childXML->appendChild($xml->createTextNode(htmlspecialchars($country_of_origin)));
		}
		
		if($ie_params['custom_xml_data']) {
			foreach($ie_params['custom_xml_data'] as $i => $e) {
				$v = isset($customfieldsvalues[$i]) ? $customfieldsvalues[$i] : $e->default;
				if($v > ' ') {
					$childXML = $offer->appendChild($xml->createElement($e->name));
					$childXML->appendChild($xml->createTextNode($v));
					if($e->atr) $childXML->setAttribute($e->atr, $e->atr_value);
				}
			}
		}
		
		// вес товара

		if($ie_params['weight']) {

			$prod_weight = isset($product->product_weight) ? $product->product_weight : 0;

			if ( $attr_set->product_weight ) {
				$prod_weight = $attr_set->product_weight;
			} elseif ( isset($attr_set->weight_volume_units) && $attr_set->weight_volume_units ) {
				$prod_weight = $attr_set->weight_volume_units;
			}

			if ((float)$prod_weight) {
				// Weight from special field
				$xmlChild = $offer->appendChild($xml->createElement('weight'));		
				if($is_weight_gr || $is_weight_kg) {
					$product_weight_kg = $prod_weight;
					// Set weight gram to kg
					if($is_weight_gr) $product_weight_kg = $prod_weight / 1000;
					$xmlChild->appendChild($xml->createTextNode($product_weight_kg));
				} else {
					$xmlChild->appendChild($xml->createTextNode($prod_weight));
					if($weight_unit) $xmlChild->setAttribute('unit', $weight_unit);
				}
				
			// Or weight from extra fields
			} elseif($extrafieldsvalues['weight']>0) {
				
				$xmlChild = $offer->appendChild($xml->createElement('weight'));
				$xmlChild->appendChild($xml->createTextNode($extrafieldsvalues['weight']));
				unset($extrafieldsvalues['weight']);
			}
		}
		
		$prod_name_sfx = array();
		
		// Has product Dependent attributes?
		if ($product->attrib_set && is_array($product->attrib_set->values)) {
			foreach($product->attrib_set->values as $attr_id => $attr_value_id) {
				$unit = self::getUnitFromTable($attr_id, $row['cid'], 'A');
				$value = $attr_values[$attr_value_id];
				if(!$unit) $unit = self::getUnitFromAttrName($attr_name[$attr_id], $value);
				$childXML = $offer->appendChild($xml->createElement('param'));
				if($unit) {
					$childXML->setAttribute('name', $unit->name);
					$childXML->setAttribute('unit', $unit->unit);
					$value = JString::substr($value, 0, JString::strpos($value, " "));
				} else {
					$childXML->setAttribute('name', $attr_name[$attr_id]);
				}
				$childXML->appendChild($xml->createTextNode($value));
				if($ie_params['fullname']) {
					$prod_name_sfx[] = JString::strtolower($unit ? $unit->name : $attr_name[$attr_id]) . ' ' . $value;
				}
			}
		}

		// Has product Undependent attributes?
		if ($product->attrib_set2) {
			foreach($product->attrib_set2 as $attr_id => $attr_data) {
				if(!isset($attr_name[$attr_data->attr_id]) || !$attr_data->attr_value_id) continue;
				$unit = self::getUnitFromTable($attr_data->attr_id, $row['cid'], 'A');
				$value = $attr_values[$attr_data->attr_value_id];
				if(!$unit) $unit = self::getUnitFromAttrName($attr_name[$attr_id], $value);

				$inLine = false;
				
				if(!$ie_params['clothes'] && $ie_params['paramlist']) {
					$xmlParam = $offer->getElementsByTagName('param');
					if($xmlParam->length) {
						foreach($xmlParam as $xmlEle) {
							if($xmlEle->getAttribute('name') == $attr_name[$attr_data->attr_id]) {
								$xmlEle->nodeValue = (string)$xmlEle->nodeValue . $efv_separator. $value;
								$inLine = true;
								break;
							}
						}
					}
				}
				
				if(!$inLine) {
					$childXML = $offer->appendChild($xml->createElement('param'));
					if($unit) {
						$childXML->setAttribute('name', $unit->name);
						$childXML->setAttribute('unit', $unit->unit);
						$value = JString::substr($value, 0, JString::strpos($value, " "));
					} else {
						$childXML->setAttribute('name', $attr_name[$attr_data->attr_id]);
					}
					$childXML->appendChild($xml->createTextNode($value));
				}
				
				if(isset($product->groupId) && $product->groupId && $ie_params['fullname']) {
					$prod_name_sfx[] = JString::strtolower($unit ? $unit->name : $attr_name[$attr_data->attr_id]) . ' ' . $value;
				}
			}
		}
		
		if($prod_name_sfx) {
			$prod_name_sfx_text = ', ' . implode(', ', $prod_name_sfx);
			if($vendor_model) {
				$xmlmodel->appendChild($xml->createTextNode($prod_name_sfx_text));
			} else {
				$xmlname->appendChild($xml->createTextNode($prod_name_sfx_text));
			}
		}
			
		// Характеристики товара включены?
		if ($ie_params['param']) {

			while ($param_value = current($extrafieldsvalues)) {
				$unit = self::getUnitFromAttrName(key($extrafieldsvalues));
				
				if($ie_params['paramlist'] == 0) {
					$param_value_list = explode($jshopConfig->multi_charactiristic_separator, $param_value);
				} else {
					$param_value = str_replace($jshopConfig->multi_charactiristic_separator, $efv_separator, $param_value);
					$param_value_list = (array)$param_value;
				}
				
				foreach($param_value_list as $param_value) {
					$childXML = $offer->appendChild($xml->createElement('param'));
					if(!$unit) {
						$unit = self::getUnit($param_value);
						if($unit) $unit->name = JString::ucfirst(key($extrafieldsvalues));
					}
					if(!$unit && $custom_units) {
						$unit = self::getUnitFromTable(key($extrafieldsvalues), $row['cid']);
					}
					if($unit) {
						$childXML->setAttribute('name', $unit->name);
						$childXML->setAttribute('unit', $unit->unit);
						// $param_value = JString::substr($param_value, 0, JString::strpos($param_value, " "));
					} else {
						$childXML->setAttribute('name', JString::ucfirst(key($extrafieldsvalues)));
					}
					$childXML->appendChild($xml->createTextNode(htmlspecialchars($param_value)));
				}
				
				next($extrafieldsvalues);
			}
		}
		// end Характеристики товара
		
		if ($ie_params['cpa'] == 1 || $ie_params['cpa'] == 2) {
			$childXML = $offer->appendChild($xml->createElement('cpa'));
			$childXML->appendChild($xml->createTextNode($cpa_value));
		}
		
		/* -----------P-L-U-G-I-N-S---------------------------------------- */
		if(isset($ie_plugin_trigger['onEndOffer'])) {
			foreach($ie_plugin_trigger['onEndOffer'] as $plugin) {
				require($plugin->file);
			}
		}
		/* -----end-of-P-L-U-G-I-N-S--------------------------------------- */
		
		$tmpProduct = new ProductData;
		$tmpProduct->catId = $row['cid'];
		$tmpProduct->name = $vendor_model ? $prod_model : $prod_name;
		$tmpProduct->price = $pricevalue;
		$tmpProduct->qty = $prod_qty;
		
		return $tmpProduct;
	}
	
	public static function getProductAttrImages($productId, $attrs) {
		global $db;
		
		$where = array();

		if($attrs) {
			foreach($attrs as $ai => $av) $where[] = 'a.attr_'.$ai.'='.$db->quote($av);
		}

		$where_attr = implode(' AND ', $where);

		$query = $db->getQuery(true);
		$query->select('b.image_name')
					->from('#__jshopping_products_attr AS a')
					->join('left', '#__jshopping_products_images AS b ON a.ext_attribute_product_id = b.product_id')
					->where('a.product_id = '.$db->quote($productId))
					->where('a.ext_attribute_product_id > 0')
					->order('b.ordering');
		if($where_attr) $query->where($where_attr);
		$db->setQuery($query);
		try {
			$attr_pictures = $db->loadColumn();
		} catch (Exception $e) {
			$attr_pictures = array();
		}
		if($attr_pictures) {
			foreach($attr_pictures as $ap) {
				$img = new StdClass;
				$img->image_name = $ap;
				$img->image_full = 'full_'.$ap;
				$result[] = $img;
			}
			return $result;
		} else {
			return array();
		}
	}

}
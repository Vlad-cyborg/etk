<?php
defined( '_JEXEC' ) or die();

foreach($items as $productId):

	$product = JSFactory::getTable('product');
	$product->load($productId);
		
	$product->sales_notes = $sales_notes;
	$product->userGroupId = $userGroupId;
	$product->usergroup_discount = $usergroup_discount;
			
	$prod_all_attrs = array();
	$prod_attrs = array();
	$prod_attrs2 = array();

	if($attr_name) {
		$prod_all_attrs = $product->getAttributes();

		// Get an array of sets of dependent attributes for the current product:
		foreach($prod_all_attrs as $i => $attr_set) {
			$a = array();
			foreach($attr_name as $attr_id => $name) {
				$fieldname = 'attr_'.$attr_id;
				if(isset($attr_set->$fieldname) && $attr_set->$fieldname ) {
					$a[$attr_id] = (int)$attr_set->$fieldname; // ID of attribute value
				}
			}
			if($a) {
				$unique = true;
				foreach($prod_attrs as $pa) {
					if(!array_diff_assoc($a, $pa->values)) {
						$unique = false;
						break;
					}
				}
				if($unique) { 
					$prod_attrs[$i] = new StdClass;
					$prod_attrs[$i]->values = $a;
					$prod_attrs[$i]->price = $attr_set->price;
					$prod_attrs[$i]->old_price = $attr_set->old_price;
					$prod_attrs[$i]->count = $attr_set->count;
					$prod_attrs[$i]->ean = strip_tags($attr_set->ean);
					$prod_attrs[$i]->manufacturer_code = strip_tags($attr_set->manufacturer_code);
					$prod_attrs[$i]->product_weight = $attr_set->weight;
					$prod_attrs[$i]->weight_volume_units = $attr_set->weight_volume_units;
				}
			}
		}
			// And get an array of sets of independent attributes for the current product:
		$prod_all_attrs2 = $product->getAttributes2();
		if($prod_all_attrs2) $prod_attrs2 = ImaudExportYMLPlusHelper::getAllCombAttrs2($prod_all_attrs2);
	}

	$k = true;
	$product->offer_id = $productId;

	switch($ie_params['offerid']) {
		case 1:
			if(isset($product->product_ean)) $product->offer_id = $product->product_ean;
			break;
		case 2:
			if(isset($product->manufacturer_code)) $product->offer_id = $product->manufacturer_code;
			break;
	}
		
	if(!$product->offer_id) $product->offer_id = $productId;
	$productOpt = 1;
	$tmpProduct = array();
	$hasProdVario = (int)($prod_attrs || $prod_attrs2);
	
	$product->groupId = $ie_params['clothes'] ? $product->offer_id : 0;
	$product->id_v = '';
	$product->hesh = $product->offer_id;

	if($ie_params['clothes'] && $ie_params['attributes'] && $prod_attrs) {
		// This product has dependent attributes

		if($prod_attrs2) {
			// We need to add each set of dependent attributes for each set of independent

			foreach($prod_attrs2 as $i => $attr2data) {
				$product->attrib_set2 = $attr2data;
				foreach($prod_attrs as $s => $oneset) {
					$product->attrib_set = $oneset; 
					if($hasProdVario) {
						$product->id_v = 'v'.$productOpt;
						$product->hesh = $product->hesh.$product->id_v;
					}
					$serProduct = ImaudExportYMLPlusHelper::getProductData($product);
					if($serProduct) {
						$productOpt++;
						if($k) {
							$k = false;
							$tmpProduct[] = $serProduct;
						}
					}
				}
			}

		} else {
			// This product only has dependent attributes

			foreach($prod_attrs as $s => $oneset) {
				$product->attrib_set = $oneset; 
				$product->attrib_set2 = false;
				if($hasProdVario) {
					$product->id_v = 'v'.$productOpt;
					$product->hesh = $product->hesh.$product->id_v;
				}
				$serProduct = ImaudExportYMLPlusHelper::getProductData($product);
				if($serProduct) {
					$productOpt++;
					if($k) {
					$k = false;
					$tmpProduct[] = $serProduct;
					}
				}
			}
		}

	} elseif($ie_params['clothes'] && $ie_params['attributes'] && $prod_attrs2) {
		// This product only has independent attributes

		foreach($prod_attrs2 as $i => $attr2data) {
			$product->attrib_set = false;
			$product->attrib_set2 = $attr2data;
			if($hasProdVario) {
				$product->id_v = 'v'.$productOpt;
				$product->hesh = $product->hesh.$product->id_v;
			}
			$serProduct = ImaudExportYMLPlusHelper::getProductData($product);
			if($serProduct) {
				$productOpt++;
				if($k) {
				$k = false;
				$tmpProduct[] = $serProduct;
			}
			}
		}

	} else {
		// We don't need to create virtual product variants for the current product

		$product->attrib_set  = ($ie_params['attr'] && $ie_params['attributes'] && $prod_attrs) ? $product->getAttributes() : false;
		$product->attrib_set2 = ($ie_params['attr'] && $ie_params['attributes'] && $prod_attrs2) ? $product->getAttributes2() : false;
		$serProduct = ImaudExportYMLPlusHelper::getProductData($product);

		if($k) {
			$k = false;
			$tmpProduct[] = $serProduct;
		}
	}

	if($tmpProduct) {
		foreach($tmpProduct as $tp) {
			if($recount < $ie_params['max_rows'] && $tp) {
				$recount++;
	?>
			<tr>
				<td><?php echo $tp->catId[0]; ?></td>
				<td><?php echo $productId; ?></td>
				<td><?php echo $tp->name; ?></td>
				<td><?php echo $tp->price; ?></td>
				<td><?php echo ($notstock || $tp->qty > 0) ? '<i class="icon icon-check text-success"></i>' : '<span class="text-warning">'._JSHOP_IMDEXPORTYML_PREORDER.'</span>'; ?></td>
			</tr>
	<?php
			}
		}
	}
	
endforeach; // $items
<?php
defined( '_JEXEC' ) or die();

class ImaudExportYMLHelper extends IeController {
	
	public static function getJshVersion(){
		$data = \JInstaller::parseXMLInstallFile(JPATH_SITE.'/administrator/components/com_jshopping/jshopping.xml');
		return $data['version'];
	}

	public static function buildTreeCategory($publish = 1, $is_select = 1, $access = 1) {
		$list = JSFactory::getTable('category', 'jshop')->getAllCategories($publish, $access, 'name');
		$tree = new treeObjectList($list, array(
			'parent' => 'category_parent_id',
			'id' => 'category_id',
			'is_select' => $is_select
		));
		return $tree->getList();
	}
	
	public static function getJsDate($date = 'now', $format = 'Y-m-d H:i:s', $local = true) {
		$jdate = new JDate($date, 'UTC');
		$jdate->setTimezone(
			new DateTimeZone(
			JFactory::getConfig()->get('offset')
			)
		);
		return $jdate->format($format, $local);
	}


	public static function getPluginByTrigger($trigger = NULL, $enabled = NULL) {
		$app = JFactory::getApplication();
		$request_plugins = $app->input->getCmd('plugin_id', '');

		if($request_plugins) {
			preg_match('/^(\d+([,.]?))+\d?/', $request_plugins, $matches);
			if($matches[0]) {
				$request_plugin_list = explode(',', $matches[0]);
			} else {
				$request_plugin_list = array($request_plugins);
			}
			if(!$enabled) $enabled = array();
			$enabled = array_merge($request_plugin_list, $enabled);
		}

		if(is_array($enabled) && count($enabled) == 0) return false;

		$result = array();

		if($trigger) {
			$result[$trigger] = array();
		} else {
			$admissible_triggers = array('onBeforeOffers', 'onInnerOffers', 'onAfterOffers', 'onStartOffer', 'onEndOffer');
			foreach($admissible_triggers as $trigger) {
				$result[$trigger] = array();
			}
		}
		/* ниже - не работает на Jshopping 3.14
		$importexport = JSFactory::getModel("importexport");    	
		$ie_plugins = $importexport->getList();
		Используем подмену:
		*/
		$ie_plugins = self::getIePlugins();

		if($ie_plugins) {
			foreach($ie_plugins as $plugin) {
				if($plugin->id != $request_plugin_id && !$plugin->steptime) continue;
				if(strpos($plugin->alias, 'imaudexportyml_') === false) continue;
				if($enabled && !in_array($plugin->id, $enabled)) continue;
				$plugin->params = self::getPluginById($plugin->id);
				$trigger = $plugin->params['trigger'];
				if(isset($trigger)) {
					if(!array_key_exists($trigger, $result)) continue;
					$dirName = str_replace('helper', '', dirname(__FILE__));
					$dirName = str_replace('imaudexportymlplus', $plugin->alias, $dirName);
					$allParams = new StdClass();
					$allParams->id = $plugin->id;
					$allParams->name = $plugin->name;
					$allParams->alias = $plugin->alias;
					$allParams->published = $plugin->steptime;
					$allParams->trigger = $trigger;
					$allParams->file = $dirName.'helper.php';
					$allParams->params = $plugin->params;
					$result[$trigger][$plugin->id] = $allParams;
				}
			}
		}
		return $result;
	}

	public static function getPluginById($id) {
			
		$tableIE = JSFactory::getTable('ImportExport');
		if(!$tableIE->load($id)) return false;
		return parseParamsToArray($tableIE->get('params'));
	
	}
		
	/* замена встроенному методу Jshopping 4
		в версии Jshopping 3 нет метода getModel()
		$importexport = JSFactory::getModel("importexport");    	
		$ie_plugins = $importexport->getList();
	*/
	public static function getIePlugins() {
		$db = JFactory::getDBO();
		$time = time();
		$query = "SELECT * FROM `#__jshopping_import_export` ORDER BY id";
		$db->setQuery($query);
		$list = $db->loadObjectList();
		$result = array();
		foreach($list as $ie){
			$alias = $ie->alias;
			if (file_exists(JPATH_COMPONENT_ADMINISTRATOR."/importexport/".$alias."/".$alias.".php")){
				$result[] = $ie;
			}
		}
		return $result;
	}

	public static function getParamRows($params, $label) {
		$rows = array();
		foreach($params as $pname => $pvalue) {
			if(!($pvalue > ' ')) continue;
			$n = explode('-', $pname);
			if( $n[0] !== $label ) continue;
			if( isset($n[1]) && is_numeric($n[1]) ) $rows[] = (int)$n[1];
		}
		return $rows;
	}
		
	public static function getValue($params = array(), $itemname = '', $default = '') {
		return (isset($params[$itemname]) ? $params[$itemname] : $default);
	}
	
	public static function prepareParams(&$params = array()) {
		$params['categories']			= self::getValue($params, 'categories', '');
		$params['manufacturers']		= self::getValue($params, 'manufacturers', '');
		$params['vendors']			= self::getValue($params, 'vendors', '');
		$params['usergroup'] 			= self::getValue($params, 'usergroup', '');
		$params['addons']			= self::getValue($params, 'addons', '');
		$params['profilename']			= self::getValue($params, 'profilename', 'YML default');
		$params['filename'] 			= self::getValue($params, 'filename', 'priceyml');
		$params['developer_name'] = self::getValue($params, 'developer_name', '');
		$params['developer_email']= self::getValue($params, 'developer_email', '');
		$params['max_rows'] 			= self::getValue($params, 'max_rows', 0);
		$params['set_time_limit'] = self::getValue($params, 'set_time_limit', 0);
		$params['qty']				= self::getValue($params, 'qty', 0);
		$params['qty_name'] 			= self::getValue($params, 'qty_name', '');
		$params['utm']				= self::getValue($params, 'utm', '');
		$params['adult']			= self::getValue($params, 'adult', 0);
		$params['warranty'] 			= self::getValue($params, 'warranty', 0);
		$params['exist_only']			= self::getValue($params, 'exist_only', 0);
		$params['not_in_stock']			= self::getValue($params, 'not_in_stock', 0);
		$params['preorder'] 			= self::getValue($params, 'preorder', 0);
		$params['delivery_time']		= self::getValue($params, 'delivery_time', 0);
		$params['transcode'] 			= self::getValue($params, 'transcode', 0);
		$params['supersef'] 			= self::getValue($params, 'supersef', 0);
		$params['urltracking']			= self::getValue($params, 'urltracking', 0);
		$params['schema']			= self::getValue($params, 'schema', 0);
		$params['clothes']			= self::getValue($params, 'clothes', 0);
		$params['oldprice'] 			= self::getValue($params, 'oldprice', 0);
		$params['auto_discounts']		= self::getValue($params, 'auto_discounts', 0);
		$params['add_price'] 			= self::getValue($params, 'add_price', 0);
		$params['weight']			= self::getValue($params, 'weight', 0);
		$params['fullname'] 			= self::getValue($params, 'fullname', 0);
		$params['content_prepare']		= self::getValue($params, 'content_prepare', 0);
		$params['exclude_child']		= self::getValue($params, 'exclude_child', 0);
		$params['attr']				= self::getValue($params, 'attr', 0);
		$params['param']			= self::getValue($params, 'param', 0);
		$params['pictures'] 			= self::getValue($params, 'pictures', 1);
		$params['store']			= self::getValue($params, 'store', 0);
	}

	public static function emailValidate($emailString) {
		$emailValidExpr="/^([a-zA-Z0-9])+([a-zA-Z0-9._-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9]+[a-zA-Z0-9_-]*)+$/";
		return preg_match($emailValidExpr, $emailString);
	}

	public static function listInt($liststring) {
		// try jshop function getListFromStr($liststring)
		$liststring = preg_replace('/[^,0-9]/', '', $liststring); // удалить всё кроме цифр и запятой
		$liststring = str_replace(" ", "", $liststring); // удалить пробелы
		$liststring = str_replace(",,", ",", $liststring); // удалить удвоенные запятые
		$liststring = trim($liststring, ","); // удалить запятую в начале/конце строки
		return $liststring;
	}

	public static function getArrayOptions($liststring) {
		$liststring = preg_replace('/[^,0-9,\-]/', '', $liststring); // удалить всё кроме цифр, запятой и минус
		// $liststring = str_replace(" ", "", $liststring); // удалить пробелы
		$result = explode(',' , $liststring);
		return $result;
	}

	public static function clearVendorModel($nameString = '', $vendorString = '') {
		if(!($nameString && $vendorString)) return $nameString;
		if ( !(JString::strpos($nameString, $vendorString) === false) 
			and (substr_count ($nameString, $vendorString.'-') == 0 ) ) {
				$nameString = str_ireplace( $vendorString, '', $nameString );
				$nameString = str_replace( '()', '', $nameString);
				$nameString = str_replace( '""', '', $nameString);
				$nameString = trim( str_replace( "''", '', $nameString));
		}
		$nameString = htmlspecialchars(self::reAmp($nameString));
		$nameString = JHtml::_('string.truncate', $nameString, 250);
		return $nameString;
	}

	public static function clearHtmlAttributes($descrString) {
		// очистить HTML элементы от атрибутов
		$clearAttr = array('class', 'style', 'id');
		foreach($clearAttr as $attr) {
			$descrString = preg_replace('/\s?'.$attr.'=["][^"]*"\s?/i', '', $descrString);
		}
		return $descrString;
	}
	
	public static function clearProductDescription($descrString) {
		$descrString = preg_replace('/{youtube}(.*){\/youtube}/', '', $descrString); // удалить видео-теги
		$descrString = preg_replace('/\[widgetkit id=\d*\]/', '', $descrString); // удалить ссылки на widgetkit	
		$descrString = preg_replace('/<p>|<td>|<br>|<\/br>/', ' ', $descrString); // заменить эти теги на пробел
		$descrString = str_replace('\r\n',' ', strip_tags($descrString)); // удалить остальные теги
		$descrString = preg_replace('/(\s{2,})/', ' ', $descrString); // два и больше пробелов заменить на один
		$descrString = preg_replace('/(\s\.)/', ' ', $descrString); // удалить оторванные от слов точки
		$descrString = htmlspecialchars(self::reAmp($descrString));
		
		// $descrString = JHtml::_('string.truncate', $descrString, 490);
		return $descrString;
		/* Дополнительные проверки включить перед строкой htmlspecialchars():
		$descrString = preg_replace('/[\d,]+ руб/', ' ', $descrString); // удалить все цены руб. из описания товара
		*/
	}
	
	public static function contentPrepare($content) {
		// main processing
		$content = JHtml::_('content.prepare', $content);
				
		// Remove all unprocessed plugins {tag}
		$content = preg_replace('/(\[.*?\/])/', '', $content);
		$content = preg_replace('/(\{.*?\/})/', '', $content);
				
		// Additional, cut content inside the tags {AG}xxxxx{/AG}
		// $content = preg_replace('/(\{AG\}(.*)\{\/AG\})/', '', $content);
				
		if(!$ie_params['nodomainurl']) {
			// Remove domain from links in content.
			// $content = preg_replace('/src="((?!http))(\/?)/', 'src="'. $base_url . '/', $content);
			// $content = preg_replace('/href="((?!http))(\/?)/', 'href="'. $base_url . '/', $content);
			$content = preg_replace('/(src="(\/?))|(href="(\/?))(?!http)(\/?)/', '${0}'. $base_url . '/', $content);
			$content = str_replace('"/http', '"http', $content);
		}
		return $content;
	}
	
	public static function getDeliveryOptions($costString = '', $daysString = '', $timeString = '') {

		if((string)$costString=='') return array();

		$cost = self::getArrayOptions($costString);
		if(isset($cost[0]) && empty($cost[0])) $cost[0] = 0;
		
		$result = new StdClass();
		$result->cost = array(0);
		$result->cost = $cost;
		$result->days = array();
		$result->before = array();

		for($i=0; $i < count($result->cost); $i++) { if(empty($result->cost)) $result->cost = $cost[0]; }

		if($daysString > '') $result->days = self::getArrayOptions($daysString);
		
		if($timeString > '') $result->before = self::getArrayOptions($timeString);
		
		if(!($result->cost[0] || $result->days[0] || $result->before[0])) return false;
		
		$count_cost   = count($result->cost);
		$count_days   = count($result->days);
		$count_before = count($result->before);
		
		$maxIdx = max( $count_cost, $count_days, $count_before );

		if($count_cost < $maxIdx) {
			for($i=0; $i < ($maxIdx - $count_cost); $i++) $result->cost[] = $cost[0];
		}
		if($count_days < $maxIdx) {
			for($i=0; $i < ($maxIdx - $count_days); $i++) $result->days[] = '';
		}
		if($count_before < $maxIdx) {
			for($i=0; $i < ($maxIdx - $count_before); $i++) $result->before[] = '';
		}
		return $result;
	}

	public static function reAmp ($value) {
		$search  = array('&amp;', '&nbsp;', '&shy;');
		$replace = array('&', ' ', '');
		return str_replace($search, $replace, $value);
	}
	
	public static function getUnit(&$value, $units = array(), &$param) {
		$tmpvalue = trim($value);
		if(!preg_match('/^\d/', $tmpvalue)) return false;
		$value_length = JString::strlen($tmpvalue);
		$tmpvalue = JString::strtolower($tmpvalue);
		foreach($units as $unit) {
			$unit_length = JString::strlen($unit) + 2;
			if($value_length < $unit_length) continue;
			$pos = JString::strpos($tmpvalue, ' '.$unit, 1);
			if($pos !== false) {
				$subvalue = JString::substr($tmpvalue, $pos+1);
				if(JString::strlen($subvalue) <= $unit_length) {
					$param->setAttribute('unit', $unit);
					$value = JString::substr($tmpvalue, 0, $pos);
					return true;
				}
			}
		}
		return false;
	}

	/*
	// alternate variant with Regular Expression
	public static function getUnit(&$value, $units = array(), &$param) {
		$tmpvalue = trim($value);
		if(!preg_match('/^\d/', $tmpvalue)) return;
		$tmpvalue = JString::strtolower($tmpvalue);
		foreach($units as $unit) {
			$strneed = '/(^[\d][\d]*[,\.]?[\d]*[\s?])('. $unit .')(а?|(ов)?)(\.?)$/';
			if(preg_match($strneed, $tmpvalue, $matches)) {
				if(isset($matches[2])) {
					$param->setAttribute('unit', $unit);
					$value = trim($matches[1]);
					return;
				}
			}
		}
	}
	*/
	
	// Пример названия поля: Размер (EU) или Длина, см
	public static function getUnitFromParamName(&$attrName, $units = array(), &$param) {
		preg_match('/\((.+)\)/', $attrName, $m);
		if(!isset($m[1])) {
			$unit_pos = JString::strrpos($attrName, ',');
			if($unit_pos !== false) {
				$unit = trim(JString::substr($attrName, $unit_pos + 1));
				if(in_array($unit, $units)) {
					$param->setAttribute('name', trim(JString::substr($attrName, 0, $unit_pos)));
					$param->setAttribute('unit', $unit);
					return true;
				}
			}
		} else {
			if(isset($m[1]) && (in_array($m[1], $units))) {
				$attrName = trim(str_replace('('.$m[1].')','',$attrName));
				$param->setAttribute('name', $attrName);
				$param->setAttribute('unit', $m[1]);
				return true;
			}
		}
		return false;
	}
	
	public static function getBooleanText($value) {
		$yes_or_no = array('да' => 'true', 'есть' => 'true', '1' => 'true', 'true' => 'true', 'нет' => 'false', '0' => 'false', 'false' => 'false');
		$result = isset($yes_or_no[$value]) ? $yes_or_no[$value] : false;
		if(!$result && (int)$value) $result = 'true';
		return $result;
	}
	
	public static function translit($input){
		$gost = array(
		 'Є'=>'ye','І'=>'i','Ѓ'=>'g','і'=>'i','№'=>'n','є'=>'ye','ѓ'=>'g',
		 'А'=>'a','Б'=>'b','В'=>'v','Г'=>'g','Д'=>'d',
		 'Е'=>'e','Ё'=>'yo','Ж'=>'zh', 'Ї'=>'yi', 'ї'=>'yi',
		 'З'=>'z','И'=>'i','Й'=>'j','К'=>'k','Л'=>'l',
		 'М'=>'m','Н'=>'n','О'=>'o','П'=>'p','Р'=>'r',
		 'С'=>'s','Т'=>'t','У'=>'u','Ф'=>'f','Х'=>'h',
		 'Ц'=>'c','Ч'=>'ch','Ш'=>'sh','Щ'=>'shch','Ъ'=>'',
		 'Ы'=>'y','Ь'=>'','Э'=>'e','Ю'=>'yu','Я'=>'ya',
		 'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d',
		 'е'=>'e','ё'=>'yo','ж'=>'zh','з'=>'z','и'=>'i',
		 'й'=>'j','к'=>'k','л'=>'l','м'=>'m','н'=>'n',
		 'о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t',
		 'у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch',
		 'ш'=>'sh','щ'=>'shch','ъ'=>'','ы'=>'y','ь'=>'',
		 'э'=>'e','ю'=>'yu','я'=>'ya','('=>'',')'=>'',' '=>'-', '&'=>'_'
		 );
		$input = strtr($input, $gost);
		$input = preg_replace('/[^A-Za-z0-9_\-]/', '_', $input);
		$input = preg_replace("/(_){2,}/", "_", $input);
		$input = JString::strtolower($input);
		return $input;
	}
	
	public static function vardump($var, $title = '') {
		if($title) echo '<h3>'.$title.'</h3>';
		if(empty($var)) {
			echo '<div>No data value</div>';
		} else {
			echo '<pre>';
			var_dump($var);
			echo '</pre><hr>';
		}
	}

	// add custom XML field
	public static function getCustomXML(&$xml, &$parentDOM, $custom_name, $custom_text) {
		
		// process field name
		$custom_name = str_replace(' ', '', trim($custom_name));
		
		if(!$custom_name) return false;
		
		// here is included fields?
		if(stristr($custom_name, '>')) {
			$shop_custom = explode('>', $custom_name);
		} else {
			$shop_custom = array($custom_name);
		}
		
		foreach($shop_custom as $i => $tag) {
			if($i) {
				$childXML = $childXML->appendChild($xml->createElement(htmlspecialchars($tag)));
			} else {
				$childXML = $parentDOM->appendChild($xml->createElement(htmlspecialchars($tag)));
			}
		}
		
		// process field text and attributes if exist
		$custom_text = trim($custom_text);
		if(!strlen($custom_text)) return true;
		
		// Value string has an attributes?		
		if(stristr($custom_text, ':')) {
			$shop_custom_pattern = explode(',', $custom_text);
		} else {
			$shop_custom_pattern = array($custom_text);
		}
		$childXMLinner = array();
		$childXMLinnerAttrs = array();
		foreach($shop_custom_pattern as $i => $pattern) {
			$pattern = trim($pattern);
			if(stristr($pattern, ':')) {
				// there may be field attribute
				$childXMLinnerAttrs[$i] = explode(':', $pattern, 2);
				if(count($childXMLinnerAttrs[$i]) < 2) {
					unset($childXMLinnerAttrs[$i]);
					$childXMLinner['text'] = $pattern;
				}
			} else {
				// there is a field text value
				$childXMLinner['text'] = $pattern;
			}
		}
		if($childXMLinnerAttrs) {
			foreach($childXMLinnerAttrs as $attr) {
				$childXML->setAttribute(trim($attr[0]), trim($attr[1]));
			}
		}
		
		if(isset($childXMLinner['text'])) $childXML->appendChild($xml->createTextNode(htmlspecialchars($childXMLinner['text'])));		
	}
	
	public static function getProductsWithDate($ids, $date) {
		$addon = JSFactory::getTable('addon');
		$addon->loadAlias('jshopping_auction');
		if(!($addon && isset($addon->id) && $addon->id)) return array();

		$db = JFactory::getDBO();
		$query = 'SELECT `product_id`, `product_auction_end`, `product_auction_price`,`product_auction_use_add_opt`,`product_auction_add_options`
				FROM `#__jshopping_products`
				WHERE (`product_publish`=1) AND (`product_auction_use`=1) AND (`product_auction_start`<=\'' . $date . '\') AND (`product_auction_end`>=\'' . $date . '\') AND `product_id` IN (' . implode(',', $ids) . ')
				GROUP BY `product_id`';
		$db->setQuery($query);
		$tmp = $db->loadObjectList();
		
		$products_with_date = array();
		
		if (count($tmp)) {
			require_once JPATH_SITE.'/components/com_jshopping/addons/special_price_time/functions.php';
			foreach ($tmp as $v) {
				if ($v->product_auction_use_add_opt) {
					$days_opt = unserialize(base64_decode($v->product_auction_add_options));
					$use_add_options = checkSpecialPriceAddOptions($date,$days_opt,$v);
				}else{
					$use_add_options = 1;
				}
				if($use_add_options){
					$products_with_date[$v->product_id] = $v;
				}
				
			}
			unset($tmp);
		}
		return $products_with_date;
	}
	
	public static function getShippingMethodCost($shippingmethod) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('`shipping_stand_price` + `package_stand_price` AS price')
					->from('#__jshopping_shipping_method_price')
					->where('`shipping_method_id` = '.(int)$shippingmethod);
		$db->setQuery($query);
		$price = $db->loadResult();
		return $price;
	}

	public static function getDeliveryCostByWeight($weight, $shippingmethod) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('`shipping_price` + `shipping_package_price` AS price')
					->from('#__jshopping_shipping_method_price_weight')
					->where('`sh_pr_method_id` = '.(int)$shippingmethod)
					->where($weight. ' >= `shipping_weight_from`')
					->where($weight. ' < `shipping_weight_to`');
		$db->setQuery($query);
		$price = $db->loadResult();
		return $price;
	}
	
	public static function getChildCategories($parent_categories) {
		// Get all child categories maximum 6 level
		$array_additional_cat = array();
		foreach($parent_categories as $category_id) {
			$category = JSFactory::getTable('category', 'jshop');
			$category->load($category_id);
			$categories = $category->getChildCategories($category->getFieldListOrdering(), $category->getSortingDirection(), 1);
			if(count($categories)) {
				foreach($categories as $child1) {
					$array_additional_cat[] = $child1->category_id;
					$category->load($child1->category_id);
					$categories1 = $category->getChildCategories($category->getFieldListOrdering(), $category->getSortingDirection(), 1);
					if(count($categories1)) {
						foreach($categories1 as $child2) {
							$array_additional_cat[] = $child2->category_id;
							$category->load($child2->category_id);
							$categories2 = $category->getChildCategories($category->getFieldListOrdering(), $category->getSortingDirection(), 1);
						}
						if(count($categories2)) {
							foreach($categories2 as $child3) {
								$array_additional_cat[] = $child3->category_id;
								$category->load($child3->category_id);
								$categories3 = $category->getChildCategories($category->getFieldListOrdering(), $category->getSortingDirection(), 1);
							}
							if(count($categories3)) {
								foreach($categories3 as $child4) {
									$array_additional_cat[] = $child4->category_id;
									$category->load($child4->category_id);
									$categories4 = $category->getChildCategories($category->getFieldListOrdering(), $category->getSortingDirection(), 1);
								}
								if(count($categories4)) {
									foreach($categories4 as $child4) $array_additional_cat[] = $child4->category_id;
								}
							}
						}
					}
				}
			}
		}
		$parent_categories = array_merge($parent_categories, $array_additional_cat);
		$parent_categories = array_unique($parent_categories);
		return $parent_categories;
	}
}
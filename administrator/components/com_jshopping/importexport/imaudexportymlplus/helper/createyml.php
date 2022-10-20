<?php
/*
*  @package      Jshopping
*  @version      4.24.2 2021-11-24
*  @author       Legh Kurow @ IMAUD Studio
*  @authorEmail  service@imaudstudio.com
*  @authorUrl		 https://imaudstudio.com/shop/joomshopping/jshopping-yml-export-yandex-market
*  @copyright    Copyright (C) 2015-2020 imaudstudio.com. All rights reserved.
*
*  Для магазина на CMS Joomla! и компоненте JoomShopping создаёт файл формата YML (xml) 
*  для предоставления ЯНДЕКС.Маркет. Описание системы размещено на сайте Яндекс:
*  https://yandex.ru/support/marketplace/catalog/yml-simple.html
*  Файл-результат ymlexport.xml содержит описание магазина и полный прайс.
*
*  Группирование товаров согласно требованиям к магазинам, торгующим одеждой и обувью
*  https://yandex.ru/support/partnermarket/guides/clothes.html
*
*  Ver 4.24 - added all functions from basic version ImaudExportYML
*  Ver 4.24.1 - added yml_weight custom field
*  Ver 4.24.2 - fixed directory path to the additional plugins; fixed start export by URL (CRON)
*/

defined( '_JEXEC' ) or die();
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

global $jshopConfig, $lang, $router, $sefStatus, $shop_item_id, $acurrencies, $main_currency_code_iso, $units, $exist_only, $taxEnabled, $taxes,
			 $delivtimes, $delivery_free, $deliv_options_global, $single_market_category, $market_category, $categories,
			 $notstock, $show_qty, $countries, $prods_included, $attr_name, $attr_values, $unitsList, $param_trigcat, $cat_list, $weight_unit,
			 $xml, $offers, $extrafields, $imliveurlhost, $base_url, $custom_units, $ie_plugin_trigger, $efv_separator;

$addons = ($ie_params['addons']) ? explode(',' , $ie_params['addons']) : array();
$ie_plugin_trigger = ImaudExportYMLHelper::getPluginByTrigger(NULL, $addons);

if((float)$ie_params['set_time_limit'] > 2) set_time_limit((float)$ie_params['set_time_limit']*60);

$categories_table = "#__jshopping_categories";
$products_table = "#__jshopping_products";
$products_cat = "#__jshopping_products_to_categories";
$vendor_table = "#__jshopping_manufacturers";
$extra_fields = "#__jshopping_products_extra_fields";
$extra_field_values = "#__jshopping_products_extra_field_values";
$menu_table = "#__menu";
$jshcurr = "#__jshopping_currencies";
$jshdelivtimes = "#__jshopping_delivery_times";
$jsh_attr = "#__jshopping_attr";
$jsh_attr_values = "#__jshopping_attr_values";

$jshopConfig = JSFactory::getConfig();
$jConfig = JFactory::getConfig();
$sefStatus = $jConfig->get('sef');
$siteName = $jConfig->get('sitename');
$app = JFactory::getApplication('site');
$router = $app->getRouter();
$imjshuri = JURI::getInstance();
$imliveurlhost = $imjshuri->toString( array("scheme",'host', 'port'));

JSFactory::loadAdminLanguageFile();

$shop_item_id = getShopMainPageItemid();

$my_agency_name = $ie_params['developer_name'];
$my_agency_email= $ie_params['developer_email'];

// список допустимых единиц измерения
$unitsList = array('INT','RU','UA','EU','DE','IT','FR','UK','US','AU','CA', 'дюйм', 'литр', 'грамм', 'гр', 'кг', 'мм', 'см', 'метр', 'м', 'кв.м.', 'вт', 'a', 'в');

$ie_params['units'] = trim($ie_params['units']);
if($ie_params['units']) {
	$ie_params['units'] = '{' . str_replace("'",'"',$ie_params['units']) . '}';
	$custom_units = json_decode($ie_params['units']);
} else {
	$custom_units = false;
}

/* object(stdClass)
["A1"]=> object { ["3"]=> string(2) "RU" ["4"]=> string(2) "RU" ["5"]=> string(2) "RU" }
*/

$lang = JSFactory::getLang();

if($params['content_prepare']) JPluginHelper::importPlugin('content');

// список валют с курсом обмена
$acurrencies = JSFactory::getAllCurrency();
$price_currency_id = (isset($ie_params['currency']) && $ie_params['currency']) ? (int)$ie_params['currency'] : 0;
$main_currency_id = $jshopConfig->mainCurrency;

// атрибуты магазина
$jshMainVendor = JSFactory::getMainVendor();
$my_site_name = $jshMainVendor->shop_name;
$my_site_name = trim(htmlspecialchars($my_site_name));
$my_site_name = (strlen($my_site_name)>2) ? $my_site_name : $siteName;

$my_company_name = $jshMainVendor->company_name;

if(isset($jshopConfig->stock)) {
	$notstock = $jshopConfig->stock ? false : true;
	$show_qty = ($jshopConfig->stock && $ie_params['qty']);
	$ie_params['qty_name'] = ($ie_params['qty_name']) ? htmlspecialchars(trim(trim($ie_params['qty_name'], ':'))) : 'quantity';
	$ie_params['qty_name'] = str_replace(' ','-',$ie_params['qty_name']);
} else {
	$notstock = true;
	$show_qty = false;
}

$base_url = $jshMainVendor->url;
if (empty($base_url)) $base_url = JURI::root();
// убрать слеш в конце URL, если есть
$base_url = rtrim($base_url,DS);
$base_url = rtrim($base_url,'/');

$countryId = $jshMainVendor->country; // default shop country

// сроки поставки
if($ie_params['delivery_time'] && $jshopConfig->show_delivery_time) {
	$query = $db->getQuery(true);
	$query->select( 'id, days, ' . $db->quoteName($lang->get('name'), 'name'));
	$query->from( $jshdelivtimes );
	$db->setQuery($query);
	$delivtimes = $db->loadAssocList('id');
} else {
	$delivtimes = false;
}

$delivery_free = isset($ie_params['delivery_free']) ? (float)$ie_params['delivery_free'] : 0;

// Get User Group Discount
$userGroupTable = JSFactory::getTable('usergroup'); 
$userGroup = $userGroupTable->getList();
$usergroup_discount = 0;
$userGroupId = 0;
$userGroupName = '';
if(isset($ie_params['list_usergroup']) && (int)$ie_params['list_usergroup']>0) {
	$userGroupTable = JSFactory::getTable('usergroup'); 
	$userGroup = $userGroupTable->getList();
	if(count($userGroup)) {
		$userGroupId = isset($ie_params['list_usergroup']) ? (int)$ie_params['list_usergroup'] : $userGroupTable->getDefaultUsergroup();
		foreach($userGroup as $grp) {
			if($grp->usergroup_id == $userGroupId) {
				$usergroup_discount = 1 - (float)$grp->usergroup_discount/100;
				$lang_name = $lang->get('name');
				$userGroupName = ($grp->$lang_name) ? $grp->$lang_name : $grp->usergroup_name;;
				break;
			}
		}


		// Plugin user_group_product_price
		$productUserGroup = (class_exists('plgJshoppingProductsUser_group_product_price')) ? $userGroup : 0;
	}
} else {
	$productUserGroup = false;
}

$jsh_units = JSFactory::getAllUnits();
$weight_unit_id = $jshopConfig->main_unit_weight;
$weight_unit = '';
if(isset($jsh_units[$weight_unit_id])) $weight_unit = $jsh_units[$weight_unit_id]->name;

$jsh_weight_short_name = JString::substr(JString::strtolower($weight_unit), 0, 2);
$is_weight_kg = in_array($jsh_weight_short_name, array('kg', 'кг'));
$is_weight_gr = in_array($jsh_weight_short_name, array('g', 'g.', 'gr', 'г', 'г.', 'гр'));

$taxEnabled = (bool)$jshopConfig->tax;
if($taxEnabled) {
	$_tax = JSFactory::getTable('tax');
	$_taxes = $_tax->getAllTaxes();
	if(!$_taxes) $taxEnabled = false;
	$taxes = array();
	foreach($_taxes as $t) $taxes[$t->tax_id] = $t->tax_value;
}
?>

<h2 class="ymlexport-title">[#<?php echo $ie_id;?>] <?php echo $ie_params['profilename']; ?></h2>
<ul class="blank">
<li class="color1"><?php echo _JSHOP_IMDEXPORTYML_VERSION?>: <?php echo _IMAUD_JSH_EXPORT_YML_VERSION;?></li>
<li><?php echo _JSHOP_STORE_NAME?>: <b><?php echo $my_site_name; ?></b></li>
<li><?php echo _JSHOP_STORE_COMPANY?>: <b><?php echo $my_company_name; ?></b></li>
<li><?php echo _JSHOP_STORE_URL?>: <?php echo $base_url; ?></li>
</ul>

<?php
// Select special YML fields
$aparams_yml = array();
foreach($ie_params as $name => $id) {
	if((substr($name, 0, 4) == "yml_") && (int)$id > 0) {
		$aparams_yml[substr($name, 4)] = (int)$id;
	}
}

$array_excludedFields = array();
$excludedFields = ImaudExportYMLHelper::listInt($ie_params['exclude_fields']);
$array_excludedFields = explode(',', $excludedFields);
$extrafields = array();

$extrafields_info = JSFactory::getAllProductExtraField();

if($extrafields_info) {
	foreach($extrafields_info as $k => $exf) {
		if(in_array($exf->id, $array_excludedFields)) {
			unset($extrafields_info[$k]);
		} else {
			if($aparams_yml) {
				// Rename original extra field to the YML field
				foreach($aparams_yml as $name => $id) {
					if($id == $exf->id) {
						$extrafields_info[$k]->name = $name;
						break;
					}
				}
			}
			$extrafields[] = $exf->id;
		}
	}
}

$attr_name = array();
$attr_values = array();

if ($ie_params['attr']) { // Список атрибутов вкл: begin attributes

	$_attributes = JSFactory::getTable("Attribut");
	$all_attr_names = $_attributes->getAllAttributes(); // массив всех значений всех атрибутов с id атрибута и id значения
		
	if($all_attr_names) {
		if($ie_params['attr'] && $ie_params['attributes']) {
			// Only selected product attributes
			$selectedAttrID = ImaudExportYMLHelper::listInt($ie_params['attributes']);
			$arr_selectedAttr = explode(',', $selectedAttrID);
			foreach($all_attr_names as $attr) {
				if(in_array($attr->attr_id, $arr_selectedAttr)) $attr_name[$attr->attr_id] = $attr->name;
			}
		} elseif($ie_params['attr']) {
			// Get all product attributes
			$all_attributes = array();
			foreach($all_attr_names as $attr) {
				$attr_name[$attr->attr_id] = $attr->name;
				$all_attributes[] = $attr->attr_id;
			}
			$ie_params['attributes'] = $all_attributes ? implode(',', $all_attributes) : '';
		}
		unset($all_attr_names, $all_attributes);
	}

	if($attr_name) {
		$_attributevalue = JSFactory::getTable("AttributValue");
		$attr_values = $_attributevalue->getAllAttributeValues(1); // массив всех значений всех атрибутов с id атрибута и id значения
	}
	
	// массив $attr_name[attr_id] содержит названия всех атрибутов кроме исключённых
	// массив $attr_value[attr_id][value_id] содержит значения для всех атрибутов
} // end attributes


if($ie_params['urltracking']) {
	if($ie_params['utm']) {
		$ie_params['utm'] = str_replace('"', '', trim($ie_params['utm']));
		$ie_params['utm'] = str_replace("'", '', $ie_params['utm']);
		$ie_params['utm'] = str_replace(' ', '%20', $ie_params['utm']);
		if(stristr($ie_params['utm'], 'utm_medium') === false) $ie_params['utm'] .= '&utm_medium=cpc';
	} else {
		$ie_params['utm'] = 'YandexMarket';
	}
} else {
	$ie_params['urltracking'] = '';
}

$param_trigmanf = (int)$ie_params['param_trigmanf'];
$manf_list = ImaudExportYMLHelper::listInt(trim($ie_params['manufacturers'], ','));

// заголовок файла

$imp = new DOMImplementation;
$dtd = $imp->createDocumentType('yml_catalog', '', 'shops.dtd');
$xml = $imp->createDocument("", "", $dtd);
$xml->version = '1.0';
$xml->encoding = 'UTF-8';

// Описание HTML5 DOMDocument http://php5.kiev.ua/manual/ru/class.domdocument.html

$yml_catalog = $xml->appendChild($xml->createElement('yml_catalog'));
$yml_catalog->setAttribute('date',date('Y-m-d H:i'));

$shop = $yml_catalog->appendChild($xml->createElement('shop'));

$xmlsh_name = $shop->appendChild($xml->createElement('name'));
$xmlsh_name->appendChild($xml->createTextNode($my_site_name));
$xmlsh_company = $shop->appendChild($xml->createElement('company'));
$xmlsh_company->appendChild($xml->createTextNode(htmlspecialchars($my_company_name)));
$xmlsh_url = $shop->appendChild($xml->createElement('url'));
$xmlsh_url->appendChild($xml->createTextNode($base_url));
if (!empty($my_agency_name)) {
	$xmlagency = $shop->appendChild($xml->createElement('agency'));
	$xmlagency->appendChild($xml->createTextNode(htmlspecialchars($my_agency_name)));
}
if (!empty($my_agency_email)) {
	if (ImaudExportYMLHelper::emailValidate($my_agency_email)) {
		$xmlemail = $shop->appendChild($xml->createElement('email'));
		$xmlemail->appendChild($xml->createTextNode($my_agency_email));
	} else {
		echo '<p class="ymlexport_errormsg">'._JSHOP_IMDEXPORTYML_EMAIL_WRONG.'</p>'.PHP_EOL;
	}
}
$xmlcurrencies = $shop->appendChild($xml->createElement('currencies'));
$vInverted = array('BYR','UAH');
foreach($acurrencies as $k):
	if($price_currency_id) {
		$itsmaincur = ($k->currency_id == $price_currency_id);
	} else {
		$itsmaincur = ($k->currency_id == $main_currency_id);
	}
	if(!$price_currency_id || ($price_currency_id && $itsmaincur)) {
		$iso = substr($k->currency_code_iso,0,3);
		if($iso == "руб" || $iso == "Руб" ) $iso = "RUB";
		$iso = JString::strtoupper($iso);
		if($ie_params['main_currency'] && !$itsmaincur) continue;
		$xmlcurrency = $xmlcurrencies->appendChild($xml->createElement('currency'));
		$xmlcurrency->setAttribute('id', $iso);
		if(in_array($iso, $vInverted)) {
			$rate = round($k->currency_value, 4);
		} else {
			$rate = round(1/$k->currency_value, 4);
		}
		if($itsmaincur) {
			$main_currency_code_iso = $k->currency_code_iso;
			$rate = 1;
		}
		$xmlcurrency->setAttribute('rate', $rate);
	}
endforeach;

$param_trigcat = (int)$ie_params['param_trigcat'];

// выбираем список категорий из БД с учётом ID исключённых в параметрах
$query = $db->getQuery(true);
$query->select('category_id'); 
$query->from($categories_table);
$query->where( 'category_publish='.$db->Quote('1'));
$query->order('category_id');
$db->setQuery($query);
if (is_null($allcats = $db->loadColumn())) echo '<p>Createyml.php DB query error on '.$categories_table.' (line 448)</p>'.PHP_EOL;

$lang_cat_name = (isset($ie_params['category_language']) ? 'name_'.$ie_params['category_language'] : $lang->get('name'));

$exclude_categoryes = trim($ie_params['categories'], ',');
$array_exclude_cat = array();

$query = $db->getQuery(true);
$query->select( 'category_id, category_parent_id')
		->select($db->quoteName($lang_cat_name, 'name'))
		->select($db->quoteName($lang->get('name'), 'name_default'))
		->from($categories_table)
		->where( 'category_publish='.$db->Quote('1'));

if($param_trigcat && strlen($exclude_categoryes)) {
	$array_exclude_cat = explode(',', $exclude_categoryes);
	if ($ie_params['exclude_child']) {
		$array_exclude_cat = ImaudExportYMLHelper::getChildCategories($array_exclude_cat);
		$exclude_categoryes = implode(',', $array_exclude_cat);
	}
	switch($param_trigcat){
		case 1:
			$query->where('(category_id IN ('.$exclude_categoryes.'))');
			break;
		case 2:
			$query->where('(category_id NOT IN ('.$exclude_categoryes.'))');
			break;
	}
}
$query->order('category_parent_id, category_id');
$db->setQuery($query);

if (is_null($categories = $db->loadObjectList('category_id'))) echo '<p>Createyml.php: DB query error on '.$categories_table.' (line 479)</p>'.PHP_EOL;

$selectcats = array();
foreach($categories as $cat) $selectcats[] = $cat->category_id;

$query = $db->getQuery(true);
$query->select($db->quoteName($lang->get('name'), 'country'));
$query->from('#__jshopping_countries');
$query->where( 'country_publish='.$db->Quote('1'));
$query->order($db->quoteName($lang->get('name')));
$db->setQuery($query);

if (is_null($countries = $db->loadColumn())) echo '<p>Createyml.php: DB query error on #__jshopping_countries (line 491)</p>'.PHP_EOL;

$ie_params['single_market_category'] = isset($ie_params['single_market_category']) ? trim($ie_params['single_market_category']) : false;
$single_market_category = 0;

if($ie_params['single_market_category']) {
	$market_category = explode('/', $ie_params['single_market_category']);
	if(is_array($market_category) && count($market_category)) {
		// build market category path
		$categories = array();
		foreach($market_category as $i => $cat_name) {
			$cat_name = trim($cat_name);
			if(!$cat_name) continue;
			$xmlCategory = new StdClass();
			$xmlCategory->category_id = $i+1;
			$xmlCategory->name = trim($cat_name);
			$xmlCategory->category_parent_id = $i;
			$categories[] = $xmlCategory;
		}
		$single_market_category = count($categories);
	}
}

$ie_params['nodomainurl'] = isset($ie_params['nodomainurl']) ? $ie_params['nodomainurl'] : 0;

if($ie_params['param_trigcat']) {
?>
<h3 class="ymlexport-title"><?php echo _JSHOP_CATEGORY_LIST?></h3>
<?php } ?>

<ul class="blank">
	<?php
	$catsxml = $shop->appendChild($xml->createElement('categories'));
	foreach($categories as $i => $row):
		if ($row->category_parent_id > 0 && ($single_market_category || in_array($row->category_parent_id, $selectcats))) {
			$parentId = $row->category_parent_id . " -> ";
		} else {
			$parentId = "";
		}
		$cat_name = ($row->name) ? $row->name : $row->name_default;
		
		$shop_cat = $catsxml->appendChild($xml->createElement('category'));
		$shop_cat->appendChild($xml->createTextNode($cat_name));
		$shop_cat->setAttribute('id',$row->category_id);
		if ($parentId) $shop_cat->setAttribute('parentId', $row->category_parent_id);
		
		if($ie_params['categoryurl']) {
			// show link to the category page

			$category_link = 'index.php?option=com_jshopping&controller=category&task=view&category_id='.$row->category_id.'&Itemid='.$shop_item_id;
			$category_url = getFullUrlSefLink($category_link, 1);

			// Если нужно - очистить ссылку от пути, оставив только алиас категории
			if($ie_params['supersef']) $category_url = ($ie_params['nodomainurl'] ? '' : $base_url) .'/'. basename($category_url);

			// Удалить домен
			$category_url = str_replace($imliveurlhost, ($ie_params['nodomainurl'] ? '' : $base_url), $category_url);

			if($ie_params['transcode']) $category_url = str_replace('%2F', '/', rawurlencode($category_url));

			$shop_cat->setAttribute('url', $category_url);
		}
		
		if($ie_params['param_trigcat']) { ?>
		<li>
			<?php echo $parentId . $row->category_id . ' -> '.$cat_name; ?>
		</li>
		<?php }
		endforeach;
	?>
</ul>

<?php
switch($ie_params['store']) {
	case 1: // есть розничный магазин
		$xmldeliv = $shop->appendChild($xml->createElement('store'));
		$xmldeliv->appendChild($xml->createTextNode("true"));
		break;
	case 2: // нет 
		$xmldeliv = $shop->appendChild($xml->createElement('store'));
		$xmldeliv->appendChild($xml->createTextNode("false"));
}

switch($ie_params['pickup']) {
	case 1: // самовывоз
		$xmldeliv = $shop->appendChild($xml->createElement('pickup'));
		$xmldeliv->appendChild($xml->createTextNode("true"));
		break;
	case 2: // нет
		$xmldeliv = $shop->appendChild($xml->createElement('pickup'));
		$xmldeliv->appendChild($xml->createTextNode("false"));
}

// Информация о доставке

$deliv_options_global = array();

switch($ie_params['delivery']) {
	case 1: // доставка возможна
		$deliv = $shop->appendChild($xml->createElement('delivery'));
		$deliv->appendChild($xml->createTextNode("true"));
		$deliv_options_global = ImaudExportYMLHelper::getDeliveryOptions( $ie_params['delivery_opt_cost'], $ie_params['delivery_opt_days'], $ie_params['delivery_order_before']);
		if($deliv_options_global) {
			$deliv = $shop->appendChild($xml->createElement('delivery-options'));
			for ($i = 0; $i < count($deliv_options_global->cost); $i++) {
				$childXML = $deliv->appendChild($xml->createElement('option'));
				$childXML->setAttribute('cost', $deliv_options_global->cost[$i]);
				$childXML->setAttribute('days', $deliv_options_global->days[$i]);
				if($deliv_options_global->before[$i]) $childXML->setAttribute('order-before', $deliv_options_global->before[$i]);
			}
		}
		break;
	case 2: // доставки нет
		$deliv = $shop->appendChild($xml->createElement('delivery'));
		$deliv->appendChild($xml->createTextNode("false"));
		break;
}

if(isset($ie_params['sales_notes']) && strlen(trim($ie_params['sales_notes'])) > 1) {
	$sales_notes = trim($ie_params['sales_notes']);
} else {
	$sales_notes = '';
}

if ($ie_params['adult']=='1') {
	$xmladult = $shop->appendChild($xml->createElement('adult'));
	$xmladult->appendChild($xml->createTextNode("true"));
}

if (isset($ie_params['cpa'])) {
	$ie_params['cpa'] = (int)$ie_params['cpa'];
	switch((int)$ie_params['cpa']) {
		case 1:
			$xmlcpa = $shop->appendChild($xml->createElement('cpa'));
			$xmlcpa->appendChild($xml->createTextNode("0"));
			echo '<p>'._JSHOP_IMDEXPORTYML_CPA.": "._JSHOP_IMDEXPORTYML_CPA_NO.'</p>'.PHP_EOL;
			break;
		case 2:
			$xmlcpa = $shop->appendChild($xml->createElement('cpa'));
			$xmlcpa->appendChild($xml->createTextNode("1"));
			echo '<p>'._JSHOP_IMDEXPORTYML_CPA.": "._JSHOP_IMDEXPORTYML_CPA_YES.'</p>'.PHP_EOL;
			break;
	}
}

if ($ie_params['auto_discounts'] == 1) {
	$childXML = $shop->appendChild($xml->createElement('enable_auto_discounts'));
	$childXML->appendChild($xml->createTextNode("true"));
}

ImaudExportYMLHelper::getCustomXML($xml, $shop, $ie_params['shop_custom_name-1'], $ie_params['shop_custom_text-1']);

switch($param_trigcat) {
	case 1 :
		foreach($allcats as $id) {
			if (in_array($id, $selectcats) && !in_array($id, $array_exclude_cat)) array_push($array_exclude_cat, $id);
		}
		break;
	case 2 :
		foreach($allcats as $id) {
			if (!in_array($id, $selectcats) && !in_array($id, $array_exclude_cat)) array_push($array_exclude_cat, $id);
		}
}

$cat_list = implode(',', $array_exclude_cat);
if ($param_trigcat && $cat_list) {
	echo "<p>";
	if ($param_trigcat == 1) echo _JSHOP_IMDEXPORTYML_INCLUDED_CATEGORIES;
	if ($param_trigcat == 2) echo _JSHOP_IMDEXPORTYML_EXCLUDED_CATEGORIES;
	echo ": ".str_replace(',' , ', ', $cat_list)."</p>".PHP_EOL;
} else {
	$cat_list = false;
}

if($single_market_category) 
	echo '<p>'._JSHOP_IMDEXPORTYML_MARKET_CATEGORY.': '.$ie_params['single_market_category'].'</p>'.PHP_EOL;

// список исключённого товара - id через запятую
$exclude_items = $ie_params['exclude_items'];
if (!empty($exclude_items)) {
	$items_list = ImaudExportYMLHelper::listInt($exclude_items);
	echo "<p>"._JSHOP_IMDEXPORTYML_EXCLUDED_ITEMS.": ".$items_list."</p>".PHP_EOL;
} else {
	$items_list = false;
}

if (!empty($selectedAttrID)) echo "<p>"._JSHOP_IMDEXPORTYML_INCLUDE_ATTR.": ".str_replace(',' , ', ', $selectedAttrID)."</p>".PHP_EOL;
if (!empty($excludedFields)) echo "<p>"._JSHOP_IMDEXPORTYML_EXCLUDE_FIELDS.": ".str_replace(',' , ', ', $excludedFields)."</p>".PHP_EOL;

$exist_only = ((int)$ie_params['exist_only']==1 && !$notstock);
if ($exist_only) echo "<p>"._JSHOP_IMDEXPORTYML_EXIST_ONLY_TIP."</p>".PHP_EOL;

if($ie_params['schema']) echo "<p>"._JSHOP_IMDEXPORTYML_SCHEMA."</p>".PHP_EOL;

if($userGroupName) echo "<p>"._JSHOP_IMDEXPORTYML_USERGROUP.": ".$userGroupName." (скидка " . ((1-$usergroup_discount)*100) ."%)</p>".PHP_EOL;

// Get all "custom_" xml-fields from addon parameters
$custom_xml = ImaudExportYMLHelper::getParamRows($ie_params, 'custom_name');
$ie_params['custom_xml_data'] = array();

foreach($custom_xml as $i) {
	if(!($ie_params['custom_name-'.$i]>' ' && ($ie_params['custom_id-'.$i] || $ie_params['custom_def-'.$i]>' '))) continue;
	$e = new StdClass();
	$e->id = (int)$ie_params['custom_id-'.$i];
	$e->name = htmlspecialchars(trim($ie_params['custom_name-'.$i]));
	$e->default = htmlspecialchars(trim($ie_params['custom_def-'.$i]));
	$e->atr = htmlspecialchars(trim($ie_params['custom_atr-'.$i]));
	$e->atr_value = htmlspecialchars(trim($ie_params['custom_atr_val-'.$i]));
	$ie_params['custom_xml_data'][] = $e;
}

$custom_price_id = 0;
if($ie_params['custom_price']) $custom_price_id = (int)$ie_params['custom_price'];

$ie_params['paramlist'] = isset($ie_params['paramlist']) ? (int)$ie_params['paramlist'] : 2; // для совместимости со старыми версиями

switch($ie_params['paramlist']) {
	case 1:
		$efv_separator = ', ';
		break;
	case 2:
		$efv_separator = '; ';
		break;
	case 3:
		$efv_separator = '|';
		break;
	case 9:
		$efv_separator = $jshopConfig->multi_charactiristic_separator;
		break;
	default:
		$efv_separator = '';
}

$prods_included = array(); // здесь собираем все товары, включенные в прайс

/* -----------P-L-U-G-I-N-S---------------------------------------- */
if(isset($ie_plugin_trigger['onBeforeOffers'])) {
	foreach($ie_plugin_trigger['onBeforeOffers'] as $plugin) {
		require($plugin->file);
	}
}
/* -----end-of-P-L-U-G-I-N-S--------------------------------------- */

$offers = $shop->appendChild($xml->createElement('offers'));

// выбираем список товаров, соответствующих всем условиям 
$query = $db->getQuery(true);
$query->select('DISTINCT pt.product_id')
			->from($products_table.' AS pt')
			->leftJoin($products_cat.' AS pc ON pt.product_id = pc.product_id')
			->leftJoin($vendor_table.' AS vt ON pt.product_manufacturer_id = vt.manufacturer_id')
			->leftJoin($categories_table.' AS c ON c.category_id = pc.category_id')
			->where('pt.product_publish='.$db->Quote('1'))
			->where('c.category_publish='.$db->Quote('1'));

if($param_trigmanf && $manf_list) {
	switch($param_trigmanf) {
		case 1:
			$query->where('vt.manufacturer_publish=' . $db->Quote('1'));
			$query->where('pt.product_manufacturer_id IN ('.$manf_list.')');
			break;
		case 2:
			$query->where('(
			(ISNULL(pt.product_manufacturer_id) OR pt.product_manufacturer_id=' . $db->Quote('0') . ')
			OR
			(vt.manufacturer_publish=' . $db->Quote('1').' AND pt.product_manufacturer_id NOT IN ('.$manf_list.'))
			)');
	}
} else {
		$query->where('(ISNULL(pt.product_manufacturer_id) OR pt.product_manufacturer_id=' . $db->Quote('0') . ' OR vt.manufacturer_publish=' . $db->Quote('1').')');
}

if ($exist_only) $query->where('(pt.product_quantity>0 OR pt.unlimited=1)');

if ($items_list) $query->where('pt.product_id NOT IN ('.$items_list.')');
if ($param_trigcat && $cat_list) {
	$query->where('pc.category_id ' . (($param_trigcat == 2) ? 'NOT ' : '') .'IN ('.$cat_list.')');
}

$query->order('pc.category_id, pc.product_ordering');
$db->setQuery($query);
$items = $db->loadColumn();
$countitems = $items ? count($items) : 0;

/* -----------P-L-U-G-I-N-S---------------------------------------- */
if(isset($ie_plugin_trigger['onInnerOffers'])) {
	foreach($ie_plugin_trigger['onInnerOffers'] as $plugin) {
		require($plugin->file);
	}
}
/* -----end-of-P-L-U-G-I-N-S--------------------------------------- */

if($ie_params['max_rows'] && $countitems) {?>
	<h3 class="ymlexport-title"><?php echo _JSHOP_IMDEXPORTYML_PRODUCT_LIST?></h3>
	<table class="table table-bordered table-sm ymlexport_list">
		<thead class="table-dark">
			<tr>
				<th><?php echo _JSHOP_CATEGORY?> (id)</th>
				<th><?php echo _JSHOP_PRODUCT?> (id)</th>
				<th><?php echo _JSHOP_NAME_PRODUCT?></th>
				<th><?php echo _JSHOP_PRODUCT_PRICE?></th>
				<th><?php echo _JSHOP_AVILABILITY_PRODUCT?></th>
			</tr>
		</thead>
		<tbody>
<?php }

if(!$countitems) { ?>
	<p class="ymlexport_errormsg" style="color:red;"><?php echo _JSHOP_IMDEXPORTYML_ERRORSELECT?></p>
<?php }

$recount = 0;

/* -------------------------------------------
** Generate XML data for each selected product
** -------------------------------------------
*/

include(dirname(__FILE__)."/product_yml_plus.php");


/* -----------P-L-U-G-I-N-S---------------------------------------- */
if(isset($ie_plugin_trigger['onAfterOffers'])) {
	foreach($ie_plugin_trigger['onAfterOffers'] as $plugin) {
		require($plugin->file);
	}
}
/* -----end-of-P-L-U-G-I-N-S--------------------------------------- */

if ($ie_params['max_rows']) {
	if ($recount >= $ie_params['max_rows']) {?>
	<tr>
		<td><?php echo _JSHOP_IMDEXPORTYML_MORE;?></td>
		<td><?php echo (count($prods_included)-$ie_params['max_rows']);?>
		<td colspan="3"><?php echo _JSHOP_IMDEXPORTYML_ITEMS;?>...</td>
	</tr>
	<?php }?>
</tbody>
<tfoot class="table-dark">
	<tr class="color2">
		<td><strong><?php echo _JSHOP_IMDEXPORTYML_TOTAL;?></strong></td>
		<td><strong><?php echo count($prods_included);?></strong></td>
		<td colspan="3">&nbsp;</td>
	</tr>
</tfoot>

</table>
<h3 class="ymlexport-title"><?php echo _JSHOP_IMDEXPORTYML_RESULT?></h3>
<?php } else { ?>
<p><strong><?php echo _JSHOP_IMDEXPORTYML_RESULT . ': ' .count($prods_included). ' ' . _JSHOP_IMDEXPORTYML_ITEMS?></strong></p>
<?php }

unset($res,$categories,$prods_included);
// save result to file
$filename = $ie_params['filename'].'.xml';
$fssrv = $jshopConfig->importexport_path.$alias.'/'.$filename;
$xml->formatOutput = true;
$fsurl = $jshopConfig->importexport_live_path.$alias.'/'.$filename;
$fssize = $xml->save($fssrv);

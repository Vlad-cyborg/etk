<?php
/*
*  @package      Jshopping
*  @version      4.24.2 2021-11-30
*  @author       Legh Kurow @ IMaud Studio Int.
*  @authorEmail  service@imaudstudio.com
*  @authorUrl		 https://imaudstudio.com/shop/joomshopping/jshopping-yml-export-yandex-market
*  @copyright    Copyright (C) 2015-2021 imaudstudio.com All rights reserved.
*  Для магазина на CMS Joomla! и компоненте JoomShopping создаёт файл формата YML (xml) 
*	 для предоставления ЯНДЕКС.Маркет. Описание системы размещено на сайте Яндекс:
*  https://yandex.ru/support/marketplace/catalog/yml-simple.html
*  https://yandex.ru/support/partnermarket/guides/clothes.html
*  Файл-результат ymlexport.xml содержит описание магазина и полный прайс.
*/
defined( '_JEXEC' ) or die();
defined('_VM_IE_IMAUD') or define('_VM_IE_IMAUD', 1);
defined('_JSH_IE_IMAUD') or define('_JSH_IE_IMAUD', 1);

define("_IMAUD_JSH_EXPORT_YML_VERSION", "4.24.2 @ 2021-11-30");

jimport('joomla.filesystem.folder'); // Check for new the version!
jimport('joomla.application.component.controller'); // Check for new the version!

class IeImaudExportYMLPlus extends IeController {
    
    function view(){
        require_once(dirname(__FILE__)."/helper/helper.php");

        $jshopVersion = ImaudExportYMLHelper::getJshVersion();
        if(version_compare($jshopVersion, '4.17', 'lt') || version_compare($jshopVersion, '5.0', 'ge')) {
          echo '<h3>This extension is only for Joomshopping 4.17 and newest (not 5)!</h3><p>Your site has Joomshopping '.$jshopVersion.' version.</p>';
          return false;
        }
		
        $app = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();

        IeController::loadLanguageFile();

        $ie_id = $this->ie_id;
        $_importexport = JSFactory::getTable('ImportExport'); 
        $_importexport->load($ie_id);
        $name  = $_importexport->get('name');
        $alias = $_importexport->get('alias');
				
        $endstart = $_importexport->get('endstart');
				if($endstart > 100000) {
					$lastrundate = new DateTime();
					$lastrundate->setTimestamp($endstart);
				} else {
					$lastrundate = '';
				}
				
        $ie_params_str = $_importexport->get('params');
        $ie_params = parseParamsToArray($ie_params_str);
				
				$manufacturer = JSFactory::getTable('manufacturer');
				$ordering = $jshopConfig->manufacturer_sorting==1 ? "ordering" : "name";
				$manufacturers = $manufacturer->getAllManufacturers(1, $ordering, 'asc');
				$list = null;
				if($ie_params['manufacturers']) $list = explode(',', trim($ie_params['manufacturers'],','));
				$list_manufacturers = JHTML::_('select.genericlist', $manufacturers, 'params[list_manufacturers]', 'class="inputbox" size="7" multiple onchange = "saveSelectedOptions(this, \'#manufacturers\')"', 'manufacturer_id', 'name', $list);
				
				$categories = buildTreeCategory(0,1,0);
				$trigcat = ($ie_params['param_trigcat'] == 0) ? ' disabled' : '';
				if($ie_params['categories']) $list = explode(',', trim($ie_params['categories'],','));
				$list_categories = JHTML::_('select.genericlist', $categories, 'params[list_cat]', 'class="inputbox" size="7" multiple'.$trigcat.' onchange = "saveSelectedOptions(this, \'#categories\')"', 'category_id', 'name', $list);

				if($jshopConfig->admin_show_vendors) {
					$vendortable = JSFactory::getTable('vendor');
					$vendors = $vendortable->getAllVendors(1, 0, 0, 'id');
					$list = null;
					if($ie_params['vendors']) $list = explode(',', trim($ie_params['vendors'],','));
					$list_vendors = JHTML::_('select.genericlist', $vendors, 'params[list_vendors]', 'class="inputbox" size="7" multiple onchange = "saveSelectedOptions(this, \'#vendors\')"', 'id', 'f_name', $list);
				} else {
					$list_vendors = false;
				}
				
				$lang = JSFactory::getLang();
				$lang_name = $lang->get('name');
								
				$_lang = JSFactory::getTable('language'); 
				$languages = $_lang->getAllLanguages();
				
				if(!isset($ie_params['category_language'])) $ie_params['category_language'] = $lang->lang;
				$list = explode(',', trim($ie_params['category_language'],','));
				$list_lang = JHTML::_('select.genericlist', $languages, 'params[category_language]', 'class="inputbox" size="1"', 'language', 'name', $list);
				$userGroupTable = JSFactory::getTable('usergroup'); 
				$userGroup = $userGroupTable->getList();
				$userGroupDefault = $userGroupTable->getDefaultUsergroup();
				if(count($userGroup)) {
					foreach($userGroup as $i => $ug) {
						if(!$ug->$lang_name) $userGroup[$i]->$lang_name = $ug->usergroup_name;
					}
					array_unshift($userGroup, array('usergroup_id' => 0, $lang_name => _JSHOP_IMDEXPORTYML_GROUP_GUEST));
					$list = explode(',', trim($ie_params['html_usergroup'],','));
					$list_usergroup = JHTML::_('select.genericlist', $userGroup, 'params[list_usergroup]', 'class="inputbox" size="1"', 'usergroup_id', $lang_name, $list);
				} else {
					$list_usergroup = false;
				}
				
				// наличие поля manufacturer_code в $products_table (в старых версиях Joomshopping этого поля нет)
				$query = $db->getQuery(true);
				$query->select('COUNT(*)');
				$query->from('information_schema.columns');
				$table = $db->replacePrefix('#__jshopping_products');
				$query->where('table_name = ' . $db->quote($table));
				$query->where('column_name = ' . $db->quote('manufacturer_code'));
				$db->setQuery($query);
				$has_manufacturer_code = $db->loadResult();

				$imjshuri = JURI::getInstance();
				$imliveurlhost = $imjshuri->toString( array("scheme",'host', 'port'));

				$files = JFolder::files($jshopConfig->importexport_path.$alias, $ie_params['filename'] . '.xml');
				$count = count($files);

				$user = JFactory::getUser();
				$accessgroups = getAccessGroups();
				if($accessgroups) {
					$list = null;
					if($ie_params['admin_access']) $list = explode(',', trim($ie_params['admin_access'],','));
					$list_accessgroups = JHTML::_('select.genericlist', array_merge(array(_JSHOP_IMDEXPORTYML_NA), $accessgroups), 'params[admin_access]','class = "inputbox" size = "1"','id','title', $params['admin_access'], $list);
					$list = explode(',', trim($ie_params['admin_access'],','));
					$app_access = !$list[0] || (in_array(8, $user->groups) || in_array($list[0], $user->groups));
					if(($list[0] != '0') && !in_array(8, $user->groups)) $list_accessgroups = '';
				} else {
					$app_access = true;
					$accessgroups = array();
					$list_accessgroups = '';
				}
				
				if(!$app_access) {
					echo '<p><b>'._JSHOP_IMDEXPORTYML_ACCESS_DENIED.'</b></p>';
					return false;
				}
				
				// список валют с курсом обмена (в старых версиях нет метода getAllCurrency)
				if (method_exists('JSFactory','getAllCurrency')) {
					$acurrencies = JSFactory::getAllCurrency();
				} else {
					$query = $db->getQuery(true);
					$query->select( $db->quoteName('currency_id'))
								->select( $db->quoteName('currency_code_ISO', 'currency_code_iso'))
								->select( $db->quoteName('currency_value'))
								->from( $jshcurr )
								->where( 'currency_publish='.$db->quote('1') );

					$db->setQuery($query);
					$acurrencies = $db->loadObjectList('currency_id');
					unset($query);
				}
				$allcurr = new StdClass();
				$allcurr->currency_id = "0";
				$allcurr->currency_name = "Все валюты";
				$allcurr->currency_code = "";
				$allcurr->currency_code_iso = "Все валюты";
				$allcurr->currency_value = "0"; 
				array_unshift($acurrencies, $allcurr);
				$list = null;

				if(!isset($ie_params['currency'])) $ie_params['currency'] = 0;
				if($acurrencies) $list = explode(',', trim($ie_params['currency'],','));
				$list_currencies = JHTML::_('select.genericlist', $acurrencies, 'params[currency]', 'class="inputbox" size="1"', 'currency_id', 'currency_code_iso', $list);

				$addonsPosition = ImaudExportYMLHelper::getPluginByTrigger();
				$addons = array();
				foreach($addonsPosition as $position) {
					if(!$position) continue;
					foreach($position as $addon) {
						$addon->name = $addon->id.': '.$addon->name;
						$addons[] = $addon;
					}
				}

				if($addons) {
					$list = null;
					$list = explode(',', trim($ie_params['addons'],','));
					$list_addons = JHTML::_('select.genericlist', $addons, 'params[list_addons]', 'class="inputbox" size="7" multiple onchange = "saveSelectedOptions(this, \'#addons\')"', 'id', 'name', $list);
				} else {
					$list_addons = '';
				}
				
				$_shipping = JSFactory::getTable('shippingmethod');
				$shippingmethods = $_shipping->getAllShippingMethods(1);
				if($shippingmethods) {
					$addrow = new StdClass();
					$addrow->shipping_id = 0;
					$addrow->name = '- - - -';
					array_unshift($shippingmethods, $addrow);
					$ie_params['shippingmethod'] = isset($ie_params['shippingmethod']) ? trim($ie_params['shippingmethod'],',') : 1;
					$list = explode(',', trim($ie_params['shippingmethod'],','));
					$list_shippingmethod = JHTML::_('select.genericlist', $shippingmethods, 'params[list_shippingmethod]', 'class="inputbox" onchange="saveSelectedOptions(this, \'#shippingmethod\')"', 'shipping_id', 'name', $list);
				} else {
					$list_shippingmethod = '';
				}

				// Find custom xml params
				$custom_xml = ImaudExportYMLHelper::getParamRows($ie_params, 'custom_name');
				if(!$custom_xml) $custom_xml[] = 0; // add empty "custom" row if no one row
				
				$addon = JSFactory::getTable('addon');
				$addon->loadAlias('second_short_description_for_product');
				$ssd = !empty($addon->get('params'));

				$query = $db->getQuery(true);
				$query->select( 'attr_id, independent, ' . $db->quoteName($lang->get('name'), 'attr_name'));
				$query->from("#__jshopping_attr");
				$query->order("attr_ordering");
				$db->setQuery($query);
				$attrs = $db->loadAssocList();
				$list_attr = '';
				$has_attr = false;
				if($attrs) {
					$has_attr = true;
					$list = null;
					if($ie_params['attributes']) $list = explode(',', trim($ie_params['attributes'],','));
					$list_attr = JHTML::_('select.genericlist', $attrs, 'params[list_attr]', 'class="inputbox" size="7" multiple onchange = "saveSelectedOptions(this, \'#attributes\')"', 'attr_id', 'attr_name', $list);
				}

				$document = JFactory::getDocument();
				$document->addStyleSheet($jshopConfig->live_admin_path."/importexport/".$alias."/assets/style.css");
				$document->addScript($jshopConfig->live_admin_path."/importexport/".$alias."/assets/script.js");
				
				JToolBarHelper::title($name, 'generic.png' );
				JToolBarHelper::custom("backtolistie", "back", 'browser.png', _JSHOP_BACK_TO.' "'._JSHOP_PANEL_IMPORT_EXPORT.'"', false );
				JToolBarHelper::custom('copyparams', 'copy', 'copy_f2.png', _JSHOP_IMDEXPORTYML_COPY_PARAMS, false);
				JToolBarHelper::spacer();
				JToolBarHelper::save("save", _JSHOP_EXPORT);
				include(dirname(__FILE__)."/params.php");
    }

    function save(){
			
        global $ie_id, $ie_params, $db, $router;

        $jshopConfig = JSFactory::getConfig();

        $timestart = time();
        $app = JFactory::getApplication();
        
        IeController::loadLanguageFile();

        $ie_id = JRequest::getInt('ie_id');
        if (!$ie_id) $ie_id = $this->get('ie_id');

        $_importexport = JSFactory::getTable('ImportExport'); 
        $_importexport->load($ie_id);
        $name  = $_importexport->get('name');
        $alias = $_importexport->get('alias');

        if(!$alias) {
          echo 'BAD REQUEST!<br><strong>Export/Import template with ID='.$ie_id.' is not exist on this site!</strong>'.PHP_EOL;
          return false;
        }
				
        $_importexport->set('endstart', time());        
        $ie_params = JRequest::getVar("params"); // параметры в поле params таблицы importexport
				
        if (is_array($ie_params)){
            $paramsstr = parseArrayToParams($ie_params);
            $_importexport->set('params', $paramsstr);
        }                
        $_importexport->store();

        $ie_params_str = $_importexport->get('params');
        $ie_params = parseParamsToArray($ie_params_str);

				$db = JFactory::getDBO();
				
				// changed template name?
				$profilename = $ie_params['profilename'] ? trim($ie_params['profilename']) : '';
				if($profilename && $name != $profilename) {
					$oUpdate = (object)array(
						'name' => $profilename,
						'id' => $ie_id
					);
					$db->updateObject('#__jshopping_import_export', $oUpdate, 'id');	
				}
        
				if(class_exists('JToolBarHelper')) {
					JToolBarHelper::title($name, 'generic.png' );
					JToolBarHelper::back(_JSHOP_BACK_TO.' "'.$name.'"', "index.php?option=com_jshopping&controller=importexport&task=view&ie_id=".$ie_id);
				}
				
				$document = JFactory::getDocument();
				$document->addStyleSheet($jshopConfig->live_admin_path."/importexport/".$alias."/assets/style.css");

				require_once(dirname(__FILE__)."/helper/helper.php");
				require_once(dirname(__FILE__)."/helper/helper_plus.php");
				
				include(dirname(__FILE__)."/helper/createyml.php");
?>
				<div class="ymlexport_result" style="font-size: 16px">
<?php
				if (file_exists($fssrv)) {?>
				<p class="ymlexport_successmsg"><?php echo _JSHOP_IMDEXPORTYML_TO_FILE?> <a href="<?php echo $fsurl?>" target="_blank"><strong><?php echo $filename?></strong></a> <?php echo _JSHOP_IMDEXPORTYML_SAVED .' ' . $fssize . ' ' . _JSHOP_IMDEXPORTYML_BYTES?></p>
					<?php
					if($ie_params['max_rows']) {
						$bar = JToolBar::getInstance('toolbar');
						$bar->appendButton('Link', 'folder-open', _JSHOP_IMDEXPORTYML_RESULT_BUTTON_LABEL, $fsurl);
					}
				} else {?>
					<p class="ymlexport_errormsg"><?php echo _JSHOP_IMDEXPORTYML_ERROREXPORT?></p>
				<?php }
				$duration = (time() - $timestart);
				$duration_min = intval($duration/60);
				$duration_sec = $duration % 60; ?>
				<p><?php echo _JSHOP_IMDEXPORTYML_DURATION . $duration_min . ' m : ' . $duration_sec;?> s </p>
				<?php if($ie_params['max_rows']) { ?>
				<p><?php echo _JSHOP_IMDEXPORTYML_PRESS?> <a class="btn btn-success" title="<?php echo _JSHOP_IMDEXPORTYML_BACK_TO_EXPORT?>" href="index.php?option=com_jshopping&controller=importexport&task=view&ie_id=<?php echo $ie_id?>">ОК</a> <?php echo _JSHOP_IMDEXPORTYML_BACK_TO_CONTROLPANEL?></p>
				<?php }?>
				</div>
<?php }
		
    function filedelete(){
			
        $jshopConfig = JSFactory::getConfig();
        $app = JFactory::getApplication();
        $ie_id = JRequest::getInt("ie_id");
        $_importexport = JSFactory::getTable('ImportExport'); 
        $_importexport->load($ie_id);
        $alias = $_importexport->get('alias');
        $file = JRequest::getVar("file");
        $filename = $jshopConfig->importexport_path.$alias."/".$file;
        @unlink($filename);
        $app->redirect("index.php?option=com_jshopping&controller=importexport&task=view&ie_id=".$ie_id);

		}
		
    function copyparams(){
			
        $app = JFactory::getApplication();

        IeController::loadLanguageFile();

        $ie_id = JRequest::getInt("ie_id");
        if (!$ie_id) $ie_id = $this->get('ie_id');
			
        $_importexport = JSFactory::getTable('ImportExport'); 
        $_importexport->load($ie_id);
        $name = $_importexport->get('name');
        $alias = $_importexport->get('alias');
        $_importexport->set('endstart', time());        
        $ie_params = JRequest::getVar('params'); // параметры в поле params таблицы importexport

        if (is_array($ie_params)){
            $paramsstr = parseArrayToParams($ie_params);
            $_importexport->set('params', $paramsstr);
        }

				$profilename = $ie_params['profilename'] ? trim($ie_params['profilename']) : '';
				$profilename = $profilename ? $profilename : $name;

				$db = JFactory::getDBO();
				if($name == $profilename) {
					$oUpdate = (object)array(
						'params' => $paramsstr,
						'id' => $ie_id
					);
					$db->updateObject('#__jshopping_import_export', $oUpdate, 'id');	
					$usID = $ie_id;
				} else {
					$oUpdate = (object)array(
						'name'					=> $profilename,
						'alias'					=> $alias,
						'description'		=> 'Профиль параметров экспорта для '.$alias,
						'params'				=> $paramsstr,
						'endstart'			=> 1,
						'steptime'			=> 1
					);
					$db->insertObject('#__jshopping_import_export', $oUpdate);
					$usID = $db->insertid();
				}
			$app->redirect("index.php?option=com_jshopping&controller=importexport&task=view&ie_id=".$usID);				
 		}
}
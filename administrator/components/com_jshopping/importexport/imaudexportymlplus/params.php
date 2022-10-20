<?php
/*
*  @package      Jshopping
*  @version      4.24.2 2021-11-19
*  @author       Legh Kurow (St.iMaud)
*  @authorEmail  service@imaudstudio.com
*  @authorUrl    https://imaudstudio.com/shop/joomshopping/jshopping-yml-export-yandex-market
*  @copyright    Copyright (C) 2015-2021 imaudstudio.com All rights reserved.
*  Для магазина на CMS Joomla 3! и компоненте JoomShopping 4 создаёт xml-файл по схеме YML.
*  Описание системы размещено на сайте Яндекс:
*  https://yandex.ru/legal/market_adv_rules/
*  https://yandex.ru/support/partnermarket/export/yml.html
*  Группирование товаров по атрибутам корзины согласно требованиям "Одежда, обувь"
*  https://partner.market.yandex.ru/legal/clothes
*  Файл-результат ymlexport.xml содержит описание магазина и полный прайс.
*/

defined( '_JEXEC' ) or die( );

JHtml::_('behavior.framework', true);
JHtml::_('bootstrap.tooltip');

$addon = JSFactory::getTable('addon');
$addon->loadAlias('second_short_description_for_product');
$tmp = $addon->get('params');
$ssd = !empty($tmp);
?>

<form id="adminForm" class="ymlexport" action="index.php?option=com_jshopping&controller=importexport" method="post" name="adminForm">
	<input type="hidden" name="task" value = "" />
	<input type="hidden" name="hidemainmenu" value = "0" />
	<input type="hidden" name="boxchecked" value = "0" />
	<input type="hidden" name="ie_id" value = "<?php echo $ie_id?>" />
	<?php if($accessgroups && !$list_accessgroups) { // Hasn't user access groups or Super Users only! ?>
	<input type="hidden" name="params[admin_access]" value="<?php echo $ie_params['admin_access']?>"/>
	<?php }?>
	<input type="hidden" id="categories" name = "params[categories]" value = "<?php echo $ie_params['categories']?>" />
	<input type="hidden" id="manufacturers" name = "params[manufacturers]" value = "<?php echo $ie_params['manufacturers']?>" />
	<input type="hidden" id="vendors" name = "params[vendors]" value = "<?php echo $ie_params['vendors']?>" />
	<input type="hidden" id="usergroup" name = "params[usergroup]" value = "<?php echo $ie_params['usergroup']?>" />
	<input type="hidden" id="shippingmethod" name = "params[shippingmethod]" value = "<?php echo $ie_params['shippingmethod']?>" />
	<input type="hidden" id="addons" name = "params[addons]" value = "<?php echo $ie_params['addons']?>" />
	<input type="hidden" id="attributes" name="params[attributes]" value="<?php echo $ie_params['attributes'];?>" />

	<div class="main-card">

		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', ['active' => 'part1', 'recall' => true, 'breakpoint' => 768]); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'part1', _JSHOP_IMDEXPORTYML_TAB1); ?>
			<div style="margin: 2em 0 0 0">
				<p><?php echo _JSHOP_IMDEXPORTYML_VERSION?>: <?php echo _IMAUD_JSH_EXPORT_YML_VERSION; ?> by <a href="https://imaudstudio.com/shop/joomshopping/jshopping-yml-export-yandex-market" target="_blank">IMAUD&nbsp;Studio</a></p>
				<?php if($lastrundate) {?>
				<p><?php echo _JSHOP_IMDEXPORTYML_LASTRUNDATE?>: <?php echo $lastrundate->format('Y-m-d H:i')?></p>
				<?php }?>
				<p><?php echo _JSHOP_IMDEXPORTYML_CRON_TASK?>:<br>
				<?php $cronhref = $imliveurlhost.'/index.php?option=com_jshopping&amp;controller=importexportbyid&amp;task=start&amp;key='.$jshopConfig->securitykey.'&amp;alias='.$alias.'&amp;ie_id='.$ie_id;?>
				<code style="background:#d9fbff; display: block;">/usr/bin/wget -O /dev/null "<a href="<?php echo $cronhref;?>" target="_blank"><?php echo $cronhref;?></a>"</code></p>
				<table class="adminlist table table-striped" style="margin-top:2em">
					<thead>
					<tr>
						<th class="title" width="10">#</th>    
						<th align="left"><?php echo _JSHOP_NAME?></th>
						<th width="150"><?php echo _JSHOP_DATE?></th>    
						<th width="50"><?php echo _JSHOP_DELETE?></th>
					</tr>
					</thead>
					<tbody>
						<?php
						$i = 0;
						foreach($files as $row){?>
						<tr class = "row<?php echo $i % 2?>">
							<td><?php echo $i+1?></td>
						<td>
							<a target="_blank" href = "<?php echo $jshopConfig->importexport_live_path.$_importexport->get('alias')."/".$row; ?>"><?php echo $row?></a>
						</td>
						<td>
							<?php echo date("d.m.Y H:i:s", filemtime($jshopConfig->importexport_path.$_importexport->get('alias')."/".$row)); ?>
						</td>    
						<td align="center">
							<a href='index.php?option=com_jshopping&controller=importexport&task=filedelete&ie_id=<?php echo $ie_id?>&file=<?php echo $row?>' onclick="return confirm('<?php echo JText::_("JSHOP_DELETE")?>');"><img src="components/com_jshopping/images/publish_r.png"></a>
						</td>
						</tr>
						<?php $i++; }?>
					</tbody>
				</table>
		</div><!-- /#part1 -->
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'part2', _JSHOP_IMDEXPORTYML_TAB2); ?>
		<div class="row" style="margin: 2em 0 0 0">
			<div class="span4">
			<ul>
				<li>
					<label for="param_profilename" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_PROFILENAME_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_PROFILENAME?></label> 
					<input type="text" name="params[profilename]" id="param_profilename" value="<?php echo $name?>" size="30">
				</li>
				<li>
					<label for="param_filename" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_FILENAME_TIP?>"><?php echo _JSHOP_FILE_NAME;?></label> 
					<input type="text" name="params[filename]" id="param_filename" onkeyup="value=value.replace(/[^a-zA-Z\d\._~@\-\(\)]/g,'')" value="<?php echo $ie_params['filename']?>" size="30">
				</li>
				<li>
					<label for="param_developer_name" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_DEVELOPER_NAME_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_DEVELOPER_NAME?></label>
					<input type="text" id="param_developer_name" name="params[developer_name]" value="<?php echo $ie_params['developer_name']?>" size="30">
				</li>
				<li>
					<label for="param_developer_email"  class="hasTip"title="<?php echo _JSHOP_IMDEXPORTYML_DEVELOPER_EMAIL_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_DEVELOPER_EMAIL?></label>
					<input type="email" for="param_developer_email" name="params[developer_email]" onkeyup="value=value.replace(/[^a-zA-Z\d\._@\-\(\)]/g,'')" value="<?php echo $ie_params['developer_email']?>" size="30">
				</li>
				<li>
					<label for="param_max_rows" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_MAX_ROWS_TO_DISPLAY_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_MAX_ROWS_TO_DISPLAY?></label>
					<select id="param_max_rows" name="params[max_rows]" default="0" size="1">
						<option <?php if($ie_params['max_rows']=="0") echo "selected"; ?> value="0"><?php echo _JSHOP_IMDEXPORTYML_DONT_LISTING?></option>
						<option <?php if($ie_params['max_rows']=="20") echo "selected"; ?> value="20">20</option>
						<option <?php if($ie_params['max_rows']=="30") echo "selected"; ?> value="30">30</option>
						<option <?php if($ie_params['max_rows']=="50") echo "selected"; ?> value="50">50</option>
						<option <?php if($ie_params['max_rows']=="100") echo "selected"; ?> value="100">100</option>
						<option <?php if($ie_params['max_rows']=="150") echo "selected"; ?> value="150">150</option>
						<option <?php if($ie_params['max_rows']=="200") echo "selected"; ?> value="200">200</option>
						<option <?php if($ie_params['max_rows']=="999") echo "selected"; ?> value="999">999</option>
					</select>
				</li>
				<li>
					<label for="params[set_time_limit]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_SET_TIMELIMIT_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_SET_TIMELIMIT?></label>
					<input type="number" id="params[set_time_limit]" name="params[set_time_limit]" value="<?php if($ie_params['set_time_limit']>0) echo $ie_params['set_time_limit']; ?>" size="3" step="1" min="2">
				</li>
				
				<?php if($list_accessgroups) { // Super Users only! ?>
				<li>
					<label for="params[admin_access]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_ACCESSGROUPS_TIP?>"><b><?php echo _JSHOP_IMDEXPORTYML_ACCESSGROUPS?>:</b></label>
					<?php echo $list_accessgroups; ?>
				</li>
				<?php }?>

				<?php if($addons) {?>
				<li>
					<label for="addons" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_ADDONS_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_ADDONS?>:</label>
					<?php echo $list_addons;?>
				</li>
				<?php }?>

				<li>
					<label for="params[utm]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_UTM_STRING_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_UTM_STRING?></label> 
					<input type="text" name="params[utm]" value="<?php echo $ie_params['utm']?>" size="30" placeholder="YandexMarket">
				</li>
				<li>
					<label for="params_currency" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_CURRENCIES_TIP?>"><b><?php echo _JSHOP_IMDEXPORTYML_CURRENCIES?>:</b></label>
					<?php echo $list_currencies?>
				</li>
				<li style="border-top: 1px dotted #ccc; padding: 15px 0;">
					<label for="param_qty" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_QUANTITY_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_QUANTITY?></label>
					<select id="param_qty" name="params[qty]" default="0" size="1">
						<option <?php if($ie_params['qty']=="0") echo "selected"?> value="0"><?php echo _JSHOP_IMDEXPORTYML_QTY_OFF?></option>
						<option <?php if($ie_params['qty']=="1") echo "selected"?> value="1"><?php echo _JSHOP_IMDEXPORTYML_QTY_ATTR?></option>
						<option <?php if($ie_params['qty']=="2") echo "selected"?> value="2"><?php echo _JSHOP_IMDEXPORTYML_QTY_TAG?></option>
						<option <?php if($ie_params['qty']=="3") echo "selected"?> value="3"><?php echo _JSHOP_IMDEXPORTYML_QTY_PARAM?></option>
						<option <?php if($ie_params['qty']=="4") echo "selected"?> value="4"><?php echo _JSHOP_IMDEXPORTYML_QTY_OUTLET?></option>
					</select>
				</li>
				<li>
					<label for="param_qty_name" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_QTY_NAME_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_QTY_NAME?></label>
					<input type="text" id="param_qty_name" name="params[qty_name]" value="<?php echo $ie_params['qty_name']?>" size="30" placeholder="quantity">
				</li>
			</ul>
			</div>

			<div class="span4">	
			<ul>
				<li class="checkbox">
					<input type="checkbox" name="params[adult]" value="1" <?php if($ie_params['adult']=="1") echo "checked"; ?>>
					<label for="params[adult]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_ADULT_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_ADULT?></label>
				</li>
				<li class="checkbox">
					<input type="checkbox" name="params[warranty]" value="1" <?php if($ie_params['warranty']=="1") echo "checked"; ?>>
					<label for="params[warranty]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_WARRANTY_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_WARRANTY?></label>
				</li>
				<li class="checkbox">
					<input type="checkbox" name="params[exist_only]" value="1" <?php if($ie_params['exist_only']=="1") echo "checked"; ?>>
					<label for="params[exist_only]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_EXIST_ONLY_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_EXIST_ONLY?></label>
				</li>
				<li class="checkbox">
					<input type="checkbox" name="params[not_in_stock]" value="1" <?php if($ie_params['not_in_stock']=="1") echo "checked"?>>
					<label for="params[not_in_stock]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_NOT_IN_STOCK_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_NOT_IN_STOCK?></label>
				</li>
				<li class="checkbox">
					<input type="checkbox" name="params[preorder]" value="1" <?php if($ie_params['preorder']=="1") echo "checked"?>>
					<label for="params[preorder]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_PREORDERALL_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_PREORDERALL?></label>
				</li>
				<?php if($jshopConfig->show_delivery_time) {?>
				<li class="checkbox">
					<input type="checkbox" name="params[delivery_time]" value="1" <?php if($ie_params['delivery_time']=="1") echo "checked"; ?>>
					<label for="params[delivery_time]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_DELIVERY_TIME_TIP;?>"><?php echo _JSHOP_IMDEXPORTYML_DELIVERY_TIME;?></label>
				</li>
				<?php }?>
				<li class="checkbox">
					<input type="checkbox" name="params[transcode]" value="1" <?php if($ie_params['transcode']=="1") echo "checked"; ?>>
					<label for="params[transcode]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_TRANSCODE_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_TRANSCODE?></label>
				</li>
				<li class="checkbox">
					<input type="checkbox" name="params[supersef]" value="1" <?php if($ie_params['supersef']=="1") echo "checked"?>>
					<label for="params[supersef]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_SUPERSEF_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_SUPERSEF?></label>
				</li>
				<li class="checkbox">
					<input type="checkbox" name="params[nodomainurl]" value="1" <?php if($ie_params['nodomainurl']=="1") echo "checked"?>>
					<label for="params[nodomainurl]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_NO_DOMAIN_URL_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_NO_DOMAIN_URL?></label>
				</li>
				<li class="checkbox">
					<input type="checkbox" name="params[urltracking]" value="1" <?php if($ie_params['urltracking']=="1") echo "checked"?>>
					<label for="params[urltracking]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_URL_TRACKING_OPTIONS_TIP?>"><a href="https://support.google.com/analytics/answer/1033867?hl=ru" target="_blank"><?php echo _JSHOP_IMDEXPORTYML_URL_TRACKING_OPTIONS?></a></label>
				</li>
				<li class="checkbox">
					<input type="checkbox" name="params[categoryurl]" value="1" <?php if($ie_params['categoryurl']=="1") echo "checked"?>>
					<label for="params[categoryurl]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_CATEGORY_URL_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_CATEGORY_URL?></label>
				</li>
				<li class="checkbox">
					<input type="checkbox" name="params[schema]" value="1" <?php if($ie_params['schema']=="1") echo "checked"; ?>>
					<label for="params[schema]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_SCHEMA_TIP?>"><a href="https://partner.market.yandex.ru/legal/tt/" target="_blank"><?php echo _JSHOP_IMDEXPORTYML_SCHEMA?></a></label>
				</li>
				<li class="checkbox">
					<input type="checkbox" name="params[main_currency]" value="1" <?php if($ie_params['main_currency']=="1") echo "checked"; ?>>
					<label for="params[main_currency]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_MAIN_CURRENCY_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_MAIN_CURRENCY?></label>
				</li>
				<li class="checkbox">
					<input type="checkbox" name="params[oldprice]" value="1" <?php if($ie_params['oldprice']=="1") echo "checked"; ?>>
					<label for="params[oldprice]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_OLDPRICE_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_OLDPRICE?></label>
				</li>
				<li class="checkbox">
					<input type="checkbox" name="params[auto_discounts]" value="1" <?php if($ie_params['auto_discounts']=="1") echo "checked"; ?>>
					<label for="params[auto_discounts]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_ENABLE_AUTO_DISCOUNTS_TIP?>"><a href="https://yandex.ru/support/partnermarket/elements/enable_auto_discounts.html" target="_blank"><?php echo _JSHOP_IMDEXPORTYML_ENABLE_AUTO_DISCOUNTS?></a></label>
				</li>
				<li class="checkbox">
					<input type="checkbox" name="params[add_price]" value="1" <?php if($ie_params['add_price']=="1") echo "checked"?>>
					<label for="params[add_price]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_ADD_PRICE_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_ADD_PRICE?></label>
				</li>
				<?php if($jshopConfig->admin_show_product_bay_price) {?>
				<li class="checkbox">
					<input type="checkbox" name="params[buy_price]" value="1" <?php if($ie_params['buy_price']=="1") print "checked";?>>
					<label for="params[buy_price]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_BUY_PRICE_TIP?>"><?php print _JSHOP_IMDEXPORTYML_BUY_PRICE?></label>
				</li>
				<?php }?>
				<li class="checkbox">
					<input type="checkbox" name="params[vat]" value="1" <?php if($ie_params['vat']=="1") echo "checked";?>>
					<label for="params[vat]" class="hasTip text-sm-start" title="<?php echo _JSHOP_IMDEXPORTYML_VAT_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_VAT?></label>
				</li>
				<?php if($jshopConfig->admin_show_weight) {?>
				<li class="checkbox">
					<input type="checkbox" name="params[weight]" value="1" <?php if($ie_params['weight']=="1") echo "checked"?>>
					<label for="params[weight]" class="hasTip text-sm-start" title="<?php echo _JSHOP_IMDEXPORTYML_WEIGHT_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_WEIGHT?></label>
				</li>
				<?php }?>
				<li class="checkbox">
					<input type="checkbox" name="params[content_prepare]" value="1" <?php if($ie_params['content_prepare']=="1") echo "checked"?>>
					<label for="params[content_prepare]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_CONTENT_PREPARE_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_CONTENT_PREPARE?></label>
				</li>
			</ul>
			</div>

			<div class="span4">
			<ul>
				<li><small><?php echo _JSHOP_IMDEXPORTYML_EFIDS?></small></li>
				<li>
					<label for="params[yml_cpa]" ><code>cpa</code> <span><?php echo _JSHOP_IMDEXPORTYML_ID_CPA?></span></label>
					<input type="number" id="params[yml_cpa]" name="params[yml_cpa]" value="<?php echo (($ie_params['yml_cpa']>0)? $ie_params['yml_cpa'] : '0'); ?>" size="5" step="1" min="0">
				</li>
				<li>
					<label for="params[yml_available]" ><code>available</code> <span><?php echo _JSHOP_IMDEXPORTYML_ID_AVAILABLE?></span></label>
					<input type="number" id="params[yml_available]" name="params[yml_available]" value="<?php echo (($ie_params['yml_available']>0)? $ie_params['yml_available'] : '0'); ?>" size="5" step="1" min="0">
				</li>
				<li>
					<label for="params[yml_market_category]" ><code>market_category</code> <span><?php echo _JSHOP_IMDEXPORTYML_ID_MARKET_CATEGORY?></span></label>
					<input type="number" id="params[yml_market_category]" name="params[yml_market_category]" value="<?php echo (($ie_params['yml_market_category']>0)? $ie_params['yml_market_category'] : '0'); ?>" size="5" step="1" min="0">
				</li>
				<li>
					<label for="params[yml_store]" ><code>store</code> <span><?php echo _JSHOP_IMDEXPORTYML_ID_STORE?></span></label>
					<input type="number" id="params[yml_store]" name="params[yml_store]" value="<?php echo (($ie_params['yml_store']>0)? $ie_params['yml_store'] : '0'); ?>" size="5" step="1" min="0">
				</li>
				<li>
					<label for="params[yml_pickup]" ><code>pickup</code> <span><?php echo _JSHOP_IMDEXPORTYML_ID_PICKUP?></span></label>
					<input type="number" id="params[yml_pickup]" name="params[yml_pickup]" value="<?php echo (($ie_params['yml_pickup']>0)? $ie_params['yml_pickup'] : '0'); ?>" size="5" step="1" min="0">
				</li>
				<li>
					<label for="params[yml_typePrefix]" ><code>typePrefix</code> <span><?php echo _JSHOP_IMDEXPORTYML_ID_TYPEPREFIX?></span></label>
					<input type="number" id="params[yml_typePrefix]" name="params[yml_typePrefix]" value="<?php echo (($ie_params['yml_typePrefix']>0)? $ie_params['yml_typePrefix'] : '0'); ?>" size="5" step="1" min="0">
				</li>
				<li>
					<label for="params[yml_name]" ><code>name</code> <span><?php echo _JSHOP_IMDEXPORTYML_ID_NAME?></span></label>
					<input type="number" id="params[yml_name]" name="params[yml_name]" value="<?php echo (($ie_params['yml_name']>0)? $ie_params['yml_name'] : '0'); ?>" size="5" step="1" min="0">
				</li>
				<li>
					<label for="params[yml_model]" ><code>model</code> <span><?php echo _JSHOP_IMDEXPORTYML_ID_MODEL?></span></label>
					<input type="number" id="params[yml_model]" name="params[yml_model]" value="<?php echo (($ie_params['yml_model']>0)? $ie_params['yml_model'] : '0'); ?>" size="5" step="1" min="0">
				</li>
				<li>
					<label for="params[yml_price]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_ID_PRICE_TIP?>"><code>price</code> <span><?php echo _JSHOP_IMDEXPORTYML_CUSTOM_PRICE?></span></label>
					<input type="number" id="params[yml_price]" name="params[yml_price]" value="<?php echo $ie_params['yml_price']>0 ? $ie_params['yml_price'] : '0'?>" size="5" step="1" min="0">
				</li>
				<li>
					<label for="params[yml_vendorCode]" ><code>vendorCode</code> <span><?php echo _JSHOP_IMDEXPORTYML_ID_VENDORCODE?></span></label>
					<input type="number" id="params[yml_vendorCode]" name="params[yml_vendorCode]" value="<?php echo $ie_params['yml_vendorCode']>0 ? $ie_params['yml_vendorCode'] : '0'?>" size="5" step="1" min="0">
				</li>
				<li>
					<label for="params[yml_barcode]" ><code>barcode</code> <span><?php echo _JSHOP_IMDEXPORTYML_ID_BARCODE?></span></label>
					<input type="number" id="params[yml_barcode]" name="params[yml_barcode]" value="<?php echo $ie_params['yml_barcode']>0? $ie_params['yml_barcode'] : '0'?>" size="5" step="1" min="0">
				</li>
				<li>
					<label for="params[yml_weight]" ><code>weight</code> <span><?php echo _JSHOP_IMDEXPORTYML_ID_WEIGHT?></span></label>
					<input type="number" id="params[yml_weight]" name="params[yml_weight]" value="<?php echo (($ie_params['yml_weight']>0)? $ie_params['yml_weight'] : '0'); ?>" size="5" step="1" min="0">
				</li>
				<li>
					<label for="params[yml_sales_notes]" ><code>sales_notes</code> <span><?php echo _JSHOP_IMDEXPORTYML_ID_SALES_NOTES?></span></label>
					<input type="number" id="params[yml_sales_notes]" name="params[yml_sales_notes]" value="<?php echo $ie_params['yml_sales_notes']>0? $ie_params['yml_sales_notes'] : '0'?>" size="5" step="1" min="0">
				</li>
				<li>
					<label for="params[yml_manufacturer_warranty]" ><code>manufacturer_warranty</code> <span><?php echo _JSHOP_IMDEXPORTYML_ID_MANF_WARRANTY?></span></label>
					<input type="number" id="params[yml_manufacturer_warranty]" name="params[yml_manufacturer_warranty]" value="<?php echo (($ie_params['yml_manufacturer_warranty']>0)? $ie_params['yml_manufacturer_warranty'] : '0'); ?>" size="5" step="1" min="0">
				</li>
				<li>
					<label for="params[yml_country_of_origin]" ><code>country_of_origin</code> <span><?php echo _JSHOP_IMDEXPORTYML_ID_COUNTRY_OF_ORIGIN?></span></label>
					<input type="number" id="params[yml_country_of_origin]" name="params[yml_country_of_origin]" value="<?php echo (($ie_params['yml_country_of_origin']>0)? $ie_params['yml_country_of_origin'] : '0'); ?>" size="5" step="1" min="0">
				</li>
				<li>
					<label for="params[yml_enable_auto_discounts]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_ID_ENABLE_AUTO_DISCOUNTS_TIP?>"><code>enable_auto_discounts</code> <span><?php echo _JSHOP_IMDEXPORTYML_ENABLE_AUTO_DISCOUNTS?></span></label>
					<input type="number" id="params[yml_enable_auto_discounts]" name="params[yml_enable_auto_discounts]" value="<?php echo (($ie_params['yml_enable_auto_discounts']>0)? $ie_params['yml_enable_auto_discounts'] : '0'); ?>" size="5" step="1" min="0">
				</li>
			</ul>
			</div>
		</div><!-- /#part2 -->
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'part3', _JSHOP_IMDEXPORTYML_TAB3); ?>
		<div class="row" style="margin: 2em 0 0 0">
			<div class="span4">
			<ul>
				<li>
					<label for="paramscategory_language" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_CATEGORY_LANGUAGE_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_CATEGORY_LANGUAGE?>:</label>
					<?php echo $list_lang;?>
				</li>
				<li>
					<label for="param_trigcat" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_TRIG_CATEGORIES_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_TRIG_CATEGORIES?>:</label>
					<select id="param_trigcat" name="params[param_trigcat]" size="1" onchange="switchListCats()">
						<option <?php if($ie_params['param_trigcat']=="0") echo "selected"; ?> value="0"><?php echo _JSHOP_IMDEXPORTYML_LIST_OFF?></option>
						<option <?php if($ie_params['param_trigcat']=="1") echo "selected"; ?> value="1"><?php echo _JSHOP_IMDEXPORTYML_INCLUDE?></option>
						<option <?php if($ie_params['param_trigcat']=="2") echo "selected"; ?> value="2"><?php echo _JSHOP_IMDEXPORTYML_EXCLUDE?></option>
					</select>
				</li>
				<li>
					<label for="paramslist_categories" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_LIST_CATEGORIES_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_LIST_CATEGORIES?>:</label>
					<?php echo $list_categories;?>
				</li>
				<li class="checkbox">
					<input type="checkbox" name="params[exclude_child]" value="1" <?php if($ie_params['exclude_child']=="1") echo "checked"; ?>>
					<label for="params[exclude_child]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_EXCLUDE_CHILD_CATS_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_EXCLUDE_CHILD_CATS?></label>
				</li>
				<li>
					<label for="params[single_market_category]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_MARKET_CATEGORY_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_MARKET_CATEGORY?>:</label>
					<textarea name="params[single_market_category]" cols="30" rows="3"><?php echo $ie_params['single_market_category']?></textarea>
				</li>
			</ul>
				<li>
					<label for="param_cpa" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_CPA_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_CPA?>:</label> 
					<select id="param_cpa" name="params[cpa]" size="1">
						<option <?php if($ie_params['cpa']=="0") echo "selected"; ?> value="0"><?php echo _JSHOP_IMDEXPORTYML_CPA_NA?></option>
						<option <?php if($ie_params['cpa']=="1") echo "selected"; ?> value="1"><?php echo _JSHOP_IMDEXPORTYML_CPA_NO?></option>
						<option <?php if($ie_params['cpa']=="2") echo "selected"; ?> value="2"><?php echo _JSHOP_IMDEXPORTYML_CPA_YES?></option>
						<option <?php if($ie_params['cpa']=="3") echo "selected"; ?> value="3"><?php echo _JSHOP_IMDEXPORTYML_CPA_AVAILABLE?></option>
						<option <?php if($ie_params['cpa']=="4") echo "selected"; ?> value="4"><?php echo _JSHOP_IMDEXPORTYML_CPA_NOTAVAILABLE?></option>
					</select>
				</li>
				<li>
					<label for="pictures" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_PICTURES_SELECT_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_PICTURES_SELECT?></label> 
					<select id="pictures" name="params[pictures]" size="1">
						<option <?php if($ie_params['pictures']=="1") echo "selected"; ?> value="1"><?php echo _JSHOP_IMDEXPORTYML_MAIN_PICTURE?></option>
						<option <?php if($ie_params['pictures']=="10") echo "selected"; ?> value="10"><?php echo _JSHOP_IMDEXPORTYML_10_PICTURES?></option>
						<option <?php if($ie_params['pictures']=="0") echo "selected"; ?> value="0"><?php echo _JSHOP_IMDEXPORTYML_ALL_PICTURES?></option>
					</select>
				</li>
				<li>
					<label for="param_description" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_DESCRIPTION_FIELD_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_DESCRIPTION_FIELD?>:</label> 
					<select id="param_description" name="params[description]" size="1">
						<option <?php if($ie_params['description']=="0") echo "selected"; ?> value="0"><?php echo _JSHOP_IMDEXPORTYML_SHORT_DESCRIPTION?></option>
						<option <?php if($ie_params['description']=="1") echo "selected"; ?> value="1"><?php echo _JSHOP_IMDEXPORTYML_DESCRIPTION?></option>
						<option <?php if($ie_params['description']=="2") echo "selected"; ?> value="2"><?php echo _JSHOP_IMDEXPORTYML_HTML_DESCRIPTION?></option>
						<option <?php if($ie_params['description']=="3") echo "selected"; ?> value="3"><?php echo _JSHOP_META_DESCRIPTION?></option>
					</select>
				</li>				
			</div>

			<div class="span4">		
			<ul>
				<?php if($list_manufacturers) {?>
				<li>
					<hr>
					<label for="param_trigmanf" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_TRIG_MANUFACTURERS_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_TRIG_MANUFACTURERS?>:</label>
					<select id="param_trigmanf" name="params[param_trigmanf]" default="0" size="1" onchange="switchListManfs()">
						<option <?php if($ie_params['param_trigmanf']=="0") echo "selected";?> value="0"><?php echo _JSHOP_IMDEXPORTYML_LIST_OFF?></option>
						<option <?php if($ie_params['param_trigmanf']=="1") echo "selected";?> value="1"><?php echo _JSHOP_IMDEXPORTYML_INCLUDE?></option>
						<option <?php if($ie_params['param_trigmanf']=="2") echo "selected";?> value="2"><?php echo _JSHOP_IMDEXPORTYML_EXCLUDE?></option>
					</select>
				</li>
				<li>
					<label for="paramslist_manufacturers" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_MANUFACTURERS_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_MANUFACTURERS?>:</label>
					<?php echo $list_manufacturers;?>
				</li>
				<?php }?>
				<li>
					<label for="sales_notes" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_SALES_NOTES_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_SALES_NOTES?>:</label>
					<input id="sales_notes" type="text" name="params[sales_notes]" value="<?php echo $ie_params['sales_notes']?>" size="30" maxlength="50">
				</li>
				<li>
					<label for="sales_notes2" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_SALES_NOTES2_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_SALES_NOTES2?>:</label>
					<input id="sales_notes2" type="text" name="params[sales_notes2]" value="<?php echo $ie_params['sales_notes2']?>" size="30" maxlength="50">
				</li>
				<?php if($html_usergroup) {?>
				<li>
					<label for="paramslist_usergroup" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_USERGROUP_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_USERGROUP?>:</label>
					<?php echo $list_usergroup;?>
				</li>
				<?php } ?>
			</ul>
			</div>

			<div class="span4">		
			<ul>
				<li>
					<label for="params[min_quantity]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_MIN_QUANTITY_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_MIN_QUANTITY?></label>
					<select id="params[min_quantity]" name="params[min_quantity]" default="1" size="1">
						<option value="0" <?php if($ie_params['min_quantity']=="0") echo "selected"?>>0</option>
						<option value="1" <?php if($ie_params['min_quantity']=="1" || !isset($ie_params['min_quantity'])) echo "selected"?>>1</option>
						<option value="2" <?php if($ie_params['min_quantity']=="2") echo "selected"?>>2</option>
						<option value="3" <?php if($ie_params['min_quantity']=="3") echo "selected"?>>3</option>
						<option value="4" <?php if($ie_params['min_quantity']=="4") echo "selected"?>>4</option>
						<option value="5" <?php if($ie_params['min_quantity']=="5") echo "selected"?>>5</option>
						<option value="6" <?php if($ie_params['min_quantity']=="6") echo "selected"?>>6</option>
						<option value="7" <?php if($ie_params['min_quantity']=="7") echo "selected"?>>7</option>
						<option value="8" <?php if($ie_params['min_quantity']=="8") echo "selected"?>>8</option>
						<option value="9" <?php if($ie_params['min_quantity']=="9") echo "selected"?>>9</option>
						<option value="10" <?php if($ie_params['min_quantity']=="10") echo "selected"?>>10</option>
						<option value="100" <?php if($ie_params['min_quantity']=="100") echo "selected"?>>100</option>
					</select>
				</li>
				<li>
					<label for="min_price" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_PRICEFROM_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_PRICEFROM?>:</label>
					<input id="min_price" type="number" name="params[min_price]" value="<?php echo ($ie_params['min_price']) ? $ie_params['min_price'] : 0; ?>" min="-1" max="1000000" onkeyup="value=value.replace(/[^0-9]/g,'')" />
				</li>	
				<li>
					<label for="param_price" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_PRICE_SELECT_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_PRICE_SELECT?>:</label> 
					<select id="param_price" name="params[price]" default="0" size="1">
						<option <?php if($ie_params['price']=="0") echo "selected"; ?> value="0"><?php echo _JSHOP_IMDEXPORTYML_PRICE?></option>
						<option <?php if($ie_params['price']=="1") echo "selected"; ?> value="1"><?php echo _JSHOP_IMDEXPORTYML_MIN_PRICE?></option>
					</select>
				</li>
			</ul>
			</div>
		</div><!-- /#part3 -->
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'part4', _JSHOP_IMDEXPORTYML_TAB4); ?>
		<div class="row" style="margin: 2em 0 0 0">
			<div class="span4">
			<ul>
				<li class="checkbox">
					<input id="params_param" type="checkbox" name="params[param]" value="1" <?php if($ie_params['param']=="1") echo "checked"; ?> onclick="switchParam();">
					<label for="params[param]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_PARAM_CHECKBOX_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_PARAM_CHECKBOX?></label>
				</li>
				<li>
					<label for="param_xfields" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_EXCLUDE_FIELDS_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_EXCLUDE_FIELDS?>:</label>
					<input id="param_xfields" type="text" name="params[exclude_fields]" onkeyup="value=value.replace(/[^0-9\,]/g,'')" value="<?php echo $ie_params['exclude_fields']?>" size="30">
				</li>
				<li>
					<label for="params[paramlist]" class="hasTip" title="<?php print _JSHOP_IMDEXPORTYML_PARAM_LIST_TIP?>"><?php print _JSHOP_IMDEXPORTYML_PARAM_LIST?></label>
					<select id="params[paramlist]" name="params[paramlist]" default="2" size="1">
						<option <?php if($ie_params['paramlist']=="0") print "selected";?> value="0"><?php print _JSHOP_IMDEXPORTYML_PARAM_LIST_ROWS?></option>
						<option <?php if($ie_params['paramlist']=="1") print "selected";?> value="1"><?php print _JSHOP_IMDEXPORTYML_PARAM_LIST_COMA?></option>
						<option <?php if($ie_params['paramlist']=="2") print "selected";?> value="2"><?php print _JSHOP_IMDEXPORTYML_PARAM_LIST_SEMICOLUMN?></option>
						<option <?php if($ie_params['paramlist']=="3") print "selected";?> value="3"><?php print _JSHOP_IMDEXPORTYML_PARAM_LIST_TILDA?></option>
						<option <?php if($ie_params['paramlist']=="9") print "selected";?> value="9"><?php print _JSHOP_IMDEXPORTYML_PARAM_LIST_DEFAULT.' ('.$jshopConfig->multi_charactiristic_separator.')'?></option>
					</select>
				</li>
				<li>
					<label for="params[offerid]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_OFFERID_TIP?>">&lt;offer Id&gt;:</label>
					<select id="params[offerid]" name="params[offerid]" default="0" size="1">
						<option <?php if($ie_params['offerid']=="0") echo "selected"; ?> value="0"><?php echo _JSHOP_IMDEXPORTYML_PRODUCT_ID?></option>
						<option <?php if($ie_params['offerid']=="1") echo "selected"; ?> value="1"><?php echo _JSHOP_IMDEXPORTYML_PRODUCT_EAN?></option>
						<?php if($has_manufacturer_code) {?>
						<option <?php if($ie_params['offerid']=="2") echo "selected"; ?> value="2"><?php echo _JSHOP_IMDEXPORTYML_PRODUCT_MNFCODE?></option>
						<?php }?>
					</select>
				</li>
				<li>
					<label for="params[vendorcode]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_VENDORCODE_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_ID_VENDORCODE?> &lt;vendorCode&gt;:</label>
					<select id="params[vendorcode]" name="params[vendorcode]" default="0" size="1">
						<option <?php if($ie_params['vendorcode']=="0") echo "selected"; ?> value="0"><?php echo _JSHOP_IMDEXPORTYML_PRODUCT_EXFIELD?></option>
						<option <?php if($ie_params['vendorcode']=="1") echo "selected"; ?> value="1"><?php echo _JSHOP_IMDEXPORTYML_PRODUCT_EAN?></option>
						<?php if($has_manufacturer_code) {?>
						<option <?php if($ie_params['vendorcode']=="2") echo "selected"; ?> value="2"><?php echo _JSHOP_IMDEXPORTYML_PRODUCT_MNFCODE?></option>
						<?php }?>
						<option <?php if($ie_params['vendorcode']=="3") echo "selected"; ?> value="3"><?php echo _JSHOP_IMDEXPORTYML_SHORT_DESCRIPTION?></option>
					</select>
				</li>
				<li>
					<label for="params[barcode]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_BARCODE_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_ID_BARCODE;?> &lt;barcode&gt;:</label>
					<select id="params[barcode]" name="params[barcode]" default="0" size="1">
						<option <?php if($ie_params['barcode']=="0") echo "selected"; ?> value="0"><?php echo _JSHOP_IMDEXPORTYML_PRODUCT_EXFIELD?></option>
						<option <?php if($ie_params['barcode']=="1") echo "selected"; ?> value="1"><?php echo _JSHOP_IMDEXPORTYML_PRODUCT_EAN?></option>
						<?php if($has_manufacturer_code) {?>
						<option <?php if($ie_params['barcode']=="2") echo "selected"; ?> value="2"><?php echo _JSHOP_IMDEXPORTYML_PRODUCT_MNFCODE?></option>
						<option <?php if($ie_params['barcode']=="2") echo "selected"; ?> value="2"><?php echo _JSHOP_IMDEXPORTYML_PRODUCT_MNFCODE?></option>
						<?php }?>
						<option <?php if($ie_params['barcode']=="3") echo "selected"; ?> value="3"><?php echo _JSHOP_IMDEXPORTYML_SHORT_DESCRIPTION?></option>
					</select>
				</li>
				<li><hr></li>
				<li style="margin-bottom: 7px;">
					<label for="param_units_list" style="text-align:center; width:100%; max-width:100%;"><b><?php echo _JSHOP_IMDEXPORTYML_UNITS?></b></label>
					<input type="text" name="params[param_units_list]" id="param_units" value="<?php echo $ie_params['param_units_list']?>" style="width:100%; max-width:100%;">
				</li>
				<li style="background: #e6f9e1; padding: 0 5px">
					<small><?php echo _JSHOP_IMDEXPORTYML_UNITS_TIP?></small><br><small><?php echo _JSHOP_IMDEXPORTYML_UNITS_DEFAULT?></small>
				</li>
				<li><hr></li>
				<li style="margin-bottom: 0;">
					<label for="param_units" style="text-align:center; width:100%; max-width:100%;"><b><?php echo _JSHOP_IMDEXPORTYML_UNITS_BY_DATA?></b></label>
					<textarea name="params[units]" cols="60" rows="5" style="width:100%; max-width:100%;"><?php echo $ie_params['units']?></textarea>
				</li>
				<li style="background: #e6f9e1; padding: 0 5px"><small><?php echo _JSHOP_IMDEXPORTYML_UNITS_BY_DATA_TIP?></small></li>
				</ul>
			</div>
			
			<div class="span4">
			<ul>
				<li>
				<label for="params[store]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_STORE_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_STORE?></label>
					<select id="store" name="params[store]" size="1">
						<option <?php if($ie_params['store']=="0") echo "selected"; ?> value="0"><?php echo _JSHOP_IMDEXPORTYML_STORE_OFF?></option>
						<option <?php if($ie_params['store']=="1") echo "selected"; ?> value="1"><?php echo _JSHOP_IMDEXPORTYML_STORE_YES?></option>
						<option <?php if($ie_params['store']=="2") echo "selected"; ?> value="2"><?php echo _JSHOP_IMDEXPORTYML_STORE_NO?></option>
					</select>
				</li>
				<li>
				<label for="params[pickup]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_PICKUP_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_PICKUP?></label>
					<select id="pickup" name="params[pickup]" size="1">
						<option <?php if($ie_params['pickup']=="0") echo "selected"; ?> value="0"><?php echo _JSHOP_IMDEXPORTYML_PICKUP_OFF?></option>
						<option <?php if($ie_params['pickup']=="1") echo "selected"; ?> value="1"><?php echo _JSHOP_IMDEXPORTYML_PICKUP_YES?></option>
						<option <?php if($ie_params['pickup']=="2") echo "selected"; ?> value="2"><?php echo _JSHOP_IMDEXPORTYML_PICKUP_NO?></option>
					</select>
				</li>
				<li>
				<label for="params[delivery]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_DELIVERY_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_DELIVERY?></label>
					<select id="delivery" name="params[delivery]" size="1">
						<option <?php if($ie_params['delivery']=="0") echo "selected"; ?> value="0"><?php echo _JSHOP_IMDEXPORTYML_DELIVERY_OFF?></option>
						<option <?php if($ie_params['delivery']=="1") echo "selected"; ?> value="1"><?php echo _JSHOP_IMDEXPORTYML_DELIVERY_YES?></option>
						<option <?php if($ie_params['delivery']=="2") echo "selected"; ?> value="2"><?php echo _JSHOP_IMDEXPORTYML_DELIVERY_NO?></option>
					</select>
				</li>
				<li>
					<label for="params[delivery_free]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_DELIVERY_FREE_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_DELIVERY_FREE?></label>
					<input id="param_delivery_free" type="number" name="params[delivery_free]" value="<?php echo $ie_params['delivery_free']?>" size="30" min="0" step="100">
				</li>
			</ul>
			</div>

			<div class="span4">
			<ul>
				<li class="text-center"><strong><?php echo _JSHOP_IMDEXPORTYML_COMMON_VALUES;?> &lt;delivery-options&gt;</strong></li>
				<li class="deliv_opt_param">
					<label for="params[delivery_opt_cost]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_DELIVERY_OPT_COST_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_DELIVERY_OPT_COST?></label>
					<input id="param_delivery_opt_cost" type="text" name="params[delivery_opt_cost]" onkeyup="value=value.replace(/[^0-9\.\,]/g,'')" value="<?php echo $ie_params['delivery_opt_cost']?>" size="50">
				</li>
				<li class="deliv_opt_param">
					<label for="params[delivery_opt_days]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_DELIVERY_OPT_DAYS_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_DELIVERY_OPT_DAYS?></label>
					<input id="param_delivery_opt_days" type="text" name="params[delivery_opt_days]" onkeyup="value=value.replace(/[^0-9\-\,]/g,'')" value="<?php echo $ie_params['delivery_opt_days']?>" size="50">
				</li>
				<li class="deliv_opt_param" style="margin-bottom:7px">
					<label for="params[delivery_order_before]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_DELIVERY_ORDER_BEFORE_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_DELIVERY_ORDER_BEFORE?></label>
					<input id="param_delivery_order_before" type="text" name="params[delivery_order_before]" onkeyup="value=value.replace(/[^0-9\-\,]/g,'')" value="<?php echo $ie_params['delivery_order_before']?>" size="50">
				</li>
				<li class="deliv_opt_param" style="background: #e6f9e1; padding: 0 5px"><small><?php echo _JSHOP_IMDEXPORTYML_DELIVERY_OPT_TIP?></small></li>
			</ul>
			</div>
		</div><!-- /#part4 -->
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'part_plus', _JSHOP_IMDEXPORTYML_TAB_PLUS); ?>
		<div class="row" style="margin: 2em 0 0 0">
			<div class="span12">
				<h3><?php echo _JSHOP_IMDEXPORTYML_PLUS_SECTION_HEADER;?></h3>
				<p style="background: #e6f9e1; padding: 0 5px"><small><?php echo _JSHOP_IMDEXPORTYML_CLOTHES_TIP?></small></p>
			</div>
			
			<div class="span4">
				<ul>
				<li class="checkbox">
					<input id="param_clothes" type="checkbox" name="params[clothes]" value="1" <?php if($ie_params['clothes']=="1") echo "checked"; ?> <?php if(!$has_attr) echo 'disabled';?>>
					<label for="params[clothes]"><?php echo _JSHOP_IMDEXPORTYML_CLOTHES_ON?></label>
				</li>
				<li class="checkbox">
					<input id="param_attr" type="checkbox" name="params[attr]" value="1" <?php if($ie_params['attr']=="1") echo "checked"; ?> onclick="switchListAttrs()" <?php // if(!$has_attr) echo 'disabled';?>>
					<label for="params[attr]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_ATTR_CHECKBOX_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_ATTR_CHECKBOX?></label>
				</li>
				<li class="<?php if(!$has_attr) echo ' hidden';?>">
					<label for="params[list_attr]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_INCLUDE_ATTR_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_INCLUDE_ATTR?>:</label>
					<?php if($has_attr) echo $list_attr;?>
				</li>
				<li class="checkbox attributes<?php if(!$has_attr) echo ' hidden';?>">
					<input type="checkbox" name="params[attr_in_url]" value="1" <?php if($ie_params['attr_in_url']=="1") echo "checked"; ?>>
					<label for="params[attr_in_url]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_ATTR_IN_URL_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_ATTR_IN_URL?></label>
				</li>
				<li class="checkbox attributes<?php if(!$has_attr) echo ' hidden';?>">
					<input type="checkbox" name="params[fullname]" value="1" <?php if($ie_params['fullname']=="1") echo "checked"; ?>>
					<label for="params[fullname]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_FULLNAME_TIP?>"><?php echo _JSHOP_IMDEXPORTYML_FULLNAME?></label>
				</li>
				</ul>
				</div>
		</div><!-- /#part_plus -->
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'part5', _JSHOP_IMDEXPORTYML_TAB5); ?>
		<div style="margin: 2em 0 0 0">
			<h3><?php echo _JSHOP_IMDEXPORTYML_SHOP_CUSTOM?></h3>
			<p style="background: #e6f9e1; padding: 0 5px"><small><?php echo _JSHOP_IMDEXPORTYML_SHOP_CUSTOM_TIP?></small></p>
			<ul>
				<li>
					<label for="params[shop_custom_name-1]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_SHOP_CUSTOM_NAME_TIP?>" style="width:14em;"><?php echo _JSHOP_IMDEXPORTYML_CUSTOM_FIELD_NAME?></label>
					<input type="text" name="params[shop_custom_name-1]" value="<?php echo $ie_params['shop_custom_name-1']?>" size="30" default="" onkeyup="value=value.replace(/[\s]/g,'')" style="width: auto; min-width: 20em;">
				</li>
				<li>
					<label for="params[shop_custom_text-1]" class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_SHOP_CUSTOM_TEXT_TIP?>" style="width:14em;"><?php echo _JSHOP_IMDEXPORTYML_SHOP_CUSTOM_TEXT?></label>
					<input type="text" name="params[shop_custom_text-1]" value="<?php echo $ie_params['shop_custom_text-1']?>" size="30" default="" style="width: auto; min-width: 20em;">
				</li>
			</ul>
			<hr>
			<h3><?php echo _JSHOP_IMDEXPORTYML_CUSTOM_FIELD;?></h3>
			<table id="custom_xml" class="table table-condensed" style="max-width:100%; width: 700px;">
				<thead>
					<tr>
						<th class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_CUSTOM_FIELD_NAME_TIP;?>"><?php echo _JSHOP_IMDEXPORTYML_CUSTOM_FIELD_NAME?></th>
						<th class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_CUSTOM_FIELD_DEFAULT_TIP;?>"><?php echo _JSHOP_IMDEXPORTYML_CUSTOM_FIELD_DEFAULT?></th>
						<th class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_CUSTOM_FIELD_ID_TIP;?>"><?php echo _JSHOP_IMDEXPORTYML_CUSTOM_FIELD_ID?></th>
						<th class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_CUSTOM_FIELD_ATTR_NAME_TIP;?>"><?php echo _JSHOP_IMDEXPORTYML_CUSTOM_FIELD_ATTR_NAME?></th>
						<th class="hasTip" title="<?php echo _JSHOP_IMDEXPORTYML_CUSTOM_FIELD_ATTR_VALUE_TIP;?>"><?php echo _JSHOP_IMDEXPORTYML_CUSTOM_FIELD_ATTR_VALUE?></th>
						<th><?php echo _JSHOP_IMDEXPORTYML_CUSTOM_FIELD_REMOVE;?></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach($custom_xml as $i) {?>
					<tr id="row-<?php echo $i?>">
						<td><input type="text" name="params[custom_name-<?php echo $i;?>]" value="<?php echo $ie_params['custom_name-'.$i]; ?>" size="20"></td>
						<td><input type="text" name="params[custom_def-<?php echo $i;?>]" value="<?php echo $ie_params['custom_def-'.$i]; ?>" size="20"></td>
						<td><input type="number" name="params[custom_id-<?php echo $i;?>]" value="<?php echo $ie_params['custom_id-'.$i]>0 ? $ie_params['custom_id-'.$i] : '0'; ?>" size="2" step="1" min="0"></td>
						<td><input type="text" name="params[custom_atr-<?php echo $i;?>]" value="<?php echo $ie_params['custom_atr-'.$i]; ?>" size="20"></td>
						<td><input type="text" name="params[custom_atr_val-<?php echo $i;?>]" value="<?php echo $ie_params['custom_atr_val-'.$i]; ?>" size="20"></td>
						<td class="row-remove" onclick="imdExportYMlRemoveRow(this)"><i class="fa fa-times icon icon-cancel">&nbsp;</i></td>
					</tr>
				<?php }?>
				</tbody>
			</table>
			<div>
				<a href="javascript:void(0);" class="btn btn-info" onclick="imdExportYMlAddRow()">
					<i class="fa fa-plus icon icon-plus-2"></i> <?php echo _JSHOP_IMDEXPORTYML_CUSTOM_FIELD_ADD;?>
				</a>
			</div>
		</div><!-- /#part5 -->
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

	</div><!-- /main-card -->
</form>
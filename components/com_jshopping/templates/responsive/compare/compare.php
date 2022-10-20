<?php defined('_JEXEC') or die();

session_start();

if ($_SESSION['comparep']) {

	$products_arr = array_unique(array_values($_SESSION['comparep']));

}

$jshopConfig = JSFactory::getConfig();

$product = JTable::getInstance('product', 'jshop');

$manufacturer=JTable::getInstance('manufacturer', 'jshop');

JSFactory::loadJsFilesLightBox();

$lang = JSFactory::getLang();



function getProductExtraFieldForProductCompare($product){

	$jshopConfig = JSFactory::getConfig();

	$fields = JSFactory::getAllProductExtraField();

	$fieldvalues = JSFactory::getAllProductExtraFieldValue();

	$hide_fields = $jshopConfig->getProductHideExtraFields();

	$rows = array();

	$_cats = $product->getCategory();
	
	foreach($fields as $fid){

		if (in_array($fid->id, $hide_fields)) continue;
		if (!$fid->allcats){
			if (!in_array($_cats, $fid->cats)) continue;	
		}

		$field_id=$fid->id;

		$field_name = "extra_field_".$field_id;

		if ($fields[$field_id]->type==0){

			if ($product->$field_name!=0){

				$listid = explode(',', $product->$field_name);

				$tmp = array();

				foreach($listid as $extrafiledvalueid){

					$tmp[] = $fieldvalues[$extrafiledvalueid];

				}

				$extra_field_value = implode(", ", $tmp);

				$rows[$field_id] = array("name"=>$fields[$field_id]->name, "description"=>$fields[$field_id]->description, "value"=>$extra_field_value);

			}

		}else{

			if ($product->$field_name!=""){

				$rows[$field_id] = array("name"=>$fields[$field_id]->name, "description"=>$fields[$field_id]->description, "value"=>$product->$field_name);

			}

		}

	}

	return $rows;

}

?>

<div clacc="compare-page">

<h1><?php print JText::_('PLG_COMPARE') ?></h1>

<?php if ($products_arr && count($products_arr)>0) : ?>

<table class="jshop compare_table table table-condensed table-hover">

	<tr>

		<th>

			<?php print JText::_('PLG_DEL') ?>

		</th>

		<?php for ($i = 0; $i < count ($products_arr); $i++) {

			$product->load($products_arr[$i]);

		?>

		<td class="compare_view_<?php print $product->product_id; ?>">

			<a id="removeviewid_<?php print $product->product_id; ?>" class="remove_compare_view" href="#">

				<?php print JText::_('PLG_DEL_COMPARE') ?>

			</a>

		</td>

		<?php } ?>

	</tr>

	<tr class="compare_name">

		<th>

			<?php print JText::_('PLG_NAME') ?>

		</th>

		<?php for ($i = 0; $i < count ($products_arr); $i++) { 

			$product->load($products_arr[$i]);

		?>

		<td class="compare_view_<?php print $product->product_id; ?>">

			<a href="<?php $category = $product->getCategory(); print SEFLink('index.php?option=com_jshopping&controller=product&task=view&category_id='.$category.'&product_id='.$product->product_id,1)?>">

				<?php $name = $lang->get("name"); print $product->$name;?>

			</a>

		</td>

		<?php } ?>

	</tr>

	<tr class="compare_foto">

		<th>

			<?php print JText::_('PLG_IMAGE') ?>

		</th>

		<?php for ($i = 0; $i < count ($products_arr); $i++) {

			$product->load($products_arr[$i]);

		?>

		<td class="compare_view_<?php print $product->product_id; ?>">

			<a class="lightbox" href="<?php print $this->config->image_product_live_path ?>/<?php print "full_".$product->image?>">

				<img src="<?php print $this->config->image_product_live_path ?>/<?php print "thumb_".$product->image?>" alt="">

				<div class="text_zoom">

				<img src="/components/com_jshopping/images/search.png" alt="zoom" /><?php print JText::_('PLG_ZOOM') ?>

				</div>

			</a>

		</td>

		<?php } ?>

	</tr>

	<tr class="compare_rating <?php print $this->_tmp_var_compare_rat ?>">

		<th></th>

		<?php for ($i = 0; $i < count ($products_arr); $i++) {

			$product->load($products_arr[$i]);

		?>

		<td class="compare_view_<?php print $product->product_id; ?>">

			<?php print showMarkStar($product->average_rating); ?>

		</td>

		<?php } ?>

	</tr>

	<tr class="compare_code <?php print $this->_tmp_var_compare_code ?>">

		<th>

			<?php print JText::_('PLG_EAN') ?>

		</th>

		<?php for ($i = 0; $i < count ($products_arr); $i++) {

			$product->load($products_arr[$i]);

		?>

		<td class="compare_view_<?php print $product->product_id; ?>">

			<?php print $product->product_ean; ?>

		</td>

		<?php } ?>

	</tr>

	<tr class="compare_manufacturer <?php print $this->_tmp_var_compare_manufacturer ?>">

		<th>

			<?php print JText::_('PLG_MF') ?>

		</th>

		<?php for ($i = 0; $i < count ($products_arr); $i++) {

			$product->load($products_arr[$i]);

		?>

		<td class="compare_view_<?php print $product->product_id; ?>"><?php $manufacturer_info=$product->getManufacturerInfo(); $product->manufacturer=$manufacturer_info->name;  print htmlspecialchars($product->manufacturer);?>

			<?php if ($manufacturer_info->manufacturer_logo){?>

				<img src="<?php print $this->config->image_manufs_live_path."/".$manufacturer_info->manufacturer_logo;?>" />

			<?php } ?>

		</td>

		<?php } ?>

	</tr>

	<tr class="compare_short_descr <?php print $this->_tmp_var_compare_shortdescr ?>">

		<th>

			<?php print JText::_('PLG_SD') ?>

		</th>

		<?php for ($i = 0; $i < count ($products_arr); $i++) {

			$product->load($products_arr[$i]);

		?>

		<td class="compare_short_descr compare_view_<?php print $product->product_id; ?>">

			<?php $sd = $lang->get("short_description"); print $product->$sd; ?>				

		</td>

		<?php } ?>

	</tr>

	<tr class="compare_price">

		<th>

			<?php print JText::_('PLG_PRICE') ?>

		</th>

		<?php for ($i = 0; $i < count ($products_arr); $i++) {

			$product->load($products_arr[$i]);

		?>

		<td class="compare_view_<?php print $product->product_id; ?>">

			<p><?php print formatprice(getPriceFromCurrency($product->product_price, $product->currency_id));?></p>

			<?php

				$prqty=$product->product_quantity;

				if  ($prqty!=0 && !$jshopConfig->user_as_catalog) { ?>

					<a class="button_buy" href="<?php $category = $product->getCategory(); print SEFLink('index.php?option=com_jshopping&controller=cart&task=add&category_id='.$category.'&product_id='.$product->product_id,1)?>" title="<?php print JText::_('PLG_BUY') ?>">

						<?php print JText::_('PLG_BUY') ?>

					</a>

				<?php } elseif ($prqty==0 && !$jshopConfig->user_as_catalog && $jshopConfig->stock==0) { ?>

					<a class="button_buy" href="<?php $category = $product->getCategory(); print SEFLink('index.php?option=com_jshopping&controller=cart&task=add&category_id='.$category.'&product_id='.$product->product_id,1)?>" title="<?php print JText::_('PLG_BUY') ?>">

						<?php print JText::_('PLG_BUY') ?>

					</a>

				<?php } elseif ($prqty==0 && !$jshopConfig->user_as_catalog){ 

					print "<span style='color:#ff0000'>".JText::_('PLG_EMPTY')."</span>";

				} elseif ($jshopConfig->user_as_catalog){

					print "";

				} ?>

		</td>   

		<?php } ?>

	</tr>

	<tr class="compare_attrib <?php print $this->_tmp_var_compare_attr ?>">

		<th>

			<?php print JText::_('PLG_ATTR') ?>

		</th>

		<?php for ($i = 0; $i < count ($products_arr); $i++) {

			$product->load($products_arr[$i]);

		?>

		<td class="compare_view_<?php print $product->product_id; ?>">

			<?php   

				$product_id=$product->product_id;

				$product->load($product_id);

				$attributesDatas = $product->getAttributesDatas($back_value['attr']);

				$product->setAttributeActive($attributesDatas['attributeActive']);

				$attributeValues = $attributesDatas['attributeValues'];

				

				$attributes = $product->getBuildSelectAttributes($attributeValues, $attributesDatas['attributeSelected']);

				if (count($attributes)) {

					$_attributevalue = JTable::getInstance('AttributValue', 'jshop');

					$all_attr_values = $_attributevalue->getAllAttributeValues();

				} else {

					$all_attr_values = array();

				}

				if (count($attributes)) { ?>

					<div class="jshop_prod_attributes_compare">

						<table class="jshop">

							<?php foreach($attributes as $attribut){;?>

							<tr>

								<td class="attributes_title">

									<span class="attributes_name"><?php print $attribut->attr_name?>:</span>

									<span class="attributes_description"><?php print $attribut->attr_description;?></span>

								</td>

								<td>

									<span id='block_attr_sel_<?php print $attribut->attr_id?>' class="sel_compare">

									<?php print $attribut->selects; ?>

									</span>						

								</td>

							</tr>

							<?php }?>

						</table>

					</div>

				<?php }?>   

			</td>

			<?php } ?>

		</tr>

		<tr class="compare_weight <?php print $this->_tmp_var_compare_weight ?>">

			<th>

				<?php print JText::_('PLG_WEIGHT') ?>

			</th>

			<?php for ($i = 0; $i < count ($products_arr); $i++) {

				$product->load($products_arr[$i]);

			?>

			<td class="compare_view_<?php print $product->product_id; ?>">

				<?php print formatweight($product->product_weight); ?>

			</td>

			<?php } ?>

		</tr>

		<?php

			$j = 0;

			for ($i = 0; $i < count ($products_arr); $i++) { 

				$product->load($products_arr[$i]);

				$prex=getProductExtraFieldForProductCompare($product);	

				if (empty($prex)) continue;

					foreach ($prex as $key=>$v) {

						$exn[$j] = $v['name'];

						$exv[$j] = $v['value'];

						$j++;

					}

			}	

			if ($exn && count ($exn)) {

				$extra_un = array_unique($exn);

				$ex = array_values($extra_un);

			for ($j = 0; $j < count ($ex); $j++) { ?>

				<tr>

					<th>

						<?php print $ex[$j]; ?>

					</th>

					<?php for ($i = 0; $i < count ($products_arr); $i++) {

						$product->load($products_arr[$i]);

					?>

					<td class="extra compare_view_<?php print $product->product_id; ?>">

						<?php 

							$prex=getProductExtraFieldForProductCompare($product);

							if (empty($prex)) {

								print "-"; continue;

							}

							foreach ($prex as $key=>$v) { 

								if ($v['name'] == $ex[$j]) {	  

									print $v['value'];

								}

							}

						?>

					</td>

					<?php } ?>

				</tr>

			<?php }
			} ?>

</table>

<?php else: ?>

	<p><?php print JText::_('PLG_NOSELECT') ?></p>

<?php endif; ?>

</div>
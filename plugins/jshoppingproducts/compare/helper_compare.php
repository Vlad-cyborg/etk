<?php
defined('_JEXEC') or die('Restricted access');
include_once dirname(__FILE__) . '/helper_compare_params.php';
if ($view->product->product_quantity>0){
	$ident_link_prod = $this->params->get('ident_link_prod', '');
} else {
	$ident_link_prod = $this->params->get('ident_link_prod_null', '');
}

if (isset($comparray) && in_array($view->product->product_id, $comparray)){
	$compare_toggle_dnone="compare_dnone";
	$compare_toggle_dblock="";
	$btn_class="in-compare";
} else {
	$compare_toggle_dnone="";
	$compare_toggle_dblock="compare_dnone";
	$btn_class="not-in-compare";
}
$price_calc=formatprice(getPriceFromCurrency($view->product->product_price, $view->product->currency_id));
//$price_calc=formatprice($view->product->product_price,$jshopConfig->currency_code, $jshopConfig->currency_value,0);
$view->$ident_link_prod .=
'<div class="input-append">
<button '.$rt.' data-placement="top" data-original-title="'.$title_add.'" type="button" class="btn list-btn cp '.$btn_class.' '.$hide_i.'">'.$text_link_add_to_compare.'</button>
<input data-compare="name='.htmlspecialchars($view->product->name).'&link='.SEFLink('index.php?option=com_jshopping&controller=product&task=view&category_id='.$view->category_id.'&product_id='.$view->product->product_id.'', 1).'&image='.$view->image_product_path.'/thumb_'.$view->product->image.'&price='.$price_calc.'" type="button" id="compare_'.$view->product->product_id.'" class="btn list-btn button comp_add '.$compare_toggle_dnone.' '.$hide_b.' cp" value="'.$text_link_add_to_compare.'" />
';
$view->$ident_link_prod .=
'<input type="button" id="gotocompare_'.$view->product->product_id.'" class="btn list-btn button go_to_compare '.$compare_toggle_dblock.' '.$hide_b.' cp" value="'.$text_link_go_to_compare.'" onClick="window.location.href=\''.$seflink.'\'" />';
if ($compare_remove=="1"){
$view->$ident_link_prod .=
'<input data-rel="tooltip" data-placement="top" data-original-title="'.$title_del.'" type="button" id="removeid_'.$view->product->product_id.'" class="btn list-btn button remove_comp '.$compare_toggle_dblock.'" value="X" title="'.$title_del.'" />';
}
$view->$ident_link_prod .='</div>';	
?>
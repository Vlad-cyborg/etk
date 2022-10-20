<?php
defined('_JEXEC') or die('Restricted access');
include_once dirname(__FILE__) . '/helper_compare_params.php';
$ident_link_list = $this->params->get('ident_link_list', '');
$jshopConfig = JSFactory::getConfig();
foreach($view->rows As $key => $product){
	if (isset($comparray) && in_array($product->product_id, $comparray)){
		$compare_toggle_dnone="compare_dnone";
		$compare_toggle_dblock="";
	} else {
		$compare_toggle_dnone="";
		$compare_toggle_dblock="compare_dnone";
	}
	//$price_calc=formatprice(getPriceFromCurrency($product->product_price, $product->currency_id,$jshopConfig->currency_value));
	$price_calc=formatprice($product->product_price);
		$view->rows[$key]->$ident_link_list .= '<a '.$rt.' data-placement="top" data-original-title="'.$title_add.'" data-compare="name='.htmlspecialchars($product->name).'&link='.$product->product_link.'&price='.$price_calc.'&image='.$product->image.'" data-id="comparelist_'.$product->product_id.'" class="btn list-btn compare_link_to_list '.$compare_toggle_dnone.'" href="#">'.$text_link_add_to_compare.'</a>';
		$view->rows[$key]->$ident_link_list .= '<a '.$rt.' data-placement="top" data-original-title="'.$title_go.'" data-id="gotocomparelist_'.$product->product_id.'" class="btn list-btn go_to_compre_list '.$compare_toggle_dblock.'" href="'.$seflink.'">'.$text_link_go_to_compare.'</a>';
		if ($compare_remove=="1"){
			$view->rows[$key]->$ident_link_list .= '<a '.$rt.' data-placement="top" data-original-title="'.$title_del.'" data-id="removelistid_'.$product->product_id.'" class="btn list-btn remove_compare_list '.$compare_toggle_dblock.'" href="#" title="'.$title_del.'">x</a>';
		}
}
?>
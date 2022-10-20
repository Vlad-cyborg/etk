<?php
defined('_JEXEC') or die('Restricted access');

class mod_jshopping_compareHelper{	
	public static function ajaxDataInCompare($jshopConfig, $document, $module, $bsv){
	$document->addScriptDeclaration('
	var mod_params_compare, mod_compare_ajax_data;
	mod_params_compare='.$module->params.';
	mod_compare_ajax_data={
	"data_uri":"'.JURI::base().'",
	"data_ilp":"'.$jshopConfig->image_product_live_path.'", 
	"data_cc":"'.$jshopConfig->currency_code.'", 
	"data_to":"'.JText::_('TO_COMPARE').' &rarr;",
	"data_add":"'.JText::_('ADD_TO_COMPARE').'",
	"data_go":"'.JText::_('GO_TO_COMPARE').'",
	"data_del":"'.JText::_('DEL_COMPARE').'",
	"data_max":"'.JText::_('MAX_COMPARE').'",
	"data_added":"'.JText::_('ADDED_COMPARE').'",
	"data_cb":"&larr; '.JText::_('COME_BACK').'",
	"data_ec":"'.JText::_('EMPTY_COMPARE').'",
	"data_bsv":"'.$bsv.'",
	"data_offcheader":"'.JText::_('LABEL_COMPARE_NAME').'",
	"data_controller":"'.JRequest::getVar('controller', null).'"
	};
	');
	}
}
?>
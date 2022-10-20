<?php
/**
 * @name JoomShopping Plugin - Easy select in admin panel
 * @package			Joomla
 * @subpackage		JoomShopping
 * @version			1.0
 * @creationDate	18.05.2020
 *
 * @author			Dmitry Kairlinov (StudioDK-WEB, kit2m2)
 * @authorUrl		http://dk-web.ru/
 * @email			kit2m@mail.ru
 * @copyright		Copyright © 2008 - 2020 StudioDK-WEB. All rights reserved.
 * @license			GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die;

class plgJshoppingAdminEasy_Select extends JPlugin
{

	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}
	
	public function onBeforeEditCategories(&$view)
	{
	// Params settings
		$chosen_select2 = (int)$this->params->get('chosen_select2', 1);
		$loading_script = (int)$this->params->get('loading_script', 0);
		
		$doc = JFactory::getDocument();
		if ($chosen_select2 == 1){
			if ($loading_script == 1){
				JHtml::stylesheet('//cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css', array(), true);
				JHtml::script( '//cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js', true);
			}else{
				$doc->addStyleSheet(JURI::root(true) . '/plugins/jshoppingadmin/easy_select/assets/css/chosen.min.css');
				$doc->addScript(JURI::root(true) . '/plugins/jshoppingadmin/easy_select/assets/js/chosen.jquery.min.js');
			}
			$doc->addCustomTag(
				"<script type=\"text/javascript\">
					jQuery(document).ready(function($){
						$('#main-page select').chosen({
							width: 220,
							no_results_text: '".JText::_('PLG_JSHOPPINGADMIN_EASY_SELECT_NO_RESULT')."',
							placeholder_text_single: '".JText::_('PLG_JSHOPPINGADMIN_EASY_SELECT_CHOOSE_SINGLE_OPTION')."',
							placeholder_text_multiple: '".JText::_('PLG_JSHOPPINGADMIN_EASY_SELECT_CHOOSE_MULTIPLE_OPTIONS')."'});
						$('#main-page select').trigger('chosen:updated');
					})
				</script>");
		}else{
			$doc->addStyleDeclaration('#adminForm input.select2-search__field{width:100%!important;}');
			
			if ($loading_script == 1){
				JHtml::stylesheet('//cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/css/select2.min.css', array(), true);
				JHtml::script( '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/js/select2.min.js', true);
				$doc->addScript(JURI::root(true) . '/plugins/jshoppingadmin/easy_select/assets/js/i18n/ru.js');
			}else{
				$doc->addStyleSheet(JURI::root(true) . '/plugins/jshoppingadmin/easy_select/assets/css/select2.min.css');
				$doc->addScript(JURI::root(true) . '/plugins/jshoppingadmin/easy_select/assets/js/select2.min.js');
				$doc->addScript(JURI::root(true) . '/plugins/jshoppingadmin/easy_select/assets/js/i18n/ru.js');
			}
			$doc->addCustomTag(
				"<script type=\"text/javascript\">
					jQuery(document).ready(function($){
						$('#main-page select').select2({
							language: 'ru',
							width: 'element',
							placeholder: {
								id: '-1',
								text: '".JText::_('PLG_JSHOPPINGADMIN_EASY_SELECT_CHOOSE_MULTIPLE_OPTIONS')."'
							}
						});
						$.fn.select2.defaults.set('language', 'ru');
					})
				</script>");
		}
	}
	
	public function onBeforeDisplayEditProduct(&$product, &$related_products, &$lists, &$listfreeattributes, &$tax_value)
	{
	// Params settings
		$chosen_select2 = (int)$this->params->get('chosen_select2', 1);
		$loading_script = (int)$this->params->get('loading_script', 0);
		
		$doc = JFactory::getDocument();
		if ($chosen_select2 == 1){
			if ($loading_script == 1){
				JHtml::stylesheet('//cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css', array(), true);
				JHtml::script( '//cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js', true);
			}else{
				$doc->addStyleSheet(JURI::root(true) . '/plugins/jshoppingadmin/easy_select/assets/css/chosen.min.css');
				$doc->addScript(JURI::root(true) . '/plugins/jshoppingadmin/easy_select/assets/js/chosen.jquery.min.js');
			}
		//$doc->addStyleDeclaration('.chosen-container{width:100%!important;}');
		$doc->addCustomTag(
			"<script type=\"text/javascript\">
				jQuery(document).ready(function($){
					$('#adminForm select').chosen({
						width: 220,
						no_results_text: '".JText::_('PLG_JSHOPPINGADMIN_EASY_SELECT_NO_RESULT')."',
						placeholder_text_single: '".JText::_('PLG_JSHOPPINGADMIN_EASY_SELECT_CHOOSE_SINGLE_OPTION')."',
						placeholder_text_multiple: '".JText::_('PLG_JSHOPPINGADMIN_EASY_SELECT_CHOOSE_MULTIPLE_OPTIONS')."'});
					$('#adminForm select').trigger('chosen:updated');
				})
			</script>");
		}else{
			$doc->addStyleDeclaration('#adminForm input.select2-search__field{width:100%!important;}');
			
			if ($loading_script == 1){
				JHtml::stylesheet('//cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/css/select2.min.css', array(), true);
				JHtml::script( '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/js/select2.min.js', true);
				$doc->addScript(JURI::root(true) . '/plugins/jshoppingadmin/easy_select/assets/js/i18n/ru.js');
			}else{
				$doc->addStyleSheet(JURI::root(true) . '/plugins/jshoppingadmin/easy_select/assets/css/select2.min.css');
				$doc->addScript(JURI::root(true) . '/plugins/jshoppingadmin/easy_select/assets/js/select2.min.js');
				$doc->addScript(JURI::root(true) . '/plugins/jshoppingadmin/easy_select/assets/js/i18n/ru.js');
			}
			$doc->addCustomTag(
				"<script type=\"text/javascript\">
					jQuery(document).ready(function($){
						$('#adminForm select').select2({
							language: 'ru',
							width: 'element',
							placeholder: {
								id: '-1',
								text: '".JText::_('PLG_JSHOPPINGADMIN_EASY_SELECT_CHOOSE_MULTIPLE_OPTIONS')."'
							}
						});
						$.fn.select2.defaults.set('language', 'ru');
					})
				</script>");
		}
	}
}
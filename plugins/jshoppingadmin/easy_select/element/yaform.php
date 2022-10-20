<?php
/**
 * @name			YaForm
 * @package			Joomla
 * @subpackage		JoomShopping
 * @version			1.0.1
 * @creationDate	20.12.2018
 *
 * @author			Dmitry Kairlinov (StudioDK-WEB, kit2m2)
 * @authorUrl		http://dk-web.ru/
 * @email			kit2m@mail.ru
 * @copyright		Copyright © 2008 - 2018 StudioDK-WEB. All rights reserved.
 * @license			GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('JPATH_PLATFORM') or die;

class JFormFieldYaForm extends JFormField
{

	protected $type = 'YaForm';

	protected function getInput()
	{
		return ' ';
	}

	protected function getLabel()
	{
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('#general .control-label{width:auto;} #general .control-label iframe{min-height:335px;}');
		$html = array();
		$html[] = '<h4>'.JText::_('PLG_JSHOPPING_PLUGIN_YAFORM_TOP_LABEL').'</h4>';
		$html[] = '<iframe frameborder="0" allowtransparency="true" scrolling="no" src="https://money.yandex.ru/embed/shop.xml?account=41001435878243&quickpay=shop&payment-type-choice=on&mobile-payment-type-choice=on&writer=seller&targets=%D0%A1%D0%BF%D0%B0%D1%81%D0%B8%D0%B1%D0%BE+%D0%B0%D0%B2%D1%82%D0%BE%D1%80%D1%83+%D0%B7%D0%B0+%D0%BF%D0%BB%D0%B0%D0%B3%D0%B8%D0%BD+-+Thanks+to+author+of+plugin&targets-hint=&default-sum=&button-text=03&comment=on&hint=&successURL=" width="450" height="268"></iframe>';
		$html[] = '<h4>'.JText::_('PLG_JSHOPPING_PLUGIN_YAFORM_BOTTOM_LABEL').'</h4>';
		$html[] = '<hr>';
		$html[] = '<h4>'.JText::_('PLG_JSHOPPING_PLUGIN_WEBMONEY_LABEL').'</h4>';
		$html[] = '<h4>WMR125653540554</h4>';
		$html[] = '<h4>WMZ271156839629</h4>';
		return implode('', $html);
	}
}

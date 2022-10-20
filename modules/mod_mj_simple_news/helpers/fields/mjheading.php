<?php
/**
 * @package MJ Simple News
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2013 MicroJoom. All Rights Reserved.
 * @author MicroJoom http://www.microjoom.com
 * 
 */
defined('_JEXEC') or die;

if (!class_exists('JFormFieldMjHeading')){
	class JFormFieldMjHeading extends JFormField{
		public function getInput(){
			$html = '<div class="mjheading" >' . $this->value . '</div>';
			$this->addStyleSheet();
			return $html;
		}
		
		protected function addStyleSheet(){
			$document = JFactory::getDocument();
			$document->addStyleDeclaration("
				.mjheading{
					padding: 5px 10px;
					background:none repeat scroll 0 0 #EDEDED;
					color:#0069cc;
					background-color:#e5f3ff;
					border-width:1px;
					border-style:solid;
					border-color: #b3dbff;
					border-left:0;
					border-right:0;
					font-weight:bold;
					clear:both;
				}
			");
			return true;
		}
	};
}
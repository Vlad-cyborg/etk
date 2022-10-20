<?php
defined('_JEXEC') or die('Restricted access');
session_start();
$comparray=array();
$comparray=$_SESSION['comparep'];
$compare_remove = $this->params->get('compare_remove', 1);
$text_or_icons = $this->params->get('text_or_icons', '2');
$compare_link_text = $this->params->get('compare_link_text', '');
if (!trim($compare_link_text)){
	$seflink=SEFLink('index.php?option=com_jshopping&controller=compare&task=view', 1);
} else {
	$seflink="/".trim($compare_link_text);
}

if (JFactory::getApplication()->getLanguageFilter()==1){
	$add_to_compare=JText::_('PLG_ADD_TO_COMPARE');
	$go_to_compare=JText::_('PLG_GO_TO_COMPARE');
	$text_del_compare=JText::_('PLG_DEL_COMPARE');
	$title_add = JText::_('PLG_ADD_TO_COMPARE');
	$title_go = JText::_('PLG_GO_TO_COMPARE');
	$title_del = JText::_('PLG_DEL_COMPARE');
} else {
	$add_to_compare=$this->params->get('text_link_add_to_compare', '');
	$go_to_compare=$this->params->get('text_link_go_to_compare', '');
	$title_add = $this->params->get('text_link_add_to_compare_title', '');
	$title_go = $this->params->get('text_link_go_to_compare_title', '');
	$title_del = JText::_('PLG_DEL_COMPARE');
}

if ($text_or_icons=="1"){
	$text_link_add_to_compare = "<i class='".$this->params->get('text_link_add_to_compare', '')."'></i>";
	$text_link_go_to_compare = "<i class='".$this->params->get('text_link_go_to_compare', '')."'></i>";
	$hide_b="hidden hidden-tablet hidden-phone";
	$hide_i="";
	$rt='data-rel="tooltip"';
} else {
	$text_link_add_to_compare = $add_to_compare;
	$text_link_go_to_compare = $go_to_compare;
	$hide_i="hidden hidden-tablet hidden-phone";
	$hide_b="";
	$rt='';
}

$session =JFactory::getSession();
$session->set('text_link_add_to_compare', $text_link_add_to_compare );
$session->set('text_link_go_to_compare', $text_link_go_to_compare );
?>
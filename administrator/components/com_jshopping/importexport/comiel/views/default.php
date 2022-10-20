<?php
/**
* @package Joomla
* @subpackage JoomShopping
* @author Nevigen.com
* @website https://nevigen.com/
* @email support@nevigen.com
* @copyright Copyright © Nevigen.com. All rights reserved.
* @license Proprietary. Copyrighted Commercial Software
* @license agreement https://nevigen.com/license-agreement.html
**/

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');
$request = JFactory::getApplication()->input;
JFactory::getDocument()->addStyleDeclaration('
	#loading {
		background: url(\'' . JHtml::_('image', 'jui/ajax-loader.gif', '', null, true, true) . '\') 50% 50% no-repeat;
		position: fixed;
		top:0;
		left:0;
		width:100%;
		height:100%;
		z-index:88888888;
		overflow: hidden;
	}
');
?>
<div class="form-horizontal">
    <form action="index.php?option=com_jshopping&controller=importexport&task=view&ie_id=<?= $this->get('ie_id') ?>"
          method="post"
          id="adminForm"
          name="adminForm"
          class="form-validate"
          enctype="multipart/form-data">
        <input type="hidden" name="ie_id" value="<?= $this->get('ie_id') ?>" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="" />
        <input type="hidden" name="active" value="" />
        <?= JHtmlForm::token() ?>
        <div class="form-horizontal">
            <?= JHtmlBootstrap::startTabSet('comielContent', ['active' => $this->get('active', 'export')]); ?>
            <?= JHtmlBootstrap::addTab('comielContent', 'export', JText::_('COMIEL_TAB_EXPORT')); ?>
            <?php include_once(__DIR__ . '/export.php') ?>
            <?= JHtmlBootstrap::endTab(); ?>
            <?= JHtmlBootstrap::addTab('comielContent', 'import', JText::_('COMIEL_TAB_IMPORT')); ?>
            <?php include_once(__DIR__ . '/import.php') ?>
            <?= JHtmlBootstrap::endTab(); ?>
            <?= JHtmlBootstrap::addTab('comielContent', 'actions', JText::_('COMIEL_TAB_ACTIONS')); ?>
            <?php include_once(__DIR__ . '/actions.php') ?>
            <?= JHtmlBootstrap::endTab(); ?>
            <?= JHtmlBootstrap::endTabSet(); ?>
        </div>
    </form>
</div>
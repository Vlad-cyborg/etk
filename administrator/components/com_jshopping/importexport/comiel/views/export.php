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

$viewFiles = $this->get('viewFiles');
$jshopConfig = $this->get('jshopConfig');
$params = $this->get('params');
$form = $this->get('formExport');
?>
<div class="row-fluid">
    <div class="span7">
        <?= $viewFiles->loadTemplate('export') ?>
    </div>
    <div class="span5">
        <div class="btn-group span12">
            <a class="btn btn-primary span6" onclick="Joomla.loadingLayer('show');document.getElementById('adminForm').active.value='export';return Joomla.submitbutton('export');">
                <span class="icon icon-download"></span>
                <?= JText::_('COMIEL_ACTIONS_EXPORT_DATA') ?>
            </a>
            <a class="btn span6" onclick="Joomla.loadingLayer('show');document.getElementById('adminForm').active.value='export';return Joomla.submitbutton('saveConfig');">
                <span class="icon-apply text-success"></span>
                <span class="text-success"><?= JText::_('COMIEL_ACTIONS_SAVE_CONFIG') ?></span>
            </a>
        </div>
        <?php foreach ($form->getFieldsets() as $fieldset) { ?>
            <fieldset>
                <legend><?= JText::_($fieldset->label) ?></legend>
                <?= $form->renderFieldset($fieldset->name) ?>
            </fieldset>
        <?php } ?>
        <div class="btn-group span12">
            <a class="btn btn-primary span6" onclick="Joomla.loadingLayer('show');document.getElementById('adminForm').active.value='export';return Joomla.submitbutton('export');">
                <span class="icon icon-download"></span>
                <?= JText::_('COMIEL_ACTIONS_EXPORT_DATA') ?>
            </a>
            <a class="btn span6" onclick="Joomla.loadingLayer('show');document.getElementById('adminForm').active.value='export';return Joomla.submitbutton('saveConfig');">
                <span class="icon-apply text-success"></span>
                <span class="text-success"><?= JText::_('COMIEL_ACTIONS_SAVE_CONFIG') ?></span>
            </a>
        </div>
    </div>
</div>
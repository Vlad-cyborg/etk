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
?>
<div class="row-fluid">
    <div class="span7">
    </div>
    <div class="span5">
        <div class="row-fluid">
            <div class="control-group">
                <button class="btn span12" disabled="disabled" onclick="Joomla.loadingLayer('show');return Joomla.submitbutton('clearImagesDirectory');">
                    <span class="icon icon-file-remove"></span>
                    <?= JText::_('COMIEL_ACTIONS_CLEAR_IMAGES_DIRECTORY') ?>
                </button>
            </div>
            <div class="control-group">
                <button class="btn span12" disabled="disabled" onclick="Joomla.loadingLayer('show');return Joomla.submitbutton('clearImagesDatabase');">
                    <span class="icon icon-database"></span>
                    <?= JText::_('COMIEL_ACTIONS_CLEAR_IMAGES_DATABASE') ?>
                </button>
            </div>
            <div class="control-group">
                <button class="btn span12" onclick="Joomla.loadingLayer('show');return Joomla.submitbutton('backupImages');">
                    <span class="icon icon-archive"></span>
                    <?= JText::_('COMIEL_ACTIONS_CREATE_BACKUP_IMAGES') ?>
                </button>
            </div>
            <div class="control-group">
                <button class="btn span12" onclick="if(confirm('<?= JText::_('COMIEL_ACTIONS_CREATE_BACKUP_MYSQL_CONFIRM') ?>')){Joomla.loadingLayer('show');return Joomla.submitbutton('backupMySQL');}">
                    <span class="icon icon-box-add"></span>
                    <?= JText::_('COMIEL_ACTIONS_CREATE_BACKUP_MYSQL') ?>
                </button>
            </div>
        </div>
    </div>
</div>
<div class="clr"></div>
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

$actionParams = function ($class, $title) use ($checkbox) {
    return [
        'checkbox' => 'cb_' . $checkbox,
        'active_class' => $class,
        'inactive_class' => $class,
        'active_title' => $title,
        'inactive_title' => $title,
        'tip' => true,
    ];
}
?>
<div class="row-fluid">
    <div class="span12">
        <fieldset>
            <legend><?= $title ?></legend>
            <?php if ($files) { ?>
                <table class="table table-striped" id="exportList">
                    <thead>
                    <tr>
                        <th width="1%" class="nowrap center hidden-phone">
                            <?= JHtmlGrid::checkall('checkall-toggle', 'JGLOBAL_CHECK_ALL', 'Joomla.checkAll(this, \'cb_' . $checkbox . '\')') ?>
                        </th>
                        <th>
                        </th>
                        <th style="width:100px;" class="nowrap center hidden-phone">
                        </th>
                        <th style="width:100px;" class="nowrap center hidden-phone">
                        </th>
                        <th style="width:100px;" class="nowrap center">
                            <a class="hasTooltip" href="javascript:void(0);"
                               onclick="if(confirm('<?= JText::_('COMIEL_ACTIONS_REMOVE_FILES_CONFIRM') ?>'))return Joomla.submitbutton('removeFiles');">
                                <?= JText::_('COMIEL_ACTIONS_REMOVE_FILES') ?>
                            </a>
                        </th>
                    </tr>
                    </thead>
                    <?php $tmpFiles = $files;
                    $files = [];
                    foreach ($tmpFiles as $file) {
                        $timestamp = @filemtime($this->get('path') . DIRECTORY_SEPARATOR . $file);
                        $files[$timestamp . $file] = $file;
                    }
                    ksort($files); ?>
                    <tbody>
                    <?php foreach ($files as $i => $file) { ?>
                        <tr>
                            <td class="nowrap center hidden-phone">
                                <?= JHtmlGrid::id($i, $file, false, 'cid', 'cb_' . $checkbox) ?>
                            </td>
                            <td>
                                <a href="<?= $this->get('url') . '/' . $file ?>">
                                    <?= $file ?>
                                </a>
                            </td>
                            <td class="nowrap center hidden-phone">
                                <?= date(JText::_('DATE_FORMAT_LC2'), @filemtime($this->get('path') . DIRECTORY_SEPARATOR . $file)) ?>
                            </td>
                            <td class="nowrap center hidden-phone">
                                <?php $fileSize = (int)@filesize($this->get('path') . DIRECTORY_SEPARATOR . $file) ?>
                                <?= $fileSize >= 1024 ? number_format($fileSize / 1024) . 'Kb' : number_format($fileSize) . 'b' ?>
                            </td>
                            <td class="nowrap center">
                                <a class="btn btn-micro hasTooltip" href="javascript:void(0);"
                                   onclick="if(confirm('<?= JText::_('COMIEL_ACTIONS_RESTORE_FILE_CONFIRM') ?>')){Joomla.loadingLayer('show');return listItemTask('cb_<?= $checkbox . $i ?>', 'restoreFile');}"
                                   title="<?= JText::_('COMIEL_ACTIONS_RESTORE_FILE') ?>"
                                   data-original-title="<?= JText::_('COMIEL_ACTIONS_RESTORE_FILE') ?>">
                                    <span class="icon-refresh"></span>
                                </a>
                                <a class="btn btn-micro hasTooltip" href="javascript:void(0);"
                                   onclick="if(confirm('<?= JText::_('COMIEL_ACTIONS_REMOVE_FILES_CONFIRM') ?>')){Joomla.loadingLayer('show');return listItemTask('cb_<?= $checkbox . $i ?>', 'removeFile');}"
                                   title="<?= JText::_('COMIEL_ACTIONS_REMOVE_FILE') ?>"
                                   data-original-title="<?= JText::_('COMIEL_ACTIONS_REMOVE_FILE') ?>">
                                    <span class="icon-trash"></span>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </fieldset>
        <?php if (!$files) { ?>
            <?= JText::_('JGLOBAL_NO_MATCHING_RESULTS') ?>
        <?php } ?>
    </div>
</div>
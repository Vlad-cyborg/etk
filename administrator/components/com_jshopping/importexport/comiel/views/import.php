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
$form = $this->get('formImport');
if ($request->getInt('start')) {
	$session = JFactory::getSession();
	$query = [
		'option' => 'com_jshopping',
		'controller' => 'importexport',
		'task' => 'import',
		'ie_id' => $this->get('ie_id'),
		'start' => $session->get('start', 0, 'comiel'),
		'restoreFile' => $session->get('restoreFile', '', 'comiel'),
		JSession::getFormToken() => 1,
	];
	$totalRows = $session->get('totalRows', 0, 'comiel');
	$percent = $totalRows > 0 ? floor(($query['start'] / $totalRows) * 100) : 0;
	$importDuring = microtime(true) - $session->get('importStart', 0, 'comiel');
?>
<script>
jQuery(window).load(function() {
	setTimeout(function(){
		window.location.href = 'index.php?<?php echo http_build_query($query) ?>';
	}, 1000);
});
</script>
<div class="row-fluid">
    <div class="span6">
		<?php echo $query['start']?> / <?php echo $totalRows ?>
    </div>
    <div class="span6 text-right">
		<?php echo JText::_('COMIEL_IMPORT_TIME_FOR_END') ?> <?php echo gmdate('H:i:s', $importDuring / $query['start'] * $totalRows - $importDuring) ?>
    </div>
</div>
<div class="row-fluid">
    <div class="span12">
		<div class="progress progress-striped active">
			<div class="bar" style="width: <?php echo $percent ?>%;"><?php echo $percent ?>%</div>
		</div>
    </div>
</div>
<div id="loading"></div>
<?php } ?>
<div class="row-fluid">
    <div class="span7">
        <?= $viewFiles->loadTemplate('import') ?>
    </div>
    <div class="span5">
        <div class="row-fluid">
            <div class="btn-group span12">
                <a class="btn btn-primary span6" onclick="Joomla.loadingLayer('show');document.getElementById('adminForm').active.value='import';return Joomla.submitbutton('import');">
                    <span class="icon icon-upload"></span>
                    <?= JText::_('COMIEL_ACTIONS_IMPORT_FILE') ?>
                </a>
                <a class="btn span6" onclick="Joomla.loadingLayer('show');document.getElementById('adminForm').active.value='import';return Joomla.submitbutton('saveConfig');">
                    <span class="icon-apply text-success"></span>
                    <span class="text-success"><?= JText::_('COMIEL_ACTIONS_SAVE_CONFIG') ?></span>
                </a>
            </div>
            <?php foreach ($form->getFieldsets() as $fieldset) { ?>
                <fieldset>
                    <legend><?= JText::_($fieldset->label) ?></legend>
                    <div class="control-group">
                        <div class="control-label">
                            <label id="file-lbl" for="file" class="hasPopover" title="" data-content="<?= JText::_('COMIEL_IMPORT_FILE_DESC') ?>" data-original-title="<?= JText::_('COMIEL_IMPORT_FILE') ?>">
                                <?= JText::_('COMIEL_IMPORT_FILE') ?>
                            </label>
                        </div>
                        <div class="controls">
                            <input type="file" name="file" id="file" accept="application/vnd.ms-excel, .xlsx, .csv">
                        </div>
                    </div>
                    <?= $form->renderFieldset($fieldset->name) ?>
                </fieldset>
            <?php } ?>
            <div class="btn-group span12">
                <a class="btn btn-primary span6" onclick="Joomla.loadingLayer('show');document.getElementById('adminForm').active.value='import';return Joomla.submitbutton('import');">
                    <span class="icon icon-upload"></span>
                    <?= JText::_('COMIEL_ACTIONS_IMPORT_FILE') ?>
                </a>
                <a class="btn span6" onclick="Joomla.loadingLayer('show');document.getElementById('adminForm').active.value='import';return Joomla.submitbutton('saveConfig');">
                    <span class="icon-apply text-success"></span>
                    <span class="text-success"><?= JText::_('COMIEL_ACTIONS_SAVE_CONFIG') ?></span>
                </a>
            </div>
        </div>
    </div>
</div>
<div class="clr"></div>
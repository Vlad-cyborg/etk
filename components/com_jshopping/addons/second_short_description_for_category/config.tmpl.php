<?php
    /*
    * @version      1.0.0 07.12.2015
    * @author       MAXXmarketing GmbH
    * @package      addon_second_short_description_for_category
    * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
    * @license      GNU/GPL
    */
    defined('_JEXEC') or die('Restricted access');
    
    JHtml::_('bootstrap.tooltip');
    JHtml::_('formbehavior.chosen', 'select');
    JSFactory::loadExtLanguageFile('addon_second_short_description_for_category');
    JFactory::getDocument()->addStyleDeclaration('.jshop_edit .controls { display: block; }');
    
    $params = (object) $this->params;
    $yes_no_options = array();
    $yes_no_options[] = JHtml::_('select.option', '1', JText::_('JYES'));
    $yes_no_options[] = JHtml::_('select.option', '0', JText::_('JNO'));
?>
<fieldset class="form-horizontal">
    <legend>Second Short Description <?php echo JText::_('JOPTIONS'); ?></legend>
    <div class="control-group">
        <div class="control-label">
            <label class="hasTooltip" title="<?php echo _JSHOP_SSDESCAT_STATE_LIST_DESC; ?>"><?php echo _JSHOP_SSDESCAT_STATE_LIST; ?></label>
        </div>
        <div class="controls">
            <?php echo JHtml::_('select.genericlist', $yes_no_options, 'params[state_list]', 'class="chzn-color-state"', 'value', 'text', ( isset($params->state_list) ? $params->state_list : 1 ) ); ?>
        </div>
    </div>
    <div class="control-group">
        <div class="control-label">
            <label class="hasTooltip" title="<?php echo _JSHOP_SSDESCAT_OUTPUT_POSITION_LIST_DESC; ?>"><?php echo _JSHOP_SSDESCAT_OUTPUT_POSITION_LIST; ?></label>
        </div>
        <div class="controls">
            <input type="text" name="params[output_position_list]" value="<?php echo empty($params->output_position_list) ? 'short_description' : $params->output_position_list; ?>">
        </div>            
    </div>
    <div class="control-group">
        <div class="control-label">
            <label class="hasTooltip" title="<?php echo _JSHOP_SSDESCAT_STATE_CAT_DESC; ?>"><?php echo _JSHOP_SSDESCAT_STATE_CAT; ?></label>
        </div>
        <div class="controls">
            <?php echo JHtml::_('select.genericlist', $yes_no_options, 'params[state_cat]', 'class="chzn-color-state"', 'value', 'text', ( isset($params->state_cat) ? $params->state_cat : 1 ) ); ?>
        </div>
    </div>
    <div class="control-group">
        <div class="control-label">
            <label class="hasTooltip" title="<?php echo _JSHOP_SSDESCAT_OUTPUT_POSITION_CAT_DESC; ?>"><?php echo _JSHOP_SSDESCAT_OUTPUT_POSITION_CAT; ?></label>
        </div>
        <div class="controls">
            <input type="text" name="params[output_position_cat]" value="<?php echo empty($params->output_position_cat) ? '_tmp_category_html_start' : $params->output_position_cat; ?>">
        </div>            
    </div>
</fieldset>
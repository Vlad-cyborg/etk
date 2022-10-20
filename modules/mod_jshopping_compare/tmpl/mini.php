<?php defined('_JEXEC') or die('Restricted access');?>

<?php require JModuleHelper::getLayoutPath('mod_jshopping_compare', '_ccolor');?>

<div id="jshop_module_compare" class="min_view compare-wrapp <?php print $set_color;?>" data-jscolor="<?php print $set_color;?>">
    <div id="jshop_quantity_products" class="compare-header">
        <div class="click_mycompare">
            <span class="<?php print $params->get('font_class');?>" title="<?php print $compare_name; ?>"></span>
            <span class="count_compare"><?php if ($compare_arr && count($compare_arr)>0) {print count($compare_arr);} else {print "0";}?></span>
        </div>
    </div>
    <?php require JModuleHelper::getLayoutPath('mod_jshopping_compare', '_ccontent');?>
</div>
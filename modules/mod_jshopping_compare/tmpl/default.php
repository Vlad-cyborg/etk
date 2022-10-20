<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php require JModuleHelper::getLayoutPath('mod_jshopping_compare', '_ccolor');?>
<div id="jshop_module_compare" class="compare-wrapp <?php print $set_color;?>" data-jscolor="<?php print $set_color;?>">
    <div id="jshop_quantity_products" class="compare-header">
        <p class="click_mycompare">
            <?php print $compare_name; ?> - (<span class="count_compare"><?php if ($compare_arr && count($compare_arr)>0) {print count($compare_arr);} else {print "0";}?></span>)
        </p>
    </div>
    <?php require JModuleHelper::getLayoutPath('mod_jshopping_compare', '_ccontent');?>
</div>
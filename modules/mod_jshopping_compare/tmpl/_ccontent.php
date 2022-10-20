<?php defined('_JEXEC') or die('Restricted access'); ?>
<div class="mycompare_content <?php if ($compare_position=="1"){print "rel_pos";} else {print "abs_pos";} if ($compare_content_show=="0") {print " show";} else {print " dnone";} ?>">
    <div class="mycompare_table extern_wrap_compare">
        <div class="compare_mode_table extern_content_compare">
        <?php if ($compare_arr && count($compare_arr)>0) { ?>
            <?php for ($i = 0; $i < count ($compare_arr); $i++) { 
                $product->load($compare_arr[$i]); ?>
                <div class="compare_mod_<?php print $product->product_id; ?> extern_row_compare">
                    <div class="compare_mod_img pict">
                        <span class="pict">
                            <img src="<?php print $jshopConfig->image_product_live_path ?>/<?php print "thumb_".$product->image; ?>" alt="">
                        </span>
                    </div>
                    <div class="compare_mod_name name">
                        <span class="name">
                            <a href="<?php $category = $product->getCategory(); print SEFLink('index.php?option=com_jshopping&controller=product&task=view&category_id='.$category.'&product_id='.$product->product_id,1)?>"><?php $name = $lang->get("name"); print $product->$name;?></a>
                        </span>
                        <div class="price">
                            <span class="summ"><?php print formatprice(getPriceFromCurrency($product->product_price, $product->currency_id)); ?></span>
                        </div>
                        </div>
                    <div class="compare_mod_remove delete">
                        <span class="delete">
                            <a id="removemodid_<?php print $product->product_id; ?>" class="remove_compare_mod" href="#" title="<?php print JText::_('DEL_COMPARE') ?>">X</a>
                        </span>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
        </div>
    </div>
    <div class="empty_compare_text" data-empty-text="<?php print $empty_text; ?>"></div>
    <div class="extern_bottom_compare">
        <div class="to_compare">
            <span class="to_compare">
                <a class="go_to_compre_mod <?php if (!count($compare_arr)){print "compare_dnone";}?>" href="<?php print $seflink;?>"><?php print JText::_('TO_COMPARE') ?></a>
            </span>
        </div>
    </div>
</div>
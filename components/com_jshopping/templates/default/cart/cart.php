<?php
/**
 * @version      4.16.4 05.10.2017
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */
defined('_JEXEC') or die();

$countprod = count($this->products);
?>
<div class="jshop" id="comjshop">
<?php print $this->checkout_navigator?>

<form action="<?php print SEFLink('index.php?option=com_jshopping&controller=cart&task=refresh') ?>" method="post" name="updateCart">

<?php print $this->_tmp_ext_html_cart_start ?>

<?php if ($countprod > 0) : ?>
    <table class="jshop cart">
    <tr>
        <th class="jshop_img_description_center" width="20%">
            <?php print _JSHOP_IMAGE ?>
        </th>
        <th class="product_name">
            <?php print _JSHOP_ITEM ?>
        </th>
        <th class="single_price" width="15%">
            <?php print _JSHOP_SINGLEPRICE ?>
        </th>
        <th class="quantity" width="18%">
            <?php print _JSHOP_NUMBER ?>
        </th>
        <th class="total_price" width="15%">
            <?php print _JSHOP_PRICE_TOTAL ?>
        </th>
        <th class="remove" width="10%">
            <?php print _JSHOP_REMOVE ?>
        </th>
    </tr>
    <?php
    $i = 1;
    foreach ($this->products as $key_id => $prod){
        echo $prod['_tmp_tr_before'];
    ?>
    <tr class="jshop_prod_cart <?php if ($i % 2 == 0) print "even"; else print "odd"?>">
        <td class="jshop_img_description_center">
            <div class="mobile-cart">
                <?php print _JSHOP_IMAGE; ?>
            </div>
            <div class="data">
                <?php echo $prod['_tmp_img_before']; ?>
                <a href="<?php print $prod['href'] ?>">
                    <img src="<?php print $this->image_product_path ?>/<?php
                    if ($prod['thumb_image'])
                        print $prod['thumb_image'];
                    else
                        print $this->no_image;
                    ?>" alt="<?php print htmlspecialchars($prod['product_name']); ?>" class="jshop_img" />
                </a>
                <?php echo $prod['_tmp_img_after']; ?>
            </div>
        </td>
        <td class="product_name">
            <div class="mobile-cart">
                <?php print _JSHOP_ITEM; ?>
            </div>
            <div class="data">
                <a href="<?php print $prod['href'] ?>">
                    <?php print $prod['product_name'] ?>
                </a>
                <?php if ($this->config->show_product_code_in_cart) { ?>
                    <span class="jshop_code_prod">(<?php print $prod['ean'] ?>)</span>
                <?php } ?>
				<?php print $prod['_ext_product_name'] ?>
                <?php if ($prod['manufacturer'] != '') { ?>
                    <div class="manufacturer"><?php print _JSHOP_MANUFACTURER ?>: <span><?php print $prod['manufacturer'] ?></span></div>
                <?php } ?>
                <?php if ($this->config->manufacturer_code_in_cart && $prod['manufacturer_code']){?>
                    <div class="manufacturer_code"><?php print _JSHOP_MANUFACTURER_CODE ?>: <span><?php print $prod['manufacturer_code'] ?></span></div>
                <?php }?>
                <?php print sprintAtributeInCart($prod['attributes_value']); ?>
                <?php print sprintFreeAtributeInCart($prod['free_attributes_value']); ?>
                <?php print sprintFreeExtraFiledsInCart($prod['extra_fields']); ?>
                <?php print $prod['_ext_attribute_html'] ?>
				<?php if ($this->config->show_delivery_time_step5 && $prod['delivery_times_id']){?>
                    <div class="deliverytime">
                        <?php print _JSHOP_DELIVERY_TIME?>:
                        <?php print $this->deliverytimes[$prod['delivery_times_id']]?>
                    </div>
                <?php }?>
            </div>
        </td>
        <td class="single_price">
            <div class="mobile-cart">
                <?php print _JSHOP_SINGLEPRICE; ?>
            </div>
            <div class="data">
                <?php print formatprice($prod['price']) ?>
                <?php print $prod['_ext_price_html'] ?>
                <?php if ($this->config->show_tax_product_in_cart && $prod['tax'] > 0) { ?>
                    <span class="taxinfo"><?php print productTaxInfo($prod['tax']); ?></span>
                <?php } ?>
                <?php if ($this->config->cart_basic_price_show && $prod['basicprice'] > 0) { ?>
                    <div class="basic_price">
                        <?php print _JSHOP_BASIC_PRICE ?>:
                        <span><?php print sprintBasicPrice($prod); ?></span>
                    </div>
                <?php } ?>
            </div>
        </td>
        <td class="quantity">
            <div class="mobile-cart">
                <?php print _JSHOP_NUMBER; ?>
            </div>
            <div class="data">
				<?php if ($prod['not_qty_update']){?>
					<span class="qtyval"><?php print $prod['quantity'] ?></span>
				<?php }else{?>
					<span class = "cart_minus">
						<img src="<?php print $this->image_path?>images/minus.png" name = "quantity[<?php print $key_id ?>]" title="<?php print _JSHOP_UPDATE_CART ?>" alt = "<?php print _JSHOP_UPDATE_CART ?>" onclick="QuantityMinus(document.updateCart, name);" />
					</span>
					<input type = "text" name = "quantity[<?php print $key_id ?>]" value = "<?php print $prod['quantity'] ?>" class = "inputbox" onchange="document.updateCart.submit();" />
                    <span class = "cart_plus">
						<img src="<?php print $this->image_path?>images/plus.png" name = "quantity[<?php print $key_id ?>]" title="<?php print _JSHOP_UPDATE_CART ?>" alt = "<?php print _JSHOP_UPDATE_CART ?>" onclick="QuantityPlus(document.updateCart, name);" />
					</span>
				<?php }?>
                <?php print $prod['_qty_unit']; ?>
				<?php if (!$prod['not_qty_update']){?>
				<?php }?>
            </div>
        </td>
        <td class="total_price">
            <div class="mobile-cart">
                <?php print _JSHOP_PRICE_TOTAL; ?>
            </div>
            <div class="data">
                <?php print formatprice($prod['price'] * $prod['quantity']); ?>
                <?php print $prod['_ext_price_total_html'] ?>
                <?php if ($this->config->show_tax_product_in_cart && $prod['tax'] > 0) { ?>
                    <span class="taxinfo"><?php print productTaxInfo($prod['tax']); ?></span>
                <?php } ?>
            </div>
        </td>
        <td class="remove">
            <div class="mobile-cart">
                <?php print _JSHOP_REMOVE; ?>
            </div>
            <div class="data">
                <?php if ($prod['not_delete']) { ?>
                    <?php echo $prod['not_delete_html'] ? $prod['not_delete_html'] : '-'; ?>
                <?php } else { ?>
                    <a class="button-img" href="<?php print $prod['href_delete']?>" onclick="return confirm('<?php print _JSHOP_CONFIRM_REMOVE?>')">
                        <img src = "<?php print $this->image_path ?>images/remove.png" alt = "<?php print _JSHOP_DELETE?>" title = "<?php print _JSHOP_DELETE?>" />
                    </a>
                <?php } ?>
            </div>
        </td>
    </tr>
    <?php
        echo $prod['_tmp_tr_after'];
        $i++;
    }
    ?>
    </table>

    <?php if ($this->config->show_cart_clear){?>
        <div class="clear-cart">
            <a class="btn clear-cart" href="<?php print SEFLink('index.php?option=com_jshopping&controller=cart&task=clear')?>" onclick="return confirm('<?php print _JSHOP_CONFIRM_REMOVE_ALL?>')">
                <?php print _JSHOP_CLEAR_CART?>
            </a>
        </div>
    <?php }?>

    <?php if ($this->config->show_weight_order) : ?>
        <div class = "weightorder">
            <?php print _JSHOP_WEIGHT_PRODUCTS?>: <span><?php print formatweight($this->weight);?></span>
        </div>
    <?php endif; ?>

    <?php if ($this->config->summ_null_shipping > 0) : ?>
        <div class = "shippingfree">
            <?php printf(_JSHOP_FROM_PRICE_SHIPPING_FREE, formatprice($this->config->summ_null_shipping, null, 1));?>
        </div>
    <?php endif; ?>

    <div class = "cartdescr"><?php print $this->cartdescr; ?></div>

    <table class="jshop jshop_subtotal">
        <?php if (!$this->hide_subtotal){?>
            <tr class="subtotal">
                <td class="name">
                    <?php print _JSHOP_SUBTOTAL ?>
                </td>
                <td class="value">
                    <?php print formatprice($this->summ);?><?php print $this->_tmp_ext_subtotal?>
                </td>
            </tr>
        <?php } ?>

        <?php print $this->_tmp_html_after_subtotal?>

        <?php if ($this->discount > 0){ ?>
            <tr class="discount">
                <td class="name">
                    <?php print _JSHOP_RABATT_VALUE ?><?php print $this->_tmp_ext_discount_text?>
                </td>
                <td class="value">
                    <?php print formatprice(-$this->discount);?><?php print $this->_tmp_ext_discount?>
                </td>
            </tr>
        <?php } ?>
        <?php if (!$this->config->hide_tax){?>
            <?php foreach($this->tax_list as $percent=>$value){ ?>
                <tr class="tax">
                    <td class = "name">
                        <?php print displayTotalCartTaxName();?>
                        <?php if ($this->show_percent_tax) print formattax($percent)."%"?>
                    </td>
                    <td class = "value">
                        <?php print formatprice($value);?><?php print $this->_tmp_ext_tax[$percent]?>
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>

        <tr class="total">
            <td class = "name">
                <?php print _JSHOP_PRICE_TOTAL ?>
            </td>
            <td class = "value">
                <?php print formatprice($this->fullsumm)?><?php print $this->_tmp_ext_total?>
            </td>
        </tr>

        <?php print $this->_tmp_html_after_total?>

        <?php if ($this->config->show_plus_shipping_in_product){?>
            <tr class="plusshipping">
                <td colspan="2" align="right">
                    <span class="plusshippinginfo"><?php print sprintf(_JSHOP_PLUS_SHIPPING, $this->shippinginfo);?></span>
                </td>
            </tr>
        <?php }?>

        <?php if ($this->free_discount > 0){?>
            <tr class="free_discount">
                <td colspan="2" align="right">
                    <span class="free_discount"><?php print _JSHOP_FREE_DISCOUNT;?>: <?php print formatprice($this->free_discount); ?></span>
                </td>
            </tr>
        <?php }?>

    </table>
<?php else : ?>
    <div class="cart_empty_text"><?php print _JSHOP_CART_EMPTY?></div>
<?php endif; ?>

<?php print $this->_tmp_html_before_buttons?>
<div class = "jshop cart_buttons">
    <div id = "checkout">
        <div class = "pull-left td_1">
            <a href = "<?php print $this->href_shop ?>" class="btn btn-arrow-left">
                <?php print _JSHOP_BACK_TO_SHOP ?>
            </a>
        </div>
        <div class = "pull-right td_2">
        <?php if ($countprod>0) : ?>
            <a href = "<?php print $this->href_checkout ?>" class="btn btn-arrow-right">
                <?php print _JSHOP_CHECKOUT ?>
            </a>
        <?php endif; ?>
        </div>
        <div class = "clearfix"></div>
    </div>
</div>

<?php print $this->_tmp_html_after_buttons?>

</form>

<?php print $this->_tmp_ext_html_before_discount?>

<?php if ($this->use_rabatt && $countprod>0) : ?>
    <div class="cart_block_discount">
        <form name="rabatt" method="post" action="<?php print SEFLink('index.php?option=com_jshopping&controller=cart&task=discountsave'); ?>">
            <div class = "row-fluid jshop">
                <div class = "span12">
                    <div class="name"><?php print _JSHOP_RABATT ?></div>
                    <input type = "text" class = "inputbox" name = "rabatt" value = "" />
                    <input type = "submit" class = "button btn" value = "<?php print _JSHOP_RABATT_ACTIVE ?>" />
                </div>
            </div>
        </form>
    </div>
<?php endif; ?>

</div>
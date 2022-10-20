<?php
/**
* @version      4.16.3 25.05.2017
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
$product = $this->product;
include(dirname(__FILE__)."/load.js.php");
?>
<div class="jshop productfull" id="comjshop">
    <form name="product" method="post" action="<?php print $this->action?>" enctype="multipart/form-data" autocomplete="off">

        <h1><?php print $this->product->name?><?php if ($this->config->show_product_code){?> <span class="jshop_code_prod">(<?php print _JSHOP_EAN?>: <span id="product_code"><?php print $this->product->getEan();?></span>)</span><?php }?></h1>

        <?php print $this->_tmp_product_html_start;?>

        <?php if ($this->config->display_button_print) print printContent();?>

        <?php include(dirname(__FILE__)."/ratingandhits.php");?>

        <div class="row-fluid jshop">
            <div class="span4 image_middle">

                <?php print $this->_tmp_product_html_before_image;?>

                <?php if ($product->label_id){?>
                    <div class="product_label">
                        <?php if ($product->_label_image){?>
                            <img src="<?php print $product->_label_image?>" alt="<?php print htmlspecialchars($product->_label_name)?>" />
                        <?php }else{?>
                            <span class="label_name"><?php print $product->_label_name;?></span>
                        <?php }?>
                    </div>
                <?php }?>

                <?php if (count($this->videos)){?>
                    <?php foreach($this->videos as $k=>$video){?>
                        <?php if ($video->video_code){ ?>
                            <div style="display:none" class="video_full" id="hide_video_<?php print $k?>"><?php echo $video->video_code?></div>
                        <?php } else { ?>
                            <a style="display:none" class="video_full" id="hide_video_<?php print $k?>" href=""></a>
                        <?php } ?>
                    <?php } ?>
                <?php }?>

                <span id='list_product_image_middle'>
                    <?php print $this->_tmp_product_html_body_image?>

                    <?php if(!count($this->images)){?>
                        <img id = "main_image" src = "<?php print $this->image_product_path?>/<?php print $this->noimage?>" alt = "<?php print htmlspecialchars($this->product->name)?>" />
                    <?php }?>

                    <?php foreach($this->images as $k=>$image){?>
                        <a class="lightbox" id="main_image_full_<?php print $image->image_id?>" href="<?php print $this->image_product_path?>/<?php print $image->image_full;?>" <?php if ($k!=0){?>style="display:none"<?php }?> title="<?php print htmlspecialchars($image->_title)?>">
                            <img id = "main_image_<?php print $image->image_id?>" src = "<?php print $this->image_product_path?>/<?php print $image->image_name;?>" alt="<?php print htmlspecialchars($image->_title)?>" title="<?php print htmlspecialchars($image->_title)?>" />
                            <div class="text_zoom">
                                <img src="<?php print $this->path_to_image?>search.png" alt="zoom" />
                                <?php print _JSHOP_ZOOM_IMAGE?>
                            </div>
                        </a>
                    <?php }?>
                </span>

                <?php print $this->_tmp_product_html_after_image;?>

                <?php if ($this->config->product_show_manufacturer_logo && $this->product->manufacturer_info->manufacturer_logo!=""){?>
                <div class="manufacturer_logo">
                    <a href="<?php print SEFLink('index.php?option=com_jshopping&controller=manufacturer&task=view&manufacturer_id='.$this->product->product_manufacturer_id, 2);?>">
                        <img src="<?php print $this->config->image_manufs_live_path."/".$this->product->manufacturer_info->manufacturer_logo?>" alt="<?php print htmlspecialchars($this->product->manufacturer_info->name);?>" title="<?php print htmlspecialchars($this->product->manufacturer_info->name);?>" border="0" />
                    </a>
                </div>
                <?php }?>
            </div>








            <div class = "span8 jshop_img_description">
                <?php print $this->_tmp_product_html_before_image_thumb;?>

                <span id='list_product_image_thumb'>
                    <?php if ( (count($this->images)>1) || (count($this->videos) && count($this->images)) ) {?>
                        <?php foreach($this->images as $k=>$image){?>
                            <img class="jshop_img_thumb" src="<?php print $this->image_product_path?>/<?php print $image->image_thumb?>" alt="<?php print htmlspecialchars($image->_title)?>" title="<?php print htmlspecialchars($image->_title)?>" onclick="showImage(<?php print $image->image_id?>)" />
                        <?php }?>
                    <?php }?>
                </span>

                <?php print $this->_tmp_product_html_after_image_thumb;?>

                <?php if (count($this->videos)){?>
                    <?php foreach($this->videos as $k=>$video){?>
                        <?php if ($video->video_code) { ?>
                            <a href="#" id="video_<?php print $k?>" onclick="showVideoCode(this.id);return false;"><img class="jshop_video_thumb" src="<?php print $this->video_image_preview_path."/"; if ($video->video_preview) print $video->video_preview; else print 'video.gif'?>" alt="video" /></a>
                        <?php } else { ?>
                            <a href="<?php print $this->video_product_path?>/<?php print $video->video_name?>" id="video_<?php print $k?>" onclick="showVideo(this.id, '<?php print $this->config->video_product_width;?>', '<?php print $this->config->video_product_height;?>'); return false;"><img class="jshop_video_thumb" src="<?php print $this->video_image_preview_path."/"; if ($video->video_preview) print $video->video_preview; else print 'video.gif'?>" alt="video" /></a>
                        <?php } ?>
                    <?php } ?>
                <?php }?>

                <?php print $this->_tmp_product_html_after_video;?>
            </div>


                    <?php if (is_array($this->product->extra_field)){?>
                        <div class="extra_fields">
                        <?php foreach($this->product->extra_field as $extra_field){?>
                            <?php if ($extra_field['grshow']){?>
                                <div class='block_efg'>
                                <div class='extra_fields_group'><?php print $extra_field['groupname']?></div>
                            <?php }?>

                            <div class="extra_fields_el">
                                <span class="extra_fields_name"><?php print $extra_field['name'];?></span><?php if ($extra_field['description']){?>
                                    <span class="extra_fields_description">
                                        <?php print $extra_field['description'];?>
                                    </span><?php } ?>:
                                <span class="extra_fields_value">
                                    <?php print $extra_field['value'];?>
                                </span>
                            </div>

                            <?php if ($extra_field['grshowclose']){?>
                                </div>
                            <?php }?>
                        <?php }?>
                          <?php print $this->_tmp_product_html_my_after_buttons; ?>
                        </div>
                    <?php }?>

        </div>




        <div class="old_price" <?php if ($this->product->product_old_price == 0){?>style="display:none"<?php }?>>
    			<?php print _JSHOP_OLD_PRICE?>:
    			<span class="old_price" id="old_price">
    				<?php print formatprice($this->product->product_old_price)?>
    				<?php print $this->product->_tmp_var_old_price_ext;?>
    			</span>
    		</div>

            <?php if ($this->product->product_price_default > 0 && $this->config->product_list_show_price_default){?>
                <div class="default_price"><?php print _JSHOP_DEFAULT_PRICE?>: <span id="pricedefault"><?php print formatprice($this->product->product_price_default)?></span></div>
            <?php }?>

            <?php print $this->_tmp_product_html_before_price;?>

            <?php if ($this->product->_display_price){?>
                <div class="prod_price">
                    <?php print _JSHOP_PRICE?>:
                    <span id="block_price">
            <?php 
            if($product->product_price == 0){
                $price = "Цена по запросу";
            }else{
                $price =formatprice($this->product->getPriceCalculate());
            }
            ?>
                        <?php print $price?>
                        <?php print $this->product->_tmp_var_price_ext;?>
                    </span>
                </div>
            <?php }?>

            <?php print $this->product->_tmp_var_bottom_price;?>

            <?php if ($this->config->show_tax_in_product && $this->product->product_tax > 0){?>
                <span class="taxinfo"><?php print productTaxInfo($this->product->product_tax);?></span>
            <?php }?>

            <?php if ($this->config->show_plus_shipping_in_product){?>
                <span class="plusshippinginfo"><?php print sprintf(_JSHOP_PLUS_SHIPPING, $this->shippinginfo);?></span>
            <?php }?>

            <?php if ($this->product->delivery_time != ''){?>
                <div class="deliverytime" <?php if ($product->hide_delivery_time){?>style="display:none"<?php }?>><?php print _JSHOP_DELIVERY_TIME?>: <?php print $this->product->delivery_time?></div>
            <?php }?>

            <?php if ($this->config->product_show_weight && $this->product->product_weight > 0){?>
                <div class="productweight"><?php print _JSHOP_WEIGHT?>: <span id="block_weight"><?php print formatweight($this->product->getWeight())?></span></div>
            <?php }?>

            <?php if ($this->product->product_basic_price_show){?>
                <div class="prod_base_price"><?php print _JSHOP_BASIC_PRICE?>: <span id="block_basic_price"><?php print formatprice($this->product->product_basic_price_calculate)?></span> / <?php print $this->product->product_basic_price_unit_name;?></div>
            <?php }?>

            <?php print $this->product->_tmp_var_bottom_allprices;?>

            <?php print $this->_tmp_product_html_after_ef;?>

            <?php if ($this->product->vendor_info){?>
                <div class="vendorinfo">
                    <?php print _JSHOP_VENDOR?>: <?php print $this->product->vendor_info->shop_name?> (<?php print $this->product->vendor_info->l_name." ".$this->product->vendor_info->f_name;?>),
                    (
                    <?php if ($this->config->product_show_vendor_detail){?><a href="<?php print $this->product->vendor_info->urlinfo?>"><?php print _JSHOP_ABOUT_VENDOR?></a>,<?php }?>
                    <a href="<?php print $this->product->vendor_info->urllistproducts?>"><?php print _JSHOP_VIEW_OTHER_VENDOR_PRODUCTS?></a> )
                </div>
            <?php }?>

            <?php if (!$this->config->hide_text_product_not_available){ ?>
                <div class = "not_available" id="not_available"><?php print $this->available?></div>
            <?php }?>

            <?php if ($this->config->product_show_qty_stock){?>
                <div class="qty_in_stock">
                    <?php print _JSHOP_QTY_IN_STOCK?>:
                    <span id="product_qty"><?php print sprintQtyInStock($this->product->qty_in_stock);?></span>
                </div>
            <?php }?>

            <?php print $this->_tmp_product_html_before_buttons;?>

            <?php if (!$this->hide_buy){?>
                <div class="prod_buttons" style="<?php print $this->displaybuttons?>">

                    <div class="prod_qty">
                        <?php print _JSHOP_QUANTITY?>:
                    </div>

                    <div class="prod_qty_input">
                        <input type="text" name="quantity" id="quantity" onkeyup="reloadPrices();" class="inputbox" value="<?php print $this->default_count_product?>" /><?php print $this->_tmp_qty_unit;?>
                    </div>

                    <div class="buttons product-buttons">
                        <input type="submit" class="btn btn-primary button btn-buy" value="<?php print _JSHOP_ADD_TO_CART?>" onclick="jQuery('#to').val('cart');" />

                        <?php if ($this->enable_wishlist){?>
                            <input type="submit" class="btn button btn-wishlist" value="<?php print _JSHOP_ADD_TO_WISHLIST?>" onclick="jQuery('#to').val('wishlist');" />
                        <?php }?>

                        <?php print $this->_tmp_product_html_buttons;?>
                    </div>
                    <div id="jshop_image_loading" style="display:none"></div>
                </div>
            <?php }?>







        <?php if ($this->product->product_url!=""){?>
            <div class="prod_url">
                <a target="_blank" href="<?php print $this->product->product_url;?>"><?php print _JSHOP_READ_MORE?></a>
            </div>
        <?php }?>

        <?php if ($this->config->product_show_manufacturer && $this->product->manufacturer_info->name!=""){?>
            <div class="manufacturer_name">
                <?php print _JSHOP_MANUFACTURER?>: <span><?php print $this->product->manufacturer_info->name?></span>
            </div>
        <?php }?>

		<?php if ($this->config->manufacturer_code_in_product_detail && $this->product->getManufacturerCode()!=""){?>
            <div class="manufacturer_code">
                <?php print _JSHOP_MANUFACTURER_CODE?>: <span id="manufacturer_code"><?php print $this->product->getManufacturerCode()?></span>
            </div>
        <?php }?>

        <?php print $this->_tmp_product_html_before_atributes;?>

        <?php if (count($this->attributes)) : ?>
            <div class="jshop_prod_attributes jshop">
                <?php foreach($this->attributes as $attribut) : ?>
                    <?php if ($attribut->grshow){?>
                        <div>
                            <span class="attributgr_name"><?php print $attribut->groupname?></span>
                        </div>
                    <?php }?>
                    <div class="row-fluid row-attr-<?php print $attribut->attr_id?>">
                        <div class="span2 attributes_title">
                            <span class="attributes_name"><?php print $attribut->attr_name?>:</span>
                            <span class="attributes_description"><?php print $attribut->attr_description;?></span>
                        </div>
                        <div class = "span10">
                            <span id='block_attr_sel_<?php print $attribut->attr_id?>'>
                                <?php print $attribut->selects?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php print $this->_tmp_product_html_after_atributes;?>

        <?php if (isset($this->product->freeattributes) && count($this->product->freeattributes)){?>
            <div class="prod_free_attribs jshop">
                <?php foreach($this->product->freeattributes as $freeattribut){?>
                    <div class="row-fluid row-free-attr-<?php print $freeattribut->id?>">
                        <div class="span2 name">
                            <span class="freeattribut_name"><?php print $freeattribut->name;?></span>
                            <?php if ($freeattribut->required){?><span>*</span><?php }?>
                            <span class="freeattribut_description"><?php print $freeattribut->description;?></span>
                        </div>
                        <div class="span10 field">
                            <?php print $freeattribut->input_field;?>
                        </div>
                    </div>
                <?php }?>
                <?php if ($this->product->freeattribrequire) {?>
                    <div class="requiredtext">* <?php print _JSHOP_REQUIRED?></div>
                <?php }?>
            </div>
        <?php }?>

        <?php print $this->_tmp_product_html_after_freeatributes;?>

        <?php if ($this->product->product_is_add_price){?>
            <div class="price_prod_qty_list_head"><?php print _JSHOP_PRICE_FOR_QTY?></div>
            <table class="price_prod_qty_list">
                <?php foreach($this->product->product_add_prices as $k=>$add_price){?>
                    <tr>
                        <td class="qty_from" <?php if ($add_price->product_quantity_finish==0){?>colspan="3"<?php } ?>>
                            <?php if ($add_price->product_quantity_finish==0) print _JSHOP_FROM?>
                            <?php print $add_price->product_quantity_start?>
                            <?php print $this->product->product_add_price_unit?>
                        </td>

                        <?php if ($add_price->product_quantity_finish > 0){?>
                            <td class="qty_line"> - </td>
                        <?php } ?>

                        <?php if ($add_price->product_quantity_finish > 0){?>
                            <td class="qty_to">
                                <?php print $add_price->product_quantity_finish?> <?php print $this->product->product_add_price_unit?>
                            </td>
                        <?php } ?>

                        <td class="qty_price">
                            <span id="pricelist_from_<?php print $add_price->product_quantity_start?>">
                                <?php print formatprice($add_price->price)?><?php print $add_price->ext_price?>
                            </span>
                            <span class="per_piece">/ <?php print $this->product->product_add_price_unit?></span>
                        </td>
						<?php print $add_price->_tmp_var?>
                    </tr>
                <?php }?>
            </table>
        <?php }?>



        <?php print $this->_tmp_product_html_after_buttons;?>

        <input type="hidden" name="to" id='to' value="cart" />
        <input type="hidden" name="product_id" id="product_id" value="<?php print $this->product->product_id?>" />
        <input type="hidden" name="category_id" id="category_id" value="<?php print $this->category_id?>" />
    </form>






    <div id="modal_form_bay">
      <span id="modal_close_bay">X</span>
      <p style="margin-bottom:20px;">Для быстрой покупки заполните поля:</p>
      <form action="" method="POST">
        <input required type="text" value="" placeholder="Телефон" name="price_tel">
        <input type="text" value="" placeholder="Город" name="price_name">
        <input type="text" style="display:none;" value="" name="price_hid">
        <div class="personal">
          <input style="margin:5px;cursor:pointer;width: 10px;" type="checkbox" name="checkme" id="agree2" checked>
          «Нажимая на кнопку, Вы даете согласие на обработку своих персональных данных,
          с <a href="/personal" target="_blank">положением об обработке персональных данных</a>
          и <a href="/policy" target="_blank">политикой в отношении обработки персональных данных</a> ознакомлен»
          </div>
          <script type="text/javascript">
          $(document).ready(function(){
            $('#continue2').prop('disabled', false);
            $('#agree2').change(function() {
              $('#continue2').prop('disabled', function(i, val) {
              return !val;
              })
            });
          })
          </script>
        <input id="continue2" type="submit" value="КУПИТЬ" name="quick_bay" style="background: #b22 none repeat scroll 0 0;color: #fff;width: 110px;margin-top:0px;">
      </form>
    </div>
    <a href="#" id="modal_a_bay">КУПИТЬ в 1 клик</a>

    <div id="modal_form">
      <span id="modal_close">X</span>
      <p>Получить подробный каталог</p>
      <form action="" method="POST">
        <input type="text" value="" placeholder="Имя" name="price_name">
        <input type="text" value="" placeholder="Email" name="price_email">
        <input required type="text" value="" placeholder="Телефон" name="price_tel">
        <input type="text" style="display:none;" value="" name="price_hid">
        <div class="personal">
          <input style="margin:5px;cursor:pointer;width: 10px;" type="checkbox" name="checkme" id="agree3" checked>
          «Нажимая на кнопку, Вы даете согласие на обработку своих персональных данных,
          с <a href="/personal" target="_blank">положением об обработке персональных данных</a>
          и <a href="/policy" target="_blank">политикой в отношении обработки персональных данных</a> ознакомлен»
          </div>
          <script type="text/javascript">
          $(document).ready(function(){
            $('#continue3').prop('disabled', false);
            $('#agree3').change(function() {
              $('#continue3').prop('disabled', function(i, val) {
              return !val;
              })
            });
          })
          </script>
        <input id="continue3" type="submit" value="ЗАПРОСИТЬ" name="price_sub" style="background: #b22 none repeat scroll 0 0;color: #fff;width: 110px;">
      </form>
    </div>
    <a href="#" id="modal_a">Получить подробный прайс</a>
    <div id="overlay"></div>









    <div class="jshop_prod_description">
        <?php print $this->product->description; ?>
    </div>

    <?php print $this->_tmp_product_html_before_demofiles; ?>

    <div id="list_product_demofiles"><?php include(dirname(__FILE__)."/demofiles.php");?></div>

    <?php if ($this->config->product_show_button_back){?>
        <div class="button_back">
            <input type="button" class="btn button" value="<?php print _JSHOP_BACK;?>" onclick="<?php print $this->product->button_back_js_click;?>" />
        </div>
    <?php }?>

    <?php

  echo "<br>";
// ВЫВОДИМ ССЫЛКУ ЕЩЕ ТОВАРЫ
$last_link = $_SERVER['REQUEST_URI'];
$last_link = explode("/" , $last_link);
$del_last = array_pop($last_link);
$last_link = implode("/", $last_link);
?>
<a href="<?php echo $last_link; ?>" class="a_button">Еще варианты</a>

    <?php
        print $this->_tmp_product_html_before_review;
        include(dirname(__FILE__)."/review.php");

        print $this->_tmp_product_html_before_related;
        include(dirname(__FILE__)."/related.php");
    ?>

    <?php print $this->_tmp_product_html_end;?>
</div>
















<?php
// Форма внизу
if (isset($_POST['ok'])){

//if(!isset($_POST['_antispam'])){exit;}

	$fio=strip_tags($_POST['fio']);
	$tel=strip_tags($_POST['tel']);
	$text=strip_tags($_POST['text']);
	if ($price_hid != ""){exit;}
  $ulink='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$massaga = "Заявка или вопрос по товару $ulink <br> ФИО: $fio<br> ТЕЛ: $tel<br> Комментарий: $text";
$headers=null;
$headers.="Content-Type: text/html; charset=utf-8\r\n";
$headers.="X-Mailer: PHP/".phpversion()."\r\n";
mail('energoteplokomplekt@yandex.ru,admin@etk72.ru', 'заявка с сайта БЫСТРЫЙ ЗАКАЗ', $massaga,$headers);
echo "<script>alert('Спасибо, в ближайшее время с Вами свяжется наш специалист!')</script>";
}

//Форма запроса цен рядом с кнопкой купить
if (isset($_POST['price_sub'])){

//if(!isset($_POST['_antispam'])){exit;}

	$price_name=strip_tags($_POST['price_name']);
	$price_email=strip_tags($_POST['price_email']);
	$price_tel=strip_tags($_POST['price_tel']);
	$price_hid=strip_tags($_POST['price_hid']);
	if ($price_hid != ""){exit;}
  $ulink='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$massaga = "Заявка на получение прайс листа $ulink <br> Имя: $price_name<br> ТЕЛ: $price_tel<br> Email: $price_email";
$headers=null;
$headers.="Content-Type: text/html; charset=utf-8\r\n";
$headers.="X-Mailer: PHP/".phpversion()."\r\n";
mail('energoteplokomplekt@yandex.ru,admin@etk72.ru', 'заявка с сайта ЗАПРОС ПРАЙС ЛИСТА', $massaga,$headers);
echo "<script>alert('Спасибо, в ближайшее время с Вами свяжется наш специалист!')</script>";
}


//Форма купить в 1 клик
if (isset($_POST['quick_bay'])){

//if(!isset($_POST['_antispam'])){exit;}

	$price_name=strip_tags($_POST['price_name']);
	$price_tel=strip_tags($_POST['price_tel']);
	$price_hid=strip_tags($_POST['price_hid']);
	if ($price_hid != ""){exit;}
  $ulink='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$massaga = "Заявка на покупку в 1 клик $ulink <br> Город: $price_name<br> ТЕЛ: $price_tel<br>";
$headers=null;
$headers.="Content-Type: text/html; charset=utf-8\r\n";
$headers.="X-Mailer: PHP/".phpversion()."\r\n";
mail('energoteplokomplekt@yandex.ru,admin@etk72.ru', 'заявка с сайта Покупка в 1 клик', $massaga,$headers);
echo "<script>alert('Спасибо, в ближайшее время с Вами свяжется наш специалист!')</script>";
}
?>



<script>
	$(document).ready(function() {
	$('#modal_a').click( function(event){
		event.preventDefault();
		$('#overlay').fadeIn(400,
		 	function(){
				$('#modal_form')
					.css('display', 'block')
					.animate({opacity: 1, top: '50%'}, 200);
		});
	});

	$('#modal_close, #overlay').click( function(){
		$('#modal_form')
			.animate({opacity: 0, top: '45%'}, 200,
				function(){ // пoсле aнимaции
					$(this).css('display', 'none');
					$('#overlay').fadeOut(400);
				}
			);
	});
});
</script>

<script>
	$(document).ready(function() {
	$('#modal_a_bay').click( function(event){
		event.preventDefault();
		$('#overlay').fadeIn(400,
		 	function(){
				$('#modal_form_bay')
					.css('display', 'block')
					.animate({opacity: 1, top: '50%'}, 200);
		});
	});

	$('#modal_close_bay, #overlay').click( function(){
		$('#modal_form_bay')
			.animate({opacity: 0, top: '45%'}, 200,
				function(){ // пoсле aнимaции
					$(this).css('display', 'none');
					$('#overlay').fadeOut(400);
				}
			);
	});
});
</script>

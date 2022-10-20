<div id = "jshop_module_cart" >
<p><img alt="корзина" src="/images/sys/cart.png">
      <span id = "jshop_quantity_products"><?php print $cart->count_product?></span>&nbsp;<?php print JText::_('PRODUCTS')?><span> - </span>

      <span id = "jshop_summ_product"><?php print formatprice($cart->getSum(0,1))?></span></p>
	  
      <a class="go_cart" href = "<?php print SEFLink('index.php?option=com_jshopping&controller=cart&task=view', 1)?>"><?php print JText::_('GO_TO_CART')?></a>
	  <div class="mobile_tel_cart">
 				<a href="tel:+7 (800) 777-32-09">
            	<img class="cart_tel" src="/images/sys/tel.png" alt="">
      			</a> 
		   </div>
</div>
<div class="desctop_tel_cart"><img class="jshop_module_cart_tel" src="/components/com_jshopping/images/tel.png" alt=""><span> +7 (800) 777-32-09 </span></div>
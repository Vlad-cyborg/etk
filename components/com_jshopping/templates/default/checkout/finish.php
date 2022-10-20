<?php 
/**
* @version      4.8.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>
<?php if (!empty($this->text)){?>
<?php echo $this->text;?>
<?php }else{?>
<div class="finish_order">  
        <p class="finish_ty"><?php print _JSHOP_THANK_YOU_ORDER?></p>
        <p class="finish_callback"><?php print _JSHOP_FINISH_CALLBACK?></p>
        <img src="/components/com_jshopping/images/finish_order2.png">
    </div>
<?php }?>
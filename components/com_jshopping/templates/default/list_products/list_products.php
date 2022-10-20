<?php
/**
* @version      4.9.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>
<?php
$responseLimit = JRequest::getInt('start',0);
$totalNumberPages = $this->pagination_obj->pagesTotal;
if ($responseLimit > 0 and ($totalNumberPages - 1) >= ($responseLimit / $this->pagination_obj->limit)){
	$document = JFactory::getDocument();
	$mytitle = $document->getTitle();
	$desc = $document->getMetadata('description');

	$selector = $this->product_count;
	preg_match('|"selected">(\d*)|si', $selector, $arr);

	$numpage =  $responseLimit / $arr[1] +1;
	
	$titletext =' | Страница '.$numpage.' из '.$totalNumberPages;

	$document->setTitle($mytitle.$titletext);
	$document->setMetadata('description', $desc.$titletext);
}?>
<div class="jshop list_product" id="comjshop_list_product">
<?php print $this->_tmp_list_products_html_start?>
<?php foreach ($this->rows as $k=>$product) : ?>
    <?php if ($k % $this->count_product_to_row == 0) : ?>
        <div class = "row-fluid">
    <?php endif; ?>

    <div class = "sblock<?php echo $this->count_product_to_row;?>">
        <div class = "block_product">
            <?php include(dirname(__FILE__)."/".$product->template_block_product);?>
        </div>
    </div>

    <?php if ($k % $this->count_product_to_row == $this->count_product_to_row - 1) : ?>
        <div class = "clearfix"></div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php if ($k % $this->count_product_to_row != $this->count_product_to_row - 1) : ?>
    <div class = "clearfix"></div>
    </div>
<?php endif; ?>
<?php print $this->_tmp_list_products_html_end;?>
</div>

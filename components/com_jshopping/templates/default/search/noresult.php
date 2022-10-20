<?php 
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="jshop" id="comjshop">
<h1><?php print _JSHOP_SEARCH_RESULT?> <?php if ($this->search) print '"'.$this->search.'"';?></h1>
	<div class="empty_search">
		<img src="/images/sys/empty_search.png" alt="">
		<p><b>По Вашему запросу ничего не нашлось</b></p>
		<p>Попробуйте изменить критерии поиска: например поставить пробел между наименованием товара и его маркировкой.</p>
	</div>
<?php //echo _JSHOP_NO_SEARCH_RESULTS;?>
</div>
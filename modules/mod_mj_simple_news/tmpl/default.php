<?php
/**
 * @package MJ Simple News
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2013 MicroJoom. All Rights Reserved.
 * @author MicroJoom http://www.microjoom.com
 * 
 */
defined('_JEXEC') or die;

if(!empty($list)){
	// ID for html module
	$tag_id = 'mj_simple_news_'.time().rand();
	$jquery_lazyload = false;
	// Include  Css
	JHtml::stylesheet('modules/'.$module->module.'/assets/css/styles.css');
	if($jquery_lazyload){
		global $mjquery;
		if( !isset($mjquery) && (int)$params->get('local_jquery', 0) ){
			JHtml::script('modules/'.$module->module.'/assets/js/jquery-2.0.3.min.js');
			JHtml::script('modules/'.$module->module.'/assets/js/jquery-noconflict.js');
			$mjquery = '2.0.3';
		}
		//JHtml::script('modules/'.$module->module.'/assets/js/jquery.lazyload.js');
		//$document->addScript(JURI::root().'/modules/mod_mj_simple_news/assets/js/jquery.lazyload.js', 'text/javascript', false, true);
	}
	// Get params configuration
	$preset_class = array();
	$preset_class[] =  'cols-lg-' . $params->get('col_lg', 6);
	$preset_class[] =  'cols-md-' . $params->get('col_md', 4);
	$preset_class[] =  'cols-sm-' . $params->get('col_sm', 2);
	$preset_class[] =  'cols-xs-' . $params->get('col_xs', 1);
	$preset_class	= implode(' ', $preset_class);
	$readmore_display		= (int)$params->get('item_readmore_display');
?>	
	<!--Begin mj-simple-news-->
	<!--[if lt IE 9]><div class="mj-simple-news msie lt-ie9" id="<?php echo $tag_id; ?>"><![endif]-->
	<!--[if IE 9]><div class="mj-simple-news msie" id="<?php echo $tag_id; ?>"><![endif]-->
	<!--[if gt IE 9]><!--><div class="mj-simple-news" id="<?php echo $tag_id; ?>"><!--<![endif]-->
		<!--Begin sn-items-->
		<div class="sn-items cf <?php echo $preset_class;  ?>">
			<?php  $j= 0; 
			foreach($list as $item){  $j++;  ?>
			<!--Begin sn-item-->
			<div class="sn-item ">
				<!--Begin sn-item-inner-->
				<div class="sn-item-inner">
					<!--Begin sn-image-->
					<?php
					$imgattr = '';
					$imgsrc = $item->image_src;
					if($jquery_lazyload) {
						$imgattr = ' data-original="'.JURI::base().$item->image_src.'"';
						$imgsrc  = JURI::base().'modules/'.$module->module.'/assets/img/white.gif';
					}
					if($imgsrc) { ?>
					<div class="sn-image">
						<a href="<?php echo $item->link ?>" rel="nofollow" title="<?php echo $item->title; ?>" <?php echo $item->link_target; ?>>
							<img src="<?php echo $imgsrc  ?>" <?php echo $imgattr.' '.$item->image_attr ?> />
						</a>
						<?php if($item->_created != '' || $item->_hits != '') { ?>
						<div class="sn-created-hits">
							<?php if($item->_created != ''){ ?>
							<span class="sn-created">
								<?php echo  JText::_('PUBLISHED_LABEL').JHTML::_('date', $item->_created,JText::_('m-d-Y')); ?>
							</span>
							<?php }
							if($item->_hits != ''){ ?>
							<span class="sn-hits">
								<?php echo JText::_('HITS_LABEL').$item->_hits; ?>
							</span> 
							<?php } ?>
						</div>
						<?php } ?>
					</div>
					<?php } ?>
					<!--End sn-image-->
					<?php
					$show_infor = $item->sub_title != '' ||  $item->_description != '' || $item->tags != '' || $readmore_display;
					if($show_infor) {	?>
					<!--Begin sn-infor-->
					<div class="sn-infor">
						<?php if($item->sub_title != '') {?>
						<div class="sn-title">
							<a href="<?php echo $item->link ?>" rel="nofollow" title="<?php echo $item->title; ?>" <?php echo $item->link_target; ?>>
								<!--noindex--><?php echo $item->sub_title; ?><!--/noindex-->
							</a>
						</div>
						<?php }
						if($item->_description != '') {?>
						<div class="sn-description">
                          	<!--noindex-->
							<?php echo $item->_description; ?>
                            <!--/noindex-->
						</div>
						<?php }
						if($item->tags != ''){?>
						<div class="sn-tags">
							<?php echo $item->tags; ?>
						</div>	
						<?php }
						if($readmore_display){?>
						<div class="sn-readmore">
							<a href="<?php echo $item->link ?>" rel="nofollow" title="<?php echo $item->title; ?>"  <?php echo $item->link_target; ?>>
							<!--noindex-->Подробнее...<!--/noindex-->
							</a>
						</div>
						<?php }?>
					</div>
					<!--End sn-infor-->
					<?php } ?>
				</div>
				<!--End sn-item-inner-->
			</div>
			<!--End sn-item-->
			 <?php
			$clear = 'clr1';
			if ($j % 2 == 0) $clear .= ' clr2';
			if ($j % 3 == 0) $clear .= ' clr3';
			if ($j % 4 == 0) $clear .= ' clr4';
			if ($j % 5 == 0) $clear .= ' clr5';
			if ($j % 6 == 0) $clear .= ' clr6';
			?>
			<div class="<?php echo $clear; ?>"></div>
			<?php } ?>
		</div>
		<!--End sn-items-->
	</div>
	<!--End mj-simple-news-->
	<?php
	if($jquery_lazyload){?>
	<script type="text/javascript">
	//<![CDATA[	
         jQuery(document).ready(function($){  
			;(function(element){
				var $element = $(element);
					$('img',$element).lazyload({
						effect: 'fadeIn',
						effect_speed:1000,
						load: function(){
							$(this).removeAttr("data-original");
						}
					});
			})('#<?php echo $tag_id; ?>'); 
        }); 
	//]]>	
    </script>
	<?php } ?>
<?php 
}else{
	//	Do not display this message for guest.
	$user = JFactory::getUser();
	if(	$user->id ){
		echo JText::_('WARNING_LABEL');	
	}
}?>
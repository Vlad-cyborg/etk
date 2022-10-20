<?php 
defined('_JEXEC') or die('Restricted access');
error_reporting(E_ALL & ~E_NOTICE);
if (!empty($arResult)):
?>
<table class="odcat <?php if($class){ echo $class;}?>">
<?php foreach($arResult as $arItem):?>

	<?php if ($arItem["IS_PARENT"]):?>
		<?php if ($arItem["DEPTH"] == 1): ?>
		<tr>
			<td class="root parent<?php if ($arItem["SELECTED"]):?> selected<?php endif;?>">
				<a href="<?php echo $arItem["LINK"]?>" class="<?php if ($arItem["SELECTED"]):?> selected<?php endif;?>">
					<?if(($display_img == 1) and $arItem["IMG"]):?>
					<img src="<?php echo $jshopConfig->image_category_live_path."/".$arItem["IMG"]?>">
					<?endif?>
					<?php echo $arItem["NAME"]?>
				</a>
			</td>
			<?php if($count):?>
			<td>
				<?php echo $arItem["COUNT"];?>
			</td>
			<?php endif;?>
		</tr>
		<?php else: ?>
		<tr>
			<td class="odsubcat-<?php echo $arItem["DEPTH"]?><?php if ($arItem["SELECTED"]):?> selected<?php endif;?>">
				<a href="<?php echo $arItem["LINK"]?>" class="<?php if ($arItem["SELECTED"]):?> selected<?php endif;?>">
					<?if(($display_img == 1) and $arItem["IMG"]):?>
					<img src="<?php echo $jshopConfig->image_category_live_path."/".$arItem["IMG"]?>">
					<?endif?>
					<?php echo $arItem["NAME"]?>
				</a>
			</td>
			<?php if($count):?>
			<td>
				<?php echo $arItem["COUNT"];?>
			</td>
			<?php endif;?>
		</tr>
		<?php endif;?>
	<?php else: ?>
		<?php if ($arItem["DEPTH"] == 1): ?>
		<tr>
			<td class="root<?php if ($arItem["SELECTED"]):?> selected<?php endif;?>">
				<a href="<?php echo $arItem["LINK"]?>" class="<?php if ($arItem["SELECTED"]):?> selected<?php endif;?>">
					<?if(($display_img == 1) and $arItem["IMG"]):?>
					<img src="<?php echo $jshopConfig->image_category_live_path."/".$arItem["IMG"]?>">
					<?endif?>
					<?php echo $arItem["NAME"]?>
				</a>
			</td>
			<?php if($count):?>
			<td>
				<?php echo $arItem["COUNT"];?>
			</td>
			<?php endif;?>
		</tr>
		<?php else: ?>
		<tr>
			<td class="odsubcat-<?php echo $arItem["DEPTH"]?><?php if ($arItem["SELECTED"]):?> selected<?php endif;?>">
				<a href="<?php echo $arItem["LINK"]?>" class="<?php if ($arItem["SELECTED"]):?> selected<?php endif;?>">
					<?if(($display_img == 1) and $arItem["IMG"]):?>
					<img src="<?php echo $jshopConfig->image_category_live_path."/".$arItem["IMG"]?>">
					<?endif?>
					<?php echo $arItem["NAME"]?>
				</a>
			</td>
			<?php if($count):?>
			<td>
				<?php echo $arItem["COUNT"];?>
			</td>
			<?php endif ?>
		</tr>
		<?php endif;?>
	<?php endif;?>
<?php endforeach;?>
</table>
<?php endif;?>
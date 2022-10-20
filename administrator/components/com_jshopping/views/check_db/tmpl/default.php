<?php
defined('_JEXEC') or die();

$version = $this->version;
$joomla = $this->joomla;	
$config = $this->config;
$tableData = $this->tableData;
$i=0;
$checkAll = ($joomla)?"Joomla.checkAll(this)":'checkAll('. count($tableData).')';
$isChecked = ($joomla)?"Joomla.isChecked(this.checked)":'isChecked(this.checked)';
	if(class_exists('JHtmlSidebar') && count(JHtmlSidebar::getEntries()))
		$sidebar = JHtmlSidebar::render();
	$classMain = '';
	if($sidebar): $classMain = ' class="span10 jshop_edit"';
?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $sidebar; ?>
	</div>
<?php endif; ?>

<div id="j-main-container"<?php echo $classMain;?>>
	<?php displaySubmenuOptions();?>	
	<h2><?php print _CHECK_DB_VERSION_JOOM_SHOPING." ".$version;?></h2>	
	<?php if($this->error_file):?>
	<h1><?php print _CHECK_DB_NO_FILE;?></h1>
	<?php return; endif;?>
<?php if(count($tableData)):?>	
	<form action = "index.php?option=com_jshopping&controller=check_db" method = "post" name = "adminForm" id = "adminForm" >
		<table class = "adminlist" width = "100%">
			<thead>
				<tr>
					<th class = "title" width  = "10">
						#
					</th>
					<th width = "20">
						 <input type="checkbox" name="toggle" value="" onClick="<?php print $checkAll;?>" />
					</th>
					<th align = "left" width = "200"> 
						<?php print _CHECK_DB_NAME_TABLE;?>
					</th>
					<th>
						<?php print _CHECK_DB_NAME_COLUMN;?>
					</th>
					<th>
						<?php print _CHECK_DB_NAME_TYPE;?>
					</th>
					<th>
						<?php print _CHECK_DB_ERROR;?>
					</th>
				</tr>
			</thead>
<?php	foreach($tableData as $key=>$row):
		if(!count($row->errorColumns) && !count($row->errorColumnsType) && !$row->errorTableCreate)continue;
		$i++;?>
			<tr class='row<?php print $i%2;?>'>
				<td>
<?php 				print $i;?>
				</td>
				<td>
					<input type = "checkbox" onclick = "<?php print $isChecked;?>" name = "cid[]" id = "cb<?php echo $key;?>" value = "<?php print $key;?>" />
				</td>
				<td>
					<?php print $row->nameTable;?>
				</td>
				<td class='center'>
					<?php print $row->nameColumn;?>
				</td>
				<td class='center'>
					<?php print $row->typeColumn;?>
				</td>
				<td>
					<?php print $row->error;?>
				</td>
			<tr>
<?php 	endforeach;?>
		</table>
		<input type = "hidden" name = "task" value = "" />
	</form>
<?php else:?>
	<div>
		<h1><?php print _CHECK_DB_OK;?></h1>
	</div>
<?php endif;?>
</div>	
	
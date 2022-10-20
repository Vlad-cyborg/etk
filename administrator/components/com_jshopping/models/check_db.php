<?php
/**
* @version      2.4.0 14.10.2010
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model');

class JshoppingModelcheck_db extends JModelLegacy{
    
	function errorColumns($data){
		$this->check_alter($this->prefix_table.$data->nameTable,$data->errorColumnsData, $data->errorColumns);
		if(!count($data->errorColumnsData))return;
		$db = JFactory::getDbo();
		$nameTable = "`#__jshopping_".$data->nameTable."`";
		$query = "ALTER TABLE ".$nameTable." ".implode(',',$data->errorColumnsData);
		$db->setQuery($query);
        $db->query();
	}
	
	function check_alter($nameTable ,&$alter = array(),$columns = array()){
		$db = JFactory::getDbo();
		$tablColumn = $this->array_keytolower($db->getTableColumns($nameTable));
		foreach($columns as $key=>$column){
			if(isset($tablColumn[$column]))unset($alter[$key]); 
		}
	}
	
	function array_keytolower($array){
		$columns = array();
		foreach($array as $key=>$rw){
			$columns[strtolower($key)] = $rw;
		}
		return $columns;
	}
	
	function errorColumnsType($data){
		$this->checkColumnType($this->prefix_table.$data->nameTable,$data->errorColumnsType);
		if(!count($data->errorColumnsType))return;
		$db = JFactory::getDbo();
		$nameTable = "`#__jshopping_".$data->nameTable."`";
		foreach($data->errorColumnsType as $errorColumnsType){
			$this->checkColumnType($this->prefix_table.$data->nameTable,$data->errorColumnsType);
			$query = "ALTER TABLE ".$nameTable." CHANGE `".$errorColumnsType->name."` `".$errorColumnsType->name."` ".$errorColumnsType->data;
			$db->setQuery($query);
			$db->query();
		}
	}
	
	function checkColumnType($nameTable,&$alter = array()){
		$db = JFactory::getDbo();
		$tablColumn = $this->array_keytolower($db->getTableColumns($nameTable));
		foreach($alter as $key=>$column){
			if($tablColumn[$column->name] == $column->must)unset($alter[$key]); 
		}	
	}
	
	function errorTableCreate($data){
		$db = JFactory::getDbo();
		$nameTable = "`#__jshopping_".$data->nameTable."`";
		$rows = array();
		foreach($data->columns as $key=>$row){
			$rows[] = "`".$row."`".$data->columnsData[$key];
		}
		if(count($data->index)){
			$pr_index=array();
			foreach($data->index as $row){
				$pr_index[]="INDEX (`".$row."`)";
			}
			$rows[] = implode(", ",$pr_index);
		}
		if(count($data->primary_key)){
			$pr_key=array();
			foreach($data->primary_key as $row){
				$pr_key[]="`".$row."`";
			}
			$rows[] = "PRIMARY KEY (".implode(", ",$pr_key).")";
		}
		
		if($data->unique_key)$rows[] = "UNIQUE KEY ".$data->unique_key;
		if(count($data->key)){
			foreach($data->key as $row){
				$rows[] = "KEY ".$row;
			}
		}
		$query = 'CREATE TABLE IF NOT EXISTS '.$nameTable."(".implode(",",$rows).")";;
		$db->setQuery($query);
		$db->query();
	}
	
	function getAllAttribute(){
		$db = JFactory::getDbo();
		$query = "SELECT attr_id FROM `#__jshopping_attr`";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		foreach($rows as &$row){
			$row->column = "attr_".$row->attr_id;
			$row->columnData = "int(11) NOT NULL";
			$row->columnType = "int";
		}
		return $rows;
	}
	
	function getAllExtra_fields(){
		$db = JFactory::getDbo();
		
		$type = array();
		$obj = new stdClass();
		$obj->type = 'text';
		$obj->data = 'text NOT NULL';
		$type[1] = $obj;
		$obj = new stdClass();
		$obj->type = 'varchar';
		$obj->data = 'varchar(255) NOT NULL';
		$type[0] = $obj;
		
		$query = "SELECT id,type FROM `#__jshopping_products_extra_fields`";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		foreach($rows as &$row){
			$row->column = "extra_field_".$row->id;
			$row->columnData = $type[$row->type]->data;
			$row->columnType = $type[$row->type]->type;
		}
		
		return $rows;
	}
}
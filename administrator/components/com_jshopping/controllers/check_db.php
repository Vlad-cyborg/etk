<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerCheck_db extends JControllerLegacy
{
    function __construct( $config = array() ){
        parent::__construct($config);
        checkAccessController("check_db");
		addSubmenu("other");
    }
    
    function display($cachable = false, $urlparams = false){
        $session = JFactory::getSession();
		$document = JFactory::getDocument();
        $mainframe = JFactory::getApplication();
		$jshopConfig = JSFactory::getConfig();
		$this->version = version_compare(JVERSION, '3.0.0', '>=');

		$document = JFactory::getDocument();
		$document->addStyleSheet( JUri::base() . 'components/com_jshopping/css/check-db.css');
		
        JSFactory::loadExtAdminLanguageFile('check_db');
		
        $data = JApplicationHelper::parseXMLInstallFile($jshopConfig->admin_path."jshopping.xml");
		
		$tableData = $this->check_db();
		
		$error_file=1;
		if(!isset($tableData['error']))
			$error_file=0;

		$table_rows = array();
		foreach($tableData as $key=>$row){
		
			$error_file=0;
			
			if(count($row->errorColumns)){
				foreach($row->errorColumns as $_row){
					$obj = new stdClass();
					$obj->errorTableCreate = $row->errorTableCreate;
					$obj->errorColumnsData = $row->errorColumnsData;
					$obj->errorColumnsType = $row->errorColumnsType;
					$obj->errorColumns = $row->errorColumns;
					$obj->nameTable = $row->nameTable;
					$obj->nameColumn = $_row;
					$obj->error = _CHECK_DB_ERROR_COLUMN.$_row;
					$obj->typeColumn = '';
		
					$table_rows[]=$obj;
				}
				
			}
			if(count($row->errorColumnsType)){
				foreach($row->errorColumnsType as $_row){
					$obj = new stdClass();
					$obj->errorTableCreate = $row->errorTableCreate;
					$obj->errorColumnsData = $row->errorColumnsData;
					$obj->errorColumnsType = $row->errorColumnsType;
					$obj->errorColumns = $row->errorColumns;
					$obj->nameTable = $row->nameTable;
					$obj->nameColumn = $_row->name;
					$obj->error = _CHECK_DB_ERROR_COLUMN_TYPE.$_row->must;
					$obj->typeColumn = $_row->eat;
		
					$table_rows[]=$obj;
				}
				
			}
			if($row->errorTableCreate){
				$obj = new stdClass();
				$obj->errorTableCreate = $row->errorTableCreate;
				$obj->errorColumnsData = $row->errorColumnsData;
				$obj->errorColumnsType = $row->errorColumnsType;
				$obj->errorColumns = $row->errorColumns;
				$obj->columns = $row->columns;
				$obj->primary_key = $row->primary_key;
				$obj->unique_key = $row->unique_key;
				$obj->key = $row->key;
				$obj->columnsData = $row->columnsData;
				$obj->nameTable = $row->nameTable;
				$obj->nameColumn = '';
				$obj->error = _CHECK_DB_ERROR_CREATE_TABLE.$row->nameTable;
				$obj->typeColumn = '';
		
				$table_rows[]=$obj;

			}
		}
		unset($tableData);
		
		$session->set('test_table',$table_rows);
		
        $view = $this->getView("check_db", 'html');
        $view->assign('version', $data['version']);
        $view->assign('config', $jshopConfig); 
        $view->assign('joomla', $this->version);
        $view->assign('error_file', $error_file);
        $view->assign('tableData', $table_rows);
        $view->display();
    }
	
	function check_db(){
		$jshopConfig = JSFactory::getConfig();
		
		$data = array();
		$langs = getAllLanguages();
		$db = JFactory::getDbo();
		$prefix = $db->getPrefix();
		$mCheck_db = $this->getModel("check_db");
		
		// table list db joomla
		$tableList = $db->getTableList();
		$prefix_table = $prefix."jshopping_";
		$mCheck_db->prefix_table = $prefix_table;
		//add check my version 
		$jshoppingXml = JApplicationHelper::parseXMLInstallFile($jshopConfig->admin_path."jshopping.xml");
		
		$data['version'] = $jshoppingXml['version'];
		$version = $jshoppingXml['version'];
		
		//read file 
		if($this->version){
			$folder = 'v_4/';
		}else{
			$folder = 'v_3/';
		}
		
		$files = JFolder::files($jshopConfig->path."files/check_db/".$folder, '.xml');
		if(!count($files)){
			$data['error'] = 'No file *.xml';
			return $data;
		}
		
		$test_table = array();
		$attr = array();
	  foreach($files as $_file){
		$file = $jshopConfig->path."files/check_db/".$folder.$_file;
		
		if($this->version){
			$xml = JFactory::getXML( $file,true);
			$document = $xml;
		}else{
			$xml = JFactory::getXMLParser( 'simple' );
			$xml->loadFile($file);
			$document = $xml->document;
		}
		
		if($this->version){
			$name = $document->getName();
		}else{
			$name = $document->name();
		}
		
		$attributes = $document->attributes();
		
		$_version = $attributes['version'];
		// check version and name file 
		if($name != 'com_jshopping'){
			continue;
		}
		
		// check version
		if(!version_compare($version,$_version,'>=')){
			continue;
		}
		
		$rows = $document->children();
		if(count($rows))
			foreach($rows as $row){
				$attributes = $row->attributes();
				if($this->version){
					$name = $row->getName();
				}else{
					$name = $row->name();
				}
				if($name == 'attr')
					$attr['products_attr'] = $mCheck_db->getAllAttribute();
				if($name == 'products_extra_fields')
					$attr['products'] = $mCheck_db->getAllExtra_fields();
				
				$obj = new stdClass();
				$obj->nameTable = $name;
				$obj->primary_key = (isset($attributes['primary_key']))?explode(",",$attributes['primary_key']):array();
				$obj->unique_key = (isset($attributes['unique_key']))?$attributes['unique_key']:'';
				$obj->key = (isset($attributes['key']))?explode(",",$attributes['key']):array();
				$obj->index = (isset($attributes['index']))?explode(",",$attributes['index']):array();
				$nameTable = $prefix_table.$obj->nameTable;
				
				$obj->errorTableCreate = 0;
				// array columns
				$_columns_ = array();
				$_columnsData = array();
				$_errorColumns = array();
				$_errorColumnsData = array();
				$_errorColumnsType = array();
				$_moreColumns = array();
				// check availability table
				if(in_array($nameTable,$tableList)){
					// check availability table columns
					$column = $db->getTableColumns($nameTable);
					$columns = array();
					foreach($column as $key=>$rw){
						$columns[strtolower($key)] = $rw;
					}
					
					$_columns = $row->children();
					
					if(count($_columns)){
						foreach($_columns as $nam){
							$attributes = $nam->attributes();
							if($this->version){
								$_name = $nam->getName();
								$_data = (string)$nam;
							}else{
								$_name = $nam->name();
								$_data = $nam->data();
							}
							
							if(isset($attributes['multilang'])){
								foreach($langs as $lang){
									$name = strtolower($_name."_".$lang->language);
									if(isset($columns[$name])){
										$_columns_[] = $name;
										$_columnsData[] = $_data;
										if($columns[$name] != $attributes['type']){
											// error type
											$_obj = new stdClass();
											$_obj->name = $_name."_".$lang->language;
											$_obj->must = (string)$attributes['type'];
											$_obj->eat = $columns[$name];
											$_obj->data = $_data;
											$_errorColumnsType[] = $_obj;
										}
										unset($columns[$name]);
									}else{
										// error no columns
										$_errorColumns[] = $name;
										$_errorColumnsData[] ="ADD `".$_name."_".$lang->language."`". (string)$_data;
									}
								}
							}else{
								$name = strtolower($_name);
								if(isset($columns[$name])){
									$_columns_[] = $name;
									$_columnsData[] = $_data;
									if($columns[$name] != $attributes['type']){
										// error type
										$_obj = new stdClass();
										$_obj->name = $_name;
										$_obj->must = (string)$attributes['type'];
										$_obj->eat = $columns[$name];
										$_obj->data = $_data;
										$_errorColumnsType[] = $_obj;
									}
									unset($columns[$name]);
								}else{
									// error no columns
									$_errorColumns[] = $name;
									$_errorColumnsData[] = "ADD `".$_name."`".(string)$_data;
								}					
							}
						}
						if(isset($attr[$obj->nameTable])){
							foreach($attr[$obj->nameTable] as $data){
								$name = strtolower($data->column);
								if(isset($columns[$name])){
									$_columns_[] = $name;
									$_columnsData[] = $data->columnData;
									if($columns[$name] != $data->columnType){
										// error type
										$_obj = new stdClass();
										$_obj->name = $data->column;
										$_obj->must = (string)$data->columnType;
										$_obj->eat = $columns[$name];
										$_obj->data = $data->columnData;
										$_errorColumnsType[] = $_obj;
									}
									
									unset($columns[$name]);
								}else{
									// error no columns
									$_errorColumns[] = $name;
									$_errorColumnsData[] = "ADD `".$data->column."`".$data->columnData;
									
								}
							}
						}
					}	
					// add more columns
					if(count($columns))	
						$_moreColumns = $columns;
				}else{
					$obj->errorTableCreate = 1;
					// add error data create table
					$_columns = $row->children();
					if(count($_columns))
						foreach($_columns as $nam){
							$attributes = $nam->attributes();
							
							if($this->version){
								$_name = $nam->getName();
								$_data = (string)$nam;
							}else{
								$_name = $nam->name();
								$_data = $nam->data();
							}
							
							if(isset($attributes['multilang'])){
								foreach($langs as $lang){
									$_columns_[] = $_name."_".$lang->language;
									$_columnsData[] = $_data;
								}
							}else{
								$_columns_[] = $_name;
								$_columnsData[] = $_data;
							}
						}
				}
				
				// save array to object
				$obj->columns = $_columns_;
				$obj->columnsData = $_columnsData;
				$obj->errorColumns_type = $_errorColumns_type;
				$obj->errorColumns = $_errorColumns;
				$obj->errorColumnsData = $_errorColumnsData;
				$obj->errorColumnsType = $_errorColumnsType;
				$obj->moreColumns  = $_moreColumns;
				
				// save object to array
				$test_table[] = $obj;
			}
	  }
	  return $test_table;
	}
	
	function apply(){
		$session = JFactory::getSession();
		$mainframe = JFactory::getApplication();
		$post = JRequest::get('post');
		$cid = $post['cid'];
		$db = JFactory::getDbo();
		$prefix = $db->getPrefix();
		$mCheck_db = $this->getModel("check_db");
		$mCheck_db->prefix_table = $prefix."jshopping_";
		if(!count($cid)){
			$mainframe->redirect('index.php?option=com_jshopping&controller=check_db');
		}
		$test_table = $session->get('test_table','');
		
		
		
		foreach($cid as $row){
			$data = $test_table[$row];
			if(count($data->errorColumns))$mCheck_db->errorColumns($data);
			if(count($data->errorColumnsType))$mCheck_db->errorColumnsType($data);
			if($data->errorTableCreate)$mCheck_db->errorTableCreate($data);
		}
		
		$mainframe->redirect('index.php?option=com_jshopping&controller=check_db');
	}
}
?>
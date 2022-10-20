<?php
/**
* @version      4.11.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

class jshopImportExport extends JTable {
    
    function __construct( &$_db ){
        parent::__construct( '#__jshopping_import_export', 'id', $_db );
    }        
}
?>
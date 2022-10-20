<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgJshoppingMenuCheck_db extends JPlugin 
{
	function __construct(& $subject, $config){
		parent::__construct($subject, $config);
		
	}
	
	function onBeforeAdminOptionPanelMenuDisplay(&$menu) {
		JSFactory::loadExtAdminLanguageFile('check_db');
		$menu['check_db'] = array(_CHECK_DB, 'index.php?option=com_jshopping&controller=check_db', 'jshop_report_b.png', 1);
	}
	
	function onBeforeAdminOptionPanelIcoDisplay(&$menu) {
		JSFactory::loadExtAdminLanguageFile('check_db');
		$menu['check_db'] = array(_CHECK_DB, 'index.php?option=com_jshopping&controller=check_db', 'jshop_report_b.png', 1);
	}
}
?>
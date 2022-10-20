<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');

class JshoppingViewCheck_db extends JViewLegacy
{
    function display($tpl = null)
    {
        JToolBarHelper::title(_CHECK_DB, 'generic.png' ); 
        JToolBarHelper::apply('apply',_JSHOP_FIX);
        parent::display($tpl);
	}
}
?>
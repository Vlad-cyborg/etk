<?php
/**
* @version      4.0.2 12.03.2015
* @author       Brooksus
* @package      Jshopping
* @copyright    Copyright (C) 2013 brooksite.ru. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerCompare extends JControllerLegacy{

  function display($cachable = false, $urlparams = false){
  $this->view();
		}
						
	 function add(){
			 //JPlugin::loadLanguage('plg_jshoppingproducts_compare',JPATH_ADMINISTRATOR); 
				$lang = JFactory::getLanguage();
		$lang->load('plg_jshoppingproducts_compare',JPATH_ADMINISTRATOR);
				session_start();
				if(isset($_POST['ids'])){
				$_SESSION['comparep'][] = $_POST['ids'];
					
				$_SESSION['compare']["ses_".$_POST['ids']] = array("product_".$_POST['ids']=>array("ids"=>$_POST['ids'],"image"=>$_POST['image'],"link"=>'<a href="'.$_POST['link'].'">'.$_POST['name'].'</a>',"price"=>$_POST['price'],"remove"=>'<a id="removemodid_'.$_POST['ids'].'" class="remove_compare_mod" href="#" title="'.JText::_('PLG_DEL_COMPARE').'">X</a>',"count"=>count($_SESSION['comparep'])));
				
				$compare_mod=$_SESSION['compare'];
				echo json_encode($compare_mod);
				exit();
				}
		}
		function remove(){
				session_start();
				if(isset($_POST['removeid'])) {
				if (is_array($_SESSION['comparep'])){
				unset($_SESSION['comparep'][array_search($_POST['removeid'],$_SESSION['comparep'])]);
				} else {
				unset($_SESSION['comparep'][$_POST['removeid']]);
				}
				if (!count($_SESSION['comparep'])){
					unset($_SESSION['compare']);
				}
				
				if (is_array(	$_SESSION['compare'])){
					unset($_SESSION['compare']["ses_".$_POST['removeid']]);
				} else {
					unset($_SESSION['compare']);
				}
				if (!count($_SESSION['comparep'])){
					unset($_SESSION['compare']);
				}
				if (is_array($_SESSION['compare'])){
				$compare_mod=$_SESSION['compare'][array_search("product_".$_POST['removeid'],$_SESSION['compare'])];
				} else {
				$compare_mod=$_SESSION['compare']["ses_".$_POST['removeid']];
				}
				echo json_encode($compare_mod);
				exit();
				}
			}
								
		function view(){
		//JPlugin::loadLanguage('plg_jshoppingproducts_compare',JPATH_ADMINISTRATOR); 	
		$lang = JFactory::getLanguage();
		$lang->load('plg_jshoppingproducts_compare',JPATH_ADMINISTRATOR);
	 $mainframe = JFactory::getApplication();
  $jshopConfig = JSFactory::getConfig();
  $params = $mainframe->getParams();
        if (getThisURLMainPageShop()){
												$document = JFactory::getDocument();
            appendPathWay(JText::_('PLG_COMPARE'));
            setMetaData(JText::_('PLG_COMPARE'), JText::_('PLG_META_SECOND'), JText::_('PLG_META_THIRD'));
        }else{
            setMetaData(JText::_('PLG_COMPARE'), $seodata->keyword, $seodata->description, $params);
        }
        JPluginHelper::importPlugin('jshoppingproducts');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayCompare', array(&$compare) );
																				
								$view_name = "compare";
        $view_config = array("template_path"=>JPATH_COMPONENT."/templates/".$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("compare");
        $view->assign('config', $jshopConfig);
	      	$view->assign('products', $compare->products);
							 $dispatcher->trigger('onBeforeDisplayCompareView', array(&$view));     
		      $view->display();
        //if ($ajax) die();
    }

}
?>
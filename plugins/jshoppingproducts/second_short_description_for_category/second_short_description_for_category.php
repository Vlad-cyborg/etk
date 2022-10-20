<?php
    /*
    * @version      1.0.0 07.12.2015
    * @author       MAXXmarketing GmbH
    * @package      addon_second_short_description_for_category
    * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
    * @license      GNU/GPL
    */
    defined('_JEXEC') or die('Restricted access');

    class plgJshoppingProductsSecond_short_description_for_category extends JPlugin {
        
        private $_params,
                $_separator = "{second_short_description}";
        
        public function __construct(&$subject, $config) {
            parent::__construct($subject, $config);
            
            $addon = JTable::getInstance('addon', 'jshop');
            $addon->loadAlias('second_short_description_for_category');
            $this->_params = (object) $addon->getParams();
        }

        public function onBeforeDisplayCategoryView(&$view) {
            $this->_add_ss_description_to_subcats($view);
        }
        
        public function onBeforeDisplayProductListView(&$view) {
            $category = $view->category;
            $descr_varname = JSFactory::getLang()->get('description');
            $tmp = explode($this->_separator, $category->$descr_varname);
            if( isset($tmp[1]) ) {
                $category->description = $tmp[0];
                if( !isset($this->_params->state_cat) || !empty($this->_params->state_cat) ) {
                    $output_position_cat = empty($this->_params->output_position_cat) ? '_tmp_category_html_before_products' : $this->_params->output_position_cat;
                    if( empty($view->$output_position_cat) ) $view->$output_position_cat = '';
                    $view->$output_position_cat .= '<div class="second_short_description">' . $tmp[1] . '</div>';
                }
            }
            $this->_add_ss_description_to_subcats($view);
        }
        
        private function _add_ss_description_to_subcats($view) {
            if( !isset($this->_params->state_list) || !empty($this->_params->state_list) ) {
                $db = JFactory::getDbo();
                $descr_varname = JSFactory::getLang()->get('description');
                $output_position_list = empty($this->_params->output_position_list) ? 'short_description' : $this->_params->output_position_list;
                foreach($view->categories as $subcategory) {
                    $query = $db->getQuery(true)
                            ->select( $db->qn($descr_varname) )
                            ->from('#__jshopping_categories')
                            ->where('category_id = ' . intval($subcategory->category_id));
                    $db->setQuery($query);
                    $cat_description = $db->loadResult();
                    $tmp = explode($this->_separator, $cat_description);
                    if( isset($tmp[1]) ) {
                        if( empty($subcategory->$output_position_list) ) $subcategory->$output_position_list = '';
                        $subcategory->$output_position_list .= '<div class="second_short_description">' . $tmp[1] . '</div>';
                    }
                }
            }
        }

    }
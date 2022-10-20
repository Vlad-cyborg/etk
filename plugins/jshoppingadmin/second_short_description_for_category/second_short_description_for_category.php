<?php
    /*
    * @version      1.0.0 07.12.2015
    * @author       MAXXmarketing GmbH
    * @package      addon_second_short_description_for_category
    * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
    * @license      GNU/GPL
    */
    defined('_JEXEC') or die('Restricted access');

    class plgJshoppingAdminSecond_short_description_for_category extends JPlugin {
        
        private $_separator = "{second_short_description}";

        public function onBeforeSaveCategory(&$post) {
            $jinp = JFactory::getApplication()->input;
            $model_langs = JModelLegacy::getInstance('Languages', 'JshoppingModel');
            $languages = $model_langs->getAllLanguages(1);			
            foreach($languages as $lang) {
                $descr_varname = 'description' . $lang->id;
                $descr  = $jinp->get($descr_varname, '', 'raw');
                $descr .= $this->_separator;
                $descr .= $jinp->get('second_short_' . $descr_varname, '', 'raw');                
				$post['description_'.$lang->language] = $descr;
            }			
        }

        public function onBeforeEditCategories(&$view) {
            $category = $view->category;
            $model_langs = JModelLegacy::getInstance('Languages', 'JshoppingModel');
            $languages = $model_langs->getAllLanguages(1);
            foreach($languages as $lang) {
                $descr_varname = 'description_' . $lang->language;
                $ss_descr_varname = 'second_short_' . $descr_varname;
                $category->$ss_descr_varname = '';
                $tmp = explode($this->_separator, $category->$descr_varname);
                if( isset($tmp[1]) ) {
                    $category->$descr_varname = $tmp[0];
                    $category->$ss_descr_varname = $tmp[1];
                }
                $editor = JFactory::getEditor();
                $view->{'plugin_template_' . $descr_varname} .= '<tr><td class="key">' . _JSHOP_CATEGORY_TAGS . '</td><td>' . $editor->display('second_short_description' . $lang->id, $category->$ss_descr_varname, '100%', '350', '75', '20' ) . '</td></tr>';
            }
        }

    }
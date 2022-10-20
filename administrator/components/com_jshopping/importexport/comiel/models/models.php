<?php
/**
* @package Joomla
* @subpackage JoomShopping
* @author Nevigen.com
* @website https://nevigen.com/
* @email support@nevigen.com
* @copyright Copyright © Nevigen.com. All rights reserved.
* @license Proprietary. Copyrighted Commercial Software
* @license agreement https://nevigen.com/license-agreement.html
**/

defined('_JEXEC') or die;

class ComielModels extends JModelItem {

    protected static $lang;
    protected $tables = [];

    public function __construct(array $config = []) {
        parent::__construct($config);
        if (!empty($config['lang'])) {
            self::$lang = $config['lang'];
        }
    }

    public function findOne($keys, $field = null, $data = []) {
        $uniqueKey = md5(http_build_query(is_array($keys) ? $keys : [$keys]));
        if (!array_key_exists($uniqueKey, $this->tables)) {
            $jshopTable = $this->getTable();
            if (!$jshopTable->load($keys)) {
                if (!is_array($keys)) {
                    if ($field === null) {
                        $field = $jshopTable->getKeyName();
                    }
                    $keys = [$field => $keys];
                }
                $jshopTable->bind(array_merge($keys, $data));
                $this->beforeCreate($jshopTable);
                if (!$jshopTable->store()) {
                    throw new RuntimeException(_JSHOP_ERROR_SAVE_DATABASE);
                }
                $this->afterCreate($jshopTable);
            }
            $this->tables[$uniqueKey] = $jshopTable;
        }

        return $this->tables[$uniqueKey];
    }

    public static function getInstance($type, $prefix = 'ComielModel', $config = []) {
        $config['lang'] = JSFactory::getLang();
        $model = parent::getInstance($type, $prefix, $config);

        return $model;
    }

    protected function afterCreate($jshopTable) {
    }

    protected function beforeCreate($jshopTable) {
    }

}
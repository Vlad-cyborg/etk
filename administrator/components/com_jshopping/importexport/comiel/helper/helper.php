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

class ComielHelper {

    public static function parseLine($line, $separator = ';') {
		return preg_split('/(?<!\\\)'.preg_quote($separator).'/', $line);
    }

    public static function parseMultipleLine($line, $separator = '|') {
        return ComielHelper::parseLine($line, $separator);
    }

}
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

$files = $this->get('filesExport');
$title = JText::_('COMIEL_FILES_GROUP_EXPORT');
$checkbox = 'export';
include __DIR__ . '/files.php';
$files = $this->get('filesArchive');
$title = JText::_('COMIEL_FILES_GROUP_ARCHIVE');
$checkbox = 'archive';
include __DIR__ . '/files.php';
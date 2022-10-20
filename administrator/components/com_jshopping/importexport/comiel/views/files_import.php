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

$files = $this->get('filesImport');
$title = JText::_('COMIEL_FILES_GROUP_IMPORT');
$checkbox = 'import';
include __DIR__ . '/files.php';
$files = $this->get('filesBackup');
$title = JText::_('COMIEL_FILES_GROUP_BACKUP');
$checkbox = 'backup';
include __DIR__ . '/files.php';
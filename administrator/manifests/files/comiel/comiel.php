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

require_once JPATH_SITE.'/components/com_jshopping/lib/factory.php';
require_once JPATH_SITE.'/components/com_jshopping/lib/functions.php';

class comielInstallerScript {
	
	private $minimum_php_release = '5.4.0';
	private $usekey = 1;
	private $install_extension = array ();
	private $install_folders = array (
		'administrator/components/com_jshopping/importexport/comiel',
		'components/com_jshopping/files/importexport/comiel',
	);
	private $install_files = array (
	);
	private $old_folders = array (
	);
	private $old_files = array (
	);
	private $name;
	private $scriptfile;
	private $element;

	private function setVar($parent) {
		$manifest = $parent->get('manifest');
		$this->name = (string)$manifest->name;
		$this->scriptfile = (string)$manifest->scriptfile;
		$this->element = substr($this->scriptfile, 0, -4);
		$this->version = (string)$manifest->version;
	}

	private function updateDataBase($type='install') {
		JFactory::getLanguage()->load($this->element, JPATH_ADMINISTRATOR . '/components/com_jshopping/importexport/comiel');
        $tableImportExport = JTable::getInstance('ImportExport', 'jshop');
        $tableImportExport->load(array('alias'=>$this->element));
		if ($type=='install') {
			$tableImportExport->name = 'Comiel';
			$tableImportExport->description = 'Complex XLS(X)/CSV Import/Export v' . $this->version;
			$tableImportExport->alias = $this->element;
			$tableImportExport->endstart = 0;
			$tableImportExport->steptime = 1;
			$tableImportExport->store();
		} else if ($tableImportExport->id) {
			$tableImportExport->delete();
		}
	}

	function preflight($type, $parent) {
		$this->setVar($parent);
		$error = 0;
		$app = JFactory::getApplication();
		if (version_compare(phpversion(),$this->minimum_php_release,'<')) {
			$app->enqueueMessage($this->name.' requires PHP '.$this->minimum_php_release.' or later version!', 'error');
			$error = 1;
		}
		if ($this->usekey && !extension_loaded('openssl')) {
			$app->enqueueMessage($this->name.' requires PHP OpenSSL extension!', 'error');
			$error = 1;
		}
		if ($error) {
			$app->enqueueMessage('The installation was canceled', 'error');
			return false;
		}
	}
	
	function install($parent) {
	}

	function update($parent) {
	}

	function postflight($type, $parent) {
		$installer = new JInstaller;
		$install_folder = JPATH_ROOT.'/tmp/'.$this->element;
		foreach($this->install_extension as $extension){
			if ($extension['type'] == 'plugin') {
				$folder = 'plugins/'.$extension['folder'].'/'.$extension['element'];
			} else {
				$folder = 'modules/'.$extension['element'];
			}
			if ($extension['checkversion'] && file_exists(JPATH_ROOT.'/'.$folder.'/'.$extension['element'].'.xml')) {
				$oldXML = JFactory::getXML(JPATH_ROOT.'/'.$folder.'/'.$extension['element'].'.xml');
				$xml = JFactory::getXML($install_folder.'/'.$folder.'/'.$extension['element'].'.xml');
				if (version_compare(trim($xml->version), trim($oldXML->version), '<')) {
					continue;
				}
			}
			$installer->install($install_folder.'/'.$folder);
			if ($extension['enabled']) {
				$t_extension = JTable::getInstance('Extension');
				$extension_id = $t_extension->find(array('type'=>$extension['type'], 'element'=>$extension['element'], 'folder'=>$extension['folder']));
				if ($extension_id) {
					$t_extension->load($extension_id);
					$t_extension->enabled = 1;
					$t_extension->store();
				}
			}
		}
		if (file_exists($install_folder)) {
			@JFolder::delete($install_folder);
		}
		
		$extension_root = $parent->getParent()->getPath('extension_root');
		$extension_source = $parent->getParent()->getPath('source');
		@JFile::copy($extension_source.'/'.$this->scriptfile, $extension_root.'/'.$this->scriptfile);
		
		$this->updateDataBase();

		foreach($this->old_folders as $folder){
			if (file_exists(JPATH_ROOT.'/'.$folder)) {
				@JFolder::delete(JPATH_ROOT.'/'.$folder);
			}
		}

		foreach ($this->old_files as $file) {
			if (file_exists(JPATH_ROOT.'/'.$file)) {
				@JFile::delete(JPATH_ROOT.'/'.$file);
			}
		}
		
		$manifest = $parent->getParent()->getManifest();
		$addon = JTable::getInstance('Addon', 'jshop');
		$addon->loadAlias($this->element);
		$addon->name = '<b><a style="display:inline-block;height:16px;padding:0 18px;background:url(https://nevigen.com/ico/'.$this->element.'.png) no-repeat" href="https://nevigen.com/">'.JString::ucfirst(str_replace('_', ' ', $this->name)).'</a></b>';
		$addon->version = $this->version;
		$addon->usekey = $this->usekey;
		if (strlen($addon->key)<50) {
			$addon->key = '';
		}
		$addon->uninstall = str_replace(JPATH_ROOT,'',$parent->getParent()->getPath('extension_root')).'/'.$this->scriptfile;
		$addon->store();
		if ($this->usekey && !$addon->key) {
			$parent->getParent()->setRedirectURL('index.php?option=com_jshopping&controller=licensekeyaddon&alias='.$this->element.'&back='.base64_encode('index.php?option=com_jshopping&controller=addons'));
		} else {
			$parent->getParent()->setRedirectURL('index.php?option=com_jshopping&controller=addons');
		}
	}
	
	function uninstall($parent) {
		$this->setVar($parent);
		$installer = new JInstaller;
		foreach($this->install_extension as $extension){
			$extension_id = JTable::getInstance('Extension')->find(array('type'=>$extension['type'], 'element'=>$extension['element'], 'folder'=>$extension['folder']));
			if ($extension_id) {
				$installer->uninstall($extension['type'], $extension_id);
			}
		}

		foreach($this->install_folders as $folder){
			if (file_exists(JPATH_ROOT.'/'.$folder)) {
				@JFolder::delete(JPATH_ROOT.'/'.$folder);
			}
		}

		foreach($this->install_files as $file){
			if (file_exists(JPATH_ROOT.'/'.$file)) {
				@JFile::delete(JPATH_ROOT.'/'.$file);
			}
		}

		if (file_exists($parent->getParent()->getPath('extension_root').'/'.$this->scriptfile)) {
			@JFile::delete($parent->getParent()->getPath('extension_root').'/'.$this->scriptfile);
		}

		if (JFactory::getApplication()->input->getCmd('option') != 'com_jshopping') {
			$addon = JTable::getInstance('Addon', 'jshop');
			$addon->loadAlias($this->element);
			if ($addon->id) {
				$addon->delete();
			}
		}
		
		$this->updateDataBase('uninstall');
	}
	
}

if (JFactory::getApplication()->input->getCmd('option') == 'com_jshopping') {
	$extension_id = JTable::getInstance('Extension')->find(array('type'=>'file', 'element'=>$row->alias));
	if ($extension_id) {
		JInstaller::getInstance()->uninstall('file', $extension_id);
	}
}
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

include_once __DIR__ . '/compleximportexport.php';

class IEComiel extends IEComplexImportExport
{
    const ROWS_IN_EXPORT_MAX = 5000;
    const ROWS_IN_EXPORT_MIN = 1000;

    const FIELDS = [
        'id' => 'COMIEL_PRODUCT_ID',
        'ean' => 'COMIEL_PRODUCT_EAN',
        'name' => 'COMIEL_PRODUCT_NAME',
        'alias' => 'COMIEL_PRODUCT_ALIAS',
        'short_description' => 'COMIEL_PRODUCT_SHORT_DESCRIPTION',
        'description' => 'COMIEL_PRODUCT_DESCRIPTION',
        'meta_title' => 'COMIEL_PRODUCT_META_TITLE',
        'meta_description' => 'COMIEL_PRODUCT_META_DESCRIPTION',
        'meta_keyword' => 'COMIEL_PRODUCT_META_KEYWORDS',
        'manufacturer_code' => 'COMIEL_PRODUCT_MANUFACTURER_CODE',
        'qty' => 'COMIEL_PRODUCT_QTY',
        'unlimited' => 'COMIEL_PRODUCT_UNLIMITED',
        'publish' => 'COMIEL_PRODUCT_PUBLISH',
        'categories' => 'COMIEL_PRODUCT_CATEGORIES',
        'tax' => 'COMIEL_PRODUCT_TAX',
        'currency' => 'COMIEL_PRODUCT_CURRENCY',
        'template' => 'COMIEL_PRODUCT_TEMPLATE',
        'url' => 'COMIEL_PRODUCT_URL',
        'old_price' => 'COMIEL_PRODUCT_OLD_PRICE',
        'buy_price' => 'COMIEL_PRODUCT_BUY_PRICE',
        'price' => 'COMIEL_PRODUCT_PRICE',
        'add_price' => 'COMIEL_PRODUCT_ADD_PRICE',
        'basic_price' => 'COMIEL_PRODUCT_BASIC_PRICE',
        'weight' => 'COMIEL_PRODUCT_WEIGHT',
        'unit' => 'COMIEL_PRODUCT_UNIT',
        'image' => 'COMIEL_PRODUCT_IMAGE',
        'images' => 'COMIEL_PRODUCT_IMAGES',
        'manufacturer' => 'COMIEL_PRODUCT_MANUFACTURER',
        'delivery_times' => 'COMIEL_PRODUCT_DELIVERY_TIMES',
        'hits' => 'COMIEL_PRODUCT_HITS',
        'label' => 'COMIEL_PRODUCT_LABEL',
        'free_attributes' => 'COMIEL_PRODUCT_FREE_ATTRIBUTES',
        'attributes' => 'COMIEL_PRODUCT_ATTRIBUTES',
        'extra_fields' => 'COMIEL_PRODUCT_EXTRA_FIELDS',
        'video' => 'COMIEL_PRODUCT_VIDEOS',
        'files' => 'COMIEL_PRODUCT_FILES',
        'product_url' => 'COMIEL_PRODUCT_LINK',
        'related' => 'COMIEL_PRODUCT_RELATED',
        'vendor' => 'COMIEL_PRODUCT_VENDOR',
    ];

    /**
     * @var JDatabaseDriver
     * @since 3.0.0
     */
    protected $db;
    /**
     * @var string
     * @since 3.0.0
     */
    protected $fileName;

    /**
     * @var array
     * @since 3.0.0
     */
    private $attributes;
    /**
     * @var  array
     * @since 3.0.0
     */
    private $attributeValues;
    /**
     * @var array
     * @since 3.0.0
     */
    private $categories;
    /**
     * @var array
     * @since 3.0.0
     */
    private $currencies;
    /**
     * @var array
     * @since 3.0.0
     */
    private $deliveryTimes;
    /**
     * @var array
     * @since 3.0.0
     */
    private $extraFields;
    /**
     * @var array
     * @since 3.0.0
     */
    private $files;
    /**
     * @var array
     * @since 3.0.0
     */
    private $freeAttributes;
    /**
     * @var array
     * @since 3.0.0
     */
    private $images;
    /**
     * @var array
     * @since 3.0.0
     */
    private $labels;
    /**
     * @var array
     * @since 3.0.0
     */
    private $manufacturers;
    /**
     * @var array
     * @since 3.0.0
     */
    private $relations;
    /**
     * @var array
     * @since 3.0.0
     */
    private $taxes;
    /**
     * @var array
     * @since 3.0.0
     */
    private $units;
    /**
     * @var array
     * @since 3.0.0
     */
    private $vendors;
    /**
     * @var array
     * @since 3.0.0
     */
    private $video;
    /**
     * @var array
     * @since 3.0.0
     */
    private $extraFieldGroups;
    /**
     * @var array
     * @since 3.0.0
     */
    private $extraFieldValues;

    /**
     * IEComiel constructor.
     * @param array $properties
     *
     * @since 3.0.0
     */
    public function __construct(array $properties = null)
    {
        parent::__construct($properties);
        $this->set('db', JFactory::getDbo());
        JModelLegacy::addIncludePath(__DIR__ . DIRECTORY_SEPARATOR . 'models');
        JLoader::registerPrefix('Comiel', __DIR__);
    }

    /**
     * @return IEComiel
     *
     * @since 3.0.0
     */
    public function backupImages()
    {
		$this->initImageData();
        $app = JFactory::getApplication();
        if (!JSession::checkToken(strtolower($app->input->getMethod()))) {
			$this->clearImageData();
            return $this->redirect('view', $app->input->getMethod() === 'POST' ? 'actions' : 'import', [], 'JINVALID_TOKEN', 'error');
        }
        register_shutdown_function([$this, 'backupImagesShutdown']);
        try {
            if ($app->input->getMethod() === 'POST') {
                $this->fileName = JUri::getInstance()->getHost() . '_' . date('Y.m.d.H.i.s') . '_archive.zip';
            } elseif ($app->input->getMethod() === 'GET') {
                $fileName = $app->input->get->getString('fileName');
                $this->fileName = JFile::stripExt(str_replace('_export', '_archive', $fileName)) . '.zip';
            }
            if (!$this->fileName) {
                throw new Exception(JText::_('COMIEL_ERROR_GENERATE_FILENAME'));
            }
            $zipArchive = new ZipArchive;
            $zipArchive->open($this->fileNamePath(), ZIPARCHIVE::CREATE);
			
			if ($this->imageData['products']) {
				$jshopImage = JTable::getInstance('image', 'jshop');
				$query = $jshopImage->getDbo()->getQuery(true);
				$query->select($jshopImage->getDbo()->qn('image_name', 'image'));
				$query->from($jshopImage->getTableName());
				$query->where($jshopImage->getDbo()->qn('image_name') . ' <> ' . $jshopImage->getDbo()->q(''));
				$query->where($jshopImage->getDbo()->qn('product_id') . ' IN (' . implode(',', $this->imageData['products']) . ')');
				$images = $jshopImage->getDbo()->setQuery($query)->loadObjectList();
				$localName = realpath($this->jshopConfig->get('image_product_path'));
				$localName = str_replace(JPATH_ROOT . DIRECTORY_SEPARATOR, '', $localName);
				$zipArchive->addEmptyDir($localName);
				foreach ($images as $image) {
					$file = $this->jshopConfig->get('image_product_path') . DIRECTORY_SEPARATOR . 'full_' . $image->image;
					if (is_file($file)) {
						$zipArchive->addFile($file, $localName . DIRECTORY_SEPARATOR . $image->image);
					} elseif (is_file($file = $this->jshopConfig->get('image_product_path') . DIRECTORY_SEPARATOR . $image->image)) {
						$zipArchive->addFile($file, $localName . DIRECTORY_SEPARATOR . $image->image);
					}
				}
			}
			
			if ($this->imageData['categories']) {
				$jshopCategory = JTable::getInstance('category', 'jshop');
				$query = $jshopCategory->getDbo()->getQuery(true);
				$query->select($jshopCategory->getDbo()->qn('category_image', 'image'));
				$query->from($jshopCategory->getTableName());
				$query->where($jshopCategory->getDbo()->qn('category_image') . ' <> ' . $jshopCategory->getDbo()->q(''));
				$query->where($jshopCategory->getDbo()->qn('category_id') . ' IN (' . implode(',', $this->imageData['categories']) . ')');
				$images = $jshopCategory->getDbo()->setQuery($query)->loadObjectList();
				$localName = realpath($this->jshopConfig->get('image_category_path'));
				$localName = str_replace(JPATH_ROOT . DIRECTORY_SEPARATOR, '', $localName);
				$zipArchive->addEmptyDir($localName);
				foreach ($images as $image) {
					$file = $this->jshopConfig->get('image_category_path') . DIRECTORY_SEPARATOR . $image->image;
					if (is_file($file)) {
						$zipArchive->addFile($file, $localName . DIRECTORY_SEPARATOR . $image->image);
					}
				}
			}

			if ($this->imageData['manufacturers']) {
				$jshopManufacturer = JTable::getInstance('manufacturer', 'jshop');
				$db = $jshopManufacturer->getDbo();
				$query = $db->getQuery(true);
				$query->select($db->qn('manufacturer_logo', 'image'));
				$query->from($jshopManufacturer->getTableName());
				$query->where($db->qn('manufacturer_logo') . ' <> ' . $db->q(''));
				$query->where($jshopCategory->getDbo()->qn('manufacturer_id') . ' IN (' . implode(',', $this->imageData['manufacturers']) . ')');
				$images = $db->setQuery($query)->loadObjectList();
				$localName = realpath($this->jshopConfig->get('image_manufs_path'));
				$localName = str_replace(JPATH_ROOT . DIRECTORY_SEPARATOR, '', $localName);
				$zipArchive->addEmptyDir($localName);
				foreach ($images as $image) {
					$file = $this->jshopConfig->get('image_manufs_path') . DIRECTORY_SEPARATOR . $image->image;
					if (is_file($file)) {
						$zipArchive->addFile($file, $localName . DIRECTORY_SEPARATOR . $image->image);
					}
				}
			}

			if ($this->imageData['labels']) {
				$jshopProductLabel = JTable::getInstance('productLabel', 'jshop');
				$query = $jshopProductLabel->getDbo()->getQuery(true);
				$query->select($jshopProductLabel->getDbo()->qn('image', 'image'));
				$query->from($jshopProductLabel->getTableName());
				$query->where($jshopProductLabel->getDbo()->qn('image') . ' <> ' . $jshopProductLabel->getDbo()->q(''));
				$query->where($jshopCategory->getDbo()->qn('label_id') . ' IN (' . implode(',', $this->imageData['labels']) . ')');
				$images = $jshopProductLabel->getDbo()->setQuery($query)->loadObjectList();
				$localName = realpath($this->jshopConfig->get('image_labels_path'));
				$localName = str_replace(JPATH_ROOT . DIRECTORY_SEPARATOR, '', $localName);
				$zipArchive->addEmptyDir($localName);
				foreach ($images as $image) {
					$file = $this->jshopConfig->get('image_labels_path') . DIRECTORY_SEPARATOR . $image->image;
					if (is_file($file)) {
						$zipArchive->addFile($file, $localName . DIRECTORY_SEPARATOR . $image->image);
					}
				}
			}

            $zipArchive->close();
        } catch (Exception $e) {
            return $this->redirect('view', $app->input->get->get('fileName') ? 'export' : 'actions', [], $e->getMessage(), 'error');
        }

		$this->clearImageData();
        return $this->redirect('view', $app->input->get->get('fileName') ? 'export' : 'actions', [], 'COMIEL_SUCCESS_BACKUP_IMAGES_DONE');
    }

    /**
     * @return IEComiel
     *
     * @since 3.0.0
     */
    public function backupImagesShutdown()
    {
        if (connection_status() >= 2) {
            return $this->redirect('view', 'export', ['status' => 'timeout_archive']);
        }

        return $this;
    }

    /**
     * @return IEComiel|string
     *
     * @since 3.0.0
     */
    public function backupMySQL()
    {
        $app = JFactory::getApplication();
        $noRedirect = $app->input->get(strtolower('noRedirect'));
        if (!JSession::checkToken(strtolower($app->input->getMethod()))) {
            return $noRedirect ? JText::_('JINVALID_TOKEN') : $this->redirect('view', $app->input->getMethod() === 'POST' ? 'actions' : 'import', [], 'JINVALID_TOKEN', 'error');
        }
        register_shutdown_function([$this, 'backupMySQLShutdown']);
        try {
            if ($app->input->getMethod() === 'POST') {
                $this->fileName = JUri::getInstance()->getHost() . '_' . date('Y.m.d.H.i.s') . '_backup.sql';
            } elseif ($app->input->getMethod() === 'GET') {
                $restoreFile = $app->input->get->getString('restoreFile');
                $this->fileName = JFile::stripExt(str_replace('_import', '_backup', $restoreFile)) . '.sql';
            }
            if (!$this->fileName) {
                throw new Exception(JText::_('COMIEL_ERROR_GENERATE_FILENAME'));
            }
            $tables = array_filter($this->db->getTableList(), function ($tableName) {
                return strpos($tableName, '_jshopping_');
            });
            $fp = fopen($this->fileNamePath(), 'w+');
            $dump = '';
            foreach ($this->db->getTableCreate($tables) as $tableName => $sqlCreate) {
                $dump .= 'DROP TABLE' . ' IF EXISTS `' . $tableName . '`;' . $sqlCreate;
                unset($sqlCreate);
                $query = $this->db->getQuery(true)->select('*')->from($this->db->qn($tableName));
                if ($rows = $this->db->setQuery($query)->loadRowList()) {
                    $values = '';
                    foreach ($rows as $i => $row) {
                        $query = [];
                        foreach ($row as $field) {
                            if (is_null($field)) {
                                $field = 'NULL';
                            } elseif (is_int($field)) {
                                $field = (int)$field;
                            } else {
                                $field = $this->db->quote($field);
                            }
                            $query[] = $field;
                        }
                        if ($values) {
                            $values .= ',' . PHP_EOL . '(' . implode(',', $query) . ')';
                        } else {
                            $values = PHP_EOL . '(' . implode(',', $query) . ')';
                        }
                    }
                    $dump .= PHP_EOL . 'INSERT ' . 'IGNORE INTO ' . $this->db->qn($tableName) . ' VALUES ' . $values . ';' . PHP_EOL;
                }
            }
            fwrite($fp, $dump);
            fclose($fp);
            if ($restoreFile = $app->input->get->get('restoreFile')) {
                $this->parseParams('import');
                $queryData = $this->generateQueryData('import');
                $queryData['executeBackupMySQL'] = 0;
                $queryData['restoreFile'] = $restoreFile;
                $queryData[JSession::getFormToken()] = 1;

                return $noRedirect ? JText::_('COMIEL_SUCCESS_BACKUP_MYSQL_DONE') : $this->redirect('import', 'import', $queryData, 'COMIEL_SUCCESS_BACKUP_MYSQL_DONE');
            }
        } catch (Exception $e) {
            return $noRedirect ? $e->getMessage() : $this->redirect('view', $app->input->get->get('restoreFile') ? 'import' : 'actions', [], $e->getMessage(), 'error');
        }

        return $noRedirect ? JText::_('COMIEL_SUCCESS_BACKUP_MYSQL_DONE') : $this->redirect('view', 'actions', [], 'COMIEL_SUCCESS_BACKUP_MYSQL_DONE');
    }

    /**
     * @return IEComiel
     *
     * @since 3.0.0
     */
    public function backupMySQLShutdown()
    {
        if (connection_status() >= 2) {
            return $this->redirect('view', 'import', ['status' => 'timeout_backup']);
        }

        return $this;
    }

    /**
     * @return IEComiel
     *
     * @since 3.0.0
     */
    public function clearImagesDatabase()
    {
        $app = JFactory::getApplication();
        $names = $this->getDeleteImagesTable();
        if (count($names)) {
            $app->enqueueMessage(_JSHOP_CLEARED);
            foreach ($names as $name) {
                try {
                    $query = $this->db->getQuery(true);
                    $query->delete($this->db->qn("#__jshopping_products_images"))
                          ->where($this->db->qn("image_name") . " = " . $this->db->quote($name));
                    $this->db->setQuery($query)->execute();
                    try {
                        $query = $this->db->getQuery(true);
                        $query->update($this->db->qn("#__jshopping_products"))
                              ->set($this->db->qn("image") . " = " . $this->db->quote(""))
                              ->where($this->db->qn("image") . " = " . $this->db->quote($name));
                        $this->db->setQuery($query)->execute();
                    } catch (Exception $e) {
                    }
                    $app->enqueueMessage($name);
                } catch (Exception $e) {
                }
            }
        } else {
            $app->enqueueMessage(_JSHOP_NOT_FOUNT_DELETE);
        }
        $app->redirect("index.php?option=com_jshopping&controller=importexport&task=view&active=" . $app->input->get("active") . "&ie_id=" . $this->ie_id);

        return $this;
    }

    /**
     * @return IEComiel
     *
     * @since 3.0.0
     */
    public function clearImagesDirectory()
    {
        $app = JFactory::getApplication();
        $jshopImportExport = JTable::getInstance('importExport', 'jshop');
        $jshopImportExport->load($this->ie_id);
        $ImportExport_Name = $jshopImportExport->get("description");
        $names = $this->getDeleteImages();
        if (count($names)) {
            JToolBarHelper::custom("clear_images", "trash", "trash.png", _JSHOP_CLEAR_IMAGES_YES, false);
            JToolBarHelper::spacer();
            JToolBarHelper::title($ImportExport_Name, "generic.png");
            JToolBarHelper::custom("view", "back icon-backward", "trash.png", _JSHOP_CLEAR_IMAGES_NO, false);
            $app->enqueueMessage(_JSHOP_CLEAR_IMAGES_QUESTION, "warning");
            echo '<form action = "index.php?option=com_jshopping&controller=importexport&task=view&ie_id=' . $this->ie_id . '" method = "post" id = "adminForm" name = "adminForm" enctype = "multipart/form-data">';
            echo '<input type = "hidden" name = "task" value = "" />';
            echo '<input type = "hidden" name = "ie_id" value = "' . $this->ie_id . '" />';
            echo '</form>';
            foreach ($names as $name) {
                echo $name . "<BR>";
            }
        } else {
            $app->enqueueMessage(_JSHOP_NOT_FOUNT_DELETE);
            $app->redirect("index.php?option=com_jshopping&controller=importexport&task=view&ie_id=" . $this->ie_id);
        }

        return $this;
    }

    /**
     * @return IEComiel
     *
     * @since 3.0.0
     */
    public function clear_images()
    {
        $app = JFactory::getApplication();
        $names = $this->getDeleteImages();
        if (count($names)) {
            $app->enqueueMessage(_JSHOP_DELETED);
            foreach ($names as $name) {
                @unlink($this->jshopConfig->get('image_product_path') . "/" . $name);
                $app->enqueueMessage($name);
            }
        } else {
            $app->enqueueMessage(_JSHOP_NOT_FOUNT_DELETE);
        }
        $app->redirect("index.php?option=com_jshopping&controller=importexport&task=view&ie_id=" . $this->ie_id);

        return $this;
    }

    /**
     * @return IEComiel
     *
     * @since 3.0.0
     */
    public function clear_images_table_question()
    {
        $app = JFactory::getApplication();
        $jshopImportExport = JTable::getInstance('importExport', 'jshop');
        $jshopImportExport->load($this->ie_id);
        $ImportExport_Name = $jshopImportExport->get("description");
        $names = $this->getDeleteImagesTable();
        if (count($names)) {
            JToolBarHelper::custom("clear_images_table", "save", "save", _JSHOP_CLEAR_IMAGES_TABLE_YES, false);
            JToolBarHelper::spacer();
            JToolBarHelper::title($ImportExport_Name, "generic.png");
            JToolBarHelper::custom("view", "cancel", "cancel", _JSHOP_CLEAR_IMAGES_TABLE_NO, false);
            $app->enqueueMessage(_JSHOP_CLEAR_IMAGES_TABLE_QUESTION, "warning");
            echo '<form action = "index.php?option=com_jshopping&controller=importexport&task=view&ie_id=' . $this->ie_id . '" method = "post" id = "adminForm" name = "adminForm" enctype = "multipart/form-data">';
            echo '<input type = "hidden" name = "task" value = "" />';
            echo '<input type = "hidden" name = "ie_id" value = "' . $this->ie_id . '" />';
            echo '</form>';
            foreach ($names as $name) {
                echo $name . "<BR>";
            }
        } else {
            $app->enqueueMessage(_JSHOP_NOT_FOUNT_DELETE);
            $app->redirect("index.php?option=com_jshopping&controller=importexport&task=view&active=" . $app->input->get("active", "") . "&ie_id=" . $this->ie_id);
        }

        return $this;
    }

    /**
     * @return IEComiel
     *
     * @throws Exception
     * @since 3.0.0
     */
    public function export()
    {
        $app = JFactory::getApplication();
        if (!JSession::checkToken(strtolower($app->input->getMethod()))) {
            return $this->redirect('view', 'export', [], 'JINVALID_TOKEN', 'error');
        }
        register_shutdown_function([$this, 'exportShutdown']);
        try {
            $exportStartTime = microtime(true);
            $this->parseParams('export');
            $this->jshopLang->setLang($this->params->get('language', 'en-GB'));
            $lang = JFactory::getLanguage();
            $lang->load($this->alias, __DIR__, $this->jshopLang->lang, true);
            unset($lang);
            $session = JFactory::getSession();
            if ($app->input->getMethod() === 'POST') {
                $session->set('exportStart', $exportStartTime, $this->alias);
                $session->set('exportCount', 0, $this->alias);
                $session->set('loadExcel', 0, $this->alias);
            }
            $header = $this->getChangedHeader();
            if (!$this->fileName = $this->params->get('fileName')) {
                $this->fileName = JUri::getInstance()->getHost() . '_' . date('Y.m.d.H.i.s') . '_export.' . $this->params->get('extension', 'xlsx');
            } else {
                $this->fileName .= '_export.' . $this->params->get('extension', 'xlsx');
            }
            $this->pRow = $app->input->getInt('start', 0);
			if ($this->pRow <= 0 && file_exists($this->fileNamePath())) {
				unlink($this->fileNamePath());
			}
            if ($app->input->getMethod() === 'GET' && file_exists($this->fileNamePath())) {
                $excelReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($this->fileNamePath());
                $excelReader->setIncludeCharts(false);
                $excelReader->setReadDataOnly(true);
                $excelReader->setReadEmptyCells(false);
                if ($excelReader instanceof \PhpOffice\PhpSpreadsheet\Reader\Csv) {
                    $excelReader->setDelimiter($this->params->get('csvDelimiter'));
                    $excelReader->setEnclosure($this->params->get('csvEnclosure'));
                }
                $session->set('loadExcel', 1, $this->alias);
                $this->spreadsheet = $excelReader->load($this->fileNamePath());
                $session->clear('loadExcel', $this->alias);
                $sheet = $this->spreadsheet->getActiveSheet();
                $pRow = $sheet->getHighestRow();
                unset($excelReader);
				$this->initImageData();
            } else {
                $this->spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet;
                $sheet = $this->spreadsheet->getActiveSheet();
                foreach (array_values($header) as $pColumn => $pValue) {
                    $sheet->setCellValueByColumnAndRow($pColumn+1, 1, $pValue);
                }
                $pRow = 1;
                unset($pColumn, $pValue);
				$this->initImageData(true);
            }
            foreach (JFactory::getLanguage()->getLocale() as $locale) {
                if (\PhpOffice\PhpSpreadsheet\Settings::setLocale($locale)) {
                    break;
                }
            }
            unset($locale);
            $query = $this->db->getQuery(true);
            $query->select('products.product_id');
            $query->from($this->db->qn('#__jshopping_products', 'products'));
            $query->where($this->db->qn('products.parent_id') . ' = ' . $this->db->q(0));
            $query->group($this->db->qn('products.product_id'));
            $query->order($this->db->qn('products.product_id'));
            $whereFilter = null;
            $categories = array_filter((array)$this->params->get('categories'));
            $manufacturers = array_filter((array)$this->params->get('manufacturers'));
            $selectCategories = in_array('categories', $header);
            if ($selectCategories || $categories) {
                $query->leftJoin($this->db->qn('#__jshopping_products_to_categories', 'p2c') . ' USING(' . $this->db->qn('product_id') . ')');
            }
            if ($selectCategories) {
                $query->select('GROUP_CONCAT(DISTINCT ' . $this->db->qn('p2c.category_id') . ' SEPARATOR \',\') AS categories');
            }
            if ($categories) {
                $whereFilter = $this->db->qn('p2c.category_id') . ' IN(' . implode(',', $categories) . ')';
            }
            if ($categories && $manufacturers) {
                $whereFilter .= ' ' . $this->params->get('logicCondition', 'AND') . ' ';
            }
            if ($manufacturers) {
                $whereFilter .= $this->db->qn('products.product_manufacturer_id') . ' IN(' . implode(',', $manufacturers) . ')';
            }
            if ($whereFilter) {
                $query->where('(' . $whereFilter . ')');
            }
            if ($this->params->get('published')) {
                $query->where($this->db->qn('products.product_publish') . ' = ' . $this->db->q(1));
            }
            if ($this->params->get('available')) {
                $query->where($this->db->qn('products.product_quantity') . ' > ' . $this->db->q(0));
            }
            $query->where($this->db->qn('product_id') . ' > ' . $this->db->q($this->pRow));
            unset($selectCategories, $manufacturers, $categories, $whereFilter);
            $products = $this->db->setQuery($query, 0, (int)$app->input->get('limit', self::ROWS_IN_EXPORT_MAX))->loadObjectList();
            $query->clear('order')
                  ->clear('limit')
                  ->clear('offset')
                  ->order($this->db->qn('products.product_id') . ' DESC');
            $lastProductId = (int)$this->db->setQuery($query, 0, 1)->loadResult();
            if (!$products && $this->pRow < $lastProductId) {
                return $this->redirect('view', 'export', [], JText::_('COMIEL_ERROR_COUNT_PRODUCTS'));
            }
            unset($db, $query);
            $exportCount = max(1, $session->get('exportCount', 0, $this->alias));
            $exportCount--;
            $executionTime = max(0, (int)ini_get('max_execution_time') * 0.8);
            foreach ($products as $key => $productData) {
                if (($executionTime && (microtime(true) - $exportStartTime) >= $executionTime) || $key >= (int)$app->input->get('limit', self::ROWS_IN_EXPORT_MAX)) {
                    $this->saveExportFile();
                    $queryData = $this->generateQueryData('export');
                    $queryData['start'] = $this->pRow;
					if ($app->isSite()) {
						$queryData['key'] = JSFactory::getConfig()->securitykey;
						return $this->redirect('start', null, $queryData);
					} else {
						$queryData[JSession::getFormToken()] = 1;
						return $this->redirect('export', 'export', $queryData);
					}
                }
                $jshopProduct = JTable::getInstance('product', 'jshop');
                $jshopProduct->load($productData->product_id);
                $productId = (int)$jshopProduct->get('product_id');
				$this->storeImageData('products', $productId);
                $row = [];
                foreach ($header as $fieldName) {
                    switch ($fieldName) {
                        case 'id':
                            $row[$fieldName] = $productId;
                            break;
                        case 'ean':
                        case 'template':
                        case 'url':
                            $row[$fieldName] = (string)$jshopProduct->get('product_' . $fieldName);
                            break;
                        case 'name':
                        case 'alias':
                        case 'short_description':
                        case 'description':
                        case 'meta_title':
                        case 'meta_description':
                        case 'meta_keyword':
                            $row[$fieldName] = (string)$jshopProduct->get($this->jshopLang->get($fieldName));
                            break;
                        case 'qty':
                            $row[$fieldName] = (float)$jshopProduct->get('product_quantity');
                            break;
                        case 'unlimited':
                            $row[$fieldName] = (string)$jshopProduct->get('unlimited') ? JText::_('JYES') : JText::_('JNO');
                            break;
                        case 'publish':
                            $row[$fieldName] = (string)$jshopProduct->get('product_publish') ? JText::_('JYES') : JText::_('JNO');
                            break;
                        case 'categories':
                            $categories = [];
                            foreach (explode(',', $productData->categories) as $categoryId) {
                                $categories[$categoryId] = $this->asStringCategory((int)$categoryId);
                            }
                            $row[$fieldName] = implode('|', $categories);
                            break;
                        case 'tax':
                            $row[$fieldName] = $this->asStringTax((int)$jshopProduct->get('product_tax_id'));
                            break;
                        case 'currency':
                            $row[$fieldName] = $this->asStringCurrency((int)$jshopProduct->get('currency_id'));
                            break;
                        case 'old_price':
                        case 'buy_price':
                        case 'price':
                        case 'weight':
                        case 'bonus_add':
                        case 'bonus_sub':
                            $row[$fieldName] = (float)$jshopProduct->get('product_' . $fieldName);
                            break;
                        case 'basic_price':
                            $row[$fieldName] = (float)$jshopProduct->get('weight_volume_units');
                            break;
                        case 'unit':
                            $row[$fieldName] = $this->asStringUnit((int)$jshopProduct->get('basic_price_unit_id'));
                            break;
                        case 'add_price':
                            $row[$fieldName] = $this->asStringAddPrice($jshopProduct);
                            break;
                        case 'image':
                            $row[$fieldName] = (string)$jshopProduct->get('image');
                            break;
                        case 'images':
                            $row[$fieldName] = $this->asStringImages($jshopProduct);
                            break;
                        case 'manufacturer':
                            $row[$fieldName] = $this->asStringManufacturer((int)$jshopProduct->get('product_manufacturer_id'));
                            break;
                        case 'delivery_times':
                            $row[$fieldName] = $this->asStringDeliveryTimes((int)$jshopProduct->get('delivery_times_id'));
                            break;
                        case 'hits':
                            $row[$fieldName] = (int)$jshopProduct->get('hits');
                            break;
                        case 'label':
                            $row[$fieldName] = $this->asStringLabel((int)$jshopProduct->get('label_id'));
                            break;
                        case 'free_attributes':
                            $row[$fieldName] = $this->asStringFreeAttribute($jshopProduct);
                            break;
                        case 'attributes':
                            $row[$fieldName] = $this->asStringAttributes($jshopProduct);
                            break;
                        case 'extra_fields':
                            $row[$fieldName] = $this->asStringExtraFields($jshopProduct);
                            break;
                        case 'video':
                            $row[$fieldName] = $this->asStringVideos($jshopProduct, ';');
                            break;
                        case 'files':
                            $row[$fieldName] = $this->asStringFiles($jshopProduct);
                            break;
                        case 'product_url':
                            $jshopProduct->getCategory();
                            $row[$fieldName] = $this->getURL($jshopProduct);
                            break;
                        case 'related':
                            $row[$fieldName] = $this->asStringRelated($productId);
                            break;
                        case 'vendor':
                            $row[$fieldName] = $this->asStringVendor((int)$jshopProduct->get('vendor_id'));
                            break;
                        default:
                            try {
                                $row[$fieldName] = (string)$jshopProduct->get($fieldName);
                            } catch (Exception $e) {
                                $row[$fieldName] = null;
                            }
                            break;
                    }
                }
                $pRow++;
                foreach (array_values($row) as $pColumn => $pValue) {
                    if ($pValue === null) {
                        continue;
                    }
                    $sheet->setCellValueByColumnAndRow($pColumn+1, $pRow, $pValue);
                }
                $this->pRow = $productId;
                $exportCount++;
                $session->set('exportCount', $exportCount, $this->alias);
            }
            $this->saveExportFile();
            if ($this->pRow < $lastProductId) {
                $queryData = $this->generateQueryData('export');
                $queryData['start'] = $this->pRow;

				if ($app->isSite()) {
					$queryData['key'] = JSFactory::getConfig()->securitykey;
					return $this->redirect('start', null, $queryData);
				} else {
					$queryData[JSession::getFormToken()] = 1;
					return $this->redirect('export', 'export', $queryData);
				}
            }
            $exportCount = $session->get('exportCount', 0, $this->alias);
            $app->enqueueMessage(JText::_('COMIEL_SUCCESS_EXPORT_DONE'));
            $app->enqueueMessage(JText::sprintf('COMIEL_SUCCESS_EXPORT_PRODUCTS', $exportCount));
            $exportStart = $session->get('exportStart', 0, $this->alias);
            $exportFinish = microtime(true) - $exportStart;
            if ($exportFinish >= 60) {
                $exportFinish = $exportFinish / 60;
                $exportFinishTime = JText::_('COMIEL_SUCCESS_TIME_MIN');
            } else {
                $exportFinishTime = JText::_('COMIEL_SUCCESS_TIME_SEC');
            }
            $session->clear('exportStart', $this->alias);
            $app->enqueueMessage(JText::sprintf('COMIEL_SUCCESS_TIME', number_format($exportFinish, 2, ',', ' ') . ' ' . $exportFinishTime));
            if ($this->params->get('executeArchiveImages')) {
                return $this->redirect('backupImages', 'export', [
                    'fileName' => basename($this->fileName),
                    JSession::getFormToken() => 1,
                ]);
            }
			$this->clearImageData();

			if ($app->isSite()) {
				die;
			}
            return $this->redirect('view', 'export');
        } catch (Exception $e) {
			$this->clearImageData();

            return $this->redirect('view', 'export', [], $e->getMessage(), 'error');
        }
    }

    /**
     * @return IEComiel
     *
     * @since 3.0.0
     */
    public function exportShutdown()
    {
        if (connection_status() == CONNECTION_ABORTED) {
            return $this->redirect('view', 'export', [], CONNECTION_ABORTED, 'error');
        }
        if (connection_status() == CONNECTION_TIMEOUT) {
            if (JFactory::getSession()->get('loadExcel', null, $this->alias)) {
                return $this->redirect('view', 'export', [], 'Need more time for read file', 'error');
            } else {
                $this->spreadsheet->getSheet(0)->removeRow($this->pRow);
                $this->saveExportFile();
                $queryData = $this->generateQueryData('export');
                $queryData['status'] = 'timeout_export';
                $queryData['start'] = $this->pRow - 1;

				if ($app->isSite()) {
					$queryData['key'] = JSFactory::getConfig()->securitykey;
					return $this->redirect('start', null, $queryData);
				} else {
					$queryData[JSession::getFormToken()] = 1;
					return $this->redirect('export', 'export', $queryData);
				}
            }
        }

        return $this;
    }

    /**
     * @return IEComiel|string
     *
     * @since 3.0.0
     */
    public function import()
    {
        $app = JFactory::getApplication();
        $noRedirect = $app->input->get(strtolower('noRedirect'));
        if (!JSession::checkToken(strtolower($app->input->getMethod()))) {
            return $noRedirect ? JText::_('JINVALID_TOKEN') : $this->redirect('view', 'import', [], 'JINVALID_TOKEN', 'error');
        }
        register_shutdown_function([$this, 'importShutdown']);
        try {
            $messageBackupMySQL = null;
            $importStartTime = microtime(true);
            $this->parseParams('import');
            if ($this->params->get('debug') || $app->input->get('debug')) {
                JFactory::getDbo()->setDebug(true);
                JLog::addLogger(['text_file' => $this->alias . DIRECTORY_SEPARATOR . $importStartTime . '.debug.php'], JLog::DEBUG, [$this->alias]);
            }
            $session = JFactory::getSession();
            if ($app->input->getMethod() === 'POST' || ($app instanceof JApplicationSite && $app->input->get->get('task') === 'start')) {
                $task = $app->input->post->get('task');
                if ($task === 'import') {
                    $this->fileName = $this->uploadFile($app->input->files->get('file'));
                } elseif ($task === 'restoreFile') {
                    $cid = $app->input->post->get('cid', [], 'array');
                    $this->fileName = current($cid);
                    unset($cid);
                }
                if ($app instanceof JApplicationSite && $app->input->get->get('task') === 'start') {
                    $this->fileName = $app->input->get->get('restoreFile');
                }
                unset($task);
                if (!$this->fileName) {
                    throw new Exception(JText::_('COMIEL_ERROR_GENERATE_FILENAME'));
                }
				$session->set('importStart', $importStartTime, $this->alias);
				$session->set('countInsert', 0, $this->alias);
				$session->set('countUpdate', 0, $this->alias);
				$session->set('countIgnore', 0, $this->alias);
				$session->set('countIgnoreInsert', 0, $this->alias);
				$session->set('countIgnoreUpdate', 0, $this->alias);
				$session->set('loadExcel', 0, $this->alias);
                if ($this->params->get('executeBackupMySQL')) {
                    if ($noRedirect) {
                        $fileName = $this->fileName;
                        $messageBackupMySQL = $this->backupMySQL();
                        $this->fileName = $fileName;
                    } else {
                        $queryData = $this->generateQueryData('import');
                        $queryData['restoreFile'] = $this->fileName;
                        $queryData[JSession::getFormToken()] = 1;

                        return $this->redirect('backupMySQL', 'import', $queryData);
                    }
                }
            } elseif ($app->input->getMethod() === 'GET') {
                $restoreFile = $app->input->get->get('restoreFile');
                if (file_exists($this->filePath() . $restoreFile)) {
                    $this->fileName = $restoreFile;
                } elseif (file_exists($restoreFile)) {
                    JFile::copy($restoreFile, $this->filePath() . basename($restoreFile));
                    $this->fileName = basename($restoreFile);
                } elseif (substr($restoreFile, 0, 7) === 'http://' || substr($restoreFile, 0, 8) === 'https://') {
                    file_put_contents($this->filePath() . basename($restoreFile), file_get_contents($restoreFile));
                    $this->fileName = basename($restoreFile);
                }
            }
            if (!$this->fileName) {
                throw new Exception(JText::_('COMIEL_ERROR_GENERATE_FILENAME'));
            }
            if (!file_exists($this->fileNamePath())) {
                throw new Exception(JText::sprintf('COMIEL_ERROR_FILE_EXISTS', $this->fileName));
            }
            $start = (int)$app->input->get('start');
            $limit = (int)$this->params->get('maxLimitRead');
            if ($limit <= 0) {
                $limit = self::ROWS_IN_EXPORT_MAX;
            }
            $excelReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($this->fileNamePath());
            if ($start) {
				$totalRows = $session->get('totalRows', 0, $this->alias);
            } else {
				$worksheetData = $excelReader->listWorksheetInfo($this->fileNamePath());
				$totalRows = $worksheetData[0]['totalRows'] - 1;
				$session->set('totalRows', $totalRows, $this->alias);
            }
            $excelReader->setIncludeCharts(false);
            $excelReader->setReadDataOnly(true);
            $excelReader->setReadEmptyCells(false);
            if ($excelReader instanceof \PhpOffice\PhpSpreadsheet\Reader\Csv) {
                $excelReader->setDelimiter($this->params->get('csvDelimiter'));
                $excelReader->setEnclosure($this->params->get('csvEnclosure'));
                $excelReader->setInputEncoding(strtoupper($this->params->get('csvEncode')));
            }
            $chunkFilter = new chunkReadFilter; 
            if ($start) {
                $chunkFilter->setRows(1, 1);
                $excelReader->setReadFilter($chunkFilter);
                $session->set('loadExcel', 1, $this->alias);
                $this->spreadsheet = $excelReader->load($this->fileNamePath());
                $session->clear('loadExcel', $this->alias);
                $this->spreadsheet->setActiveSheetIndex(0);
                $this->structure();
            }
            $chunkFilter->setRows($start ? $start : 1, $limit + 1);
            $excelReader->setReadFilter($chunkFilter);
            $session->set('loadExcel', 1, $this->alias);
            JLog::add('Before load: ' . (microtime(true) - $importStartTime), JLog::DEBUG, $this->alias);
            $this->spreadsheet = $excelReader->load($this->fileNamePath());
            JLog::add('After load: ' . (microtime(true) - $importStartTime), JLog::DEBUG, $this->alias);
            $session->clear('loadExcel', $this->alias);
            $this->spreadsheet->setActiveSheetIndex(0);
            $sheet = $this->spreadsheet->getActiveSheet();
            $sheet->garbageCollect();
            $rows = $sheet->getRowIterator();
            if ($start) {
                $rows->seek($start);
            } else {
                $this->structure();
                if ($this->params->get('unPublish')) {
                    $this->unPublish($this->params->get('unPublishCategories'), $this->params->get('unPublishLogicCondition'), $this->params->get('unPublishManufacturers'));
                }
                if ($this->params->get('unCount')) {
                    $this->unCount($this->params->get('unCountCategories'), $this->params->get('unCountLogicCondition'), $this->params->get('unCountManufacturers'));
                }
            }
            $rows->next();
            JLog::add('Structure: ' . (microtime(true) - $importStartTime), JLog::DEBUG, $this->alias);
            $highestRow = (int)$sheet->getHighestRow();
            unset($excelReader, $chunkFilter, $sheet);
            if ($highestRow < $start) {
                return $noRedirect ? JText::_('COMIEL_ERROR_COUNT_ROWS_IN_EXCEL') : $this->redirect('view', 'import', [], 'COMIEL_ERROR_COUNT_ROWS_IN_EXCEL');
            } elseif ($highestRow == $start) {
                $rows->next();
            }
            $countIgnoreInsert = $session->get('countIgnoreInsert', 0, $this->alias);
            $countIgnoreUpdate = $session->get('countIgnoreUpdate', 0, $this->alias);
            $countInsert = $session->get('countInsert', 0, $this->alias);
            $countUpdate = $session->get('countUpdate', 0, $this->alias);
            $countIgnore = $session->get('countIgnore', 0, $this->alias);
            $uniqueFieldName = $this->params->get('uniqueFieldName');
            $query = $this->db->getQuery(true);
            $query->select($this->db->qn('product_id'));
            switch ($uniqueFieldName) {
                case 'id':
                    $query->select($this->db->qn('product_id', $uniqueFieldName));
                    break;
                case 'name':
                    $query->select($this->db->qn($this->jshopLang->get('name'), $uniqueFieldName));
                    break;
                case 'ean':
                    $query->select($this->db->qn('product_ean', $uniqueFieldName));
                    break;
                case 'manufacturer_code':
                    $query->select($this->db->qn('manufacturer_code', $uniqueFieldName));
                    break;
                default:
                    return $noRedirect ? JText::_('COMIEL_IMPORT_UNIQUE_FIELD_NAME_ERROR') : $this->redirect('view', 'import', [], 'COMIEL_IMPORT_UNIQUE_FIELD_NAME_ERROR');
                    break;
            }
            $query->from($this->db->qn('#__jshopping_products'));
            $query->order($this->db->qn('product_id'));
            $this->db->setQuery($query);
            $existsProducts = $this->db->loadObjectList($uniqueFieldName);
            $executionTime = ini_get('max_execution_time') - 5;
			$maxExecutionTime = (int)$this->params->get('maxExecutionTime');
			if ($maxExecutionTime > 0 && $maxExecutionTime < $executionTime) {
				$executionTime = $maxExecutionTime;
			}
            unset($db, $query, $maxExecutionTime);
            $jshopAddonBonusSystem = JTable::getInstance('addon', 'jshop');
            $jshopAddonBonusSystem->loadAlias('addon_bonus_system');
            JLog::add('Before While: ' . (microtime(true) - $importStartTime), JLog::DEBUG, $this->alias);
            while ($rows->valid()) {
                $this->pRow = $rows->key();
                if ($executionTime > 0 && (microtime(true) - $importStartTime) >= $executionTime) {
                    if ($this->pRow === 2 || $this->pRow === $start) {
                        throw new RuntimeException(JText::_('COMIEL_ERROR_RECURS'));
                    }
                    $queryData = $this->generateQueryData('import');
                    $queryData['restoreFile'] = $this->fileName;
                    $queryData[JSession::getFormToken()] = 1;
                    $queryData['start'] = $this->pRow - 1;
                    $task = 'import';
                    if ($noRedirect) {
                        $queryData['key'] = $app->input->get->get('key');
                        $task = 'start';
                    }
					
					if ($app->isAdmin()) {
						$start = $this->pRow - 1;
						$session->set('start', $start, 'comiel');
						$session->set('restoreFile', $this->fileName, 'comiel');
						$memoryGetPeakUsage = $session->get('memoryGetPeakUsage', 0, 'comiel');
						$session->set('memoryGetPeakUsage', max($memoryGetPeakUsage, memory_get_peak_usage(true)), 'comiel');
						$app->redirect('index.php?option=com_jshopping&controller=importexport&task=view&ie_id='.$this->ie_id.'&active=import&start='.$start);
					}

                    return $this->redirect($task, 'import', $queryData);
                }
                $cells = $rows->current()->getCellIterator();
                if (($pUniqueValue = $this->getValue($cells, $uniqueFieldName)) === null) {
                    $countIgnore++;
                    $session->set('countIgnore', $countIgnore, $this->alias);
                    $rows->next();
                    continue;
                }
                $productId = null;
                if (isset($existsProducts[$pUniqueValue])) {
                    $productId = (int)$existsProducts[$pUniqueValue]->product_id;
                }
                $jshopProduct = JTable::getInstance('product', 'jshop');
                if ($productId) {
                    if (!$this->params->get('update')) {
                        $countIgnoreUpdate++;
                        $session->set('countIgnoreUpdate', $countIgnoreUpdate, $this->alias);
                        $rows->next();
                        continue;
                    }
                    $jshopProduct->load($productId);
                    $countUpdate++;
                    $session->set('countUpdate', $countUpdate, $this->alias);
                    $isNew = false;
                } else {
                    if (!$this->params->get('insert')) {
                        $countIgnoreInsert++;
                        $session->set('countIgnoreInsert', $countIgnoreInsert, $this->alias);
                        $rows->next();
                        continue;
                    }
                    $jshopProduct->set('product_date_added', date('Y-m-d H:i:s'));
                    if ($uniqueFieldName === 'id') {
                        $jshopProduct->set('product_id', $pUniqueValue);
                        $jshopProduct->getDbo()->insertObject($jshopProduct->getTableName(), $jshopProduct, $jshopProduct->getKeyName());
                        $existsProducts[$pUniqueValue] = (object)['product_id' => $pUniqueValue];
                    }
                    $countInsert++;
                    $session->set('countInsert', $countInsert, $this->alias);
                    $isNew = true;
                }
                unset($productId);
                JLog::add('Get product: ' . (microtime(true) - $importStartTime), JLog::DEBUG, $this->alias);
                if ($this->params->get('updateModified')) {
                    $jshopProduct->set('date_modify', date('Y-m-d H:i:s'));
                }
                $structure = $this->get('structure', []);
                if (!isset($structure['currency']) && $isNew) {
                    $jshopProduct->set('currency_id', $this->getCurrencyId(null, ':', $isNew));
                }
                JLog::add('Start Structure: ' . (microtime(true) - $importStartTime), JLog::DEBUG, $this->alias);
				$needSetMinPrice = $add_price = false;
				$extraFields = $attributes = array();
                foreach ($structure as $columnName => $columnId) {
                    if (($value = $this->getValue($cells, $columnName)) === null) {
                        continue;
                    }
                    if ($value === false) {
                        $value = null;
                    }
                    $langColumnName = null;
                    if (strpos($columnName, 'name_') !== false
                        || strpos($columnName, 'alias_') !== false
                        || strpos($columnName, 'short_description_') !== false
                        || strpos($columnName, 'description_') !== false
                        || strpos($columnName, 'meta_title_') !== false
                        || strpos($columnName, 'meta_description_') !== false
                        || strpos($columnName, 'meta_keyword_') !== false) {
                        $langColumnName = $columnName;
                        $columnName = 'langColumnName';
                    }
                    switch ($columnName) {
                        case 'price':
							$value = (float)str_replace(',', '.', $value);
                            $jshopProduct->set('product_' . $columnName, $value);
							$needSetMinPrice = true;
                            break;
                        case 'ean':
                        case 'template':
                        case 'url':
                            $jshopProduct->set('product_' . $columnName, (string)$value);
                            break;
                        case 'old_price':
                        case 'buy_price':
                        case 'weight':
                            $jshopProduct->set('product_' . $columnName, (float)str_replace(',', '.', $value));
                            break;
                        case 'bonus_add':
                        case 'bonus_sub':
                            if ($jshopAddonBonusSystem->get('id')) {
                                $jshopProduct->set('product_' . $columnName, (float)str_replace(',', '.', $value));
                            }
                            break;
                        case 'qty':
                            $firstSymbol = substr($value, 0, 1);
							$value = (float)str_replace(',', '.', $value);
                            switch ($firstSymbol) {
                                case '+':
                                case '-':
                                    $jshopProduct->set('product_quantity', (float)$jshopProduct->get('product_quantity') + $value);
                                    break;
                                default:
                                    $jshopProduct->set('product_quantity', $value);
                                    break;
                            }
                            break;
                        case 'add_qty':
                            $jshopProduct->set('product_quantity', (float)$jshopProduct->get('product_quantity') + (float)str_replace(',', '.', $value));
                            break;
                        case 'basic_price':
                            $jshopProduct->set('weight_volume_units', (float)str_replace(',', '.', $value));
                            break;
                        case 'name':
                        case 'alias':
                        case 'short_description':
                        case 'description':
                        case 'meta_title':
                        case 'meta_description':
                        case 'meta_keyword':
                            $jshopProduct->set($this->jshopLang->get($columnName), (string)$value);
                            break;
                        case 'langColumnName':
                            $jshopProduct->set($langColumnName, (string)$value);
                            break;
                        case 'publish':
                            $value = str_replace(_JSHOP_YES, '1', (string)$value);
                            $value = str_replace(_JSHOP_NO, '0', $value);
                            $value = str_replace(JText::_('JYES'), '1', $value);
                            $value = str_replace(JText::_('JNO'), '0', $value);
                            $jshopProduct->set('product_' . $columnName, (int)$value);
                            break;
                        case 'unlimited':
                            $value = str_replace(_JSHOP_YES, '1', (string)$value);
                            $value = str_replace(_JSHOP_NO, '0', $value);
                            $value = str_replace(JText::_('JYES'), '1', $value);
                            $value = str_replace(JText::_('JNO'), '0', $value);
                            $value = (int)$value;
                            $jshopProduct->set($columnName, $value);
                            if ($value) {
                                $jshopProduct->set('product_quantity', 1);
                            }
                            break;
                        case 'hits':
                            $jshopProduct->set($columnName, (int)$value);
                            break;
                        case 'manufacturer_code':
                            $jshopProduct->set($columnName, (string)$value);
                            break;
                        case 'tax':
                            if ($value === 'Array') {
                                break;
                            }
                            if ($value === null) {
                                $jshopProduct->set('product_' . $columnName . '_id', 0);
                            } elseif (($taxId = $this->getTaxId($value, ':')) !== null) {
                                $jshopProduct->set('product_' . $columnName . '_id', $taxId);
                            }
                            break;
                        case 'manufacturer':
                            $jshopProduct->set('product_' . $columnName . '_id', $this->getManufacturerId($value, ':', (bool)$this->params->get('generateAlias')));
                            break;
                        case 'currency':
                            $jshopProduct->set($columnName . '_id', $this->getCurrencyId($value, ':', $isNew));
                            break;
                        case 'vendor':
                            $jshopProduct->set($columnName . '_id', $this->getVendorId($value));
                            break;
                        case 'delivery_times':
                            $jshopProduct->set($columnName . '_id', $this->getDeliveryTimeId($value));
                            break;
                        case 'label':
                            $jshopProduct->set($columnName . '_id', $this->getLabelId($value));
                            break;
                        case 'unit':
                            $jshopProduct->set('basic_price_unit_id', $this->getUnitId($value));
                            break;
                        case 'image':
                            if (!$jshopProduct->get('product_id')) {
                                $jshopProduct->store();
                            }
                            if ($this->getValue($cells, 'images') === null) {
                                $value = $this->getImageName($jshopProduct, $value, ':', (bool)$this->params->get('executeResize'));
                            }
                            $jshopProduct->set($columnName, $value);
                            break;
                        case 'images':
                            if (!$jshopProduct->get('product_id')) {
                                $jshopProduct->store();
                            }
                            $imageName = $this->getImageName($jshopProduct, $value, ':', (bool)$this->params->get('executeResize'));
                            if ($this->getValue($cells, 'image') === null) {
                                $jshopProduct->set('image', $imageName);
                            }
                            break;
                        case 'free_attributes':
                            if (!$jshopProduct->get('product_id')) {
                                $jshopProduct->store();
                            }
                            $this->parseFreeAttributes($jshopProduct, $value);
                            break;
                        case 'categories':
                        case 'category':
                            if (!$jshopProduct->get('product_id')) {
                                $jshopProduct->store();
                            }
                            if ($value === null) {
                                $value = '-1';
                            }
                            $this->parseCategories($jshopProduct, $value, ':', (bool)$this->params->get('generateAlias'));
                            break;
                        case 'attributes':
                        case 'add_attributes':
							$attributes[] = array($value, $columnName === 'attributes');
                            break;
                        case 'extra_fields':
                        case 'add_extra_fields':
							$extraFields[] = array($value, $columnName === 'extra_fields');
                            break;
                        case 'video':
                            if (!$jshopProduct->get('product_id')) {
                                $jshopProduct->store();
                            }
                            $this->parseVideo($jshopProduct, $value);
                            break;
                        case 'files':
                            if (!$jshopProduct->get('product_id')) {
                                $jshopProduct->store();
                            }
                            $this->parseFiles($jshopProduct, $value);
                            break;
                        case 'add_price':
							$needSetMinPrice = true;
							$add_price = $value;
                            break;
                        case 'related':
                            if (!$jshopProduct->get('product_id')) {
                                $jshopProduct->store();
                            }
                            $this->parseRelated($jshopProduct, $value, $this->params->get('relatedFieldName'));
                            break;
                        default:
                            break;
                    }
                }
                JLog::add('End Structure: ' . (microtime(true) - $importStartTime), JLog::DEBUG, $this->alias);
                if (!$jshopProduct->get($this->jshopLang->get('alias')) && $this->params->get('generateAlias')) {
                    $alias = $this->generateAlias(JTable::getInstance('product', 'jshop'), $jshopProduct->get($this->jshopLang->get('name')));
                    $jshopProduct->set($this->jshopLang->get('alias'), $alias);
                }
				if ($attributes) {
					if (!$jshopProduct->product_id) {
						$jshopProduct->store();
					}
					foreach ($attributes as $data) {
						$this->parseAttributes($jshopProduct, $data[0], ':', (bool)$this->params->get('executeResize'), $data[1]);
					}
					$needSetMinPrice = true;
				}
				if ($extraFields) {
					if (!$jshopProduct->product_id) {
						$jshopProduct->store();
					}
					foreach ($extraFields as $data) {
						$this->parseExtraFields($jshopProduct, $data[0], ':', $data[1]);
					}
				}
				if ($needSetMinPrice) {
					$maxDiscount = false;
					if ($add_price !== false) {
						if (!$jshopProduct->product_id) {
							$jshopProduct->store();
						}
						$maxDiscount = $this->parsePrices($jshopProduct, $add_price, ':');
					}
					if ($jshopProduct->product_is_add_price) {
						$jshopProduct->different_prices = 1;
						if ($maxDiscount === false) {
							$maxDiscount = (float)$this->db->setQuery(
								$this->db->getQuery(true)
									->select('max(discount)')
									->from($this->db->qn('#__jshopping_products_prices'))
									->where('product_id = ' . $this->db->q($jshopProduct->product_id))
							)->loadResult();
						}
					} else {
						$jshopProduct->different_prices = 0;
						$maxDiscount = 0;
					}
					
					$minPrice = $jshopProduct->product_price;

					$attributes = $jshopProduct->getAttributes();
					if ($attributes) {
						$totalQty = 0;
						$attrPrices = array();
						foreach ($attributes as $row) {
							$attrPrices[] = $row->price;
							if ($row->price < $minPrice) {
								$minPrice = $row->price;
							}
							$totalQty += $row->count;
						}
						$jshopProduct->product_quantity = $jshopProduct->unlimited ? 1 : $totalQty;
						if (count(array_unique($attrPrices))>1) {
							$jshopProduct->different_prices = 1;
						}
					}
					
					if (JSFactory::getConfig()->product_price_qty_discount == 1){
						$minPrice = $minPrice - $maxDiscount; //discount value
					}else{
						$minPrice = $minPrice - ($minPrice * $maxDiscount / 100); //discount percent
					}

					$attributes = $jshopProduct->getAttributes2();
					if ($attributes) {
						$tmpprice = array();
						$attrPrices = array();
						foreach ($attributes as $row) {
							$attrPrices = $row->price_mod . $row->addprice;
							if ($row->price_mod == '+') {
								$tmpprice[] = $minPrice + $row->addprice;
							} else if ($row->price_mod == '-') {
								$tmpprice[] = $minPrice - $row->addprice;
							} else if ($row->price_mod == '*') {
								$tmpprice[] = $minPrice * $row->addprice;
							} else if ($row->price_mod == '/') {
								$tmpprice[] = $minPrice / $row->addprice;
							} else if ($row->price_mod == '%') {
								$tmpprice[] = $minPrice * $row->addprice / 100;
							} else if ($row->price_mod == '=') {
								$tmpprice[] = $row->addprice;
							}
						}
						$minPrice = min($tmpprice);
						if (count(array_unique($attrPrices))>1) {
							$jshopProduct->different_prices = 1;
						}
					}

					$jshopProduct->min_price = $minPrice;
				}
                $jshopProduct->store();
                JLog::add('After store ID '.$jshopProduct->product_id.': ' . (microtime(true) - $importStartTime), JLog::DEBUG, $this->alias);
                unset($jshopProduct);
                $rows->next();
            }
            JLog::add('After While: ' . (microtime(true) - $importStartTime), JLog::DEBUG, $this->alias);
            if (($this->pRow == (($start ? $start : 1) + $limit) || $this->pRow < $highestRow) && $start !== $highestRow) {
                $queryData = $this->generateQueryData('import');
                $queryData['restoreFile'] = $this->fileName;
                $queryData[JSession::getFormToken()] = 1;
                $queryData['start'] = $this->pRow;
                $task = 'import';
                if ($noRedirect) {
                    $queryData['key'] = $app->input->get->get('key');
                    $task = 'start';
                }

                return $this->redirect($task, 'import', $queryData);
            }
            unset($rows, $cells, $importStartTime, $executionTime, $uniqueFieldName, $existsProducts, $highestRow, $start, $limit);
            $importStart = $session->get('importStart', 0, $this->alias);
            $session->clear('importStart', $this->alias);
            $importFinish = microtime(true) - $importStart;
            $app->enqueueMessage(JText::_('COMIEL_SUCCESS_IMPORT_DONE'));
            $app->enqueueMessage(JText::sprintf('COMIEL_SUCCESS_IMPORT_INSERT', $countInsert));
            $app->enqueueMessage(JText::sprintf('COMIEL_SUCCESS_IMPORT_UPDATE', $countUpdate));
            if ($countIgnore) {
                $app->enqueueMessage(JText::sprintf('COMIEL_SUCCESS_IMPORT_IGNORE', $countIgnore));
            }
            if ($countIgnoreInsert) {
                $app->enqueueMessage(JText::sprintf('COMIEL_SUCCESS_IMPORT_IGNORE_INSERT', $countIgnoreInsert));
            }
            if ($countIgnoreUpdate) {
                $app->enqueueMessage(JText::sprintf('COMIEL_SUCCESS_IMPORT_IGNORE_UPDATE', $countIgnoreUpdate));
            }
            if ($app instanceof JApplicationAdministrator) {
                return $this->redirect('view', 'import', [], JText::sprintf('COMIEL_SUCCESS_TIME', gmdate('H:i:s', $importFinish)));
            }
            $messages = [];
            if ($messageBackupMySQL) {
                $messages[] = $messageBackupMySQL;
            }
            foreach ($app->getMessageQueue(true) as $message) {
                $messages[] = ucfirst($this->alias) . ' ' . ucfirst($message['type']) . ': ' . $message['message'];
            }
            $messages[] = JText::sprintf('COMIEL_SUCCESS_TIME', gmdate('H:i:s', $importFinish));

            return implode('<br />', $messages) . '<br />';
        } catch (Exception $e) {
            return $noRedirect ? $e->getMessage() : $this->redirect('view', 'import', [], $e->getMessage(), 'error');
        }
    }

    /**
     * @return IEComiel
     *
     * @since 3.0.0
     */
    public function importShutdown()
    {
        if (connection_status() == CONNECTION_ABORTED) {
            return $this->redirect('view', 'import', [], CONNECTION_ABORTED, 'error');
        }
        if (connection_status() == CONNECTION_TIMEOUT) {
            if (JFactory::getSession()->get('loadExcel', null, $this->alias)) {
                return $this->redirect('view', 'import', [], 'Need more time for read file', 'error');
            } else {
                $this->parseParams('import');
                $queryData = $this->generateQueryData('import');
                $queryData['status'] = 'timeout_import';
                $queryData['restoreFile'] = $this->fileName;
                $queryData[JSession::getFormToken()] = 1;
                $queryData['start'] = $this->pRow;

                return $this->redirect('import', 'import', $queryData);
            }
        }

        return $this;
    }

    /**
     * @return IEComiel
     *
     * @since 3.0.0
     */
    public function removeFile()
    {
        $app = JFactory::getApplication();
        $files = $app->input->get('cid', [], 'array');
        $activeImport = false;
        foreach ($files as $file) {
            $activeImport = strpos($file, '_import.') || strpos($file, '_backup.');
            $filePath = $this->jshopConfig->get('importexport_path') . $this->alias . DIRECTORY_SEPARATOR . $file;
            if (file_exists($filePath) && JFile::delete($filePath)) {
                $app->enqueueMessage(JText::sprintf('COMIEL_SUCCESS_FILE_DELETED', $file));
            }
            break;
        }

        return $this->redirect('view', $activeImport ? 'import' : 'export');
    }

    /**
     * @return IEComiel
     *
     * @since 3.0.0
     */
    public function removeFiles()
    {
        $app = JFactory::getApplication();
        $files = $app->input->get('cid', [], 'array');
        if (!$files) {
            $app->enqueueMessage(JText::_('COMIEL_WARNING_FILES_NOT_EXISTS'), 'warning');
        }
        $activeImport = false;
        foreach ($files as $file) {
            $activeImport = strpos($file, '_import.') || strpos($file, '_backup.');
            $filePath = $this->jshopConfig->get('importexport_path') . $this->alias . DIRECTORY_SEPARATOR . $file;
            if (file_exists($filePath) && JFile::delete($filePath)) {
                $app->enqueueMessage(JText::sprintf('COMIEL_SUCCESS_FILE_DELETED', $file));
            } else {
                $app->enqueueMessage(JText::sprintf('COMIEL_ERROR_FILE_NOT_DELETED', $file), 'error');
            }
        }

        return $this->redirect('view', $activeImport ? 'import' : 'export');
    }

    /**
     * @return IEComiel
     *
     * @since 3.0.0
     */
    public function restoreFile()
    {
        $app = JFactory::getApplication();
        if ($cid = $app->input->get('cid', [], 'array')) {
            $restoreFile = current($cid);
            switch (strtolower(JFile::getExt($restoreFile))) {
                case 'csv':
                case 'xls':
                case 'xlsx':
                    $session = JFactory::getSession();
                    $session->clear('importStart', $this->alias);
                    $app->input->get->set('restoreFile', $restoreFile);

                    return $this->import();
                    break;
                case 'zip':
                    $this->fileName = $restoreFile;
                    $zipArchive = new ZipArchive;
                    $result = $zipArchive->open($this->fileNamePath());
                    if ($result === true) {
                        $zipArchive->extractTo(JPATH_ROOT);

                        return $this->redirect('view', 'export', [], 'COMIEL_SUCCESS_RESTORE_IMAGES_DONE');
                    }
                    $zipArchive->close();
                    break;
            }
        }
        $app->enqueueMessage(JText::_('COMIEL_ERROR_RESTORE_FILE'), 'warning');

        return $this->redirect();
    }

    /**
     * @return IEComiel
     *
     * @since 3.0.0
     */
    public function save()
    {
        $session = JFactory::getSession();
        $session->clear('importStart', $this->alias);
        $session->clear('exportStart', $this->alias);
        $app = JFactory::getApplication();
        $app->input->get->set(JSession::getFormToken(true), 1);
        echo $app->input->getString('restoreFile') ? $this->import() : $this->export();

        return $this;
    }

    /**
     * @var bool $redirect
     *
     * @return IEComiel
     *
     * @since 3.0.0
     */
    public function saveConfig($redirect = true)
    {
        $app = JFactory::getApplication();
        $export = $app->input->post->get('export', [], 'array');
        $import = $app->input->post->get('import', [], 'array');
        foreach (['fields', 'categories', 'manufacturers'] as $field) {
            if (isset($export[$field]) && is_array($export[$field])) {
                $export[$field] = array_diff($export[$field], ['']) ?: [''];
            }
        }
        $this->params = new Joomla\Registry\Registry(['import' => $import, 'export' => $export]);
        $jshopImportExport = JTable::getInstance('importExport', 'jshop');
        $jshopImportExport->load($this->ie_id);
        $jshopImportExport->set('params', $this->params->toString());
        if ($jshopImportExport->store()) {
            $app->enqueueMessage(JText::_('COMIEL_SUCCESS_DATABASE_SAVE_MODEL'));
        } else {
            $app->enqueueMessage(JText::sprintf('COMIEL_ERROR_DATABASE_SAVE_MODEL', $jshopImportExport), 'error');
        }
        if ($redirect) {
            return $this->redirect();
        }

        return $this;
    }

    /**
     * @param JTable $jTable
     * @param string $name
     *
     * @return string
     *
     * @since 3.0.0
     */
    protected function generateAlias(JTable $jTable, $name)
    {
        if (JFactory::getConfig()->get(strtolower('uniCodeSlugs')) == 1) {
            $alias = JFilterOutput::stringURLUnicodeSlug($name);
        } else {
            $alias = JFilterOutput::stringURLSafe($name);
        }
        while ($jTable->load([$this->jshopLang->get('alias') => $alias])) {
            $alias = \Joomla\String\StringHelper::increment($alias, 'dash');
        }

        return $alias;
    }

    /**
     * @param string $type
     * @param string $imageName
     *
     * @return bool
     *
     * @since 3.0.0
     */
    protected function resize($type, $imageName)
    {
        $app = JFactory::getApplication();
        require_once($this->jshopConfig->get('path') . 'lib/image.lib.php');
        switch ($type) {
            case 'attributeDependent':
            case 'product':
                $path = realpath($this->jshopConfig->get('image_product_path'));
                $fullImagePath = $path . DIRECTORY_SEPARATOR . 'full_' . $imageName;
                $imagePath = $path . DIRECTORY_SEPARATOR . $imageName;
                $thumbImagePath = $path . DIRECTORY_SEPARATOR . 'thumb_' . $imageName;
                if (!file_exists($fullImagePath) && !file_exists($imagePath)) {
                    return false;
                }
                if (!file_exists($fullImagePath) && file_exists($imagePath)) {
                    if (!rename($imagePath, $fullImagePath)) {
                        return false;
                    }
                }
                $width = (int)$this->jshopConfig->get('image_product_original_width');
                $height = (int)$this->jshopConfig->get('image_product_original_height');
                if ($width || $height) {
                    try {
                        $this->resizeImage($fullImagePath, $fullImagePath, $width, $height);
                    } catch (RuntimeException $e) {
                        return false;
                    } catch (Exception $e) {
                        $app->enqueueMessage($e->getMessage(), 'error');

                        return false;
                    }
                }
                $width = (int)$this->jshopConfig->get('image_product_full_width');
                $height = (int)$this->jshopConfig->get('image_product_full_height');
                try {
                    $this->resizeImage($fullImagePath, $imagePath, $width, $height);
                } catch (RuntimeException $e) {
                    return false;
                } catch (Exception $e) {
                    $app->enqueueMessage($e->getMessage(), 'error');

                    return false;
                }
                $width = (int)$this->jshopConfig->get('image_product_width');
                $height = (int)$this->jshopConfig->get('image_product_height');
                try {
                    $this->resizeImage($imagePath, $thumbImagePath, $width, $height);
                } catch (RuntimeException $e) {
                    return false;
                } catch (Exception $e) {
                    $app->enqueueMessage($e->getMessage(), 'error');

                    return false;
                }

                return true;
                break;
            case 'category':
                $path = realpath($this->jshopConfig->get('image_category_path'));
                $imagePath = $path . DIRECTORY_SEPARATOR . $imageName;
                if (!file_exists($imagePath)) {
                    return false;
                }
                $width = (int)$this->jshopConfig->get('image_category_width');
                $height = (int)$this->jshopConfig->get('image_category_height');
                try {
                    return $this->resizeImage($imagePath, $imagePath, $width, $height);
                } catch (RuntimeException $e) {
                    return false;
                } catch (Exception $e) {
                    $app->enqueueMessage($e->getMessage(), 'error');

                    return false;
                }
                break;
            case 'manufacturer':
                $path = realpath($this->jshopConfig->get('image_manufs_path'));
                $imagePath = $path . DIRECTORY_SEPARATOR . $imageName;
                if (!file_exists($imagePath)) {
                    return false;
                }
                $width = (int)$this->jshopConfig->get('image_category_width');
                $height = (int)$this->jshopConfig->get('image_category_height');
                try {
                    return $this->resizeImage($imagePath, $imagePath, $width, $height);
                } catch (RuntimeException $e) {
                    return false;
                } catch (Exception $e) {
                    $app->enqueueMessage($e->getMessage(), 'error');

                    return false;
                }
                break;
            case 'vendor':
                $path = realpath($this->jshopConfig->get('image_vendors_path'));
                $imagePath = $path . DIRECTORY_SEPARATOR . $imageName;
                if (!file_exists($imagePath)) {
                    return false;
                }
                $width = (int)$this->jshopConfig->get('image_category_width');
                $height = (int)$this->jshopConfig->get('image_category_height');
                try {
                    return $this->resizeImage($imagePath, $imagePath, $width, $height);
                } catch (RuntimeException $e) {
                    return false;
                } catch (Exception $e) {
                    $app->enqueueMessage($e->getMessage(), 'error');

                    return false;
                }
                break;
        }

        return false;
    }

    /**
     * @param string $source
     * @param string $desc
     * @param int $width
     * @param int $height
     *
     * @return bool
     *
     * @throws Exception
     *
     * @since 3.0.0
     */
    protected function resizeImage($source, $desc, $width, $height)
    {
        if (file_exists($desc)) {
            $image = @getimagesize($desc);
            if (is_array($image) && count($image) >= 2) {
                $w = $image[0];
                $h = $image[1];
                if ($width && !($height) && $width === $w) {
                    throw new RuntimeException(JText::sprintf('EXPRESIM_IGNORE_W', basename($desc), $w));
                } elseif (!$width && $height && $height === $h) {
                    throw new RuntimeException(JText::sprintf('EXPRESIM_IGNORE_H', basename($desc), $h));
                } elseif ($width && $height && $width === $w && $height === $h) {
                    throw new RuntimeException(JText::sprintf('EXPRESIM_IGNORE_W_H', basename($desc), $w, $h));
                }
            }
        }
        if (!ImageLib::resizeImageMagic($source, $width, $height, $this->jshopConfig->get('image_cut'), $this->jshopConfig->get('image_fill', 1), $desc, $this->jshopConfig->get('image_quality', 85), $this->jshopConfig->get('image_fill_color', 0xffffff))) {
            throw new Exception(_JSHOP_ERROR_CREATE_THUMBAIL . " " . basename($desc));
        }

        return true;
    }

    /**
     * @param array $categories
     * @param string $logicCondition
     * @param array $manufacturers
     *
     * @return bool
     *
     * @since 3.2.0
     */
    protected function unCount($categories = [], $logicCondition = 'OR', $manufacturers = [])
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->update($db->qn('#__jshopping_products'));
            $query->set($db->qn('product_quantity') . ' = ' . $db->q(0));
            $query->where($db->qn('unlimited') . ' = ' . $db->q(0));
            ComielHelperFilter::setFilter($query, $categories, $logicCondition, $manufacturers);
            $db->setQuery($query)->execute();

            return true;
        } catch (\Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, $this->alias);
        }

        return false;
    }

    /**
     * @param array $categories
     * @param string $logicCondition
     * @param array $manufacturers
     *
     * @return bool
     *
     * @since 3.2.0
     */
    protected function unPublish($categories = [], $logicCondition = 'OR', $manufacturers = [])
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->update($db->qn('#__jshopping_products'));
            $query->set($db->qn('product_publish') . ' = ' . $db->q(0));
            ComielHelperFilter::setFilter($query, $categories, $logicCondition, $manufacturers);
            $db->setQuery($query)->execute();

            return true;
        } catch (\Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, $this->alias);
        }

        return false;
    }

    /**
     * @param jshopProduct $jshopProduct
     * @param string $separator
     *
     * @return null|string
     *
     * @since 3.0.0
     */
    private function asStringAddPrice($jshopProduct, $separator = ':')
    {
        $jshopProduct->getAddPrices();
        $productAddPrices = [];
        foreach ((array)$jshopProduct->get('product_add_prices') as $addPrice) {
            array_unshift($productAddPrices, implode($separator, [
                $addPrice->product_quantity_start,
                $addPrice->product_quantity_finish,
                (float)$addPrice->discount,
            ]));
        }
        if (!$productAddPrices) {
            return null;
        }

        return $jshopProduct->get('product_add_price_unit') . ';' . implode('|', $productAddPrices);
    }

    /**
     * @param jshopProduct $jshopProduct
     * @param string $separator
     *
     * @return string
     *
     * @since 3.0.0
     */
    private function asStringAttributes($jshopProduct, $separator = ':')
    {
        $langTag = JFactory::getLanguage()->getTag();
        $this->preloadAttributes('name');
        $this->preloadAttributeValues('name');
        $attributes = [];
        foreach ($jshopProduct->getAttributes() as $productAttribute) {
            $attributeNames = [];
            $attributeValues = [];
            foreach ($this->attributes[0] as $attributeId => $attributeName) {
				$attributeName = addcslashes($attributeName, ',|:;');
                if ($attributeValueIds = $productAttribute->{'attr_' . $attributeId}) {
                    $attributeValueNames = [];
                    foreach (explode(',', $attributeValueIds) as $attributeValueId) {
                        if (array_key_exists((int)$attributeValueId, $this->attributeValues[(int)$attributeId])) {
                            $attributeValueNames[] = addcslashes($this->attributeValues[(int)$attributeId][(int)$attributeValueId], ',|:;');
                        }
                    }
                    $attributeNames[] = $attributeName . $separator . implode(',', $attributeValueNames);
                    unset($attributeName, $attributeId, $attributeValueNames, $attributeValueIds, $attributeValueId);
                }
            }
            if ((float)$productAttribute->price) {
                $attributeValues[] = addcslashes($this->jshopLang->lang == $langTag ? JText::_('COMIEL_PRODUCT_PRICE') : 'price', ',|:;') . $separator . (float)$productAttribute->price;
            }
            if ((float)$productAttribute->old_price) {
                $attributeValues[] = addcslashes($this->jshopLang->lang == $langTag ? JText::_('COMIEL_PRODUCT_OLD_PRICE') : 'old_price', ',|:;') . $separator . (float)$productAttribute->old_price;
            }
            if ((float)$productAttribute->buy_price) {
                $attributeValues[] = addcslashes($this->jshopLang->lang == $langTag ? JText::_('COMIEL_PRODUCT_BUY_PRICE') : 'buy_price', ',|:;') . $separator . (float)$productAttribute->buy_price;
            }
            if ((float)$productAttribute->weight_volume_units) {
                $attributeValues[] = addcslashes($this->jshopLang->lang == $langTag ? JText::_('COMIEL_PRODUCT_BASIC_PRICE') : 'basic_price', ',|:;') . $separator . (float)$productAttribute->weight_volume_units;
            }
            if ((float)$productAttribute->count) {
                $attributeValues[] = addcslashes($this->jshopLang->lang == $langTag ? JText::_('COMIEL_PRODUCT_QTY') : 'qty', ',|:;') . $separator . (float)$productAttribute->count;
            }
            if ((string)$productAttribute->ean) {
                $attributeValues[] = addcslashes($this->jshopLang->lang == $langTag ? JText::_('COMIEL_PRODUCT_EAN') : 'ean', ',|:;') . $separator . (string)$productAttribute->ean;
            }
            if ((float)$productAttribute->weight) {
                $attributeValues[] = addcslashes($this->jshopLang->lang == $langTag ? JText::_('COMIEL_PRODUCT_WEIGHT') : 'weight', ',|:;') . $separator . (float)$productAttribute->weight;
            }
            if ((int)$productAttribute->ext_attribute_product_id) {
                $query = $this->db->getQuery(true);
                $query->select($this->db->qn('image_name'));
                $query->from($this->db->qn('#__jshopping_products_images'));
                $query->where($this->db->qn('product_id') . ' = ' . $this->db->q((int)$productAttribute->ext_attribute_product_id));
                if ($images = $this->db->setQuery($query)->loadColumn()) {
					foreach ($images as $key=>$image) {
						$images[$key] = addcslashes($image, ',|:;');
					}
                    $attributeValues[] = addcslashes($this->jshopLang->lang == $langTag ? JText::_('COMIEL_PRODUCT_IMAGE') : 'image', ',|:;') . $separator . implode(',', $images);
					$this->storeImageData('products', $productAttribute->ext_attribute_product_id);
                }
            }
            $attributes[] = implode(';', $attributeNames) . ';' . implode(';', $attributeValues);
            unset($attributeNames, $attributeValues);
        }
        foreach ($jshopProduct->getAttributes2() as $productAttribute) {
            if (array_key_exists((int)$productAttribute->attr_id, $this->attributes[1]) && array_key_exists((int)$productAttribute->attr_value_id, $this->attributeValues[$productAttribute->attr_id])) {
                $attributes[] = $this->attributes[1][(int)$productAttribute->attr_id] . $separator . $this->attributeValues[(int)$productAttribute->attr_id][(int)$productAttribute->attr_value_id] . $separator . $productAttribute->price_mod . $separator . (float)$productAttribute->addprice;
            }
        }
        unset($jshopProduct, $attributeName, $attributeValue, $productAttribute, $separator, $langTag);

        return implode('|', $attributes);
    }

    /**
     * @param int $id
     * @param string $separator
     *
     * @return string
     *
     * @since 3.0.0
     */
    private function asStringCategory($id, $separator = ':')
    {
        if ($this->categories === null) {
            $this->categories = [];
        }
        if (!array_key_exists($id, $this->categories)) {
            $categoryParentId = $id;
            $categoryParents = [];
            while ($categoryParentId) {
                $jshopCategory = JTable::getInstance('category', 'jshop');
                $jshopCategory->load($categoryParentId);
				$this->storeImageData('categories', $jshopCategory->category_id);
                switch ($this->params->get('method')) {
                    case 'minimum':
                        array_unshift($categoryParents, $jshopCategory->get($this->jshopLang->get('name')));
                        break;
                    case 'medium':
                        if ($alias = $jshopCategory->get($this->jshopLang->get('alias'))) {
                            $alias = $separator . $alias;
                        }
                        array_unshift($categoryParents, $jshopCategory->get($this->jshopLang->get('name')) . $alias);
                        break;
                    default:
                        if ($alias = $jshopCategory->get($this->jshopLang->get('alias'))) {
                            $alias = $separator . $alias;
                        }
                        if ($image = $jshopCategory->get('category_image')) {
                            $image = $separator . $image;
                        }
                        array_unshift($categoryParents, $jshopCategory->get($this->jshopLang->get('name')) . $alias . $image);
                        break;
                }
                $categoryParentId = (int)$jshopCategory->get('category_parent_id');
            }
            $this->categories[$id] = implode($this->params->get('categoryDelimiter'), $categoryParents);
        }

        return $this->categories[$id];
    }

    /**
     * @param int $id
     * @param string $separator
     *
     * @return string
     *
     * @since 3.0.0
     */
    private function asStringCurrency($id, $separator = ':')
    {
        if ($this->currencies === null) {
            $this->currencies = [];
        }
        if (!array_key_exists($id, $this->currencies)) {
            $jshopCurrency = JTable::getInstance('currency', 'jshop');
            $jshopCurrency->load($id);
            switch ($this->params->get('method')) {
                case 'minimum':
                case 'medium':
                    $this->currencies[$id] = $jshopCurrency->get('currency_code_iso');
                    break;
                default:
                    $this->currencies[$id] = $jshopCurrency->get('currency_name') . $separator . $jshopCurrency->get('currency_code') . $separator . $jshopCurrency->get('currency_code_iso') . $separator . (float)$jshopCurrency->get('currency_value');
                    break;
            }
        }

        return $this->currencies[$id];
    }

    /**
     * @param int $id
     * @param string $separator
     *
     * @return string
     *
     * @since 3.0.0
     */
    private function asStringDeliveryTimes($id, $separator = ':')
    {
        if ($this->deliveryTimes === null) {
            $this->deliveryTimes = [];
        }
        if (!array_key_exists($id, $this->deliveryTimes)) {
            $jshopDeliveryTimes = JTable::getInstance('deliveryTimes', 'jshop');
            $jshopDeliveryTimes->load($id);
            switch ($this->params->get('method')) {
                case 'minimum':
                case 'medium':
                    $this->deliveryTimes[$id] = $jshopDeliveryTimes->get($this->jshopLang->get('name'));
                    break;
                default:
                    if ($days = (float)$jshopDeliveryTimes->get('days')) {
                        $days = $separator . $days;
                    } else {
                        $days = null;
                    }
                    $this->deliveryTimes[$id] = $jshopDeliveryTimes->get($this->jshopLang->get('name')) . $days;
                    break;
            }
        }

        return $this->deliveryTimes[$id];
    }

    /**
     * @param jshopProduct $jshopProduct
     * @param string $separator
     *
     * @return string
     *
     * @since 3.0.0
     */
    private function asStringExtraFields($jshopProduct, $separator = ':')
    {
        if ($this->extraFields === null) {
            $jshopProductField = JTable::getInstance('productField', 'jshop');
            $this->extraFields = $jshopProductField->getList(1);
        }
        $ProductExtraFields = [];
        foreach ($this->extraFields as $listProductField) {
            $listProductFieldValues = [];
            if ($jshopProduct->get('extra_field_' . $listProductField->id)) {
                $typeList = '';
                if ($listProductField->type == 1 || $listProductField->type == 3) {
                    $listProductFieldValues[] = '"' . addcslashes($jshopProduct->get('extra_field_' . $listProductField->id), ',|:;') . '"';
                } else {
                    $listProductFieldValues = explode(',', $jshopProduct->get('extra_field_' . $listProductField->id));
                    foreach ($listProductFieldValues as $key => $listProductFieldValue) {
                        $jshopProductFieldValue = JTable::getInstance('productFieldValue', 'jshop');
                        $jshopProductFieldValue->load($listProductFieldValue);
						$listProductFieldValues[$key] = addcslashes($jshopProductFieldValue->get($this->jshopLang->get('name')), ',|:;');
                    }
                    if (!$listProductField->type && !$listProductField->multilist) {
                        $typeList = 'list/';
                    }
                }
                if ($listProductField->group) {
                    $jshopProductFieldGroup = JTable::getInstance('productFieldGroup', 'jshop');
                    $jshopProductFieldGroup->load($listProductField->group);
                    $listProductFieldGroup = addcslashes($jshopProductFieldGroup->get($this->jshopLang->get('name')), ',|:;') . ';';
                } else {
                    $listProductFieldGroup = '';
                }
				$listProductFieldName = addcslashes($listProductField->name, ',|:;');
                $ProductExtraFields[] = $typeList . $listProductFieldGroup . $listProductFieldName . $separator . implode(',', $listProductFieldValues);
            }
        }

        return implode('|', $ProductExtraFields);
    }

    /**
     * @param jshopProduct $jshopProduct
     * @param string $separator
     *
     * @return string
     *
     * @since 3.0.0
     */
    private function asStringFiles($jshopProduct, $separator = ':')
    {
        $query = $this->db->getQuery(true);
        $query->select($this->db->qn('demo'));
        $query->select($this->db->qn('demo_descr'));
        $query->select($this->db->qn('file'));
        $query->select($this->db->qn('file_descr'));
        $query->from($this->db->qn('#__jshopping_products_files'));
        $query->where($this->db->qn('product_id') . ' = ' . $this->db->q($jshopProduct->get('product_id')));
        $files = [];
        foreach ($this->db->setQuery($query)->loadObjectList() as $file) {
            $demo_desc = $file->demo_descr ? $separator . $file->demo_descr : '';
            if ($file->demo || $demo_desc) {
                $files[] = $file->file . $separator . $file->file_descr . $separator . $file->demo . $demo_desc;
            } elseif ($file->file_descr) {
                $files[] = $file->file . $separator . $file->file_descr;
            } else {
                $files[] = $file->file;
            }
        }

        return implode('|', $files);
    }

    /**
     * @param jshopProduct $jshopProduct
     *
     * @return string
     *
     * @since 3.0.0
     */
    private function asStringFreeAttribute($jshopProduct)
    {
        $freeAttributes = [];
        foreach ($jshopProduct->getListFreeAttributes() as $freeAttribute) {
            $freeAttributes[$freeAttribute->name] = $freeAttribute->name;
        }

        return implode('|', $freeAttributes);
    }

    /**
     * @param jshopProduct $jshopProduct
     * @param string $separator
     *
     * @return string
     *
     * @since 3.0.0
     */
    private function asStringImages($jshopProduct, $separator = ':')
    {
        $jshopProduct->getImages();
        $query = $this->db->getQuery(true);
        $query->select($this->db->qn('image_name', 'image'));
        $query->select($this->db->qn('name', 'title'));
        $query->from($this->db->qn('#__jshopping_products_images'));
        $query->where($this->db->qn('product_id') . ' = ' . $this->db->q($jshopProduct->get('product_id')));
        $query->order($this->db->qn('ordering'));
        if (!$images = $this->db->setQuery($query)->loadObjectList()) {
            return null;
        }
        $allImages = [];
        foreach ($images as $image) {
            switch ($this->params->get('method')) {
                case 'minimum':
                case 'medium':
                    $allImages[] = $image->image;
                    break;
                default:
                    if ($title = $image->title) {
                        $title = $separator . $title;
                    }
                    $allImages[] = $image->image . $title;
                    break;
            }
        }

        return implode('|', $allImages);
    }

    /**
     * @param int $id
     * @param string $separator
     *
     * @return string
     *
     * @since 3.0.0
     */
    private function asStringLabel($id, $separator = ':')
    {
        if ($this->labels === null) {
            $this->labels = [];
        }
        if (!array_key_exists($id, $this->labels)) {
            $jshopProductLabel = JTable::getInstance('productLabel', 'jshop');
            $jshopProductLabel->load($id);
			$this->storeImageData('labels', $jshopProductLabel->label_id);
            $image = $jshopProductLabel->get('image');
            switch ($this->params->get('method')) {
                case 'minimum':
                case 'medium':
                    $this->labels[$id] = $jshopProductLabel->get($this->jshopLang->get('name'));
                    break;
                default:
                    if ($image) {
                        $image = $separator . $image;
                    }
                    $this->labels[$id] = $jshopProductLabel->get($this->jshopLang->get('name')) . $image;
                    break;
            }
        }

        return $this->labels[$id];
    }

    /**
     * @param int $id
     * @param string $separator
     *
     * @return string
     *
     * @since 3.0.0
     */
    private function asStringManufacturer($id, $separator = ':')
    {
        if ($this->manufacturers === null) {
            $this->manufacturers = [];
        }
        if (!array_key_exists($id, $this->manufacturers)) {
            $jshopManufacturer = JTable::getInstance('manufacturer', 'jshop');
            $jshopManufacturer->load($id);
			$this->storeImageData('manufacturers', $jshopManufacturer->manufacturer_id);
            switch ($this->params->get('method')) {
                case 'minimum':
                    $this->manufacturers[$id] = $jshopManufacturer->get($this->jshopLang->get('name'));
                    break;
                case 'medium':
                    if ($alias = $jshopManufacturer->get($this->jshopLang->get('alias'))) {
                        $alias = $separator . $alias;
                    }
                    $this->manufacturers[$id] = $jshopManufacturer->get($this->jshopLang->get('name')) . $alias;
                    break;
                default:
                    if ($alias = $jshopManufacturer->get($this->jshopLang->get('alias'))) {
                        $alias = $separator . $alias;
                        if ($image = $jshopManufacturer->get('manufacturer_logo')) {
                            $alias .= $separator . $image;
                            if ($url = $jshopManufacturer->get('manufacturer_url')) {
                                $alias .= $separator . $url;
                            }
                        }
                    }
                    $this->manufacturers[$id] = $jshopManufacturer->get($this->jshopLang->get('name')) . $alias;
                    break;
            }
        }

        return $this->manufacturers[$id];
    }

    /**
     * @param int $id
     *
     * @return string
     *
     * @since 3.0.0
     */
    private function asStringRelated($id)
    {
        if ($this->relations === null) {
            $query = $this->db->getQuery(true);
            $query->select($this->db->qn('product_id'));
            $query->select('GROUP_CONCAT(' . $this->db->qn('product_related_id') . ' SEPARATOR \'|\') AS relations');
            $query->from($this->db->qn('#__jshopping_products_relations'));
            $query->group($this->db->qn('product_id'));
            $relations = $this->db->setQuery($query)->loadObjectList('product_id');
            $this->relations = array_map(function ($relation) {
                return $relation->relations;
            }, $relations);
        }
        if (array_key_exists($id, $this->relations)) {
            return $this->relations[$id];
        }

        return null;
    }

    /**
     * @param int $id
     * @param string $separator
     *
     * @return string
     *
     * @since 3.0.0
     */
    private function asStringTax($id, $separator = ':')
    {
        if (!$id) {
            return null;
        }
        if ($this->taxes === null) {
            $this->taxes = [];
        }
        if (!array_key_exists($id, $this->taxes)) {
            $jshopTax = JTable::getInstance('tax', 'jshop');
            $jshopTax->load($id);
            switch ($this->params->get('method')) {
                case 'minimum':
                case 'medium':
                    $this->taxes[$id] = $jshopTax->get('tax_name');
                    break;
                default:
                    if ($value = (float)$jshopTax->get('tax_value')) {
                        $value = $separator . $value;
                    } else {
                        $value = null;
                    }
                    $this->taxes[$id] = $jshopTax->get('tax_name') . $value;
                    break;
            }
        }

        return $this->taxes[$id];
    }

    /**
     * @param int $id
     * @param string $separator
     *
     * @return string
     *
     * @since 3.0.0
     */
    private function asStringUnit($id, $separator = ':')
    {
        if (!$id) {
            return null;
        }
        if ($this->units === null) {
            $this->units = [];
        }
        if (!array_key_exists($id, $this->units)) {
            $jshopUnit = JTable::getInstance('unit', 'jshop');
            $jshopUnit->load($id);
            switch ($this->params->get('method')) {
                case 'minimum':
                case 'medium':
                    $this->units[$id] = $jshopUnit->get($this->jshopLang->get('name'));
                    break;
                default:
                    if ($qty = (int)$jshopUnit->get('qty')) {
                        $qty = $separator . $qty;
                    } else {
                        $qty = null;
                    }
                    $this->units[$id] = $jshopUnit->get($this->jshopLang->get('name')) . $qty;
                    break;
            }
        }

        return $this->units[$id];
    }

    /**
     * @param int $id
     *
     * @return string
     *
     * @since 3.0.0
     */
    private function asStringVendor($id)
    {
        if (!$id) {
            return null;
        }
        if ($this->vendors === null) {
            $this->vendors = [];
        }
        if (!array_key_exists($id, $this->vendors)) {
            $jshopVendor = JTable::getInstance('vendor', 'jshop');
            $jshopVendor->load($id);
			$this->storeImageData('vendors', $jshopVendor->vendor_id);
            switch ($this->params->get('method')) {
                default:
                    $this->vendors[$id] = $jshopVendor->get('shop_name');
                    break;
            }
        }

        return $this->vendors[$id];
    }

    /**
     * @param jshopProduct $jshopProduct
     * @param string $separator
     *
     * @return string
     *
     * @since 3.0.0
     */
    private function asStringVideos($jshopProduct, $separator = ':')
    {
        $query = $this->db->getQuery(true);
        $query->select($this->db->qn('video_name', 'name'));
        $query->select($this->db->qn('video_code', 'code'));
        $query->select($this->db->qn('video_preview', 'preview'));
        $query->from($this->db->qn('#__jshopping_products_videos'));
        $query->where($this->db->qn('product_id') . ' = ' . $this->db->q($jshopProduct->get('product_id')));
        $videos = [];
        foreach ($this->db->setQuery($query)->loadObjectList() as $video) {
            $preview = null;
            if ($video->preview) {
                $preview = $video->preview;
            }
            if ($video->name) {
                $videos[] = implode($separator, ['name', $video->name, $preview]);
            } elseif ($video->code) {
                $videos[] = implode($separator, ['code', $video->code, $preview]);
            }
        }

        return implode('|', $videos);
    }

    /**
     * @param array $data
     *
     * @return int|null
     *
     * @since 3.0.0
     */
    private function createAttribute(array $data)
    {
        $jshopAttribute = JTable::getInstance('attribut', 'jshop');
        foreach ($data as $property => $value) {
            $jshopAttribute->set($property, $value);
        }
        $jshopAttribute->set('attr_ordering', $jshopAttribute->getNextOrder($this->db->qn('group') . ' = ' . $this->db->q(0)));
        if ($jshopAttribute->store()) {
            $jshopAttribute->addNewFieldProductsAttr();
            $id = (int)$jshopAttribute->get($jshopAttribute->getKeyName());
            $this->attributes[(int)$jshopAttribute->get('independent')][$jshopAttribute->get($this->jshopLang->get('name'))] = $id;

            return $id;
        }

        return null;
    }

    /**
     * @param array $data
     *
     * @return int|null
     *
     * @since 3.0.0
     */
    private function createAttributeValue(array $data)
    {
        $jshopAttributeValue = JTable::getInstance('attributValue', 'jshop');
        foreach ($data as $property => $value) {
            $jshopAttributeValue->set($property, $value);
        }
        $jshopAttributeValue->set('value_ordering', $jshopAttributeValue->getNextOrder($this->db->qn('attr_id') . ' = ' . $this->db->q($jshopAttributeValue->get('attr_id'))));
        if ($jshopAttributeValue->store()) {
            $id = (int)$jshopAttributeValue->get($jshopAttributeValue->getKeyName());
            $this->attributeValues[(int)$jshopAttributeValue->get('attr_id')][(string)$jshopAttributeValue->get($this->jshopLang->get('name'))] = $id;

            return $id;
        }

        return null;
    }

    /**
     * @param array $data
     *
     * @return int
     *
     * @since 3.0.0
     */
    private function createCategory(array $data)
    {
        $jshopCategory = JTable::getInstance('category', 'jshop');
        foreach ($data as $property => $value) {
            $jshopCategory->set($property, $value);
        }
        $jshopCategory->set('category_add_date', date('Y-m-d H:i:s'));
        $jshopCategory->set('ordering', $jshopCategory->getNextOrder($this->db->qn('category_parent_id') . ' = ' . $this->db->q((int)$jshopCategory->get('category_parent_id'))));
        if ($jshopCategory->store()) {
            $id = (int)$jshopCategory->get('category_id');
            $this->categories[(int)$jshopCategory->get('category_parent_id')][$jshopCategory->get($this->jshopLang->get('name'))] = $id;

            return $id;
        }

        return 0;
    }

    /**
     * @param array $data
     *
     * @return int
     *
     * @since 3.0.0
     */
    private function createCurrency(array $data)
    {
        $jshopCurrency = JTable::getInstance('currency', 'jshop');
        foreach ($data as $property => $value) {
            $jshopCurrency->set($property, $value);
        }
        if ($jshopCurrency->store()) {
            $id = (int)$jshopCurrency->get('currency_id');
            $this->currencies[$jshopCurrency->get('currency_code')] = $id;
            $this->currencies[$jshopCurrency->get('currency_name')] = $id;
            $this->currencies[$jshopCurrency->get('currency_code_iso')] = $id;

            return $id;
        }

        return 0;
    }

    /**
     * @param array $data
     *
     * @return int
     *
     * @since 3.0.0
     */
    private function createDeliveryTime(array $data)
    {
        $jshopDeliveryTimes = JTable::getInstance('deliveryTimes', 'jshop');
        foreach ($data as $property => $value) {
            $jshopDeliveryTimes->set($property, $value);
        }
        if ($jshopDeliveryTimes->store()) {
            $id = (int)$jshopDeliveryTimes->get($jshopDeliveryTimes->getKeyName());
            $this->deliveryTimes[$jshopDeliveryTimes->get($this->jshopLang->get('name'))] = $id;

            return $id;
        }

        return 0;
    }

    /**
     * @param array $data
     *
     * @return int|null
     *
     * @since 3.0.0
     */
    private function createExtraField(array $data)
    {
        $jshopProductField = JTable::getInstance('productField', 'jshop');
        foreach ($data as $property => $value) {
            $jshopProductField->set($property, $value);
        }
        if ($jshopProductField->store()) {
            $id = (int)$jshopProductField->get($jshopProductField->getKeyName());
            $this->extraFields[(int)$jshopProductField->get('group')][$jshopProductField->get($this->jshopLang->get('name'))] = $id;
            $jshopProductField->addNewFieldProducts();

            return $id;
        }

        return null;
    }

    /**
     * @param array $data
     *
     * @return int|null
     *
     * @since 3.0.0
     */
    private function createExtraFieldGroup(array $data)
    {
        $jshopProductFieldGroup = JTable::getInstance('productFieldGroup', 'jshop');
        foreach ($data as $property => $value) {
            $jshopProductFieldGroup->set($property, $value);
        }
        if ($jshopProductFieldGroup->store()) {
            $id = (int)$jshopProductFieldGroup->get($jshopProductFieldGroup->getKeyName());
            $this->extraFieldGroups[$jshopProductFieldGroup->get($this->jshopLang->get('name'))] = $id;

            return $id;
        }

        return 0;
    }

    /**
     * @param array $data
     *
     * @return int|null
     *
     * @since 3.0.0
     */
    private function createExtraFieldValue(array $data)
    {
        $jshopProductFieldValue = JTable::getInstance('productFieldValue', 'jshop');
        foreach ($data as $property => $value) {
            $jshopProductFieldValue->set($property, $value);
        }
        if ($jshopProductFieldValue->store()) {
            $id = (int)$jshopProductFieldValue->get($jshopProductFieldValue->getKeyName());
            $this->extraFieldValues[(int)$jshopProductFieldValue->get('field_id')][(string)$jshopProductFieldValue->get($this->jshopLang->get('name'))] = $id;

            return $id;
        }

        return null;
    }

    /**
     * @param array $data
     *
     * @return int
     *
     * @since 3.0.0
     */
    private function createFile(array $data)
    {
        $jshopProductFiles = JTable::getInstance('productFiles', 'jshop');
        foreach ($data as $property => $value) {
            $jshopProductFiles->set($property, $value);
        }
        if ($jshopProductFiles->store()) {
            $id = (int)$jshopProductFiles->get($jshopProductFiles->getKeyName());
            if ($file = (string)$jshopProductFiles->get('file')) {
                $this->files[(int)$jshopProductFiles->get('product_id')][$file] = $id;
            }
            if ($demo = (string)$jshopProductFiles->get('demo')) {
                $this->files[(int)$jshopProductFiles->get('product_id')][$demo] = $id;
            }

            return $id;
        }

        return 0;
    }

    /**
     * @param string $name
     *
     * @return int
     *
     * @since 3.0.0
     */
    private function createFreeAttribute($name)
    {
        $jshopFreeAttribute = JTable::getInstance('freeAttribut', 'jshop');
        $jshopFreeAttribute->set($this->jshopLang->get('name'), $name);
        $jshopFreeAttribute->set('required', 0);
        if ($jshopFreeAttribute->store()) {
            $id = (int)$jshopFreeAttribute->get($jshopFreeAttribute->getKeyName());
            $this->freeAttributes[$name] = $id;

            return $id;
        }

        return 0;
    }

    /**
     * @param array $data
     *
     * @return int
     *
     * @since 3.0.0
     */
    private function createImage(array $data)
    {
        $jshopImage = JTable::getInstance('image', 'jshop');
        foreach ($data as $property => $value) {
            $jshopImage->set($property, $value);
        }
        if ($jshopImage->store()) {
            $id = (int)$jshopImage->get($jshopImage->getKeyName());
            $this->images[(int)$jshopImage->get('product_id')][$jshopImage->get('image_name')] = $id;

            return $id;
        }

        return 0;
    }

    /**
     * @param array $data
     *
     * @return int
     *
     * @since 3.0.0
     */
    private function createLabel(array $data)
    {
        $jshopProductLabel = JTable::getInstance('productLabel', 'jshop');
        foreach ($data as $property => $value) {
            $jshopProductLabel->set($property, $value);
        }
        if ($jshopProductLabel->store()) {
            $id = (int)$jshopProductLabel->get($jshopProductLabel->getKeyName());
            $this->labels[$jshopProductLabel->get($this->jshopLang->get('name'))] = $id;

            return $id;
        }

        return 0;
    }

    /**
     * @param array $data
     *
     * @return int
     *
     * @since 3.0.0
     */
    private function createManufacturer(array $data)
    {
        $jshopManufacturer = JTable::getInstance('manufacturer', 'jshop');
        foreach ($data as $property => $value) {
            $jshopManufacturer->set($property, $value);
        }
        if ($jshopManufacturer->store()) {
            $id = (int)$jshopManufacturer->get($jshopManufacturer->getKeyName());
            $this->manufacturers[$jshopManufacturer->get($this->jshopLang->get('name'))] = $id;

            return $id;
        }

        return 0;
    }

    /**
     * @param array $data
     *
     * @return object
     *
     * @since 3.0.0
     */
    private function createTax(array $data)
    {
        $jshopTax = JTable::getInstance('tax', 'jshop');
        foreach ($data as $property => $value) {
            $jshopTax->set($property, $value);
        }
        if (!$jshopTax->store()) {
            throw new RuntimeException(JText::sprintf('COMIEL_ERROR_DATABASE_SAVE_MODEL', $jshopTax));
        }
        $tax = (object)$jshopTax->getProperties();
        $this->taxes[$jshopTax->get('tax_name')] = $tax;

        return $tax;
    }

    /**
     * @param array $data
     *
     * @return int
     *
     * @since 3.0.0
     */
    private function createUnit(array $data)
    {
        $jshopUnit = JTable::getInstance('unit', 'jshop');
        foreach ($data as $property => $value) {
            $jshopUnit->set($property, $value);
        }
        if ($jshopUnit->store()) {
            $id = (int)$jshopUnit->get($jshopUnit->getKeyName());
            $this->units[$jshopUnit->get($this->jshopLang->get('name'))] = $id;

            return $id;
        }

        return 0;
    }

    /**
     * @param array $data
     *
     * @return int
     *
     * @since 3.0.0
     */
    private function createVendor(array $data)
    {
        $jshopVendor = JTable::getInstance('vendor', 'jshop');
        foreach ($data as $property => $value) {
            $jshopVendor->set($property, $value);
        }
        if ($jshopVendor->store()) {
            $id = $jshopVendor->get($jshopVendor->getKeyName());
            $this->vendors[$jshopVendor->get('shop_name')] = $id;

            return $id;
        }

        return 0;
    }

    /**
     * @param array $data
     *
     * @return int
     *
     * @since 3.0.0
     */
    private function createVideo(array $data)
    {
        $jshopProductVideo = JTable::getInstance('productVideo', 'jshop');
        foreach ($data as $property => $value) {
            $jshopProductVideo->set($property, $value);
        }
        if ($jshopProductVideo->store()) {
            $id = (int)$jshopProductVideo->get($jshopProductVideo->getKeyName());
            if ($name = $jshopProductVideo->get('video_name')) {
                $this->vendors[(int)$jshopProductVideo->get('product_id')][(string)$name] = $id;
            }
            if ($code = $jshopProductVideo->get('video_code')) {
                $this->vendors[(int)$jshopProductVideo->get('product_id')][(string)$code] = $id;
            }

            return $id;
        }

        return 0;
    }

    /**
     * @return string
     *
     * @since 3.0.0
     */
    private function fileNamePath()
    {
        return $this->filePath() . $this->fileName;
    }

    /**
     * @return string
     *
     * @since 3.0.0
     */
    private function filePath()
    {
        return realpath($this->jshopConfig->get('importexport_path') . $this->alias) . DIRECTORY_SEPARATOR;
    }

    /**
     * @param string $action
     * @return array
     *
     * @since 3.0.0
     */
    private function generateQueryData($action)
    {
        $queryData = [];
        foreach ($this->params->extract($action) as $path => $value) {
            if ($this->params->exists($path) && $this->params->get($path) != $value) {
                $queryData[$path] = $this->params->get($path);
            }
        }
        unset($path, $value);
        return $queryData;
    }

    /**
     * @return array
     *
     * @since 3.0.0
     */
    private function getChangedHeader()
    {
        if (!$columns = array_filter($this->params->get('fields'))) {
            $columns = array_keys(self::FIELDS);
        }
        $result = [];
        foreach ($columns as $column) {
            switch ($column) {
                case 'name':
                case 'alias':
                case 'short_description':
                case 'description':
                case 'meta_title':
                case 'meta_description':
                case 'meta_keyword':
                    if ($this->jshopConfig->get('admin_show_languages') && $this->params->get('languages')) {
                        foreach ($this->getLanguageTags() as $key => $languageTag) {
                            $result[] = $column . '_' . $languageTag;
                        }
                    } else {
                        $result[] = $column;
                    }
                    break;
                case 'template':
                    if ($this->jshopConfig->get('use_different_templates_cat_prod')) {
                        $result[] = $column;
                    }
                    break;
                case 'vendor':
                    if ($this->jshopConfig->get('admin_show_vendors')) {
                        $result[] = $column;
                    }
                    break;
                case 'unit':
                    if ($this->jshopConfig->get('admin_show_units')) {
                        $result[] = $column;
                    }
                    break;
                case 'tax':
                    if ($this->jshopConfig->get('tax')) {
                        $result[] = $column;
                    }
                    break;
                case 'qty':
                case 'unlimited':
                    if ($this->jshopConfig->get('stock')) {
                        $result[] = $column;
                    }
                    break;
                case 'attributes':
                    if ($this->jshopConfig->get('admin_show_attributes')) {
                        $result[] = $column;
                    }
                    break;
                case 'free_attributes':
                    if ($this->jshopConfig->get('admin_show_freeattributes')) {
                        $result[] = $column;
                    }
                    break;
                case 'delivery_times':
                    if ($this->jshopConfig->get('admin_show_delivery_time')) {
                        $result[] = $column;
                    }
                    break;
                case 'video':
                    if ($this->jshopConfig->get('admin_show_product_video')) {
                        $result[] = $column;
                    }
                    break;
                case 'related':
                    if ($this->jshopConfig->get('admin_show_product_related')) {
                        $result[] = $column;
                    }
                    break;
                case 'files':
                    if ($this->jshopConfig->get('admin_show_product_files')) {
                        $result[] = $column;
                    }
                    break;
                case 'label':
                    if ($this->jshopConfig->get('admin_show_product_labels')) {
                        $result[] = $column;
                    }
                    break;
                case 'buy_price':
                    if ($this->jshopConfig->get('admin_show_product_bay_price')) {
                        $result[] = $column;
                    }
                    break;
                case 'basic_price':
                    if ($this->jshopConfig->get('admin_show_product_basic_price')) {
                        $result[] = $column;
                    }
                    break;
                case 'extra_fields':
                    if ($this->jshopConfig->get('admin_show_product_extra_field')) {
                        $result[] = $column;
                    }
                    break;
                case 'weight':
                    if ($this->jshopConfig->get('admin_show_weight')) {
                        $result[] = $column;
                    }
                    break;
                case 'hits':
                    if ($this->jshopConfig->get('show_hits')) {
                        $result[] = $column;
                    }
                    break;
                default:
                    $result[] = $column;
                    break;
            }
        }
        $jshopAddon = JTable::getInstance('addon', 'jshop');
        $jshopAddon->loadAlias('addon_bonus_system');
        if ($jshopAddon->id) {
            $result[] = 'bonus_add';
            $result[] = 'bonus_sub';
        }

        return $result;
    }

    /**
     * @param null|string $data
     * @param string $separator
     * @param bool $isNew
     *
     * @return int
     *
     * @since 3.0.0
     */
    private function getCurrencyId($data, $separator = ':', $isNew = false)
    {
        if ((!$data && $isNew) || (string)$data === '-1') {
            return (int)$this->jshopConfig->get('mainCurrency');
        }
        if (!$data) {
            return 0;
        }
        $this->preloadCurrencies();
        list($name, $code, $iso, $value) = array_slice(explode($separator, $data), 0, 4);
        if ($code === null && isset($this->currencies[(int)$name])) {
            return (int)$name;
        }
        if (isset($this->currencies[(string)$name])) {
            return $this->currencies[(string)$name];
        }
        if ($code && isset($this->currencies[(string)$code])) {
            return $this->currencies[(string)$code];
        }
        if ($iso && isset($this->currencies[(string)$iso])) {
            return $this->currencies[(string)$iso];
        }
        if (!$name) {
            return (int)$this->jshopConfig->get('mainCurrency');
        }
        /** @var jshopCurrency $jshopCurrency */
        $jshopCurrency = JTable::getInstance('currency', 'jshop');
        $jshopCurrency->setColumnAlias('ordering', 'currency_ordering');
        $data = [
            'currency_name' => $name,
            'currency_code' => $code,
            'currency_code_iso' => $iso,
            'currency_ordering' => $jshopCurrency->getNextOrder(),
            'currency_value' => (float)$value,
            'currency_publish' => 1,
        ];

        return $this->createCurrency($data);
    }

    /**
     * @return array
     *
     * @since 3.0.0
     */
    private function getDeleteImages()
    {
        $allImages = [];
        $query = $this->db->getQuery(true);
        $query->select('image_name');
        $query->from($this->db->qn('#__jshopping_products_images'));
        $images = $this->db->setQuery($query)->loadObjectList();
        foreach ($images as $image) {
            foreach ($image as $image_name) {
                $allImages[] = $image_name;
            }
        }
        $jshopProduct = JTable::getInstance('product', 'jshop');
        $fields = $jshopProduct->getFields();
        $query = $this->db->getQuery(true);
        if (array_key_exists("product_full_image", $fields)) {
            $query->select("product_full_image");
        }
        if (array_key_exists("product_name_image", $fields)) {
            $query->select("product_name_image");
        }
        if (array_key_exists("product_thumb_image", $fields)) {
            $query->select("product_thumb_image");
        }
        if (array_key_exists("image", $fields)) {
            $query->select("image");
        }
        $query->from("#__jshopping_products");
        $this->db->setQuery($query);
        $images = $this->db->loadObjectList();
        foreach ($images as $image) {
            foreach ($image as $image_name) {
                $allImages[] = $image_name;
            }
        }
        $allImages[] = $this->jshopConfig->get('noimage');
        $allImages[] = "index.html";
        $allImages[] = "index.php";
        $allImages = array_unique($allImages);
        $dir = $this->jshopConfig->get('image_product_path');
        $files = JFolder::files($dir);
        $names = [];
        foreach ($files as $file) {
            if (!in_array($file, $allImages)) {
                $names[] = $file;
            }
        }

        return $names;
    }

    /**
     * @return array
     *
     * @since 3.0.0
     */
    private function getDeleteImagesTable()
    {
        $allImages = [];
        $tableImage = JTable::getInstance("Image", 'jshop');
        $fields = $tableImage->getFields();
        if (!array_key_exists("image_name", $fields)) {
            return [];
        }
        $query = $this->db->getQuery(true);
        $query->select('image_name');
        $query->from($this->db->qn('#__jshopping_products_images'));
        $images = $this->db->setQuery($query)->loadObjectList();
        foreach ($images as $image) {
            foreach ($image as $image_name) {
                $allImages[] = $image_name;
            }
        }
        $allImages = array_unique($allImages);
        $dir = $this->jshopConfig->get('image_product_path');
        $files = JFolder::files($dir);
        $names = array_diff($allImages, $files);

        return $names;
    }

    /**
     * @param null|string $data
     * @param string $separator
     *
     * @return int
     *
     * @since 3.0.0
     */
    private function getDeliveryTimeId($data, $separator = ':')
    {
        if (!$data || (string)$data === '-1') {
            return 0;
        }
        $this->preloadDeliveryTimes();
        list($name, $days) = array_slice(explode($separator, $data), 0, 2);
        if ($days === null && isset($this->deliveryTimes[(int)$name])) {
            return (int)$name;
        }
        if (isset($this->deliveryTimes[(string)$name])) {
            return $this->deliveryTimes[(string)$name];
        }
        if (!$name) {
            return 0;
        }
        $data = [$this->jshopLang->get('name') => $name, 'days' => (float)$days,];

        return $this->createDeliveryTime($data);
    }

    /**
     * @param jshopProduct $jshopProduct
     * @param null|string $data
     * @param string $separator
     * @param bool $executeResize
     *
     * @return string|null
     *
     * @since 3.0.0
     */
    private function getImageName($jshopProduct, $data, $separator = ':', $executeResize = false)
    {
        $productId = (int)$jshopProduct->get('product_id');
        try {
            $jshopImage = JTable::getInstance('image', 'jshop');
            $query = $this->db->getQuery(true);
            $query->delete($this->db->qn($jshopImage->getTableName()));
            $query->where($this->db->qn('product_id') . ' = ' . $this->db->q($productId));
            $this->db->setQuery($query)->execute();
            unset($this->images[$productId]);
        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, $this->alias);
        }
        if (!$data) {
            return null;
        }
        $this->preloadImages();
        $productId = (int)$jshopProduct->get('product_id');
        $firstImage = null;
        foreach (ComielHelper::parseMultipleLine($data) as $i => $imageData) {
            list($image, $name, $title, $localName) = array_slice(explode($separator, $imageData), 0, 4);
            if ($image === 'http' || $image === 'https') {
				$image = $image . $separator . $name;
				$name = $title;
                try {
                    $path = realpath($this->jshopConfig->get('image_product_path'));
                    $options = new \Joomla\Registry\Registry();
                    $options->set('transport.curl',
                        [
                            CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_SSL_VERIFYHOST => false,
                            CURLOPT_PROXY => null,
                            CURLOPT_PROXYUSERPWD => null,
                        ]
                    );
                    $response = JHttpFactory::getHttp($options)->get($image);
                    if ($response && $response->code === 200) {
						$image = $localName ? $localName : basename($image);
                        JFile::write($path . DIRECTORY_SEPARATOR . $image, $response->body);
                    } else {
						throw new Exception;
					}
                } catch (Exception $e) {
					JFactory::getApplication()->enqueueMessage('Failed to load ' . $image , 'warning');
                    continue;
                }
            }
            if (isset($this->images[$productId][(string)$image])) {
                if ($executeResize) {
                    $this->resize('product', $image);
                }

                return $this->images[$productId][(string)$image];
            }
            if (!$image) {
                continue;
            }
            $jshopImage = JTable::getInstance('image', 'jshop');
            $data = [
                'product_id' => $productId,
                'image_name' => $image,
                'name' => $name,
                'ordering' => $jshopImage->getNextOrder($this->db->qn('product_id') . ' = ' . $this->db->q($productId)),
            ];
            $this->createImage($data);
            if ($executeResize) {
                $this->resize('product', $image);
            }
            if (!$i) {
                $firstImage = $image;
            }
        }

        return $firstImage;
    }

    /**
     * @param null|string $data
     * @param string $separator
     *
     * @return int
     *
     * @since 3.0.0
     */
    private function getLabelId($data, $separator = ':')
    {
        if (!$data || (string)$data === '-1') {
            return 0;
        }
        $this->preloadLabels();
        list($name, $image) = array_slice(explode($separator, $data), 0, 2);
        if ($image === null && isset($this->labels[(int)$name])) {
            return (int)$name;
        }
        if (isset($this->labels[(string)$name])) {
            return $this->labels[(string)$name];
        }
        if (!$name) {
            return 0;
        }
        $data = [$this->jshopLang->get('name') => $name, 'image' => (string)$image,];

        return $this->createLabel($data);
    }

    /**
     * @return array
     *
     * @since 3.0.0
     */
    private function getLanguageTags()
    {
        $tableLanguage = JTable::getInstance("Language", 'jshop');
        $allLanguages = $tableLanguage->getAllLanguages();
        $languages = [];
        foreach ($allLanguages as $language) {
            $languages[] = $language->language;
        }
        unset($allLanguages);

        return $languages;
    }

    /**
     * @param null|string $data
     * @param string $separator
     * @param bool $generateAlias
     *
     * @return int
     *
     * @since 3.0.0
     */
    private function getManufacturerId($data, $separator = ':', $generateAlias = false)
    {
        if (!$data || (string)$data === '-1') {
            return 0;
        }
        $this->preloadManufacturers();
        list($name, $alias, $image, $schema, $link) = array_slice(explode($separator, $data), 0, 5);
        if ($alias === null && isset($this->manufacturers[(int)$name])) {
            return (int)$name;
        }
        $url = null;
        if ($schema && $link) {
            $url = $schema . $separator . $link;
        }
        if (isset($this->manufacturers[(string)$name])) {
            return $this->manufacturers[(string)$name];
        }
        if (!$name) {
            return 0;
        }
        $jshopManufacturer = JTable::getInstance('manufacturer', 'jshop');
        if (!$alias && $generateAlias) {
            $alias = $this->generateAlias($jshopManufacturer, $name);
        }
        $data = [
            $this->jshopLang->get('name') => $name,
            $this->jshopLang->get('alias') => $alias,
            'manufacturer_logo' => $image,
            'manufacturer_url' => $url,
            'manufacturer_publish' => 1,
            'ordering' => $jshopManufacturer->getNextOrder(),
            'products_page' => $this->jshopConfig->get('count_products_to_page'),
            'products_row' => $this->jshopConfig->get('count_products_to_row'),
        ];

        return $this->createManufacturer($data);
    }

    /**
     * @param null|string $data
     * @param string $separator
     *
     * @return int
     *
     * @since 3.0.0
     */
    private function getTaxId($data, $separator = ':')
    {
        if (!$data) {
            return null;
        }
        if ((string)$data === '-1') {
            return 0;
        }
        $this->preloadTaxes();
        list($name, $value) = array_slice(explode($separator, $data), 0, 2);
        if ($name === 'id') {
            return (int)$value;
        }
        if (isset($this->taxes[$name])) {
            return (int)$this->taxes[$name]->tax_id;
        }
        if (!$name) {
            return 0;
        }

        return $this->createTax(['tax_name' => $name, 'tax_value' => (float)$value])->tax_id;
    }

    /**
     * @param jshopProduct $jshopProduct
     *
     * @return string
     *
     * @since 3.0.0
     */
    private function getURL($jshopProduct)
    {
        $query = $this->db->getQuery(true);
        $query->select($this->db->qn('alias'));
        $query->select($this->db->qn('home'));
        $query->from($this->db->qn('#__menu'));
        $query->where($this->db->qn('link') . ' = ' . $this->db->quote('index.php?option=com_jshopping&controller=category&task=&category_id=&manufacturer_id=&label_id=&vendor_id=&page=&price_from=&price_to=&product_id='), 'OR');
        $query->where($this->db->qn('link') . ' = ' . $this->db->quote('index.php?option=com_jshopping&controller=category'), 'OR');
        $query->where($this->db->qn('link') . ' = ' . $this->db->quote('index.php?option=com_jshopping&view=category'), 'OR');
        $this->db->setQuery($query);
        $menu = $this->db->loadObject();
        if (is_object($menu)) {
            if ($menu->home) {
                $urlBase = str_replace('/administrator', '', JUri::base());
            } else {
                $urlBase = str_replace('/administrator', '', JUri::base()) . $menu->alias . '/';
            }
        } else {
            return '';
        }
        if ($category_id = $jshopProduct->get('category_id')) {
            $jshopCategory = JTable::getInstance('category', 'jshop');
            $jshopCategory->load($category_id);
            if ($jshopCategory->get($this->jshopLang->get('alias'))) {
                if ($jshopProduct->get($this->jshopLang->get('alias'))) {
                    return $urlBase . $jshopCategory->get($this->jshopLang->get('alias')) . "/" . $jshopProduct->get($this->jshopLang->get('alias'));
                } else {
                    return $urlBase . 'product/view/' . $category_id . '/' . $jshopProduct->get('product_id');
                }
            } else {
                return $urlBase . 'product/view/' . $category_id . '/' . $jshopProduct->get('product_id');
            }
        }

        return '';
    }

    /**
     * @param null|string $data
     * @param string $separator
     *
     * @return int
     *
     * @since 3.0.0
     */
    private function getUnitId($data, $separator = ':')
    {
        if (!$data || (string)$data === '-1') {
            return 0;
        }
        $this->preloadUnits();
        list($name, $qty) = array_slice(explode($separator, $data), 0, 2);
        if ($qty === null && isset($this->units[(int)$name])) {
            return (int)$name;
        }
        if (isset($this->units[(string)$name])) {
            return $this->units[(string)$name];
        }
        if (!$name) {
            return 0;
        }
        $data = [$this->jshopLang->get('name') => $name, 'qty' => (int)$qty];

        return $this->createUnit($data);
    }

    /**
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\RowCellIterator $cells
     * @param string $columnName
     *
     * @return mixed
     *
     * @since 3.0.0
     */
    private function getValue($cells, $columnName)
    {
        try {
            $structure = $this->get('structure');
            if (!isset($structure[$columnName])) {
                return null;
            }
            $columnId = $structure[$columnName];
            $cells->seek($columnId);
            $pValue = $cells->current()->getCalculatedValue();
            if ((string)$pValue === '-1') {
                return false;
            }
            unset($structure, $columnId, $columnName, $cells);

            return $pValue !== '' ? $pValue : null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param null|string $data
     *
     * @return int
     *
     * @since 3.0.0
     */
    private function getVendorId($data)
    {
        if (!$data || (string)$data === '-1') {
            return 0;
        }
        $this->preloadVendors();
        if (isset($this->vendors[(int)$data])) {
            return (int)$data;
        }
        if (isset($this->vendors[(string)$data])) {
            return $this->vendors[(string)$data];
        }
        $data = ['shop_name' => $data];

        return $this->createVendor($data);
    }

    /**
     * @param $arrObjData
     * @param array $arrSkipIndices
     *
     * @return array
     *
     * @since 3.0.0
     */
    private function objectsIntoArray($arrObjData, $arrSkipIndices = [])
    {
        $arrData = [];
        if (is_object($arrObjData)) {
            $arrObjData = get_object_vars($arrObjData);
        }
        if (is_array($arrObjData)) {
            foreach ($arrObjData as $index => $value) {
                if (is_object($value) || is_array($value)) {
                    $value = $this->objectsIntoArray($value, $arrSkipIndices);
                }
                if (in_array($index, $arrSkipIndices)) {
                    continue;
                }
                $arrData[$index] = $value;
            }
        }

        return $arrData;
    }

    /**
     * @param jshopProduct $jshopProduct
     * @param string $data
     * @param string $separator
     * @param bool $executeResize
     * @param bool $clearOldData
     *
     * @since 3.0.0
     */
    private function parseAttributes($jshopProduct, $data, $separator = ':', $executeResize = false, $clearOldData = true)
    {
		if (!$data || $clearOldData) {
			$jshopProductAttribute = JTable::getInstance('productAttribut', 'jshop');
			foreach ($jshopProduct->getAttributes() as $attribute) {
				$db = $jshopProductAttribute->getDbo();
				
				if ($attribute->ext_attribute_product_id) {
					$query = $db->getQuery(true);
					$query->delete($db->qn('#__jshopping_products_images'));
					$query->where($db->qn('product_id') . ' = ' . $db->q($attribute->ext_attribute_product_id));
					$db->setQuery($query)->execute();
					
					$query = $db->getQuery(true);
					$query->delete($db->qn('#__jshopping_products_videos'));
					$query->where($db->qn('product_id') . ' = ' . $db->q($attribute->ext_attribute_product_id));
					$db->setQuery($query)->execute();
					
					$query = $db->getQuery(true);
					$query->delete($db->qn('#__jshopping_products_files'));
					$query->where($db->qn('product_id') . ' = ' . $db->q($attribute->ext_attribute_product_id));
				}
				
				$jshopProductAttribute->deleteAttribute($attribute->product_attr_id);
			}
			unset($jshopProductAttribute);
			$jshopProductAttribute2 = JTable::getInstance('productAttribut2', 'jshop');
			$jshopProductAttribute2->set('product_id', $jshopProduct->get('product_id'));
			$jshopProductAttribute2->deleteAttributeForProduct();
			unset($jshopProductAttribute2);
		}
        if ($data) {
            $updateAttributes = array();
            $this->preloadAttributes();
            $this->preloadAttributeValues();
            foreach (ComielHelper::parseMultipleLine($data) as $attributeInList) {
                $attributeInList = ComielHelper::parseLine(str_replace('\|', '|', $attributeInList), ';');
                if (count($attributeInList) === 1) {
                    $independentMods = ['+', '-', '*', '/', '=', '%'];
                    $attribute = ComielHelper::parseLine(str_replace('\;', ';', current($attributeInList)), $separator);
                    if (isset($attribute[2]) && in_array($attribute[2], $independentMods)) {
                        $attributeName = $attribute[0];
                        $attributeValue = $attribute[1];
                        $attributeMod = $attribute[2];
                        $attributePrice = isset($attribute[3]) ? (float)$attribute[3] : 0;
                        if (isset($this->attributes[1][$attributeName])) {
                            $attributeId = $this->attributes[1][$attributeName];
							$updateAttributes[] = $attributeId;
                        } else {
                            $attributeId = $this->createAttribute([
                                $this->jshopLang->get('name') => $attributeName,
                                'independent' => 1,
                                strtolower('allCats') => 1,
                                strtolower('cats') => serialize([]),
                            ]);
                        }
                        if (isset($this->attributeValues[$attributeId][$attributeValue])) {
                            $attributeValueId = $this->attributeValues[$attributeId][$attributeValue];
                        } else {
                            $attributeValueId = $this->createAttributeValue([
                                'attr_id' => $attributeId,
                                $this->jshopLang->get('name') => $attributeValue,
                            ]);
                        }
                        $jshopProductAttribute2 = JTable::getInstance("ProductAttribut2", 'jshop');
                        $jshopProductAttribute2->set('product_id', $jshopProduct->get('product_id'));
                        $jshopProductAttribute2->set('attr_id', $attributeId);
                        $jshopProductAttribute2->set('attr_value_id', $attributeValueId);
                        $jshopProductAttribute2->set('price_mod', $attributeMod);
                        $jshopProductAttribute2->set('addprice', floatval(str_replace(',', '.', $attributePrice)));
                        if ($jshopProductAttribute2->check()) {
                            $jshopProductAttribute2->store();
                        }
                    }
                } else {
                    $comielProductAttribute = ComielModels::getInstance('productAttributes');
                    $updateAttributes = array_merge($updateAttributes, $comielProductAttribute->parseAttributeDependence($attributeInList, (int)$jshopProduct->get('product_id')));
                    if ($executeResize) {
                        foreach ($comielProductAttribute->images() as $imageName) {
                            $this->resize('attributeDependent', $imageName);
                        }
                    }
                }
            }
			if ($updateAttributes) {
				$productCategorys = $jshopProduct->getCategories(1);
				foreach ($updateAttributes as $attributeId) {
					if ($attributeId && isset($this->attributesToCategorys[$attributeId]) && $this->attributesToCategorys[$attributeId]['allcats'] == 0) {
						$needUpdate = false;
						foreach ($productCategorys as $category_id) {
							if (!in_array($category_id, $this->attributesToCategorys[$attributeId]['cats'])) {
								$this->attributesToCategorys[$attributeId]['cats'][] = $category_id;
								$needUpdate = true;
							}
						}
						if ($needUpdate) {
							$db = JFactory::getDbo();
							$db->setQuery('update ' . $db->qn('#__jshopping_attr') . ' set cats = ' . $db->q(serialize($this->attributesToCategorys[$attributeId]['cats'])) . ' where attr_id =' . $db->q($attributeId));
							$db->execute();
						}
					}
				}
			}
            unset($data, $comielProductAttribute);
        }
    }

    /**
     * @param jshopProduct $jshopProduct
     * @param null|string $data
     * @param string $separator
     * @param bool $generateAlias
     *
     * @since 3.0.0
     */
    private function parseCategories($jshopProduct, $data, $separator = ':', $generateAlias = false)
    {
        if (!$data) {
            return;
        }
        $jshopCategory = JTable::getInstance('category', 'jshop');
        if ((string)$data === '-1') {
            $query = $this->db->getQuery(true);
            $query->delete($this->db->qn('#__jshopping_products_to_categories'));
            $query->where($this->db->qn('product_id') . ' = ' . $this->db->q($jshopProduct->get('product_id')));
            $this->db->setQuery($query)->execute();

            return;
        }
        $this->preloadCategories();
        $cid = [];
        foreach (ComielHelper::parseMultipleLine($data) as $categoryData) {
            $categoryId = null;
            $categoryParentId = null;
            foreach (explode($this->params->get('categoryDelimiter', '/'), $categoryData) as $categoryParent) {
                list($name, $alias, $image) = array_slice(explode($separator, $categoryParent), 0, 3);
                if (strpos($name, ';') !== false) {
                    list($categoryParentId, $name) = explode(';', $name, 2);
                }
                if (!$name) {
                    continue;
                }
                if (isset($this->categories[(int)$categoryParentId][(string)$name])) {
                    $categoryId = $this->categories[(int)$categoryParentId][(string)$name];
                    $categoryParentId = $categoryId;
                    continue;
                }
                if ($jshopCategory->load(['category_parent_id' => $categoryParentId, $this->jshopLang->get('name') => $name])) {
                    $categoryId = (int)$jshopCategory->get('category_id');
                    $categoryParentId = $categoryId;
                    $this->categories[(int)$jshopCategory->get('category_parent_id')][$jshopCategory->get($this->jshopLang->get('name'))] = $categoryId;
                    continue;
                }
                if (!$alias && $generateAlias) {
                    $alias = $this->generateAlias($jshopCategory, $name);
                }
                $data = [
                    $this->jshopLang->get('name') => $name,
                    $this->jshopLang->get('alias') => $alias,
                    'category_image' => $image,
                    'category_parent_id' => $categoryParentId ? $categoryParentId : $categoryId,
                ];
                $categoryId = $this->createCategory($data);
                $categoryParentId = $categoryId;
            }
            array_unshift($cid, $categoryId);
        }
        $cid = array_diff($cid, [null, 0, '']);
        $jshoppingModelProducts = JModelLegacy::getInstance('products', 'jshoppingModel');
        $jshoppingModelProducts->setCategoryToProduct($jshopProduct->get('product_id'), $cid);

        return;
    }

    /**
     * @param jshopProduct $jshopProduct
     * @param string $data
     * @param string $separator
     * @param bool $clearOldData
     *
     * @since 3.0.0
     */
    private function parseExtraFields($jshopProduct, $data, $separator = ':', $clearOldData = true)
    {
        $this->preloadExtraFields();
        if (!$data || $clearOldData) {
            foreach ($this->extraFields as $groupId => $extraFields) {
                foreach ($extraFields as $extraFieldID) {
                    $jshopProduct->set('extra_field_' . $extraFieldID, '');
                }
            }
			if (!$data) {
				$jshopProduct->store();
				return;
			}
        }
		
		$db = JFactory::getDbo();
		$jshopConfig = JSFactory::getConfig();
		
		$extraFields = $updateData = array();
        foreach (ComielHelper::parseMultipleLine($data) as $extraFieldData) {
            $type = 0;
            $multilist = 1;
            if (strpos($extraFieldData, 'list/') === 0) {
                $multilist = 0;
                $extraFieldData = str_replace('list/', '', $extraFieldData);
            }

            list($groupName, $extraFieldData) = array_slice(ComielHelper::parseLine(str_replace('\|', '|', $extraFieldData), ';'), 0, 2);
			$groupName = (string)$groupName;
            if ($extraFieldData === null) {
                $extraFieldData = $groupName;
                $groupName = '';
            }
			if ($groupName !== '') {
                $this->preloadExtraFieldGroups();
                if (!isset($this->extraFieldGroups[$groupName])) {
                    $updateData[] = $groupName;
                }
            }

			if (!isset($extraFields[$groupName])) {
				$extraFields[$groupName] = array();
			}

            list($extraFieldName, $extraFieldValue) = array_slice(ComielHelper::parseLine($extraFieldData, $separator), 0, 2);
            $extraFieldValues = array();
            if (preg_match('/^\"(.*?)\"$/', $extraFieldValue, $extraFieldValuesTmp)) {
                if (isset($extraFieldValuesTmp[1])) {
                    $extraFieldValues = array($extraFieldValuesTmp[1] => $extraFieldValuesTmp[1]);
                }
                $type = 1;
                $multilist = 0;
            }
			$extraFieldName = str_replace(array('\;', '\:', '\,'), array(';', ':', ','), (string)$extraFieldName);
			if ($extraFieldName === '') {
				continue;
			}
			if (!isset($extraFields[$groupName][$extraFieldName])) {
				$extraFields[$groupName][$extraFieldName] = array(
					'type' => $type,
					'multilist' => $multilist,
					'values' => array()
				);
			}
			
			if ($type) {
				$extraFields[$groupName][$extraFieldName]['values'] = str_replace(array('\;', '\:', '\,'), array(';', ':', ','), $extraFieldValues);
			} else {
				if ($multilist) {
					$extraFieldValuesTmp = ComielHelper::parseLine($extraFieldValue, ',');
				} else {
					$extraFieldValuesTmp = array($extraFieldValue);
				}
				foreach ($extraFieldValuesTmp as $extraFieldValueName) {
					$extraFieldValueName = str_replace(array('\;', '\:', '\,'), array(';', ':', ','), $extraFieldValueName);
					if ($extraFieldValueName === '') {
						continue;
					}
					$extraFields[$groupName][$extraFieldName]['values'][$extraFieldValueName] = '';
				}
			}
        }
		
		if ($updateData) {
			$db->setQuery('SELECT MAX(ordering) FROM ' . $db->qn('#__jshopping_products_extra_field_groups'));
			$maxOrdering = (int)$db->loadResult();
			foreach ($updateData as $key=>$value) {
				$maxOrdering++;
				$updateData[$key] = '(' . $db->q($maxOrdering) . ',' . $db->q($value) . ')';
			}
			$db->setQuery('INSERT INTO ' . $db->qn('#__jshopping_products_extra_field_groups') . ' (' . $db->qn('ordering') . ',' . $db->qn($this->jshopLang->get('name')) . ') VALUES ' . implode(',', $updateData));
			$db->execute();
			$this->extraFieldGroups = null;
			$this->preloadExtraFieldGroups();
			$updateData = array();
		}
		
		foreach ($extraFields as $groupName=>$extraField) {
			$groupId = $groupName === '' ? 0 : $this->extraFieldGroups[$groupName];
			foreach ($extraField as $extraFieldName=>$extraFieldData) {
				if (!isset($this->extraFields[$groupId][$extraFieldName])) {
					$updateData[] = array(
						'name' => $extraFieldName,
						'type' => $extraFieldData['type'],
						'multilist' => $extraFieldData['multilist'],
						'group' => $groupId
					);
				}
			}
		}
		
		if ($updateData) {
			$db->setQuery('SELECT MAX(ordering) FROM ' . $db->qn('#__jshopping_products_extra_fields'));
			$maxOrdering = (int)$db->loadResult();
			foreach ($updateData as $key=>$value) {
				$maxOrdering++;
				$updateData[$key] = '(' . $db->q('1') . ',' . $db->q('a:0:{}') . ',' . $db->q($value['type']) . ',' . $db->q($value['multilist']) . ',' . $db->q($value['group']) . ',' . $db->q($maxOrdering) . ',' . $db->q($value['name']) . ')';
			}
			$db->setQuery('INSERT INTO ' . $db->qn('#__jshopping_products_extra_fields') . ' (' . $db->qn('allcats') . ',' . $db->qn('cats') . ',' . $db->qn('type') . ',' . $db->qn('multilist') . ',' . $db->qn('group') . ',' . $db->qn('ordering') . ',' . $db->qn($this->jshopLang->get('name')) . ') VALUES ' . implode(',', $updateData));
			$db->execute();
			$this->extraFields = null;
			$this->preloadExtraFields();
			$updateData = array();
			
			foreach ($this->extraFields as $groupId=>$extraFieldData) {
				foreach ($extraFieldData as $extraFieldName=>$extraFieldID) {
					if (!property_exists($jshopProduct, 'extra_field_'.$extraFieldID)) {
						$updateData[$extraFieldID] = 'ADD ' . $db->qn('extra_field_'.$extraFieldID) . ' TEXT NOT NULL';
					}
				}
			}
			if ($updateData) {
				$db->setQuery('ALTER TABLE ' . $db->qn('#__jshopping_products') . ' ' . implode(',', $updateData));
				$db->execute();
				$updateData = array();
			}
		}
		
		$this->preloadExtraFieldValues();
		foreach ($extraFields as $groupName=>$extraField) {
			$groupId = $groupName === '' ? 0 : $this->extraFieldGroups[$groupName];
			foreach ($extraField as $extraFieldName=>$extraFieldData) {
				if ($extraFieldData['type']) {
					continue;
				}
				$extraFieldID = $this->extraFields[$groupId][$extraFieldName];
				foreach ($extraFieldData['values'] as $extraFieldOptionName=>$extraFieldOptionValue) {
					if (!isset($this->extraFieldValues[$extraFieldID][$extraFieldOptionName])) {
						$updateData[] = array(
							'id' => $extraFieldID,
							'name' => $extraFieldOptionName
						);
					}
				}
			}
		}
		
		if ($updateData) {
			$db->setQuery('SELECT field_id, MAX(ordering) as ordering FROM ' . $db->qn('#__jshopping_products_extra_field_values') . ' GROUP BY field_id');
			$maxOrderings = $db->loadObjectList('field_id');
			foreach ($updateData as $key=>$value) {
				if (isset($maxOrderings[$value['id']])) {
					$maxOrderings[$value['id']]->ordering++;
				} else {
					$maxOrderings[$value['id']]->ordering = 1;
				}
				$updateData[$key] = '(' . $db->q($value['id']) . ',' . $db->q($maxOrderings[$value['id']]->ordering) . ',' . $db->q($value['name']) . ')';
			}
			$db->setQuery('INSERT INTO ' . $db->qn('#__jshopping_products_extra_field_values') . ' (' . $db->qn('field_id') . ',' . $db->qn('ordering') . ',' . $db->qn($this->jshopLang->get('name')) . ') VALUES ' . implode(',', $updateData));
			$db->execute();
			$this->extraFieldValues = null;
			$this->preloadExtraFieldValues();
			$updateData = array();
		}

		foreach ($extraFields as $groupName=>$extraField) {
			$groupId = $groupName === '' ? 0 : $this->extraFieldGroups[$groupName];
			foreach ($extraField as $extraFieldName=>$extraFieldData) {
				$extraFieldID = $this->extraFields[$groupId][$extraFieldName];
				if (!$extraFieldData['type']) {
					foreach ($extraFieldData['values'] as $extraFieldOptionName=>$extraFieldOptionValue) {
						if (isset($this->extraFieldValues[$extraFieldID][$extraFieldOptionName])) {
							$extraFieldData['values'][$extraFieldOptionName] = $this->extraFieldValues[$extraFieldID][$extraFieldOptionName];
						} else {
							unset($extraFieldData['values'][$extraFieldOptionName]);
						}
					}
				}
				$jshopProduct->set('extra_field_' . $extraFieldID, implode(',', $extraFieldData['values']));
				if ($this->extraFieldsToCategorys[$extraFieldID]['allcats'] == 0) {
					$needUpdate = false;
					$productCategorys = $jshopProduct->getCategories(1);
					foreach ($productCategorys as $category_id) {
						if (!in_array($category_id, $this->extraFieldsToCategorys[$extraFieldID]['cats'])) {
							$this->extraFieldsToCategorys[$extraFieldID]['cats'][] = $category_id;
							$needUpdate = true;
						}
					}
					if ($needUpdate) {
						$db->setQuery('update ' . $db->qn('#__jshopping_products_extra_fields') . ' set cats = ' . $db->q(serialize($this->extraFieldsToCategorys[$extraFieldID]['cats'])) . ' where id =' . $db->q($extraFieldID));
						$db->execute();
					}
				}
			}
		}
        $jshopProduct->store();

        return;
    }

    /**
     * @param jshopProduct $jshopProduct
     * @param string|null $data
     * @param string $separator
     *
     * @since 3.0.0
     */
    private function parseFiles($jshopProduct, $data = null, $separator = ':')
    {
        if (!$data) {
            return;
        }
        $jshopProductFiles = JTable::getInstance('ProductFiles', 'jshop');
        if ((string)$data === '-1') {
            $query = $this->db->getQuery(true);
            $query->delete($this->db->qn($jshopProductFiles->getTableName()));
            $query->where($this->db->qn('product_id') . ' = ' . $this->db->q($jshopProduct->get('product_id')));
            $this->db->setQuery($query)->execute();

            return;
        }
        $this->preloadFiles();
        $cid = $this->files[(int)$jshopProduct->get('product_id')];
        foreach (ComielHelper::parseMultipleLine($data) as $fileData) {
            if ($fileData === 'clear') {
                $query = $this->db->getQuery(true);
                $query->delete($this->db->qn($jshopProductFiles->getTableName()));
                $query->where($this->db->qn('product_id') . ' = ' . $this->db->q($jshopProduct->get('product_id')));
                $this->db->setQuery($query)->execute();
                continue;
            }
            list($file, $fileDesc, $demo, $demoDesc) = array_slice(explode($separator, $fileData), 0, 4);
            if (!$file && !$demo) {
                continue;
            }
            if ($file && isset($cid[(string)$file])) {
                unset($cid[(string)$file]);
                continue;
            }
            if ($demo !== null && isset($cid[(string)$demo])) {
                unset($cid[(string)$demo]);
                continue;
            }
            $data = [
                'product_id' => $jshopProduct->get('product_id'),
                'file' => (string)$file,
                'file_descr' => (string)$fileDesc,
                'demo' => (string)$demo,
                'demo_descr' => (string)$demoDesc,
                'ordering' => $jshopProductFiles->getNextOrder($this->db->qn('product_id') . ' = ' . $this->db->q($jshopProduct->get('product_id'))),
            ];
            $this->createFile($data);
        }
        foreach ($cid as $id) {
            $jshopProductFiles->delete($id);
        }

        return;
    }

    /**
     * @param jshopProduct $jshopProduct
     * @param string|null $data
     *
     * @since 3.0.0
     */
    private function parseFreeAttributes($jshopProduct, $data = null)
    {
        if (!$data) {
            // TODO Changed to SQL-Request
            $jshoppingModelProducts = JModelLegacy::getInstance('products', 'jshoppingModel');
            $jshoppingModelProducts->saveFreeAttributes($jshopProduct->get('product_id'), null);
            return;
        }
        $this->preloadFreeAttributes();
        $cid = [];
        foreach (ComielHelper::parseMultipleLine($data) as $freeAttribute) {
            if (isset($this->freeAttributes[(string)$freeAttribute])) {
                $id = $this->freeAttributes[(string)$freeAttribute];
            } else {
                $id = $this->createFreeAttribute($freeAttribute);
            }
            if ($id !== null) {
                $cid[$id] = $id;
            }
        }
        // TODO Changed to SQL-Request
        $jshoppingModelProducts = JModelLegacy::getInstance('products', 'jshoppingModel');
        $jshoppingModelProducts->saveFreeAttributes($jshopProduct->get('product_id'), $cid);

        return;
    }

    /**
     * @param $action
     *
     * @since 3.0.0
     */
    private function parseParams($action)
    {
        if (($registry = $this->params->extract($action)) === null) {
            $this->saveConfig(false);
            $registry = $this->params->extract($action);
        }
        foreach ($registry as $path => $value) {
            $this->params->set($path, $value);
        }
		$session = JFactory::getSession();
        $app = JFactory::getApplication();
        $params = $app->input->post->get($action, [], 'array');
		if ($params) {
			$session->set($action.'_params', $params, $this->alias);
		} else {
			$params = $session->get($action.'_params', [], $this->alias);
		}
        foreach ($registry as $path => $value) {
            if (isset($params[$path])) {
                $this->params->set($path, $params[$path]);
            }
            if (($value = $app->input->get->get($path)) !== null) {
                $this->params->set($path, $value);
            }
        }
        unset($app, $registry, $action, $params, $path, $value);
    }

    /**
     * @param jshopProduct $jshopProduct
     * @param string $data
     * @param string $separator
     *
     * @since 3.0.0
     */
    private function parsePrices($jshopProduct, $data, $separator = ';')
    {
		if ($data) {
			$UnitsInData = explode(";", $data);
			if (count($UnitsInData) == 2) {
				$lang = JSFactory::getLang();
				$unit_id = $this->db->setQuery(
					$this->db->getQuery(true)
						->select('id')
						->from($this->db->qn('#__jshopping_unit'))
						->where($this->db->qn($lang->get('name')) . ' = ' . $this->db->q($UnitsInData[0]))
				)->loadResult();
				if (!$unit_id) {
					$this->db->setQuery(
						$this->db->getQuery(true)
							->insert($this->db->qn('#__jshopping_unit'))
							->set($this->db->qn($lang->get('name')) . ' = ' . $this->db->q($UnitsInData[0]))
							->set('qty = 1')
					)->execute();
					$unit_id = $this->db->insertid();
				}
				if ($unit_id) {
					$jshopProduct->product_is_add_price = 0;
					$jshopProduct->add_price_unit_id = 0;
					$this->db->setQuery(
						$this->db->getQuery(true)
							->delete($this->db->qn('#__jshopping_products_prices'))
							->where('product_id = ' . $this->db->q($jshopProduct->product_id))
					)->execute();
					
					$query = $this->db->getQuery(true);
					$query->insert($this->db->qn('#__jshopping_products_prices'));
					$query->columns($this->db->qn(array('product_id','discount','product_quantity_start','product_quantity_finish')));
					
					$maxDiscount = 0;
					$AddPricesInData = explode("|", $UnitsInData[1]);
					foreach ($AddPricesInData as $AddPrice) {
						$AddPrice = explode($separator, $AddPrice);
						$price = floatval($jshopProduct->get("product_price", 0));
						$discount = 0;
						$add_price = 0;
						if (count($AddPrice) == 3) {
							$percent = floatval($AddPrice[2]);
							$add_price = $price - ($price / 100 * $percent);
							$discount = $AddPrice[2];
						} elseif (count($AddPrice) == 4) {
							$add_price = floatval($AddPrice[3]);
							$discount = $price / $add_price;
						}
						$maxDiscount = max($discount, $maxDiscount);
						$query->values($this->db->q($jshopProduct->product_id).','.$this->db->q($discount).','.$this->db->q($AddPrice[0]).','.$this->db->q($AddPrice[1]));
					}
					if ($AddPricesInData) {
						$this->db->setQuery($query)->execute();

						$jshopProduct->product_is_add_price = 1;
						$jshopProduct->add_price_unit_id = $unit_id;
					}
					return $maxDiscount;
				}
			}
		} else {
			$this->db->setQuery(
				$this->db->getQuery(true)
					->delete($this->db->qn('#__jshopping_products_prices'))
					->where('product_id = ' . $this->db->q($jshopProduct->product_id))
			)->execute();
			$jshopProduct->product_is_add_price = 0;
			$jshopProduct->add_price_unit_id = 0;
		}
		return false;
    }

    /**
     * @param jshopProduct $jshopProduct
     * @param string $data
     * @param int $relatedFieldName
     *
     * @return array
     *
     * @since 3.0.0
     */
    private function parseRelated($jshopProduct, $data, $relatedFieldName = 0)
    {
        $returnRelated = [];
        if ($data) {
            if ($relatedFieldName === 'ean') {
                $related_field = "product_ean";
            } else {
                $related_field = "product_id";
            }
			$data = explode("|", $data);
			if (is_array($data) && count($data)) {
				$query = $this->db->getQuery(true);
				$query->select("product_related_id");
				$query->from("#__jshopping_products_relations");
				$query->where("product_id = " . $jshopProduct->get('product_id'));
				$this->db->setQuery($query);
				$existRelated = $this->db->loadColumn();
				foreach ($data as $product) {
					if ($product == "-1") {
						$query = $this->db->getQuery(true);
						$query->delete("#__jshopping_products_relations");
						$query->where("product_id = " . $jshopProduct->get('product_id'));
						$this->db->setQuery($query);
						$this->db->execute();
					} else {
						$query = $this->db->getQuery(true);
						$query->select("product_id");
						$query->from("#__jshopping_products");
						$query->where($related_field . " = " . $this->db->q($product) . " LIMIT 1");
						$this->db->setQuery($query);
						$productObject = $this->db->loadObject();
						if ($productObject->product_id && !in_array($productObject->product_id, $existRelated)) {
							$returnRelated[] = $productObject;
							$query = $this->db->getQuery(true);
							$query->insert("#__jshopping_products_relations");
							$query->set("product_id = " . $jshopProduct->get('product_id'));
							$query->set("product_related_id = " . $productObject->product_id);
							$this->db->setQuery($query);
							$this->db->execute();
							$existRelated[] = $productObject->product_id;
						}
					}
				}
			}
            unset($data, $db, $query, $product, $productObject, $yesRecord);
        } else {
			$query = $this->db->getQuery(true);
			$query->delete("#__jshopping_products_relations");
			$query->where("product_id = " . $jshopProduct->get('product_id'));
			$this->db->setQuery($query);
			$this->db->execute();
		}

        return $returnRelated;
    }

    /**
     * @param jshopProduct $jshopProduct
     * @param string|null $data
     * @param string $separator
     *
     * @since 3.0.0
     */
    private function parseVideo($jshopProduct, $data = null, $separator = ':')
    {
        if (!$data) {
            return;
        }
        $jshopProductVideo = JTable::getInstance('productVideo', 'jshop');
        if ((string)$data === '-1') {
            $query = $this->db->getQuery(true);
            $query->delete($this->db->qn($jshopProductVideo->getTableName()));
            $query->where($this->db->qn('product_id') . ' = ' . $this->db->q($jshopProduct->get('product_id')));
            $this->db->setQuery($query)->execute();

            return;
        }
        $this->preloadVideo();
        $cid = (array)$this->video[(int)$jshopProduct->get('product_id')];
        foreach (ComielHelper::parseMultipleLine($data) as $videoData) {
            list($type, $video) = explode(';', $videoData);
            if (!$video) {
                continue;
            }
            $name = null;
            $code = null;
            $preview = null;
            switch ($type) {
                case 'name':
                    list($name, $preview) = array_slice(explode($separator, $video), 0, 2);
                    break;
                case 'code':
                    list($code, $preview) = array_slice(explode($separator, $video), 0, 2);
                    break;
            }
            if ($name === null && $code === null) {
                continue;
            }
            if ($name !== null && isset($cid[(string)$name])) {
                unset($cid[(string)$name]);
                continue;
            }
            if ($code !== null && isset($cid[(string)$code])) {
                unset($cid[(string)$code]);
                continue;
            }
            $data = [
                'product_id' => $jshopProduct->get('product_id'),
                'video_name' => (string)$name,
                'video_code' => (string)$code,
                'video_preview' => (string)$preview,
            ];
            $this->createVideo($data);
        }
        foreach ($cid as $id) {
            $jshopProductVideo->delete($id);
        }

        return;
    }

    /**
     * @param null $mode
     *
     * @return array|null
     *
     * @since 3.0.0
     */
    private function preloadAttributeValues($mode = null)
    {
        if ($this->attributeValues === null) {
            $this->attributeValues = [];
            $jshopAttributeValue = JTable::getInstance('attributValue', 'jshop');
            $query = $this->db->getQuery(true);
            $query->select($this->db->qn('value_id'));
            $query->select($this->db->qn('attr_id'));
            $query->select($this->db->qn($this->jshopLang->get('name'), 'name'));
            $query->from($this->db->qn($jshopAttributeValue->getTableName()));
            $attributeValues = $this->db->setQuery($query)->loadObjectList();
            foreach ($attributeValues as $attributeValue) {
                if ($mode === 'name') {
                    $this->attributeValues[(int)$attributeValue->attr_id][(int)$attributeValue->value_id] = (string)$attributeValue->name;
                } else {
                    $this->attributeValues[(int)$attributeValue->attr_id][(string)$attributeValue->name] = (int)$attributeValue->value_id;
                }
            }
            unset($attributeValues, $attributeValue);
        }

        return $this->attributeValues;
    }

    /**
     * @param null $mode
     *
     * @return array|null
     *
     * @since 3.0.0
     */
    private function preloadAttributes($mode = null)
    {
        if ($this->attributes === null) {
            $this->attributes = [
                0 => [],
                1 => [],
            ];
			$this->attributesToCategorys = array();
            $jshopAttribute = JTable::getInstance('attribut', 'jshop');
            $query = $this->db->getQuery(true);
            $query->select($this->db->qn($jshopAttribute->getKeyName(), 'id'));
            $query->select($this->db->qn('independent', 'independent'));
            $query->select($this->db->qn($this->jshopLang->get('name'), 'name'));
            $query->select($this->db->qn('allcats'));
            $query->select($this->db->qn('cats'));
            $query->from($this->db->qn($jshopAttribute->getTableName()));
            $attributes = $this->db->setQuery($query)->loadObjectList();
            foreach ($attributes as $attribute) {
                if ($mode === 'name') {
                    $this->attributes[(int)$attribute->independent][(int)$attribute->id] = (string)$attribute->name;
                } else {
                    $this->attributes[(int)$attribute->independent][(string)$attribute->name] = (int)$attribute->id;
                }
				$this->attributesToCategorys[$attribute->id] = array(
					'allcats' => $attribute->allcats,
					'cats' => unserialize($attribute->cats),
				);
            }
            unset($attributes, $attribute);
        }

        return $this->attributes;
    }

    /**
     * @return array|null
     *
     * @since 3.0.0
     */
    private function preloadCategories()
    {
        if ($this->categories === null) {
            $this->categories = [];
            $jshopCategory = JTable::getInstance('category', 'jshop');
            $query = $this->db->getQuery(true);
            $query->select($this->db->qn('category_id', 'id'));
            $query->select($this->db->qn('category_parent_id', 'parentId'));
            $query->select($this->db->qn($this->jshopLang->get('name'), 'name'));
            $query->select($this->db->qn($this->jshopLang->get('alias'), 'alias'));
            $query->select($this->db->qn('category_image', 'image'));
            $query->from($this->db->qn($jshopCategory->getTableName()));
            $categories = $this->db->setQuery($query)->loadObjectList();
            foreach ($categories as $category) {
                $this->categories[(int)$category->parentId][(string)$category->name] = (int)$category->id;
            }
            unset($categories, $category);
        }

        return $this->categories;
    }

    /**
     * @return array|null
     *
     * @since 3.0.0
     */
    private function preloadCurrencies()
    {
        if ($this->currencies === null) {
            $this->currencies = [];
            $jshopCurrency = JTable::getInstance('currency', 'jshop');
            $query = $this->db->getQuery(true);
            $query->select($this->db->qn($jshopCurrency->getKeyName(), 'id'));
            $query->select($this->db->qn('currency_name', 'name'));
            $query->select($this->db->qn('currency_code', 'code'));
            $query->select($this->db->qn('currency_code_iso', 'iso'));
            $query->from($this->db->qn($jshopCurrency->getTableName()));
            $currencies = $this->db->setQuery($query)->loadObjectList();
            foreach ($currencies as $currency) {
                $this->currencies[$currency->name] = (int)$currency->id;
                $this->currencies[$currency->code] = (int)$currency->id;
                $this->currencies[$currency->iso] = (int)$currency->id;
            }
            unset($currencies, $currency);
        }

        return $this->currencies;
    }

    /**
     * @return array|null
     *
     * @since 3.0.0
     */
    private function preloadDeliveryTimes()
    {
        if ($this->deliveryTimes === null) {
            $this->deliveryTimes = [];
            $jshopDeliveryTimes = JTable::getInstance('deliveryTimes', 'jshop');
            $query = $this->db->getQuery(true);
            $query->select($this->db->qn($jshopDeliveryTimes->getKeyName(), 'id'));
            $query->select($this->db->qn($this->jshopLang->get('name'), 'name'));
            $query->from($this->db->qn($jshopDeliveryTimes->getTableName()));
            $deliveryTimes = $this->db->setQuery($query)->loadObjectList();
            foreach ($deliveryTimes as $deliveryTime) {
                $this->deliveryTimes[(string)$deliveryTime->name] = (int)$deliveryTime->id;
            }
            unset($deliveryTimes, $deliveryTime);
        }

        return $this->deliveryTimes;
    }

    /**
     * @return array|null
     *
     * @since 3.0.0
     */
    private function preloadExtraFieldGroups()
    {
        if ($this->extraFieldGroups === null) {
            $this->extraFieldGroups = [];
            $jshopProductFieldGroup = JTable::getInstance('productFieldGroup', 'jshop');
            $query = $this->db->getQuery(true);
            $query->select($this->db->qn($jshopProductFieldGroup->getKeyName(), 'id'));
            $query->select($this->db->qn($this->jshopLang->get('name'), 'name'));
            $query->from($this->db->qn($jshopProductFieldGroup->getTableName()));
            $extraFieldGroups = $this->db->setQuery($query)->loadObjectList();
            foreach ($extraFieldGroups as $extraFieldGroup) {
                $this->extraFieldGroups[(string)$extraFieldGroup->name] = (int)$extraFieldGroup->id;
            }
            unset($extraFieldGroups, $extraFieldGroup);
        }

        return $this->extraFieldGroups;
    }

    /**
     * @return array|null
     *
     * @since 3.0.0
     */
    private function preloadExtraFieldValues()
    {
        if ($this->extraFieldValues === null) {
            $this->extraFieldValues = [];
            $jshopProductFieldValue = JTable::getInstance('productFieldValue', 'jshop');
            $query = $this->db->getQuery(true);
            $query->select($this->db->qn($jshopProductFieldValue->getKeyName(), 'id'));
            $query->select($this->db->qn($this->jshopLang->get('name'), 'name'));
            $query->select($this->db->qn('field_id', 'extraFieldId'));
            $query->from($this->db->qn($jshopProductFieldValue->getTableName()));
            $extraFieldValues = $this->db->setQuery($query)->loadObjectList();
            foreach ($extraFieldValues as $extraFieldValue) {
                $this->extraFieldValues[(int)$extraFieldValue->extraFieldId][(string)$extraFieldValue->name] = (int)$extraFieldValue->id;
            }
            unset($extraFieldValues, $extraFieldGroup);
        }

        return $this->extraFieldValues;
    }

    /**
     * @return array|null
     *
     * @since 3.0.0
     */
    private function preloadExtraFields()
    {
        if ($this->extraFields === null) {
            $this->extraFields = $this->extraFieldsToCategorys = [];
            $jshopProductField = JTable::getInstance('productField', 'jshop');
            $query = $this->db->getQuery(true);
            $query->select($this->db->qn($jshopProductField->getKeyName(), 'id'));
            $query->select($this->db->qn($this->jshopLang->get('name'), 'name'));
            $query->select($this->db->qn('group', 'groupId'));
            $query->select($this->db->qn('allcats'));
            $query->select($this->db->qn('cats'));
            $query->from($this->db->qn($jshopProductField->getTableName()));
            $extraFields = $this->db->setQuery($query)->loadObjectList();
            foreach ($extraFields as $extraField) {
                $this->extraFields[(int)$extraField->groupId][(string)$extraField->name] = (int)$extraField->id;
				$this->extraFieldsToCategorys[$extraField->id] = array(
					'allcats' => $extraField->allcats,
					'cats' => unserialize($extraField->cats),
				);
            }
            unset($extraFields, $extraField);
        }

        return $this->extraFields;
    }

    /**
     * @return array|null
     *
     * @since 3.0.0
     */
    private function preloadFiles()
    {
        if ($this->files === null) {
            $this->files = [];
            $jshopProductFiles = JTable::getInstance('productFiles', 'jshop');
            $query = $this->db->getQuery(true);
            $query->select($this->db->qn($jshopProductFiles->getKeyName(), 'id'));
            $query->select($this->db->qn('product_id', 'productId'));
            $query->select($this->db->qn('file', 'file'));
            $query->select($this->db->qn('demo', 'demo'));
            $query->from($this->db->qn($jshopProductFiles->getTableName()));
            $files = $this->db->setQuery($query)->loadObjectList();
            foreach ($files as $file) {
                if ((string)$file->file) {
                    $this->files[(int)$file->productId][(string)$file->file] = (int)$file->id;
                }
                if ((string)$file->demo) {
                    $this->files[(int)$file->productId][(string)$file->demo] = (int)$file->id;
                }
            }
            unset($files, $file);
        }

        return $this->files;
    }

    /**
     * @return array|null
     *
     * @since 3.0.0
     */
    private function preloadFreeAttributes()
    {
        if ($this->freeAttributes === null) {
            $this->freeAttributes = [];
            $jshopFreeAttribute = JTable::getInstance('freeAttribut', 'jshop');
            $query = $this->db->getQuery(true);
            $query->select($this->db->qn($jshopFreeAttribute->getKeyName(), 'id'));
            $query->select($this->db->qn($this->jshopLang->get('name'), 'name'));
            $query->from($this->db->qn($jshopFreeAttribute->getTableName()));
            $freeAttributes = $this->db->setQuery($query)->loadObjectList();
            foreach ($freeAttributes as $freeAttribute) {
                $this->freeAttributes[(string)$freeAttribute->name] = (int)$freeAttribute->id;
            }
            unset($freeAttributes, $freeAttribute);
        }

        return $this->freeAttributes;
    }

    /**
     * @return array|null
     *
     * @since 3.0.0
     */
    private function preloadImages()
    {
        if ($this->images === null) {
            $this->images = [];
            $jshopImage = JTable::getInstance('image', 'jshop');
            $query = $this->db->getQuery(true);
            $query->select($this->db->qn($jshopImage->getKeyName(), 'id'));
            $query->select($this->db->qn('product_id', 'productId'));
            $query->select($this->db->qn('image_name', 'image'));
            $query->from($this->db->qn($jshopImage->getTableName()));
            $images = $this->db->setQuery($query)->loadObjectList();
            foreach ($images as $image) {
                $this->images[(int)$image->productId][(string)$image->image] = (int)$image->id;
            }
            unset($images, $image);
        }

        return $this->images;
    }

    /**
     * @return array|null
     *
     * @since 3.0.0
     */
    private function preloadLabels()
    {
        if ($this->labels === null) {
            $this->labels = [];
            $jshopProductLabel = JTable::getInstance('productLabel', 'jshop');
            $query = $this->db->getQuery(true);
            $query->select($this->db->qn($jshopProductLabel->getKeyName(), 'id'));
            $query->select($this->db->qn($this->jshopLang->get('name'), 'name'));
            $query->from($this->db->qn($jshopProductLabel->getTableName()));
            $labels = $this->db->setQuery($query)->loadObjectList();
            foreach ($labels as $label) {
                $this->labels[$label->name] = (int)$label->id;
            }
            unset($labels, $label);
        }

        return $this->labels;
    }

    /**
     * @return array|null
     *
     * @since 3.0.0
     */
    private function preloadManufacturers()
    {
        if ($this->manufacturers === null) {
            $this->manufacturers = [];
            $jshopManufacturer = JTable::getInstance('manufacturer', 'jshop');
            $query = $this->db->getQuery(true);
            $query->select($this->db->qn($jshopManufacturer->getKeyName(), 'id'));
            $query->select($this->db->qn($this->jshopLang->get('name'), 'name'));
            $query->from($this->db->qn($jshopManufacturer->getTableName()));
            $manufacturers = $this->db->setQuery($query)->loadObjectList();
            foreach ($manufacturers as $manufacturer) {
                $this->manufacturers[$manufacturer->name] = (int)$manufacturer->id;
            }
            unset($manufacturers, $manufacturer);
        }

        return $this->manufacturers;
    }

    /**
     * @return array|null
     *
     * @since 3.0.0
     */
    private function preloadTaxes()
    {
        if ($this->taxes === null) {
            $this->taxes = [];
            $query = $this->db->getQuery(true);
            $query->select($this->db->qn('tax_id'));
            $query->select($this->db->qn('tax_name'));
            $query->from($this->db->qn('#__jshopping_taxes'));
            $this->taxes = $this->db->setQuery($query)->loadObjectList('tax_name');
        }

        return $this->taxes;
    }

    /**
     * @return array|null
     *
     * @since 3.0.0
     */
    private function preloadUnits()
    {
        if ($this->units === null) {
            $this->units = [];
            $jshopUnit = JTable::getInstance('unit', 'jshop');
            $query = $this->db->getQuery(true);
            $query->select($this->db->qn($jshopUnit->getKeyName(), 'id'));
            $query->select($this->db->qn($this->jshopLang->get('name'), 'name'));
            $query->from($this->db->qn($jshopUnit->getTableName()));
            $units = $this->db->setQuery($query)->loadObjectList();
            foreach ($units as $unit) {
                $this->units[(string)$unit->name] = (int)$unit->id;
            }
            unset($units, $unit);
        }

        return $this->units;
    }

    /**
     * @return array|null
     *
     * @since 3.0.0
     */
    private function preloadVendors()
    {
        if ($this->vendors === null) {
            $this->vendors = [];
            $jshopVendor = JTable::getInstance('vendor', 'jshop');
            $query = $this->db->getQuery(true);
            $query->select($this->db->qn($jshopVendor->getKeyName(), 'id'));
            $query->select($this->db->qn('shop_name', 'name'));
            $query->from($this->db->qn($jshopVendor->getTableName()));
            $vendors = $this->db->setQuery($query)->loadObjectList();
            foreach ($vendors as $vendor) {
                $this->vendors[(string)$vendor->name] = (int)$vendor->id;
            }
            unset($vendors, $vendor);
        }

        return $this->vendors;
    }

    /**
     * @return array|null
     *
     * @since 3.0.0
     */
    private function preloadVideo()
    {
        if ($this->video === null) {
            $this->video = [];
            $jshopProductVideo = JTable::getInstance('productVideo', 'jshop');
            $query = $this->db->getQuery(true);
            $query->select($this->db->qn($jshopProductVideo->getKeyName(), 'id'));
            $query->select($this->db->qn('product_id', 'productId'));
            $query->select($this->db->qn('video_name', 'name'));
            $query->select($this->db->qn('video_code', 'code'));
            $query->from($this->db->qn($jshopProductVideo->getTableName()));
            $videos = $this->db->setQuery($query)->loadObjectList();
            foreach ($videos as $video) {
                if ((string)$video->name) {
                    $this->video[(int)$video->productId][(string)$video->name] = (int)$video->id;
                }
                if ((string)$video->code) {
                    $this->video[(int)$video->productId][(string)$video->code] = (int)$video->id;
                }
            }
            unset($videos, $video);
        }

        return $this->video;
    }

    /**
     * @param string $task
     * @param string $active
     * @param array $query
     * @param string $message
     * @param string $messageType
     *
     * @return $this
     *
     * @since 3.0.0
     */
    private function redirect($task = 'view', $active = null, $query = [], $message = null, $messageType = 'message')
    {
        $app = JFactory::getApplication();
        if ($message) {
            $app->enqueueMessage(JText::_($message), $messageType);
        }
        $query = array_merge([
            'option' => 'com_jshopping',
            'controller' => 'importexport',
            'task' => $task,
        ], $query);
        if ($app instanceof JApplicationAdministrator) {
            $query['ie_id'] = $this->ie_id;
        }
        if ($active === null && $app->input->get('active')) {
            $active = $app->input->get('active');
        }
        $query['active'] = $active;
        $app->redirect('index.php?' . http_build_query($query));

        return $this;
    }

    /**
     * @since 3.0.0
     */
    private function saveExportFile()
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        switch ($this->params->get('extension')) {
            case 'csv':
                $objWriter = new \PhpOffice\PhpSpreadsheet\Writer\Csv($this->spreadsheet);
                $objWriter->setDelimiter($this->params->get('csvDelimiter'));
                $objWriter->setEnclosure($this->params->get('csvEnclosure'));
                $objWriter->save($this->fileNamePath());
                break;
            case 'xls':
                $sheet->setTitle(_JSHOP_EXPORT . ' ' . date('d-m-y H-i-s'));
                $objWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xls($this->spreadsheet);
                $objWriter->save($this->fileNamePath());
                break;
            default:
                $sheet->setTitle(_JSHOP_EXPORT . ' ' . date('d-m-y H-i-s'));
                $objWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($this->spreadsheet);
                $objWriter->save($this->fileNamePath());
                break;
        }
        $this->spreadsheet->disconnectWorksheets();
    }

    /**
     * @param array $file
     *
     * @return string
     *
     * @since 3.0.0
     */
    private function uploadFile($file = null)
    {
        if (!is_array($file)) {
            throw new RuntimeException(JText::_('COM_INSTALLER_MSG_INSTALL_NO_FILE_SELECTED'));
        }
        if (!(bool)ini_get('file_uploads')) {
            throw new RuntimeException(JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLFILE'));
        }
        if ($file['error'] && ($file['error'] == UPLOAD_ERR_NO_TMP_DIR)) {
            throw new RuntimeException(JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR') . '<br />' . JText::_('COM_INSTALLER_MSG_WARNINGS_PHPUPLOADNOTSET'));
        }
        if ($file['error'] && ($file['error'] == UPLOAD_ERR_INI_SIZE)) {
            throw new RuntimeException(JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR') . '<br />' . JText::_('COM_INSTALLER_MSG_WARNINGS_SMALLUPLOADSIZE'));
        }
        if ($file['error'] || $file['size'] < 1) {
            throw new RuntimeException(JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR'));
        }
        $fileName = JUri::getInstance()->getHost() . '_' . date('Y.m.d.H.i.s') . '_import';
		$fileExt = JFile::getExt($file['name']);
        JFile::upload($file['tmp_name'], $this->jshopConfig->get('importexport_path') . $this->alias . '/' . $fileName . '.' . $fileExt);

        return $fileName . '.' . $fileExt;
    }

    private function initImageData($reset = false) {
		if ($reset) {
			$this->imageData = array();
		} else {
			$this->imageData = JFactory::getSession()->get('imageData', array(), 'comielExport');
		}
	}

    private function storeImageData($type, $id) {
		if (!$id) {
			return;
		}
		if (!isset($this->imageData[$type])) {
			$this->imageData[$type] = array();
		}
		$this->imageData[$type][$id] = $id;
		JFactory::getSession()->set('imageData', $this->imageData, 'comielExport');
	}

    private function clearImageData() {
		$this->imageData = null;
		JFactory::getSession()->clear('imageData', 'comielExport');
	}
}

include_once(__DIR__ . '/PhpOffice/PhpSpreadsheet/Reader/IReadFilter.php');

class chunkReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    private $_endRow = 0;
    private $_startRow = 0;

    public function readCell($column, $row, $worksheetName = '')
    {
        $row = (int)$row;
        if ($row < $this->_startRow) {
            return null;
        }
        if ($row >= $this->_endRow) {
            return false;
        }
        if ($row >= $this->_startRow && $row < $this->_endRow) {
            return true;
        }

        return false;
    }

    public function setRows($startRow, $chunkSize)
    {
        $this->_startRow = $startRow;
        $this->_endRow = $startRow + $chunkSize;
    }
}
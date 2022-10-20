<?php
/**
 * @package MJ Simple News
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2013 MicroJoom. All Rights Reserved.
 * @author MicroJoom http://www.microjoom.com
 * 
 */
defined('_JEXEC') or die;

require_once dirname(__FILE__).'/helpers/helper.php';

$cacheid = md5(serialize(array ($module, $params)));
$cacheparams = new stdClass;
$cacheparams->cachemode = 'id';
$cacheparams->class = 'MJSimpleNewsHelper';
$cacheparams->method = 'getList';
$cacheparams->methodparams = $params;
$cacheparams->modeparams = $cacheid;
$list = JModuleHelper::moduleCache ($module, $params, $cacheparams);
$layout = $params->get('layout', 'default');
require  JModuleHelper::getLayoutPath($module->module,$layout);

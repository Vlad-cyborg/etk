<?php
defined('_JEXEC') or die;



setcookie('modal',true,time()+260);


require JModuleHelper::getLayoutPath('mod_modal', $params->get('layout', 'default'));
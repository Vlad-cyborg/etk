<?php
define('JPATH_JOOMSHOPPING', JPATH_ROOT.'/components/com_jshopping');

JTable::addIncludePath(JPATH_JOOMSHOPPING.'/tables');
JLoader::discover('JshopHelpers', JPATH_JOOMSHOPPING."/helpers");
include_once(JPATH_JOOMSHOPPING."/tables/multilang.php");
include_once(JPATH_JOOMSHOPPING."/lib/jtableauto.php");
include_once(JPATH_JOOMSHOPPING."/lib/multilangfield.php");
include_once(JPATH_JOOMSHOPPING."/tables/config.php");
require_once(JPATH_JOOMSHOPPING."/lib/functions.php");
require_once(JPATH_JOOMSHOPPING."/lib/shop_item_menu.php");
require_once(JPATH_JOOMSHOPPING."/models/base.php");
include_once(JPATH_JOOMSHOPPING."/payments/payment.php");
include_once(JPATH_JOOMSHOPPING."/shippingform/shippingform.php");
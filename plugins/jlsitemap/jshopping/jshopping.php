<?php
/**
 * @package    Joomla
 * @subpackage JLSitemap
 * @author     Nevigen.com
 * @website    https://nevigen.com/
 * @email      support@nevigen.com
 * @copyright  Copyright © Nevigen.com. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Registry\Registry;

require_once JPATH_SITE . '/components/com_jshopping/lib/factory.php';

class plgJLSitemapJshopping extends CMSPlugin
{

	protected $autoloadLanguage = true;

	/**
	 * @param array    $urls
	 * @param Registry $config
	 *
	 * @return array
	 * @since 1.0
	 */
	public function onGetUrls(&$urls, $config)
	{
		$changefreq = $config->get('changefreq', 'weekly');
		$priority = $config->get('priority', '0.5');
		$multilang = $config->get('multilanguage');
		$access = $this->params->get('access', 1);
		$hide_product_not_avaible_stock = $this->params->get('hide_product_not_avaible_stock', 0);
		$defaultItemid = getDefaultItemid();
		$db = Factory::getDbo();

		$defaultLanguage = ComponentHelper::getParams('com_languages')->get('site', 'en-GB');
		if ($multilang)
		{
			$db->setQuery(
				$db->getQuery(true)
					->select('language')
					->from($db->qn('#__jshopping_languages'))
					->where('publish = 1')
			);
			$languages = $db->loadColumn();
		}
		else
		{
			$languages = array($defaultLanguage);
		}

		if ($this->params->get('products', 1))
		{
			$changefreq_products = $this->params->get('changefreq_products', $changefreq);
			$priority_products = $this->params->get('priority_products', $priority);
			$q = $db->getQuery(true)
					->select('prod.product_id, prod.product_publish, prod.access, prod.date_modify, prod.product_quantity, cat.category_id, cat.category_publish, cat.access as cat_access')
					->from($db->qn('#__jshopping_products', 'prod'))
					->innerJoin($db->qn('#__jshopping_products_to_categories', 'pr_cat') . ' ON pr_cat.product_id = prod.product_id')
					->leftJoin($db->qn('#__jshopping_categories', 'cat') . ' ON pr_cat.category_id = cat.category_id')
					->where('prod.parent_id = 0')
					->group('prod.product_id');
			foreach ($languages as $lang)
			{
				$q->select($db->qn('prod.name_' . $lang));
			}
			$db->setQuery($q);

			foreach ($db->loadObjectList() as $row)
			{
				$exclude  = array();
				if (!$row->product_publish)
				{
					$exclude[] = array(
						'type' => Text::_('PLG_JLSITEMAP_JSHOPPING_EXCLUDE_PRODUCT'),
						'msg'  => Text::_('PLG_JLSITEMAP_JSHOPPING_EXCLUDE_PRODUCT_PUBLISH')
					);
				}
				if ($hide_product_not_avaible_stock && $row->product_quantity <= 0)
				{
					$exclude[] = array(
						'type' => Text::_('PLG_JLSITEMAP_JSHOPPING_EXCLUDE_PRODUCT'),
						'msg'  => Text::_('PLG_JLSITEMAP_JSHOPPING_EXCLUDE_PRODUCT_QUANTITY')
					);
				}
				if ($row->access != $access)
				{
					$exclude[] = array(
						'type' => Text::_('PLG_JLSITEMAP_JSHOPPING_EXCLUDE_PRODUCT'),
						'msg'  => Text::_('PLG_JLSITEMAP_JSHOPPING_EXCLUDE_PRODUCT_ACCESS')
					);
				}
				if (!$row->category_publish)
				{
					$exclude[] = array(
						'type' => Text::_('PLG_JLSITEMAP_JSHOPPING_EXCLUDE_CATEGORY'),
						'msg'  => Text::_('PLG_JLSITEMAP_JSHOPPING_EXCLUDE_CATEGORY_PUBLISH')
					);
				}
				if ($row->cat_access != $access)
				{
					$exclude[] = array(
						'type' => Text::_('PLG_JLSITEMAP_JSHOPPING_EXCLUDE_CATEGORY'),
						'msg'  => Text::_('PLG_JLSITEMAP_JSHOPPING_EXCLUDE_CATEGORY_ACCESS')
					);
				}
				foreach ($languages as $lang)
				{
					$url = new stdClass();
					$url->type = Text::_('PLG_JLSITEMAP_JSHOPPING_TYPES_PRODUCT');
					$url->title = $row->{'name_' . $lang};
					$url->loc = 'index.php?option=com_jshopping&controller=product&task=view&category_id=' . $row->category_id . '&product_id=' . $row->product_id . '&Itemid=' . $defaultItemid;
					if ($multilang)
					{
						$url->alternates = array();
						foreach ($languages as $tag)
						{
							$url->alternates[$tag] = $url->loc . '&lang=' . $tag;
						}
						$url->loc .= '&lang=' . $lang;
					}
					else
					{
						$url->alternates = false;
					}
					$url->changefreq = $changefreq_products;
					$url->priority = $priority_products;
					$url->lastmod = $row->date_modify;
					$url->exclude = $exclude ? $exclude : false;

					$urls[] = $url;
				}
			}
		}

		if ($this->params->get('categories', 1))
		{
			$changefreq_categories = $this->params->get('changefreq_categories', $changefreq);
			$priority_categories   = $this->params->get('priority_categories', $priority);
			$q = $db->getQuery(true)
					->select('category_id, category_publish, access')
					->from($db->qn('#__jshopping_categories'));
			foreach ($languages as $lang)
			{
				$q->select($db->qn('name_' . $lang));
			}
			$db->setQuery($q);

			foreach ($db->loadObjectList() as $row)
			{
				$exclude  = array();
				if (!$row->category_publish)
				{
					$exclude[] = array(
						'type' => Text::_('PLG_JLSITEMAP_JSHOPPING_EXCLUDE_CATEGORY'),
						'msg'  => Text::_('PLG_JLSITEMAP_JSHOPPING_EXCLUDE_CATEGORY_PUBLISH')
					);
				}
				if ($row->access != $access)
				{
					$exclude[] = array(
						'type' => Text::_('PLG_JLSITEMAP_JSHOPPING_EXCLUDE_CATEGORY'),
						'msg'  => Text::_('PLG_JLSITEMAP_JSHOPPING_EXCLUDE_CATEGORY_ACCESS')
					);
				}
				foreach ($languages as $lang)
				{
					$url = new stdClass();
					$url->type = Text::_('PLG_JLSITEMAP_JSHOPPING_TYPES_CATEGORY');
					$url->title = $row->{'name_' . $lang};
					$url->loc = 'index.php?option=com_jshopping&controller=category&task=view&category_id=' . $row->category_id . '&Itemid=' . $defaultItemid;
					if ($multilang)
					{
						$url->alternates = array();
						foreach ($languages as $tag)
						{
							$url->alternates[$tag] = $url->loc . '&lang=' . $tag;
						}
						$url->loc .= '&lang=' . $lang;
					}
					else
					{
						$url->alternates = false;
					}
					$url->changefreq = $changefreq_categories;
					$url->priority = $priority_categories;
					$url->exclude = $exclude ? $exclude : false;

					$urls[] = $url;
				}
			}
		}

		if ($this->params->get('manufacturers', 1))
		{
			$changefreq_manufacturers = $this->params->get('changefreq_manufacturers', $changefreq);
			$priority_manufacturers   = $this->params->get('priority_manufacturers', $priority);
			$q = $db->getQuery(true)
					->select('manufacturer_id, manufacturer_publish')
					->from($db->qn('#__jshopping_manufacturers'));
			foreach ($languages as $lang)
			{
				$q->select($db->qn('name_' . $lang));
			}
			$db->setQuery($q);

			foreach ($db->loadObjectList() as $row)
			{
				$exclude  = array();
				if (!$row->manufacturer_publish)
				{
					$exclude[] = array(
						'type' => Text::_('PLG_JLSITEMAP_JSHOPPING_EXCLUDE_MANUFACTURER'),
						'msg'  => Text::_('PLG_JLSITEMAP_JSHOPPING_EXCLUDE_MANUFACTURER_PUBLISH')
					);
				}
				foreach ($languages as $lang)
				{
					$url = new stdClass();
					$url->type = Text::_('PLG_JLSITEMAP_JSHOPPING_TYPES_MANUFACTURER');
					$url->title = $row->{'name_' . $lang};
					$url->loc = 'index.php?option=com_jshopping&controller=manufacturer&task=view&manufacturer_id=' . $row->manufacturer_id . '&Itemid=' . $defaultItemid;
					if ($multilang)
					{
						$url->alternates = array();
						foreach ($languages as $tag)
						{
							$url->alternates[$tag] = $url->loc . '&lang=' . $tag;
						}
						$url->loc .= '&lang=' . $lang;
					}
					else
					{
						$url->alternates = false;
					}
					$url->changefreq = $changefreq_manufacturers;
					$url->priority = $priority_manufacturers;
					$url->exclude = $exclude ? $exclude : false;

					$urls[] = $url;
				}
			}
		}

		if ($this->params->get('vendors', 0))
		{
			$changefreq_vendors = $this->params->get('changefreq_vendors', $changefreq);
			$priority_vendors   = $this->params->get('priority_vendors', $priority);
			$db->setQuery(
				$db->getQuery(true)
					->select('id, publish, shop_name')
					->from($db->qn('#__jshopping_vendors'))
			);

			foreach ($db->loadObjectList() as $row)
			{
				$exclude  = array();
				if (!$row->publish)
				{
					$exclude[] = array(
						'type' => Text::_('PLG_JLSITEMAP_JSHOPPING_EXCLUDE_VENDOR'),
						'msg'  => Text::_('PLG_JLSITEMAP_JSHOPPING_EXCLUDE_VENDOR_PUBLISH')
					);
				}
				foreach ($languages as $lang)
				{
					$url = new stdClass();
					$url->type = Text::_('PLG_JLSITEMAP_JSHOPPING_TYPES_VENDOR');
					$url->title = $row->shop_name;
					$url->loc = 'index.php?option=com_jshopping&controller=vendor&task=info&vendor_id=' . $row->id . '&Itemid=' . $defaultItemid;
					if ($multilang)
					{
						$url->alternates = array();
						foreach ($languages as $tag)
						{
							$url->alternates[$tag] = $url->loc . '&lang=' . $tag;
						}
						$url->loc .= '&lang=' . $lang;
					}
					else
					{
						$url->alternates = false;
					}
					$url->changefreq = $changefreq_vendors;
					$url->priority = $priority_vendors;
					$url->exclude = $exclude ? $exclude : false;

					$urls[] = $url;
				}
			}
		}

		return $urls;
	}

}
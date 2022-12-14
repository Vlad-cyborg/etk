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

$com_path = JPATH_SITE.'/components/com_content/';
require_once $com_path.'router.php';
require_once $com_path.'helpers/route.php';
require_once dirname(__FILE__).'/abstract.php';
if(!class_exists('JModelLegacy')){
	class JModelLegacy extends JModel{}
}

JModelLegacy::addIncludePath($com_path . '/models', 'ContentModel');

/**
 * Helper for mod_mj_simple_news
 *
 * @package     MicroJoom
 * @subpackage  mod_mj_simple_news
 */
abstract class MJSimpleNewsHelper extends MJ_Helper_Abstract{
	public static function getList( &$params ){
		$list = array();
		// Display options
		$show_created 			= $params->get('item_created_display', 0);
		$show_hits 				= $params->get('item_hits_display', 1);
		$show_tag 				= $params->get('item_tags_display', 0);
		$show_description		= $params->get('item_desc_display', 1);
		$maxlength_desc 		= $params->get('item_desc_maxlength', 200);
		$show_title				= $params->get('title_display');
		$maxlength_title		= $params->get('item_title_maxlength',25);
		
		$articles = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
		$db =  JFactory::getDbo();
		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$articles->setState('params', $appParams);

		// Set the filters based on the module params
		$articles->setState('list.start', 0);
		$articles->setState('list.limit', (int)$params->get('count', 0));
		
		$articles->setState('filter.published', 1);

		// Access filter
		$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$articles->setState('filter.access', $access);
		$catids = $params->get('catid');
		
		// Category filter
		if ($catids){
			if ($params->get('show_child_category_articles', 0) && (int) $params->get('levels', 0) > 0){
				// Get an instance of the generic categories model
				$categories = JModelLegacy::getInstance('Categories', 'ContentModel', array('ignore_request' => true));
				$categories->setState('params', $appParams);
				$levels = $params->get('levels', 1) ? $params->get('levels', 1) : 9999;
				$categories->setState('filter.get_children', $levels);
				$categories->setState('filter.published', 1);
				$categories->setState('filter.access', $access);
				$additional_catids = array();
				foreach ($catids as $catid){
					$categories->setState('filter.parentId', $catid);
					$recursive = true;
					$items = $categories->getItems($recursive);
					if ($items){
						foreach ($items as $category){
							$condition = (($category->level - $categories->getParent()->level) <= $levels);
							if ($condition){
								$additional_catids[] = $category->id;
							}
						}
					}
				}
				$catids = array_unique(array_merge($catids, $additional_catids));
			}
			$articles->setState('filter.category_id', $catids);
		}

		// Ordering
		$articles->setState('list.ordering', $params->get('article_ordering', 'a.ordering'));
		$articles->setState('list.direction', $params->get('article_ordering_direction', 'ASC'));

		// New Parameters
		$articles->setState('filter.featured', $params->get('show_front', 'show'));

		// Filter by language
		$articles->setState('filter.language', $app->getLanguageFilter());
		// Prepare data for display using display options
		$items = $articles->getItems(); 
		if( !empty($items) ){
			foreach ($items as &$item) {
				$item->slug = $item->id.':'.$item->alias;
				$item->catslug = $item->catid ? $item->catid .':'.$item->category_alias : $item->catid;
				if ($access || in_array($item->access, $authorised)){
					// We know that user has the privilege to view the article
					$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
				}else {
					$app  = JFactory::getApplication();
					$menu = $app->getMenu();
					$menuitems = $menu->getItems('link', 'index.php?option=com_users&view=login');
					if (isset($menuitems[0])){
						$Itemid = $menuitems[0]->id;
					}elseif ($app->input->getInt('Itemid') > 0){
						// Use Itemid from requesting page only if there is no existing menu
						$Itemid = $app->input->getInt('Itemid');
					}
					$item->link = JRoute::_('index.php?option=com_users&view=login&Itemid='.$Itemid);
				}
				$item->_hits = ($show_hits)?(int)$item->hits:'';
				$item->_created = ($show_created)?$item->created:'';
				$item->_introtext = self::_cleanText($item->introtext);
				$item->_description = (self::_trimEncode($item->_introtext) != '' && $show_description  ) ? self::truncate($item->_introtext, $maxlength_desc):'';
				$item->tags = '';
				if($show_tag && class_exists('JHelperTags')){
					$tagLayout = new JLayoutFile('joomla.content.tags');
					$tags = new JHelperTags;
					$tags->getItemTags('com_content.article', $item->id);
					$item->tags = $tagLayout->render($tags->itemTags);
				}
				$item->sub_title = ($show_title) ? self::truncate($item->title, $maxlength_title) : '';
				$image = self::createThumbs($item, $params, 'imgf');
				$attr = ' ';
				$attr .= isset($image['title'])? ' title = "'.$image['title'].'"':'';
				$attr .= isset($image['alt'])?' alt = "'.$image['alt'].'"':'';
				$attr .= isset($image['class'])?' class = "'.$image['class'].'"':'';
				$item->image_src = isset($image['src'])?$image['src']:'';
				$item->image_attr = $attr;
				$item->link_target = self::linkTarget($params->get('link_target'));
				$list[] =  $item;
			}
			
		}
		return $list;
	}
}



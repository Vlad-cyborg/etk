<?php
/**
 * @version 1.0.1 from Arkadiy Sedelnikov
 * @copyright Copyright (C) 2012 Arkadiy Sedelnikov. All rights reserved.
 * @license GNU General Public License version 2 or later;
 */
 
// no direct access
defined('_JEXEC') or die;

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

class plgContentJsproductsnapshots extends JPlugin
{
    public function onContentBeforeDisplay($context, &$article, &$params, $page = 0)
    {
        // Don't run this plugin when the content is being indexed
        if ($context == 'com_finder.indexer') {
            return true;
        }

        $param_defaults = array('id' => '0',
            'enabled' => $this->params->get('enabled',''),
            'showname' => $this->params->get('showname',''),
            'showimage' => $this->params->get('showimage',''),
            'showdesc' => $this->params->get('showdesc',''),
            'showprice' => $this->params->get('showprice',''),
            'showaddtocart' => $this->params->get('showaddtocart',''),
            'displaylist' => $this->params->get('displaylist',''),
            'displayeach' => $this->params->get('displayeach',''),
            'width' => $this->params->get('width',''),
            'border' => '0',
            'to_one_line' => $this->params->get('to_one_line',''),
            'style' => '');


        // get settings from admin mambot parameters
        foreach ($param_defaults as $key => $value) {
            $param_defaults[$key] = $params->get($key, $value);
		}
        $object = (!empty($article->text)) ? 'text' : 'introtext';
        $enabled = $param_defaults['enabled'];
        if (!$enabled) {
            $article->$object = preg_replace("/{product_snapshot:.+?}/", '', $article->$object);
            return true;
        }

        $jsproductsnap_entrytext = $article->$object;

        $jsproductsnap_matches = array();

        if (preg_match_all("/{product_snapshot:.+?}/", $jsproductsnap_entrytext, $jsproductsnap_matches, PREG_PATTERN_ORDER) > 0)
        {
            foreach ($jsproductsnap_matches[0] as $jsproductsnap_match) {
                $jsproductsnap_match = str_replace("{product_snapshot:", "", $jsproductsnap_match);
                $jsproductsnap_match = str_replace("}", "", $jsproductsnap_match);

                // Get Bot Parameters
                $jsproductsnap_params = $this->get_prodsnap_params($jsproductsnap_match, $param_defaults);

                // Get the html
                $showsnapshot = $this->return_snapshot($jsproductsnap_params);

                $jsproductsnap_entrytext = preg_replace("/{product_snapshot:.+?}/", $showsnapshot, $jsproductsnap_entrytext, 1);
            }
            $article->$object = $jsproductsnap_entrytext;

        }
        return;
    }

    private function get_prodsnap_params($jsproductsnap_match, $param_defaults)
    {
        $params = explode(",", $jsproductsnap_match);
        foreach ($params as $param) {
            $param = explode("=", $param);
            if (isset($param_defaults[$param[0]])) {
                $param_defaults[$param[0]] = $param[1];
            }
        }
        $param_defaults['id'] = "'" . str_replace("|", "','", $param_defaults['id']) . "'";
        return $param_defaults;
    }

    function return_snapshot(&$params)
    {



        if (!file_exists(JPATH_SITE.DS.'components'.DS.'com_jshopping'.DS.'jshopping.php')){
            JError::raiseError(500,"Please install component \"joomshopping\"");
        }

        require_once (JPATH_SITE.DS.'components'.DS.'com_jshopping'.DS."lib".DS."factory.php");
        require_once (JPATH_SITE.DS.'components'.DS.'com_jshopping'.DS."lib".DS."functions.php");

        JModelLegacy::addIncludePath(JPATH_SITE.DS.'components'.DS.'com_jshopping'.DS.'models');
        JTable::addIncludePath(JPATH_SITE.DS.'components'.DS.'com_jshopping'.DS.'tables');

        $html = "";

        $products = $this->getProducts($params['id']);

        $ordering = explode(",", str_replace("'", "", $params['id']));

        $product_count = count($products);

            $listClass = ($params['displaylist'] == 'v') ? 'vertical' : 'horizontal';
            $productClass = ($params['displayeach'] == 'v') ? 'vertical' : 'horizontal';
            $width = ' style="width:'.$params['width'].'"';

        if ($product_count > 0) {
            $document = JFactory::getDocument();
            $document->addStyleSheet('/plugins/content/jsproductsnapshots/assets/style.css');

            $html .= "<div class='jspss_products'$width>\n";

            $desc_width = $product_width = '';
            if($params['displaylist'] != 'v'){
                if(strpos($width, '%') !== false){
                    $product_width = ' style="width:'.floor(str_replace('%', '', $params['width'])/$params['to_one_line']).'%"';
                }
                else{
                    $product_width = ' style="width:'.floor(str_replace('px', '', $params['width'])/$params['to_one_line']).'px"';
                }
            }
            else if($params['displayeach'] != 'v'){
                $desc_width = ' style="width:50%"';
            }

            foreach ($ordering as $k=>$v) {

                $html .= "<div class='jspss_product $listClass'$product_width>\n";

                if(!isset($products[$v])) continue;

                $product = $products[$v];

                if ('y' == $params['showname']) {

                    $html .= "<div class=\"jspss_product_name $productClass\">" . $product->name . "</div>\n";

                }
				
                if ('y' == $params['showimage']) {
                    $product_link = SEFLink('index.php?option=com_jshopping&controller=product&task=view&category_id=' . $product->category_id.'&product_id=' . $product->product_id ,1);

                    $html .= "<a class='jspss_product_link $productClass' href=\"" . $product_link . "\">";
                    $html .= "<img class='jspss_product_img $productClass' alt=\"" . $product->name . "\" hspace=\"7\" src=\"" . $product->image . "\" />";
                    $html .= "</a>\n";

                }
                if ('y' == $params['showdesc']) {
                    $html .= "<div class=\"jspss_desc $productClass\"$desc_width>" . $product->short_description . "</div>\n";
                }
                if ('y' == $params['showprice']) {
                    $html .= "<div class=\"jspss_price $productClass\">" . formatprice($product->product_price) . "</div>\n";
                }
                if ('y' == $params['showaddtocart']) {
                    $cartLink = SEFLink('index.php?option=com_jshopping&controller=cart&task=add&category_id=' . $product->category_id.'&product_id=' . $product->product_id ,1);
                    $html .= "<div class=\"jspss_addtocart $productClass\">";

                    $html .= "<a href=\"".$cartLink."\">В корзину</a>\n";

                    $html .= "</div>\n";
                }
                $html .= "</div>\n";
            }
            $html .= "</div>";
            return ($html);
        } else {
            echo 'Product not found';
            return ("");
        }
    }

    function getProducts($ids, $array_categories = null){

        $db =& JFactory::getDBO();

        $adv_query = "";
        $adv_from = "";
        $lang = &JSFactory::getLang();
        $adv_result = " prod.product_id,
                        pr_cat.category_id, prod.`".$lang->get('name')."` as name,
                        prod.`".$lang->get('short_description')."` as short_description,
                        prod.product_ean,
                        prod.image,
                        prod.product_price,
                        prod.currency_id,
                        prod.product_tax_id as tax_id,
                        prod.product_old_price,
                        prod.product_weight,
                        prod.average_rating,
                        prod.reviews_count,
                        prod.hits,
                        prod.weight_volume_units,
                        prod.basic_price_unit_id,
                        prod.label_id,
                        prod.product_manufacturer_id,
                        prod.min_price,
                        prod.product_quantity,
                        prod.different_prices
                        ";

        $jshopProduct = &JTable::getInstance('product', 'jshop');

        $order_query = '';

		$jshopProduct->getBuildQueryListProductSimpleList("tophits", null, $array_categories, $adv_query, $adv_from, $adv_result);

        JPluginHelper::importPlugin('jshoppingproducts');
        $dispatcher =& JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeQueryGetProductList', array("top_hits_products", &$adv_result, &$adv_from, &$adv_query, &$order_query));

        $query = "SELECT $adv_result FROM `#__jshopping_products` AS prod
                  INNER JOIN `#__jshopping_products_to_categories` AS pr_cat ON pr_cat.product_id = prod.product_id
                  LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id = cat.category_id
                  $adv_from
                  WHERE prod.product_publish = '1' AND prod.product_id IN ($ids) AND cat.category_publish='1' ".$adv_query."
                  GROUP BY prod.product_id ";
        $db->setQuery($query);
        $products = $db->loadObjectList('product_id');
        $products = listProductUpdateData($products);

        return $products;
    }
}

?>
<?php	 	
defined('_JEXEC') or die('Restricted access');
error_reporting(E_ALL & ~E_NOTICE);

class modODJShoppingCategoryHelper{
	public static function getTreeCats($category, $params, $active_id, $parent_id = 0, $deep = 1){
        $jshopConfig = JSFactory::getConfig();
		
		$rows  = $category->getSubCategories($parent_id, $params->get('category_sort', 'id'), $params->get('sort_order', 'asc'), 1);
		$odcatarr = array();
		if(count($rows))
		foreach($rows as $row) {
			$child = $category->getSubCategories($row->category_id, $params->get('category_sort', 'id'), $params->get('sort_order', 'asc'), 1);
			// Show counter
			if($params->get('counter', 0)) {
				$category->category_id = $row->category_id;
				$counter = $category->getCountProducts('');
			}

			$odcatarr[] = array(
				'NAME' => $row->name, 
				'LINK' => $row->category_link, 
				'SELECTED' => in_array($row->category_id, $active_id), 
				'ACTIVE' => $row->category_publish, 
				'COUNT' => $counter, 
				'IS_PARENT' => count($child) ? true : false, 
				'DEPTH' => $deep, 
				'IMG' => $row->category_image,
				);

				// Show child
			if(count($child)) {
				$deep++;
				$odcatarr = array_merge($odcatarr, modODJShoppingCategoryHelper::getTreeCats($category, $params, $active_id, $row->category_id, $deep));
				$deep--;
			}
		}
		
		return $odcatarr;
    }
    
    public static function getCatsArray($params, $active_id, $category)
	{
	    $category->load($active_id);
    	$categories_id = $category->getTreeParentCategories();
	   return modODJShoppingCategoryHelper::getTreeCats($category, $params, $categories_id);
    }
	
}
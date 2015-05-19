<?php
/*------------------------------------------------------------------------
 # mod_j2store_products - J2Store
 # ------------------------------------------------------------------------
 # author    ThemeParrot - ThemeParrot http://www.ThemeParrot.com
 # copyright Copyright (C) 2014 ThemeParrot.com. All Rights Reserved.
 # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 # Websites: http://ThemeParrot.com
 # Based on Latest Articles module of Joomla
 -------------------------------------------------------------------------*/

defined('_JEXEC') or die;

$com_path = JPATH_SITE.'/components/com_content/';
require_once $com_path.'router.php';
require_once $com_path.'helpers/route.php';

$j2store_path = JPATH_ADMINISTRATOR.'/components/com_j2store/j2store.php';
if(JFile::exists($j2store_path)) {
	require_once (JPATH_SITE.'/components/com_j2store/helpers/cart.php');
	require_once (JPATH_ADMINISTRATOR.'/components/com_j2store/library/prices.php');
	require_once (JPATH_ADMINISTRATOR.'/components/com_j2store/library/tax.php');
}

JModelLegacy::addIncludePath($com_path . '/models', 'ContentModel');

/**
 * Helper for mod_articles_category
 *
 * @package     Joomla.Site
 * @subpackage  mod_articles_category
 */
abstract class ModJ2StoreProductsHelper
{
	public static function getList(&$params)
	{

		$source = $params->get('product_source', 'category');
		$itams = array();

		switch($source) {

			case 'item':

					$ids = $params->get('items_list', '');
					$ids = explode(",", $ids);
					if($ids) {
						// Get an instance of the generic articles model
						$model = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
						$app = JFactory::getApplication();
						$appParams = $app->getParams();
						$model->setState('params', $appParams);
						$model->setState('filter.published', 1);
						// Access filter
						$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
						$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
						$model->setState('filter.access', $access);

						$model->setState('filter.article_id', $ids);
						$model->setState('filter.article_id.include', true); // include
						$items = $model->getItems();
					}
			break;
			case 'category':
			default:
				$articles = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
					// Set application parameters in model
					$app = JFactory::getApplication();
					$appParams = $app->getParams();
					$articles->setState('params', $appParams);

					// Set the filters based on the module params
					$articles->setState('list.start', 0);
					$articles->setState('list.limit', (int) $params->get('count', 0));
					$articles->setState('filter.published', 1);

					// Access filter
					$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
					$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
					$articles->setState('filter.access', $access);

					//get category ids
					$catids = $params->get('catid');
					$articles->setState('filter.category_id.include', (bool) $params->get('category_filtering_type', 1));

					// Category filter
					if ($catids)
					{
						if ($params->get('show_child_category_articles', 0) && (int) $params->get('levels', 0) > 0)
						{
							// Get an instance of the generic categories model
							$categories = JModelLegacy::getInstance('Categories', 'ContentModel', array('ignore_request' => true));
							$categories->setState('params', $appParams);
							$levels = $params->get('levels', 1) ? $params->get('levels', 1) : 9999;
							$categories->setState('filter.get_children', $levels);
							$categories->setState('filter.published', 1);
							$categories->setState('filter.access', $access);
							$additional_catids = array();

							foreach ($catids as $catid)
							{
								$categories->setState('filter.parentId', $catid);
								$recursive = true;
								$items = $categories->getItems($recursive);

								if ($items)
								{
									foreach ($items as $category)
									{
										$condition = (($category->level - $categories->getParent()->level) <= $levels);
										if ($condition)
										{
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
					$articles->setState('filter.author_id', $params->get('created_by', ""));
					$articles->setState('filter.author_id.include', $params->get('author_filtering_type', 1));
					$articles->setState('filter.author_alias', $params->get('created_by_alias', ""));
					$articles->setState('filter.author_alias.include', $params->get('author_alias_filtering_type', 1));
					$excluded_articles = $params->get('excluded_articles', '');

					if ($excluded_articles)
					{
						$excluded_articles = explode("\r\n", $excluded_articles);
						$articles->setState('filter.article_id', $excluded_articles);
						$articles->setState('filter.article_id.include', false); // Exclude
					}

					$date_filtering = $params->get('date_filtering', 'off');
					if ($date_filtering !== 'off')
					{
						$articles->setState('filter.date_filtering', $date_filtering);
						$articles->setState('filter.date_field', $params->get('date_field', 'a.created'));
						$articles->setState('filter.start_date_range', $params->get('start_date_range', '1000-01-01 00:00:00'));
						$articles->setState('filter.end_date_range', $params->get('end_date_range', '9999-12-31 23:59:59'));
						$articles->setState('filter.relative_date', $params->get('relative_date', 30));
					}

					// Filter by language
					$articles->setState('filter.language', $app->getLanguageFilter());

					$items = $articles->getItems();
				break;
		}


		// Display options
		$show_date = $params->get('show_date', 0);
		$show_date_field = $params->get('show_date_field', 'created');
		$show_date_format = $params->get('show_date_format', 'Y-m-d H:i:s');
		$show_category = $params->get('show_category', 0);
		$show_hits = $params->get('show_hits', 0);
		$show_author = $params->get('show_author', 0);
		$show_introtext = $params->get('show_introtext', 0);
		$introtext_limit = $params->get('introtext_limit', 100);

		// Find current Article ID if on an article page
		$option = $app->input->get('option');
		$view = $app->input->get('view');

		if ($option === 'com_content' && $view === 'article')
		{
			$active_article_id = $app->input->getInt('id');
		}
		else
		{
			$active_article_id = 0;
		}
		$list = array();
		// Prepare data for display using display options
		foreach ($items as $item)
		{
			$price = J2StorePrices::getJ2Product($item->id);
			if(isset($price)) {

				$item->slug = $item->id.':'.$item->alias;
				$item->catslug = $item->catid ? $item->catid .':'.$item->category_alias : $item->catid;

				if ($access || in_array($item->access, $authorised))
				{
					// We know that user has the privilege to view the article
					$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
				}
				else
				{
					$app  = JFactory::getApplication();
					$menu = $app->getMenu();
					$menuitems = $menu->getItems('link', 'index.php?option=com_users&view=login');
					if (isset($menuitems[0]))
					{
						$Itemid = $menuitems[0]->id;
					}
					elseif ($app->input->getInt('Itemid') > 0)
					{
						// Use Itemid from requesting page only if there is no existing menu
						$Itemid = $app->input->getInt('Itemid');
					}

					$item->link = JRoute::_('index.php?option=com_users&view=login&Itemid='.$Itemid);
				}

				// Used for styling the active article
				$item->active = $item->id == $active_article_id ? 'active' : '';

				$item->displayDate = '';
				if ($show_date)
				{
					$item->displayDate = JHTML::_('date', $item->$show_date_field, $show_date_format);
				}

				if ($item->catid)
				{
					$item->displayCategoryLink = JRoute::_(ContentHelperRoute::getCategoryRoute($item->catid));
					$item->displayCategoryTitle = $item->category_title;
				}
				else {
					$item->displayCategoryTitle = $show_category ? $item->category_title : '';
				}

				$item->displayHits = $show_hits ? $item->hits : '';
				$item->displayAuthorName = $show_author ? $item->author : '';
				if ($show_introtext)
				{
					$item->introtext = JHtml::_('content.prepare', $item->introtext, '', 'mod_articles_category.content');
					$item->introtext = self::_cleanIntrotext($item->introtext);
				}
				if($params->get('introtext_count') > 0 ) {
					$item->displayIntrotext = self::truncate($item->introtext, $introtext_limit);
				} else {
					$item->displayIntrotext = $item->intro_text;
				}
				$item->displayReadmore = $item->alternative_readmore;

				//get j2store options
				$item->j2store = new JObject();
				self::getJ2StoreData($item);
			$list[] = $item;
			}

		}
		return $list;
	}

	public static function _cleanIntrotext($introtext)
	{
		$introtext = str_replace('<p>', ' ', $introtext);
		$introtext = str_replace('</p>', ' ', $introtext);
		$introtext = strip_tags($introtext, '<a><em><strong>');

		$introtext = trim($introtext);

		return $introtext;
	}

	/**
	* Method to truncate introtext
	*
	* The goal is to get the proper length plain text string with as much of
	* the html intact as possible with all tags properly closed.
	*
	* @param string   $html       The content of the introtext to be truncated
	* @param integer  $maxLength  The maximum number of charactes to render
	*
	* @return  string  The truncated string
	*/
	public static function truncate($html, $maxLength = 0)
	{
		$baseLength = strlen($html);
		$diffLength = 0;

		// First get the plain text string. This is the rendered text we want to end up with.
		$ptString = JHtml::_('string.truncate', $html, $maxLength, $noSplit = true, $allowHtml = false);

		for ($maxLength; $maxLength < $baseLength;)
		{
			// Now get the string if we allow html.
			$htmlString = JHtml::_('string.truncate', $html, $maxLength, $noSplit = true, $allowHtml = true);

			// Now get the plain text from the html string.
			$htmlStringToPtString = JHtml::_('string.truncate', $htmlString, $maxLength, $noSplit = true, $allowHtml = false);

			// If the new plain text string matches the original plain text string we are done.
			if ($ptString == $htmlStringToPtString)
			{
				return $htmlString;
			}
			// Get the number of html tag characters in the first $maxlength characters
			$diffLength = strlen($ptString) - strlen($htmlStringToPtString);

			// Set new $maxlength that adjusts for the html tags
			$maxLength += $diffLength;
			if ($baseLength <= $maxLength || $diffLength <= 0)
			{
				return $htmlString;
			}
		}
		return $html;
	}

	public static function groupBy($list, $fieldName, $article_grouping_direction, $fieldNameToKeep = null)
	{
		$grouped = array();

		if (!is_array($list))
		{
			if ($list == '')
			{
				return $grouped;
			}

			$list = array($list);
		}

		foreach ($list as $key => $item)
		{
			if (!isset($grouped[$item->$fieldName]))
			{
				$grouped[$item->$fieldName] = array();
			}

			if (is_null($fieldNameToKeep))
			{
				$grouped[$item->$fieldName][$key] = $item;
			}
			else {
				$grouped[$item->$fieldName][$key] = $item->$fieldNameToKeep;
			}

			unset($list[$key]);
		}

		$article_grouping_direction($grouped);

		return $grouped;
	}

	public static function groupByDate($list, $type = 'year', $article_grouping_direction, $month_year_format = 'F Y')
	{
		$grouped = array();

		if (!is_array($list))
		{
			if ($list == '')
			{
				return $grouped;
			}

			$list = array($list);
		}

		foreach ($list as $key => $item)
		{
			switch($type)
			{
				case 'month_year':
					$month_year = JString::substr($item->created, 0, 7);

					if (!isset($grouped[$month_year]))
					{
						$grouped[$month_year] = array();
					}

					$grouped[$month_year][$key] = $item;
					break;

				case 'year':
				default:
					$year = JString::substr($item->created, 0, 4);

					if (!isset($grouped[$year]))
					{
						$grouped[$year] = array();
					}

					$grouped[$year][$key] = $item;
					break;
			}

			unset($list[$key]);
		}

		$article_grouping_direction($grouped);

		if ($type === 'month_year')
		{
			foreach ($grouped as $group => $items)
			{
				$date = new JDate($group);
				$formatted_group = $date->format($month_year_format);
				$grouped[$formatted_group] = $items;
				unset($grouped[$group]);
			}
		}

		return $grouped;
	}

	public static function getImage($id, $jparams) {

		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->from('#__content')->where('id='.$id);
		$db->setQuery($query);
		$item = $db->loadObject();
		$item_image =new JRegistry();
		$item_image->loadString($item->images, 'JSON');
		/*
		 * JRegistry Object ( [data:protected] => stdClass Object ( [image_intro] => [float_intro] =>
		 		* [image_intro_alt] => [image_intro_caption] =>
		 		* [image_fulltext] => images/sampledata/parks/landscape/120px_rainforest_bluemountainsnsw.jpg [float_fulltext] =>
		 		* [image_fulltext_alt] => [image_fulltext_caption] => ) )
		* */
		if ($jparams->get('show_image') == 'fulltext' && $item_image->get('image_fulltext') ) {
			$image = $item_image->get('image_fulltext');

		} else 	if ($jparams->get('show_image') == 'intro' && $item_image->get('image_intro') ) {
			$image = $item_image->get('image_intro');
		} else 	if ($jparams->get('show_image') == 'within_text') {
			$image_path = self::getImages($item->introtext);
			$image = $image_path;
		} else {
			$image = '';
		}

		return $image;
	}

 public static function getImages($text) {
		$matches = array();
		preg_match("/\<img.+?src=\"(.+?)\".+?\/>/", $text, $matches);
		$images = '';
		$images = false;
		$paths = array();
		if (isset($matches[1])) {

			$image_path = $matches[1];

			//joomla 1.5 only
			$full_url = JURI::base();

			//remove any protocol/site info from the image path
			$parsed_url = parse_url($full_url);

			$paths[] = $full_url;
			if (isset($parsed_url['path']) && $parsed_url['path'] != "/") $paths[] = $parsed_url['path'];


			foreach ($paths as $path) {
				if (strpos($image_path,$path) !== false) {
					$image_path = substr($image_path,strpos($image_path, $path)+strlen($path));
				}
			}

			// remove any / that begins the path
			if (substr($image_path, 0 , 1) == '/') $image_path = substr($image_path, 1);

			//if after removing the uri, still has protocol then the image
			//is remote and we don't support thumbs for external images
			if (strpos($image_path,'http://') !== false ||
					strpos($image_path,'https://') !== false) {
				return false;
			}

			$images = JURI::Root(True)."/".$image_path;
		}
		return $images;
	}

public static function getJ2StoreData($item) {

		$j2store_carthelper= new J2StoreHelperCart();
		$item->j2store->cart_block = $j2store_carthelper->getAjaxCart($item);
		if(class_exists('J2StoreTax')) {
			$tax = new J2StoreTax();
		} else {
			$tax = new Tax();
		}

		$product_id = $item->product_id = $item->id;

		$product = $j2store_carthelper->getItemInfo($item->id);
		$stock = $product->stock;
		if(isset($stock->min_sale_qty) && $stock->min_sale_qty > 1 && J2STORE_PRO == 1) {
			$item->j2store->product_quantity = (int) $stock->min_sale_qty;
			$item->j2store->item_minimum_notice = JText::sprintf('J2STORE_MINIMUM_QUANTITY_NOTIFICATION', $product->product_name, (int) $stock->min_sale_qty);
		} else {
			$item->j2store->product_quantity = 1;
		}

		//$j2item = J2StorePrices::getJ2Product($item->id);
		//get prices
		$item->j2store->prices = J2StorePrices::getPrice($product_id, $item->j2store->product_quantity);
		//base price
		$item->j2store->price =  $item->j2store->prices->product_baseprice;

		$product_tax = $tax->getProductTax($item->j2store->price, $product_id);
		$item->j2store->tax = 0;

		if(isset($product_tax)) {
			$item->j2store->tax = $product_tax;
		}

		//now get the special price
		$item->j2store->special_price = isset($item->j2store->prices->product_specialprice)? (float) $item->j2store->prices->product_specialprice: null;

		$item->j2store->sp_tax = 0;

		if($item->j2store->special_price ) {
			$sp_tax = $tax->getProductTax($item->j2store->special_price,$item->product_id);
			if($sp_tax) {
				$item->j2store->sp_tax = $sp_tax;
			}
		}

		//get sku
		$item->j2store->product_model = (isset($product->item_sku))?$product->item_sku:'';

		if(J2STORE_PRO == 1) {
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_j2store/models');
			$qtyModel = JModelLegacy::getInstance('ProductQuantities', 'J2StoreModel');
			$qtyModel->setState('filter_product', $item->product_id);
			$item->j2store->product_stock = $qtyModel->getQuantityTotal();
		} else {
			$item->j2store->product_stock = 99;
		}

	}
}

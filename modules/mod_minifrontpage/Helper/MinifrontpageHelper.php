<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_minifrontpage
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * 
 * @subpackage	mod_minifrontpage
 * @author     	TemplatePlazza
 * @link 		http://www.templateplazza.com
 */

defined('_JEXEC') or die;

JLoader::register('ContentHelperRoute', JPATH_SITE . '/components/com_content/helpers/route.php');

JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_content/models', 'ContentModel');

use Joomla\Utilities\ArrayHelper;

/**
 * Helper for mod_articles_latest
 *
 * @since  1.6
 */
abstract class MinifrontpageHelper
{
	/**
	 * Retrieve a list of article
	 *
	 * @param   \Joomla\Registry\Registry  &$params  module parameters
	 *
	 * @return  mixed
	 *
	 * @since   1.6
	 */
	public static function getList(&$params)
	{
		// Get the dbo
		$db = JFactory::getDbo();

		// Get an instance of the generic articles model
		$model = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

		// Set application parameters in model
		$app       = JFactory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set the filters based on the module params
		$model->setState('list.start', (int) $params->get('num_intro_skip', 0));
		$model->setState('list.limit', (int) $params->get('count', 5));
		$model->setState('filter.published', 1);

		// This module does not use tags data
		$model->setState('load_tags', false);

		// Access filter
		$access     = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$model->setState('filter.access', $access);

		// Category filter
		$model->setState('filter.category_id', $params->get('catid', array()));
		$model->setState('filter.tag', $params->get('filter_tag', array()));

		// User filter
		$userId = JFactory::getUser()->get('id');

		switch ($params->get('user_id'))
		{
			case 'by_me' :
				$model->setState('filter.author_id', (int) $userId);
				break;
			case 'not_me' :
				$model->setState('filter.author_id', $userId);
				$model->setState('filter.author_id.include', false);
				break;

			case 'created_by' :
				$model->setState('filter.author_id', $params->get('author', array()));
				break;

			case '0' :
				break;

			default:
				$model->setState('filter.author_id', (int) $params->get('user_id'));
				break;
		}

		// Filter by language
		$model->setState('filter.language', $app->getLanguageFilter());

		// Featured switch
		$featured = $params->get('show_featured', '');

		if ($featured === '')
		{
			$model->setState('filter.featured', 'show');
		}
		elseif ($featured)
		{
			$model->setState('filter.featured', 'only');
		}
		else
		{
			$model->setState('filter.featured', 'hide');
		}

		// Set ordering
		$order_map = array(
			'm_dsc' => 'a.modified DESC, a.created',
			'mc_dsc' => 'CASE WHEN (a.modified = ' . $db->quote($db->getNullDate()) . ') THEN a.created ELSE a.modified END',
			'c_dsc' => 'a.created',
			'p_dsc' => 'a.publish_up',
			'random' => $db->getQuery(true)->Rand(),
		);

		$ordering = ArrayHelper::getValue($order_map, $params->get('ordering'), 'a.publish_up');
		$dir      = 'DESC';

		$model->setState('list.ordering', $ordering);
		$model->setState('list.direction', $dir);

		//Filter and Set Period (days)
		$period = $params->get('period', '');
		if($period == '9') {
			$period = $params->get('custom_period');
		}
		if ($period != '') {
			$model->setState('filter.date_filtering', 'relative');
			$model->setState('filter.relative_date', intval($period));
		}

		$items = $model->getItems();

		foreach ($items as &$item)
		{
			$item->slug    = $item->id . ':' . $item->alias;

			/** @deprecated Catslug is deprecated, use catid instead. 4.0 */
			$item->catslug = $item->catid . ':' . $item->category_alias;

			if ($access || in_array($item->access, $authorised))
			{
				// We know that user has the privilege to view the article
				$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid, $item->language));
			}
			else
			{
				$item->link = JRoute::_('index.php?option=com_users&view=login');
			}
		}

		return $items;
	}
	public static function getThumbnail($item_id, $article_images, $thumb_folder, $show_default_thumb, $custom_default_thumb, $thumb_width,$thumb_height,$title,$introtext,$module_id) {
		$thumb_name = str_replace([':', '\\', '/', '*', '\'', '"'], '', str_replace(' ', '_', strtolower($item_id)) );
		$thumb_name = $thumb_name."_".$module_id;
		if (JFile::exists(JPATH_ROOT.$thumb_folder.$thumb_name.'.jpg')) {
			// .jpg thumbnail is exists
			$thumb_img = '<img src="'.JUri::root(true).$thumb_folder.$thumb_name.'.jpg" width="'.$thumb_width.'" height="'.$thumb_height.'" alt="'.$title.'" />';
			$thumb_img_url = JUri::root(true).$thumb_folder.$thumb_name.'.jpg';

		}elseif(JFile::exists(JPATH_ROOT.$thumb_folder.$thumb_name.'.png')) {
			// .png thumbnail is exists
			$thumb_img = '<img src="'.JUri::root(true).$thumb_folder.$thumb_name.'.png" width="'.$thumb_width.'" height="'.$thumb_height.'" alt="'.$title.'" />';
			$thumb_img_url = JUri::root(true).$thumb_folder.$thumb_name.'.png';

		}elseif(JFile::exists(JPATH_ROOT.$thumb_folder.$thumb_name.'_default.jpg')) {
			if($show_default_thumb){
				$thumb_img = '<img src="'.JUri::root(true).$thumb_folder.$thumb_name.'_default.jpg" width="'.$thumb_width.'" height="'.$thumb_height.'" alt="'.$title.'" />';
				$thumb_img_url = JUri::root(true).$thumb_folder.$thumb_name.'_default.jpg';
			}else{
				$thumb_img = "";
				$thumb_img_url ="";
			}

		} else {
			// Thumbnail is not exist
			$images = json_decode($article_images);
			// Find Article's Image
			if (!empty($images->image_intro) ) {
				// J4 - remove strings after # character image_intro
				$orig_image = strstr($images->image_intro, '#', true);
				// Added JCE PRO support
				if ($orig_image == NULL) {
					$orig_image = $images->image_intro;
				}
			} elseif (empty($images->image_intro) && !empty($images->image_fulltext) ) { 
				// J4 - remove strings after # character image_fulltext
				$orig_image = strstr($images->image_fulltext, '#', true);
				// Added JCE PRO support
				if ($orig_image == NULL) {
					$orig_image = $images->image_fulltext;
				}
			} else {
				// Find first image in the article
				$html = $introtext;
				$pattern = '/<img .*?src="([^"]+)"/si';

				if ( preg_match($pattern, $html, $match) ) {
					$orig_image = $match[1];
				} else {
					$orig_image = "";
				}
			}
			// If article doesn't have any image then generate thumbnail from the default image / custom default thumb
			if($orig_image == ""){
				$size_multiplier = 3; // to generate retina quality of thumbnail
				$custom_default_thumb = strstr($custom_default_thumb, '#', true);
				// Added JCE PRO support
				if ($custom_default_thumb == NULL) {
					$custom_default_thumb = $custom_default_thumb;
				}
				$custom_default_thumb = str_replace('%20', ' ', $custom_default_thumb); // Replace %20 character for image's name with space
				
				$file_ext = JFile::getExt($custom_default_thumb);
				if($file_ext == 'png'){
					$imagetype = IMAGETYPE_PNG;
					$quality = '7';
					$file_ext = 'png';
				}elseif($file_ext == 'gif'){
					$imagetype = IMAGETYPE_PNG;
					$quality = '7';
					$file_ext = 'png';
				} else {
					$imagetype = IMAGETYPE_JPEG;
					$quality = '70';
					$file_ext = 'jpg';
				}
				if($custom_default_thumb){
					$default_thumb = $custom_default_thumb;

				} else {
					$default_thumb = 'modules/mod_minifrontpage/tmpl/assets/default.png';
				}
				$image = new JImage($default_thumb);

				$resizedImage = $image->cropResize($thumb_width * $size_multiplier, $thumb_height * $size_multiplier, true);
				$thumb = $resizedImage->toFile(JPATH_BASE.$thumb_folder.$thumb_name.'_default.'.$file_ext, $imagetype, array('quality' => $quality));
				if($show_default_thumb){
					$thumb_img = '<img src="'.JUri::root(true).$thumb_folder.$thumb_name.'_default.'.$file_ext.'" alt="'.$title.'" width="'.$thumb_width.'" height="'.$thumb_height.'"/>';	
					$thumb_img_url = JUri::root(true).$thumb_folder.$thumb_name.'_default.'.$file_ext;	
				}else {
					$thumb_img 		= "";
					$thumb_img_url 	= "";
				}

			} else {
				// If article contains an image then generate a thumbnail
				$orig_image = str_replace('%20', ' ', $orig_image); // Replace %20 character for image's name with space
				$orig_image = ltrim($orig_image, '/'); // Replace foreword slash
				$size_multiplier = 3; // to generate retina quality of thumbnail
				$file_ext = JFile::getExt($orig_image);
				
				if($file_ext == 'png'){ 
					$imagetype = IMAGETYPE_PNG;
					$quality	= '7';
					$file_ext = 'png';
				}elseif($file_ext == 'gif'){ 
					$imagetype = IMAGETYPE_PNG;
					$quality	= '7';
					$file_ext = 'png';
				} else {
					$imagetype = IMAGETYPE_JPEG;
					$quality	= '70';
					$file_ext = 'jpg';
				}
				if (strpos($orig_image, 'http') !== false) {
					// If external image
					$file_ext = 'jpg';
					$imagetype = IMAGETYPE_JPEG;
					if($custom_default_thumb){
						$file_ext = JFile::getExt($custom_default_thumb);
						if($file_ext == 'png'){
							$imagetype = IMAGETYPE_PNG;
							$quality = '7';
							$file_ext = 'png';
						}elseif($file_ext == 'gif'){
							$imagetype = IMAGETYPE_PNG;
							$quality = '7';
							$file_ext = 'png';
						} else {
							$imagetype = IMAGETYPE_JPEG;
							$quality = '70';
							$file_ext = 'jpg';
						}
						$default_thumb = $custom_default_thumb;
	
					} else {
						$default_thumb = 'modules/mod_minifrontpage/tmpl/assets/default.png';
					}
					
					$image = new JImage($default_thumb);

					$resizedImage = $image->cropResize($thumb_width * $size_multiplier, $thumb_height * $size_multiplier, true);
					$thumb = $resizedImage->toFile(JPATH_BASE.$thumb_folder.$thumb_name.'_default.'.$file_ext, $imagetype, array('quality' => $quality));
					if($show_default_thumb){
						$thumb_img = '<img src="'.JUri::root(true).$thumb_folder.$thumb_name.'_default.'.$file_ext.'" alt="'.$title.'" width="'.$thumb_width.'" height="'.$thumb_height.'"/>';	
						$thumb_img_url = JUri::root(true).$thumb_folder.$thumb_name.'_default.'.$file_ext;	
					} else {
						$thumb_img 		= "";
						$thumb_img_url 	= "";
					}

				} else {
					if (JFile::exists(JPATH_ROOT.'/'.$orig_image)) {
						$orig_image =$orig_image;
					} else {
						if($custom_default_thumb){
							$file_ext = JFile::getExt($custom_default_thumb);
							if($file_ext == 'png'){
								$imagetype = IMAGETYPE_PNG;
								$quality = '7';
								$file_ext = 'png';
							}elseif($file_ext == 'gif'){
								$imagetype = IMAGETYPE_PNG;
								$quality = '7';
								$file_ext = 'png';
							} else {
								$imagetype = IMAGETYPE_JPEG;
								$quality = '70';
								$file_ext = 'jpg';
							}
							$orig_image = $custom_default_thumb;
						} else {
							$orig_image = 'modules/mod_minifrontpage/tmpl/assets/default.png';
						}
					}
					$image = new JImage($orig_image);
					$resizedImage = $image->cropResize($thumb_width * $size_multiplier, $thumb_height * $size_multiplier, true);
					$thumb = $resizedImage->toFile(JPATH_BASE.$thumb_folder.$thumb_name.'.'.$file_ext, $imagetype, array('quality' => $quality));
					$thumb_img = '<img src="'.JUri::root(true).$thumb_folder.$thumb_name.'.'.$file_ext.'" alt="'.$title.'" width="'.$thumb_width.'" height="'.$thumb_height.'"/>';	
					$thumb_img_url = JUri::root(true).$thumb_folder.$thumb_name.'.'.$file_ext;	
				}
			}
		}
		return array($thumb_img, $thumb_img_url);
	}
}

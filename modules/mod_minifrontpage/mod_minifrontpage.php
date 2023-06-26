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

//J3 Part
JLoader::register('MinifrontpageHelper', __DIR__ . '/Helper/MinifrontpageHelper.php');
$list = MinifrontpageHelper::getList($params);

$doc = JFactory::getDocument();
$thumb_width = $params->get('thumb_width', 56);
$thumb_height = $params->get('thumb_height', 56);
$show_intro = $params->get('show_intro', 1);
$introtext_truncate = $params->get('limit_intro', 200);
$title_truncate = $params->get('limit_title', 0);
$show_default_thumb = $params->get('show_default_thumb', 0);
$custom_default_thumb = $params->get('custom_default_thumb');

$css_adjustment = $params->get('css_adjustment');
if($css_adjustment){
    $doc->addStyleDeclaration($css_adjustment);
}

// module version for css and js call
$static_files_version = "3.0.2";

// Get timezone 
//$config = JFactory::getConfig();
//$offset = $config->get('offset');
$thumb_folder ="/images/thumbnails/mod_minifrontpage/";

require JModuleHelper::getLayoutPath('mod_minifrontpage', $params->get('layout', 'default'));

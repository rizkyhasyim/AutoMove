<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_minifrontpagepro
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * 
 * @subpackage	mod_minifrontpagepro
 * @author     	TemplatePlazza
 * @link 		http://www.templateplazza.com
 */

// No direct access to this file
defined('_JEXEC') or die;

class mod_minifrontpageInstallerScript
{
	public function install($parent) 
    {
		$thumb_folder_0 ="/images/thumbnails/";
		$thumb_folder ="/images/thumbnails/mod_minifrontpage/";

		// Create thumbnail folder if not exist
		if (!JFolder::exists(JPATH_ROOT.$thumb_folder)) {
			JFolder::create(JPATH_ROOT.$thumb_folder);
			JFile::write(JPATH_ROOT.$thumb_folder_0.'/index.html', "");
			JFile::write(JPATH_ROOT.$thumb_folder.'/index.html', "");
		}
        echo '
		<p style="border-radius:4px;display:block;border:1px solid #BCE8F1;padding:10px 15px;background:#D8EDF7;color:#31718F;font-weight:400;"> <strong><a style="color:#333;text-decoration:underline;" href="index.php?option=com_modules&view=modules&filter_search=&filter_module=mod_minifrontpage">Open Module Manager</a></strong> to publish the module.</p><br/>';
    }

	function update($parent) 
	{
		$thumb_folder_0 ="/images/thumbnails/";
		$thumb_folder ="/images/thumbnails/mod_minifrontpage/";

		// Create thumbnail folder if not exist
		if (!JFolder::exists(JPATH_ROOT.$thumb_folder)) {
			JFolder::create(JPATH_ROOT.$thumb_folder);
			JFile::write(JPATH_ROOT.$thumb_folder_0.'/index.html', "");
			JFile::write(JPATH_ROOT.$thumb_folder.'/index.html', "");
		}
		echo '
		<p style="border-radius:4px;display:block;border:1px solid #BCE8F1;padding:10px 15px;background:#D8EDF7;color:#31718F;font-weight:400;">The module has been updated to the latest version. <strong><a style="color:#333;text-decoration:underline;" href="index.php?option=com_modules&view=modules&filter_search=&filter_module=mod_minifrontpage">Open Module Manager</a></strong> to manage the module.</p><br/>';
	}
}
<?php
/**
 * @copyright	Copyright (c) 2020 TemplatePlazza. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */


defined('JPATH_BASE') or die;
jimport('joomla.form.formfield');

/* adding additional javascript and css loads to the  backend */

class JFormFieldMiniFrontpage extends JFormField {
protected $type = 'minifrontpage';
protected function getInput() {
    $doc = JFactory::getDocument();
    $doc->addStyleSheet(JURI::root() .'/modules/mod_minifrontpage/admin/assets/css/admin.min.css');
    return null;
    }
}
?>
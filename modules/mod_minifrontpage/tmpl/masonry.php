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
JHtml::_('jquery.framework');
$theme_masonry = $params->get('theme_masonry');

$doc->addStyleSheet(JURI::base(true).'/modules/mod_minifrontpage/tmpl/masonry/grid.min.css?v='.$static_files_version);
/* Skin */
$doc->addStyleSheet(JURI::base(true).'/modules/mod_minifrontpage/tmpl/masonry/skin/'.$theme_masonry->masonry_skin.'.css?v='.$static_files_version);

$doc->addScript(JURI::base(true).'/modules/mod_minifrontpage/tmpl/masonry/imagesloaded.min.js?v='.$static_files_version);
$doc->addScript(JURI::base(true).'/modules/mod_minifrontpage/tmpl/masonry/masonry.min.js?v='.$static_files_version);

// General params
$show_date = $theme_masonry->show_date;
$date_format = $theme_masonry->date_format;
if($date_format == "custom_date_format") {
    $date_format = $theme_masonry->custom_date_format;
}

/* Theme Settings */
$masonry_num_column             = $theme_masonry->masonry_num_column;
$masonry_show_author            = $theme_masonry->masonry_show_author;
$masonry_show_cat               = $theme_masonry->masonry_show_cat;
$masonry_thumbnail_position     = $theme_masonry->masonry_thumbnail_position;
$masonry_skin                   = str_replace('.min', '', $theme_masonry->masonry_skin);

$scriptcr ='
jQuery( document ).ready(function() {

    // init Masonry
  var $grid = jQuery(".mfp-masonry-'.$module->id.'").masonry({
    itemSelector: ".mfp_masonry_item"
  });
  // layout Masonry after each image loads
  $grid.imagesLoaded().progress( function() {
    $grid.masonry("layout");
  });

});
';

$doc->addScriptDeclaration($scriptcr);
?>
<div class="mfp_masonry_skin_<?php echo $masonry_skin;?>">
    <div class="mfp-grid mfp-masonry-<?php echo $module->id; ?>">  
    <?php 
        foreach ($list as $item) : 
            // Get the thumbnail 
            $thumb_img = MinifrontpageHelper::getThumbnail($item->id, $item->images,$thumb_folder,$show_default_thumb,$custom_default_thumb,$thumb_width,$thumb_height,$item->title,$item->introtext.$item->fulltext,$module->id);

            ?>
            <div class="mfp-col-xs-12 mfp-col-sm-6 mfp-col-md-<?php echo (12/$masonry_num_column);?> mfp_masonry_item">
                <div>
                    <?php 
                    // Show Thumbnail - Position before title
                    if(($masonry_thumbnail_position == "top")||($masonry_thumbnail_position == "left") || ($masonry_thumbnail_position == "right")) { ?>
                        <a href="<?php echo $item->link; ?>" class="mfp_thumb_pos_<?php echo $masonry_thumbnail_position; ?>" itemprop="url"><?php echo $thumb_img[0]; ?></a>
                    <?php } ?>
                    <?php 
                        // Show Article Category
                        if($masonry_show_cat){
                            echo "<span class='mfp_cat'><a href=".JRoute::_('index.php?option=com_content&view=category&id='.$item->catid).">".$item->category_title."</a></span>"; 
                        }
                    ?>
                    <h4 class="mfp_masonry_title">
                        <a href="<?php echo $item->link; ?>" itemprop="url">
                            <?php echo JHtmlString::truncate($item->title, $title_truncate,true,false);  ?>
                        </a>
                    </h4>
                    <?php 
                        // Show Author
                        if($masonry_show_author){
                            echo "<span class='mfp_author'> ".$item->author."</span>";
                        }
                    ?>
                    <?php
                        // Show Date
                        if($show_date) {
                            if($masonry_show_author) {
                                echo "- ";
                            }
                            if($params->get('ordering') == "m_dsc") {
                                $orderingdate = $item->modified;
                            }elseif($params->get('ordering') == "p_dsc") {
                                $orderingdate = $item->publish_up;
                            }else{
                                $orderingdate = $item->created;
                            }
                            echo "<span class='mfp_date'>".JHtml::_('date',$orderingdate, JText::_($date_format))."</span>";
                        }
                    ?>
                    <?php if($show_intro != 0){ ?>		
                    <p class="mfp_masonry_introtext">
                        <?php 
                        // Show Thumbnail - Position before introtext
                        if(($masonry_thumbnail_position == "top2")||($masonry_thumbnail_position == "left2") || ($masonry_thumbnail_position == "right2")) { ?>
                            <a href="<?php echo $item->link; ?>" class="mfp_thumb_pos_<?php echo $masonry_thumbnail_position; ?>" itemprop="url"><?php echo $thumb_img[0]; ?></a>
                            
                        <?php } ?>
                        <?php 
                        echo JHtmlString::truncate($item->introtext, $introtext_truncate, true, false);  ?>
                    </p>
                    <?php } ?>
                </div>
            </div>
            <?php
        endforeach; ?>
    </div>
</div>
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
$theme_default = $params->get('theme_default');
$doc->addStyleSheet(JURI::base(true).'/modules/mod_minifrontpage/tmpl/default/grid.min.css?v='.$static_files_version);

/* Skin */
$doc->addStyleSheet(JURI::base(true).'/modules/mod_minifrontpage/tmpl/default/skin/'.$theme_default->default_skin.'.css?v='.$static_files_version);

// General params
$count = $params->get('count');
$show_date = $theme_default->show_date;
$date_format = $theme_default->date_format;
if($date_format == "custom_date_format") {
    $date_format = $theme_default->custom_date_format;
}

/* Theme Settings */
$default_num_column             = $theme_default->default_num_column;
$default_show_author            = $theme_default->default_show_author;
$default_show_cat               = $theme_default->default_show_cat;
$default_thumbnail_position     = $theme_default->default_thumbnail_position;
$default_show_more              = $theme_default->default_show_more;
if($default_show_more == 1) {
    $default_more_item_count    = $theme_default->default_more_item_count; 
}else {
    $default_more_item_count    = $params->get('count');
}
$default_skin                   = str_replace('.min', '', $theme_default->default_skin);
$default_cat_pos                = $theme_default->default_cat_pos; 
$default_cat_pos_align          = $theme_default->default_cat_pos_align;

if(($default_cat_pos == "1") && ($default_thumbnail_position == "top")){
    $doc->addStyleDeclaration("
    .mfp_mid_".$module->id." .mfp_cat a {position: absolute;top:0;".$default_cat_pos_align.": 0;z-index: 9999;background: #222;padding: 5px 8px;line-height:1!important;text-decoration:none;color:#fff!important;}.mfp_mid_".$module->id." .mfp_default_item:hover .mfp_cat a {background:#cc0000;}");
} 
?>

<div class="mfp_default_skin_<?php echo $default_skin;?> mfp_mid_<?php echo $module->id;?>">  
    <div class="mfp-grid">  
    <?php 
            $n = 1;
            $moreitem = "";
            foreach ($list as $item) : 
                // Get the thumbnail 
                $thumb_img = MinifrontpageHelper::getThumbnail($item->id, $item->images,$thumb_folder,$show_default_thumb,$custom_default_thumb,$thumb_width,$thumb_height,$item->title,$item->introtext.$item->fulltext,$module->id);

                // Item count check for showing More Articles Block
                if($n <= $default_more_item_count){
                ?>
                <div class="mfp-col-xs-12 mfp-col-sm-6 mfp-col-md-<?php echo 12/$default_num_column;?> mfp_default_item">
                    <div>
                    <?php 
                    // Show Thumbnail - Position before title
                    if(($default_thumbnail_position == "top")||($default_thumbnail_position == "left") || ($default_thumbnail_position == "right")) { ?>
                        <a href="<?php echo $item->link; ?>" class="mfp_thumb_pos_<?php echo $default_thumbnail_position; ?>" itemprop="url"><?php echo $thumb_img[0]; ?></a>
                    <?php } ?>
                    <?php 
                        // Show Article Category
                        if($default_show_cat){
                            echo "<span class='mfp_cat'><a href=".JRoute::_('index.php?option=com_content&view=category&id='.$item->catid).">".$item->category_title."</a></span>"; 
                        }
                    ?>
                    <h4 class="mfp_default_title">    
                        <a href="<?php echo $item->link; ?>" itemprop="url">
                            <?php echo JHtmlString::truncate($item->title, $title_truncate,true,false);  ?>
                        </a>
                    </h4>
                    <?php 
                        // Show Author
                        if($default_show_author){
                            echo "<span class='mfp_author'>".$item->author."</span>";
                        }
                    ?>
                    <?php
                        // Show Date
                        if($show_date) {
                            if($default_show_author) {
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
                    <p class="mfp_introtext">
                        <?php 
                        // Show Thumbnail - Position before introtext
                        if(($default_thumbnail_position == "top2")||($default_thumbnail_position == "left2") || ($default_thumbnail_position == "right2")) { ?>
                            <a href="<?php echo $item->link; ?>" class="mfp_thumb_pos_<?php echo $default_thumbnail_position; ?>" itemprop="url"><?php echo $thumb_img[0]; ?></a>
                            
                        <?php } ?>
                        <?php 
                        echo JHtmlString::truncate($item->introtext, $introtext_truncate, true, false);  ?>
                    </p>
                    <?php } ?>
                        </div>
                </div>
                <?php } else {
                    if($params->get('ordering') == "m_dsc") {
                        $orderingdate = $item->modified;
                    }elseif($params->get('ordering') == "p_dsc") {
                        $orderingdate = $item->publish_up;
                    }else{
                        $orderingdate = $item->created;
                    }
                    $moreitem .= '<li><a href="'.$item->link.'" itemprop="url">'.JHtmlString::truncate($item->title, $title_truncate,true,false).'</a><span class="mfp_date">'.JHtml::_('date',$orderingdate, JText::_($date_format)).'</span></li>';
                }  
            $n++;
        endforeach; ?>
        <?php 
        if($default_show_more == 1) {
            $grid_width = (((ceil(($count - ($count - $default_more_item_count))/$default_num_column)) * $default_num_column) - $default_more_item_count) * (12/$default_num_column);
            if($grid_width == 0) {
                $grid_width = 12;
            }
            $row2 = (ceil(($count - ($count - $default_more_item_count))/$default_num_column)) * $default_num_column;
        if($moreitem != "") {?>
        <div class="mfp-col-xs-12 mfp-col-sm-6 mfp-col-md-<?php echo $grid_width;?> mfp_default_item mfp_default_more_articles">
            <div>
                <h5><?php echo JText::_('MOD_MFP_THM_DEFAULT_MORE_ARTICLES'); ?></h5>
                <ul><?php echo $moreitem; ?></ul>
            </div>
        </div>
        <?php 
            }
        } ?>
    </div>
</div> 



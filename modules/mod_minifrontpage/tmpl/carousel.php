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
$theme_carousel = $params->get('theme_carousel');

$doc->addStyleSheet(JURI::base(true).'/modules/mod_minifrontpage/tmpl/carousel/slider.min.css?v='.$static_files_version);
$doc->addStyleSheet(JURI::base(true).'/modules/mod_minifrontpage/tmpl/carousel/animate.min.css?v='.$static_files_version);
/* Skin */
$doc->addStyleSheet(JURI::base(true).'/modules/mod_minifrontpage/tmpl/carousel/skin/'.$theme_carousel->carousel_skin.'.css?v='.$static_files_version);

$doc->addScript(JURI::base(true).'/modules/mod_minifrontpage/tmpl/carousel/slider.min.js?v='.$static_files_version);

// General params
$show_date = $theme_carousel->show_date;
$date_format = $theme_carousel->date_format;
if($date_format == "custom_date_format") {
    $date_format = $theme_carousel->custom_date_format;
}

/* Theme Settings */
$carousel_num_column            = $theme_carousel->carousel_num_column;
$carousel_show_author           = $theme_carousel->carousel_show_author;
$carousel_show_cat              = $theme_carousel->carousel_show_cat;
$carousel_thumbnail_position    = $theme_carousel->carousel_thumbnail_position;
$carousel_anim_in               = $theme_carousel->carousel_anim_in;
if($carousel_anim_in == "tns-fadeIn") {
    $carousel_mode              = "carousel";
    $carousel_anim_out          = "tns-fadeOut";
    $carousel_anim_speed        = "1000";
} else {
    $carousel_mode              = "gallery";
    $carousel_anim_out          = $theme_carousel->carousel_anim_out;
    $carousel_anim_speed        = $theme_carousel->carousel_anim_speed;
}
$carousel_gutter_size           = $theme_carousel->carousel_gutter_size;
$carousel_autoplay              = $theme_carousel->carousel_autoplay;
$carousel_autoplay_timeout      = $theme_carousel->carousel_autoplay_timeout;

if($carousel_num_column < 2){
    $carousel_num_column_768        = 2;
}else {
    $carousel_num_column_768        = $carousel_num_column;
}

$scriptcr ="var slider".$module->id." = tns({container: '.mfp-carousel-".$module->id."',items: ".$carousel_num_column.",autoplay: ".$carousel_autoplay.",autoplayTimeout: ".$carousel_autoplay_timeout.",autoplayHoverPause:true,autoplayText: ['▶','❚❚'],mode : '".$carousel_mode."',animateIn: '".$carousel_anim_in."',animateOut: '".$carousel_anim_out."',speed: ".$carousel_anim_speed.",swipeAngle: false,responsive: {200: {gutter: ".$carousel_gutter_size.",items: 1},480: {gutter: ".$carousel_gutter_size.",items: 2},768: {gutter: ".$carousel_gutter_size.",items: ".($carousel_num_column_768 - 1)."},1024: {gutter: ".$carousel_gutter_size.",items: ".$carousel_num_column."}}
});";

$scriptcr_preload = "jQuery(document).ready(function(){
    jQuery('.mfp_carousel_skin_".str_replace('.min', '', $theme_carousel->carousel_skin)."').show(); });";

//$doc->addScriptDeclaration($scriptcr, 'module');
$doc->addScriptDeclaration($scriptcr_preload);

if($carousel_mode == "gallery"){
$doc->addStyleDeclaration(".mfp_carousel_skin_".str_replace('.min', '', $theme_carousel->carousel_skin)." .tns-inner {margin: 0 -".$carousel_gutter_size."px 0 0;}");
}
if($carousel_autoplay == "true"){
$doc->addStyleDeclaration(".mfp_carousel_skin_".str_replace('.min', '', $theme_carousel->carousel_skin)." .tns-controls {margin-right: 17px;}");
}
?>
<div class="mfp_carousel_skin_<?php echo str_replace('.min', '', $theme_carousel->carousel_skin);?>">
    <div class="mfp_block_title"><h4><?php echo $module->title; ?></h4></div>
    <div class="mfp-carousel-<?php echo $module->id; ?>">  

    <?php foreach ($list as $item) : 
                // Get the thumbnail 
                $thumb_img = MinifrontpageHelper::getThumbnail($item->id, $item->images,$thumb_folder,$show_default_thumb,$custom_default_thumb,$thumb_width,$thumb_height,$item->title,$item->introtext.$item->fulltext,$module->id);

                ?>
                <div class="mfp_carousel_item">
                    <div>
                        <?php 
                        // Show Thumbnail - Position before title
                        if(($carousel_thumbnail_position == "top")||($carousel_thumbnail_position == "left") || ($carousel_thumbnail_position == "right")) { ?>
                            <a href="<?php echo $item->link; ?>" class="mfp_thumb_pos_<?php echo $carousel_thumbnail_position; ?>" itemprop="url"><?php echo $thumb_img[0]; ?></a>
                        <?php } ?>
                        <?php 
                            // Show Article Category
                            if($carousel_show_cat){
                                echo "<span class='mfp_cat'><a href=".JRoute::_('index.php?option=com_content&view=category&id='.$item->catid).">".$item->category_title."</a></span>"; 
                            }
                        ?>
                        <h4 class="mfp_carousel_title">    
                            <a href="<?php echo $item->link; ?>" itemprop="url">
                                <?php echo JHtmlString::truncate($item->title, $title_truncate,true,false);  ?>
                            </a>
                        </h4>
                        <?php 
                            // Show Author
                            if($carousel_show_author){
                                echo "<span class='mfp_author'> ".$item->author."</span>";
                            }
                        ?>
                        <?php
                            // Show Date
                            if($show_date) {
                                if($carousel_show_author) {
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
                        <p class="mfp_carousel_introtext">
                            <?php 
                            // Show Thumbnail - Position before introtext
                            if(($carousel_thumbnail_position == "top2")||($carousel_thumbnail_position == "left2") || ($carousel_thumbnail_position == "right2")) { ?>
                                <a href="<?php echo $item->link; ?>" class="mfp_thumb_pos_<?php echo $carousel_thumbnail_position; ?>" itemprop="url"><?php echo $thumb_img[0]; ?></a>
                                
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
<?php echo "<script>".$scriptcr."</script>";?>
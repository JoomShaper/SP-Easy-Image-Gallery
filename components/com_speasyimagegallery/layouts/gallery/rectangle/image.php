<?php
/**
 * @package com_speasyimagegallery
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2022 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */


// No direct access
defined('_JEXEC') or die('Restricted access');

extract($displayData);

if ($column == 1) {
  $sizes = array(
    array('x_thumb', 'speasyimagegallery-col-xs-12 speasyimagegallery-col-sm-8'),
    array('thumb', 'speasyimagegallery-col-xs-6 speasyimagegallery-col-sm-4'),
    array('thumb', 'speasyimagegallery-col-xs-6 speasyimagegallery-col-sm-4'),
  );
} else if ($column == 2) {
  $sizes = array(
    array('x_thumb', 'speasyimagegallery-col-xs-12 speasyimagegallery-col-sm-8 speasyimagegallery-col-sm-push-4'),
    array('thumb', 'speasyimagegallery-col-xs-6 speasyimagegallery-col-sm-4 speasyimagegallery-col-sm-pull-8'),
    array('thumb', 'speasyimagegallery-col-xs-6 speasyimagegallery-col-sm-4 speasyimagegallery-col-sm-pull-8'),
  );
} else {
  $sizes = array(
    array('thumb', 'speasyimagegallery-col-xs-12 speasyimagegallery-col-sm-4'),
    array('thumb', 'speasyimagegallery-col-xs-6 speasyimagegallery-col-sm-4'),
    array('thumb', 'speasyimagegallery-col-xs-6 speasyimagegallery-col-sm-4'),
  );
}

$source = json_decode($image->images);
$thumb = $sizes[$index][0];
$col = $sizes[$index][1];
?>
<div class="<?php echo $col; ?>">
    <a class="speasyimagegallery-gallery-item" href="<?php echo $source->original; ?>" data-title="<?php echo $image->title; ?>"
        data-desc="<?php echo ($image->description) ? strip_tags($image->description) : ''; ?>">
        <div>
            <img src="<?php echo $source->$thumb; ?>" title="<?php echo $image->title; ?>" alt="<?php echo $image->alt; ?>">
            <div class="speasyimagegallery-gallery-item-content">
                <span class="speasyimagegallery-gallery-item-title"><?php echo $image->title; ?></span>
            </div>
        </div>
    </a>
</div>

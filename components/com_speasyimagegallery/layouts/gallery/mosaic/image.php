<?php
/**
* @package com_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2021 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/


// No direct access
defined('_JEXEC') or die('Restricted access');

extract($displayData);

if($column == 1) {
  $sizes = array(
    'thumb',
    'y_thumb',
    'thumb',
  );
} else if($column == 2) {
  $sizes = array(
    'thumb',
    'thumb',
    'y_thumb',
  );
} else {
  $sizes = array(
    'y_thumb',
    'thumb',
    'thumb',
  );
}

$source = json_decode($image->images);
$thumb = $sizes[$index];
?>
<a class="speasyimagegallery-gallery-item" href="<?php echo $source->original; ?>" data-title="<?php echo $image->title; ?>"
    data-desc="<?php echo ($image->description) ? strip_tags($image->description) : ''; ?>">
    <div>
        <img src="<?php echo $source->$thumb; ?>" title="<?php echo $image->title; ?>" alt="<?php echo $image->alt; ?>">
        <div class="speasyimagegallery-gallery-item-content">
            <span class="speasyimagegallery-gallery-item-title"><?php echo $image->title; ?></span>
        </div>
    </div>
</a>
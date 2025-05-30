<?php
/**
* @package com_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2024 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/


// No direct access
defined('_JEXEC') or die('Restricted access');

extract($displayData);

$source = json_decode($image->images);
?>
<a class="speasyimagegallery-gallery-item" href="<?php echo $source->original; ?>" data-title="<?php echo $image->title; ?>"
    data-desc="<?php echo ($image->description) ? htmlspecialchars($image->description) : ''; ?>">
    <div>
        <img src="<?php echo $source->thumb; ?>" title="<?php echo $image->title; ?>" alt="<?php echo $image->alt; ?>">
        <div class="speasyimagegallery-gallery-item-content">
            <span class="speasyimagegallery-gallery-item-title"><?php echo $image->title; ?></span>
            <?php if($params->get('album_show_desc', false) && $image->description) { ?>
            <span class="speasyimagegallery-gallery-item-title"><small><?php echo $image->description; ?></small></span>
            <?php } ?>
        </div>
    </div>
</a>
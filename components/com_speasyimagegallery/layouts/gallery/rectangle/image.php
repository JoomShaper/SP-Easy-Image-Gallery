<?php
/**
 * @package com_speasyimagegallery
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
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
    <a class="speasyimagegallery-gallery-item" href="<?php echo htmlspecialchars($source->original, ENT_QUOTES, 'UTF-8'); ?>" data-title="<?php echo htmlspecialchars($image->title, ENT_QUOTES, 'UTF-8'); ?>"
        data-desc="<?php echo ($image->description) ? htmlspecialchars($image->description, ENT_QUOTES, 'UTF-8') : ''; ?>">
        <div>
            <img src="<?php echo htmlspecialchars($source->$thumb, ENT_QUOTES, 'UTF-8'); ?>" title="<?php echo htmlspecialchars($image->title, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($image->alt, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="speasyimagegallery-gallery-item-content">
                <span class="speasyimagegallery-gallery-item-title"><?php echo htmlspecialchars($image->title, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>
    </a>
</div>
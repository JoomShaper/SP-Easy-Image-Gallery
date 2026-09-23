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
<a class="speasyimagegallery-gallery-item" href="<?php echo htmlspecialchars($source->original, ENT_QUOTES, 'UTF-8'); ?>" data-title="<?php echo htmlspecialchars($image->title, ENT_QUOTES, 'UTF-8'); ?>"
    data-desc="<?php echo ($image->description) ? htmlspecialchars($image->description, ENT_QUOTES, 'UTF-8') : ''; ?>">
    <div>
        <img src="<?php echo htmlspecialchars($source->$thumb, ENT_QUOTES, 'UTF-8'); ?>" title="<?php echo htmlspecialchars($image->title, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($image->alt, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="speasyimagegallery-gallery-item-content">
            <span class="speasyimagegallery-gallery-item-title"><?php echo htmlspecialchars($image->title, ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
    </div>
</a>
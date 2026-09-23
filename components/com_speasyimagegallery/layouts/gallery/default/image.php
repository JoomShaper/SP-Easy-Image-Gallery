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

$source = json_decode($image->images);
?>
<a class="speasyimagegallery-gallery-item" href="<?php echo htmlspecialchars($source->original, ENT_QUOTES, 'UTF-8'); ?>" data-title="<?php echo htmlspecialchars($image->title, ENT_QUOTES, 'UTF-8'); ?>"
    data-desc="<?php echo ($image->description) ? htmlspecialchars($image->description, ENT_QUOTES, 'UTF-8') : ''; ?>">
    <div>
        <img src="<?php echo htmlspecialchars($source->thumb, ENT_QUOTES, 'UTF-8'); ?>" title="<?php echo htmlspecialchars($image->title, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($image->alt, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="speasyimagegallery-gallery-item-content">
            <span class="speasyimagegallery-gallery-item-title"><?php echo htmlspecialchars($image->title, ENT_QUOTES, 'UTF-8'); ?></span>
            <?php if($params->get('album_show_desc', false) && $image->description) { ?>
            <span class="speasyimagegallery-gallery-item-title"><small><?php echo htmlspecialchars($image->description, ENT_QUOTES, 'UTF-8'); ?></small></span>
            <?php } ?>
        </div>
    </div>
</a>
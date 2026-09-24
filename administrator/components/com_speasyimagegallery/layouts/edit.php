<?php
/**
* @package com_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2025 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use JoomShaper\Component\Speasyimagegallery\Administrator\Helper\SpeasyimagegalleryHelper;

// No direct access
defined('_JEXEC') or die('Restricted access');

$image = $displayData['image'] ?? null;
if (!$image) {
	return;
}

$source = !empty($image->images) ? json_decode($image->images) : null;
$originalPath = (!empty($source) && !empty($source->original)) ? Uri::root(true) . '/' . htmlspecialchars($source->original, ENT_QUOTES, 'UTF-8') : '';

$isLegacy = SpeasyimagegalleryHelper::getVersion() < 4;
$row_css_class = $isLegacy ? 'row-fluid' : 'row';
$col_image_class = $isLegacy ? 'span6 hidden-phone' : 'col-lg-6 d-none d-md-block';
$col_form_class = $isLegacy ? 'span5 offset1' : 'col-lg-6';

?>

<div class="clearfix">
	<div class="<?php echo $row_css_class; ?>">
		<div class="<?php echo $col_image_class; ?>">
			<?php if (!empty($originalPath)) : ?>
				<a href="<?php echo $originalPath; ?>" target="_blank"><img src="<?php echo $originalPath; ?>" alt="<?php echo htmlspecialchars($image->alt ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid"></a>
			<?php endif; ?>
		</div>
		<div class="<?php echo $col_form_class; ?>">
			<div class="control-group mb-3">
				<div class="control-label mb-1">
					<label for="speasyimagegallery-image-field-title">
						<?php echo Text::_('COM_SPEASYIMAGEGALLERY_EDIT_IMAGE_TITLE'); ?>
					</label>
				</div>
				<div class="controls">
					<input type="text" class="form-control" id="speasyimagegallery-image-field-title" value="<?php echo htmlspecialchars($image->title ?? '', ENT_QUOTES, 'UTF-8'); ?>">
				</div>
			</div>

			<div class="control-group mb-3">
				<div class="control-label mb-1">
					<label for="speasyimagegallery-image-field-alt">
						<?php echo Text::_('COM_SPEASYIMAGEGALLERY_EDIT_IMAGE_ALT'); ?>
					</label>
				</div>
				<div class="controls">
					<input type="text" class="form-control" id="speasyimagegallery-image-field-alt" value="<?php echo htmlspecialchars($image->alt ?? '', ENT_QUOTES, 'UTF-8'); ?>">
				</div>
			</div>

			<div class="control-group mb-3">
				<div class="control-label mb-1">
					<label for="speasyimagegallery-image-field-desc">
						<?php echo Text::_('COM_SPEASYIMAGEGALLERY_EDIT_IMAGE_DESC'); ?>
					</label>
				</div>
				<div class="controls">
					<textarea class="form-control" id="speasyimagegallery-image-field-desc" rows="6"><?php echo htmlspecialchars($image->description ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
				</div>
			</div>

			<div class="control-group">
				<button type="button" class="btn btn-success btn-lg w-100" id="btn-save-image-settings" data-image="<?php echo (int) $image->id; ?>"><i class="fa fa-save"></i> <?php echo Text::_('COM_SPEASYIMAGEGALLERY_EDIT_SAVE'); ?></button>
			</div>
		</div>
	</div>
</div>

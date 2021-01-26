<?php
/**
* @package com_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2021 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

// No direct access
defined('_JEXEC') or die('Restricted access');

$image = $displayData['image'];
$source = json_decode($image->images);

$row_css_class = SpeasyimagegalleryHelper::getVersion() < 4 ? 'row-fluid' : 'row';
$col_css_class = SpeasyimagegalleryHelper::getVersion() < 4 ? 'span' : 'col-lg-';

?>

<div class="clearfix">
	<div class="<?php echo $row_css_class; ?>">
		<div class="<?php echo $col_css_class ;?>6 hidden-phone">
			<a href="<?php echo Uri::root(true) . '/' . $source->original; ?>" target="_blank"><img src="<?php echo Uri::root(true) . '/' . $source->original; ?>" alt="<?php echo $image->alt; ?>" class="img-fluid"></a>
		</div>
		<div class="<?php echo $col_css_class ;?>5 offset1">
			<div class="control-group">
				<div class="control-label">
					<label for="speasyimagegallery-image-field-title">
						<?php echo Text::_('COM_SPEASYIMAGEGALLERY_EDIT_IMAGE_TITLE'); ?>
					</label>
				</div>
				<div class="controls">
					<input type="text" id="speasyimagegallery-image-field-title" value="<?php echo $image->title; ?>">
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<label for="speasyimagegallery-image-field-alt">
						<?php echo Text::_('COM_SPEASYIMAGEGALLERY_EDIT_IMAGE_ALT'); ?>
					</label>
				</div>
				<div class="controls">
					<input type="text" id="speasyimagegallery-image-field-alt" value="<?php echo $image->alt; ?>">
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<label for="speasyimagegallery-image-field-desc">
						<?php echo Text::_('COM_SPEASYIMAGEGALLERY_EDIT_IMAGE_DESC'); ?>
					</label>
				</div>
				<div class="controls">
					<textarea id="speasyimagegallery-image-field-desc" rows="8" ><?php echo $image->description; ?></textarea>
				</div>
			</div>

			<div class="control-group">
				<a href="#" class="btn btn-success btn-large btn-block" id="btn-save-image-settings" data-image="<?php echo $image->id; ?>"><i class="fa fa-save"></i> <?php echo Text::_('COM_SPEASYIMAGEGALLERY_EDIT_SAVE'); ?></a>
			</div>
		</div>
	</div>
</div>

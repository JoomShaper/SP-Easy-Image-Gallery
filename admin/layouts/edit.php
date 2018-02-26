<?php
/**
* @package com_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

$image = $displayData['image'];
$source = json_decode($image->images);

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<div class="clearfix">
  <div class="row-fluid">
    <div class="span6 hidden-phone">
      <a href="<?php echo JURI::root(true) . '/' . $source->original; ?>" target="_blank"><img src="<?php echo JURI::root(true) . '/' . $source->original; ?>" alt="<?php echo $image->alt; ?>"></a>
    </div>
    <div class="span5 offset1">
      <div class="control-group">
        <div class="control-label">
          <label for="speasyimagegallery-image-field-title">
            <?php echo JText::_('COM_SPEASYIMAGEGALLERY_EDIT_IMAGE_TITLE'); ?>
          </label>
        </div>
        <div class="controls">
          <input type="text" id="speasyimagegallery-image-field-title" value="<?php echo $image->title; ?>">
        </div>
      </div>

      <div class="control-group">
        <div class="control-label">
          <label for="speasyimagegallery-image-field-alt">
            <?php echo JText::_('COM_SPEASYIMAGEGALLERY_EDIT_IMAGE_ALT'); ?>
          </label>
        </div>
        <div class="controls">
          <input type="text" id="speasyimagegallery-image-field-alt" value="<?php echo $image->alt; ?>">
        </div>
      </div>

      <div class="control-group">
        <div class="control-label">
          <label for="speasyimagegallery-image-field-desc">
            <?php echo JText::_('COM_SPEASYIMAGEGALLERY_EDIT_IMAGE_DESC'); ?>
          </label>
        </div>
        <div class="controls">
          <textarea id="speasyimagegallery-image-field-desc" rows="8" cols="80"><?php echo $image->description; ?></textarea>
        </div>
      </div>

      <div class="control-group">
        <a href="#" class="btn btn-success btn-large btn-block" id="btn-save-image-settings" data-image="<?php echo $image->id; ?>"><i class="fa fa-save"></i> <?php echo JText::_('COM_SPEASYIMAGEGALLERY_EDIT_SAVE'); ?></a>
      </div>
    </div>
  </div>
</div>

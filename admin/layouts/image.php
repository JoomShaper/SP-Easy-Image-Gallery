<?php
/**
* @package com_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

$image = $displayData['image'];
$sources = json_decode($image->images);

// No direct access
defined('_JEXEC') or die('Restricted access');
?>
<div class="sp-tr speasyimagegallery-singe-image clearfix" id="<?php echo $image->id; ?>">

  <div style="width: 5%;" class="order nowrap center hidden-phone">
    <span class="sortable-handler" style="cursor: move;">
      <span class="icon-menu"></span>
    </span>
  </div>

  <div style="width: 15%;" class="center">
    <img src="<?php echo JURI::root(true) . '/' . $sources->mini; ?>" alt="<?php echo $image->alt; ?>" width="64">
  </div>

  <div style="width: 25%;" class="has-context">
    <div class="pull-left break-word">
      <a class="speasyimagegallery-image-title" href="<?php echo JURI::root(true) . '/' . $sources->original; ?>" target="_blank"><?php echo $image->title; ?></a>
      <span class="speasyimagegallery-image-filename"><?php echo $image->filename; ?></span>
      <div class="speasyimagegallery-image-tools">
        <a href="#" class="speasyimagegallery-edit-image" data-id="<?php echo $image->id; ?>"><i class="fa fa-edit"></i> <?php echo JText::_('COM_SPEASYIMAGEGALLERY_IMAGE_EDIT'); ?></a>
        <a href="#" class="speasyimagegallery-delete-image"><i class="fa fa-times"></i> <?php echo JText::_('COM_SPEASYIMAGEGALLERY_IMAGE_DELETE'); ?></a>
      </div>
    </div>
  </div>

  <div style="width: 15%;" class="center">
    <?php echo JHtml::_('date', $image->created, JText::_('DATE_FORMAT_LC4')); ?>
  </div>

  <div style="width: 15%;" class="center hidden-phone">
    <?php
    $author = JFactory::getUser((int) $image->created_by);
    ?>
    <a class="hasTooltip" href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id=' . (int) $image->created_by); ?>" title="<?php echo JText::_('JAUTHOR'); ?>">
    <?php echo $this->escape($author->username); ?></a>
  </div>

  <div style="width: 10%;" class="center hidden-phone">
    <?php if($image->state) { ?>
      <a class="btn btn-micro btn-change-state active" href="javascript:void(0);" data-state="enabled"><span class="icon-publish"></span></a>
    <?php } else { ?>
      <a class="btn btn-change-state btn-micro" href="javascript:void(0);" data-state="disabled"><span class="icon-unpublish"></span></a>
    <?php } ?>
  </div>

  <div style="width: 10%;" class="center hidden-phone">
    <?php echo $image->id; ?>
  </div>
</div>

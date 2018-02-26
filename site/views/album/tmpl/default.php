<?php
/**
* @package com_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::_('jquery.framework');
$doc = JFactory::getDocument();
$doc->addStylesheet( JURI::base(true) . '/components/com_speasyimagegallery/assets/css/style-min.css' );
$doc->addScript( JURI::base(true) . '/components/com_speasyimagegallery/assets/js/script-min.js' );
$layout = $this->params->get('layout', 'default');
$show_title = $this->params->get('show_title', 1);
$show_desc = $this->params->get('show_desc', 1);
$show_count = $this->params->get('show_count', 1);
$gutter = $this->params->get('gutter', 20)/2;
$gutter_sm = $this->params->get('gutter_sm', 15)/2;
$gutter_xs = $this->params->get('gutter_xs', 10)/2;

// Javascript
$js = <<<JS
jQuery(function ($) {
	$(document).on('click', '.speasyimagegallery-gallery-item', function(event) {
		event.preventDefault();
    $(this).speasyimagegallery({
      showTitle: {$show_title},
      showDescription: {$show_desc},
      showCounter: {$show_count}
    });
	});
})
JS;
$doc->addScriptDeclaration($js);

// Stylesheet
if($gutter || $gutter_sm || $gutter_xs) {
  $css = '';
  if($gutter) {
    $css .= '.speasyimagegallery-row {margin: -' . $gutter . 'px;}';
    $css .= '.speasyimagegallery-row > div > .speasyimagegallery-gallery-item {padding: ' . $gutter . 'px;}';
  }

  if($gutter_sm) {
    $css .= '@media only screen and (max-width : 992px) {';
    $css .= '.speasyimagegallery-row {margin: -' . $gutter_sm . 'px;}';
    $css .= '.speasyimagegallery-row > div > .speasyimagegallery-gallery-item {padding: ' . $gutter_sm . 'px;}';
    $css .= '}';
  }

  if($gutter_xs) {
    $css .= '@media only screen and (max-width : 768px) {';
    $css .= '.speasyimagegallery-row {margin: -' . $gutter_xs . 'px;}';
    $css .= '.speasyimagegallery-row > div > .speasyimagegallery-gallery-item {padding: ' . $gutter_xs . 'px;}';
    $css .= '}';
  }

  $doc->addStyleDeclaration($css);
}
?>

<?php if ($this->params->get('show_page_heading', 1)) : ?>
  <div class="page-header">
    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
  </div>
<?php endif; ?>
<?php if($this->item->description): ?>
  <div class="speasyimagegallery-album-description"><?php echo $this->item->description; ?></div>
<?php endif; ?>

<?php
if(count($this->item->images)) {
  ?>
  <div class="speasyimagegallery-gallery clearfix">
    <?php echo JLayoutHelper::render('gallery.'. $layout .'.row', array('images'=>$this->item->images, 'params'=>$this->params)); ?>
  </div>
  <?php
} else {
  echo '<div class="alert">' . JText::_('COM_SPEASYIMAGEGALLERY_NO_IMAGES') . '</div>';
}

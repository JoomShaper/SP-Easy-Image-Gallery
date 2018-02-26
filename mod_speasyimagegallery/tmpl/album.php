<?php
/**
* @package com_speasyimagegallery
* @subpackage mod_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

// No direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('jquery.framework');
$app = JFactory::getApplication();
$option = $app->input->get('option', '', 'STRING');
$view = $app->input->get('view', '', 'STRING');
$doc = JFactory::getDocument();
$doc->addStylesheet( JURI::base(true) . '/components/com_speasyimagegallery/assets/css/style-min.css' );
$doc->addScript( JURI::base(true) . '/components/com_speasyimagegallery/assets/js/script-min.js' );
$layout = $params->get('album_layout', 'default');
$show_title = $params->get('show_title', 1);
$show_desc = $params->get('show_desc', 1);
$show_count = $params->get('show_count', 1);
$gutter = $params->get('album_gutter', 20)/2;
$gutter_sm = $params->get('album_gutter_sm', 15)/2;
$gutter_xs = $params->get('album_gutter_xs', 10)/2;
$id = '#mod-speasyimagegallery-' . $module->id;

// Javascript
$js = <<<JS
jQuery(function($) {
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

if((($option != 'com_speasyimagegallery') && ($view != 'album')) || (($option == 'com_speasyimagegallery') && ($view == 'albums'))) {
  $doc->addScriptDeclaration($js);
}

// Stylesheet
if($gutter || $gutter_sm || $gutter_xs) {
  $css = '';
  if($gutter) {
    $css .= $id . ' .speasyimagegallery-row {margin: -' . $gutter . 'px;}';
    $css .= $id . ' .speasyimagegallery-row > div > .speasyimagegallery-gallery-item {padding: ' . $gutter . 'px;}';
  }

  if($gutter_sm) {
    $css .= '@media only screen and (max-width : 992px) {';
    $css .= $id . ' .speasyimagegallery-row {margin: -' . $gutter_sm . 'px;}';
    $css .= $id . ' .speasyimagegallery-row > div > .speasyimagegallery-gallery-item {padding: ' . $gutter_sm . 'px;}';
    $css .= '}';
  }

  if($gutter_xs) {
    $css .= '@media only screen and (max-width : 768px) {';
    $css .= $id . ' .speasyimagegallery-row {margin: -' . $gutter_xs . 'px;}';
    $css .= $id . ' .speasyimagegallery-row > div > .speasyimagegallery-gallery-item {padding: ' . $gutter_xs . 'px;}';
    $css .= '}';
  }

  $doc->addStyleDeclaration($css);
}
?>

<div class="mod-speasyimagegallery" id="mod-speasyimagegallery-<?php echo $module->id; ?>">
  <?php
  if(count($images)) {
    ?>
    <div class="speasyimagegallery-gallery clearfix">
      <?php
      $layout = new JLayoutFile('gallery.'. $layout .'.row', JPATH_ROOT .'/modules/mod_speasyimagegallery/layouts');
      echo $layout->render(array('images'=>$images, 'params'=>$params));
      ?>
    </div>
    <?php
  } else {
    echo '<div class="alert">' . JText::_('COM_SPEASYIMAGEGALLERY_NO_IMAGES') . '</div>';
  }
  ?>
</div>

<?php
/**
* @package com_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2021 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;

$doc = Factory::getDocument();
$doc->addStylesheet( Uri::base(true) . '/components/com_speasyimagegallery/assets/css/style-min.css' );
$input = Factory::getApplication()->input;

$col = 'speasyimagegallery-col-md-' . $this->params->get('albums_column', 4);
$col .= ' speasyimagegallery-col-sm-' . $this->params->get('albums_column_sm', 3);
$col .= ' speasyimagegallery-col-xs-' . $this->params->get('albums_column_xs', 2);
$gutter = $this->params->get('albums_gutter', 20)/2;
$gutter_sm = $this->params->get('albums_gutter_sm', 15)/2;
$gutter_xs = $this->params->get('albums_gutter_xs', 10)/2;

// Stylesheet
if($gutter || $gutter_sm || $gutter_xs) {
  $css = '';
  if($gutter) {
    $css .= '.speasyimagegallery-row {margin: -' . $gutter . 'px;}';
    $css .= '.speasyimagegallery-row .speasyimagegallery-album {padding: ' . $gutter . 'px;}';
  }

  if($gutter_sm) {
    $css .= '@media only screen and (max-width : 992px) {';
    $css .= '.speasyimagegallery-row {margin: -' . $gutter_sm . 'px;}';
    $css .= '.speasyimagegallery-row .speasyimagegallery-album {padding: ' . $gutter_sm . 'px;}';
    $css .= '}';
  }

  if($gutter_xs) {
    $css .= '@media only screen and (max-width : 768px) {';
    $css .= '.speasyimagegallery-row {margin: -' . $gutter_xs . 'px;}';
    $css .= '.speasyimagegallery-row .speasyimagegallery-album {padding: ' . $gutter_xs . 'px;}';
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

<?php
if(count($this->items)) {
  ?>
  <div class="speasyimagegallery-albums">
    <div class="speasyimagegallery-row clearfix">
      <?php foreach ($this->items as $key => $this->item) { ?>
        <?php
        $cover = 'thumb.' . File::getExt(basename($this->item->image));
        $Itemid = ($input->get('Itemid', 0, 'INT'))? '&Itemid=' . $input->get('Itemid', 0, 'INT') : '';
        $link = 'index.php?option=com_speasyimagegallery&view=album&id=' . $this->item->id . ':' . $this->item->alias . $Itemid;
        ?>
        <div class="<?php echo $col; ?>">
          <div class="speasyimagegallery-album">
            <div>
              <a href="<?php echo Route::_($link); ?>">
                <img src="images/speasyimagegallery/albums/<?php echo $this->item->id; ?>/<?php echo $cover; ?>" alt="<?php echo $this->item->title; ?>">
                <div class="speasyimagegallery-album-info">
                  <span class="speasyimagegallery-album-title"><?php echo $this->item->title; ?></span>
                  <div class="speasyimagegallery-album-meta clearfix">
                    <span class="speasyimagegallery-album-meta-count"><?php echo $this->item->count; ?> <?php echo ($this->item->count > 1) ? Text::_('COM_SPEASYIMAGEGALLERY_PHOTOS') : Text::_('COM_SPEASYIMAGEGALLERY_PHOTO'); ?></span>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>

  <?php if ($this->pagination->pagesTotal > 1) : ?>
    <div class="pagination-wrapper">      
      <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
  <?php endif; ?>
  <?php
} else {
  echo '<div class="alert">' . Text::_('COM_SPEASYIMAGEGALLERY_NO_ALBUMS') . '</div>';
}

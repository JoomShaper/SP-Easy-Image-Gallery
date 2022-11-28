<?php
/**
* @package com_speasyimagegallery
* @subpackage mod_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2021 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;

// No direct access
defined('_JEXEC') or die('Restricted access');

HTMLHelper::_('jquery.framework');
$doc = Factory::getDocument();
$doc->addStylesheet( Uri::base(true) . '/components/com_speasyimagegallery/assets/css/style-min.css' );

$col = 'speasyimagegallery-col-md-' . $params->get('albums_column', 4);
$col .= ' speasyimagegallery-col-sm-' . $params->get('albums_column_sm', 3);
$col .= ' speasyimagegallery-col-xs-' . $params->get('albums_column_xs', 2);
$gutter = $params->get('albums_gutter', 20)/2;
$gutter_sm = $params->get('albums_gutter_sm', 15)/2;
$gutter_xs = $params->get('albums_gutter_xs', 10)/2;
$id = '#mod-speasyimagegallery-' . $module->id;

// Stylesheet
if($gutter || $gutter_sm || $gutter_xs) {
  $css = '';
  if($gutter) {
    $css .= $id . ' .speasyimagegallery-row {margin: -' . $gutter . 'px;}';
    $css .= $id . ' .speasyimagegallery-row .speasyimagegallery-album {padding: ' . $gutter . 'px;}';
  }

  if($gutter_sm) {
    $css .= '@media only screen and (max-width : 992px) {';
    $css .= $id . ' .speasyimagegallery-row {margin: -' . $gutter_sm . 'px;}';
    $css .= $id . ' .speasyimagegallery-row .speasyimagegallery-album {padding: ' . $gutter_sm . 'px;}';
    $css .= '}';
  }

  if($gutter_xs) {
    $css .= '@media only screen and (max-width : 768px) {';
    $css .= $id . ' .speasyimagegallery-row {margin: -' . $gutter_xs . 'px;}';
    $css .= $id . ' .speasyimagegallery-row .speasyimagegallery-album {padding: ' . $gutter_xs . 'px;}';
    $css .= '}';
  }

  $doc->addStyleDeclaration($css);
}
?>

<div class="mod-speasyimagegallery" id="mod-speasyimagegallery-<?php echo $module->id; ?>">
	<?php
	if(count($albums)) {
	  ?>
    <div class="speasyimagegallery-albums">
      <div class="speasyimagegallery-row clearfix">
        <?php foreach ($albums as $key => $album) { ?>
          <?php
          $cover = 'thumb.' . File::getExt(basename($album->image));
          ?>
          <div class="<?php echo $col; ?>">
            <div class="speasyimagegallery-album">
              <div>
                <a href="<?php echo $album->url; ?>">
                  <img src="images/speasyimagegallery/albums/<?php echo $album->id; ?>/<?php echo $cover; ?>" alt="<?php echo $album->title; ?>">
                  <div class="speasyimagegallery-album-info">
                    <span class="speasyimagegallery-album-title"><?php echo $album->title; ?></span>
                    <div class="speasyimagegallery-album-meta clearfix">
                      <span class="speasyimagegallery-album-meta-count"><?php echo $album->count; ?> <?php echo ($album->count > 1) ? Text::_('MOD_SPEASYIMAGEGALLERY_PHOTOS') : Text::_('MOD_SPEASYIMAGEGALLERY_PHOTO'); ?></span>
	                    <?php if(!empty($album->description)): ;?>
                            <br>
                            <span class="speasyimagegallery-album-meta-count" style="font-weight: normal;"><small><?php echo $album->description ;?></small></span>
	                    <?php endif; ?>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
	  <?php
	} else {
	  echo '<div class="alert">' . Text::_('MOD_SPEASYIMAGEGALLERY_NO_ALBUMS') . '</div>';
	}
	?>
</div>

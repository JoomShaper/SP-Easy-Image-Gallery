<?php
/**
 * @package     SP Easy Image Gallery
 * @subpackage  com_speasyimagegallery
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useStyle('com_speasyimagegallery.site-css')
   ->useScript('com_speasyimagegallery.site-gallery');

$layout     = $this->params->get('layout', 'default');
$show_title = $this->params->get('show_title', 1);
$show_desc  = $this->params->get('show_desc', 1);
$show_count = $this->params->get('show_count', 1);
$gutter     = (float) $this->params->get('gutter', 20) / 2;
$gutter_sm  = (float) $this->params->get('gutter_sm', 15) / 2;
$gutter_xs  = (float) $this->params->get('gutter_xs', 10) / 2;

// Gallery Attributes
$gallery_attribs = 'data-showtitle="' . $show_title . '" data-showdescription="' . $show_desc . '" data-showcounter="' . $show_count . '"';

// Inline Stylesheet for dynamic gutters
if ($gutter || $gutter_sm || $gutter_xs) {
    $css = '';
    if ($gutter) {
        $css .= '.speasyimagegallery-row {margin: -' . $gutter . 'px;}';
        $css .= '.speasyimagegallery-row > div > .speasyimagegallery-gallery-item {padding: ' . $gutter . 'px;}';
    }

    if ($gutter_sm) {
        $css .= '@media only screen and (max-width : 992px) {';
        $css .= '.speasyimagegallery-row {margin: -' . $gutter_sm . 'px;}';
        $css .= '.speasyimagegallery-row > div > .speasyimagegallery-gallery-item {padding: ' . $gutter_sm . 'px;}';
        $css .= '}';
    }

    if ($gutter_xs) {
        $css .= '@media only screen and (max-width : 768px) {';
        $css .= '.speasyimagegallery-row {margin: -' . $gutter_xs . 'px;}';
        $css .= '.speasyimagegallery-row > div > .speasyimagegallery-gallery-item {padding: ' . $gutter_xs . 'px;}';
        $css .= '}';
    }

    $this->document->addStyleDeclaration($css);
}
?>

<div class="speasyimagegallery-album-view" <?php echo $gallery_attribs; ?>>
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
		<div class="page-header">
			<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
		</div>
	<?php endif; ?>

	<?php if ($this->params->get('show_album_description', 1) && !empty($this->item->description)) : ?>
		<div class="speasyimagegallery-album-description mb-4">
			<?php echo $this->item->description; ?>
		</div>
	<?php endif; ?>

	<?php
	if (!empty($this->item->images)) :
		echo LayoutHelper::render('gallery.' . $layout . '.row', [
			'images' => $this->item->images,
			'params' => $this->params,
			'item'   => $this->item
		], JPATH_ROOT . '/components/com_speasyimagegallery/layouts');
	else : ?>
		<div class="alert alert-info">
			<?php echo Text::_('COM_SPEASYIMAGEGALLERY_NO_IMAGES'); ?>
		</div>
	<?php endif; ?>
</div>

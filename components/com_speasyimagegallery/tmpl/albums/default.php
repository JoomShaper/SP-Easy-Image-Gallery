<?php
/**
 * @package     SP Easy Image Gallery
 * @subpackage  com_speasyimagegallery
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\File;

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useStyle('com_speasyimagegallery.site-css');

$input = Factory::getApplication()->input;

$col = 'speasyimagegallery-col-md-' . (int) $this->params->get('albums_column', 4);
$col .= ' speasyimagegallery-col-sm-' . (int) $this->params->get('albums_column_sm', 3);
$col .= ' speasyimagegallery-col-xs-' . (int) $this->params->get('albums_column_xs', 2);

$gutter    = (float) $this->params->get('albums_gutter', 20) / 2;
$gutter_sm = (float) $this->params->get('albums_gutter_sm', 15) / 2;
$gutter_xs = (float) $this->params->get('albums_gutter_xs', 10) / 2;

// Inline Stylesheet for dynamic gutters
if ($gutter || $gutter_sm || $gutter_xs) {
    $css = '';
    if ($gutter) {
        $css .= '.speasyimagegallery-row {margin: -' . $gutter . 'px;}';
        $css .= '.speasyimagegallery-row .speasyimagegallery-album {padding: ' . $gutter . 'px;}';
    }

    if ($gutter_sm) {
        $css .= '@media only screen and (max-width : 992px) {';
        $css .= '.speasyimagegallery-row {margin: -' . $gutter_sm . 'px;}';
        $css .= '.speasyimagegallery-row .speasyimagegallery-album {padding: ' . $gutter_sm . 'px;}';
        $css .= '}';
    }

    if ($gutter_xs) {
        $css .= '@media only screen and (max-width : 768px) {';
        $css .= '.speasyimagegallery-row {margin: -' . $gutter_xs . 'px;}';
        $css .= '.speasyimagegallery-row .speasyimagegallery-album {padding: ' . $gutter_xs . 'px;}';
        $css .= '}';
    }

    $this->document->addStyleDeclaration($css);
}
?>

<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<div class="page-header">
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
<?php endif; ?>

<?php if (!empty($this->items)) : ?>
	<div class="speasyimagegallery-albums">
		<div class="speasyimagegallery-row clearfix">
			<?php foreach ($this->items as $item) : ?>
				<?php
				$cover  = 'thumb.' . File::getExt(basename((string) $item->image));
				$itemId = $input->getInt('Itemid', 0);
				$itemidParam = $itemId > 0 ? '&Itemid=' . $itemId : '';
				$link   = 'index.php?option=com_speasyimagegallery&view=album&id=' . (int) $item->id . ':' . $item->alias . $itemidParam;
				$imgSrc = Uri::root(true) . '/images/speasyimagegallery/albums/' . (int) $item->id . '/' . $cover;
				?>
				<div class="<?php echo $col; ?>">
					<div class="speasyimagegallery-album">
						<div>
							<a href="<?php echo Route::_($link); ?>">
								<img src="<?php echo $imgSrc; ?>" alt="<?php echo $this->escape($item->title); ?>">
								<div class="speasyimagegallery-album-info">
									<span class="speasyimagegallery-album-title"><?php echo $this->escape($item->title); ?></span>
									<div class="speasyimagegallery-album-meta clearfix">
										<span class="speasyimagegallery-album-meta-count">
											<?php echo (int) $item->count; ?> <?php echo ((int) $item->count > 1) ? Text::_('COM_SPEASYIMAGEGALLERY_PHOTOS') : Text::_('COM_SPEASYIMAGEGALLERY_PHOTO'); ?>
										</span>
									</div>
								</div>
							</a>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<?php if ($this->pagination && $this->pagination->pagesTotal > 1) : ?>
		<div class="pagination-wrapper mt-4">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php endif; ?>
<?php else : ?>
	<div class="alert alert-info"><?php echo Text::_('COM_SPEASYIMAGEGALLERY_NO_ALBUMS'); ?></div>
<?php endif; ?>

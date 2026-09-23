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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use JoomShaper\Component\Speasyimagegallery\Administrator\Helper\SpeasyimagegalleryHelper;

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useStyle('com_speasyimagegallery.admin-css');

$user      = Factory::getApplication()->getIdentity();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering', 'a.id'));
$listDirn  = $this->escape($this->state->get('list.direction', 'desc'));
$saveOrder = $listOrder === 'a.ordering';

if ($saveOrder && !empty($this->items)) {
    $saveOrderingUrl = 'index.php?option=com_speasyimagegallery&task=albums.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
    HTMLHelper::_('draggablelist.draggable');
}
?>

<form action="<?php echo Route::_('index.php?option=com_speasyimagegallery&view=albums'); ?>" method="post" id="adminForm" name="adminForm">
	<div id="j-main-container">
		<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

		<?php if (empty($this->items)) : ?>
			<div class="alert alert-info">
				<span class="icon-info-circle" aria-hidden="true"></span>
				<span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
				<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped" id="albumList">
				<thead>
					<tr>
						<th class="w-1 text-center d-none d-md-table-cell">
							<?php echo HTMLHelper::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder); ?>
						</th>
						<th class="w-1 text-center">
							<?php echo HTMLHelper::_('grid.checkall'); ?>
						</th>
						<th class="w-10 text-center">
							<?php echo Text::_('COM_SPEASYIMAGEGALLERY_HEADING_IMAGE'); ?>
						</th>
						<th>
							<?php echo HTMLHelper::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<th class="w-5 text-center d-none d-md-table-cell">
							<?php echo HTMLHelper::_('grid.sort', 'JFEATURED', 'a.featured', $listDirn, $listOrder); ?>
						</th>
						<th class="w-10 d-none d-md-table-cell">
							<?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
						</th>
						<th class="w-10 d-none d-md-table-cell">
							<?php echo HTMLHelper::_('grid.sort', 'JAUTHOR', 'a.created_by', $listDirn, $listOrder); ?>
						</th>
						<th class="w-10 d-none d-md-table-cell">
							<?php echo HTMLHelper::_('grid.sort', 'COM_SPEASYIMAGEGALLERY_HEADING_DATE_CREATED', 'a.created', $listDirn, $listOrder); ?>
						</th>
						<th class="w-5 d-none d-md-table-cell">
							<?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
						</th>
						<th class="w-5 text-center d-none d-md-table-cell">
							<?php echo HTMLHelper::_('grid.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
						</th>
						<th class="w-5 text-center">
							<?php echo HTMLHelper::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
						</th>
						<th class="w-1 text-center d-none d-md-table-cell">
							<?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<td colspan="12">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>

				<tbody <?php if ($saveOrder) : ?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="false" <?php endif; ?>>
					<?php foreach ($this->items as $i => $item) :
						$item->max_ordering = 0;
						$ordering   = ($listOrder === 'a.ordering');
						$canEdit    = $user->authorise('core.edit', 'com_speasyimagegallery.album.' . $item->id) || ($user->authorise('core.edit.own', 'com_speasyimagegallery.album.' . $item->id) && $item->created_by == $userId);
						$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
						$canChange  = $user->authorise('core.edit.state', 'com_speasyimagegallery.album.' . $item->id) && $canCheckin;
					?>
						<tr class="row<?php echo $i % 2; ?>" data-draggable-group="1">
							<td class="order text-center d-none d-md-table-cell">
								<?php
								$iconClass = '';
								if (!$canChange) {
									$iconClass = ' inactive';
								} elseif (!$saveOrder) {
									$iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::tooltipText('JORDERINGDISABLED');
								}
								?>
								<span class="sortable-handler<?php echo $iconClass; ?>">
									<span class="icon-menu" aria-hidden="true"></span>
								</span>
								<?php if ($canChange && $saveOrder) : ?>
									<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order" />
								<?php endif; ?>
							</td>
							<td class="text-center">
								<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
							</td>
							<td class="text-center">
								<?php
								$thumbPath = Uri::root(true) . '/images/speasyimagegallery/albums/' . $item->id . '/thumb.' . SpeasyimagegalleryHelper::getExt(basename($item->image));
								?>
								<img src="<?php echo $thumbPath; ?>" alt="" style="width: 48px; height: 48px; object-fit: cover; border-radius: 4px; border: 1px solid #dee2e6; background-color: #f8f9fa;">
							</td>
							<td>
								<?php if ($item->checked_out) : ?>
									<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'albums.', $canCheckin); ?>
								<?php endif; ?>

								<?php if ($canEdit) : ?>
									<a class="sp-easy-image-gallery-title fw-semibold text-decoration-none" href="<?php echo Route::_('index.php?option=com_speasyimagegallery&task=album.edit&id=' . $item->id); ?>">
										<?php echo $this->escape($item->title); ?>
									</a>
								<?php else : ?>
									<?php echo $this->escape($item->title); ?>
								<?php endif; ?>

								<div class="small text-muted">
									<?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
								</div>
								<?php if ($item->catid) : ?>
									<div class="small text-muted">
										<?php echo Text::_('JCATEGORY') . ': ' . $this->escape($item->category_title); ?>
									</div>
								<?php endif; ?>
							</td>
							<td class="text-center d-none d-md-table-cell">
								<?php
								$isFeatured = (bool) $item->featured;
								$task       = $isFeatured ? 'unfeature' : 'feature';
								$title      = $isFeatured ? Text::_('COM_SPEASYIMAGEGALLERY_FEATURED_ITEM') : Text::_('COM_SPEASYIMAGEGALLERY_NOTFEATURED_ITEM');
								$iconClass  = $isFeatured ? 'icon-star text-warning' : 'icon-circle text-muted';

								echo HTMLHelper::_(
									'link',
									Route::_('index.php?option=com_speasyimagegallery&task=albums.' . $task . '&cid[]=' . $item->id . '&' . Session::getFormToken() . '=1'),
									'<span class="' . $iconClass . '" aria-hidden="true"></span>',
									['title' => $title, 'class' => 'btn btn-sm btn-link']
								);
								?>
							</td>
							<td class="d-none d-md-table-cell">
								<?php echo $this->escape($item->access_title); ?>
							</td>
							<td class="small d-none d-md-table-cell">
								<a href="<?php echo Route::_('index.php?option=com_users&task=user.edit&id=' . (int) $item->created_by); ?>">
									<?php echo $this->escape($item->author_name); ?>
								</a>
							</td>
							<td class="nowrap small d-none d-md-table-cell">
								<?php echo $item->created > 0 ? HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC4')) : '-'; ?>
							</td>
							<td class="small nowrap d-none d-md-table-cell">
								<?php if ($item->language === '*') : ?>
									<?php echo Text::alt('JALL', 'language'); ?>
								<?php else : ?>
									<?php echo $item->language_title ? $this->escape($item->language_title) : Text::_('JUNDEFINED'); ?>
								<?php endif; ?>
							</td>
							<td class="text-center d-none d-md-table-cell">
								<span class="badge bg-info">
									<?php echo (int) $item->hits; ?>
								</span>
							</td>
							<td class="text-center">
								<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'albums.', $canChange); ?>
							</td>
							<td class="text-center d-none d-md-table-cell text-muted">
								<?php echo (int) $item->id; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>

<?php
/**
* @package com_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

JHtml::_('formbehavior.chosen', 'select');
jimport('joomla.filesystem.file');

$user		= JFactory::getUser();
$userId		= $user->get('id');

$listOrder = $this->escape($this->filter_order);
$listDirn = $this->escape($this->filter_order_Dir);
$saveOrder = $listOrder == 'a.ordering';

if ($saveOrder) {
	$saveOrderingUrl = 'index.php?option=com_speasyimagegallery&task=albums.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'albumList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
}
?>

<script type="text/javascript">
Joomla.orderTable = function() {
	table = document.getElementById("sortTable");
	direction = document.getElementById("directionTable");
	order = table.options[table.selectedIndex].value;
	if (order != '<?php echo $listOrder; ?>')
	{
		dirn = 'asc';
	} else {
		dirn = direction.options[direction.selectedIndex].value;
	}
	Joomla.tableOrdering(order, dirn, '');
}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_speasyimagegallery&view=albums'); ?>" method="post" id="adminForm" name="adminForm">
		<?php if (!empty( $this->sidebar)) : ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
			<div id="j-main-container" class="span10">
		<?php else : ?>
			<div id="j-main-container">
		<?php endif; ?>

		<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped" id="albumList">
				<thead>
				<tr>
					<th width="2%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder); ?>
					</th>
					<th width="2%" class="hidden-phone">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('COM_SPEASYIMAGEGALLERY_HEADING_IMAGE'); ?>
					</th>
					<th>
						<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone">
						<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone">
						<?php echo JHtml::_('grid.sort',  'JAUTHOR', 'a.created_by', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone">
						<?php echo JHtml::_('grid.sort', 'COM_SPEASYIMAGEGALLERY_HEADING_DATE_CREATED', 'a.created', $listDirn, $listOrder); ?>
					</th>
					<th width="5%" class="nowrap hidden-phone">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap hidden-phone">
						<?php echo JHtml::_('grid.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap hidden-phone">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
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

				<tbody>
					<?php if (!empty($this->items)) : ?>
						<?php foreach ($this->items as $i => $item) :
							$item->max_ordering = 0;
							$ordering   = ($listOrder == 'a.ordering');
							$canEdit    = $user->authorise('core.edit', 'com_sppagebuilder.page.' . $item->id) || ($user->authorise('core.edit.own',   'com_speasyimagegallery.album.' . $item->id) && $item->created_by == $userId);
							$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
							$canChange  = $user->authorise('core.edit.state', 'com_sppagebuilder.page.' . $item->id) && $canCheckin;
							$link = JRoute::_('index.php?option=com_speasyimagegallery&task=album.edit&id=' . $item->id);
						?>
							<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">
								<td class="order nowrap center hidden-phone">
									<?php
									$iconClass = '';
									if (!$canChange)
									{
										$iconClass = ' inactive';
									}
									elseif (!$saveOrder)
									{
										$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
									}
									?>
									<span class="sortable-handler<?php echo $iconClass ?>">
										<span class="icon-menu"></span>
									</span>
									<?php if ($canChange && $saveOrder) : ?>
										<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
									<?php endif; ?>
								</td>
								<td class="hidden-phone">
									<?php echo JHtml::_('grid.id', $i, $item->id); ?>
								</td>
								<td>
										<img src="<?php echo JURI::root(true) . '/images/speasyimagegallery/albums/' . $item->id . '/thumb.' . JFile::getExt(basename($item->image)); ?>" alt="" style="width: 64px; height: 64px; border: 1px solid #e5e5e5; background-color: #f5f5f5;">
								</td>
								<td>
									<?php if ($item->checked_out) : ?>
										<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'albums.', $canCheckin); ?>
									<?php endif; ?>

									<?php if ($canEdit) : ?>
										<a class="sp-pagebuilder-page-title" href="<?php echo JRoute::_('index.php?option=com_speasyimagegallery&task=album.edit&id='.$item->id);?>">
											<?php echo $this->escape($item->title); ?>
										</a>
									<?php else : ?>
										<?php echo $this->escape($item->title); ?>
									<?php endif; ?>

									<span class="small break-word">
										<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
									</span>
									<?php if($item->catid) : ?>
									<div class="small">
										<?php echo JText::_('JCATEGORY') . ': ' . $this->escape($item->category_title); ?>
									</div>
									<?php endif; ?>
								</td>
								<td class="hidden-phone">
									<?php echo $this->escape($item->access_title); ?>
								</td>
								<td class="small hidden-phone">
										<a class="hasTooltip" href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id=' . (int) $item->created_by); ?>" title="<?php echo JText::_('JAUTHOR'); ?>">
										<?php echo $this->escape($item->author_name); ?></a>
								</td>
								<td class="nowrap small hidden-phone">
									<?php
									echo $item->created > 0 ? JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')) : '-';
									?>
								</td>
								<td class="small nowrap hidden-phone">
									<?php if ($item->language == '*') : ?>
										<?php echo JText::alt('JALL', 'language'); ?>
									<?php else:?>
										<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
									<?php endif;?>
								</td>
								<td class="center hidden-phone">
									<span class="badge badge-info">
										<?php echo (int) $item->hits; ?>
									</span>
								</td>

								<td class="center">
									<?php echo JHtml::_('jgrid.published', $item->published, $i, 'albums.', $canChange);?>
								</td>

								<td align="center" class="hidden-phone">
									<?php echo $item->id; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>

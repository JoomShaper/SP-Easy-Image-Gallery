<?php
/**
 * @package     SP Easy Image Gallery
 * @subpackage  com_speasyimagegallery
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useStyle('com_speasyimagegallery.admin-css')
   ->useScript('com_speasyimagegallery.admin-album');

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

// Language strings
Text::script('COM_SPEASYIMAGEGALLERY_DELETE_IMAGE_CONFIRM');
Text::script('COM_SPEASYIMAGEGALLERY_IMAGE_UPLOADING');
Text::script('COM_SPEASYIMAGEGALLERY_MODAL_EDIT_IMAGE');

$albumId = (int) ($this->item->id ?? 0);
$this->document->addScriptDeclaration('var album_id = ' . $albumId . ';');
?>

<form action="<?php echo Route::_('index.php?option=com_speasyimagegallery&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" name="adminForm" id="adminForm" class="form-validate">
	<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="main-card">
		<div class="row">
			<div class="col-lg-9">
				<?php if ($albumId > 0) : ?>
					<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'images']); ?>
					<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'images', Text::_('COM_SPEASYIMAGEGALLERY_TAB_IMAGES', true)); ?>
					<?php echo LayoutHelper::render('images', ['total' => $this->total, 'images' => $this->images]); ?>
					<?php echo HTMLHelper::_('uitab.endTab'); ?>
				<?php else : ?>
					<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'description']); ?>
				<?php endif; ?>

				<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'description', Text::_('COM_SPEASYIMAGEGALLERY_TAB_DESCRIPTION', true)); ?>
				<div class="p-3">
					<?php echo $this->form->getInput('description'); ?>
				</div>
				<?php echo HTMLHelper::_('uitab.endTab'); ?>

				<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'publishing', Text::_('COM_SPEASYIMAGEGALLERY_TAB_PUBLISHING', true)); ?>
				<div class="p-3">
					<?php echo $this->form->renderFieldset('publishing'); ?>
				</div>
				<?php echo HTMLHelper::_('uitab.endTab'); ?>

				<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
			</div>

			<div class="col-lg-3">
				<fieldset class="form-vertical">
					<?php echo $this->form->renderFieldset('info'); ?>
				</fieldset>
			</div>
		</div>
	</div>

	<input type="hidden" name="task" value="album.edit" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="album_id" value="<?php echo $albumId; ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>

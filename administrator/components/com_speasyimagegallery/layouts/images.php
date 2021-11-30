<?php
/**
 * @package	com_speasyimagegallery
 * @author	JoomShaper http://www.joomshaper.com
 * @copyright	Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

$images = $displayData['images'];
$total = $displayData['total'];
$count = count($images);

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<div class="speasyimagegallery-toolbar">
	<a href="#" class="btn btn-primary" id="speasyimagegallery-btn-upload-images">
	<i class="fa fa-upload"></i> <?php echo Text::_('COM_SPEASYIMAGEGALLERY_IMAGES_MANAGER_UPLOAD_IMAGES'); ?>
	</a>
</div>

<div class="speasyimagegallery-images-list<?php echo ($count) ? '' : ' speasyimagegallery-no-images'; ?> clearfix">
	<div class="speasyimagegallery-images-empty">
		<div>
			<i class="fa fa-upload fa-3x"></i>
			<h3 class="speasyimagegallery-images-empty-title">
				<?php echo Text::_('COM_SPEASYIMAGEGALLERY_IMAGES_MANAGER_DRAG_DROP_UPLOAD'); ?>
			</h3>
			<div>
				<input type="file" id="speasyimagegallery-images-input-file" multiple="multiple" style="display:none" accept="image/*">
				<a 
					href="#" 
					id="speasyimagegallery-upload-images-empty" 
					class="btn btn-primary btn-large"><?php echo Text::_('COM_SPEASYIMAGEGALLERY_IMAGES_MANAGER_OR_SELECT'); ?>
				</a>
			</div>
		</div>
	</div>
	<div class="speasyimagegallery-images-wrapper">

		<div class="sp-table">
			<div class="sp-thead clearfix">
				<div style="width: 5%;" class="nowrap center hidden-phone">
					<div>
						<span class="icon-menu-2"></span>
					</div>
				</div>
				<div style="width: 2%;" class="nowrap center hidden-phone">
					<div>
						<input type="checkbox" class="speasyimage-gallery-select-all-row" value="all" />
					</div>
				</div>
				<div style="width: 15%;" class="nowrap center">
					<div>
						<?php echo Text::_('COM_SPEASYIMAGEGALLERY_LIST_IMAGE'); ?>
					</div>
				</div>
				<div style="width: 25%;">
					<div>
						<?php echo Text::_('COM_SPEASYIMAGEGALLERY_LIST_TITLE'); ?>
					</div>
				</div>
				<div style="width: 15%;" class="nowrap center">
					<div>
						<?php echo Text::_('COM_SPEASYIMAGEGALLERY_LIST_CREATED'); ?>
					</div>
				</div>
				<div style="width: 15%;" class="nowrap hidden-phone center">
					<div>
						<?php echo Text::_('COM_SPEASYIMAGEGALLERY_LIST_AUTHOR'); ?>
					</div>
				</div>
				<div style="width: 10%;" class="nowrap hidden-phone center">
					<div>
						<?php echo Text::_('COM_SPEASYIMAGEGALLERY_LIST_STATUS'); ?>
					</div>
				</div>
				<div style="width: 10%;" class="nowrap hidden-phone center">
					<div>
						<?php echo Text::_('COM_SPEASYIMAGEGALLERY_LIST_ID'); ?>
					</div>
				</div>
			</div>

			<div id="imagesList" class="sp-tbody">
				<?php
				foreach ($images as $key => $image)
				{
					echo LayoutHelper::render('image', array('image' => $image));
				}
				?>
			</div>

		</div>

	</div>
</div>
<?php





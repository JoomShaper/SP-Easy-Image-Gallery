<?php
/**
 * @package com_speasyimagegallery
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2021 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */


// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

$doc = Factory::getDocument();
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

if (SpeasyimagegalleryHelper::getVersion() < 4)
{
	HTMLHelper::_('formbehavior.chosen', 'select', null, array('disable_search_threshold' => 0 ));
}

if (SpeasyimagegalleryHelper::getVersion() < 4)
{
	HTMLHelper::_('jquery.ui', array('core', 'sortable'));
}
else
{
	$doc->addStylesheet(Uri::base(true) . '/components/com_speasyimagegallery/assets/css/jquery-ui.min.css');
	HTMLHelper::_('jquery.framework');
	$doc->addScript(Uri::base(true) . '/components/com_speasyimagegallery/assets/js/jquery-ui.min.js');
}


$doc->addStylesheet(Uri::base(true) . '/components/com_speasyimagegallery/assets/css/font-awesome.min.css');
$doc->addStylesheet(Uri::base(true) . '/components/com_speasyimagegallery/assets/css/style.css');
$doc->addScript(Uri::base(true) . '/components/com_speasyimagegallery/assets/js/validation.js');
$doc->addScript(Uri::base(true) . '/components/com_speasyimagegallery/assets/js/script.js');

// Language strings
Text::script('COM_SPEASYIMAGEGALLERY_DELETE_IMAGE_CONFIRM');
Text::script('COM_SPEASYIMAGEGALLERY_IMAGE_UPLOADING');
Text::script('COM_SPEASYIMAGEGALLERY_MODAL_EDIT_IMAGE');

if ($this->item->id)
{
	$doc->addScriptdeclaration('var album_id = ' . $this->item->id . ';');
}
else
{
	$doc->addScriptdeclaration('var album_id = 0;');
}

$rowClass = JVERSION < 4 ? 'row-fluid' : 'row';
$colClass = JVERSION < 4 ? 'span' : 'col-lg-';
$JHtmlTag = JVERSION < 4 ? 'bootstrap' : 'uitab';
?>
<form action="<?php echo Route::_('index.php?option=com_speasyimagegallery&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" name="adminForm" id="adminForm" class="form-validate">
	<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">

		<div class="<?php echo $rowClass; ?>">
			<div class="<?php echo $colClass; ?>9">
<?php


if ($this->item->id)
{
	echo HTMLHelper::_($JHtmlTag . '.startTabSet', 'myTab', array('active' => 'images'));
	echo HTMLHelper::_($JHtmlTag . '.addTab', 'myTab', 'images', 'Images');
	echo LayoutHelper::render('images', array('total' => $this->total, 'images' => $this->images));
	echo HTMLHelper::_($JHtmlTag . '.endTab');
}
else
{
	echo HTMLHelper::_($JHtmlTag . '.startTabSet', 'myTab', array('active' => 'description'));
}
?>
				<?php echo HTMLHelper::_($JHtmlTag . '.addTab', 'myTab', 'description', 'Description'); ?>
				<div>
					<?php echo $this->form->getInput('description'); ?>
				</div>
				<?php echo HTMLHelper::_($JHtmlTag . '.endTab'); ?>

				<?php echo HTMLHelper::_($JHtmlTag . '.addTab', 'myTab', 'publishing', 'Publishing'); ?>
				<div>
					<?php echo $this->form->renderFieldset('publishing'); ?>
				</div>
				<?php echo HTMLHelper::_($JHtmlTag . '.endTab');?>

				<?php echo HTMLHelper::_($JHtmlTag . '.endTabSet'); ?>
			</div>

			<div class="<?php echo $colClass; ?>3">
				<fieldset class="form-vertical">
					<?php echo $this->form->renderFieldset('info'); ?>
				</fieldset>
			</div>
		</div>

	</div>

	<input type="hidden" name="task" value="album.edit" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="album_id" value="<?php echo !empty($this->item->id) ? $this->item->id : 0; ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>

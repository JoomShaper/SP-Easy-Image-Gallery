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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class SpeasyimagegalleryViewAlbum extends HtmlView
{

	protected $form;
	protected $item;
	protected $canDo;
	protected $id;

	public function display($tpl = null)
	{
		// Get the Data
		$model = $this->getModel('Album');
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->id = $this->item->id;

		$this->images = array();
		$this->total = 0;

		if (isset($this->id) && $this->id)
		{
			$this->images = $model->getImages($this->id);
			$this->total = $model->getCount($this->id);
		}

		$this->canDo = SpeasyimagegalleryHelper::getActions($this->item->id);

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \Exception(implode('<br />', $errors), 500);

			return false;
		}

		$this->addToolBar();
		parent::display($tpl);
	}

	protected function addToolBar()
	{
		$input = Factory::getApplication()->input;

		// Hide Joomla Administrator Main menu
		$input->set('hidemainmenu', true);

		$isNew = ($this->item->id == 0);

		ToolbarHelper::title(Text::_('COM_SPEASYIMAGEGALLERY_MANAGER') . ($isNew ? Text::_('COM_SPEASYIMAGEGALLERY_ALBUM_NEW') : Text::_('COM_SPEASYIMAGEGALLERY_ALBUM_EDIT')), 'pictures');

		if ($isNew)
		{
			// For new records, check the create permission.
			if ($this->canDo->get('core.create'))
			{
				ToolbarHelper::apply('album.apply', 'JTOOLBAR_APPLY');
			}

			ToolbarHelper::cancel('album.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			if ($this->canDo->get('core.edit'))
			{
				// We can save the new record
				ToolbarHelper::apply('album.apply', 'JTOOLBAR_APPLY');
				ToolbarHelper::save('album.save', 'JTOOLBAR_SAVE');
			}

			ToolbarHelper::cancel('album.cancel', 'JTOOLBAR_CLOSE');
		}

		if ($this->canDo->get('core.edit'))
		{
			ToolbarHelper::custom('album.deleteSelectedList', 'delete has-text-danger', '', Text::_('COM_SPEASYIMAGEGALLERY_DELETE'), false);
		}
	}
}

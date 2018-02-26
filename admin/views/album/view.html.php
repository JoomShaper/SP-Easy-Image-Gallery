<?php
/**
* @package com_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class SpeasyimagegalleryViewAlbum extends JViewLegacy
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
		if(isset($this->id) && $this->id) {
			$this->images = $model->getImages($this->id);
			$this->total = $model->getCount($this->id);
		}

		$this->canDo = SpeasyimagegalleryHelper::getActions($this->item->id);

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		$this->addToolBar();
		parent::display($tpl);
	}

	protected function addToolBar()
	{
		$input = JFactory::getApplication()->input;

		// Hide Joomla Administrator Main menu
		$input->set('hidemainmenu', true);

		$isNew = ($this->item->id == 0);

		JToolBarHelper::title(JText::_('COM_SPEASYIMAGEGALLERY_MANAGER') .  ($isNew ? JText::_('COM_SPEASYIMAGEGALLERY_ALBUM_NEW') : JText::_('COM_SPEASYIMAGEGALLERY_ALBUM_EDIT')), 'pictures');

		if ($isNew)
		{
			// For new records, check the create permission.
			if ($this->canDo->get('core.create'))
			{
				JToolBarHelper::apply('album.apply', 'JTOOLBAR_APPLY');
			}
			JToolBarHelper::cancel('album.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			if ($this->canDo->get('core.edit'))
			{
				// We can save the new record
				JToolBarHelper::apply('album.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('album.save', 'JTOOLBAR_SAVE');
			}
			JToolBarHelper::cancel('album.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}

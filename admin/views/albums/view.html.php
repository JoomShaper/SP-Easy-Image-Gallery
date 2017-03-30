<?php
/**
 * @package com_speasyimagegallery
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2017 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class SpeasyimagegalleryViewAlbums extends JViewLegacy
{

	protected $items;
	protected $pagination;
	protected $state;
	public $filterForm;
	public $activeFilters;
	protected $sidebar;

	function display($tpl = null)
	{

		// Get application
		$app = JFactory::getApplication();
		$context = "com_speasyimagegallery.albums";

		// Get data from the model
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', 'id', 'cmd');
		$this->filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'cmd');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$this->canDo = SpeasyimagegalleryHelper::getActions();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Set the submenu
		SpeasyimagegalleryHelper::addSubmenu('albums');
		$this->addToolBar();
		$this->sidebar = JHtmlSidebar::render();

		return parent::display($tpl);

	}

	protected function addToolBar()
	{
		JToolBarHelper::title(JText::_('COM_SPEASYIMAGEGALLERY_MANAGER') .  JText::_('COM_SPEASYIMAGEGALLERY_MANAGER_ALBUMS'), 'pictures');

		if ($this->canDo->get('core.create'))
		{
			JToolBarHelper::addNew('album.add', 'JTOOLBAR_NEW');
		}
		if ($this->canDo->get('core.edit'))
		{
			JToolBarHelper::editList('album.edit', 'JTOOLBAR_EDIT');
		}

		if ($this->state->get('filter.published') == -2 && $this->canDo->get('core.delete')) {
			JToolbarHelper::deleteList('', 'albums.delete', 'JTOOLBAR_EMPTY_TRASH');
		} elseif ($this->canDo->get('core.edit.state')) {
			JToolbarHelper::trash('albums.trash');
		}

		if ($this->canDo->get('core.admin'))
		{
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_speasyimagegallery');
		}
	}
}

<?php
/**
* @package com_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.application.component.helper');

class SpeasyimagegalleryViewAlbum extends JViewLegacy
{

	protected $item;
	protected $params;

	function display($tpl = null)
	{
		// Assign data to the view
		$this->item = $this->get('Item');

		$app = JFactory::getApplication();
		$this->params = $app->getParams();
		$menus = JFactory::getApplication()->getMenu();
		$menu = $menus->getActive();

		if($menu) {
			$this->params->merge($menu->params);
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseWarning(500, implode("\n", $errors));

			return false;
		}

		$model = $this->getModel();
		$model->hit();

		$this->_prepareDocument($this->item);
		parent::display($tpl);
	}

	protected function _prepareDocument($item)
	{
		$app   = JFactory::getApplication();
		$title = null;

		// Because the application sets a default page title,
		$this->params->def('page_heading', $item->title);
		$title = $item->title;

		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);

		if (isset($this->item->metadesc) && $this->item->metadesc)
		{
			$this->document->setDescription($this->item->metadesc);
		}

		if (isset($this->item->metakey) && $this->item->metakey)
		{
			$this->document->setMetadata('keywords', $this->item->metakey);
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}

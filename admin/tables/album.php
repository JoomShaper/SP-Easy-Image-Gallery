<?php
/**
* @package com_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

// No direct access
defined('_JEXEC') or die('Restricted access');

class SpeasyimagegalleryTableAlbum extends JTable
{

	public function __construct(&$db)
	{
		parent::__construct('#__speasyimagegallery_albums', 'id', $db);
	}

	public function store($updateNulls = false)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		if ($this->id) {
			$this->modified		= $date->toSql();
			$this->modified_by		= $user->get('id');
		} else {
			if (!(int) $this->created) {
				$this->created = $date->toSql();
			}
			if (empty($this->created_by)) {
				$this->created_by = $user->get('id');
			}
		}

		// Verify that the alias is unique
		$table = JTable::getInstance('Album', 'SpeasyimagegalleryTable');

		if ($table->load(array('alias' => $this->alias)) && ($table->id != $this->id || $this->id == 0))
		{
			$this->setError(JText::_('COM_SPEASYIMAGEGALLERY_ERROR_UNIQUE_ALIAS'));

			return false;
		}

		return parent::store($updateNulls);
	}

	public function check()
	{
		// Check for valid name.
		if (trim($this->title) == '')
		{
			throw new UnexpectedValueException(sprintf('The title is empty'));
		}

		if (empty($this->alias))
		{
			$this->alias = $this->title;
		}

		$this->alias = JApplicationHelper::stringURLSafe($this->alias, $this->language);

		if (trim(str_replace('-', '', $this->alias)) == '')
		{
			$this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
		}

		return true;

	}

}

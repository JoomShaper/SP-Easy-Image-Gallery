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
use Joomla\CMS\Table\Table;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Language\Text;

class SpeasyimagegalleryTableAlbum extends Table
{

	public function __construct(&$db)
	{
		parent::__construct('#__speasyimagegallery_albums', 'id', $db);
	}

	public function store($updateNulls = false)
	{
		$date = Factory::getDate()->toSql();
		$user = Factory::getUser();

		if ($this->id) {
			$this->modified		= $date;
			$this->modified_by		= $user->get('id');
		} else {
			if (!(int) $this->created) {
				$this->created = $date;
			}
			if (empty($this->created_by)) {
				$this->created_by = $user->get('id');
			}
			if (!(int) $this->modified)
			{
				$this->modified = $date;
			}

			if (empty($this->modified_by))
			{
				$this->modified_by = $user->get('id');
			}
		}

		// Verify that the alias is unique
		$table = Table::getInstance('Album', 'SpeasyimagegalleryTable');

		if ($table->load(array('alias' => $this->alias)) && ($table->id != $this->id || $this->id == 0))
		{
			$this->setError(Text::_('COM_SPEASYIMAGEGALLERY_ERROR_UNIQUE_ALIAS'));

			return false;
		}

		return parent::store($updateNulls);
	}

	public function check()
	{
		// Check for valid name.
		if (trim($this->title) == '')
		{
			throw new UnexpectedValueException(sprintf(Text::_('COM_SPEASYIMAGEGALLERY_ALBUM_TITLE_EMPTY')));
		}

		if (empty($this->alias))
		{
			$this->alias = $this->title;
		}

		if (empty($this->attribs))
		{
			$this->attribs = '';
		}

		if (empty($this->metadata))
		{
			$this->metadata = '';
		}

		$this->alias = ApplicationHelper::stringURLSafe($this->alias, $this->language);

		if (trim(str_replace('-', '', $this->alias)) == '')
		{
			$this->alias = Factory::getDate()->format('Y-m-d-H-i-s');
		}

		return true;

	}

}

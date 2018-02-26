<?php
/**
* @package com_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class SpeasyimagegalleryModelAlbum extends JModelItem
{
	protected $_context = 'com_speasyimagegallery.album';

	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		$albumId = $app->input->getInt('id');
		$this->setState('album.id', $albumId);

		$user = JFactory::getUser();

		$this->setState('filter.language', JLanguageMultilang::isEnabled());
	}

	public function getItem( $albumId = null )
	{
		$user = JFactory::getUser();

		$albumId = (!empty($albumId))? $albumId : (int)$this->getState('album.id');

		if ( $this->_item == null )
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$albumId]))
		{
			try
			{
				$db = $this->getDbo();
				$query = $db->getQuery(true)
					->select('a.*')
					->from('#__speasyimagegallery_albums as a')
					->where('a.id = ' . (int) $albumId);

				$query->select('l.title AS language_title')
					->leftJoin( $db->quoteName('#__languages') . ' AS l ON l.lang_code = a.language');

				$query->select('ua.name AS author_name')
					->leftJoin('#__users AS ua ON ua.id = a.created_by');

				// Filter by published state.
				$query->where('a.published = 1');

				if ($this->getState('filter.language'))
				{
					$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
				}

				$db->setQuery($query);
				$data = $db->loadObject();

				if(isset($data->id) && $data->id) {
					$data->images = $this->getImages($data->id);
				}

				if (empty($data)) {
					return JError::raiseError(404, JText::_('COM_SPEASYIMAGEGALLERY_ERROR_ALBUM_NOT_FOUND'));
				}

				$user = JFactory::getUser();
				$groups = $user->getAuthorisedViewLevels();
				if(!in_array($data->access, $groups)) {
					return JError::raiseError(404, JText::_('COM_SPEASYIMAGEGALLERY_ERROR_ALBUM_NOT_AUTHORISED'));
				}

				$this->_item[$albumId] = $data;
			}
			catch (Exception $e)
			{
				if ($e->getCode() == 404 )
				{
					JError::raiseError(404, $e->getMessage());
				}
				else
				{
					$this->setError($e);
					$this->_item[$albumId] = false;
				}
			}
		}

		return $this->_item[$albumId];
	}

	public function getImages($album_id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('a.*'));
		$query->from($db->quoteName('#__speasyimagegallery_images', 'a'));
		$query->where($db->quoteName('album_id') . ' = '. $db->quote($album_id));
		$query->where($db->quoteName('state') . ' = '. $db->quote(1));
		$query->order('a.ordering DESC');
		$db->setQuery($query);
		return $db->loadObjectList();
	}


	public function hit($pk = 0)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('album.id');
		$table = JTable::getInstance('Album', 'SpeasyimagegalleryTable');
		$table->load($pk);
		$table->hit($pk);

		return true;
	}

}

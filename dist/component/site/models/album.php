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
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\CMS\Language\Multilanguage;

class SpeasyimagegalleryModelAlbum extends ItemModel
{
	protected $_context = 'com_speasyimagegallery.album';

	protected function populateState()
	{
		$app = Factory::getApplication('site');

		$albumId = $app->input->getInt('id');
		$this->setState('album.id', $albumId);

		$user = Factory::getUser();

		$this->setState('filter.language', Multilanguage::isEnabled());
	}

	public function getItem( $albumId = null )
	{
		$user = Factory::getUser();

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
					$query->where('a.language in (' . $db->quote(Factory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
				}

				$db->setQuery($query);
				$data = $db->loadObject();

				if(isset($data->id) && $data->id) {
					$data->images = $this->getImages($data->id);
				}

				if (empty($data)) {
					return new \Exception(Text::_('COM_SPEASYIMAGEGALLERY_ERROR_ALBUM_NOT_FOUND'), 404);
				}

				$user = Factory::getUser();
				$groups = $user->getAuthorisedViewLevels();
				if(!in_array($data->access, $groups)) {
					return new \Exception(Text::_('COM_SPEASYIMAGEGALLERY_ERROR_ALBUM_NOT_AUTHORISED'), 404);
				}

				$this->_item[$albumId] = $data;
			}
			catch (Exception $e)
			{
				if ($e->getCode() == 404 )
				{
					throw new Exception($e->getMessage(), 404);
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
		$db = Factory::getDbo();
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
		$table = Table::getInstance('Album', 'SpeasyimagegalleryTable');
		$table->load($pk);
		$table->hit($pk);

		return true;
	}

}

<?php
/**
* @package com_speasyimagegallery
* @subpackage mod_speasyimagegallery
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2017 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

// No direct access
defined('_JEXEC') or die('Restricted access');

class ModSpeasyimagegalleryHelper
{
	public static function getAlbumList($params) {
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$catid = $params->get('catid', 0, 'INT');

		// Create a new query object.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('a.*');
		$query->from($db->quoteName('#__speasyimagegallery_albums', 'a'));

		// Join over the categories.
		$query->select('c.title AS category_title, c.alias AS category_alias')
		->join('LEFT', '#__categories AS c ON c.id = a.catid');

		// Images count
		$query->select('CASE WHEN c.count IS NULL THEN 0 ELSE c.count END as count')->join('LEFT', '( SELECT b.album_id, COUNT(b.album_id) as count FROM '. $db->quoteName('#__speasyimagegallery_images', 'b') . ' WHERE b.state = 1 GROUP BY b.album_id ) AS c ON c.album_id = a.id');

		//Authorised
		$groups = implode(',', $user->getAuthorisedViewLevels());
		$query->where('a.access IN (' . $groups . ')');

		// Filter category
		if($catid) {
			$query->where('a.catid = ' . $catid);
		}

		// Filter by language
		$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		$query->where('a.published = 1');
		$query->order('a.ordering ASC');
		$db->setQuery($query);
		$items = $db->loadObjectList();
		$ItemID = self::getItemID();

		if(count($items)) {
			foreach ($items as &$item) {
				$item->url = JRoute::_('index.php?option=com_speasyimagegallery&view=album&id=' . $item->id . ':' . $item->alias . $ItemID);
			}
		}

		return $items;
	}

	public static function getImages($params) {
		$album_id = $params->get('album_id', 0);
		$limit = $params->get('album_limit', 8);
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('a.*'));
		$query->from($db->quoteName('#__speasyimagegallery_images', 'a'));
		$query->where($db->quoteName('album_id') . ' = '. $db->quote($album_id));
		$query->where($db->quoteName('state') . ' = '. $db->quote(1));
		$query->order('a.ordering DESC');
		$query->setLimit($limit);
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	private static function getItemID() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id')));
		$query->from($db->quoteName('#__menu'));
		$query->where($db->quoteName('link') . ' LIKE '. $db->quote('%option=com_speasyimagegallery%'));
		$query->where($db->quoteName('published') . ' = '. $db->quote('1'));
		$db->setQuery($query);
		$result = $db->loadResult();

		if(count($result)) {
			return '&Itemid=' . $result;
		}

		return;
	}

}
